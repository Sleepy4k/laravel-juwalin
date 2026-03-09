<?php

namespace App\Services\Proxmox;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Low-level HTTP client for the Proxmox VE REST API.
 *
 * Handles authentication, form encoding (required by Proxmox's Perl parser),
 * and the {"data": ...} response envelope unwrapping.
 *
 * @see https://pve.proxmox.com/pve-docs/api-viewer/
 */
final class ProxmoxHttpClient
{
    private readonly string $baseUrl;

    private readonly string $tokenId;

    private readonly string $secret;

    private readonly bool $verifyTls;

    private readonly int $timeout;

    public function __construct()
    {
        $this->baseUrl = rtrim((string) config('proxmox.url'), '/');
        $this->tokenId = (string) config('proxmox.token_id');
        $this->secret = (string) config('proxmox.secret');
        $this->verifyTls = (bool) config('proxmox.verify_tls');
        $this->timeout = (int) config('proxmox.timeout');
    }

    /**
     * @return array<mixed>|bool|float|int|string|null
     */
    public function get(string $path): mixed
    {
        try {
            return $this->unwrap($this->client()->get("{$this->baseUrl}/api2/json/{$path}"));
        } catch (ConnectionException $e) {
            throw new RuntimeException("Cannot connect to Proxmox at {$this->baseUrl}: {$e->getMessage()}", 0, $e);
        }
    }

    /**
     * @param  array<string, mixed>                    $data
     * @return array<mixed>|bool|float|int|string|null
     */
    public function post(string $path, array $data = []): mixed
    {
        try {
            // Proxmox VE only accepts application/x-www-form-urlencoded bodies.
            // Sending Content-Type: application/json causes "Not a HASH reference".
            return $this->unwrap(
                $this->formClient()->post("{$this->baseUrl}/api2/json/{$path}", $data),
            );
        } catch (ConnectionException $e) {
            throw new RuntimeException("Cannot connect to Proxmox at {$this->baseUrl}: {$e->getMessage()}", 0, $e);
        }
    }

    /**
     * @param array<string, mixed> $data
     */
    public function put(string $path, array $data = []): void
    {
        try {
            $this->unwrap(
                $this->formClient()->put("{$this->baseUrl}/api2/json/{$path}", $data),
            );
        } catch (ConnectionException $e) {
            throw new RuntimeException("Cannot connect to Proxmox at {$this->baseUrl}: {$e->getMessage()}", 0, $e);
        }
    }

    /**
     * @return array<mixed>|bool|float|int|string|null
     */
    public function delete(string $path): mixed
    {
        try {
            return $this->unwrap($this->client()->delete("{$this->baseUrl}/api2/json/{$path}"));
        } catch (ConnectionException $e) {
            throw new RuntimeException("Cannot connect to Proxmox at {$this->baseUrl}: {$e->getMessage()}", 0, $e);
        }
    }

    // ─────────────────────────────── HTTP internals ───────────────────────────

    private function client(): PendingRequest
    {
        return Http::withHeaders([
            'Authorization' => "PVEAPIToken={$this->tokenId}={$this->secret}",
            'Accept'        => 'application/json',
        ])
            ->withOptions([
                'verify'          => $this->verifyTls,
                'connect_timeout' => $this->timeout,
            ])
            ->timeout($this->timeout);
    }

    private function formClient(): PendingRequest
    {
        return $this->client()->asForm();
    }

    /**
     * Unwraps the {"data": ...} envelope; throws on HTTP errors or Proxmox error bodies.
     */
    private function unwrap(Response $response): mixed
    {
        if ($response->failed()) {
            $json = $response->json();

            if (is_array($json)) {
                $raw = $json['errors'] ?? $json['message'] ?? $json;
                $errors = is_array($raw) ? json_encode($raw) : (string) $raw;
            } else {
                $errors = trim((string) $response->body());
            }

            throw new RuntimeException(
                "Proxmox API [{$response->status()}]: {$errors}",
            );
        }

        $json = $response->json();

        if (!is_array($json)) {
            return $response->body() ?: null;
        }

        return $json['data'] ?? null;
    }
}
