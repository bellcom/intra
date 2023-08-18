<?php

namespace Drupal\bc_speed_admin\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure bc speed admin settings for this site.
 */
class SettingsForm extends ConfigFormBase {


  /**
   * Name of the config.
   *
   * @var string
   */
  public static $configName = 'bc_speed_admin.settings';

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'bc_speed_admin_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [SettingsForm::$configName];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['speedAdminConfig']['apikey'] = array(
      '#type' => 'textfield',
      '#title' => t('Api key'),
      '#description' => t('Speed admin api key'),
      '#default_value' => $this->config(SettingsForm::$configName)->get('apikey')
    );

    $form['speedAdminConfig']['run'] = array(
      '#type' => 'select',
      '#title' => $this->t('Run type'),
      '#default_value' => $this->config(SettingsForm::$configName)->get('run'),
      '#options' => array(
        'drush' => $this->t('manual drush run'),
        'cronday' => $this->t('cron daily'),
        'cronweek' => $this->t('cron week'),
        'cronmonth' => $this->t('cron month')
      ),
      '#description' => $this->t('How to run speedadmin.'),
    );

    $form['speedAdminConfig']['runnow'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('run import now'),
    );

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();

    if ($values['runnow'] == 1) {
      \Drupal\bc_speed_admin\Controller\Sync::handler();
    }

    $config = $this->config(SettingsForm::$configName);
    foreach ($values as $key => $value) {
      $config->set($key, $value);
    }

    $config->save();

    parent::submitForm($form, $form_state);
  }

}
