<?php

namespace Drupal\bc_webdav_buttons\Controller;

use Drupal\Core\Controller\ControllerBase;

class BcWebdavButtonsController extends ControllerBase {

  /**
   * Page callback for the WebDav Log.
   */
  public function log() {
    $user = \Drupal::currentUser();

    // Save log to our custom table 'bc_webdav_buttons_log'.
    $query = \Drupal::database()->insert('bc_webdav_buttons_log')
      ->fields([
        'unix_timestamp' => REQUEST_TIME,
        'nid' => \Drupal::request()->query->get('nid') ?? 0,
        'uid' => $user->id(),
        'fid' => \Drupal::request()->query->get('fid') ?? 0,
        'action' => \Drupal::request()->query->get('action') ?? 'none',
      ])
      ->execute();

    // Additional logic for the WebDav Log.

    return [
      '#markup' => $this->t('WebDav Log content goes here.'),
    ];
  }

  /**
   * Other controller methods can go here.
   */

}
