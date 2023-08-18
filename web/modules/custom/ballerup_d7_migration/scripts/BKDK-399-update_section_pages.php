<?php

/**
 * This script migrates images and list of related node to nodes.
 *
 * See task: https://os2web.atlassian.net/browse/BKDK-399
 */

use Drupal\ballerup_d7_migration\Utility\MigrationHelper;
use Drupal\Core\Database\Database;
use Drupal\file\FileInterface;
use Drupal\node\Entity\Node;
use Drupal\paragraphs\Entity\Paragraph;

$database = \Drupal::database();
$migrateDatabase = Database::getConnection('default', 'migrate');
$migrationHelper = new MigrationHelper();

$relatedLinksToProcess = [];

// Counters.
$countPicturesUpdated = 0;
$countRelatedPagesParagraphsAdded = 0;

$query = $migrateDatabase->select('taxonomy_term_data', 'term')
  ->condition('vid', "2");
$query->leftJoin('field_data_field_section_image', 'image', 'image.entity_id = term.tid');
$query->leftJoin('field_data_field_perferred_links_multiple', 'links', 'links.entity_id = term.tid');
$query->fields('term', []);
$query->fields('image', ['field_section_image_fid']);
$query->fields('links', ['field_perferred_links_multiple_target_id']);
$terms = $query->execute()->fetchAll();

foreach ($terms as $term) {
  $termTitle = $term->name;

  $hasPicture = !empty($term->field_section_image_fid);
  $hasLink = !empty($term->field_perferred_links_multiple_target_id);

  // Adding pictured to the nodes.
  if ($hasPicture) {
    // Find page with the name.
    $query = \Drupal::entityQuery('node')
      ->condition('type', 'os2web_page')
      ->condition('title', $termTitle);
    $results = $query->execute();

    if (empty($results)) {
      continue;
    }

    $nodes = Node::loadMultiple($results);

    foreach ($nodes as $node) {
      // Check if has picture, if not set it.
      if (!$node->field_os2web_page_primaryimage->target_id) {
        $sourceFileUrl = $migrationHelper->getFileDownloadUrl(['fid' => $term->field_section_image_fid]);
        $file_name = basename($sourceFileUrl);

        // Saving file.
        $directory = 'public://';
        $file = system_retrieve_file($sourceFileUrl, $directory . '/' . $file_name, TRUE);

        if ($file instanceof FileInterface) {
          $node->field_os2web_page_primaryimage->target_id = $file->id();
          $node->save();
          $countPicturesUpdated++;
        }
      }
    }
  }

  if ($hasLink) {
    // Find page with the name.
    $query = \Drupal::entityQuery('node')
      ->condition('type', 'os2web_page')
      ->condition('title', $termTitle);
    $results = $query->execute();

    if (empty($results)) {
      continue;
    }

    $nodes = Node::loadMultiple($results);

    // Create an array of nodes that need links to be added.
    foreach ($nodes as $node) {
      $sourceNodeId = $term->field_perferred_links_multiple_target_id;

      // Find local page representing this target id.
      $localNodeId = $migrationHelper->findLocalNode($sourceNodeId);
      if ($localNodeId) {
        $relatedLinksToProcess[$node->id()][]['target_id'] = $localNodeId;
      }
    }
  }
}


// Processing related links.
foreach ($relatedLinksToProcess as $nid => $links) {
  $node = Node::load($nid);

  // Does this node already have content reference paragraph.
  $right_paragraphs = $node->get('field_os2web_page_paragraph_righ')->referencedEntities();
  if (!empty($right_paragraphs)) {
    $break = FALSE;

    /** @var \Drupal\paragraphs\Entity\Paragraph $par */
    foreach ($right_paragraphs as $par) {
      if ($par->getType() == 'os2web_content_reference') {
        $break = TRUE;
      }
    }

    // Breaking the loop if this node already have content reference paragraph.
    if ($break) {
      continue;
    }
  }

  // Creating content reference paragraph.
  if (!empty($links)) {
    $paragraph = Paragraph::create([
      'type' => 'os2web_content_reference',
      'field_os2web_content_reference_h' => 'MEST BESØGTE SIDER',
      'field_os2web_content_ref_vmod' => 'list',
      'field_os2web_content_reference' => $links,
    ]);
    $paragraph->save();

    // Getting ol paragraphs.
    $right_paragraphs_field = $node->field_os2web_page_paragraph_righ;

    // Adding a new one.
    $right_paragraphs_field[] = [
      'target_id' => $paragraph->id(),
      'target_revision_id' => $paragraph->getRevisionId(),
    ];

    // Eventually saving.
    $node->field_os2web_page_paragraph_righ = $right_paragraphs_field;
    $node->save();
    $countRelatedPagesParagraphsAdded++;
  }
}

print_r('Images updated: ' . $countPicturesUpdated);
print_r(PHP_EOL);
print_r('New related pages blocks added: ' . $countRelatedPagesParagraphsAdded);
print_r(PHP_EOL);
