<?php

$storage = \Drupal::service('entity_type.manager')->getStorage('node');
$nodes = $storage->loadByProperties(['type' => 'os2web_page']);
foreach ($nodes as $node) {
  $field_heading = $node->get('field_os2web_page_heading')->getValue(); 
  if ($field_heading == false) {
    $node->set('field_os2web_page_heading', $node->getTitle());
    $node->save();
  } 
}

$storage = \Drupal::service('entity_type.manager')->getStorage('node');
$nodes = $storage->loadByProperties(['type' => 'os2web_news']);
foreach ($nodes as $node) {
  $field_heading = $node->get('field_os2web_news_heading')->getValue(); 
  if ($field_heading == false) {
    $node->set('field_os2web_news_heading', $node->getTitle());
    $node->save();
  } 
}
