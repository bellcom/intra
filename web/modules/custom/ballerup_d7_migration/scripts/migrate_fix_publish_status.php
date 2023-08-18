<?php
use Drupal\Core\Database\Database;
use Drupal\node\Entity\Node;

$database = \Drupal::database();
$migrateDatabase = Database::getConnection('default', 'migrate');

// Getting all migrate NIDS.
$node_migrate_tables = [
  'migrate_map_ballerup_d7_node_gallery_slide',
  'migrate_map_ballerup_d7_node_indholdside',
  'migrate_map_ballerup_d7_node_institution_page',
  'migrate_map_ballerup_d7_node_news',
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

// Getting node status.
$migrateNodeStatus = $migrateDatabase->select('node')->fields('node', [
  'nid',
  'status',
])->execute()->fetchAllKeyed();

$nids = \Drupal::entityQuery('node')
  ->condition('status', 0)
  ->condition('type', ['os2web_page', 'os2web_news'], 'IN')
  ->execute();

$i = 0;
$totalPublished = 0;
foreach ($nids as $nid) {
  // Find corresponding migrate_nid
  if (isset($migrate_nids[$nid])) {
    $migrateNid = $migrate_nids[$nid];
//    print_r("Inspecting $nid : $migrateNid");
//    print_r(PHP_EOL);

    // Find migrate node status
    $status = $migrateNodeStatus[$migrateNid];
//    print_r("Status is $status");
//    print_r(PHP_EOL);

    // If published.
    if ($status) {
//      pint_r("Setting $nid published");
//      print_r(PHP_EOL);

      $node = Node::load($nid);
      $node->setPublished(TRUE);
      $node->set('moderation_state', "published");
      $node->save();
      $totalPublished++;
    }
  }
}

print_r("Total published: $totalPublished");
print_r(PHP_EOL);



