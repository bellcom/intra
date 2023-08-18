<?php

use Drupal\Core\Database\Database;

$database = \Drupal::database();
$migrateDatabase = Database::getConnection('default', 'migrate');

$userMigrateTable = 'migrate_map_faxekommune_d7_user';
$usersMigrated = $database->select($userMigrateTable)->fields($userMigrateTable, [
    'sourceid1',
    'destid1',
  ])
  ->isNotNull('destid1')
  ->execute()
  ->fetchAll();

$totalUpdated = 0;

foreach ($usersMigrated as $userMigrated) {
  $authmapValue = $migrateDatabase->select('authmap')
    ->fields('authmap', ['authname'])
    ->condition('uid', $userMigrated->sourceid1, '=')
    ->execute()
    ->fetchField();


  if ($authmapValue) {
    $user = \Drupal\user\Entity\User::load($userMigrated->destid1);
    // Addind authmap value.
    $authmap = \Drupal::service('externalauth.authmap');

    if (empty($authmap->getAll($user->id()))) {
      print_r('Updating user ' . $user->label() . ' authmap value: ' . $authmapValue . PHP_EOL);
      $authmap->save($user, 'simplesamlphp_auth', $authmapValue);
      $totalUpdated++;
    }
  }
}

print_r("Total updated: $totalUpdated");
print_r(PHP_EOL);


