<?php

namespace Drupal\bc_external_search\Service;

use Drupal\Core\Config\ConfigFactoryInterface;

final class ImportManager {

  public function __construct(
    private readonly ConfigFactoryInterface $configFactory,
    private readonly HetznerImporter $hetznerImporter,
    private readonly Drupal7CustomerImporter $drupal7Importer,
    private readonly UrlImporter $urlImporter,
  ) {}

  /** @return array<string,mixed> */
  public function importAll(bool $deactivateMissing = FALSE): array {
    $result = [];
    $result['hetzner'] = $this->hetznerImporter->import(NULL, $deactivateMissing);

    if ($this->drupal7Importer->isConfigured()) {
      $result['drupal7_kunder'] = $this->drupal7Importer->import();
    }
    else {
      $result['drupal7_kunder'] = ['skipped' => 'not configured'];
    }

    $result['url_sources'] = [];
    foreach ($this->getConfiguredUrlSources() as $source) {
      $result['url_sources'][$source['source']] = $this->urlImporter->crawl(
        $source['url'],
        $source['source'],
        $source['depth'],
        $source['limit'],
        FALSE,
        $deactivateMissing,
      );
    }

    return $result;
  }

  /** @return array<int,array{source:string,url:string,depth:int,limit:int}> */
  public function getConfiguredUrlSources(): array {
    $raw = (string) $this->configFactory->get('bc_external_search.settings')->get('url_sources');
    $sources = [];
    foreach (preg_split('/\R/', $raw) ?: [] as $line) {
      $line = trim($line);
      if ($line === '' || str_starts_with($line, '#')) {
        continue;
      }
      $parts = array_map('trim', explode('|', $line));
      if (count($parts) < 2 || !filter_var($parts[1], FILTER_VALIDATE_URL)) {
        continue;
      }
      $sources[] = [
        'source' => $parts[0] ?: ((string) parse_url($parts[1], PHP_URL_HOST) ?: 'external'),
        'url' => $parts[1],
        'depth' => isset($parts[2]) ? max(0, (int) $parts[2]) : 2,
        'limit' => isset($parts[3]) ? max(1, (int) $parts[3]) : 50,
      ];
    }
    return $sources;
  }

}
