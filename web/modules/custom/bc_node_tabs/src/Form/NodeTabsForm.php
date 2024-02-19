<?php
namespace Drupal\bc_node_tabs\Form;

use Drupal\bc_flag_extension\Form\FlagForm;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\bc_basic\Debug;
use Symfony\Component\HttpFoundation\Request;

//use Drupal\Core\Routing\RouteProviderInterface;
//use Drupal\search\Routing\SearchPageRoutes;
//use Drupal\search\Routing;

Class NodeTabsForm extends ConfigFormBase {

  public static $configName = 'bc_node_tabs.settings';


  public function getFormId() {
    return self::$configName;
  }


  protected function getEditableConfigNames() {
    return [NodeTabsForm::$configName];
  }


  /**
   * @param array $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *
   * @return array
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {

    $config = $this->config(NodeTabsForm::$configName);

    $form['form']['enabled'] = [
      '#type' => 'checkbox',
      '#title' => 'this is enabled',
      '#default_value' => $config->get('enabled')
    ];

    $Types = array();
    $nodeTypes = \Drupal\node\Entity\NodeType::loadMultiple();
    foreach ($nodeTypes AS $Type ) {
      $Types[$Type->id()] = $Type->label();
    }

    $form['form']['tabs'] = array(
      '#type' => 'table',
      '#caption' => $this->t('<br>Extra tabs :<br><br>'),
      '#title' => 'Tabs',
      '#header' => array(
        $this->t('content type'),
        $this->t('route'),
        $this->t('title'),
        $this->t('weight'),
        ''
      )
    );

    $tabs = $config->get('tabs');

    foreach ( $tabs AS $idx => $tab ) {
      if ($idx != 'new') {
        $form['form']['tabs'][$idx]['node_type'] = array(
          '#type' => 'select',
          '#options' => $Types,
          '#default_value' => $tab['node_type']
        );
        $form['form']['tabs'][$idx]['route'] = array(
          '#type' => 'textfield',
          '#autocomplete_route_name' => 'bc_node_tabs.autocomplete',
          '#default_value' => $tab['route']
        );
        $form['form']['tabs'][$idx]['title'] = array(
          '#type' => 'textfield',
          '#default_value' => $tab['title']
        );
        $form['form']['tabs'][$idx]['weight'] = array(
          '#type' => 'number',
          '#default_value' => $tab['weight']
        );
        $form['form']['tabs'][$idx]['delete'] = array(
          '#type' => 'checkbox',
          '#title' => $this->t('delete'),
        );
      }
    }

    array_unshift($Types, array('new' => "add new"));
    $form['form']['tabs']['new']['node_type'] = array(
        '#type' => 'select',
        '#options' => $Types
    );

    $form['form']['tabs']['new']['route'] = array(
      '#type' => 'textfield',
      '#autocomplete_route_name' => 'bc_node_tabs.autocomplete',
    );

    $form['form']['tabs']['new']['title'] = array(
      '#type' => 'textfield',
    );

    $form['form']['tabs']['new']['weight'] = array(
      '#type' => 'number',
    );

    return parent::buildForm($form, $form_state);
  }


  public function submitForm(array &$form, FormStateInterface $form_state) {

    $values = $form_state->getValues();
    $config = $this->config(NodeTabsForm::$configName);

    foreach ($values as $key => $value) {

      if ($key === 'tabs') {
        $_tabs = array();
        foreach ($value AS $_key => $tab ) {
          if (is_string($_key) && $_key === 'new') {
              $add = true;
              if ($tab['node_type'] == 'new') $add = false;
              if (empty($tab['route'])) $add = false;
              if (empty($tab['title'])) $add = false;
              if (empty($tab['weight']) || !is_integer((int) $tab['weight'])) $tab['weight'] = 10;
              if ($add) $_tabs[] = $tab;
          } elseif (is_integer($_key) && !$tab['delete']) {

            if (empty($tab['weight']) || !is_integer((int)$tab['weight'])) $tab['weight'] = 10;
            $_tabs[] = $tab;
          }
        }
        $config->set('tabs', $_tabs);
      } else {

        $config->set($key, $value);
      }
    }

    $config->save();

    parent::submitForm($form, $form_state);

  }
}
