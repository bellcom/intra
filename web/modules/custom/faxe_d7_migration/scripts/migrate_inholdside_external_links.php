<?php

use Drupal\Core\Database\Database;
use Drupal\node\Entity\Node;

$database = \Drupal::database();
$migrateDatabase = Database::getConnection('default', 'migrate');

// Getting all migrate NIDS.
$node_migrate_tables = [
  'migrate_map_faxe_d7_node_indholdside',
];

$migrate_nids = [];
$migrate_links = [];
foreach ($node_migrate_tables as $table) {
  $table_nids = $database->select($table)->fields($table,
    [
      'sourceid1',
      'destid1',
    ]
  )
    ->isNotNull('destid1')
    ->execute()
    ->fetchAllKeyed();

  $migrate_nids += $table_nids;
}

// Getting node status.
$migrateExternalLinks = $migrateDatabase->select('field_data_field_os2web_base_field_ext_link'
)->fields('field_data_field_os2web_base_field_ext_link',
  [
    'entity_id',
    'field_os2web_base_field_ext_link_title',
    'field_os2web_base_field_ext_link_url',
  ]
)
  ->condition('bundle', 'os2web_base_contentpage', '=')
  ->execute();

foreach ($migrateExternalLinks as $ExternalLink) {
  $migrate_links[$ExternalLink->entity_id][] = [
    'uri' => $ExternalLink->field_os2web_base_field_ext_link_url,
    'title' => $ExternalLink->field_os2web_base_field_ext_link_title,
  ];
}
$totalUpdated = 0;
foreach ($migrate_links as $key => $links) {
  if (isset($migrate_nids[$key])) {
    $nid = $migrate_nids[$key];
    $node = Node::load($nid);
    $node->set('field_ext_links', $links);
    $node->save();
    $totalUpdated++;
  }
}
print_r("Total updated: $totalUpdated");
print_r(PHP_EOL);


