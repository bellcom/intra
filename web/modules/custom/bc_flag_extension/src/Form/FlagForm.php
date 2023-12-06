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


    // main menu items
    $menu_tree = \Drupal::menuTree();
    $parameters = $menu_tree->getCurrentRouteMenuTreeParameters('main');
    $tree = $menu_tree->load('main', $parameters);
    $manipulators = array(
      array('callable' => 'menu.default_tree_manipulators:checkAccess'),
      array('callable' => 'menu.default_tree_manipulators:generateIndexAndSort'),
    );
    $tree = $menu_tree->transform($tree, $manipulators);

    $mainMenu = array();
    foreach ($tree as $item) {
      if ($item->link->isEnabled()) {
        $param = $item->link->getRouteParameters();
        if (!empty($param['node'])) {
          $mainMenu[$param['node']] = (string) $item->link->getTitle();
        }
      }
    }

    $form['menu_counter'] = array(
      '#type' => 'table',
      '#caption' => $this->t('<br>Add counter number to main menu :<br><br>'),
      '#title' => 'menu counter',
      '#header' => array(
        $this->t('menu item'),
        $this->t('counter type')
      )
    );

    $menu_counter = $config->get('menu_counter');
    foreach ( $mainMenu AS $nodeID => $title ) {

      $form['menu_counter'][$nodeID]['menu_item'] = array(
        '#type' => 'item',
        '#title' => $title,
        '#default_value' => $title
      );

      $default_value = '';
      if (!empty($menu_counter)) {
        if (!empty($menu_counter[$nodeID]['counter_type'])) {
          $default_value = $menu_counter[$nodeID]['counter_type'];
        }
      }

      $form['menu_counter'][$nodeID]['counter_type'] = array(
        '#type' => 'select',
        '#options' => array(
          '' => '',
          'unread_group_content' => $this->t('Unread group content'),
          'unread_news_content' => $this->t('Unread news content'),
          'unread_organisation_content' => $this->t('Unread organisation content')
        ),
        '#default_value' => $default_value
      );
    }

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
