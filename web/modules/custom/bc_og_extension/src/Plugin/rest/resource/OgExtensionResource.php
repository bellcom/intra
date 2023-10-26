<?php


namespace Drupal\bc_og_extension\Plugin\rest\resource;

use Drupal\node\Entity\Node;
use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ModifiedResourceResponse;
use Drupal\views\Views;
use Symfony\Component\HttpFoundation\Request;

/**
 * Provides a og extension Resource
 *
 * @RestResource(
 *   id = "data",
 *   label = @Translation("Flag extension Resource"),
 *   uri_paths = {
 *     "canonical" = "/bc_og_extension/membership"
 *   }
 * )
 */
class OgExtensionResource extends ResourceBase
{

    /**
     * Responds to entity GET requests.
     *
     * @param \Symfony\Component\HttpFoundation\Request
     *
     * @return \Drupal\rest\ModifiedResourceResponse
     * @throws \Drupal\Core\Entity\EntityMalformedException
     */
    public function get(Request $request)
    {
        $response = array(
            'success' => true
        );

        return new ModifiedResourceResponse($response);
    }

}
