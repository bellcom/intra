<?php

namespace Drupal\bc_utility\Plugin\Condition;

use Drupal\node\NodeInterface;
use Drupal\rules\Core\RulesConditionBase;

/**
 * Provides an 'Node has moderation state' condition.
 *
 * @Condition(
 *   id = "bc_utility_node_has_moderation_state",
 *   label = @Translation("Node has moderation state"),
 *   category = @Translation("Content"),
 *   context_definitions = {
 *     "node" = @ContextDefinition("entity:node",
 *       label = @Translation("Node"),
 *       description = @Translation("Specifies the node for which to evaluate the condition."),
 *       assignment_restriction = "selector"
 *     ),
 *     "moderation_state" = @ContextDefinition("string",
 *       label = @Translation("Moderation state"),
 *       description = @Translation("Node has this moderation state"),
 *       assignment_restriction = "input"
 *     ),
 *   }
 * )
 */
class NodeHasModerationState extends RulesConditionBase {

  /**
   * Check if the provided node has the moderation state.
   *
   * @param \Drupal\node\NodeInterface $node
   *   The node to check.
   * @param string $moderation_state
   *   The moderation state to test against.
   *
   * @return bool
   *   TRUE if the node has the specified moderation state.
   */
  protected function doEvaluate(NodeInterface $node, $moderation_state) {
    $nodeModerationState = '';
    try {
      $vid = \Drupal::entityTypeManager()
        ->getStorage('node')
        ->getLatestRevisionId($node->id());

      $nodeLatestRevision = \Drupal::entityTypeManager()
        ->getStorage('node')
        ->loadRevision($vid);

      $nodeModerationState = $nodeLatestRevision->get('moderation_state')->value;
    } catch (\Exception $e) {
      // We don't want to raise this exception. So just silently catch it and ignore.
    }

    // Check to see whether the node was modified before specified time.
    return strcasecmp($nodeModerationState, $moderation_state) === 0;
  }

}
