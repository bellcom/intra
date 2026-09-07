# BC External Search

Drupal 11 module that ports the external data-source functionality from Bellcom Search 0.5.7 into Drupal.

## Included sources

- Hetzner Cloud servers from the six Bellcom projects.
- Authenticated Drupal 7 customer pages under `/kunder/...`.
- One-off external URLs.
- Recursive external website crawling.
- Combined import of all configured sources.

## Storage model

Imported records are normal Drupal nodes of bundle `bc_external_search_document` (`External search document`). This is intentional: the existing Search API `entity:node` datasource and the existing AI Search rendered-content field can index them without a second vector database or a custom Search API datasource.

Each imported node stores:

- source type (`hetzner`, `drupal7_kunde`, `url`, `crawl`)
- source/facet name
- stable external ID
- external URL when one exists
- summary
- searchable content
- raw source data (hidden from the default node display)
- last import time

Missing records are unpublished when `--deactivate-missing` is used, which removes them from normal published-node search/access flows.

## Secrets

Secrets are referenced through Drupal Key entities. API tokens/passwords are never stored in `bc_external_search.settings`.

Configure the module at:

`/admin/config/search/bc-external-search`

## Drush

```bash
drush bc-external-search:hetzner --deactivate-missing
drush bc-external-search:drupal7-kunder
drush bc-external-search:index-url https://example.com --source=example
drush bc-external-search:crawl-url https://docs.example.com --source=docs --depth=2 --limit=100 --deactivate-missing
drush bc-external-search:import-all --deactivate-missing
```

## URL sources in configuration

One source per line:

```text
docs|https://docs.example.com/|2|100
website|https://www.example.com/|3|250
```

Format: `source|url|depth|limit`.

## Search API / AI Search

The module defaults to the existing Search API index machine name `ai_intranet_poc`.

After the first import, verify that the index's `entity:node` datasource includes the new bundle. If the datasource indexes all node bundles, no Search API configuration change is required. Then index queued items with the site's normal Search API process.

The default node display includes `summary` and `search content`, so Search API's Rendered HTML output processor can pass the imported data to the existing AI Search vector backend.

## Security difference from the Symfony project

The old Symfony code encoded the final eight characters of the Hetzner API token in the source/facet value. This module deliberately does **not** do that. Sources are stable names such as `hetzner:produktion`; no token fragment is exposed in indexed data.
