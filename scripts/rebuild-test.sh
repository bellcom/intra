#!/usr/bin/env bash
# Script reinstalling drupal from scratch based in os2web profile.

# Trapping CTRL-C
trap ctrl_c INT
trap ctrl_c SIGINT
trap ctrl_c SIGTERM

# Console colors
red='\033[0;31m'
green='\033[0;32m'
green_bg='\033[42m'
yellow='\033[1;33m'
NC='\033[0m'

# Helper functions for console output
echo-red () { echo -e "${red}$1${NC}"; }
echo-green () { echo -e "${green}$1${NC}"; }
echo-green-bg () { echo -e "${green_bg}$1${NC}"; }
echo-yellow () { echo -e "${yellow}$1${NC}"; }

# Getting correct project directory.
PROJECT_DIR=$( cd -- "$( dirname -- "${BASH_SOURCE[0]}" )/../" &> /dev/null && pwd )
echo-yellow "switching to project directory $PROJECT_DIR"
cd $PROJECT_DIR

COMPOSER=$(which composer)
DRUSH=$(which drush)

echo-yellow "Fetching composer dependencies"
$COMPOSER install

if [ ! -f "web/sites/default/env.settings.php" ]; then
  cp web/sites/default/env.example.settings.php web/sites/default/env.settings.php
fi

echo-yellow "Reinstalling drupal from scratch based in os2web profile."
## With Danish translation. NOTE this operation take much longer time.
# drush si os2web --account-pass=admin --locale=da -y -vvv

## Without translations.
$DRUSH si os2web --account-pass=admin -y -vvv

chmod 755 web/sites/default

echo-yellow "Enable theme and modules"
$DRUSH theme:enable fds_custom_theme
$DRUSH config-set system.theme default fds_custom_theme -y
$DRUSH en -y os2web_pagebuilder os2web_spotbox
