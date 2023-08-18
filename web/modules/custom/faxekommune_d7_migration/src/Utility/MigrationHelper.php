<?php

namespace Drupal\faxekommune_d7_migration\Utility;

use Drupal\Core\Database\Database;
use Drupal\file\Entity\File;
use Drupal\os2web_borgerdk\Entity\BorgerdkArticle;
use Drupal\os2web_borgerdk\Entity\BorgerdkMicroarticle;
use Drupal\os2web_borgerdk\Entity\BorgerdkSelfservice;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\taxonomy\Entity\Term;
use Drupal\media\Entity\Media;

class MigrationHelper {

  public static $siteUrl = 'https://faxekommune.dk';
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
   * Create os2web_menu_box_paragraph paragraph.
   *
   * @param $tid
   *   Term id.
   *
   * @return array
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  static function createMenuBoxParagraph($tid) {
    $paragraph = Paragraph::create([
      'type' => 'os2web_menu_box_paragraph',
      'field_only_taxonomy' => 1,
      'field_os2web_menu_links_inc_pr_p' => 1,
      'field_sektion' => [
        'target_id' => MigrationHelper::findLocalSectionTerm($tid),
      ]
    ]);

    $paragraph->save();
    return [
      '0' => $paragraph->id(),
      '1' => $paragraph->getRevisionId(),
    ];
  }

  /**
   * Creates Borger.dk paragraph with the right article selected.
   *
   * @param $field_borger_dk_article_ref
   *
   * @return array
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  static function createBorgerdkParagraph($field_borger_dk_article_ref) {
    if (!$field_borger_dk_article_ref) {
      return [];
    }

    // Source values.
    $source_article_id = $field_borger_dk_article_ref['borgerdk_article_entity_id'];//35-da
    $source_ma_ids = $field_borger_dk_article_ref['borgerdk_microarticle_entity_ids'];//string as ["4b3128c3-cc75-47b6-a99d-1598b32e323d-35","5fcbc105-0f75-4fb0-86b6-7eda98a97ff4-35","a968eca3-ae02-42c0-be7a-3f6ebfd6e56a-35","47d3627e-728c-4a53-997c-646d01a96bb7-35","4e600e9f-285e-4ac4-a230-acede0d2c2f1-35","411ce265-de67-4461-8e9f-f362dedb9993-35","8dbcfdb4-d7b2-4fa3-a87f-14498b148789-35","9c850109-60db-4eee-9e8c-702593c5e103-35"]
    $source_ma_ids = preg_replace('/(\[|\"|\])/', '', $source_ma_ids);//removing " and [ and ]
    $source_ma_ids = explode(',', $source_ma_ids);// creating real array

    $source_ss_ids = $field_borger_dk_article_ref['borgerdk_selfservice_entity_ids'];//["d79189af-2f06-4eb2-b49a-8adc8ad24bab-35","37f8c2bd-c96d-44ea-8c6a-fc7a864120d9-35","a486713a-f4e5-4fc5-9c11-5d07c56e4353-35"]
    $source_ss_ids = preg_replace('/(\[|\"|\])/', '', $source_ss_ids);//removing " and [ and ]
    $source_ss_ids = explode(',', $source_ss_ids);// creating real array

    // Destination values.
    $dest_article_id = preg_replace('/\d*(-en|da)/', '', $source_article_id);//35
    /** @var \Drupal\os2web_borgerdk\BorgerdkArticleInterface $borgerdkArticle */
    $borgerdkArticle = BorgerdkArticle::loadByBorgerdkId($dest_article_id);

    // Return if we have no Borger.dk article locally.
    if (!$borgerdkArticle) {
      return [];
    }

    // Getting article microarticles.
    $dest_microarticle_ids = $borgerdkArticle->getMicroarticles(FALSE);
    // Using IDs as indices
    $dest_microarticle_ids = array_flip($dest_microarticle_ids);
    // Setting all values to 0
    $dest_microarticle_ids = array_fill_keys(array_keys($dest_microarticle_ids), 0);

    foreach ($source_ma_ids as $source_id) {
      $id = preg_replace('/(-\d+)$/', '', $source_id);//4b3128c3-cc75-47b6-a99d-1598b32e323d-35
      $microarticle = BorgerdkMicroarticle::loadByBorgerdkId($id);

      if ($microarticle && key_exists($microarticle->id(), $dest_microarticle_ids)) {
        $dest_microarticle_ids[$microarticle->id()] = $microarticle->id();
      }
    }

    // Getting article selfservice.
    $dest_selfservice_ids = $borgerdkArticle->getSelfservices(FALSE);
    // Using IDs as indices
    $dest_selfservice_ids = array_flip($dest_selfservice_ids);
    // Setting all values to 0
    $dest_selfservice_ids = array_fill_keys(array_keys($dest_selfservice_ids), 0);

    foreach ($source_ss_ids as $source_id) {
      $id = preg_replace('/(-\d+)$/', '', $source_id);//d79189af-2f06-4eb2-b49a-8adc8ad24bab-35
      $selfservice = BorgerdkSelfservice::loadByBorgerdkId($id);

      if ($selfservice && key_exists($selfservice->id(), $dest_selfservice_ids)) {
        $dest_selfservice_ids[$selfservice->id()] = $selfservice->id();
      }
    }

    // Creating Borger.dk paragraph.
    $borgerdk_paragraph = Paragraph::create([
      'type' => 'os2web_borgerdk_article',
      'field_os2web_bdk_article' => [
        'target_id' => $borgerdkArticle->id(),
        'microarticle_ids' => serialize($dest_microarticle_ids),
        'selfservice_ids' => serialize($dest_selfservice_ids),
      ]
    ]);
    $borgerdk_paragraph->save();

    return [
      0 => $borgerdk_paragraph->id(),
      1 => $borgerdk_paragraph->getRevisionId(),
    ];
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
    if (strpos($link, 'taxonomy/term') === 0) {
      $urlParts = explode('/', $link);

      $newUrl = MigrationHelper::findTermAlias($urlParts[2]);
      $link = $newUrl;
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
      'migrate_map_faxekommune_d7_node_indholdside',
      'migrate_map_faxekommune_d7_node_news'
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
   * Helper static function to find local term by remote term ID.
   *
   * Searches in migrate_map_faxekommune_d7_taxonomy_section.
   *
   * @param $remoteTermId
   *   Remote node ID.
   *
   * @return int|null
   *   Int if the local node is found. NULL otherwise.
   */
  static function findLocalSectionTerm($remoteTermId) {
    $term_migrate_tables = [
      'migrate_map_faxekommune_d7_taxonomy_section',
    ];

    $database = \Drupal::database();
    foreach ($term_migrate_tables as $table) {
      if ($database->schema()->tableExists($table)) {
        $localTid = $database->select($table)->fields($table, [
          'destid1',
        ])
          ->condition('sourceid1', $remoteTermId)
          ->execute()
          ->fetchField();

        if ($localTid) {
          return $localTid;
        }
      }
    }

    return NULL;
  }

  /**
   * Returns local KLE term ID based on remote term ID.
   *
   * @param $remoteTermId
   *   remote term ID.
   *
   * @return int|null
   *   local term id or NULL.
   */
  static function findLocalKleTerm($remoteTermId) {
    $remoteTermId = $remoteTermId['tid'];

    // Getting connection to migrate database.
    $connection = Database::getConnection('default', 'migrate');

    // Getting term name.
    $termName = $connection->select('taxonomy_term_data', 't')
      ->fields('t', array('name'))
      ->condition('t.tid', $remoteTermId)
      ->execute()
      ->fetchField();

    // Getting local term id.
    $properties = [
      'name' => $termName,
      'vid' => 'os2web_kle'
    ];

    $terms = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties($properties);
    $kleTermsTarget = [];

    foreach ($terms as $term) {
      $kleTermsTarget = ['target_id' => $term->id()];
    }

    return $kleTermsTarget;
  }

  /**
   * Find the alias for the specific term.
   *
   * Return the last created alias.
   *
   * @param $tid
   *   Term ID.
   *
   * @return string
   *   Alias of the term.
   */
  static function findTermAlias($tid) {
    // Getting connection to migrate database.
    $connection = Database::getConnection('default', 'migrate');

    $alias = $connection->select('url_alias', 'u')
      ->fields('u', array('alias'))
      ->condition('u.source', 'taxonomy/term/' . $tid)
      ->orderBy('u.pid', 'DESC')
      ->execute()
      ->fetchField();

    if ($alias) {
      return '/' . $alias;
    }
    return '';
  }

  /**
   * Get the promote value.
   *
   * @param $field_os2web_base_field
   *   Promote field.
   *
   * @return string
   */
  static function getContentPagePromote($field_os2web_base_field) {
    if (empty($field_os2web_base_field['tid'])) {
      return '0';
    }
    else {
      return '1';
    }
  }

}
