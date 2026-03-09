<?php

namespace App\Services\Proxmox;

/**
 * Proxmox VE console session operations.
 *
 * Architecture (server-side termproxy + WebSocket proxy):
 *   1. The Laravel server calls POST .../termproxy via API token.
 *      Proxmox binds the resulting vncticket to the SERVER's outbound IP.
 *   2. The server returns {wsUrl, ticket} to the browser, where wsUrl points
 *      to PROXMOX_WS_PROXY_URL (e.g. proxy.juwalin.cloud).
 *   3. The browser WebSocket connects to the proxy server.
 *   4. The proxy forwards the WebSocket to Proxmox. Because the proxy runs
 *      on the same machine, its outbound IP matches the vncticket IP
 *      and Proxmox accepts the connection.
 *
 * Requirement: PROXMOX_WS_PROXY_URL must run on the same server (same
 * outbound IP) as the Laravel application.
 */
final class ProxmoxConsoleService
{
    private readonly string $defaultNode;

    public function __construct(private readonly ProxmoxHttpClient $client)
    {
        $this->defaultNode = (string) config('proxmox.node');
    }

    /**
     * POST /api2/json/nodes/{node}/lxc/{vmid}/termproxy.
     * Creates a terminal shell proxy session (xterm.js compatible).
     *
     * @return array{ticket: string, port: int, upid: string}
     */
    public function getTermProxy(int $vmid, ?string $node = null): array
    {
        return (array) $this->client->post(
            "nodes/{$this->resolveNode($node)}/lxc/{$vmid}/termproxy",
        );
    }

    /**
     * POST /api2/json/nodes/{node}/lxc/{vmid}/vncproxy.
     * Creates a VNC WebSocket proxy session (noVNC compatible).
     *
     * @return array{port: int, ticket: string, cert: string, upid: string}
     */
    public function getVncProxy(int $vmid, ?string $node = null): array
    {
        return (array) $this->client->post(
            "nodes/{$this->resolveNode($node)}/lxc/{$vmid}/vncproxy",
            ['websocket' => 1],
        );
    }

    /**
     * Build the wss:// URL that the browser connects to for a console session.
     *
     * Points to the PROXMOX_WS_PROXY_URL (e.g. proxy.juwalin.cloud), which
     * must forward WebSocket connections to Proxmox from the same server IP
     * that created the termproxy ticket. IP match → Proxmox accepts the
     * vncticket and the console session starts.
     *
     * @param array<string, mixed> $proxy Response from getTermProxy() or getVncProxy()
     */
    public function buildConsoleWsUrl(int $vmid, string $node, array $proxy): string
    {
        $proxyUrl = rtrim((string) config('proxmox.ws_proxy_url'), '/');
        $parts = parse_url($proxyUrl);
        $wsScheme = ($parts['scheme'] ?? 'https') === 'https' ? 'wss' : 'ws';
        $wsHost = $parts['host'] ?? '127.0.0.1';
        $wsPort = isset($parts['port']) ? ":{$parts['port']}" : '';

        return "{$wsScheme}://{$wsHost}{$wsPort}/api2/json/nodes/{$node}/lxc/{$vmid}/vncwebsocket"
            . '?port=' . ($proxy['port'] ?? '')
            . '&vncticket=' . (string) ($proxy['ticket'] ?? '');
            // . '&vncticket=' . rawurlencode((string) ($proxy['ticket'] ?? ''));
    }

    private function resolveNode(?string $node): string
    {
        return $node ?? $this->defaultNode;
    }
}
