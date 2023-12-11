<?php
namespace Drupal\bc_reassign_author\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class ReassignAuthorForm extends ConfigFormBase {

  public static $configName = 'bc_reassign_author.settings';

  public function getFormId() {
    return 'bc_reassign_author_settings';
  }

  protected function getEditableConfigNames() {
    return [ReassignAuthorForm::$configName];
  }

  public function buildForm(array $form, FormStateInterface $form_state) {

    $config = $this->config(ReassignAuthorForm::$configName);

    $form['form']['enabled'] = array(
      '#type' => 'checkbox',
      '#title' => 'this is enabled' . "<br><br>",
      '#default_value' => $config->get('enabled')
    );

    $decription = 'If user are deleted programmatically, $user->delete(), the user node are deleted automatic before the user are deleted.<br>';
    $decription .= 'To keep the nodes, you can create a copy of the node and assign to default user.';

    $form['form']['info'] = array(
      '#type' => 'item',
      '#description' => $this->t($decription)
    );

    $user = null;
    $userID = $config->get('default') ?? null;

    if ($userID) {
      $user = $account = \Drupal\user\Entity\User::load($userID);
    }

    $form['form']['default'] = array(
      '#type' => 'entity_autocomplete',
      '#target_type' => 'user',
      '#selection_settings' => ['include_anonymous' => FALSE],
      '#title' => $this->t('Default assign to user'),
      '#default_value' => $user,
      '#description' => $this->t('if a user is deleted then re-assign the user nodes to above user<br>')
    );


    // content type
    $node_types = \Drupal\node\Entity\NodeType::loadMultiple();
    $types = [];
    foreach ($node_types as $node_type) {
      $types[$node_type->id()] = $node_type->label();
    }

    $form['form']['content_types'] = array(
      '#type' => 'checkboxes',
      '#title' => $this->t('Only nodes with content type below are cloned : '),
      '#options' => $types,
      '#default_value' => $config->get('content_types') ?? array()
    );

    // Role
    $roles = \Drupal::entityTypeManager()->getStorage('user_role')->loadMultiple();
    if (!empty($roles)) {
      foreach ($roles as $role) {
        $options[$role->id()] = $role->label();
      }
    }

    $form['form']['exclude'] = array(
      '#type' => 'checkboxes',
      '#title' => $this->t('Exclude node user with role below and delete the nodes : '),
        '#options' => $options,
        '#default_value' => $config->get('exclude') ?? array()
    );

    return parent::buildForm($form, $form_state);

  }


  public function submitForm(array &$form, FormStateInterface $form_state) {

    $values = $form_state->getValues();
    $config = $this->config(ReassignAuthorForm::$configName);

    foreach ($values as $key => $value) {
        $config->set($key, $value);
    }

    $config->save();

    parent::submitForm($form, $form_state);

  }


}
