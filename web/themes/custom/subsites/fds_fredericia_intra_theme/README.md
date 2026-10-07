# Designsystem.dk - Fredericia Intra theme

Subtheme of `fds_fredericia_main_theme` for Fredericia intranet.

Templates, libraries, regions and theme settings are inherited from the base
theme. Add intranet-only overrides in this theme:

- PHP: `fds_fredericia_intra_theme.theme`
- CSS: `css/intra.css`
- Twig: `templates/` (create the folder when you need a template override)

Color module uses `color/color.inc` and a copy of the base theme stylesheet at
`dist/stylesheets/stylesheet.css`. After updating CSS in
`fds_fredericia_main_theme`, copy that stylesheet here again so Color stays in
sync.
