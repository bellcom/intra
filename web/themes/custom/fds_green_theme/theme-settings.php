<?php

function fds_green_theme_form_system_theme_settings_alter(
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
  $form['contact_information']['bygning_first'] = [
    '#type' => 'textfield',
    '#title' => t('Bygning'),
    '#default_value' => theme_get_setting('bygning_first'),
  ];
  $form['contact_information']['afdeling'] = [
    '#type' => 'textfield',
    '#title' => t('Afdeling'),
    '#default_value' => theme_get_setting('afdeling'),
  ];
  $form['contact_information']['afdeling_bygning'] = [
    '#type' => 'textfield',
    '#title' => t('Afdelingsbygning'),
    '#default_value' => theme_get_setting('afdeling_bygning'),
  ];
  $form['contact_information']['afdeling_indgang'] = [
    '#type' => 'textfield',
    '#title' => t('Afdelingsindgang'),
    '#default_value' => theme_get_setting('afdeling_indgang'),
  ];
}
