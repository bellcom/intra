<?php

namespace Drupal\bc_anonymous_subscriptions_extension\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class ASEForm extends ConfigFormBase {

  public static $configName = 'bc_anonymous_subscriptions_extension.settings';

  public function getFormId() {
    return 'bc_anonymous_subscriptions_extension_settings';
  }

  protected function getEditableConfigNames() {
    return [ASEForm::$configName];
  }

  public function buildForm(array $form, FormStateInterface $form_state) {

    $config = $this->config(ASEForm::$configName);

    $form['form']['enabled'] = [
      '#type' => 'checkbox',
      '#title' => 'this is enabled',
      '#default_value' => $config->get('enabled')
    ];

    $form['form']['all_os2web_news'] = [
      '#type' => 'checkbox',
      '#title' => 'Set automatic all users as member of anonymous subscriptions os2web_news',
      '#default_value' => $config->get('all_news')
    ];

    return parent::buildForm($form, $form_state);
  }


  public function submitForm(array &$form, FormStateInterface $form_state) {

    $values = $form_state->getValues();
    $config = $this->config(ASEForm::$configName);

    foreach ($values as $key => $value) {
      $config->set($key, $value);
    }

    $config->save();

    parent::submitForm($form, $form_state);

  }


}
