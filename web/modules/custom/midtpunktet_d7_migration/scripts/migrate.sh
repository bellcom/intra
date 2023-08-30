#!/bin/sh

echo "Migration started"

echo "Importing new import configuration"
drush cim --partial --source=modules/custom/midtpunktet_d7_migration/config/install -y
echo "Configuration imported"

echo "Migration midtpunktet_d7_node_group - START"
drush migrate:reset midtpunktet_d7_node_group
drush migrate:import midtpunktet_d7_node_group
echo "Migration midtpunktet_d7_node_group - END"

echo "Migration midtpunktet_d7_organisation_menu - START"
drush migrate:reset midtpunktet_d7_organisation_menu
drush migrate:import midtpunktet_d7_organisation_menu
echo "Migration midtpunktet_d7_organisation_menu - END"

echo "Migration finished"
