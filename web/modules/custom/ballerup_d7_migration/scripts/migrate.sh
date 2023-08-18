#!/bin/sh

echo "Migration is disabled"

#echo "Importing new import configuration"
#drush cim --partial --source=modules/custom/ballerup_d7_migration/config/install -y
#echo "Configuration imported"
#
#echo "Migration users - START"
#drush migrate:import ballerup_d7_user --update
#echo "Migration users - END"
#
#echo "Migration ballerup_d7_taxonomy_section - START"
#drush migrate:import ballerup_d7_taxonomy_section --update
#echo "Migration ballerup_d7_taxonomy_section - END"
#
#echo "Migration ballerup_d7_taxonomy_tags - START"
#drush migrate:import ballerup_d7_taxonomy_tags --update
#echo "Migration ballerup_d7_taxonomy_tags - END"
#
#echo "Migration os2web_borgerdk_articles_import - START"
#drush migrate:import os2web_borgerdk_articles_import --update
#echo "Migration os2web_borgerdk_articles_import - END"
#
#echo "Migration ballerup_d7_contact_box - START"
#drush migrate:import ballerup_d7_contact_box --update
#echo "Migration ballerup_d7_contact_box - END"
#
#echo "Migration ballerup_d7_paragraph_iframe - START"
#drush migrate:import ballerup_d7_paragraph_iframe --update
#echo "Migration ballerup_d7_paragraph_iframe - END"
#
#echo "Migration ballerup_d7_node_gallery_slide - START"
#drush migrate:import ballerup_d7_node_gallery_slide --update
#echo "Migration ballerup_d7_node_gallery_slide - END"
#
#echo "Migration ballerup_d7_node_institution_page - START"
#drush migrate:import ballerup_d7_node_institution_page --update
#echo "Migration ballerup_d7_node_institution_page - END"
#
#echo "Migration ballerup_d7_node_news - START"
#drush migrate:import ballerup_d7_node_news --update
#echo "Migration ballerup_d7_node_news - END"
#
#echo "Migration ballerup_d7_paragraph_accordion - START"
#drush migrate:import ballerup_d7_paragraph_accordion --update
#echo "Migration ballerup_d7_paragraph_accordion - END"
#
#echo "Migration ballerup_d7_node_indholdside - START"
#drush migrate:import ballerup_d7_node_indholdside --update
#echo "Migration ballerup_d7_node_indholdside - END"
#
echo "Execuing custom script [1/6] - Fix publish status"
#drush scr modules/custom/ballerup_d7_migration/scripts/migrate_fix_publish_status.php
echo "Skipped"

echo "Execuing custom script [2/6] - Remove inline picutres"
#drush scr modules/custom/ballerup_d7_migration/scripts/remove_inline_pictures.php
echo "Skipped"

echo "Execuing custom script [3/6] - Remove node header duplicates (content pages using Borger.dk article)"
#drush scr modules/custom/ballerup_d7_migration/scripts/BKDK-401-migrate_remove_page_borgerdk_body.php
echo "Skipped"

echo "Execuing custom script [4/6] - Disable related links block on migrated nodes"
#drush scr modules/custom/ballerup_d7_migration/scripts/BKDK-432-migrate_disable_related_links.php
echo "Skipped"

echo "Execuing custom script [5/6] - Updated section pages"
#drush scr modules/custom/ballerup_d7_migration/scripts/BKDK-399-update_section_pages.php
echo "Skipped"

echo "Execuing custom script [5/6] - Updated section pages"
#drush scr modules/custom/ballerup_d7_migration/scripts/BKDK-399-update_section_pages.php
echo "Skipped"

echo "Execuing custom script [6/6] - Change gallery slide headers"
drush scr modules/custom/ballerup_d7_migration/scripts/BKDK-469-change_gallery_slide_headers.php

#echo "Migration complete visit URL '/admin/config/system/delete-orphans' to delete the orphaned paragraphs"
