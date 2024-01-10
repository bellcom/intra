<?php

namespace Drupal\bc_webdav\Controller;

use Drupal\Core\Controller\ControllerBase;
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Diactoros\Response\HtmlResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Drupal\Core\File\FileSystemInterface;

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

    $html = '<script> alert(" something went wrong ! "); </script>';
    $config = (object) \Drupal::config('bc_webdav.settings')->get();
    if ($config->enabled && !empty($config->folder)) {

      $files = \Drupal::entityTypeManager()
        ->getStorage('file')
        ->loadByProperties(['uuid' => $fileId]);

      if (!empty($files)) {

        $file = reset($files);
        $file_uri = $file->uri->value;
        $file_name = $file->filename->value;

        if (file_exists($config->folder) && is_writable($config->folder) && !file_exists($config->folder . '/' . $file_name)) {

          $file_system = \Drupal::service('file_system');
          $file_system->copy($file_uri, $config->folder . '/' . $file_name, FileSystemInterface::EXISTS_REPLACE);

          if (file_exists($config->folder . '/' . $file_name)) {
            file_put_contents($config->folder . '/id.' . $file_name, trim($file->id()) . "\n");
            $html = '<script> alert(" ready "); </script>';
          }
        } elseif (file_exists($config->folder . '/' . $file_name)) {
          $html = '<script> alert(" file is edit by a other user, try later "); </script>';
        }
      }
    }

    return new HtmlResponse($html);

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
    else if ($action === 'edit') return $this->editFile($fileId);

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


