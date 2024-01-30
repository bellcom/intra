<?php
namespace Drupal\bc_node_tabs\Controller;

use Drupal\bc_basic\Debug;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

Class AutocompleteController extends ControllerBase
{
  public function handlerAutocomplete(Request $request) {
    $found = [];

    $str = $request->query->get('q');
    $str = trim($str);
    $str = addcslashes($str, '/');
    if (strlen($str) > 2) {
      $routes = \Drupal::service('router.route_provider')->getAllRoutes();
      foreach ($routes as $route) {
        $path = $route->getPath();
        $regx = "/^" . $str . "(.*)/";
        if (preg_match($regx, $path)) {

          $allBrackets = 0;
          if (preg_match_all('/{(.*?)}/', $path, $matches)) {
            $allBrackets = count($matches[0]);
          }

          if ($allBrackets == 0) {
            $found[] = array('value' => $path, 'label' => $path );

          } else {
            $nodeBracket = 0;
            if (preg_match_all('/{node}/', $path, $matches)) {
              $nodeBracket = count($matches[0]);
            }
            if ($nodeBracket == $allBrackets) {
              $found[] = array('value' => $path, 'label' => $path );
            }
          }
        }
      }
    }

    return new JsonResponse($found);
  }


}