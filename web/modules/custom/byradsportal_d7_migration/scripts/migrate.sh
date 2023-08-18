#!/bin/sh

echo "Migration started"

echo "Importing new import configuration"
drush cim --partial --source=modules/custom/byradsportal_d7_migration/config/install -y
echo "Configuration imported"

echo "Migration byradsportal_d7_forum - START"
drush migrate:reset byradsportal_d7_forum
drush migrate:import byradsportal_d7_forum
echo "Migration byradsportal_d7_forum - END"

echo "Migration byradsportal_d7_forum_archive - START"
drush migrate:reset byradsportal_d7_forum_archive
drush migrate:import byradsportal_d7_forum_archive
echo "Migration byradsportal_d7_forum_archive - END"

echo "Migration byradsportal_d7_forum_comment - START"
drush migrate:reset byradsportal_d7_forum_comment
drush migrate:import byradsportal_d7_forum_comment
echo "Migration byradsportal_d7_forum_comment - END"

echo "Migration byradsportal_d7_node_news_paragraph_files - START"
drush migrate:reset byradsportal_d7_node_news_paragraph_files
drush migrate:import byradsportal_d7_node_news_paragraph_files
echo "Migration byradsportal_d7_node_news_paragraph_files - END"

echo "Migration byradsportal_d7_node_news - START"
drush migrate:reset byradsportal_d7_node_news
drush migrate:import byradsportal_d7_node_news
echo "Migration byradsportal_d7_node_news - END"

echo "Migration finished"
