<?php

$info = [
  // Available colors and color labels used in theme.
  'fields' => [
    'primary' => t('Primary color'),
    'primary_medium' => t('Primary (medium) color'),
    'text' => t('Text color'),
    'link' => t('Link color'),
    'link_hover' => t('Link (hover) color'),
    'link_visited' => t('Link (visited) color'),
    'footer_bkg_color' => t('Footer background color'),
    'footer_text_color' => t('Footer text color'),
    'footer_link_color' => t('Footer link color'),
  ],

  // Pre-defined color schemes.
  'schemes' => [
    'default' => [
      'title' => t('Standard (default)'),
      'colors' => [
        'primary' => '#747474',
        'primary_medium' => '#454545',
        'text' => '#1a1a1a',
        'link' => '#004D99',
        'link_hover' => '#004080',
        'link_visited' => '#800080',
        'footer_bkg_color' => '#474748',
        'footer_text_color' => '#999998',
        'footer_link_color' => '#fffffe',
      ],
    ],
    'virk' => [
      'title' => t('Virk'),
      'colors' => [
        'primary' => '#0059B3',
        'primary_medium' => '#004993',
        'text' => '#1a1a1a',
        'link' => '#004D99',
        'link_hover' => '#004080',
        'link_visited' => '#800080',
        'footer_bkg_color' => '#474748',
        'footer_text_color' => '#999998',
        'footer_link_color' => '#fffffe',
      ],
    ],
    'borger' => [
      'title' => t('Borger.dk'),
      'colors' => [
        'primary' => '#44831E',
        'primary_medium' => '#3C5C22',
        'text' => '#1a1a1a',
        'link' => '#004D99',
        'link_hover' => '#004080',
        'link_visited' => '#800080',
        'footer_bkg_color' => '#474748',
        'footer_text_color' => '#999998',
        'footer_link_color' => '#fffffe',
      ],
    ],
  ],

  // CSS files (excluding @import) to rewrite with new color scheme.
  'css' => [
    'dist/stylesheets/stylesheet.css',
  ],

  // Files to copy.
  'copy' => [
    'logo.svg',
  ],
];
