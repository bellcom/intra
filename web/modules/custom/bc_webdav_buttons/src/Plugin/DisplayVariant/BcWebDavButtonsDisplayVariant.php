<?php

namespace Drupal\bc_webdav_buttons\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Node\NodeInterface;

/**
 * Provides a 'BC WebDav Buttons' block.
 *
 * @Block(
 *   id = "bc_webdav_buttons",
 *   admin_label = @Translation("BC WebDav Buttons"),
 *   category = @Translation("Custom")
 * )
 */
class BCWebDavButtonsBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $node = \Drupal::routeMatch()->getParameter('node');
    $user = \Drupal::currentUser();

    // Allowed extensions
    $extensions = [
      'ms-word' => ['docx', 'doc', 'xls', 'dot', 'wbk', 'docm', 'dotm', 'docb'],
      'ms-excel' => ['xls', 'xlt', 'xlm', 'xlsx', 'xlsm', 'xltx', 'xltm', 'csv', 'xlsb'],
      'ms-powerpoint' => ['ppt', 'pot', 'pps', 'pptx', 'pptm', 'potx', 'ppam', 'ppsx', 'ppsm', 'sldx', 'sldm'],
      'ms-publisher' => ['pub'],
    ];

    // Check if the user is a member of any groups associated with the node.
    $user_is_member = false;

    if ($node) {
      $user_is_member = $this->rkWebDavButtonsSpecialUsersPermissions($node, $user);

      if (!$user_is_member) {
        $group_ids = [];

        if (!empty($node->get('og_group_ref')->referencedEntities())) {
          foreach ($node->get('og_group_ref')->referencedEntities() as $group) {
            $group_ids[] = $group->id();
          }

          $query = \Drupal::database()
            ->select('og_membership', 'ogm')
            ->condition('ogm.gid', $group_ids, 'IN')
            ->condition('ogm.entity_type', 'user')
            ->condition('ogm.etid', $user->id())
            ->fields('ogm', ['entity_type', 'etid']);

          $result = $query->execute();
          $user_is_member = !empty($result->fetchAll());
        } else {
          $user_is_member = false;
        }
      }
    }

    // Construct your display content.
    $content = $this->buildDisplayContent($node, $user_is_member, $extensions);

    return $content;
  }

  /**
   * Builds the display content.
   *
   * @param \Drupal\node\NodeInterface|null $node
   *   The node object.
   * @param bool $user_is_member
   *   Whether the user is a member.
   * @param array $extensions
   *   The allowed extensions.
   *
   * @return array
   *   The render array for display content.
   */
  protected function buildDisplayContent($node, $user_is_member, $extensions) {
    $build = [];

    if ($node) {
      $build['#markup'] = '<div class="rk-webdav-buttons-pane-title">' . $this->t('File management') . '</div>';
      $build['#markup'] .= '<div id="rk-webdav-button-faded"></div>';
      $build['#markup'] .= '<div id="rk-webdav-button-log"></div>';

      if ($node->getType() === 'file') {
        foreach ($node->get('field_file')->referencedEntities() as $file) {
          $build['#markup'] .= $this->buildDisplayItem($file, $extensions, $node, $user_is_member);
        }
      }
    }

    return $build;
  }

  /**
   * Builds the display item for a file.
   *
   * @param \Drupal\file\FileInterface $file
   *   The file entity.
   * @param array $extensions
   *   The allowed extensions.
   * @param \Drupal\node\NodeInterface $node
   *   The node object.
   * @param bool $user_is_member
   *   Whether the user is a member.
   *
   * @return string
   *   The HTML markup for the display item.
   */
  protected function buildDisplayItem($file, $extensions, $node, $user_is_member) {
    $base_url = \Drupal::request()->getSchemeAndHttpHost();
    $file_path = file_create_url($file->getFileUri());
    $pathinfo = pathinfo($file_path);

    $scheme_to_use = null;
    foreach ($extensions as $scheme => $scheme_extensions) {
      if (in_array($pathinfo['extension'], $scheme_extensions)) {
        $scheme_to_use = $scheme;
      }
    }

    $icon_directory = \Drupal::config('file.settings')->get('icon_directory');
    $icon = theme('file_icon', ['file' => $file, 'icon_directory' => $icon_directory]);

    $content = '<div class="rk-webdav-button-item-wrapper">';
    $content .= '<div class="rk-webdav-file-name-wrapper">' . $icon . ' ' . $file->getFilename() . '</div>';
    $content .= '<div class="rk-webdav-button-buttons-wrapper">';

    // Download button
    $content .= '<a href="' . $base_url . $file_path . '" class="rk-webdav-button rk-webdav-button-download" title="' . $this->t('Download') . '">';
    $content .= '<div class="rk-webdav-button-icon"></div>';
    $content .= '</a>';

    // View button
    $content .= '<a href="' . $base_url . '/document-handle?scheme=' . $scheme_to_use . '&mode=view&filepath=' . urlencode($file_path) . '&nid=' . $node->id() . '" class="rk-webdav-button rk-webdav-button-view" data-nid="' . $node->id() . '" data-fid="' . $file->id() . '" title="' . $this->t('View') . '">';
    $content .= '<div class="rk-webdav-button-icon"></div>';
    $content .= '</a>';

    // Edit button
    $content .= '<a href="' . $base_url . '/document-handle?scheme=' . $scheme_to_use . '&mode=edit&filepath=' . urlencode($file_path) . '&nid=' . $node->id() . '" class="rk-webdav-button rk-webdav-button-edit ' . (!$user_is_member ? 'rk-webdav-button-disabled' : '') . '" data-nid="' . $node->id() . '" data-fid="' . $file->id() . '" title="' . ($user_is_member ? $this->t('Edit') : $this->t('You don\'t have access to edit this file, contact the file owner to get access.')) . '">';
    $content .= '<div class="rk-webdav-button-icon"></div>';
    $content .= '</a>';

    // History button
    $content .= '<a href="#" class="rk-webdav-button rk-webdav-button-history" title="' . $this->t('History') . '" data-fid="' . $file->id() . '">';
    $content .= '<div class="rk-webdav-button-icon"></div>';
    $content .= '</a>';

    $content .= '</div>';
    $content .= '</div>';

    return $content;
  }


  /**
   * Checks if the user has special permissions for editing.
   *
   * @param \Drupal\node\NodeInterface $node
   *   The node object.
   * @param \Drupal\Core\Session\AccountInterface $user
   *   The current user.
   *
   * @return bool
   *   TRUE if the user has special permissions, FALSE otherwise.
   */
  protected function rkWebDavButtonsSpecialUsersPermissions($node, $user) {
    if ($node->getOwnerId() == $user->id()) {
      return TRUE;
    }

    $allowed_users = $node->get('field_brugere')->referencedEntities();
    foreach ($allowed_users as $user_entity) {
      if ($user_entity->id() == $user->id()) {
        return TRUE;
      }
    }

    return FALSE;
  }

}
