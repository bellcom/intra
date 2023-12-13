<?php
namespace Drupal\bc_node_cleanup\Form;

use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\user\Entity\User;

class NodeCleanupForm extends ConfigFormBase {

  public static $configName = 'bc_node_cleanup.settings';

  public function getFormId() {
    return 'bc_node_cleanup_settings';
  }

  protected function getEditableConfigNames() {
    return [NodeCleanupForm::$configName];
  }

  public function buildForm(array $form, FormStateInterface $form_state) {

    $config = $this->config(NodeCleanupForm::$configName);

    $form['form']['enabled'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('this is enabled'),
      '#default_value' => $config->get('enabled'),
      '#weight' => 1
    );


    $form['form']['run'] = array(
      '#type' => 'select',
      '#title' => $this->t('run every'),
      '#options' => array(
        1 => $this->t('every day'),
        7 => $this->t('every week'),
        30 => $this->t('every month'),
        182 => $this->t('every 6. month'),
        365 => $this->t('every year'),
      ),
      '#default_value' => $config->get('run'),
      '#weight' => 2
    );

    $checkvalues = $config->get('checkvalues');

    $form['form']['checkvalues'] = array(
      '#type' => 'table',
      '#caption' => $this->t('<br>What should be checked :<br><br>'),
      '#header' => array(
        $this->t('Check type'),
        $this->t('Check value'),
        $this->t('mail'),
      ),
      '#weight' => 3
    );

    $checkes = array(
      'no_update' => $this->t('not updated'),
      'not_published' => $this->t('not published'),
      'no_author' => $this->t('no author'),
    );

    foreach ( $checkes AS $checkID => $title ) {

      $form['form']['checkvalues'][$checkID]['check_type'] = array(
        '#type' => 'checkbox',
        '#title' => $title,
        '#default_value' => ($checkvalues[$checkID]['check_type'] ?? '')
      );

      $form['form']['checkvalues'][$checkID]['check_value'] = array(
        '#type' => 'select',
        '#options' => array(
          1 => $this->t('> day'),
          7 => $this->t('> week'),
          30 => $this->t('> month'),
          182 => $this->t('> 6 month'),
          365 => $this->t('> year'),
        ),
        '#default_value' => ($checkvalues[$checkID]['check_value'] ?? '')
      );

      $form['form']['checkvalues'][$checkID]['mail'] = array(
        '#type' => 'checkbox',
        '#title' => $this->t('send mail to author/admin'),
        '#default_value' => ($checkvalues[$checkID]['mail'] ?? '')
      );

    }

    $form['form']['note'] = array(
      '#type' => 'item',
      '#description' => $this->t('NB. if not mail is selected the node is just deleted. Check value is always the value of node last change date'),
      '#weight' => 4
    );

    $user = null;
    $userID = $config->get('author') ?? null;

    if ($userID) {
      $user = User::load($userID);
    }

    $form['form']['author'] = array(
      '#type' => 'entity_autocomplete',
      '#target_type' => 'user',
      '#selection_settings' => ['include_anonymous' => FALSE],
      '#title' => $this->t('Default mail user'),
      '#default_value' => $user,
      '#description' => $this->t('if a node user is missing and mail is the choice, select a default user'),
      '#weight' => 5
    );


    $lastrun = $config->get('lastrun');
    if (!empty($lastrun)) {
      $lastrun = DrupalDateTime::createFromTimestamp(strtotime($lastrun));
    }

    $form['form']['lastrun'] = array(
      '#type' => 'datetime',
      '#title' => $this->t('Last run'),
      '#attributes' => array('readonly' => 'readonly'),
      '#default_value' => $lastrun,
      '#weight' => 6
    );


    return parent::buildForm($form, $form_state);
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {

    $values = $form_state->getValues();
    $config = $this->config(NodeCleanupForm::$configName);

    foreach ($values as $key => $value) {
        if ($key == 'lastrun') {

        } else {
          $config->set($key, $value);
        }
    }

    $config->save();

    parent::submitForm($form, $form_state);

  }


}
