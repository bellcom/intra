<?php

namespace Drupal\bc_external_search\Service;

final class PageExtractor {

  /**
   * @return array{title:string, content:string, summary:string, links:string[]}
   */
  public function extract(string $html, ?string $baseUrl = NULL): array {
    libxml_use_internal_errors(TRUE);
    $dom = new \DOMDocument();
    $dom->loadHTML('<?xml encoding="utf-8" ?>' . $html, LIBXML_NOWARNING | LIBXML_NOERROR);
    $xpath = new \DOMXPath($dom);

    foreach ($xpath->query('//script|//style|//noscript|//svg|//canvas|//iframe|//template|//form|//input|//button|//select|//textarea') ?: [] as $node) {
      $node->parentNode?->removeChild($node);
    }

    foreach ($xpath->query('//nav|//footer|//header|//*[@role="navigation"]|//*[contains(concat(" ", normalize-space(@class), " "), " menu ")]') ?: [] as $node) {
      $node->parentNode?->removeChild($node);
    }

    $title = trim((string) $xpath->evaluate('string(//title)')) ?: 'Uden titel';
    $mainText = trim((string) $xpath->evaluate('string(//main)'));
    $bodyText = $mainText !== '' ? $mainText : (string) $xpath->evaluate('string(//body)');
    $content = $this->normalizeText(html_entity_decode($bodyText, ENT_QUOTES | ENT_HTML5, 'UTF-8'));

    return [
      'title' => $this->normalizeLine($title),
      'content' => $content,
      'summary' => $this->makeSummary($content),
      'links' => $baseUrl ? $this->extractLinks($xpath, $baseUrl) : [],
    ];
  }

  /** @return string[] */
  public function extractLinks(\DOMXPath $xpath, string $baseUrl): array {
    $links = [];
    foreach ($xpath->query('//a[@href]') ?: [] as $node) {
      if (!$node instanceof \DOMElement) {
        continue;
      }
      $absolute = $this->normalizeUrl($node->getAttribute('href'), $baseUrl);
      if ($absolute !== NULL) {
        $links[$absolute] = $absolute;
      }
    }
    return array_values($links);
  }

  public function normalizeUrl(string $href, string $baseUrl): ?string {
    $href = trim($href);
    if ($href === '' || str_starts_with($href, '#') || preg_match('/^(mailto|tel|javascript):/i', $href)) {
      return NULL;
    }

    $base = parse_url($baseUrl);
    if (!$base || empty($base['scheme']) || empty($base['host'])) {
      return NULL;
    }

    if (str_starts_with($href, '//')) {
      $href = $base['scheme'] . ':' . $href;
    }
    elseif (str_starts_with($href, '/')) {
      $href = $base['scheme'] . '://' . $base['host'] . $href;
    }
    elseif (!preg_match('/^https?:\/\//i', $href)) {
      $path = $base['path'] ?? '/';
      $dir = rtrim(str_replace('\\', '/', dirname($path)), '/');
      $href = $base['scheme'] . '://' . $base['host'] . ($dir === '' ? '' : $dir) . '/' . $href;
    }

    $parts = parse_url($href);
    if (!$parts || empty($parts['scheme']) || empty($parts['host']) || !in_array(strtolower($parts['scheme']), ['http', 'https'], TRUE)) {
      return NULL;
    }

    $path = $this->normalizePath($parts['path'] ?? '/');
    $url = strtolower($parts['scheme']) . '://' . strtolower($parts['host']) . $path;
    if (!empty($parts['query'])) {
      $url .= '?' . $parts['query'];
    }

    return rtrim($url, '/') ?: $url;
  }

  public function isSameHost(string $url, string $startUrl): bool {
    $urlHost = parse_url($url, PHP_URL_HOST);
    $startHost = parse_url($startUrl, PHP_URL_HOST);
    return is_string($urlHost) && is_string($startHost) && strtolower($urlHost) === strtolower($startHost);
  }

  public function normalizeText(string $text): string {
    $text = preg_replace('/[\x00-\x1F\x7F]/u', "\n", $text) ?? $text;
    $text = preg_replace('/[ \t]+/', ' ', $text) ?? $text;
    $text = preg_replace('/\R{2,}/', "\n", $text) ?? $text;
    $lines = array_values(array_filter(array_map('trim', explode("\n", $text)), static fn(string $line): bool => $line !== ''));
    return trim(implode("\n", $lines));
  }

  public function makeSummary(string $content): string {
    $lines = array_values(array_filter(array_map('trim', explode("\n", $content))));
    $summary = implode(' ', array_slice($lines, 0, 5));
    return $summary === '' ? 'Intet resume fundet.' : mb_substr($summary, 0, 500);
  }

  private function normalizeLine(string $text): string {
    return trim(preg_replace('/\s+/u', ' ', html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8')) ?? $text);
  }

  private function normalizePath(string $path): string {
    $segments = [];
    foreach (explode('/', $path) as $segment) {
      if ($segment === '' || $segment === '.') {
        continue;
      }
      if ($segment === '..') {
        array_pop($segments);
        continue;
      }
      $segments[] = $segment;
    }
    return '/' . implode('/', $segments);
  }

}
