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
      '#title' => 'this is enabled',
      '#default_value' => $config->get('enabled')
    );

    $form['form']['gateway'] = array(
      '#type' => 'textfield',
      '#title' => 'mail sms gateway',
      '#default_value' => $config->get('gateway'),
      '#description' => 'the mailaddress that combine with numer send the sms ex "@sms.ringsted.int"'
    );


    $form['form']['members'] = array(
      '#type' => 'table',
      '#header' => array(
        $this->t('name'),
        $this->t('phone'),
        $this->t('active'),
        $this->t('remove')
      )
    );

    $members = $config->get('members');
    if (!empty($members)) {
      foreach ($members as $idx => $member) {

        $form['form']['members'][$idx]['name'] = [
          '#type' => 'textfield',
          '#value' => $member['name'] ?? "?",
        ];

        $form['form']['members'][$idx]['phone'] = [
          '#type' => 'textfield',
          '#value' => $member['phone'] ?? "?"
        ];

        $form['form']['members'][$idx]['active'] = [
          '#type' => 'checkbox',
          '#default_value' => $member['active'] ?? 0
        ];

        $form['form']['members'][$idx]['remove'] = [
          '#type' => 'checkbox'
        ];
      }
    }

    $form['form']['members']['add']['name'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Add'),
    );

    $form['form']['members']['add']['phone'] = array(
      '#type' => 'textfield',
      '#title' => '&nbsp;',
      '#value' => ''
    );

    $configContents = $config->get('contents');

    $form['form']['contents'] = array(
      '#type' => 'table',
      '#capiton' => 'content types',
      '#header' => array(
        $this->t('enabled'),
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
        '#title' => $idx,
        '#default_value' => $value
      );
    }

    return parent::buildForm($form, $form_state);

  }


  public function submitForm(array &$form, FormStateInterface $form_state) {

    $values = $form_state->getValues();
    $config = $this->config(SmsMailForm::$configName);

    $members = array();
    foreach ($values['members'] as $key => $member) {

      if (isset($member['remove']) && !is_object($member['remove']) && !empty($member['remove'])) {
        // do nothing if remove
      } else if ($key == 'add') {

        unset($values['members'][$key]);

        if (!empty($member['name']) && !empty($member['phone'])) {
          $new = array(
            'name' => $member['name'],
            'phone' => $member['phone'],
            'active' => 1
          );
          $members[] = $new;
        }
      } else {
        unset( $member['remove'] );
        $members[] = $member;
      }
    }
    $values['members'] = $members;

    foreach ($values as $key => $value) {
      if ($key == 'members') {
        $config->clear('members');
      }
      $config->set($key, $value);
    }

    $config->save();

    parent::submitForm($form, $form_state);

  }

}

