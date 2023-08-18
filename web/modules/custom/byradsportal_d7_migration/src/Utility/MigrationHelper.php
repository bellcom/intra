<?php

namespace Drupal\byradsportal_d7_migration\Utility;

use Drupal\Core\Database\Database;
use Drupal\file\Entity\File;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\taxonomy\Entity\Term;
use Drupal\media\Entity\Media;

class MigrationHelper {

  public static $siteUrl = '/var/www/web';
  public static $fileFolderPath = 'default';

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

  /**
   * Create accordion paragraph.
   *
   * @param $field_accordion
   *
   * @return array
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  /**
   * Create accordion paragraph.
   *
   * @param $field_accordion
   *
   * @return array
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  static function createAccordionItemParagraph($field_paragraph_text) {
    if (!$field_paragraph_text) {
      return [];
    }
    // Creating text paragraph.
    $text_paragraph = Paragraph::create([
      'type' => 'os2web_simple_text_paragraph',
      'field_os2web_simple_text_heading' => '',
      'field_os2web_simple_text_body' => [
        'value' => $field_paragraph_text['value'],
        'format' => 'wysiwyg_tekst'
      ]
    ]);
    $text_paragraph->save();

    return [
      'target_id' => $text_paragraph->id(),
      'target_revision_id' =>  $text_paragraph->getRevisionId(),
    ];
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
        //replacing public:// to https://ringsted.dk/sites/default/files/
        $fileUrl = preg_replace('/(public:\/\/)/', MigrationHelper::$siteUrl . '/sites/'. MigrationHelper::$fileFolderPath .'/old_files/', $fileUrl);
      }
    }
    return $fileUrl;
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
      'migrate_map_faxe_d7_node_indholdside',
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
   * Helper static function to get full username from migrate DB.
   *
   * @param $uid
   *   Remove user UID.
   *
   * @return string|null
   *   Int if the local node is found. NULL otherwise.
   */
  static function getUsernameFromUid($uid) {
    // Getting connection to migrate database.
    $connection = Database::getConnection('default', 'migrate');

    // Getting user name.
    $name = $connection->select('users', 'u')
      ->fields('u', array('name'))
      ->condition('u.uid', $uid)
      ->execute()
      ->fetchField();

    // Getting user full name.
    $fullName = $connection->select('field_data_field_fullname', 'f')
      ->fields('f', array('field_fullname_value'))
      ->condition('f.entity_id', $uid)
      ->execute()
      ->fetchField();

    if ($fullName) {
      return "Oprettet af: $fullName ($name)<br/><br/>";
    }
    return "Oprettet af: $name<br/><br/>";
  }

  /**
   * Converts texts new lines to p tags.
   *
   * @param $string
   *   Text with new lines.
   *
   * @return string
   *
   */
  static function textNl2p($string) {
    $line_breaks = TRUE;
    $xml = TRUE;

    $string = str_replace(['<p>', '</p>', '<br>', '<br />'], '', $string);

    // It is conceivable that people might still want single line-breaks
    // without breaking into a new paragraph.
    if ($line_breaks == TRUE) {
      return '<p>' . preg_replace([
          "/([\n]{2,})/i",
          "/([^>])\n([^<])/i",
        ], [
          "</p>\n<p>",
          '$1<br' . ($xml == TRUE ? ' /' : '') . '>$2',
        ], trim($string)) . '</p>';
    }
    else {
      return '<p>' . preg_replace(
          ["/([\n]{2,})/i", "/([\r\n]{3,})/i", "/([^>])\n([^<])/i"],
          [
            "</p>\n<p>",
            "</p>\n<p>",
            '$1<br' . ($xml == TRUE ? ' /' : '') . '>$2',
          ],

          trim($string)) . '</p>';
    }
  }

}
