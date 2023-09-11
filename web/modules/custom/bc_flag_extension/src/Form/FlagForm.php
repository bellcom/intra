<?php
namespace Drupal\bc_flag_extension\Form;

use Drupal\bc_speed_admin\Form\SettingsForm;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class FlagForm extends ConfigFormBase {

  public static $configName = 'bc_flag_extension.settings';

  public function getFormId() {
    return 'bc_flag_extension_settings';
  }

  protected function getEditableConfigNames() {
    return [FlagForm::$configName];
  }

  public function buildForm(array $form, FormStateInterface $form_state) {

    $config = $this->config(FlagForm::$configName);

    $form['form']['enabled'] = array(
      '#type' => 'checkbox',
      '#title' => 'this is enabled',
      '#default_value' => $config->get('enabled')
    );

    $form['form']['bookmark'] = array(
      '#type' => 'checkbox',
      '#title' => 'bookmark is enabled',
      '#default_value' => $config->get('bookmark')
    );

    $form['form']['shortcut'] = array(
      '#type' => 'checkbox',
      '#title' => 'shortcut is enabled',
      '#default_value' => $config->get('shortcut')
    );

    $form['form']['unread'] = array(
      '#type' => 'checkbox',
      '#title' => 'unread is enabled',
      '#default_value' => $config->get('unread')
    );

    return parent::buildForm($form, $form_state);
  }


  public function submitForm(array &$form, FormStateInterface $form_state) {

    $values = $form_state->getValues();
    $config = $this->config(FlagForm::$configName);
    foreach ($values as $key => $value) {
      $config->set($key, $value);
    }
    $config->save();

    parent::submitForm($form, $form_state);

  }


}
