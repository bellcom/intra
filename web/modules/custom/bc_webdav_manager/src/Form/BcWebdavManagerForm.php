<?php

namespace Drupal\bc_webdav_manager\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class BcWebdavManagerForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['bc_webdav_manager.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'bc_webdav_manager_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('bc_webdav_manager.settings');

    $form['outsite_location_textfield'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Webdav location outside'),
      '#default_value' => $config->get('outsite_location_textfield'),
      '#size' => 60,
      '#maxlength' => 128,
      '#required' => TRUE,
      '#description' => $this->t('The full path to the webdav for outside editing, e.g., http://example.com/webdav/'),
    ];

    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('bc_webdav_manager.settings')
      ->set('outsite_location_textfield', $form_state->getValue('outsite_location_textfield'))
      ->save();
  $this->messenger()->addMessage($this->t("Saved!"));
  }

}
