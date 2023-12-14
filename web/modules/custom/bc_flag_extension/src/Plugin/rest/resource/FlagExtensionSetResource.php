<?php

namespace Drupal\bc_flag_extension\Plugin\rest\resource;


use Drupal\rest\ModifiedResourceResponse;
use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Provides a Set Resource
 *
 * @RestResource(
 *   id = "bc_flag_extension_set_resource",
 *   label = @Translation("bc flag extension Set Resource"),
 *   uri_paths = {
 *     "canonical" = "/bc_flag_extension/set/{flag}"
 *   }
 * )
 */
class FlagExtensionSetResource extends ResourceBase {

  public function get(Request $request) {

    $response = array(
      'success' => false
    );

    if ($request->get('flag') == 'unread' && $request->get('action') == 'unreadall') {
        $response['action'] = $request->get('action');
        $user = \Drupal::currentUser();
        if ($user && !$user->isAnonymous()) {
          $result = \Drupal::database()->delete('flagging')
            ->condition('flag_id', 'unread')
            ->condition('uid', $user->id())
            ->execute();
          $response['success'] = true;
          $response['result'] = $result;
        }
    }

    return new ModifiedResourceResponse($response, 200);
  }


}