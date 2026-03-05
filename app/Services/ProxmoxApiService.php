<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Proxmox VE API Service.
 *
 * Authentication: API Token (PVEAPIToken header).
 * All responses follow the {"data": ...} envelope.
 *
 * @see https://pve.proxmox.com/pve-docs/api-viewer/
 */
class ProxmoxApiService
{
    private readonly string $baseUrl;

    private readonly string $tokenId;

    private readonly string $secret;

    private readonly string $node;

    private readonly bool $verifyTls;

    private readonly int $timeout;

    public function __construct()
    {
        $this->baseUrl = rtrim((string) config('proxmox.url'), '/');
        $this->tokenId = (string) config('proxmox.token_id');
        $this->secret = (string) config('proxmox.secret');
        $this->node = (string) config('proxmox.node');
        $this->verifyTls = (bool) config('proxmox.verify_tls');
        $this->timeout = (int) config('proxmox.timeout');
    }

    // ───────────────────────────── Node / Cluster ─────────────────────────────

    /**
     * GET /api2/json/nodes/{node}/status.
     *
     * @return array<string, mixed>
     */
    public function getNodeStatus(?string $node = null): array
    {
        return $this->get("nodes/{$this->resolveNode($node)}/status");
    }

    /**
     * GET /api2/json/nodes/{node}/storage.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getNodeStorage(?string $node = null): array
    {
        return $this->get("nodes/{$this->resolveNode($node)}/storage");
    }

    /**
     * Computes free RAM (MB) and free disk (GB) from node status + storage.
     *
     * @return array{free_memory_mb: int, free_disk_gb: float, total_memory_mb: int, used_memory_mb: int}
     */
    public function getAvailableResources(?string $node = null): array
    {
        $status = $this->getNodeStatus($node);
        $memory = $status['memory'] ?? [];
        $totalMb = (int) round(($memory['total'] ?? 0) / 1048576);
        $usedMb = (int) round(($memory['used'] ?? 0) / 1048576);

        $storages = $this->getNodeStorage($node);
        $freeBytes = 0;
        foreach ($storages as $store) {
            if (($store['enabled'] ?? 1) && ($store['active'] ?? 1)) {
                $freeBytes += (int) ($store['avail'] ?? 0);
            }
        }

        return [
            'free_memory_mb'  => $totalMb - $usedMb,
            'free_disk_gb'    => round($freeBytes / 1073741824, 2),
            'total_memory_mb' => $totalMb,
            'used_memory_mb'  => $usedMb,
        ];
    }

    // ───────────────────────────── LXC Lifecycle ──────────────────────────────

    /**
     * GET /api2/json/nodes/{node}/lxc.
     *
     * @return array<int, array<string, mixed>>
     */
    public function listContainers(?string $node = null): array
    {
        return $this->get("nodes/{$this->resolveNode($node)}/lxc");
    }

    /**
     * GET /api2/json/nodes/{node}/lxc/{vmid}/status/current.
     *
     * @return array<string, mixed>
     */
    public function getContainerStatus(int $vmid, ?string $node = null): array
    {
        return $this->get("nodes/{$this->resolveNode($node)}/lxc/{$vmid}/status/current");
    }

    /**
     * POST /api2/json/nodes/{node}/lxc/{vmid}/clone
     * Returns the UPID task string.
     *
     * @param array<string, mixed> $options
     */
    public function cloneContainer(
        int $templateVmid,
        int $newVmid,
        string $hostname,
        array $options = [],
        ?string $node = null,
    ): string {
        $payload = array_merge([
            'newid'    => $newVmid,
            'hostname' => $hostname,
            'storage'  => $options['storage'] ?? 'local-lvm',
            'full'     => 1,
        ], $options);

        return (string) $this->post(
            "nodes/{$this->resolveNode($node)}/lxc/{$templateVmid}/clone",
            $payload,
        );
    }

    /**
     * PUT /api2/json/nodes/{node}/lxc/{vmid}/config
     * Example: ['cores' => 2, 'memory' => 2048, 'rootfs' => 'local-lvm:20'].
     *
     * @param array<string, mixed> $config
     */
    public function configureContainer(int $vmid, array $config, ?string $node = null): void
    {
        $this->put("nodes/{$this->resolveNode($node)}/lxc/{$vmid}/config", $config);
    }

    /**
     * POST /api2/json/nodes/{node}/lxc/{vmid}/status/start.
     */
    public function startContainer(int $vmid, ?string $node = null): string
    {
        return (string) $this->post("nodes/{$this->resolveNode($node)}/lxc/{$vmid}/status/start");
    }

    /**
     * POST /api2/json/nodes/{node}/lxc/{vmid}/status/stop.
     */
    public function stopContainer(int $vmid, ?string $node = null): string
    {
        return (string) $this->post("nodes/{$this->resolveNode($node)}/lxc/{$vmid}/status/stop");
    }

    /**
     * POST /api2/json/nodes/{node}/lxc/{vmid}/status/shutdown.
     */
    public function shutdownContainer(int $vmid, int $timeout = 60, ?string $node = null): string
    {
        return (string) $this->post(
            "nodes/{$this->resolveNode($node)}/lxc/{$vmid}/status/shutdown",
            ['timeout' => $timeout, 'forceStop' => 0],
        );
    }

    /**
     * PUT /api2/json/nodes/{node}/lxc/{vmid}/resize
     * Resizes a container disk. $size must include unit, e.g. "20G" or "+5G".
     */
    public function resizeContainerDisk(
        int $vmid,
        string $disk,
        string $size,
        ?string $node = null,
    ): void {
        $this->put(
            "nodes/{$this->resolveNode($node)}/lxc/{$vmid}/resize",
            ['disk' => $disk, 'size' => $size],
        );
    }

    /**
     * POST /api2/json/nodes/{node}/lxc/{vmid}/status/reboot.
     */
    public function rebootContainer(int $vmid, ?string $node = null): string
    {
        return (string) $this->post("nodes/{$this->resolveNode($node)}/lxc/{$vmid}/status/reboot");
    }

    /**
     * DELETE /api2/json/nodes/{node}/lxc/{vmid}
     * Container must be stopped before deletion.
     */
    public function deleteContainer(int $vmid, ?string $node = null): string
    {
        return (string) $this->delete("nodes/{$this->resolveNode($node)}/lxc/{$vmid}");
    }

    /**
     * POST /api2/json/nodes/{node}/lxc/{vmid}/termproxy
     * Returns a terminal proxy session ticket used to open an interactive
     * shell over WebSocket (xterm.js compatible).
     *
     * Routes through the local nginx proxy so that this POST and the
     * subsequent WebSocket share the same outbound IP, which is required
     * because Proxmox binds every ticket to the requesting IP.
     *
     * @return array{ticket: string, port: int, upid: string}
     */
    public function getTermProxy(int $vmid, ?string $node = null): array
    {
        return $this->proxyOrDirect(
            "nodes/{$this->resolveNode($node)}/lxc/{$vmid}/termproxy",
        );
    }

    /**
     * POST /api2/json/nodes/{node}/lxc/{vmid}/vncproxy
     * Returns a VNC ticket used to open a websocket-based console session.
     *
     * Routes through the local nginx proxy (/vnc-proxy/) so that BOTH this
     * API call and the subsequent WebSocket use the same outbound IP path
     * (same Cloudflare edge → same source IP → Proxmox accepts the ticket).
     *
     * @return array{port: int, ticket: string, cert: string, upid: string}
     */
    public function getVncProxy(int $vmid, ?string $node = null): array
    {
        return $this->proxyOrDirect(
            "nodes/{$this->resolveNode($node)}/lxc/{$vmid}/vncproxy",
            ['websocket' => 1],
        );
    }

    // ───────────────────────────── Task Polling ───────────────────────────────

    /**
     * GET /api2/json/nodes/{node}/tasks/{upid}/status.
     *
     * @return array{status: string, exitstatus: string|null}
     */
    public function getTaskStatus(string $upid, ?string $node = null): array
    {
        return $this->get("nodes/{$this->resolveNode($node)}/tasks/" . rawurlencode($upid) . '/status');
    }

    public function isTaskFinished(string $upid, ?string $node = null): bool
    {
        $status = $this->getTaskStatus($upid, $node);

        return ($status['status'] ?? '') === 'stopped'
            && ($status['exitstatus'] ?? '') === 'OK';
    }

    // ───────────────────────────── Utility ────────────────────────────────────

    /**
     * GET /api2/json/cluster/nextid.
     */
    public function getNextVmid(): int
    {
        $vmid = (int) $this->get('cluster/nextid');
        $min = (int) config('proxmox.vmid_start', 1000);

        return max($vmid, $min);
    }

    // ───────────────────────────── HTTP Layer ─────────────────────────────────

    /**
     * @return array<mixed>|bool|float|int|string|null
     */
    private function get(string $path): mixed
    {
        try {
            return $this->unwrap($this->client()->get("{$this->baseUrl}/api2/json/{$path}"));
        } catch (ConnectionException $e) {
            throw new RuntimeException("Cannot connect to Proxmox at {$this->baseUrl}: {$e->getMessage()}", 0, $e);
        }
    }

    /**
     * @param array<string, mixed> $data
     */
    private function post(string $path, array $data = []): mixed
    {
        try {
            // Proxmox VE only accepts application/x-www-form-urlencoded bodies.
            // We always use asForm() — even for parameterless actions (start/stop)
            // — because sending Content-Type: application/json (Laravel's default)
            // causes Proxmox's Perl parser to throw "Not a HASH reference".
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
    private function put(string $path, array $data = []): void
    {
        try {
            // Same encoding requirement as POST — always form-encoded.
            $this->unwrap(
                $this->formClient()->put("{$this->baseUrl}/api2/json/{$path}", $data),
            );
        } catch (ConnectionException $e) {
            throw new RuntimeException("Cannot connect to Proxmox at {$this->baseUrl}: {$e->getMessage()}", 0, $e);
        }
    }

    private function delete(string $path): mixed
    {
        try {
            return $this->unwrap($this->client()->delete("{$this->baseUrl}/api2/json/{$path}"));
        } catch (ConnectionException $e) {
            throw new RuntimeException("Cannot connect to Proxmox at {$this->baseUrl}: {$e->getMessage()}", 0, $e);
        }
    }

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

    /**
     * HTTP client pre-configured with form-encoding.
     * Proxmox VE only accepts application/x-www-form-urlencoded bodies;
     * sending JSON causes "Not a HASH reference" in the Perl API server.
     */
    private function formClient(): PendingRequest
    {
        return $this->client()->asForm();
    }

    /**
     * Unwraps {"data": ...} envelope; throws on HTTP errors or Proxmox error bodies.
     * Handles cases where Proxmox returns a non-JSON body (e.g. Perl stack traces).
     */
    private function unwrap(Response $response): mixed
    {
        if ($response->failed()) {
            $json = $response->json();

            // Prefer structured Proxmox error fields when available.
            if (is_array($json)) {
                $raw = $json['errors'] ?? $json['message'] ?? $json;
                $errors = is_array($raw) ? json_encode($raw) : (string) $raw;
            } else {
                // Non-JSON body (e.g. Proxmox Perl error, HTML 500 page)
                $errors = trim((string) $response->body());
            }

            throw new RuntimeException(
                "Proxmox API [{$response->status()}]: {$errors}",
            );
        }

        $json = $response->json();

        // Proxmox always wraps successful data in {"data": ...}.
        // If the response is not JSON (shouldn't happen on success), return raw body.
        if (!is_array($json)) {
            return $response->body() ?: null;
        }

        return $json['data'] ?? null;
    }

    private function resolveNode(?string $node): string
    {
        return $node ?? $this->node;
    }

    /**
     * POST directly to Proxmox API.
     *
     * @param  array<string, mixed> $body
     * @return array<string, mixed>
     */
    private function proxyOrDirect(string $path, array $body = []): array
    {
        return (array) $this->post($path, $body);
    }
}
