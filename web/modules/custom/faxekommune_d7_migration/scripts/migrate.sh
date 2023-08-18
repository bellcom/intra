#!/bin/sh

echo "Migration started"

echo "Importing new import configuration"
drush cim --partial --source=modules/custom/faxekommune_d7_migration/config/install -y
echo "Configuration imported"

echo "Migration faxekommune_d7_contact_box - START"
drush migrate:reset faxekommune_d7_contact_box
drush migrate:import faxekommune_d7_contact_box
echo "Migration faxekommune_d7_contact_box - END"

echo "Migration faxekommune_d7_taxonomy_section - START"
drush migrate:reset faxekommune_d7_taxonomy_section
drush migrate:import faxekommune_d7_taxonomy_section
echo "Migration faxekommune_d7_taxonomy_section - END"

echo "Migration faxekommune_d7_os2web_contentpage_paragraph_files - START"
drush migrate:reset faxekommune_d7_os2web_contentpage_paragraph_files
drush migrate:import faxekommune_d7_os2web_contentpage_paragraph_files
echo "Migration faxekommune_d7_os2web_contentpage_paragraph_files - END"

echo "Migration faxekommune_d7_node_news - START"
drush migrate:reset faxekommune_d7_node_news
drush migrate:import faxekommune_d7_node_news
echo "Migration faxekommune_d7_node_news - END"

echo "Migration faxekommune_d7_taxonomy_keywords - START"
drush migrate:reset faxekommune_d7_taxonomy_keywords
drush migrate:import faxekommune_d7_taxonomy_keywords
echo "Migration faxekommune_d7_taxonomy_keywords - END"

echo "Migration faxekommune_d7_paragraph_simple_text - START"
drush migrate:reset faxekommune_d7_paragraph_simple_text
drush migrate:import faxekommune_d7_paragraph_simple_text
echo "Migration faxekommune_d7_paragraph_simple_text - END"

echo "Migration faxekommune_d7_paragraph_files - START"
drush migrate:reset faxekommune_d7_paragraph_files
drush migrate:import faxekommune_d7_paragraph_files
echo "Migration faxekommune_d7_paragraph_files - END"

echo "Migration faxekommune_d7_paragraph_accordion_items - START"
drush migrate:reset faxekommune_d7_paragraph_accordion_items
drush migrate:import faxekommune_d7_paragraph_accordion_items
echo "Migration faxekommune_d7_paragraph_accordion_items - END"

echo "Migration faxekommune_d7_paragraph_accordion - START"
drush migrate:reset faxekommune_d7_paragraph_accordion
drush migrate:import faxekommune_d7_paragraph_accordion
echo "Migration faxekommune_d7_paragraph_accordion - END"

echo "Migration faxekommune_d7_selfservice - START"
drush migrate:reset faxekommune_d7_selfservice
drush migrate:import faxekommune_d7_selfservice
echo "Migration faxekommune_d7_selfservice - END"

echo "Migration faxekommune_d7_node_indholdside - START"
drush migrate:reset faxekommune_d7_node_indholdside
drush migrate:import faxekommune_d7_node_indholdside
echo "Migration faxekommune_d7_node_indholdside - END"

echo "Migration faxekommune_d7_node_borgerdk_indholdside - START"
drush migrate:reset faxekommune_d7_node_borgerdk_indholdside
drush migrate:import faxekommune_d7_node_borgerdk_indholdside
echo "Migration faxekommune_d7_node_borgerdk_indholdside - END"

echo "Migration faxekommune_d7_taxonomy_section_contentpage - START"
drush migrate:reset faxekommune_d7_taxonomy_section_contentpage
drush migrate:import faxekommune_d7_taxonomy_section_contentpage
echo "Migration faxekommune_d7_taxonomy_section_contentpage - END"

echo "Migration faxekommune_d7_main_menu - START"
drush migrate:reset faxekommune_d7_main_menu
drush migrate:import faxekommune_d7_main_menu
echo "Migration faxekommune_d7_main_menu - END"

echo "Migration finished"
