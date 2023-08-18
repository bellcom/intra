#!/bin/sh

echo "Migration started"

echo "Importing new import configuration"
drush cim --partial --source=modules/custom/faxe_d7_migration/config/install -y
echo "Configuration imported"

echo "Migration faxe_d7_contact_box - START"
drush migrate:reset faxe_d7_contact_box
drush migrate:import faxe_d7_contact_box
echo "Migration faxe_d7_contact_box - END"

echo "Migration faxe_d7_paragraph_banner - START"
drush migrate:reset faxe_d7_paragraph_banner
drush migrate:import faxe_d7_paragraph_banner
echo "Migration faxe_d7_paragraph_banner - END"

echo "Migration faxe_d7_node_os2web_event - START"
drush migrate:reset faxe_d7_node_os2web_event
drush migrate:import faxe_d7_node_os2web_event
echo "Migration faxe_d7_node_os2web_event - END"

echo "Migration faxe_d7_spotbox - START"
drush migrate:reset faxe_d7_spotbox
drush migrate:import faxe_d7_spotbox
echo "Migration faxe_d7_spotbox - END"

echo "Migration faxe_d7_node_velkomst - START"
drush migrate:reset faxe_d7_node_velkomst
drush migrate:import faxe_d7_node_velkomst
echo "Migration faxe_d7_node_velkomst - END"

echo "Migration faxe_d7_os2web_contentpage_paragraph_files - START"
drush migrate:reset faxe_d7_os2web_contentpage_paragraph_files
drush migrate:import faxe_d7_os2web_contentpage_paragraph_files
echo "Migration faxe_d7_os2web_contentpage_paragraph_files - END"

echo "Migration faxe_d7_paragraph_twi - START"
drush migrate:import faxe_d7_paragraph_twi
echo "Migration faxe_d7_paragraph_twi - END"

echo "Migration faxe_d7_paragraph_simple_text - START"
drush migrate:reset faxe_d7_paragraph_simple_text
drush migrate:import faxe_d7_paragraph_simple_text
echo "Migration faxe_d7_paragraph_simple_text - END"

echo "Migration faxe_d7_paragraph_accordion_items - START"
drush migrate:reset faxe_d7_paragraph_accordion_items
drush migrate:import faxe_d7_paragraph_accordion_items
echo "Migration faxe_d7_paragraph_accordion_items - END"

echo "Migration faxe_d7_paragraph_accordion - START"
drush migrate:reset faxe_d7_paragraph_accordion
drush migrate:import faxe_d7_paragraph_accordion
echo "Migration faxe_d7_paragraph_accordion - END"

echo "Migration faxe_d7_node_news - START"
drush migrate:reset faxe_d7_node_news
drush migrate:import faxe_d7_node_news
echo "Migration faxe_d7_node_news - END"

echo "Migration faxe_d7_node_indholdside - START"
drush migrate:reset faxe_d7_node_indholdside
drush migrate:import faxe_d7_node_indholdside
echo "Migration faxe_d7_node_indholdside - END"

echo "Migration faxe_d7_main_menu - START"
drush migrate:reset faxe_d7_main_menu
drush migrate:import faxe_d7_main_menu
echo "Migration faxe_d7_main_menu - END"

echo "Migration faxe_d7_account_menu - START"
drush migrate:reset faxe_d7_account_menu
drush migrate:import faxe_d7_account_menu
echo "Migration faxe_d7_account_menu - END"

# We need to run it again in the end, so that the URL's in the fields are transformed into correct node links
echo "Running that depend on local content again"

echo "Migration faxe_d7_paragraph_banner - START"
drush migrate:import faxe_d7_paragraph_banner --update
echo "Migration faxe_d7_paragraph_banner - END"

echo "Migration faxe_d7_spotbox - START"
drush migrate:import faxe_d7_spotbox  --update
echo "Migration faxe_d7_spotbox - END"

echo "Migration finished"
