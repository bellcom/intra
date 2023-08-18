<?php

/**
 * This script disables related links on migrated content.
 * paragraph
 *
 * See task: https://os2web.atlassian.net/browse/BKDK-469
 */

use Drupal\node\Entity\Node;
use Drupal\node\NodeInterface;

$database = \Drupal::database();

// Getting all gallery slide pages.
$node_migrate_tables = [
  'migrate_map_ballerup_d7_node_gallery_slide',
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

print_r("Node to update: " . count($migrate_nids));
print_r(PHP_EOL);

$updated_count = 0;
foreach ($migrate_nids as $nid) {
  $node = Node::load($nid);
  if ($node instanceof NodeInterface) {
    $title = $node->getTitle();
    $heading = $node->field_os2web_page_heading->value;

    if (strcasecmp($title, $heading) !== 0) {
      $node->field_os2web_page_heading->value = $title;
      $node->save();
      $updated_count++;
    }
  }
}

print_r("Total nodes updated: $updated_count");
print_r(PHP_EOL);
