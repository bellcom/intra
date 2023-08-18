<?php

namespace Drupal\bc_utility\Plugin\Condition;

use Drupal\node\NodeInterface;
use Drupal\rules\Core\RulesConditionBase;

/**
 * Provides an 'Node is older than' condition.
 *
 * @Condition(
 *   id = "bc_utility_node_older_than",
 *   label = @Translation("Node is older than"),
 *   category = @Translation("Content"),
 *   context_definitions = {
 *     "node" = @ContextDefinition("entity:node",
 *       label = @Translation("Node"),
 *       description = @Translation("Specifies the node for which to evaluate the condition."),
 *       assignment_restriction = "selector"
 *     ),
 *     "datetime" = @ContextDefinition("string",
 *       label = @Translation("Modifed before"),
 *       description = @Translation("The entity was edited longer ago than specified timeperiod, use strtotime() syntax, expected result is timestamp"),
 *       assignment_restriction = "input"
 *     ),
 *   }
 * )
 */
class NodeIsOlderThan extends RulesConditionBase {

  /**
   * Check if the provided node modifier longer ago than specified period.
   *
   * @param \Drupal\node\NodeInterface $node
   *   The node to check.
   * @param string $datetime
   *   The datetime.
   *
   * @return bool
   *   TRUE if the node is was modified before the specified time.
   */
  protected function doEvaluate(NodeInterface $node, $datetime) {
    $datetimeTs = strtotime($datetime);

    // Check to see whether the node was modified before specified time.
    return $node->getChangedTime() <= $datetimeTs;
  }

}
