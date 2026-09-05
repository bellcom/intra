# Bellcom AI Search POC

Reproducerbar konfiguration til Drupal AI/RAG-søgning på intranettet.

## Funktion

POC'en bruger:

- Drupal AI
- OpenAI provider
- AI Search
- AI Search Block
- SQLite VDB provider
- sqlite-vec
- Search API
- OpenAI embeddings
- Drupal entity access checks

Den eksisterende Solr-søgning berøres ikke.

## Composer

De nødvendige contrib-pakker installeres via projektets `composer.json`.

Projektet indeholder desuden lokale patches til:

- SQLite VDB-providerens håndtering af Search API computed properties.
- AI Search Block, så `search_api_bypass_access` ikke deaktiverer Drupal-adgangskontrol.

## OpenAI secret

API-nøglen må ikke gemmes i Git.

Forventet fil:

    /etc/bellcom-secrets/openai_api_key

Anbefalede rettigheder:

    root:www-data
    0640

Konfigurationen i:

    config/partial/key.key.openai_api_key.yml

indeholder kun filstien og ikke selve nøglen.

## SQLite / sqlite-vec

POC'en forventer PHP SQLite-understøttelse og sqlite-vec-extensionen.

På nuværende testmiljø anvendes:

    /usr/local/lib/sqlite3/vec0.so

PHP skal kunne loade `vec0.so`.

## Installation

Kør fra projektroden som en bruger med sudo-adgang:

    web/modules/custom/bc_ai_search_poc/scripts/install.sh

Scriptet:

1. kontrollerer OpenAI-secret,
2. kontrollerer sqlite-vec,
3. aktiverer nødvendige Drupal-moduler,
4. importerer kun POC-konfigurationen med `drush cim --partial`,
5. genbygger Drupal cache.

Det foretager ikke en almindelig fuld config-import.

## Sikkerhed

RAG-resultater skal respektere Drupal `view`-adgang før indhold sendes
til OpenAI.

AI Search-backenden anvender:

    $entity->access('view', $this->currentUser)

AI Search Block udfører desuden et ekstra entity access-check før
entities renderes ind i LLM-prompten.

Projektets patch ændrer AI Search Block fra:

    search_api_bypass_access = TRUE

til:

    search_api_bypass_access = FALSE

Dette er testet med en midlertidig authenticated-bruger:

- med bypass slået fra blev kun tilladte søgeresultater returneret,
- med bypass slået til blev flere nodes uden view-adgang returneret.

## Efter installation

Kontroller moduler:

    vendor/bin/drush pm:list --status=enabled | grep -E 'ai|openai|key'

Kontroller Search API:

    vendor/bin/drush search-api:list

Genbyg index efter behov via Drupal Search API eller Drush.

## Bemærkninger

Denne opsætning er en POC og bruger blandt andet en RC-version af
AI Search Block.

OpenAI API-nøglen skal roteres før brug med reelt eller følsomt
intranetindhold, hvis den tidligere har været eksponeret i logs eller fejloutput.
