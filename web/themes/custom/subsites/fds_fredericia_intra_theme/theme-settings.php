<?php

/**
 * @file
 * Theme settings for Fredericia Intra.
 *
 * Overrides Drupal 11-incompatible file validators inherited from
 * fds_fredericia_main_theme.
 */

use Drupal\Core\File\FileSystemInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Implements hook_form_system_theme_settings_alter().
 */
function fds_fredericia_intra_theme_form_system_theme_settings_alter(array &$form, FormStateInterface $form_state) {
  $d11_image_validators = [
    'FileExtension' => ['extensions' => 'png gif jpg jpeg webp'],
    'FileIsImage' => [],
  ];

  if (isset($form['footer']['footer_image_container']['footer_image_upload'])) {
    $form['footer']['footer_image_container']['footer_image_upload']['#element_validate'] = [
      'fds_fredericia_intra_theme_footer_image_validate',
    ];
  }

  if (isset($form['banner_image'])) {
    $form['banner_image']['#upload_validators'] = $d11_image_validators;
    $form['banner_image']['#upload_location'] = 'public://fds_fredericia_intra_theme/images/';
    $form['banner_image']['#default_value'] = theme_get_setting('banner_image');
  }

  if (isset($form['placeholder_images'])) {
    $form['placeholder_images']['#upload_validators'] = $d11_image_validators;
    $form['placeholder_images']['#upload_location'] = 'public://fds_fredericia_intra_theme/images/';
    $form['placeholder_images']['#default_value'] = theme_get_setting('placeholder_images');
  }
}

/**
 * Validates footer image upload for the intra theme (Drupal 11 constraints).
 */
function fds_fredericia_intra_theme_footer_image_validate(array $element, FormStateInterface $form_state) {
  $validators = [
    'FileExtension' => ['extensions' => 'png gif jpg jpeg webp'],
    'FileIsImage' => [],
  ];
  $file = file_save_upload('footer_image_upload', $validators, 'public://', 0, FileSystemInterface::EXISTS_REPLACE);
  if ($file) {
    $file->setPermanent();
    $file->save();
    $form_state->setValue('footer_image_path', $file->getFileUri());
  }
}
