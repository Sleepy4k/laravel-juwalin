<?php

namespace App\Services\Proxmox;

/**
 * Proxmox VE node and cluster operations.
 */
final class ProxmoxNodeService
{
    private readonly string $defaultNode;

    public function __construct(private readonly ProxmoxHttpClient $client)
    {
        $this->defaultNode = (string) config('proxmox.node');
    }

    /**
     * GET /api2/json/nodes/{node}/status.
     *
     * @return array<string, mixed>
     */
    public function getNodeStatus(?string $node = null): array
    {
        return (array) $this->client->get("nodes/{$this->resolveNode($node)}/status");
    }

    /**
     * GET /api2/json/nodes/{node}/storage.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getNodeStorage(?string $node = null): array
    {
        return (array) $this->client->get("nodes/{$this->resolveNode($node)}/storage");
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

    private function resolveNode(?string $node): string
    {
        return $node ?? $this->defaultNode;
    }
}
