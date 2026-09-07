<?php

namespace Drupal\bc_external_search\Service;

use GuzzleHttp\ClientInterface;
use Psr\Log\LoggerInterface;

final class UrlImporter {

  public function __construct(
    private readonly ClientInterface $httpClient,
    private readonly PageExtractor $extractor,
    private readonly ExternalDocumentStorage $documents,
    private readonly LoggerInterface $logger,
  ) {}

  /** @return array{created:int,updated:int,skipped:int} */
  public function indexUrl(string $url, ?string $source = NULL): array {
    $this->assertUrl($url);
    $source = trim((string) $source) ?: ((string) parse_url($url, PHP_URL_HOST) ?: 'external');

    $response = $this->httpClient->request('GET', $url, [
      'headers' => ['User-Agent' => 'BCExternalSearchBot/0.1.0'],
      'timeout' => 20,
      'http_errors' => FALSE,
    ]);

    if ($response->getStatusCode() >= 400) {
      throw new \RuntimeException(sprintf('HTTP %d for %s.', $response->getStatusCode(), $url));
    }

    $data = $this->extractor->extract((string) $response->getBody(), $url);
    $result = $this->documents->upsert('url', $source, $url, $data['title'], $data['content'], $data['summary'], $url);

    return [
      'created' => $result['created'] ? 1 : 0,
      'updated' => $result['created'] ? 0 : 1,
      'skipped' => 0,
    ];
  }

  /**
   * @return array{created:int,updated:int,skipped:int,deactivated:int,seen:int}
   */
  public function crawl(
    string $startUrl,
    ?string $source = NULL,
    int $depth = 2,
    int $limit = 50,
    bool $allowExternal = FALSE,
    bool $deactivateMissing = FALSE,
  ): array {
    $this->assertUrl($startUrl);
    $source = trim((string) $source) ?: ((string) parse_url($startUrl, PHP_URL_HOST) ?: 'external');
    $depth = max(0, $depth);
    $limit = max(1, $limit);

    $queue = [[$startUrl, 0]];
    $seen = [];
    $imported = [];
    $created = 0;
    $updated = 0;
    $skipped = 0;

    while ($queue !== [] && count($imported) < $limit) {
      [$url, $currentDepth] = array_shift($queue);
      $url = $this->extractor->normalizeUrl($url, $startUrl) ?? $url;

      if (isset($seen[$url])) {
        continue;
      }
      $seen[$url] = TRUE;

      if (!$allowExternal && !$this->extractor->isSameHost($url, $startUrl)) {
        continue;
      }

      try {
        $response = $this->httpClient->request('GET', $url, [
          'headers' => ['User-Agent' => 'BCExternalSearchBot/0.1.0'],
          'timeout' => 20,
          'allow_redirects' => ['max' => 5],
          'http_errors' => FALSE,
        ]);
        $contentType = strtolower($response->getHeaderLine('Content-Type'));
        if ($response->getStatusCode() >= 400 || !str_contains($contentType, 'text/html')) {
          $skipped++;
          continue;
        }

        $data = $this->extractor->extract((string) $response->getBody(), $url);
        $result = $this->documents->upsert('crawl', $source, $url, $data['title'], $data['content'], $data['summary'], $url);
        $result['created'] ? $created++ : $updated++;
        $imported[$url] = TRUE;

        if ($currentDepth < $depth) {
          foreach ($data['links'] as $link) {
            if (!isset($seen[$link]) && ($allowExternal || $this->extractor->isSameHost($link, $startUrl))) {
              $queue[] = [$link, $currentDepth + 1];
            }
          }
        }
      }
      catch (\Throwable $e) {
        $skipped++;
        $this->logger->warning('URL import failed for @url: @message', ['@url' => $url, '@message' => $e->getMessage()]);
      }
    }

    $deactivated = 0;
    if ($deactivateMissing && !$allowExternal) {
      $deactivated = $this->documents->deactivateMissing('crawl', $source, array_keys($imported));
    }

    return compact('created', 'updated', 'skipped', 'deactivated') + ['seen' => count($imported)];
  }

  private function assertUrl(string $url): void {
    if (!filter_var($url, FILTER_VALIDATE_URL) || !preg_match('@^https?://@i', $url)) {
      throw new \InvalidArgumentException('Ugyldig HTTP/HTTPS URL: ' . $url);
    }
  }

}
