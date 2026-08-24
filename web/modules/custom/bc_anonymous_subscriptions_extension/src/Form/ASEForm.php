<?php

namespace Drupal\bc_anonymous_subscriptions_extension\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use \Drupal\anonymous_subscriptions\Entity\Subscription;
use Drupal\Component\Utility\Crypt;

class ASEForm extends ConfigFormBase {

  public static $configName = 'bc_anonymous_subscriptions_extension.settings';

  public function getFormId() {
    return 'bc_anonymous_subscriptions_extension_settings';
  }

  protected function getEditableConfigNames() {
    return [ASEForm::$configName];
  }

  public function buildForm(array $form, FormStateInterface $form_state) {

    $config = $this->config(ASEForm::$configName);

    $form['form']['enabled'] = [
      '#type' => 'checkbox',
      '#title' => 'this is enabled',
      '#default_value' => $config->get('enabled')
    ];

    $form['form']['all_os2web_news'] = array(
      '#type' => 'checkbox',
      '#title' => 'Set automatic all users as member of anonymous subscriptions os2web_news',
      '#default_value' => $config->get('all_os2web_news')
    );

    $form['form']['all_os2web_news_now'] = array(
      '#type' => 'checkbox',
      '#title' => 'Set all users as member of anonymous subscriptions os2web_news now and flag active news if user is not already subscribed',
    );

    $form['form']['group_notification'] = array(
      '#type' => 'checkboxes',
      '#options' => array(),
      '#title' => $this->t('Group notifications :'),
      '#description' => $this->t('The checked groups above will send notification on new content in the group to the group members.<br>Enable the Group node type in Anonymous subscriptions config and fill out the template.'),
      '#default_value' => array()
    );

    $nids = \Drupal::entityQuery('node')
      ->accessCheck(FALSE)
      ->condition('type','group')
      ->execute();
    $nodes = \Drupal\node\Entity\Node::loadMultiple($nids);
    foreach ( $nodes AS $node ) {
      $form['form']['group_notification']['#options'][$node->id()] = $node->getTitle();
      if ($config->get('group_notification')[$node->id()] ?? null) {
        $form['form']['group_notification']['#default_value'][$node->id()] = $node->id();
      }
    }

    return parent::buildForm($form, $form_state);
  }


  public function submitForm(array &$form, FormStateInterface $form_state) {

    $values = $form_state->getValues();
    $config = $this->config(ASEForm::$configName);

    foreach ($values as $key => $value) {
      $config->set($key, $value);
    }

    $config->save();

    if (!empty($values['all_os2web_news_now'])) {

      $userStorage = \Drupal::entityTypeManager()->getStorage('user');
      $query = $userStorage->getQuery();
      $uids = $query
        ->accessCheck(FALSE)
        ->condition('status', '1')
        ->execute();

      if (count($uids) > 0) {

        // get unread flag + service
        $flagService = \Drupal::service('flag');
        $flag = $flagService->getFlagById('unread');

        // get all active news
        $nids = \Drupal::entityQuery('node')
          ->accessCheck(FALSE)
          ->condition('type','os2web_news')
          ->condition('status', 1)
          ->execute();
        $news = \Drupal\node\Entity\Node::loadMultiple($nids);

        $users = $userStorage->loadMultiple($uids);
        foreach ($users as $user) {
          if (!empty($user->get('mail')->value)) {
            // set user subscription to news if not already subscribed
            $email = $user->get('mail')->value;
            $query = \Drupal::entityQuery('anonymous_subscription')
              ->condition('email', $email)
              ->condition('entity_type', 'node')
              ->condition('entity_bundle', 'os2web_news');
            $ids = $query->execute();
            if (count($ids) == 0) {
              $subscription = Subscription::create([
                'email' => $user->get('mail')->value,
                'code' => Crypt::randomBytesBase64(20),
                'entity_bundle' => 'os2web_news',
                'entity_type' => 'node',
                'verified' => 1,
              ]);
              $subscription->save();

              // flag unread news to new subscribed user
              if ($flag) {
                foreach ($news as $node) {
                  $flagging = $flagService->getFlagging($flag, $node, $user);
                  if (!$flagging) {
                    $flagService->flag($flag, $node, $user);
                  }
                }
              }
            }
          }
        }

      }
    }

    parent::submitForm($form, $form_state);

  }
}
