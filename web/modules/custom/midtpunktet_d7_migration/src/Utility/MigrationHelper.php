<?php

namespace Drupal\midtpunktet_d7_migration\Utility;

use Drupal\Core\Database\Database;
use Drupal\file\Entity\File;
use Drupal\media\Entity\Media;

class MigrationHelper {

  public static $siteUrl = 'http://midtpunktet.ringsted.dk/';

  public static $fileFolderPath = 'default';

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

  /**
   * Gets a downloadable file URL.
   *
   * @param mix $field
   *   Array coming from migration source.
   *
   * @return string
   *   File downloadable URL.
   */
  static function getFileDownloadUrl($field) {
    $fileUrl = NULL;
    if ($field) {
      $fid = is_array($field) ? $field['fid'] : $field;

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
        //replacing public:// to http://midtpunktet.ringsted.dk/sites/default/files/
        $fileUrl = preg_replace('/(public:\/\/)/', self::$siteUrl . '/sites/'. self::$fileFolderPath .'/files/', $fileUrl);
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
  static function generateFileDestinationPath($field) {
    $fileUrl = '';
    if ($field) {
      $fid = is_array($field) ? $field['fid'] : $field;
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
  static function createFileManaged($uri) {
    $properties['uri'] = $uri;
    $files = \Drupal::entityTypeManager()->getStorage('file')->loadByProperties($properties);
    $file = reset($files);

    if (empty($file)) {
      $filesystem = \Drupal::service('file_system');
      // Create file entity.
      $file = File::create();
      $file->setFileUri($uri);
      $file->setOwnerId(\Drupal::currentUser()->id());
      $file->setMimeType(\Drupal::service('file.mime_type.guesser')->guess($uri));
      $file->setFileName($filesystem->basename($uri));
      $file->setPermanent();
      $file->save();
    }
    $file->setMimeType(\Drupal::service('file.mime_type.guesser')->guess($uri));
    $file->save();
    return $file->id();
  }

  /**
   * Gets a file Name.
   *
   * @param mix $field
   *   Array coming from migration source.
   *
   * @return string
   *   File title.
   */
  static function getFileName($field) {
    $fileName = NULL;
    if ($field) {
      $fid = is_array($field) ? $field['fid'] : $field;

      // Getting connection to migrate database.
      $connection = Database::getConnection('default', 'migrate');

      // Getting file url.
      $fileName = $connection->select('file_managed', 'f')
        ->fields('f', array('filename'))
        ->condition('f.fid', $fid)
        ->condition('f.status', 1)
        ->execute()
        ->fetchField();

    }
    return $fileName;
  }

  /**
   * Creates the media entity based on file id.
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
  static function createMediaEntity($id, $filename) {
    $media = Media::create([
      'bundle' => 'document',
      'uid' => '0',
      'field_media_file' => [
        'target_id' => $id,
      ],
      'status' => 1
    ]);

    $media->setName($filename)
      ->save();
    return $media->id();
  }

  /**
   * Sets the moderation state for the node based on a status.
   *
   * @param $status
   *
   * @return string
   */
  static function setModerationState($status) {
    if ($status) {
      return 'published';
    }
    else {
      return 'draft';
    }
  }
}
