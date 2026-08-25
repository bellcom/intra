<?php

namespace Drupal\bc_webdav\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class BcWebdavForm extends ConfigFormBase {

  public static $configName = 'bc_webdav.settings';

  public function getFormId() {
    return 'bc_webdav_settings';
  }

  protected function getEditableConfigNames() {
    return [BcWebdavForm::$configName];
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config(BcWebdavForm::$configName);
    $state = \Drupal::state();

    $form['form']['enabled'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('WebDAV enabled'),
      '#default_value' => $config->get('enabled'),
    ];

    $form['form']['url'] = [
      '#type' => 'textfield',
      '#title' => $this->t('WebDAV location'),
      '#default_value' => $config->get('url'),
      '#required' => TRUE,
      '#description' => $this->t('Public WebDAV URL used by client applications, e.g. https://example.com/webdav/'),
    ];

    $form['form']['folder'] = [
      '#type' => 'textfield',
      '#title' => $this->t('WebDAV local location'),
      '#default_value' => $config->get('folder'),
      '#required' => TRUE,
      '#description' => $this->t('Local path corresponding to the WebDAV location, e.g. /var/www/webdav'),
    ];

    $form['form']['ext'] = [
      '#type' => 'textfield',
      '#title' => $this->t('File extensions'),
      '#default_value' => $config->get('ext'),
      '#size' => 60,
      '#maxlength' => 255,
      '#required' => TRUE,
      '#description' => $this->t('Extensions allowed for WebDAV editing, separated by comma. Remember that the Drupal file field has its own allowed-extension setting.'),
    ];

    $form['auth'] = [
      '#type' => 'details',
      '#title' => $this->t('WebDAV authentication'),
      '#open' => TRUE,
    ];

    $form['auth']['auth_method'] = [
      '#type' => 'select',
      '#title' => $this->t('Authentication method'),
      '#options' => [
        'sso' => $this->t('SSO / Kerberos (recommended)'),
        'basic' => $this->t('Shared WebDAV user (fallback/test)'),
        'none' => $this->t('No authentication'),
      ],
      '#default_value' => $config->get('auth_method') ?: 'sso',
      '#description' => $this->t('For production, use SSO/Kerberos. Shared-user credentials are never embedded in client URLs. With Basic Auth the desktop application authenticates separately. With SSO/Kerberos the operating system and application authenticate transparently.'),
    ];

    $form['auth']['webdav_username'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Shared WebDAV username'),
      '#default_value' => $state->get('bc_webdav.webdav_username', ''),
      '#autocomplete' => 'off',
      '#description' => $this->t('Used by Drupal for server-side WebDAV lock discovery. The username is not embedded in URLs sent to desktop applications.'),
      '#states' => [
        'visible' => [
          ':input[name="auth_method"]' => ['value' => 'basic'],
        ],
      ],
    ];

    $has_password = (string) $state->get('bc_webdav.webdav_password', '') !== '';

    $form['auth']['webdav_password'] = [
      '#type' => 'password',
      '#title' => $this->t('Shared WebDAV password'),
      '#attributes' => [
        'autocomplete' => 'new-password',
      ],
      '#description' => $has_password
        ? $this->t('A password is already stored. Leave this field empty to keep it. The password is used only server-side and is never embedded in client URLs.')
        : $this->t('Used by Drupal for server-side WebDAV lock discovery. The password is never embedded in URLs sent to desktop applications.'),
      '#states' => [
        'visible' => [
          ':input[name="auth_method"]' => ['value' => 'basic'],
        ],
      ],
    ];

    if ($has_password) {
      $form['auth']['clear_webdav_password'] = [
        '#type' => 'checkbox',
        '#title' => $this->t('Remove stored WebDAV password'),
        '#default_value' => FALSE,
        '#states' => [
          'visible' => [
            ':input[name="auth_method"]' => ['value' => 'basic'],
          ],
        ],
      ];
    }

    $form['clients'] = [
      '#type' => 'details',
      '#title' => $this->t('Client operating systems and applications'),
      '#open' => TRUE,
      '#description' => $this->t('The browser detects the client operating system and launches the configured application using its registered URI handler.'),
    ];

    // Windows.
    $form['clients']['windows'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Windows'),
    ];

    $form['clients']['windows']['os_windows_enabled'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Support Windows clients'),
      '#default_value' => $config->get('os_windows_enabled') ?? TRUE,
      '#description' => $this->t('Default application: Microsoft Office. Microsoft Office must be installed and its ms-word/ms-excel/ms-powerpoint URI handlers registered. For SSO, Windows/Office must also be able to authenticate to the WebDAV endpoint using Kerberos/Negotiate.'),
    ];

    $form['clients']['windows']['os_windows_program'] = [
      '#type' => 'select',
      '#title' => $this->t('Application'),
      '#options' => [
        'auto' => $this->t('Automatic / default (Microsoft Office)'),
        'microsoft_office' => $this->t('Microsoft Office'),
        'libreoffice' => $this->t('LibreOffice'),
        'custom' => $this->t('Custom URI handler'),
      ],
      '#default_value' => $config->get('os_windows_program') ?: 'auto',
    ];

    $form['clients']['windows']['os_windows_custom_handler'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Custom URI template'),
      '#default_value' => $config->get('os_windows_custom_handler') ?: '',
      '#description' => $this->t('Must be a registered client-side URI protocol and contain {url}. Example: myoffice:open?url={url}'),
      '#states' => [
        'visible' => [
          ':input[name="os_windows_program"]' => ['value' => 'custom'],
        ],
      ],
    ];

    // Linux.
    $form['clients']['linux'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Linux'),
    ];

    $form['clients']['linux']['os_linux_enabled'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Support Linux clients'),
      '#default_value' => $config->get('os_linux_enabled') ?? TRUE,
      '#description' => $this->t('Default application: LibreOffice. LibreOffice must be installed and the vnd.sun.star.webdav/webdavs URI handler must be registered with the desktop environment.'),
    ];

    $form['clients']['linux']['os_linux_program'] = [
      '#type' => 'select',
      '#title' => $this->t('Application'),
      '#options' => [
        'auto' => $this->t('Automatic / default (LibreOffice)'),
        'libreoffice' => $this->t('LibreOffice'),
        'custom' => $this->t('Custom URI handler'),
      ],
      '#default_value' => $config->get('os_linux_program') ?: 'auto',
    ];

    $form['clients']['linux']['os_linux_custom_handler'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Custom URI template'),
      '#default_value' => $config->get('os_linux_custom_handler') ?: '',
      '#description' => $this->t('Must be a registered client-side URI protocol and contain {url}. Example: myoffice:open?url={url}'),
      '#states' => [
        'visible' => [
          ':input[name="os_linux_program"]' => ['value' => 'custom'],
        ],
      ],
    ];

    // macOS.
    $form['clients']['mac'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('macOS'),
    ];

    $form['clients']['mac']['os_mac_enabled'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Support macOS clients'),
      '#default_value' => $config->get('os_mac_enabled') ?? TRUE,
      '#description' => $this->t('Default application: Microsoft Office for Mac. Office must be installed and its URI handlers registered. LibreOffice can also be selected.'),
    ];

    $form['clients']['mac']['os_mac_program'] = [
      '#type' => 'select',
      '#title' => $this->t('Application'),
      '#options' => [
        'auto' => $this->t('Automatic / default (Microsoft Office)'),
        'microsoft_office' => $this->t('Microsoft Office'),
        'libreoffice' => $this->t('LibreOffice'),
        'custom' => $this->t('Custom URI handler'),
      ],
      '#default_value' => $config->get('os_mac_program') ?: 'auto',
    ];

    $form['clients']['mac']['os_mac_custom_handler'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Custom URI template'),
      '#default_value' => $config->get('os_mac_custom_handler') ?: '',
      '#description' => $this->t('Must be a registered client-side URI protocol and contain {url}. Example: myoffice:open?url={url}'),
      '#states' => [
        'visible' => [
          ':input[name="os_mac_program"]' => ['value' => 'custom'],
        ],
      ],
    ];

    $form['form']['right_og'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Rights by organic groups membership'),
      '#default_value' => $config->get('right_og'),
    ];

    return parent::buildForm($form, $form_state);
  }

  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);

    $url = trim((string) $form_state->getValue('url'));
    $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));

    if (!in_array($scheme, ['http', 'https'], TRUE)) {
      $form_state->setErrorByName(
        'url',
        $this->t('The WebDAV URL must start with http:// or https://.')
      );
    }

    if ($form_state->getValue('auth_method') === 'basic') {
      $username = trim((string) $form_state->getValue('webdav_username'));
      $new_password = (string) $form_state->getValue('webdav_password');
      $existing_password = (string) \Drupal::state()->get('bc_webdav.webdav_password', '');
      $clear_password = (bool) $form_state->getValue('clear_webdav_password');

      if ($username === '') {
        $form_state->setErrorByName(
          'webdav_username',
          $this->t('A WebDAV username is required when shared-user authentication is selected.')
        );
      }

      if ($new_password === '' && ($existing_password === '' || $clear_password)) {
        $form_state->setErrorByName(
          'webdav_password',
          $this->t('A WebDAV password is required when shared-user authentication is selected.')
        );
      }
    }

    foreach (['windows', 'linux', 'mac'] as $os) {
      if (!$form_state->getValue('os_' . $os . '_enabled')) {
        continue;
      }

      if ($form_state->getValue('os_' . $os . '_program') !== 'custom') {
        continue;
      }

      $field = 'os_' . $os . '_custom_handler';
      $template = trim((string) $form_state->getValue($field));

      if ($template === '' || !str_contains($template, '{url}')) {
        $form_state->setErrorByName(
          $field,
          $this->t('A custom URI template must contain the {url} placeholder.')
        );
        continue;
      }

      $probe = strtr($template, [
        '{url}' => 'https://example.invalid/webdav/test.docx',
        '{filename}' => 'test.docx',
        '{extension}' => 'docx',
      ]);

      if (!preg_match('/^[a-z][a-z0-9+.-]*:/i', $probe)) {
        $form_state->setErrorByName(
          $field,
          $this->t('The custom handler must start with a valid URI scheme.')
        );
        continue;
      }

      $custom_scheme = strtolower((string) strstr($probe, ':', TRUE));

      if (in_array($custom_scheme, ['javascript', 'data', 'vbscript'], TRUE)) {
        $form_state->setErrorByName(
          $field,
          $this->t('This URI scheme is not allowed.')
        );
      }
    }
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config(BcWebdavForm::$configName);

    $keys = [
      'enabled',
      'url',
      'folder',
      'ext',
      'right_og',
      'auth_method',
      'os_windows_enabled',
      'os_windows_program',
      'os_windows_custom_handler',
      'os_linux_enabled',
      'os_linux_program',
      'os_linux_custom_handler',
      'os_mac_enabled',
      'os_mac_program',
      'os_mac_custom_handler',
    ];

    foreach ($keys as $key) {
      $config->set($key, $form_state->getValue($key));
    }

    // Remove legacy form-state values that older versions accidentally saved.
    foreach ([
      'submit',
      'form_build_id',
      'form_token',
      'form_id',
      'op',
      'webdav_username',
      'webdav_password',
      'clear_webdav_password',
    ] as $key) {
      $config->clear($key);
    }

    $config->save();

    // Shared credentials are environment-specific and are deliberately not
    // stored in exported Drupal configuration.
    $state = \Drupal::state();

    $state->set(
      'bc_webdav.webdav_username',
      trim((string) $form_state->getValue('webdav_username'))
    );

    if ($form_state->getValue('clear_webdav_password')) {
      $state->delete('bc_webdav.webdav_password');
    }
    else {
      $password = (string) $form_state->getValue('webdav_password');

      if ($password !== '') {
        $state->set('bc_webdav.webdav_password', $password);
      }
    }

    parent::submitForm($form, $form_state);
  }

}
