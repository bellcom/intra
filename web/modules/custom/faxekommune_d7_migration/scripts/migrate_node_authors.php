<?php

use Drupal\Core\Database\Database;
use Drupal\node\Entity\Node;

$database = \Drupal::database();
$migrateDatabase = Database::getConnection('default', 'migrate');

// Getting all migrated users.
$userMigrateTable = 'migrate_map_faxekommune_d7_user';
$usersMigrated = $database->select($userMigrateTable)->fields($userMigrateTable, [
  'sourceid1',
  'destid1',
])
  ->isNotNull('destid1')
  ->execute()
  ->fetchAllKeyed();

// Getting all migrate NIDS.
$node_migrate_tables = [
  'migrate_map_faxekommune_d7_node_borgerdk_indholdside',
  'migrate_map_faxekommune_d7_node_indholdside',
  'migrate_map_faxekommune_d7_node_news',
];

$migrate_nids = [];
foreach ($node_migrate_tables as $table) {
  $table_nids = $database->select($table)->fields($table, [
    'destid1',
    'sourceid1',
  ])
    ->isNotNull('destid1')
    ->execute()
    ->fetchAllKeyed();

  $migrate_nids += $table_nids;
}

// Getting node authors.
$migrateNodeAuthors = $migrateDatabase->select('node')->fields('node', [
  'nid',
  'uid',
])->execute()->fetchAllKeyed();

$nids = \Drupal::entityQuery('node')
  ->condition('uid', 0)
  ->execute();

$i = 0;
$totalUpdates = 0;

foreach ($nids as $nid) {
  // Find corresponding migrate_nid
  if (isset($migrate_nids[$nid])) {
    $migrateNid = $migrate_nids[$nid];

    // Find migrate node author
    $migrateAuthorId = $migrateNodeAuthors[$migrateNid];

    // If has author.
    if ($migrateAuthorId) {
      $node = Node::load($nid);
      if (isset($usersMigrated[$migrateAuthorId])) {
        $authorId = $usersMigrated[$migrateAuthorId];

        print_r('Updating node: ' . $node->label() . ' [' . $node->id() . '], current UID: ' . $node->get('uid')->value . ', migrate UID: ' . $migrateAuthorId . ', local UID: ' . $authorId);
        print_r(PHP_EOL);
        $node->set('uid', $authorId);
        $node->save();
        $totalUpdates++;
      }
    }
  }
}

print_r("Total updated: $totalUpdates");
print_r(PHP_EOL);





