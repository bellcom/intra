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

      $connection = \Drupal::service('database');
      $connection->insert('bc_webdav_log')
        ->fields([
          'fid' => $file->id(),
          'uid' => $this->currentUser()->id(),
          'action' => 'download'
        ])
        ->execute();

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

      $connection = \Drupal::service('database');
      $connection->insert('bc_webdav_log')
        ->fields([
          'fid' => $file->id(),
          'uid' => $this->currentUser()->id(),
          'action' => 'view'
        ])
        ->execute();

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
            $html = '<script> alert(" ready "); </script>';

            $connection = \Drupal::service('database');
            $connection->insert('bc_webdav_log')
              ->fields([
                'fid' => $file->id(),
                'uid' => $this->currentUser()->id(),
                'action' => 'preparing edit'
              ])
              ->execute();

          }
        } elseif (file_exists($config->folder . '/' . $file_name)) {
          $html = '<script> alert(" file is edit by another user, try later "); </script>';
        }
      }
    }

    return new HtmlResponse($html);

  }

  private function historyFile($fileId=null) {
    $return = (object) array(
      "success" => false,
      "data" => array()
    );

    $html = '';

    $config = (object) \Drupal::config('bc_webdav.settings')->get();
    if ($config->enabled && !empty($config->folder)) {

      $files = \Drupal::entityTypeManager()
        ->getStorage('file')
        ->loadByProperties(['uuid' => $fileId]);

      if (!empty($files)) {

        $return->success = true;
        $file = reset($files);

        $result = \Drupal::database()->query('SELECT DISTINCT * FROM bc_webdav_log')->fetchAll();
        foreach( $result AS $row ) {

          $account = \Drupal\user\Entity\User::load($row->uid);
          $stamp = date('d-m-Y H:i:s', strtotime($row->stamp));

          $return->data[] = array(
            "user" => $account->getDisplayName(),
            "action" => $row->action,
            "time" => $stamp
          );

          if (count($return->data) > 0) {
              $html = '<script>';
              $html .= 'window.top.showBcWebdavLogData(' . json_encode($return) . ')';
              $html .= '</script>';
          }
        }
      }
    }

    return new HtmlResponse($html);
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
    else if ($action === 'history') return $this->historyFile($fileId);

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


