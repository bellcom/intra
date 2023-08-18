<?php

namespace Drupal\os2web_anchor_link\Plugin\CKEditorPlugin;

use Drupal\editor\Entity\Editor;
use Drupal\ckeditor\CKEditorPluginBase;

/**
 * Defines the "link" plugin.
 *
 * @CKEditorPlugin(
 *   id = "link",
 *   label = @Translation("CKEditor Web link"),
 *   module = "anchor_link"
 * )
 */
class Os2webAnchorLink extends CKEditorPluginBase {

  /**
   * {@inheritdoc}
   */
  public function getFile() {
    $module_handler = \Drupal::service('module_handler');
    $module_path = $module_handler->getModule('os2web_anchor_link')->getPath();
    return $module_path . '/plugins/link/plugin.js';
  }

  /**
   * {@inheritdoc}
   */
  public function getDependencies(Editor $editor) {
    return [
      'fakeobjects',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getLibraries(Editor $editor) {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function isInternal() {
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function getButtons() {
    $module_handler = \Drupal::service('module_handler');
    $module_path = $module_handler->getModule('os2web_anchor_link')->getPath();

    return [
      'Link' => [
        'label' => $this->t('Link'),
        'image' => $module_path . '/plugins/link/icons/link.png',
      ],
      'Unlink' => [
        'label' => $this->t('Unlink'),
        'image' => $module_path . '/plugins/link/icons/unlink.png',
      ],
      'Anchor' => [
        'label' => $this->t('Anchor'),
        'image' =>$module_path . '/plugins/link/icons/anchor.png',
      ],



    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getConfig(Editor $editor) {
    return [];
  }
}
