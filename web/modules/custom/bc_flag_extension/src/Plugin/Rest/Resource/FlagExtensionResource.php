<?php

namespace Drupal\bc_flag_extension\Plugin\rest\resource;

use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Drupal\rest\ModifiedResourceResponse;

/**
 * Provides a flag extension Resource
 *
 * @RestResource(
 *   id = "data",
 *   label = @Translation("Flag extension Resource"),
 *   uri_paths = {
 *     "canonical" = "/bc_flag_extension/data"
 *   }
 * )
 */
class FlagExtensionResource extends ResourceBase {

  /**
   * Responds to entity GET requests.
   * @return \Drupal\rest\ResourceResponse
   */
  public function get() {

    $config = \Drupal::config('bc_flag_extension.settings')->get();
    unset( $config['submit'], $config['form_build_id'], $config['form_token'], $config['form_id'], $config['op']);

    $vars = array(
      'loggedin' => NULL,
      'bookmarks' => [],
      'shortcuts' => [],
      'unreads' => []
    );

    if (!empty($config['enabled'])) {

      $vars['loggedin'] = ($user_id = \Drupal::currentUser()->id()) ? true:false;
      $session_id = ($user_id ? null:\Drupal::service('session_manager')->getId());

      // bookmark
      if (!empty($config['bookmark'])) {
        $view = \Drupal\views\Views::getView('flag_extension');
        $view->setDisplay('default');
        $view->setExposedInput(array(
          'combine' => ($user_id ?? $session_id),
          'flag_id' => 'bookmark'
        ));
        $view->execute();

        foreach($view->result AS $result) {
          $res = $result->_entity;
          if ($res->get('entity_type')->value == 'node') {
            $node = $node = \Drupal\node\Entity\Node::load($res->get('entity_id')->value);
            $vars['bookmarks'][$node->id()] = array(
              'title' => $node->getTitle(),
              'link' => $node->toUrl()->setAbsolute()->toString(),
              'flag' => $res->get('uuid')->value
            );
          }
        }
      }

      // shortcut
      if (!empty($config['shortcut']) && $user_id) {
        $view = \Drupal\views\Views::getView('flag_extension');
        $view->setDisplay('default');
        $view->setExposedInput(array(
          'combine' => ($user_id ?? $session_id),
          'flag_id' => 'shortcut'
        ));
        $view->execute();

        foreach($view->result AS $result) {
          $res = $result->_entity;
          if ($res->get('entity_type')->value == 'node') {
            $node = $node = \Drupal\node\Entity\Node::load($res->get('entity_id')->value);
            $vars['shortcuts'][$node->id()] = array(
              'title' => $node->getTitle(),
              'link' => $node->toUrl()->setAbsolute()->toString(),
              'flag' => $res->get('uuid')->value
            );
          }
        }
      }

      // unread
      if (!empty($config['unread']) && $user_id) {
        $view = \Drupal\views\Views::getView('flag_extension');
        $view->setDisplay('default');
        $view->setExposedInput(array(
          'combine' => ($user_id ?? $session_id),
          'flag_id' => 'unread'
        ));
        $view->execute();

        foreach($view->result AS $result) {
          $res = $result->_entity;
          if ($res->get('entity_type')->value == 'node') {
            $node = $node = \Drupal\node\Entity\Node::load($res->get('entity_id')->value);
            $vars['unreads'][$node->id()] = array(
              'title' => $node->getTitle(),
              'link' => $node->toUrl()->setAbsolute()->toString(),
              'flag' => $res->get('uuid')->value
            );
          }
        }
      }


    }

    $response = $vars;

    return new ModifiedResourceResponse($response);
  }


}

