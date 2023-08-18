<?php

/**
 * This script disables related links on migrated content.
 * paragraph
 *
 * See task: https://os2web.atlassian.net/browse/BKDK-432
 */


use Drupal\node\Entity\Node;
use Drupal\node\NodeInterface;

$database = \Drupal::database();

// Getting all migrate NIDS.
$node_migrate_tables = [
  'migrate_map_ballerup_d7_node_gallery_slide',
  'migrate_map_ballerup_d7_node_indholdside',
  'migrate_map_ballerup_d7_node_institution_page',
];

$migrate_nids = [];
foreach ($node_migrate_tables as $table) {
  $table_nids = $database->select($table)->fields($table, [
    'destid1',
  ])
    ->isNotNull('destid1')
    ->execute()
    ->fetchCol();

  $migrate_nids += $table_nids;
}

print_r("Node count to check: " . count($migrate_nids));
print_r(PHP_EOL);

$updated_count = 0;
foreach ($migrate_nids as $nid) {
  $node = Node::load($nid);
  if ($node instanceof NodeInterface && empty($node->field_os2web_page_related_hide->value)) {
    $node->field_os2web_page_related_hide->value = 1;
    $node->save();
    $updated_count++;
  }
}

print_r("Total nodes updated: $updated_count");
print_r(PHP_EOL);
