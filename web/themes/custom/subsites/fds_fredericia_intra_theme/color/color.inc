<?php

$info = [
  // Available colors and color labels used in theme.
  'fields' => [
    'background_color__default' => t('Background color - default'),
    'background_color__primary' => t('Background color - primary'),
    'background_color__secondary' => t('Background color - secondary'),
    'background_color__tertiary' => t('Background color - tertiary'),
    'background_color__navigation' => t('Background color - navigation'),
    'background_color__footer_primary' => t('Background color - footer primary'),
    'background_color__button' => t('Background color - button'),
    'background_color__button_hover' => t('Background color - button:hover'),
    'color__link' => t('Link color'),
    'color__link__hover' => t('Link:hover color'),
    'color__footer_link' => t('Footer link color'),
    'color__bullets' => t('Bullets color'),
  ],

  // Pre-defined color schemes.
  // Default values MUST match unique placeholders in stylesheet.css (Color
  // replaces by hex). Desired intranet look (#00335b) is hardcoded in
  // css/intra.css + css/hero.css until Color works — then remove those and
  // set primary/footer to #00335b via the Color UI (or an extra scheme).
  'schemes' => [
    'default' => [
      'title' => t('Standard (default)'),
      'colors' => [
        'background_color__default' => '#ebebec',
        'background_color__primary' => '#feffff',
        'background_color__secondary' => '#eae3c0',
        'background_color__tertiary' => '#066cc5',
        'background_color__navigation' => '#fffeff',
        'background_color__footer_primary' => '#d1d3d4',
        'background_color__button' => '#c30a32',
        'background_color__button_hover' => '#e6032f',
        'color__link' => '#c40a32',
        'color__link__hover' => '#333333',
        'color__footer_link' => '#333334',
        'color__bullets' => '#333433',
      ],
    ],
  ],

  // CSS files (excluding @import) to rewrite with new color scheme.
  // Color can only rewrite files in this theme, so stylesheet.css is a copy
  // of fds_fredericia_main_theme/dist/stylesheets/stylesheet.css.
  'css' => [
    'dist/stylesheets/stylesheet.css',
  ],
];
