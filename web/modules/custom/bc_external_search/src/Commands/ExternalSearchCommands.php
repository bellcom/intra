<?php

namespace Drupal\bc_external_search\Commands;

use Drupal\bc_external_search\Service\Drupal7CustomerImporter;
use Drupal\bc_external_search\Service\HetznerImporter;
use Drupal\bc_external_search\Service\ImportManager;
use Drupal\bc_external_search\Service\UrlImporter;
use Drush\Commands\DrushCommands;

final class ExternalSearchCommands extends DrushCommands {

  public function __construct(
    private readonly HetznerImporter $hetznerImporter,
    private readonly Drupal7CustomerImporter $drupal7Importer,
    private readonly UrlImporter $urlImporter,
    private readonly ImportManager $importManager,
  ) {}

  /**
   * Import Hetzner Cloud servers.
   *
   * @command bc-external-search:hetzner
   * @aliases bces:hetzner
   * @option project Import one project only.
   * @option deactivate-missing Unpublish missing servers.
   */
  public function hetzner(array $options = ['project' => NULL, 'deactivate-missing' => FALSE]): void {
    $result = $this->hetznerImporter->import($options['project'] ?: NULL, (bool) $options['deactivate-missing']);
    $this->output()->writeln(json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
  }

  /**
   * Import customers from the configured Drupal 7 customer site.
   *
   * @command bc-external-search:drupal7-kunder
   * @aliases bces:kunder
   * @option max-pages Maximum number of customer list pages.
   * @option limit Maximum number of customers.
   * @option start-url Customer list start URL.
   */
  public function drupal7Customers(array $options = ['max-pages' => 100, 'limit' => 1000, 'start-url' => '/kunder?name=']): void {
    $result = $this->drupal7Importer->import((int) $options['max-pages'], (int) $options['limit'], (string) $options['start-url']);
    $this->output()->writeln(json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
  }

  /**
   * Import one external URL.
   *
   * @command bc-external-search:index-url
   * @aliases bces:url
   * @param url URL to import.
   * @option source Source/facet name.
   */
  public function indexUrl(string $url, array $options = ['source' => NULL]): void {
    $result = $this->urlImporter->indexUrl($url, $options['source'] ?: NULL);
    $this->output()->writeln(json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
  }

  /**
   * Crawl an external web source recursively.
   *
   * @command bc-external-search:crawl-url
   * @aliases bces:crawl
   * @param url Start URL.
   * @option source Source/facet name.
   * @option depth Crawl depth.
   * @option limit Maximum pages.
   * @option external Allow external hosts.
   * @option deactivate-missing Unpublish missing pages.
   */
  public function crawlUrl(string $url, array $options = ['source' => NULL, 'depth' => 2, 'limit' => 50, 'external' => FALSE, 'deactivate-missing' => FALSE]): void {
    $result = $this->urlImporter->crawl(
      $url,
      $options['source'] ?: NULL,
      (int) $options['depth'],
      (int) $options['limit'],
      (bool) $options['external'],
      (bool) $options['deactivate-missing'],
    );
    $this->output()->writeln(json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
  }

  /**
   * Import all configured external sources.
   *
   * @command bc-external-search:import-all
   * @aliases bces:all
   * @option deactivate-missing Unpublish missing Hetzner and crawl records.
   */
  public function importAll(array $options = ['deactivate-missing' => FALSE]): void {
    $result = $this->importManager->importAll((bool) $options['deactivate-missing']);
    $this->output()->writeln(json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
  }

}
