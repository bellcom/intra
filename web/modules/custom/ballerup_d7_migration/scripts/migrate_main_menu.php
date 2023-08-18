<?php

use Drupal\menu_link_content\Entity\MenuLinkContent;
use \Drupal\node\Entity\Node;
use Drupal\paragraphs\Entity\Paragraph;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$vid = 'os2web_sektion';
$terms = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree($vid);
$my_menu = \Drupal::entityTypeManager()->getStorage('menu_link_content')
  ->loadByProperties(['menu_name' => 'main']);
$section_nodes = [];
foreach ($terms as $term) {
  $weight = $term->weight;
  $parent_menu_link = 0;
  $parent_term = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadParents($term->tid);
  if ($parent_term) {
    $parent_term = reset($parent_term);
    $parent_term_id = $parent_term->id();
    if (isset($section_nodes[$parent_term_id])) {
      $menu_link_manager = \Drupal::service('plugin.manager.menu.link');
      $result = $menu_link_manager->loadLinksByRoute('entity.node.canonical', array('node' => $section_nodes[$parent_term_id]));
      $link = reset($result);
      $parent_menu_link = $link->getPluginId();
    }
  }
 if ($parent_term) {
  $paragraph = Paragraph::create([
      'type' => 'os2web_menu_links_paragraph',
  ]);
}
else {
$paragraph = Paragraph::create([
      'type' => 'os2web_menu_links_paragraph',
      'field_os2web_menu_links_inc_pr_p' => array(
        "value" => 1,
      ),
  ]);

}
  $paragraph->save();


  $node = Node::create([
      'type' => 'os2web_page',
      'title' => $term->name,
      'status' => 1,
      'moderation_state' => 'published',
      'field_os2web_page_paragraph_narr' => array(
        'target_id' => $paragraph->id(),
        'target_revision_id' => $paragraph->getRevisionId(),
      )
  ]);
  $node->save();
  $section_nodes[$term->tid] = $node->id();


  $menu_link = MenuLinkContent::create([
      'title' => $term->name,
      'link' => ['uri' => 'entity:node/' . $node->id()],
      'menu_name' => 'main',
      'parent' => $parent_menu_link,
      'expanded' => FALSE,
    'weight' => $weight,
  ]);
  $menu_link->save();
  $nodes[$term->tid] = $node->id();
}

$storage = \Drupal::service('entity_type.manager')->getStorage('node');
$nodes = $storage->loadByProperties(['type' => 'os2web_page']);
foreach ($nodes as $node) {
  if (in_array($node->id(), $section_nodes)) {
    continue;
  }

  if ($field_section = $node->get('field_os2web_page_section')->getValue()) {
    $field_section = reset($field_section);

    $section_tid = $field_section['target_id'];
    if (isset($section_nodes[$section_tid])) {
      $menu_link_manager = \Drupal::service('plugin.manager.menu.link');
      $result = $menu_link_manager->loadLinksByRoute('entity.node.canonical', array('node' => $section_nodes[$section_tid]));
      $link = reset($result);
      if ($link) {
        $parent_menu_link = $link->getPluginId();
        $my_menu = \Drupal::entityTypeManager()->getStorage('menu_link_content')
          ->loadByProperties(['menu_name' => 'main']);
        $menu_link = MenuLinkContent::create([
            'title' => $node->getTitle(),
            'link' => ['uri' => 'entity:node/' . $node->id()],
            'menu_name' => 'main',
            'parent' => $parent_menu_link,
            'expanded' => FALSE,
        ]);
        $menu_link->save();
      }
    }
  }
}
