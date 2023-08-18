<?php

namespace Drupal\faxe_d7_migration\Utility;

use Drupal\Core\Database\Database;
use Drupal\file\Entity\File;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\taxonomy\Entity\Term;
use Drupal\media\Entity\Media;

class MigrationHelper {

  public static $siteUrl = 'https://lokalarkiv.faxekommune.dk';
  public static $fileFolderPath = 'lokalarkiv.subsites.faxekommune.dk';

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
        $fileUrl = preg_replace('/(public:\/\/)/', MigrationHelper::$siteUrl . '/sites/'. MigrationHelper::$fileFolderPath .'/files/', $fileUrl);
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
   * Create wrapper paragraph.
   *
   * @param $field_os2web_base_field_spotbox
   *
   * @return array
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  static function createSpotboxParagraph($field_os2web_base_field_spotbox){

    if (!$field_os2web_base_field_spotbox ) {
      return [];
    }
    if (is_array($field_os2web_base_field_spotbox) ) {
      return [];
    }
    $spotbox_paragraph = Paragraph::create([
      'type' => 'os2web_spotbox_reference',
      'field_os2web_spotbox_reference' => [
        'target_id' => $field_os2web_base_field_spotbox
      ]
    ]);

    $spotbox_paragraph->save();
    $return = [
      '0' => $spotbox_paragraph->id(),
      '1' => $spotbox_paragraph->getRevisionId(),
   ];

    return $return;
  }

  /**
   * Create os2web page paragraph narrow.
   *
   * @param $values
   *
   * @return array
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  static function migrateNarrowParagraph($values) {
    $return = [];
    if (!is_array($values)) {
      return [];
    };
    foreach ($values as $key => $arr) {
      if (isset($values[0]) && is_array($values[0])) {
        foreach ($arr as $value) {
          if (is_array($value)) {
            $return[] = [
              'target_id' => $value[0],
              'target_revision_id' => $value[1],
            ];
          } else {
            $return[] = [
              'target_id' => $values[$key][0],
              'target_revision_id' => $values[$key][1],
            ];
            break;
          }
        }
      }
    }

    return ($return);
  }
  /**
   * Helper static function to populate links fields.
   *
   * @param $link
   *   Link field.
   *
   * @return int|null
   *   Int if the local node is found. NULL otherwise.
   */
  static function getFieldLinks($link) {
    if (strpos($link['url'], 'node') === 0) {
      $urlParts = explode('/', $link['url']);

      // Finding local node URL, if present.
      if ($localNid = MigrationHelper::findLocalNode($urlParts[1])) {
        $urlParts[1] = $localNid;
        $link['url'] = implode('/', $urlParts);
      }
    }

    $fieldLink = [
      'title' => $link['title'],
      'url' => $link['url']
    ];

    return $fieldLink;
  }

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
    if (parse_url($link, PHP_URL_HOST) == parse_url(MigrationHelper::$siteUrl, PHP_URL_HOST)) {
      $link = parse_url($link, PHP_URL_PATH);
    }
    if (strpos($link, 'node') === 0) {
      $urlParts = explode('/', $link);

      // Finding local node URL, if present.
      if ($localNid = MigrationHelper::findLocalNode($urlParts[1])) {
        $urlParts[1] = $localNid;
        $link = implode('/', $urlParts);
      }
    }

    return $link;
  }

  /**
   * Helper static function to populate menu link.
   *
   * @param $link
   *   Link field.
   *
   * @return int|null
   *   Int if the local node is found. NULL otherwise.
   */
  static function migrateRelatedLinks($links) {
    if ($localNid = MigrationHelper::findLocalNode($links['nid'])) {
      return [
        'target_id' => $localNid
      ];
    }
    return [];
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

}
