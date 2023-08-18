<?php

namespace Drupal\ringsted_d7_migration\Utility;

use Drupal\Core\Database\Database;
use Drupal\file\Entity\File;
use Drupal\taxonomy\Entity\Term;

class MigrationHelper {

  public static $siteUrl = 'https://ringsted.dk';

  /**
   * Gets a downloadable file URL.
   *
   * @param mix $field
   *   Array coming from migration source.
   *
   * @return string
   *   File downloadable URL.
   */
  function getFileDownloadUrl($field) {
    $fileUrl = NULL;
    if ($field) {
      $fid = $field['fid'];

      // Getting connection to migrate database.
      $connection = Database::getConnection('default', 'migrate');

      // Getting file url.
      $fileUrl = $connection->select('file_managed', 'f')
        ->fields('f', array('uri'))
        ->condition('f.fid', $fid)
        ->condition('f.status', 1)
        ->execute()
        ->fetchField();

      if ($fileUrl) {
        //replacing public:// to https://ringsted.dk/sites/default/files/
        $fileUrl = preg_replace('/(public:\/\/)/', MigrationHelper::$siteUrl . '/sites/default/files/', $fileUrl);
      }
    }

    return $fileUrl;
  }

  /**
   * Generates file destination URI.
   *
   * @param mix $field
   *   Array coming from migration source.
   *
   * @return string
   *   File destination URL.
   */
  function generateFileDestinationPath($field) {
    $fileUrl = '';
    if ($field) {
      $fid = $field['fid'];

      // Getting connection to migrate database.
      $connection = Database::getConnection('default', 'migrate');

      // Getting file url.
      $fileUrl = $connection->select('file_managed', 'f')
        ->fields('f', array('uri'))
        ->condition('f.fid', $fid)
        ->condition('f.status', 1)
        ->execute()
        ->fetchField();
    }

    return $fileUrl;
  }

  /**
   * Creates the file based on the URI or finds an existing one.
   *
   * @param string $uri
   *   Uri of the file.
   *
   * @return int
   *   File ID.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  function createFileManaged($uri) {
    $properties['uri'] = $uri;
    $files = \Drupal::entityTypeManager()->getStorage('file')->loadByProperties($properties);

    $file = reset($files);

    if (empty($file)) {
      $filesystem = \Drupal::service('file_system');
      // Create file entity.
      $file = File::create();
      $file->setFileUri($uri);
      $file->setOwnerId(\Drupal::currentUser()->id());
      $file->setMimeType('image/' . pathinfo($uri, PATHINFO_EXTENSION));
      $file->setFileName($filesystem->basename($uri));
      $file->setPermanent();
      $file->save();
    }

    return $file->id();
  }

  /**
   * Finds KLE term for postlister in os2web_kle vocabulary.
   *
   * @param int $nid
   *   Node Id.
   *
   * @return array
   *   [
   *     'tid'
   *   ]
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  function findPostlisterKleTerm($nid) {
    $name = '85.02.15 Postlister og sagslister';
    $vid = 'os2web_kle';

    $properties['name'] = $name;
    $properties['vid'] = $vid;

    $terms = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties($properties);
    $term = reset($terms);

    if ($term) {
      return $term->id();
    }
  }

}
