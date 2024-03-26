<?php

namespace Drupal\bc_ldap_userimport\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\bc_basic\Debug;

class LdapUserimportForm extends ConfigFormBase {

  public static $configName = 'bc_ldap_userimport.settings';

  public function getFormId() {
    return 'bc_ldap_userimport_settings';
  }

  protected function getEditableConfigNames() {
    return [LdapUserimportForm::$configName];
  }

  public function buildForm(array $form, FormStateInterface $form_state) {

    $config = $this->config(LdapUserimportForm::$configName);

    $form['form']['enabled'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('enabled'),
      '#default_value' => $config->get('enabled')
    ];

    $form['form']['cron'] = [
      '#type' => 'checkbox',
      '#title' => 'cron',
      '#default_value' => $config->get('cron')
    ];

    $form['form']['run'] = array(
      '#type' => 'select',
      '#options' => array(
        '' => '',
        'day' => $this->t('daily'),
        'week' => $this->t('weekly'),
        'month' => $this->t('monthly')
      ),
      '#default_value' => $config->get('run')
    );

    $form['form']['lastrun'] = array(
      '#type' => 'value',
      '#default_value' => $config->get('lastrun')
    );

    $form['form']['rdn'] = [
      '#type' => 'textfield',
      '#title' => 'ldap dn',
      '#default_value' => $config->get('rdn')
    ];

    $form['form']['pass'] = [
      '#type' => 'password',
      '#title' => 'ldap password'
    ];

    $form['form']['pass'] = [
      '#type' => 'password',
      '#title' => 'ldap password'
    ];

    $form['form']['host'] = [
      '#type' => 'textfield',
      '#title' => 'ldap host',
      '#default_value' => $config->get('host')
    ];

    $form['form']['dn'] = [
      '#type' => 'textfield',
      '#title' => 'ldap dn',
      '#default_value' => $config->get('dn')
    ];

    $form['form']['filter'] = [
      '#type' => 'textfield',
      '#title' => 'ldap filter',
      '#default_value' => $config->get('filter')
    ];

    return parent::buildForm($form, $form_state);
  }


  public function submitForm(array &$form, FormStateInterface $form_state) {

    $values = $form_state->getValues();
    $config = $this->config(LdapUserimportForm::$configName);

    foreach ($values as $key => $value) {
      if ($key !== 'pass' || ($key === 'pass' && !empty($value))) {
        $config->set($key, $value);
      }
    }

    $config->save();

    parent::submitForm($form, $form_state);

  }


}
