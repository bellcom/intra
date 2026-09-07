<?php

namespace Drupal\bc_external_search\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\key\KeyRepositoryInterface;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Cookie\CookieJar;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;

final class Drupal7CustomerImporter {

  public function __construct(
    private readonly ClientInterface $httpClient,
    private readonly ConfigFactoryInterface $configFactory,
    private readonly KeyRepositoryInterface $keyRepository,
    private readonly PageExtractor $extractor,
    private readonly ExternalDocumentStorage $documents,
    private readonly LoggerInterface $logger,
  ) {}

  public function isConfigured(): bool {
    $config = $this->configFactory->get('bc_external_search.settings');
    return trim((string) $config->get('drupal7.base_url')) !== ''
      && trim((string) $config->get('drupal7.username')) !== ''
      && trim((string) $config->get('drupal7.password_key')) !== '';
  }

  /** @return array{found:int,created:int,updated:int,failed:int} */
  public function import(int $maxPages = 100, int $limit = 1000, string $startUrl = '/kunder?name='): array {
    $config = $this->configFactory->get('bc_external_search.settings');
    $baseUrl = rtrim(trim((string) $config->get('drupal7.base_url')), '/');
    $username = trim((string) $config->get('drupal7.username'));
    $passwordKeyId = trim((string) $config->get('drupal7.password_key'));
    $source = trim((string) $config->get('drupal7.source')) ?: 'kunde';
    $passwordKey = $passwordKeyId !== '' ? $this->keyRepository->getKey($passwordKeyId) : NULL;
    $password = $passwordKey ? (string) $passwordKey->getKeyValue() : '';

    if ($baseUrl === '' || $username === '' || $password === '') {
      throw new \RuntimeException('Drupal 7 customer source is not fully configured.');
    }
    if (!filter_var($baseUrl, FILTER_VALIDATE_URL)) {
      throw new \InvalidArgumentException('Invalid Drupal 7 base URL.');
    }

    $jar = new CookieJar();
    $this->login($baseUrl, $username, $password, $jar);
    $links = $this->collectCustomerLinks($baseUrl, $startUrl, max(1, $maxPages), max(1, $limit), $jar);

    $created = 0;
    $updated = 0;
    $failed = 0;
    foreach ($links as $url) {
      try {
        $response = $this->request('GET', $url, $jar);
        if ($response->getStatusCode() >= 400) {
          $failed++;
          continue;
        }
        $data = $this->extractCustomerPage((string) $response->getBody(), $url);
        $result = $this->documents->upsert('drupal7_kunde', $source, $url, $data['title'], $data['content'], $data['summary'], $url);
        $result['created'] ? $created++ : $updated++;
      }
      catch (\Throwable $e) {
        $failed++;
        $this->logger->warning('Drupal 7 customer import failed for @url: @message', ['@url' => $url, '@message' => $e->getMessage()]);
      }
    }

    return ['found' => count($links), 'created' => $created, 'updated' => $updated, 'failed' => $failed];
  }

  private function login(string $baseUrl, string $username, string $password, CookieJar $jar): void {
    $loginUrl = $baseUrl . '/user/login';
    $response = $this->request('GET', $loginUrl, $jar);
    if ($response->getStatusCode() >= 400) {
      throw new \RuntimeException('Could not load Drupal 7 login form.');
    }

    $fields = $this->extractLoginFields((string) $response->getBody());
    $action = $fields['_action'] ?? $loginUrl;
    unset($fields['_action']);
    $fields['name'] = $username;
    $fields['pass'] = $password;
    $fields['op'] = trim((string) ($fields['op'] ?? '')) ?: 'Log ind';

    $postUrl = $this->absoluteUrl($action, $baseUrl);
    $response = $this->request('POST', $postUrl, $jar, [
      'form_params' => $fields,
      'allow_redirects' => FALSE,
      'headers' => [
        'Referer' => $loginUrl,
        'Origin' => $baseUrl,
      ],
    ]);

    if ($response->getStatusCode() >= 300 && $response->getStatusCode() < 400) {
      $location = $response->getHeaderLine('Location') ?: '/';
      $response = $this->request('GET', $this->absoluteUrl($location, $baseUrl), $jar);
    }

    $body = (string) $response->getBody();
    if ($response->getStatusCode() >= 400 || ($this->looksLikeLoginForm($body) && !$this->looksLoggedIn($body))) {
      throw new \RuntimeException('Drupal 7 login failed.');
    }
  }

  /** @return array<string,string> */
  private function extractLoginFields(string $html): array {
    $xpath = $this->xpath($html);
    $form = $xpath->query('//form[@id="user-login" or @id="user-login-form"]')->item(0)
      ?? $xpath->query('//form[contains(@action, "user/login")]')->item(0)
      ?? $xpath->query('//form[.//input[@name="name"] and .//input[@name="pass"]]')->item(0);

    if (!$form instanceof \DOMElement) {
      throw new \RuntimeException('Could not find Drupal 7 login form.');
    }

    $fields = [];
    if ($form->getAttribute('action') !== '') {
      $fields['_action'] = $form->getAttribute('action');
    }
    foreach ((new \DOMXPath($form->ownerDocument))->query('.//input', $form) ?: [] as $input) {
      if ($input instanceof \DOMElement && $input->getAttribute('name') !== '') {
        $fields[$input->getAttribute('name')] = $input->getAttribute('value');
      }
    }
    if (!isset($fields['form_id'])) {
      throw new \RuntimeException('Drupal 7 login form has no form_id.');
    }
    return $fields;
  }

  /** @return string[] */
  private function collectCustomerLinks(string $baseUrl, string $startUrl, int $maxPages, int $limit, CookieJar $jar): array {
    $links = [];
    $emptyPages = 0;
    $firstListUrl = $this->absoluteUrl($startUrl, $baseUrl);
    $baseListUrl = strtok($firstListUrl, '?') ?: $baseUrl . '/kunder';

    for ($page = 0; $page < $maxPages && count($links) < $limit; $page++) {
      $candidates = $page === 0
        ? array_values(array_unique([$firstListUrl, $baseUrl . '/kunder?name=', $baseUrl . '/kunder']))
        : [$baseUrl . '/kunder?name=&page=' . $page, $baseListUrl . '?name=&page=' . $page];

      $html = NULL;
      foreach ($candidates as $listUrl) {
        $response = $this->request('GET', $listUrl, $jar, ['headers' => ['Referer' => $baseUrl . '/user/login']]);
        if ($response->getStatusCode() < 400) {
          $html = (string) $response->getBody();
          break;
        }
      }
      if ($html === NULL || ($this->looksLikeLoginForm($html) && !$this->looksLoggedIn($html))) {
        break;
      }

      $new = 0;
      foreach ($this->extractCustomerLinks($html, $baseUrl) as $link) {
        if (!isset($links[$link])) {
          $links[$link] = $link;
          $new++;
          if (count($links) >= $limit) {
            break;
          }
        }
      }
      if ($new === 0) {
        $emptyPages++;
        if ($emptyPages >= 2) {
          break;
        }
      }
      else {
        $emptyPages = 0;
      }
    }
    return array_values($links);
  }

  /** @return string[] */
  private function extractCustomerLinks(string $html, string $baseUrl): array {
    $xpath = $this->xpath($html);
    $links = [];
    foreach ($xpath->query('//a[@href]') ?: [] as $node) {
      if (!$node instanceof \DOMElement) {
        continue;
      }
      $url = $this->absoluteUrl($node->getAttribute('href'), $baseUrl);
      $path = parse_url($url, PHP_URL_PATH) ?: '';
      if (!str_starts_with($path, '/kunder/')) {
        continue;
      }
      $slug = trim(substr($path, strlen('/kunder/')), '/');
      if ($slug === '' || str_contains($slug, '/') || str_contains($path, '/add') || str_contains($path, '/edit') || str_contains($path, '/delete')) {
        continue;
      }
      $links[$url] = $url;
    }
    return array_values($links);
  }

  /** @return array{title:string,content:string,summary:string} */
  private function extractCustomerPage(string $html, string $url): array {
    $xpath = $this->xpath($html);
    foreach ($xpath->query('//script|//style|//noscript|//svg|//canvas|//iframe|//template|//form|//input|//button|//select|//textarea|//nav|//footer|//header|//aside|//*[@role="navigation"]') ?: [] as $node) {
      $node->parentNode?->removeChild($node);
    }

    $title = $this->line((string) ($xpath->evaluate('string(//h1)') ?: $xpath->evaluate('string(//title)') ?: $url));
    $content = '';
    $selectors = [
      '//*[contains(concat(" ", normalize-space(@class), " "), " field-name-body ")]',
      '//*[contains(concat(" ", normalize-space(@class), " "), " node ")]',
      '//*[@role="main"]',
      '//*[contains(concat(" ", normalize-space(@class), " "), " region-content ")]',
      '//*[@id="content"]',
      '//main',
      '//body',
    ];
    foreach ($selectors as $selector) {
      $text = $this->extractor->normalizeText((string) $xpath->evaluate('string(' . $selector . ')'));
      if (mb_strlen($text) > mb_strlen($content)) {
        $content = $text;
      }
      if (mb_strlen($content) > 300) {
        break;
      }
    }
    $content = $content ?: $title;
    return ['title' => $title, 'content' => $content, 'summary' => $this->extractor->makeSummary($content)];
  }

  private function request(string $method, string $url, CookieJar $jar, array $options = []): ResponseInterface {
    $options['cookies'] = $jar;
    $options['timeout'] = $options['timeout'] ?? 30;
    $options['http_errors'] = FALSE;
    $headers = $options['headers'] ?? [];
    $headers += [
      'User-Agent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 Chrome/125 Safari/537.36 BCExternalSearchBot/0.1.0',
      'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
      'Accept-Language' => 'da-DK,da;q=0.9,en;q=0.8',
    ];
    $options['headers'] = $headers;
    return $this->httpClient->request($method, $url, $options);
  }

  private function looksLoggedIn(string $html): bool {
    return str_contains($html, 'Log ud') || str_contains($html, 'logout') || str_contains($html, 'Min side') || str_contains($html, 'admin/people') || str_contains($html, 'Tilføj indhold');
  }

  private function looksLikeLoginForm(string $html): bool {
    return str_contains($html, 'id="user-login"') || str_contains($html, "id='user-login'") || str_contains($html, 'id="user-login-form"') || str_contains($html, "id='user-login-form'") || str_contains($html, 'name="form_id" value="user_login') || str_contains($html, "name='form_id' value='user_login");
  }

  private function absoluteUrl(string $href, string $baseUrl): string {
    $normalized = $this->extractor->normalizeUrl($href, $baseUrl);
    if ($normalized !== NULL) {
      return $normalized;
    }
    return str_starts_with($href, '/') ? rtrim($baseUrl, '/') . $href : $href;
  }

  private function xpath(string $html): \DOMXPath {
    libxml_use_internal_errors(TRUE);
    $dom = new \DOMDocument();
    $dom->loadHTML('<?xml encoding="utf-8" ?>' . $html, LIBXML_NOWARNING | LIBXML_NOERROR);
    return new \DOMXPath($dom);
  }

  private function line(string $text): string {
    $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    return trim(preg_replace('/\s+/u', ' ', $text) ?? $text) ?: 'Uden titel';
  }

}
