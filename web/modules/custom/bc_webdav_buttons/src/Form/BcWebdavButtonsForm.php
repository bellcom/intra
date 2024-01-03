<?php

namespace Drupal\bc_webdav_buttons\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class BcWebdavButtonsForm extends ConfigFormBase {

  public static $configName = 'bc_webdav_buttons.settings';

  public function getFormId() {
    return 'bc_webdav_buttons_settings';
  }

  protected function getEditableConfigNames() {
    return [BcWebdavButtonsForm::$configName];
  }

  public function buildForm(array $form, FormStateInterface $form_state) {

    $config = $this->config(BcWebdavButtonsForm::$configName);

    $form['form']['active'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('This is active'),
      '#default_value' => $config->get('active')
    ];

    return parent::buildForm($form, $form_state);
  }


  public function submitForm(array &$form, FormStateInterface $form_state) {

    $values = $form_state->getValues();
    $config = $this->config(BcWebdavButtonsForm::$configName);
    foreach ($values as $key => $value) {
      $config->set($key, $value);
    }
    $config->save();

    parent::submitForm($form, $form_state);

  }


}
