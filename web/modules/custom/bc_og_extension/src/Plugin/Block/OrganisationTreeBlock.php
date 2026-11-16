<?php

namespace Drupal\bc_og_extension\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\node\NodeInterface;
use Drupal\user\UserInterface;

/**
 * Renders departments as a nested tree from the parent field.
 *
 * @Block(
 *   id = "bc_og_organisation_tree",
 *   admin_label = @Translation("Organisationsstruktur"),
 *   category = @Translation("Organic Groups"),
 * )
 */
class OrganisationTreeBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $departments = $this->loadDepartments();
    $tree = $this->buildLevel($this->indexByParent($departments), $departments, 0);

    return [
      '#theme' => 'organisation_tree',
      '#items' => $tree,
      '#cache' => [
        'tags' => Cache::mergeTags(['node_list:group', 'og_membership_list', 'user_list'], $this->getCacheTags()),
        'contexts' => ['user.permissions'],
      ],
    ];
  }

  /**
   * Loads published department groups.
   *
   * @return array<int, array<string, mixed>>
   *   Departments keyed by node id.
   */
  protected function loadDepartments(): array {
    $storage = \Drupal::entityTypeManager()->getStorage('node');
    $nodes = $storage->loadByProperties([
      'type' => 'group',
      'status' => 1,
    ]);

    $departments = [];
    foreach ($nodes as $node) {
      if (!$node instanceof NodeInterface || !$this->isDepartment($node)) {
        continue;
      }

      $parent = $node->get('field_overafdeling')->target_id;
      $departments[(int) $node->id()] = [
        'title' => $node->label(),
        'url' => $node->toUrl()->toString(),
        'parent' => $parent ? (int) $parent : 0,
        'members' => $this->loadMembers((int) $node->id()),
      ];
    }

    return $departments;
  }

  /**
   * Checks that a group is a department.
   */
  protected function isDepartment(NodeInterface $node): bool {
    if (!$node->hasField('field_og_group_type') || !$node->hasField('field_overafdeling')) {
      return FALSE;
    }

    $term = $node->get('field_og_group_type')->entity;
    return $term && $term->label() === 'Afdeling';
  }

  /**
   * Loads active members of a department.
   *
   * @return array<int, array<string, string>>
   *   Member name and url.
   */
  protected function loadMembers(int $group_id): array {
    $uids = \Drupal::database()->query(
      "SELECT uid FROM {og_membership} WHERE entity_type = :entity_type AND entity_id = :entity_id AND state = :state ORDER BY uid",
      [
        ':entity_type' => 'node',
        ':entity_id' => $group_id,
        ':state' => 'active',
      ]
    )->fetchCol();

    if (!$uids) {
      return [];
    }

    $users = \Drupal::entityTypeManager()->getStorage('user')->loadMultiple($uids);
    $members = [];
    foreach ($users as $user) {
      if (!$user instanceof UserInterface || !$user->isActive()) {
        continue;
      }
      $members[] = [
        'name' => $user->getDisplayName(),
        'url' => $user->toUrl()->toString(),
      ];
    }

    return $members;
  }

  /**
   * Groups department ids by their parent id.
   *
   * @param array<int, array<string, mixed>> $departments
   *   Departments keyed by node id.
   *
   * @return array<int, int[]>
   *   Child ids keyed by parent id. Roots use 0.
   */
  protected function indexByParent(array $departments): array {
    $children = [];
    foreach ($departments as $id => $department) {
      $parent = $department['parent'];
      if (!$parent || !isset($departments[$parent])) {
        $parent = 0;
      }
      $children[$parent][] = $id;
    }

    return $children;
  }

  /**
   * Builds one level of the tree.
   *
   * @param array<int, int[]> $children
   *   Child ids keyed by parent id.
   * @param array<int, array<string, mixed>> $departments
   *   Departments keyed by node id.
   * @param int $parent
   *   Parent id. 0 is the root.
   *
   * @return array<int, array<string, mixed>>
   *   Render arrays for this level.
   */
  protected function buildLevel(array $children, array $departments, int $parent): array {
    $ids = $children[$parent] ?? [];
    usort($ids, function (int $a, int $b) use ($departments): int {
      return strcasecmp($departments[$a]['title'], $departments[$b]['title']);
    });

    $items = [];
    foreach ($ids as $id) {
      $department = $departments[$id];
      $items[] = [
        '#theme' => 'organisation_tree_item',
        '#title' => $department['title'],
        '#url' => $department['url'],
        '#members' => $department['members'],
        '#children' => $this->buildLevel($children, $departments, $id),
      ];
    }

    return $items;
  }

}
