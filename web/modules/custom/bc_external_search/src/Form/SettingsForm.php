<?php

namespace Drupal\bc_external_search\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

final class SettingsForm extends ConfigFormBase {

  public function getFormId(): string {
    return 'bc_external_search_settings';
  }

  protected function getEditableConfigNames(): array {
    return ['bc_external_search.settings'];
  }

  public function buildForm(array $form, FormStateInterface $form_state): array {
    $config = $this->config('bc_external_search.settings');

    $form['search_api_index'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Search API index'),
      '#default_value' => $config->get('search_api_index') ?: 'ai_intranet_poc',
      '#description' => $this->t('The existing Search API index that should index the imported node bundle.'),
      '#required' => TRUE,
    ];

    $form['hetzner'] = [
      '#type' => 'details',
      '#title' => $this->t('Hetzner Cloud'),
      '#open' => TRUE,
      '#description' => $this->t('Enter Key module machine names. API tokens are not stored in this configuration.'),
    ];
    $projects = [
      'intern_backup_key' => '0. Internt + backup',
      'produktion_key' => '1. Produktion',
      'test_key' => '2. Test',
      'udvikling_key' => '3. Udvikling',
      'backups_key' => 'Backups',
      'konsoleh_key' => 'konsoleH',
    ];
    foreach ($projects as $key => $label) {
      $form['hetzner'][$key] = [
        '#type' => 'textfield',
        '#title' => $this->t('@project API token key ID', ['@project' => $label]),
        '#default_value' => $config->get('hetzner.' . $key) ?: '',
      ];
    }

    $form['drupal7'] = [
      '#type' => 'details',
      '#title' => $this->t('Drupal 7 customer source'),
      '#open' => TRUE,
    ];
    $form['drupal7']['base_url'] = [
      '#type' => 'url',
      '#title' => $this->t('Base URL'),
      '#default_value' => $config->get('drupal7.base_url') ?: '',
      '#placeholder' => 'https://kunde.bellcom.dk',
    ];
    $form['drupal7']['username'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Username'),
      '#default_value' => $config->get('drupal7.username') ?: '',
    ];
    $form['drupal7']['password_key'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Password key ID'),
      '#default_value' => $config->get('drupal7.password_key') ?: '',
      '#description' => $this->t('Machine name of a Key module key containing the Drupal 7 password.'),
    ];
    $form['drupal7']['source'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Source/facet name'),
      '#default_value' => $config->get('drupal7.source') ?: 'kunde',
    ];

    $form['url_sources'] = [
      '#type' => 'textarea',
      '#title' => $this->t('URL/crawl sources'),
      '#default_value' => $config->get('url_sources') ?: '',
      '#rows' => 8,
      '#description' => $this->t('One source per line: source|https://example.com/|depth|limit. Example: docs|https://docs.example.com/|2|100'),
    ];

    return parent::buildForm($form, $form_state);
  }

  public function validateForm(array &$form, FormStateInterface $form_state): void {
    foreach (preg_split('/\R/', (string) $form_state->getValue('url_sources')) ?: [] as $lineNumber => $line) {
      $line = trim($line);
      if ($line === '' || str_starts_with($line, '#')) {
        continue;
      }
      $parts = array_map('trim', explode('|', $line));
      if (count($parts) < 2 || !filter_var($parts[1], FILTER_VALIDATE_URL)) {
        $form_state->setErrorByName('url_sources', $this->t('Invalid URL source on line @line.', ['@line' => $lineNumber + 1]));
        break;
      }
    }
    parent::validateForm($form, $form_state);
  }

  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $config = $this->config('bc_external_search.settings');
    $config->set('search_api_index', trim((string) $form_state->getValue('search_api_index')));

    foreach (['intern_backup_key', 'produktion_key', 'test_key', 'udvikling_key', 'backups_key', 'konsoleh_key'] as $key) {
      $config->set('hetzner.' . $key, trim((string) $form_state->getValue($key)));
    }

    $config
      ->set('drupal7.base_url', rtrim(trim((string) $form_state->getValue('base_url')), '/'))
      ->set('drupal7.username', trim((string) $form_state->getValue('username')))
      ->set('drupal7.password_key', trim((string) $form_state->getValue('password_key')))
      ->set('drupal7.source', trim((string) $form_state->getValue('source')) ?: 'kunde')
      ->set('url_sources', trim((string) $form_state->getValue('url_sources')))
      ->save();

    parent::submitForm($form, $form_state);
  }

}
