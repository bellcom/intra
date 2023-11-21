<?php

namespace Drupal\bc_mail_smsmail\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class SmsMailForm extends ConfigFormBase {

  public static $configName = 'bc_mail_smsmail.settings';


  public function getFormId() {
    return 'bc_mail_smsmail_settings';
  }


  protected function getEditableConfigNames() {
    return [SmsMailForm::$configName];
  }


  public function buildForm(array $form, FormStateInterface $form_state) {

    $config = $this->config(SmsMailForm::$configName);

    $form['form']['enabled'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('this is enabled'),
      '#default_value' => $config->get('enabled')
    );

    $form['form']['gateway'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('mail sms gateway'),
      '#default_value' => $config->get('gateway'),
      '#description' => 'the mailaddress that combine with number send the sms ex "@sms.ringsted.int"',
      '#required' => true,
      '#states' => array(
        'visible' => array(
          ':input[name="enabled"]' => array('checked' => true)
        )
      )
    );

    $form['form']['from'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('the from address'),
      '#default_value' => $config->get('from'),
      '#description' => $this->t('the mailaddress to use for FROM'),
      '#required' => true,
      '#states' => array(
        'visible' => array(
          ':input[name="enabled"]' => array('checked' => true)
        )
      )
    );

    $form['form']['reply'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('the reply to address'),
      '#default_value' => $config->get('reply'),
      '#description' => $this->t('the mailaddress to use for reply to'),
      '#required' => true,
      '#states' => array(
        'visible' => array(
          ':input[name="enabled"]' => array('checked' => true)
        )
      )
    );

    $configContents = $config->get('contents');

    $form['form']['contents'] = array(
      '#type' => 'table',
      '#capiton' => 'content types',
      '#header' => array(
        $this->t('enabled for content type'),
      )
    );

    $entityTypeManager = \Drupal::service('entity_type.manager');
    $contentTypes = $entityTypeManager->getStorage('node_type')->loadMultiple();
    foreach ($contentTypes as $idx => $contentType) {

      $value = null;
      if (isset($configContents[$idx]) && $configContents[$idx]['enabled']) {
        $value = 1;
      }

      $form['form']['contents'][$idx]['enabled'] = array(
        '#type' => 'checkbox',
        '#title' => $contentType->label() . " (" . $idx . ")",
        '#default_value' => $value
      );
    }

    return parent::buildForm($form, $form_state);

  }


  public function submitForm(array &$form, FormStateInterface $form_state) {

    $values = $form_state->getValues();
    $config = $this->config(SmsMailForm::$configName);

    foreach ($values as $key => $value) {
      $config->set($key, $value);
    }

    $config->save();

    parent::submitForm($form, $form_state);

  }

}

