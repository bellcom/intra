# Designsystem.dk - Fredericia Intra theme

Subtheme of `fds_fredericia_main_theme` for Fredericia intranet.

Templates, libraries, regions and theme settings are inherited from the base
theme. Add intranet-only overrides in this theme:

- PHP: `fds_fredericia_intra_theme.theme`
- CSS: `css/intra.css`
- Twig: `templates/` (create the folder when you need a template override)

### Hero paragraph

Theme support: `templates/paragraph/hero/paragraph--hero.html.twig` and `css/hero.css`.

The paragraph type and fields are **not** in this theme — they must exist in Drupal
(same as on FIC): machine name `hero`, with fields `field_background_image`,
`field_hero_heading`, `field_hero_subheading`, and `field_referenced_link` (nested
entities with `field_single_link` and `field_highlight`). Export/import config from
the FIC site or create matching paragraph types before editors can add heroes.

Color module uses `color/color.inc` and a copy of the base theme stylesheet at
`dist/stylesheets/stylesheet.css`. After updating CSS in
`fds_fredericia_main_theme`, copy that stylesheet here again so Color stays in
sync.
