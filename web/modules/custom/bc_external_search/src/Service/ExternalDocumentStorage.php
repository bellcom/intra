<?php

namespace Drupal\bc_external_search\Service;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\node\NodeInterface;
use Psr\Log\LoggerInterface;

final class ExternalDocumentStorage {

  public function __construct(
    private readonly EntityTypeManagerInterface $entityTypeManager,
    private readonly TimeInterface $time,
    private readonly LoggerInterface $logger,
  ) {}

  /**
   * Creates or updates one imported document.
   *
   * @return array{node: \Drupal\node\NodeInterface, created: bool}
   */
  public function upsert(
    string $kind,
    string $source,
    string $externalId,
    string $title,
    string $content,
    string $summary = '',
    string $externalUrl = '',
    string $rawData = '',
  ): array {
    $storage = $this->entityTypeManager->getStorage('node');
    $ids = $storage->getQuery()
      ->accessCheck(FALSE)
      ->condition('type', 'bc_external_search_document')
      ->condition('field_bc_ext_kind', $kind)
      ->condition('field_bc_ext_source', $source)
      ->condition('field_bc_ext_id', $externalId)
      ->range(0, 1)
      ->execute();

    $node = $ids ? $storage->load(reset($ids)) : NULL;
    $created = !$node instanceof NodeInterface;

    if (!$node instanceof NodeInterface) {
      $node = $storage->create([
        'type' => 'bc_external_search_document',
        'uid' => 0,
      ]);
    }

    $node->setTitle(mb_substr(trim($title) ?: 'Uden titel', 0, 255));
    $node->set('field_bc_ext_kind', mb_substr(trim($kind), 0, 64));
    $node->set('field_bc_ext_source', mb_substr(trim($source), 0, 190));
    $node->set('field_bc_ext_id', mb_substr(trim($externalId), 0, 512));
    $node->set('field_bc_ext_summary', [
      'value' => trim($summary),
      'format' => 'plain_text',
    ]);
    $node->set('field_bc_ext_content', [
      'value' => trim($content),
      'format' => 'plain_text',
    ]);
    $node->set('field_bc_ext_raw', [
      'value' => trim($rawData),
      'format' => 'plain_text',
    ]);
    $node->set('field_bc_ext_imported', gmdate('Y-m-d\\TH:i:s', $this->time->getRequestTime()));

    if ($externalUrl !== '' && preg_match('@^https?://@i', $externalUrl)) {
      $node->set('field_bc_ext_url', ['uri' => $externalUrl]);
    }
    else {
      $node->set('field_bc_ext_url', []);
    }

    $node->setPublished(TRUE);
    $node->save();

    return ['node' => $node, 'created' => $created];
  }

  /**
   * Unpublishes imported documents that were not seen in the latest import.
   */
  public function deactivateMissing(string $kind, string $source, array $seenExternalIds): int {
    $storage = $this->entityTypeManager->getStorage('node');
    $query = $storage->getQuery()
      ->accessCheck(FALSE)
      ->condition('type', 'bc_external_search_document')
      ->condition('field_bc_ext_kind', $kind)
      ->condition('field_bc_ext_source', $source)
      ->condition('status', 1);

    if ($seenExternalIds !== []) {
      $query->condition('field_bc_ext_id', array_values(array_unique($seenExternalIds)), 'NOT IN');
    }

    $ids = $query->execute();
    if ($ids === []) {
      return 0;
    }

    $count = 0;
    foreach ($storage->loadMultiple($ids) as $node) {
      if ($node instanceof NodeInterface) {
        $node->setUnpublished();
        $node->save();
        $count++;
      }
    }

    $this->logger->notice('Deactivated @count external search documents from @source.', [
      '@count' => $count,
      '@source' => $source,
    ]);

    return $count;
  }

  /**
   * Returns number of imported documents, optionally filtered by kind/source.
   */
  public function count(?string $kind = NULL, ?string $source = NULL): int {
    $storage = $this->entityTypeManager->getStorage('node');
    $query = $storage->getQuery()
      ->accessCheck(FALSE)
      ->condition('type', 'bc_external_search_document')
      ->count();

    if ($kind !== NULL) {
      $query->condition('field_bc_ext_kind', $kind);
    }
    if ($source !== NULL) {
      $query->condition('field_bc_ext_source', $source);
    }

    return (int) $query->execute();
  }

}
