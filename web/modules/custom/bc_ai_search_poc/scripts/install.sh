#!/bin/bash

PROJECT_ROOT="$(cd "$(dirname "$0")/../../../../.." && pwd)"
CONFIG_DIR="$PROJECT_ROOT/web/modules/custom/bc_ai_search_poc/config/partial"
DRUSH="$PROJECT_ROOT/vendor/bin/drush"
SECRET="/etc/bellcom-secrets/openai_api_key"

echo '=== Bellcom AI Search POC installation ==='

ERROR=0

if [ ! -x "$DRUSH" ]; then
    echo "FEJL: Drush blev ikke fundet: $DRUSH"
    ERROR=1
fi

if [ ! -f "$SECRET" ]; then
    echo "FEJL: OpenAI secret mangler: $SECRET"
    ERROR=1
else
    echo 'OK: OpenAI secret findes.'
fi

if php8.4 -r '
$db = new SQLite3(":memory:");
if (!$db->loadExtension("vec0.so")) {
    throw new RuntimeException("sqlite-vec kunne ikke indlæses");
}
echo "OK: sqlite-vec virker.\n";
'; then
    :
else
    echo 'FEJL: sqlite-vec test fejlede.'
    ERROR=1
fi

if [ "$ERROR" -ne 0 ]; then
    echo 'FEJL: Forudsætningerne er ikke opfyldt. Ingen Drupal-ændringer udført.'
else
    cd "$PROJECT_ROOT" || {
        echo "FEJL: Kunne ikke gå til $PROJECT_ROOT"
        ERROR=1
    }

    if [ "$ERROR" -eq 0 ]; then
        echo
        echo '=== Aktiver nødvendige Drupal-moduler ==='

        if sudo -u www-data "$DRUSH" en \
          key \
          ai \
          ai_provider_openai \
          ai_search \
          ai_vdb_provider_sqlite \
          ai_search_block \
          -y; then
            echo 'OK: Nødvendige moduler er aktive.'
        else
            echo 'FEJL: Kunne ikke aktivere alle Drupal-moduler.'
            ERROR=1
        fi
    fi

    if [ "$ERROR" -eq 0 ]; then
        echo
        echo '=== Importer AI Search POC-config ==='

        if sudo -u www-data "$DRUSH" cim \
          --partial \
          --source="$CONFIG_DIR" \
          -y; then
            echo 'OK: AI Search POC-config importeret.'
        else
            echo 'FEJL: Config-import fejlede.'
            ERROR=1
        fi
    fi

    if [ "$ERROR" -eq 0 ]; then
        if sudo -u www-data "$DRUSH" cr; then
            echo 'OK: Drupal cache rebuild gennemført.'
        else
            echo 'FEJL: Cache rebuild fejlede.'
        fi
    fi
fi
