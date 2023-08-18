<?php

$info = [
  // Available colors and color labels used in theme.
  'fields' => [
    'background_color__default' => t('Background color - default'),
    'background_color__primary' => t('Background color - primary'),
    'background_color__secondary' => t('Background color - secondary (has white text)'),
    'background_color__tertiary' => t('Background color - tertiary (has white text)'),
    // 'background_color__navigation' => t('Background color - navigation  (has white text)'),
    // 'background_color__footer_primary' => t('Background color - footer primary (has white text)'),
    // 'background_color__footer_secondary' => t('Background color - footer secondary (has white text)'),
    // 'background_color__button' => t('Background color - button'),
    // 'background_color__button_hover' => t('Background color - button:hover'),
    // 'color__link' => t('Link color'),
    // 'color__link__hover' => t('Link:hover color'),
    // 'color__footer_link' => t('Footer link color'),
    // 'color__bullets' => t('Bullets color')
  ],

  // Pre-defined color schemes.
  'schemes' => [
    'default' => [
      'title' => t('Standard (default)'),
      'colors' => [
        'background_color__default' => '#007ca3',
        'background_color__primary' => '#007ca3',
        'background_color__secondary' => '#4195b2',
        'background_color__tertiary' => '#165f1d',
        // 'background_color__navigation' => '#921730',
        // 'background_color__footer_primary' => '#00335b',
        // 'background_color__footer_secondary' => '#2a4f75',
        // 'background_color__button' => '#196620',
        // 'background_color__button_hover' => '#114f16',
        // 'color__link' => '#022e51',
        // 'color__link__hover' => '#01233e',
        // 'color__footer_link' => '#6dcff6',
        // 'color__bullets' => '#a5122f',
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
