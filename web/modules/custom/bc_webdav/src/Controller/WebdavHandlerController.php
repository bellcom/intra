<?php

namespace Drupal\bc_webdav\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\File\FileExists;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

class WebdavHandlerController extends ControllerBase {

  private function downloadFile($fileId = '') {
    $files = \Drupal::entityTypeManager()
      ->getStorage('file')
      ->loadByProperties(['uuid' => $fileId]);

    if (!empty($files)) {
      $file = reset($files);

      $headers = [
        'Content-Type' => $file->filemime->value,
        'Content-Description' => 'File Download',
        'Content-Disposition' => 'attachment; filename=' . $file->label(),
        'Cache-Control' => 'private',
      ];

      \Drupal::service('database')
        ->insert('bc_webdav_log')
        ->fields([
          'fid' => $file->id(),
          'uid' => $this->currentUser()->id(),
          'action' => 'download',
        ])
        ->execute();

      return new BinaryFileResponse(
        $file->uri->value,
        200,
        $headers,
        TRUE
      );
    }

    return new Response('', 404);
  }

  private function viewFile($fileId = '') {
    $files = \Drupal::entityTypeManager()
      ->getStorage('file')
      ->loadByProperties(['uuid' => $fileId]);

    $html = '';

    if (!empty($files)) {
      $file = reset($files);

      $url = $file->createFileUrl(FALSE);

      $html = '<a id="webdavfilelink" href="'
        . htmlspecialchars($url, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')
        . '" target="_blank">#</a>';

      $html .= '<script>'
        . 'document.getElementById("webdavfilelink").click();'
        . '</script>';

      \Drupal::service('database')
        ->insert('bc_webdav_log')
        ->fields([
          'fid' => $file->id(),
          'uid' => $this->currentUser()->id(),
          'action' => 'view',
        ])
        ->execute();
    }

    return new Response($html);
  }

  /**
   * Detect supported desktop operating system from the browser user-agent.
   */
  private function detectClientOs(): ?string {
    $ua = strtolower(
      (string) \Drupal::request()->headers->get('User-Agent', '')
    );

    if (str_contains($ua, 'windows nt')) {
      return 'windows';
    }

    if (
      (str_contains($ua, 'macintosh') || str_contains($ua, 'mac os x'))
      && !str_contains($ua, 'iphone')
      && !str_contains($ua, 'ipad')
    ) {
      return 'mac';
    }

    if (
      (str_contains($ua, 'linux') || str_contains($ua, 'x11'))
      && !str_contains($ua, 'android')
    ) {
      return 'linux';
    }

    return NULL;
  }

  /**
   * Add Basic Auth credentials only when fallback auth is selected.
   */
  /**
   * Build the public WebDAV URL used by desktop applications.
   *
   * Credentials are deliberately never embedded in the client URL.
   *
   * With Basic Auth, the desktop application handles authentication itself
   * and may cache the credentials.
   *
   * With SSO/Kerberos, authentication is handled transparently by the
   * operating system / Office client.
   *
   * Stored shared credentials are only used server-side by Drupal for
   * internal WebDAV lock discovery.
   */
  private function buildWebdavUrl($config, string $fileName): string {
    return rtrim((string) $config->get('url'), '/')
      . '/'
      . rawurlencode($fileName);
  }

  /**
   * Build a Microsoft Office open-for-edit URI.
   */
  private function buildMicrosoftOfficeUri(
    string $webdavUrl,
    string $fileName
  ): ?string {
    $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    $scheme = match ($extension) {
      'doc', 'docx', 'docm', 'dot', 'dotx', 'dotm', 'rtf', 'txt', 'odt'
        => 'ms-word',

      'xls', 'xlsx', 'xlsm', 'xlsb', 'xlt', 'xltx', 'xltm', 'ods', 'csv'
        => 'ms-excel',

      'ppt', 'pptx', 'pptm', 'pps', 'ppsx', 'ppsm', 'odp'
        => 'ms-powerpoint',

      default => NULL,
    };

    if ($scheme === NULL) {
      return NULL;
    }

    return $scheme . ':ofe|u|' . $webdavUrl;
  }

  /**
   * Build a LibreOffice WebDAV URI.
   */
  private function buildLibreOfficeUri(string $webdavUrl): ?string {
    if (str_starts_with(strtolower($webdavUrl), 'https://')) {
      return 'vnd.sun.star.webdavs://' . substr($webdavUrl, 8);
    }

    if (str_starts_with(strtolower($webdavUrl), 'http://')) {
      return 'vnd.sun.star.webdav://' . substr($webdavUrl, 7);
    }

    return NULL;
  }

  /**
   * Build custom client URI from administrator template.
   */
  private function buildCustomUri(
    string $template,
    string $webdavUrl,
    string $fileName
  ): ?string {
    $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    $uri = strtr($template, [
      '{url}' => $webdavUrl,
      '{filename}' => rawurlencode($fileName),
      '{extension}' => $extension,
    ]);

    if (!preg_match('/^[a-z][a-z0-9+.-]*:/i', $uri)) {
      return NULL;
    }

    $scheme = strtolower((string) strstr($uri, ':', TRUE));

    if (in_array($scheme, ['javascript', 'data', 'vbscript'], TRUE)) {
      return NULL;
    }

    return $uri;
  }

  /**
   * Resolve OS, enabled state and configured application.
   */
  private function buildClientOpenUri(
    $config,
    string $webdavUrl,
    string $fileName
  ): ?string {
    $os = $this->detectClientOs();

    if ($os === NULL) {
      return NULL;
    }

    $enabled = $config->get('os_' . $os . '_enabled');

    // Existing installations default to all supported desktop OSes enabled.
    if ($enabled === NULL) {
      $enabled = TRUE;
    }

    if (!$enabled) {
      return NULL;
    }

    $program = (string) (
      $config->get('os_' . $os . '_program') ?: 'auto'
    );

    if ($program === 'auto') {
      $program = match ($os) {
        'linux' => 'libreoffice',
        'windows', 'mac' => 'microsoft_office',
        default => '',
      };
    }

    if ($program === 'microsoft_office') {
      return $this->buildMicrosoftOfficeUri($webdavUrl, $fileName);
    }

    if ($program === 'libreoffice') {
      return $this->buildLibreOfficeUri($webdavUrl);
    }

    if ($program === 'custom') {
      $template = trim(
        (string) $config->get('os_' . $os . '_custom_handler')
      );

      if ($template === '') {
        return NULL;
      }

      return $this->buildCustomUri(
        $template,
        $webdavUrl,
        $fileName
      );
    }

    return NULL;
  }

  private function editFile($fileId = NULL) {
    $html = '<script>window.top.alert("Something went wrong.");</script>';
    $config = \Drupal::config('bc_webdav.settings');

    if (!$config->get('enabled') || !$config->get('folder')) {
      return new Response($html);
    }

    $files = \Drupal::entityTypeManager()
      ->getStorage('file')
      ->loadByProperties(['uuid' => $fileId]);

    if (empty($files)) {
      return new Response($html, 404);
    }

    $file = reset($files);

    $fileName = $file->getFilename();
    $fileUri = $file->getFileUri();

    $folder = rtrim((string) $config->get('folder'), '/');
    $workingFile = $folder . '/' . $fileName;
    $sidecarFile = $folder . '/id.' . $fileName;

    /*
     * A working copy may remain after Office/LibreOffice has closed.
     * The authoritative lock state is WebDAV LOCK/UNLOCK, not whether
     * the working file exists.
     */
    if (is_file($workingFile)) {
      if (!is_file($sidecarFile)) {
        $mtime = filemtime($workingFile);
        $age = $mtime === FALSE ? 0 : max(0, time() - $mtime);

        if ($age >= BC_WEBDAV_ORPHAN_TIMEOUT) {
          @unlink($workingFile);
        }
        else {
          $html = '<script>window.top.alert('
            . json_encode(
              'The WebDAV working file exists without session metadata. Try again later.'
            )
            . ');</script>';

          return new Response($html);
        }
      }
      else {
        $started = filemtime($sidecarFile);
        $sessionAge = $started === FALSE
          ? 0
          : max(0, time() - $started);

        $lockState = \bc_webdav_get_lock_state(
          $config,
          $fileName
        );

        if ($lockState === 'locked') {
          $html = '<script>window.top.alert('
            . json_encode(
              'The file is currently being edited by another user. '
              . 'Try again when that user has closed the document.'
            )
            . ');</script>';

          return new Response($html);
        }

        if ($lockState === 'unknown') {
          $html = '<script>window.top.alert('
            . json_encode(
              'The WebDAV lock state could not be verified. '
              . 'The file has not been opened to avoid overwriting changes.'
            )
            . ');</script>';

          return new Response($html);
        }

        // Drupal creates the working copy just before the desktop application
        // obtains its initial WebDAV LOCK.
        if ($sessionAge < BC_WEBDAV_LOCK_START_GRACE) {
          $html = '<script>window.top.alert('
            . json_encode(
              'The editing session is still starting. Try again in a few seconds.'
            )
            . ');</script>';

          return new Response($html);
        }

        /*
         * WebDAV says unlocked: the previous editor is finished.
         * Import the working copy before starting a new editing session.
         */
        if (!\bc_webdav_import_working_file($workingFile)) {
          $html = '<script>window.top.alert('
            . json_encode(
              'The previous WebDAV version could not be imported into Drupal.'
            )
            . ');</script>';

          return new Response($html);
        }

        $file = \Drupal::entityTypeManager()
          ->getStorage('file')
          ->load($file->id());

        if (!$file) {
          return new Response($html);
        }

        $fileUri = $file->getFileUri();
      }
    }

    if (
      !is_dir($folder)
      || !is_writable($folder)
      || is_file($workingFile)
    ) {
      return new Response($html);
    }

    $fileSystem = \Drupal::service('file_system');

    $fileSystem->copy(
      $fileUri,
      $workingFile,
      FileExists::Replace
    );

    if (!is_file($workingFile)) {
      return new Response($html);
    }

    file_put_contents(
      $sidecarFile,
      $file->id() . ':' . $this->currentUser()->id() . "\n"
    );

    \Drupal::database()
      ->insert('bc_webdav_log')
      ->fields([
        'fid' => $file->id(),
        'uid' => $this->currentUser()->id(),
        'action' => 'preparing edit',
      ])
      ->execute();

    $webdavUrl = $this->buildWebdavUrl(
      $config,
      $fileName
    );

    $clientUri = $this->buildClientOpenUri(
      $config,
      $webdavUrl,
      $fileName
    );

    if ($clientUri !== NULL) {
      $html = '<script>'
        . 'window.top.location.href = '
        . json_encode(
          $clientUri,
          JSON_HEX_TAG
          | JSON_HEX_AMP
          | JSON_HEX_APOS
          | JSON_HEX_QUOT
        )
        . ';'
        . '</script>';
    }
    else {
      $html = '<script>window.top.alert('
        . json_encode(
          'No suitable application handler is configured for this client.'
        )
        . ');</script>';
    }

    return new Response($html);
  }

  private function historyFile($fileId = NULL) {
    $return = (object) [
      'success' => FALSE,
      'data' => [],
      'filename' => '',
    ];

    $html = '';

    $config = \Drupal::config('bc_webdav.settings');

    if ($config->get('enabled') && $config->get('folder')) {
      $files = \Drupal::entityTypeManager()
        ->getStorage('file')
        ->loadByProperties(['uuid' => $fileId]);

      if (!empty($files)) {
        $return->success = TRUE;

        $file = reset($files);
        $return->filename = $file->label();

        $result = \Drupal::database()
          ->select('bc_webdav_log', 'l')
          ->fields('l')
          ->condition('fid', $file->id())
          ->orderBy('stamp', 'DESC')
          ->orderBy('id', 'DESC')
          ->range(0, 10)
          ->execute()
          ->fetchAll();

        foreach ($result as $row) {
          $account = \Drupal\user\Entity\User::load($row->uid);

          if (!$account) {
            continue;
          }

          $stamp = date(
            'd-m-Y H:i:s',
            strtotime($row->stamp)
          );

          $return->data[] = [
            'user' => $account->getDisplayName(),
            'action' => $row->action,
            'time' => $stamp,
          ];
        }

        $html = '<script>'
          . 'window.top.showBcWebdavLogData('
          . json_encode($return)
          . ');'
          . '</script>';
      }
    }

    return new Response($html);
  }

  public function HandlerPage() {
    $action = \Drupal::request()->query->get('action');
    $fileId = \Drupal::request()->query->get('id');

    if ($action === 'download') {
      return $this->downloadFile($fileId);
    }

    if ($action === 'view') {
      return $this->viewFile($fileId);
    }

    if ($action === 'edit') {
      return $this->editFile($fileId);
    }

    if ($action === 'history') {
      return $this->historyFile($fileId);
    }

    return new Response('');
  }

}
