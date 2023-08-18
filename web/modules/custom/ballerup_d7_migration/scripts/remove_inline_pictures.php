<?php


//select * from field_data_body f, node where f.body_value like '%"fid"%' and f.bundle='info_page' and node.nid = f.entity_id and node.status=1;
//select * from field_data_field_body f, node where f.field_body_value like '%"fid"%' and node.nid = f.entity_id and node.status=1;

use Drupal\node\Entity\Node;

// os2web_page.
$query = \Drupal::entityQuery('node');
$query->condition('type', 'os2web_page');
$query->condition('field_os2web_page_description', '%"fid"%', 'LIKE');
$entity_ids = $query->execute();

$nodes = Node::loadMultiple($entity_ids);

$i = 0;
foreach ($nodes as $node) {
  $description = $node->field_os2web_page_description->value;
  $new_description = preg_replace('/\[\[\{.*\}\]\]/', '', $description);

  // Changed.
  if (strcmp($description, $new_description) !== 0) {
    $i++;
    $node->field_os2web_page_description->value = $new_description;
    $node->save();
  }
}

print_r('Updated ' . $i . ' os2web_page nodes' . PHP_EOL);

// os2web_news.
$query = \Drupal::entityQuery('node');
$query->condition('type', 'os2web_news');
$query->condition('field_os2web_news_description', '%"fid"%', 'LIKE');
$entity_ids = $query->execute();

$nodes = Node::loadMultiple($entity_ids);

$i = 0;
foreach ($nodes as $node) {
  $description = $node->field_os2web_news_description->value;
  $new_description = preg_replace('/\[\[\{.*\}\]\]/', '', $description);

  // Changed.
  if (strcmp($description, $new_description) !== 0) {
    $i++;
    $node->field_os2web_news_description->value = $new_description;
    $node->save();
  }
}

print_r('Updated ' . $i . ' os2web_news nodes' . PHP_EOL);


