<?php

use Drupal\menu_link_content\Entity\MenuLinkContent;
use \Drupal\node\Entity\Node;
use Drupal\Core\Database\Database;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/*
 *
 */
//
$migrate_tables = ['migrate_map_ballerup_d7_node_institution_page', 'migrate_map_ballerup_d7_node_gallery_slide'];
$nids = [];
foreach ($migrate_tables as $table) {
  $database = \Drupal::database();
  $query = $database->select($table, 't')
    ->fields('t', ['destid1']);
  $result = $query->execute();
  foreach ($result as $record) {
    $nids[] = $record->destid1;
  }
}
$menu_link_manager = \Drupal::service('plugin.manager.menu.link');
if (!empty($nids)) {
  foreach ($nids as $nid) {
    $result = $menu_link_manager->loadLinksByRoute('entity.node.canonical', array('node' => $nid));
    if ($result) {
      $link = reset($result);
      $id = $link->getPluginDefinition()['metadata']['entity_id'];
      $menu_link = \Drupal::service('entity.manager')->getStorage('menu_link_content')->load($id);
      $menu_link->enabled = 0;
      $menu_link->save();
    }
  }
}
