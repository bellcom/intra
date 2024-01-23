<?php

namespace Drupal\bc_og_extension\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\og\Og;

class OgForm extends ConfigFormBase {

  public static $configName = 'bc_og_extension.settings';

  public function getFormId() {
    return 'bc_og_extension_settings';
  }

  protected function getEditableConfigNames() {
    return [OgForm::$configName];
  }

  public function buildForm(array $form, FormStateInterface $form_state) {

    $config = $this->config(OgForm::$configName);

    $form['form']['default_active'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Set group membership active by default ( default is pending )'),
      '#default_value' => $config->get('default_active')
    ];
    
    return parent::buildForm($form, $form_state);
  }


  public function submitForm(array &$form, FormStateInterface $form_state) {

    $values = $form_state->getValues();
    $config = $this->config(OgForm::$configName);
    foreach ($values as $key => $value) {
      $config->set($key, $value);
    }
    $config->save();

    parent::submitForm($form, $form_state);

  }


}
