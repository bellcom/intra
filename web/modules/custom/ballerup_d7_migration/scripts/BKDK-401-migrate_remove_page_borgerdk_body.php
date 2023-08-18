<?php

/**
 * This script removes header from Content pages, that have Borger.dk reference
 * paragraph
 *
 * See task: https://os2web.atlassian.net/browse/BKDK-401
 */

$storage = \Drupal::service('entity_type.manager')->getStorage('paragraph');
$paragraphs = $storage->loadByProperties(['type' => 'os2web_borgerdk_article']);

/** @var \Drupal\paragraphs\Entity\Paragraph $par */
foreach ($paragraphs as $par) {
  $node = $par->getParentEntity();
  if ($bodyText = $node->field_os2web_page_description->value) {
    $bodyText = trim(strip_tags($bodyText));
    $bodyText = preg_replace('/\.|\,/i','', $bodyText);

    /** @var \Drupal\os2web_borgerdk\Entity\BorgerdkArticle $article */
    $article = $par->get('field_os2web_bdk_article')->entity;
    $articleHeader = $article->header->value;
    $articleHeader = preg_replace('/\.|\,/i','', $articleHeader);

    if (strcmp($bodyText, $articleHeader) == 0) {
      $node->field_os2web_page_description->value = '';
      $node->save();
    }

  }
}

