<?php

use Drupal\Core\File\FileSystemInterface;
use Drupal\Core\Form\FormStateInterface;

function fds_fredericia_main_theme_form_system_theme_settings_alter(&$form, Drupal\Core\Form\FormStateInterface $form_state) {
  // Work-around for a core bug affecting admin themes. See issue #943212.
  if (isset($form_id)) {
    return;
  }

  $form['footer']['footer_show_latest_content_header'] = [
    '#prefix' => '<h3>',
    '#markup' => t('Latest content section'),
    '#suffix' => '</h3>',
  ];

  $form['footer']['footer_show_latest_content'] = [
    '#type' => 'checkbox',
    '#title' => t('enable '),
    '#default_value' => theme_get_setting('footer_show_latest_content'),
  ];



    $form['footer']['footer_image_choice'] = [
        '#type' => 'checkbox',
        '#title' => t('Use footer top image'),
        '#default_value' => theme_get_setting('footer_image_choice'),
    ];
    $form['footer']['footer_image_container'] = [
        '#type' => 'container',
        '#states' => [
            'invisible' => [
                'input[name="footer_image_choice"]' => ['checked' => FALSE],
            ],
        ]
    ];
    $form['footer']['footer_image_container']['footer_image_path'] = [
        '#type' => 'textfield',
        '#title' => t('Path to footer image'),
        '#default_value' => theme_get_setting('footer_image_path'),
    ];

    $form['footer']['footer_image_container']['footer_image_upload'] = [
        '#type' => 'file',
        '#title' => t('upload footer image'),
        '#default_value' => theme_get_setting('footer_image_upload'),
        '#element_validate' => array('fds_base_theme_footer_image_validate'),
    ];




  $form['branding'] = [
    '#type' => 'details',
    '#title' => t('Branding'),
    '#group' => 'fds_base_theme',
  ];

  $form['branding']['branding_toggle'] = [
    '#type' => 'checkbox',
    '#title' => t('Vis branding'),
    '#default_value' => theme_get_setting('branding_toggle'),
  ];

  $form['branding']['branding_text'] = [
    '#type' => 'textfield',
    '#title' => t('Tekst'),
    '#default_value' => theme_get_setting('branding_text'),
  ];

  // External Links section
  $form['external_links'] = [
    '#type' => 'fieldset',
    '#title' => t('External Links'),
    '#collapsible' => TRUE,
    '#collapsed' => FALSE,
    '#weight' => 10,
  ];

// Link 1 URL field
  $form['external_links']['link1_url'] = [
    '#type' => 'textfield',
    '#title' => t('Link 1 URL'),
    '#default_value' => theme_get_setting('link1_url'),
  ];

// Link 1 Label field
  $form['external_links']['link1_label'] = [
    '#type' => 'textfield',
    '#title' => t('Link 1 Label'),
    '#default_value' => theme_get_setting('link1_label'),
  ];

  // Link 2 URL field
  $form['external_links']['link2_url'] = [
    '#type' => 'textfield',
    '#title' => t('Link 2 URL'),
    '#default_value' => theme_get_setting('link2_url'),
  ];

// Link 2 Label field
  $form['external_links']['link2_label'] = [
    '#type' => 'textfield',
    '#title' => t('Link 2 Label'),
    '#default_value' => theme_get_setting('link2_label'),
  ];
  // Link 3 URL field
  $form['external_links']['link3_url'] = [
    '#type' => 'textfield',
    '#title' => t('Link 3 URL'),
    '#default_value' => theme_get_setting('link3_url'),
  ];

// Link 3 Label field
  $form['external_links']['link3_label'] = [
    '#type' => 'textfield',
    '#title' => t('Link 3 Label'),
    '#default_value' => theme_get_setting('link3_label'),
  ];
  // Link 4 URL field
  $form['external_links']['link4_url'] = [
    '#type' => 'textfield',
    '#title' => t('Link 4 URL'),
    '#default_value' => theme_get_setting('link4_url'),
  ];

// Link 4 Label field
  $form['external_links']['link4_label'] = [
    '#type' => 'textfield',
    '#title' => t('Link 4 Label'),
    '#default_value' => theme_get_setting('link4_label'),
  ];
  // Link 5 URL field
  $form['external_links']['link5_url'] = [
    '#type' => 'textfield',
    '#title' => t('Link 5 URL'),
    '#default_value' => theme_get_setting('link5_url'),
  ];

// Link 5 Label field
  $form['external_links']['link5_label'] = [
    '#type' => 'textfield',
    '#title' => t('Link 5 Label'),
    '#default_value' => theme_get_setting('link5_label'),
  ];
  // Link 6 URL field
  $form['external_links']['link6_url'] = [
    '#type' => 'textfield',
    '#title' => t('Link 6 URL'),
    '#default_value' => theme_get_setting('link6_url'),
  ];

// Link 6 Label field
  $form['external_links']['link6_label'] = [
    '#type' => 'textfield',
    '#title' => t('Link 6 Label'),
    '#default_value' => theme_get_setting('link6_label'),
  ];
  // Link 7 URL field
  $form['external_links']['link7_url'] = [
    '#type' => 'textfield',
    '#title' => t('Link 7 URL'),
    '#default_value' => theme_get_setting('link7_url'),
  ];

// Link 7 Label field
  $form['external_links']['link7_label'] = [
    '#type' => 'textfield',
    '#title' => t('Link 7 Label'),
    '#default_value' => theme_get_setting('link7_label'),
  ];
  // Link 8 URL field
  $form['external_links']['link8_url'] = [
    '#type' => 'textfield',
    '#title' => t('Link 8 URL'),
    '#default_value' => theme_get_setting('link8_url'),
  ];

// Link 8 Label field
  $form['external_links']['link8_label'] = [
    '#type' => 'textfield',
    '#title' => t('Link 8 Label'),
    '#default_value' => theme_get_setting('link8_label'),
  ];
  // Link 9 URL field
  $form['external_links']['link9_url'] = [
    '#type' => 'textfield',
    '#title' => t('Link 9 URL'),
    '#default_value' => theme_get_setting('link9_url'),
  ];

// Link 9 Label field
  $form['external_links']['link9_label'] = [
    '#type' => 'textfield',
    '#title' => t('Link 9 Label'),
    '#default_value' => theme_get_setting('link9_label'),
  ];


// Link 10 URL field
  $form['external_links']['link10_url'] = [
    '#type' => 'textfield',
    '#title' => t('Link 10 URL'),
    '#default_value' => theme_get_setting('link10_url'),
  ];

// Link 10 Label field
  $form['external_links']['link10_label'] = [
    '#type' => 'textfield',
    '#title' => t('Link 10 Label'),
    '#default_value' => theme_get_setting('link10_label'),
  ];
}


function fds_base_theme_footer_image_validate($element, FormStateInterface $form_state)  {
    global $base_url;

    $validators = array('file_validate_is_image' => array());
    $file = file_save_upload('footer_image_upload', $validators, "public://", NULL, FileSystemInterface::EXISTS_REPLACE);
    if (is_array($file)) {
        $file = array_pop($file);
        $file->status = FILE_STATUS_PERMANENT;
        $file->save();

        $uri = $file->getFileUri();
        $form_state->setValue('footer_image_path', $uri);
    }
}

