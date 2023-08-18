<?php

function fds_redwhite_theme_form_system_theme_settings_alter(
  &$form,
  Drupal\Core\Form\FormStateInterface $form_state
) {

  // Texts.
  $form['texts'] = [
    '#type' => 'details',
    '#title' => t('Texts'),
    '#group' => 'fds_base_theme',
  ];
  $form['texts']['frontpage_banner_heading'] = [
    '#type' => 'textfield',
    '#title' => t('Banner overskrift.'),
    '#prefix' => '<h2>' . t('Frontpage') . '</h2>',
    '#default_value' => theme_get_setting('frontpage_banner_heading'),
  ];
  $form['texts']['frontpage_banner_subheading'] = [
    '#type' => 'textfield',
    '#title' => t('Banner underoverskrift.'),
    '#default_value' => theme_get_setting('frontpage_banner_subheading'),
  ];
  $form['contact_information']['vispaakort'] = [
    '#type' => 'textfield',
    '#title' => t('Vis på kort (link)'),
    '#default_value' => theme_get_setting('vispaakort'),
  ];
}
