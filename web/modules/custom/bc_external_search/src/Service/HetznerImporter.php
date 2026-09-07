<?php

namespace Drupal\bc_external_search\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\key\KeyRepositoryInterface;
use GuzzleHttp\ClientInterface;
use Psr\Log\LoggerInterface;

final class HetznerImporter {

  private const BASE_URL = 'https://api.hetzner.cloud/v1';

  private const PROJECTS = [
    'intern_backup' => ['label' => '0. Internt + backup', 'config' => 'intern_backup_key'],
    'produktion' => ['label' => '1. Produktion', 'config' => 'produktion_key'],
    'test' => ['label' => '2. Test', 'config' => 'test_key'],
    'udvikling' => ['label' => '3. Udvikling', 'config' => 'udvikling_key'],
    'backups' => ['label' => 'Backups', 'config' => 'backups_key'],
    'konsoleh' => ['label' => 'konsoleH', 'config' => 'konsoleh_key'],
  ];

  public function __construct(
    private readonly ClientInterface $httpClient,
    private readonly ConfigFactoryInterface $configFactory,
    private readonly KeyRepositoryInterface $keyRepository,
    private readonly ExternalDocumentStorage $documents,
    private readonly LoggerInterface $logger,
  ) {}

  /** @return array<string,array{label:string,key_id:string,configured:bool}> */
  public function getProjects(): array {
    $config = $this->configFactory->get('bc_external_search.settings');
    $result = [];
    foreach (self::PROJECTS as $project => $definition) {
      $keyId = trim((string) $config->get('hetzner.' . $definition['config']));
      $result[$project] = [
        'label' => $definition['label'],
        'key_id' => $keyId,
        'configured' => $this->getTokenByKeyId($keyId) !== '',
      ];
    }
    return $result;
  }

  /**
   * @return array{created:int,updated:int,deactivated:int,projects:int,servers:int}
   */
  public function import(?string $onlyProject = NULL, bool $deactivateMissing = FALSE): array {
    $projects = $this->getProjects();
    if ($onlyProject !== NULL) {
      $onlyProject = $this->normalizeProjectKey($onlyProject);
      $projects = isset($projects[$onlyProject]) ? [$onlyProject => $projects[$onlyProject]] : [];
    }

    $created = 0;
    $updated = 0;
    $deactivated = 0;
    $projectCount = 0;
    $serverCount = 0;

    foreach ($projects as $projectKey => $project) {
      $token = $this->getTokenByKeyId($project['key_id']);
      if ($token === '') {
        continue;
      }
      $projectCount++;
      $rawServers = $this->listServers($projectKey, $token);
      $seen = [];

      foreach ($rawServers as $rawServer) {
        $server = $this->normalizeServer($rawServer, $projectKey, $project['label']);
        $externalId = sprintf('hetzner:%s:%s', $projectKey, (string) ($server['id'] ?? $server['name']));
        $source = 'hetzner:' . $projectKey;
        $content = $this->buildSearchContent($server, $rawServer);
        $summary = $this->buildSummary($server);
        $rawJson = json_encode($rawServer, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '';

        $result = $this->documents->upsert(
          'hetzner',
          $source,
          $externalId,
          (string) ($server['name'] ?: 'Ukendt Hetzner-server'),
          $content,
          $summary,
          '',
          $rawJson,
        );
        $result['created'] ? $created++ : $updated++;
        $seen[] = $externalId;
        $serverCount++;
      }

      if ($deactivateMissing) {
        $deactivated += $this->documents->deactivateMissing('hetzner', 'hetzner:' . $projectKey, $seen);
      }
    }

    return [
      'created' => $created,
      'updated' => $updated,
      'deactivated' => $deactivated,
      'projects' => $projectCount,
      'servers' => $serverCount,
    ];
  }

  /** @return array<int,array<string,mixed>> */
  private function listServers(string $projectKey, string $token): array {
    $servers = [];
    $page = 1;
    do {
      $response = $this->httpClient->request('GET', self::BASE_URL . '/servers', [
        'headers' => [
          'Authorization' => 'Bearer ' . $token,
          'Accept' => 'application/json',
          'User-Agent' => 'BCExternalSearch/0.1.0',
        ],
        'query' => ['page' => $page, 'per_page' => 50],
        'timeout' => 20,
        'http_errors' => FALSE,
      ]);

      $body = (string) $response->getBody();
      if ($response->getStatusCode() < 200 || $response->getStatusCode() >= 300) {
        throw new \RuntimeException(sprintf('Hetzner API HTTP %d for %s: %s', $response->getStatusCode(), $projectKey, mb_substr($body, 0, 500)));
      }

      $data = json_decode($body, TRUE);
      if (!is_array($data)) {
        throw new \RuntimeException('Hetzner API returned invalid JSON.');
      }

      foreach ($data['servers'] ?? [] as $server) {
        if (is_array($server)) {
          $servers[] = $server;
        }
      }
      $next = $data['meta']['pagination']['next_page'] ?? NULL;
      $page = is_int($next) ? $next : 0;
    } while ($page > 0);

    return $servers;
  }

  /** @return array<string,mixed> */
  private function normalizeServer(array $server, string $projectKey, string $projectLabel): array {
    $publicNet = is_array($server['public_net'] ?? NULL) ? $server['public_net'] : [];
    $ipv4 = is_array($publicNet['ipv4'] ?? NULL) ? $publicNet['ipv4'] : [];
    $ipv6 = is_array($publicNet['ipv6'] ?? NULL) ? $publicNet['ipv6'] : [];
    $serverType = is_array($server['server_type'] ?? NULL) ? $server['server_type'] : [];
    $image = is_array($server['image'] ?? NULL) ? $server['image'] : [];
    $datacenter = is_array($server['datacenter'] ?? NULL) ? $server['datacenter'] : [];
    $location = is_array($datacenter['location'] ?? NULL) ? $datacenter['location'] : [];

    return [
      'project_key' => $projectKey,
      'project' => $projectLabel,
      'id' => $server['id'] ?? NULL,
      'name' => $server['name'] ?? '',
      'status' => $server['status'] ?? '',
      'ipv4' => $ipv4['ip'] ?? '',
      'ipv6' => $ipv6['ip'] ?? '',
      'server_type' => $serverType['name'] ?? '',
      'image' => $image['name'] ?? ($image['description'] ?? ''),
      'datacenter' => $datacenter['name'] ?? '',
      'location' => $location['name'] ?? '',
      'city' => $location['city'] ?? '',
      'country' => $location['country'] ?? '',
      'created' => $server['created'] ?? '',
    ];
  }

  private function buildSummary(array $server): string {
    $lines = [
      'Projekt: ' . $this->string($server['project'] ?? ''),
      'Navn: ' . $this->string($server['name'] ?? ''),
      'Status: ' . $this->string($server['status'] ?? ''),
      'IPv4: ' . $this->string($server['ipv4'] ?? ''),
      'Type: ' . $this->string($server['server_type'] ?? ''),
      'Datacenter: ' . $this->string($server['datacenter'] ?? ''),
    ];
    return implode("\n", array_filter($lines, static fn(string $line): bool => !str_ends_with($line, ': ')));
  }

  private function buildSearchContent(array $server, array $rawServer): string {
    $parts = [
      'Hetzner Cloud server',
      $this->buildSummary($server),
      'Projekt nøgle: ' . $this->string($server['project_key'] ?? ''),
      'IPv6: ' . $this->string($server['ipv6'] ?? ''),
      'Image: ' . $this->string($server['image'] ?? ''),
      'Lokation: ' . trim($this->string($server['city'] ?? '') . ' ' . $this->string($server['country'] ?? '')),
      'Oprettet: ' . $this->string($server['created'] ?? ''),
      '',
      'Normaliserede felter:',
      $this->flatten($server),
      '',
      'API-felter:',
      $this->flatten($rawServer),
    ];
    return trim(implode("\n", $parts));
  }

  private function flatten(array $data, string $prefix = ''): string {
    $lines = [];
    foreach ($data as $key => $value) {
      $path = $prefix === '' ? (string) $key : $prefix . '.' . $key;
      if (is_array($value)) {
        $nested = $this->flatten($value, $path);
        if ($nested !== '') {
          $lines[] = $nested;
        }
      }
      elseif ($value !== NULL && $value !== '') {
        $lines[] = $path . ': ' . $this->string($value);
      }
    }
    return implode("\n", $lines);
  }

  private function string(mixed $value): string {
    if ($value === NULL) {
      return '';
    }
    return is_scalar($value) ? trim((string) $value) : (json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '');
  }

  private function getTokenByKeyId(string $keyId): string {
    if ($keyId === '') {
      return '';
    }
    $key = $this->keyRepository->getKey($keyId);
    return $key ? trim((string) $key->getKeyValue()) : '';
  }

  private function normalizeProjectKey(string $projectKey): string {
    $key = strtolower(trim($projectKey));
    $key = str_replace([' ', '-', '.', '+'], '_', $key);
    return match ($key) {
      '0', 'intern', 'internt', 'internt_backup', 'intern_backup', 'backup_intern' => 'intern_backup',
      '1', 'prod', 'produktion', 'production' => 'produktion',
      '2', 'test' => 'test',
      '3', 'dev', 'udvikling', 'development' => 'udvikling',
      'backup', 'backups' => 'backups',
      'konsole', 'konsoleh' => 'konsoleh',
      default => $key,
    };
  }

}
