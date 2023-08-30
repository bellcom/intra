<?php

namespace Drupal\midtpunktet_d7_migration\Utility;

class MigrationHelper {

  public static $siteUrl = 'http://midtpunktet.ringsted.dk/';

  /**
   * Helper static function to populate menu link.
   *
   * @param $link
   *   Link field.
   *
   * @return int|null
   *   Int if the local node is found. NULL otherwise.
   */
  static function getMenuLink($link) {
    if (parse_url($link, PHP_URL_HOST) == parse_url(self::$siteUrl, PHP_URL_HOST)) {
      $link = parse_url($link, PHP_URL_PATH);
    }
    if (strpos($link, 'node') === 0) {
      $urlParts = explode('/', $link);

      // Finding local node URL, if present.
      if ($localNid = self::findLocalNode($urlParts[1])) {
        $urlParts[1] = $localNid;
        $link = implode('/', $urlParts);
      }
    }

    return $link;
  }

  /**
   * Helper static function to find local node by remote ID.
   *
   * @param $sourceNodeId
   *   Remote node ID.
   *
   * @return int|null
   *   Int if the local node is found. NULL otherwise.
   */
  static function findLocalNode($sourceNodeId) {
    $node_migrate_tables = [
      'migrate_map_midtpunktet_d7_node_group',
    ];

    $database = \Drupal::database();
    foreach ($node_migrate_tables as $table) {
      if ($database->schema()->tableExists($table)) {
        $localNid = $database->select($table)->fields($table, [
          'destid1',
        ])
          ->condition('sourceid1', $sourceNodeId)
          ->execute()
          ->fetchField();

        if ($localNid) {
          return $localNid;
        }
      }
    }

    return NULL;
  }
}
