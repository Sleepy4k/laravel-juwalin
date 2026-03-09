<?php

namespace App\Services\Proxmox;

/**
 * Proxmox VE LXC container lifecycle operations.
 */
final class ProxmoxContainerService
{
    private readonly string $defaultNode;

    public function __construct(private readonly ProxmoxHttpClient $client)
    {
        $this->defaultNode = (string) config('proxmox.node');
    }

    // ───────────────────────────── Listing & Status ───────────────────────────

    /**
     * GET /api2/json/nodes/{node}/lxc.
     *
     * @return array<int, array<string, mixed>>
     */
    public function listContainers(?string $node = null): array
    {
        return (array) $this->client->get("nodes/{$this->resolveNode($node)}/lxc");
    }

    /**
     * GET /api2/json/nodes/{node}/lxc/{vmid}/status/current.
     *
     * @return array<string, mixed>
     */
    public function getStatus(int $vmid, ?string $node = null): array
    {
        return (array) $this->client->get("nodes/{$this->resolveNode($node)}/lxc/{$vmid}/status/current");
    }

    // ────────────────────────────── Provisioning ──────────────────────────────

    /**
     * POST /api2/json/nodes/{node}/lxc/{vmid}/clone.
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

        return (string) $this->client->post(
            "nodes/{$this->resolveNode($node)}/lxc/{$templateVmid}/clone",
            $payload,
        );
    }

    /**
     * PUT /api2/json/nodes/{node}/lxc/{vmid}/config.
     * Example: ['cores' => 2, 'memory' => 2048].
     *
     * @param array<string, mixed> $config
     */
    public function configureContainer(int $vmid, array $config, ?string $node = null): void
    {
        $this->client->put("nodes/{$this->resolveNode($node)}/lxc/{$vmid}/config", $config);
    }

    /**
     * PUT /api2/json/nodes/{node}/lxc/{vmid}/resize.
     * $size must include unit, e.g. "20G" or "+5G".
     */
    public function resizeContainerDisk(
        int $vmid,
        string $disk,
        string $size,
        ?string $node = null,
    ): void {
        $this->client->put(
            "nodes/{$this->resolveNode($node)}/lxc/{$vmid}/resize",
            ['disk' => $disk, 'size' => $size],
        );
    }

    // ──────────────────────────── Power Actions ───────────────────────────────

    /**
     * POST /api2/json/nodes/{node}/lxc/{vmid}/status/start.
     */
    public function startContainer(int $vmid, ?string $node = null): string
    {
        return (string) $this->client->post("nodes/{$this->resolveNode($node)}/lxc/{$vmid}/status/start");
    }

    /**
     * POST /api2/json/nodes/{node}/lxc/{vmid}/status/stop.
     */
    public function stopContainer(int $vmid, ?string $node = null): string
    {
        return (string) $this->client->post("nodes/{$this->resolveNode($node)}/lxc/{$vmid}/status/stop");
    }

    /**
     * POST /api2/json/nodes/{node}/lxc/{vmid}/status/shutdown.
     */
    public function shutdownContainer(int $vmid, int $timeout = 60, ?string $node = null): string
    {
        return (string) $this->client->post(
            "nodes/{$this->resolveNode($node)}/lxc/{$vmid}/status/shutdown",
            ['timeout' => $timeout, 'forceStop' => 0],
        );
    }

    /**
     * POST /api2/json/nodes/{node}/lxc/{vmid}/status/reboot.
     */
    public function rebootContainer(int $vmid, ?string $node = null): string
    {
        return (string) $this->client->post("nodes/{$this->resolveNode($node)}/lxc/{$vmid}/status/reboot");
    }

    /**
     * DELETE /api2/json/nodes/{node}/lxc/{vmid}.
     * Container must be stopped before deletion.
     */
    public function deleteContainer(int $vmid, ?string $node = null): string
    {
        return (string) $this->client->delete("nodes/{$this->resolveNode($node)}/lxc/{$vmid}");
    }

    // ─────────────────────────────── VMID & Tasks ─────────────────────────────

    /**
     * GET /api2/json/cluster/nextid.
     */
    public function getNextVmid(): int
    {
        $vmid = (int) $this->client->get('cluster/nextid');
        $min = (int) config('proxmox.vmid_start', 1000);

        return max($vmid, $min);
    }

    /**
     * GET /api2/json/nodes/{node}/tasks/{upid}/status.
     *
     * @return array{status: string, exitstatus: string|null}
     */
    public function getTaskStatus(string $upid, ?string $node = null): array
    {
        return (array) $this->client->get(
            "nodes/{$this->resolveNode($node)}/tasks/" . rawurlencode($upid) . '/status',
        );
    }

    public function isTaskFinished(string $upid, ?string $node = null): bool
    {
        $status = $this->getTaskStatus($upid, $node);

        return ($status['status'] ?? '') === 'stopped'
            && ($status['exitstatus'] ?? '') === 'OK';
    }

    // ─────────────────────────────── Helpers ──────────────────────────────────

    private function resolveNode(?string $node): string
    {
        return $node ?? $this->defaultNode;
    }
}
