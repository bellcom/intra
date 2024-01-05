<?php

namespace Drupal\bc_webdav\Controller;

use Drupal\Core\Controller\ControllerBase;
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Diactoros\Response\HtmlResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;


Class WebdavHandlerController extends ControllerBase {

  private function downloadFile($fileId='') {
    $files = \Drupal::entityTypeManager()
      ->getStorage('file')
      ->loadByProperties(['uuid' => $fileId]);

    if (!empty($files)) {
      $file = reset($files);

      $headers = array(
        'Content-Type' => $file->filemime->value,
        'Content-Description' => 'File Download',
        'Content-Disposition' => 'attachment; filename=' . $file->label(),
        'Cache-Control' => 'private'
      );

      return new BinaryFileResponse($file->uri->value, 200, $headers, true );

    }
  }


  private function viewFile($fileId='') {

    $files = \Drupal::entityTypeManager()
      ->getStorage('file')
      ->loadByProperties(['uuid' => $fileId]);

    $html = '';
    if (!empty($files)) {
      $file = reset($files);
      $html = '<a id="webdavfilelink" href="' . $file->createFileUrl(FALSE) . '" target="_blank">#</a>';
      $html .= '<script> document.getElementById("webdavfilelink").click(); </script>';
    }

    return new HtmlResponse($html);

  }



  private function editFile($fileId=null) {


  }


  private function historyFile($fileId=null) {


  }


  /**
   * Returns a simple page.
   *
   * @return array
   *   A simple renderable array.
   */
  public function HandlerPage() {

    $action = \Drupal::request()->query->get('action');
    $fileId = \Drupal::request()->query->get('id');

    if ($action === 'download') return $this->downloadFile($fileId);
    else if ($action === 'view') return $this->viewFile($fileId);


    return new JsonResponse(array(
      'data' => array(
        'history' => "",
        'request' => $_REQUEST,
        'get' => $_GET,
        'post' => $_POST,
        'time' => time(),
        'action' => $action
      )
    ));

//    $response = new Response();
//    return $response;

  }



}


