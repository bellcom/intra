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

    $form['form']['enabled'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('this is enabled'),
      '#default_value' => $config->get('enabled')
    );

    $form['form']['url'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Webdav location'),
      '#default_value' => $config->get('url'),
      '#required' => TRUE,
      '#description' => $this->t('The full path to the webdav for outside editing, e.g., http://example.com/webdav/'),
    ];

    $form['form']['folder'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Webdav local location'),
      '#default_value' => $config->get('folder'),
      '#required' => TRUE,
      '#description' => $this->t('The full path to the webdav folder localy, /var/www/webdav'),
    ];


    $form['form']['ext'] = [
      '#type' => 'textfield',
      '#title' => $this->t('file extensions'),
      '#default_value' => $config->get('ext'),
      '#size' => 60,
      '#maxlength' => 128,
      '#required' => TRUE,
      '#description' => $this->t('file extensions that is allowed to edit seperated by comma'),
    ];

    // TODO if og exists
    $form['form']['right_og'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Rights by organic groups membership'),
      '#default_value' => $config->get('right_og')
    );

    return parent::buildForm($form, $form_state);

  }


  public function submitForm(array &$form, FormStateInterface $form_state) {

    $values = $form_state->getValues();
    $config = $this->config(BcWebdavForm::$configName);

    foreach ($values as $key => $value) {
      $config->set($key, $value);
    }

    $config->save();

    parent::submitForm($form, $form_state);

  }

}

