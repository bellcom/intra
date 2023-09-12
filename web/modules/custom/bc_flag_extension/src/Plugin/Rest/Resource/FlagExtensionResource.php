<?php

namespace Drupal\bc_flag_extension\Plugin\rest\resource;

use Drupal\node\Entity\Node;
use Drupal\rest\Plugin\ResourceBase;
//use Drupal\rest\ResourceResponse;
use Drupal\rest\ModifiedResourceResponse;
use Drupal\views\Views;
use Symfony\Component\HttpFoundation\Request;

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
   *
   * @param \Symfony\Component\HttpFoundation\Request
   *
   * @return \Drupal\rest\ModifiedResourceResponse
   * @throws \Drupal\Core\Entity\EntityMalformedException
   */
  public function get(Request $request) {
    $nid = $request->get('nid') ?? 0;
    $config = \Drupal::config('bc_flag_extension.settings')->get();
    unset( $config['submit'], $config['form_build_id'], $config['form_token'], $config['form_id'], $config['op']);

    $vars = array(
      'loggedin' => NULL,
      'bookmarks' => [],
      'shortcuts' => [],
      'unreads' => []
    );

    if (!empty($config['enabled'])) {

      $currentUser = \Drupal::currentUser();
      if ($currentUser->id()) {
        $currentUser = \Drupal\user\Entity\User::load(\Drupal::currentUser()->id());
      } else $currentUser = null;

      $currentNode = \Drupal::routeMatch()->getParameter('node');
      if (!$currentNode instanceof \Drupal\node\NodeInterface) {
        $currentNode = null;
      } else if ($nid) {
        $currentNode = Node::load($request->get('nid'));
        if (!$currentNode instanceof \Drupal\node\NodeInterface) {
          $currentNode = NULL;
        }
      }

      $vars['loggedin'] = ($currentUser ? true:false);
      $session_id = ($currentUser ? null:\Drupal::service('session_manager')->getId());

      // bookmark
      if (!empty($config['bookmark'])) {
        $view = Views::getView('flag_extension');
        $view->setDisplay('default');
        $view->setExposedInput(array(
          'combine' => ($currentUser ? $currentUser->id():$session_id),
          'flag_id' => 'bookmark'
        ));
        $view->execute();

        foreach($view->result AS $result) {
          $res = $result->_entity;
          if ($res->get('entity_type')->value == 'node') {
            $node = Node::load($res->get('entity_id')->value);
            $vars['bookmarks'][$node->id()] = array(
              'title' => $node->getTitle(),
              'link' => $node->toUrl()->setAbsolute()->toString(),
              'flag' => $res->get('uuid')->value
            );
          }
        }
      }

      // shortcut
      if (!empty($config['shortcut']) && $currentUser) {
        $view = Views::getView('flag_extension');
        $view->setDisplay('default');
        $view->setExposedInput(array(
          'combine' => ($currentUser ? $currentUser->id():$session_id),
          'flag_id' => 'shortcut'
        ));
        $view->execute();

        foreach($view->result AS $result) {
          $res = $result->_entity;
          if ($res->get('entity_type')->value == 'node') {
            $node = Node::load($res->get('entity_id')->value);
              $vars['shortcuts'][$node->id()] = [
                'title' => $node->getTitle(),
                'link' => $node->toUrl()->setAbsolute()->toString(),
                'flag' => $res->get('uuid')->value
              ];
          }
        }
      }

      // unread
      if (!empty($config['unread']) && $currentUser) {
        $view = Views::getView('flag_extension');
        $view->setDisplay('default');
        $view->setExposedInput(array(
          'combine' => ($currentUser ? $currentUser->id():$session_id),
          'flag_id' => 'unread'
        ));
        $view->execute();

        foreach($view->result AS $result) {
          $res = $result->_entity;
          if ($res->get('entity_type')->value == 'node') {
            $node = Node::load($res->get('entity_id')->value);
            if ($currentNode && $currentNode->id() == $node->id() && $currentUser) {
              $flagService = \Drupal::service('flag');
              $flag = $flagService->getFlagById('unread');
              $flagService->unflag($flag, $node, $currentUser);
              $flagging = $flagService->getFlagging($flag, $node, $currentUser);
              if ($flagging) {
                $flagService->unflag($flag, $node, $currentUser);
                $flagService->save();
              }
            } else {
              $vars['unreads'][$node->id()] = [
                'title' => $node->getTitle(),
                'link' => $node->toUrl()->setAbsolute()->toString(),
                'flag' => $res->get('uuid')->value
              ];
            }
          }
        }
      }
    }

    $response = $vars;

    return new ModifiedResourceResponse($response);
  }


}

