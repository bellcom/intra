<?php 

use Drupal\menu_link_content\Entity\MenuLinkContent;
use \Drupal\node\Entity\Node;
use Drupal\paragraphs\Entity\Paragraph;

$storage = \Drupal::service('entity_type.manager')->getStorage('node');
$nodes = $storage->loadByProperties(['type' => 'os2web_page']);
$nodes_to_delete = [];
foreach ($nodes as $node) {
  if ($field_paragraph = $node->get('field_os2web_page_paragraph_narr')->getValue()) {
    $field_paragraph  = reset($field_paragraph );
    $paragraph = Paragraph::load($field_paragraph['target_id']);
    if ($paragraph->bundle() == 'os2web_menu_links_paragraph') {
      $nodes_to_delete[] = $node;
    }
  }
}
if (!empty($nodes_to_delete)) {
  $storage->delete($nodes_to_delete);
}
$mids = \Drupal::entityQuery('menu_link_content')
      ->condition('menu_name', 'main')
      ->execute();
$controller = \Drupal::entityTypeManager()->getStorage('menu_link_content');
$entities = $controller->loadMultiple($mids);
$controller->delete($entities);
