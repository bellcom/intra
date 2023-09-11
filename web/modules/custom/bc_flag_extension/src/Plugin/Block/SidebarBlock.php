<?php

namespace Drupal\bc_flag_extension\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a block to flag info
 *
 * @Block(
 *   id = "sidebar",
 *   admin_label = @Translation("Sidebar for flag info"),
 *   category = @Translation("Sidebar Block")
 * )
 */
Class SidebarBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = array(
      'sidebar' => array(
        '#theme' => 'sidebar',
        '#variables' => array(
          "#vars" => array(),
          '#config' => array()
        ),
        '#attached' => array(
          'library' => array(
            'bc_flag_extension/flag_sidebar_attachment'
          )
        )
      )
    );

    return $build;

  }

  /**
   * @return int
   */
  public function getCacheMaxAge() {
    return 0;
  }

}
