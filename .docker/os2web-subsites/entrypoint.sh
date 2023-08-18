#!/bin/bash

# Starting cron service.
service cron start

# Check basic file structure for subsites creator
if [ ! -f "/etc/apache2/sites-available/000-default.conf" ]
then
  cp -f /opt/drupal/.docker/os2web-subsites/apache/000-default.conf /etc/apache2/sites-available/000-default.conf
  ln -sf /etc/apache2/sites-available/000-default.conf /etc/apache2/sites-enabled/000-default.conf
fi

# Check basic file structure for subsites creator
if [ ! -d "/opt/drupal/web/sites/default" ]
then
  echo "Default sites folder doesn't exists. Create folder and standard files"
  mkdir -p /opt/drupal/web/sites/default/files
  cp -f /opt/drupal/.docker/os2web-subsites/settings/default.settings.php /opt/drupal/web/sites/default/default.settings.php
  cp -f /opt/drupal/.docker/os2web-subsites/settings/default.settings.php /opt/drupal/web/sites/default/settings.php
  echo 'include $app_root . "/" . $site_path . "/settings.local.php";' >> /opt/drupal/web/sites/default/settings.php
  cp -f /opt/drupal/.docker/os2web-subsites/settings/settings.local.php /opt/drupal/web/sites/default/settings.local.php
  chmod 755 /opt/drupal/web/sites/default /opt/drupal/web/sites/default/settings.php /opt/drupal/web/sites/default/settings.local.php
  chown -R www-data:www-data /opt/drupal/web/sites
fi

# Check basic file structure for subsites creator
if [ ! -f "/opt/drupal/web/sites/sites.php" ]
then
  echo "Creating web/sites/sites.php"
  echo "<?php" > /opt/drupal/web/sites/sites.php
  chown www-data:www-data /opt/drupal/web/sites/sites.php
fi

# Check default config dir
if [ ! -f "/opt/drupal/config/default" ]
then
  mkdir -p /opt/drupal/config/default
  chown www-data:www-data -R /opt/drupal/config
fi

# Check default temp dir
if [ ! -f "/opt/drupal/tmp/default" ]
then
  mkdir -p /opt/drupal/tmp/default
  chown www-data:www-data -R /opt/drupal/tmp
fi

# Check default private dir
if [ ! -f "/opt/drupal/private/default" ]
then
  mkdir -p /opt/drupal/private/default
  chown www-data:www-data -R /opt/drupal/private
fi

# Copying shared settings file
cp -f /opt/drupal/.docker/os2web-subsites/settings/shared.settings.php /opt/drupal/web/sites/shared.settings.php

exec "$@"
