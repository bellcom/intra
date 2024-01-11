<?php

namespace Drupal\bc_og_extension\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Url;
use Drupal\node\NodeInterface;
use Drupal\og\GroupTypeManager;

/**
 * Provides a 'Hello' Block.
 *
 * @Block(
 *   id = "bc_og_create_content_block",
 *   admin_label = @Translation("Bellcom OG Create content block"),
 *   category = @Translation("Organic Groups"),
 * )
 */
class OgCreateContentBlock extends BlockBase {

  /**
   * Group Type manager.
   *
   * @var GroupTypeManager|mixed
   */
  protected GroupTypeManager $groupTypeManager;

  /**
   * Node Type storage.
   *
   * @var EntityStorageInterface
   */
  protected EntityStorageInterface $nodeTypeStorage;

  public function __construct(array $configuration, $plugin_id, $plugin_definition) {
    $this->groupTypeManager = \Drupal::service('og.group_type_manager');
    $this->nodeTypeStorage = \Drupal::entityTypeManager()->getStorage('node_type');

    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $links = [];

    $node = \Drupal::routeMatch()->getParameter('node');
    if ($node instanceof NodeInterface) {
      $allGroupBundles = $this->groupTypeManager->getAllGroupContentBundleIds();
      $nodeGroupTypes = [];
      if (isset($allGroupBundles['node'])) {
        $nodeGroupTypes = $allGroupBundles['node'];
      }

      foreach ($nodeGroupTypes as $nodeType) {
        $links[] = [
          'title' => $this->nodeTypeStorage->load($nodeType)->label(),
          'url' => Url::fromRoute('node.add', ['node_type' => $nodeType], ['query' => ['edit[og_audience][widget][0][target_id]' => $node->id()]]),
        ];
      }
    }

    return [
      '#theme' => 'links',
      '#links' => $links,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheContexts() {
    return Cache::mergeContexts(parent::getCacheContexts(), ['url.path']);
  }
}
