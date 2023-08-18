<?php

function fds_ringsted_theme_form_system_theme_settings_alter(
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

  $form['options'] = [
    '#type' => 'details',
    '#title' => t('Options'),
    '#group' => 'fds_base_theme',
  ];
  $form['options']['show_last_update'] = [
    '#type' => 'checkbox',
    '#title' => t('Vis sidst opdateret dato på level 2 sider'),
    '#default_value' => theme_get_setting('show_last_update')
  ];
  $form['options']['show_newsletter_action'] = [
    '#type' => 'checkbox',
    '#title' => t('Vis nyhedsbrev tilmelding'),
    '#default_value' => theme_get_setting('show_newsletter_action')
  ];
  $form['options']['top_btn_text'] = [
    '#type' => 'textfield',
    '#title' => t('Skriv titlen på top link knap'),
    '#default_value' => theme_get_setting('top_btn_text')
  ];
  $form['options']['top_btn_url'] = [
    '#type' => 'textfield',
    '#title' => t('Indsæt gyldig url til top link knap'),
    '#default_value' => theme_get_setting('top_btn_url')
  ];

  $form['options']['language_picker'] = [
    '#type' => 'checkbox',
    '#title' => t('Vis flag'),
    '#default_value' => theme_get_setting('language_picker'),
  ];

  $form['options']['language_picker_da'] = [
    '#type' => 'textfield',
    '#title' => t('Indsæt gyldig url til Dansk flag'),
    '#default_value' => theme_get_setting('language_picker_da'),
    '#states' => [
      'visible' => [
        ':input[name="language_picker"]' => [
          'checked' => TRUE,
        ],
      ],
    ],
  ];
  $form['options']['language_picker_en'] = [
    '#type' => 'textfield',
    '#title' => t('Indsæt gyldig url til Engelsk flag'),
    '#default_value' => theme_get_setting('language_picker_en'),
    '#states' => [
      'visible' => [
        ':input[name="language_picker"]' => [
          'checked' => TRUE,
        ],
      ],
    ],
  ];


  // Facebook
  $form['social']['facebook'] = [
    '#type' => 'checkbox',
    '#title' => t('Facebook'),
    '#default_value' => theme_get_setting('facebook'),
  ];
  $form['social']['facebook_url'] = [
    '#type' => 'textfield',
    '#title' => t('Facebook URL'),
    '#default_value' => theme_get_setting('facebook_url'),
    '#states' => [
      'visible' => [
        ':input[name="facebook"]' => [
          'checked' => TRUE,
        ],
      ],
    ],
  ];
  $form['social']['facebook_tooltip'] = [
    '#type' => 'textfield',
    '#title' => t('Text when mouseovering the link'),
    '#default_value' => theme_get_setting('facebook_tooltip'),
    '#states' => [
      'visible' => [
        ':input[name="facebook"]' => [
          'checked' => TRUE,
        ],
      ],
    ],
  ];

  // Twitter
  $form['social']['twitter'] = [
    '#type' => 'checkbox',
    '#title' => t('Twitter'),
    '#default_value' => theme_get_setting('twitter'),
  ];
  $form['social']['twitter_url'] = [
    '#type' => 'textfield',
    '#title' => t('Twitter URL'),
    '#default_value' => theme_get_setting('twitter_url'),
    '#states' => [
      'visible' => [
        ':input[name="twitter"]' => [
          'checked' => TRUE,
        ],
      ],
    ],
  ];
  $form['social']['twitter_tooltip'] = [
    '#type' => 'textfield',
    '#title' => t('Text when mouseovering the link'),
    '#default_value' => theme_get_setting('twitter_tooltip'),
    '#states' => [
      'visible' => [
        ':input[name="twitter"]' => [
          'checked' => TRUE,
        ],
      ],
    ],
  ];

  // Instagram
  $form['social']['instagram'] = [
    '#type' => 'checkbox',
    '#title' => t('Instagram'),
    '#default_value' => theme_get_setting('instagram'),
  ];
  $form['social']['instagram_url'] = [
    '#type' => 'textfield',
    '#title' => t('Instagram URL'),
    '#default_value' => theme_get_setting('instagram_url'),
    '#states' => [
      'visible' => [
        ':input[name="instagram"]' => [
          'checked' => TRUE,
        ],
      ],
    ],
  ];
  $form['social']['instagram_tooltip'] = [
    '#type' => 'textfield',
    '#title' => t('Text when mouseovering the link'),
    '#default_value' => theme_get_setting('instagram_tooltip'),
    '#states' => [
      'visible' => [
        ':input[name="instagram"]' => [
          'checked' => TRUE,
        ],
      ],
    ],
  ];

  // LinkedIn
  $form['social']['linkedin'] = [
    '#type' => 'checkbox',
    '#title' => t('LinkedIn'),
    '#default_value' => theme_get_setting('linkedin'),
  ];
  $form['social']['linkedin_url'] = [
    '#type' => 'textfield',
    '#title' => t('LinkedIn URL'),
    '#default_value' => theme_get_setting('linkedin_url'),
    '#states' => [
      'visible' => [
        ':input[name="linkedin"]' => [
          'checked' => TRUE,
        ],
      ],
    ],
  ];
  $form['social']['linkedin_tooltip'] = [
    '#type' => 'textfield',
    '#title' => t('Text when mouseovering the link'),
    '#default_value' => theme_get_setting('linkedin_tooltip'),
    '#states' => [
      'visible' => [
        ':input[name="linkedin"]' => [
          'checked' => TRUE,
        ],
      ],
    ],
  ];

  // Youtube
  $form['social']['youtube'] = [
    '#type' => 'checkbox',
    '#title' => t('Youtube'),
    '#default_value' => theme_get_setting('youtube'),
  ];
  $form['social']['youtube_url'] = [
    '#type' => 'textfield',
    '#title' => t('Youtube URL'),
    '#default_value' => theme_get_setting('youtube_url'),
    '#states' => [
      'visible' => [
        ':input[name="youtube"]' => [
          'checked' => TRUE,
        ],
      ],
    ],
  ];
  $form['social']['youtube_tooltip'] = [
    '#type' => 'textfield',
    '#title' => t('Text when mouseovering the link'),
    '#default_value' => theme_get_setting('youtube_tooltip'),
    '#states' => [
      'visible' => [
        ':input[name="youtube"]' => [
          'checked' => TRUE,
        ],
      ],
    ],
  ];

}
