#!/bin/sh

echo "Migration started"

echo "Importing new import configuration"
drush cim --partial --source=modules/custom/ringsted_d7_migration/config/install -y
echo "Configuration imported"

echo "Migration ringsted_d7_taxonomy_section - START"
drush migrate:import ringsted_d7_taxonomy_section
echo "Migration ringsted_d7_taxonomy_section - END"

echo "Migration ringsted_d7_node_news - START"
drush migrate:import ringsted_d7_node_news
echo "Migration ringsted_d7_node_news - END"

echo "Migration ringsted_d7_node_postlister - START"
drush migrate:import ringsted_d7_node_postlister
echo "Migration ringsted_d7_node_postlister - END"

echo "Migration finished"
