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

if (!empty(getenv('DRUPAL_TRUSTED_HOST'))) {
  $settings['trusted_host_patterns'] = ['^'.getenv('DRUPAL_TRUSTED_HOST').'$'];
}

$settings['hash_salt'] = getenv('DRUPAL_HASH_SALT');

$settings['file_temp_path'] = '../tmp/default';
$settings['file_private_path'] = '../private/default';
$settings['config_sync_directory'] = '../config/default';

$config['bc_subsites.settings'] = [
  'enabled' => TRUE,
  'domain_suffix' => getenv('DOMAIN_SUFFIX'),
  'script_dir' => getenv('SCRIPTDIR'),
  'subsites_config_dir' => '../config',
  'base_subsite_config_dir' => getenv('BASE_SUBSITE_CONFIG_DIR'),
  'allowed_install_profiles' => explode(',', getenv('ALLOWED_INSTALL_PROFILES')),
  'default_profile' => explode(',', getenv('PROFILE')),
];

// Passing EXTERNAL_DB_PROVISIONING into Drupal settings.
if (getenv('EXTERNAL_DB_PROVISIONING')) {
  $config['bc_subsites.settings']['external_db_provisioning'] =  getenv('EXTERNAL_DB_PROVISIONING');
}

