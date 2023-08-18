<?php
/**
 * @file
 * Contains \Drupal\your_migrate_module\Plugin\migrate\process\MigrateUrlAlias.
 *
 */

namespace Drupal\faxekommune_d7_migration\Plugin\migrate\process;

use Drupal\Core\Database\Database;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * This process plugin can be used for path/alias migration.
 *
 *
 * @MigrateProcessPlugin(
 *   id = "migrate_url_alias",
 * )
 */
class MigrateUrlAlias extends ProcessPluginBase {
  /**
  * {@inheritdoc}
  */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    // Retrieves a \Drupal\Core\Database\Connection which is a PDO instance
    $db = Database::getConnection('default', 'migrate');
    $query = $db->select('url_alias', 'u')
      ->fields('u', ['alias'])
      ->condition('u.source', 'node/' . $row->getSourceIdValues()['nid'])
      ->orderBy('pid', 'DESC')
      ->range(0, 1);

    $data = $query->execute()->fetch();
      if (is_object($data)) {
        return '/' . $data->alias;
      }
    return null;
  }
}
