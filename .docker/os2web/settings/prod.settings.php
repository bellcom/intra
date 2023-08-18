<?php
/**
 * @file
 * Local development override configuration feature.
 *
 * To activate this feature, copy and rename it such that its path plus
 * filename is 'sites/default/settings.local.php'. Then, go to the bottom of
 * 'sites/default/settings.php' and uncomment the commented lines that mention
 * 'settings.local.php'.
 *
 * If you are using a site name in the path, such as 'sites/example.com', copy
 * this file to 'sites/example.com/settings.local.php', and uncomment the lines
 * at the bottom of 'sites/example.com/settings.php'.
 */

$databases['default']['default'] = [
  'database' => getenv('MYSQL_DATABASE'),
  'driver' => 'mysql',
  'host' => getenv('MYSQL_HOSTNAME'),
  'namespace' => 'Drupal\\Core\\Database\\Driver\\mysql',
  'password' => getenv('MYSQL_PASSWORD'),
  'port' => getenv('MYSQL_PORT'),
  'prefix' => '',
  'username' => getenv('MYSQL_USER'),
];

if (!empty(getenv('MIGRATE_MYSQL_DATABASE'))) {
  $databases['migrate']['default'] = [
    'database' => getenv('MIGRATE_MYSQL_DATABASE'),
    'driver' => 'mysql',
    'host' => getenv('MIGRATE_MYSQL_HOSTNAME'),
    'namespace' => 'Drupal\\Core\\Database\\Driver\\mysql',
    'password' => getenv('MIGRATE_MYSQL_PASSWORD'),
    'port' => getenv('MIGRATE_MYSQL_PORT'),
    'prefix' => '',
    'username' => getenv('MIGRATE_MYSQL_USER'),
  ];
}

if (!empty(getenv('DRUPAL_TRUSTED_HOST'))) {
  $settings['trusted_host_patterns'] = ['^'.getenv('DRUPAL_TRUSTED_HOST').'$'];
}

$settings['hash_salt'] = getenv('DRUPAL_HASH_SALT');

$settings['file_temp_path'] = '../tmp';
$settings['file_private_path'] = '../private';

