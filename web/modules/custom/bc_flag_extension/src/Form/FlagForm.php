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

    $nids = array();
    foreach ($tree as $item) {
      if ($item->link->isEnabled()) {
        $parm = $item->link->getRouteParameters();
        if (!empty($parm['node'])) {
          $nids[] = $parm['node'];
        }
      }
    }

    $nodes = \Drupal::entityTypeManager()->getStorage('node')->loadMultiple($nids);
    $option_node = array('' => $this->t('none'));
    foreach ( $nodes AS $_node ) {
      $option_node[$_node->id()] = $_node->getTitle() . ' (' . $_node->id() . ')';
    }

    $option_type = array('', $this->t('none'));
    $contents_types = \Drupal\node\Entity\NodeType::loadMultiple();
    foreach ( $contents_types AS $type ) {
      $option_type[$type->id()] = $type->label();
    }

    $node_select = $config->get('node_select');

//    for ($i = 0; $i < 3; $i++) {
//
//      $form['form']['node_select'][$i] = [
//        '#type' => 'select',
//        '#title' => $this->t('Main menu links'),
//        '#default_value' => null, //$config->get('node_select'),
//        '#options' => $option_node,
//      ];
//
//      $form['form']['type_select'][$i] = [
//        '#type' => 'select',
//        '#title' => $this->t('content type counter'),
//        '#default_value' => null, // $config->get('type_select'),
//        '#options' => $option_type,
//      ];
//    }

    $multi = $config->get('multi');
    $multiCount = count($multi);

    $form['multi']['#tree'] = true;

    foreach( $multi AS $idx => $item )
    {
      $form['multi'][$idx]['name'] = array(
        '#type' => 'textfield',
        '#title' => 'name ' . $idx ,
        '#default_value' => $multi[$idx]
      );
    }

    $form['multi'][$multiCount]['name'] = array(
      '#type' => 'textfield',
      '#title' => 'name new '
    );

    $_form['multi'][$multiCount]['first'] = array(
      '#type' => 'textfield',
      '#title' => 'first new '
    );

    $_form['multi'][$multiCount]['last'] = array(
      '#type' => 'textfield',
      '#title' => 'last new '
    );


    $form['table'] = array(
      '#type' => 'table',
      '#title' => 'menu counter',
      '#header' => array(
        'Menu item',
        'content type'
      ),
      '#rows' => array(
        array(1,2),
        array(5,3)
      ),
      '#prefix' => '<div id="single-table-wrapper">',
      '#suffix' => '</div>',
    );


    return parent::buildForm($form, $form_state);
  }


  public function submitForm(array &$form, FormStateInterface $form_state) {

    $values = $form_state->getValues();
    $config = $this->config(FlagForm::$configName);

    foreach ($values as $key => $value) {
      if ($key == 'multi') {
        $new_values = array();
        foreach ( $value AS $name ) {
          if (!empty($name['name'])) $new_values[] = $name['name'];
        }
        $config->set($key, $new_values);

      } else {
        $config->set($key, $value);
      }
    }

    $config->save();

    parent::submitForm($form, $form_state);

  }


}
