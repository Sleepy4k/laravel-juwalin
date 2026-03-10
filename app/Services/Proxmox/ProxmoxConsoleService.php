<?php

namespace App\Services\Proxmox;

use RuntimeException;

/**
 * Proxmox VE console session operations.
 *
 * Architecture (server-side termproxy + WebSocket proxy):
 *   1. The Laravel server logs in to Proxmox with username + password to obtain
 *      a PVEAuthCookie ticket and CSRF token (API Token auth cannot be used here
 *      because Proxmox embeds the full token identity in the username, which the
 *      termproxy WebSocket handshake rejects).
 *   2. Using that session, the server POSTs to termproxy and receives a vncticket
 *      and port bound to the SERVER's outbound IP.
 *   3. The server returns {wsUrl, ticket, username} to the browser, where wsUrl
 *      points to PROXMOX_WS_PROXY_URL (e.g. proxy.juwalin.cloud) with query params
 *      vmid, node, port, ticket, and type understood by the proxy.
 *   4. The browser WebSocket connects to the proxy; on open it sends
 *      "username:ticket\n" (Proxmox termproxy handshake), then terminal size.
 *   5. The proxy forwards the WebSocket to Proxmox. Because the proxy runs on the
 *      same machine, its outbound IP matches the vncticket IP and Proxmox accepts.
 *
 * Required env vars: PROXMOX_CONSOLE_USERNAME, PROXMOX_CONSOLE_PASSWORD,
 *                    PROXMOX_WS_PROXY_URL (all on the same server IP).
 */
final class ProxmoxConsoleService
{
    private readonly string $defaultNode;

    public function __construct(private readonly ProxmoxHttpClient $client)
    {
        $this->defaultNode = (string) config('proxmox.node');
    }

    /**
     * Login to Proxmox with user/password, then POST to termproxy.
     *
     * API Token auth cannot be used here: Proxmox embeds the full token identity
     * (e.g. "KWU@pve!laravel") as the session username, and its termproxy WebSocket
     * handshake rejects any username containing "!". Using a real user account
     * (PROXMOX_CONSOLE_USERNAME / PROXMOX_CONSOLE_PASSWORD) produces a clean
     * username (e.g. "KWU@pve") that the handshake accepts.
     *
     * @return array{ticket: string, port: int, upid: string, username: string}
     *
     * @throws RuntimeException if credentials are missing, login fails, or termproxy fails
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
     * Points to the PROXMOX_WS_PROXY_URL (e.g. proxy.juwalin.cloud) with query
     * params the proxy uses to construct the Proxmox vncwebsocket URL internally.
     * The proxy must run on the same server IP as Laravel so the vncticket IP
     * check passes.
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
            // . '&vncticket=' . (string) ($proxy['ticket'] ?? '');
            . '&vncticket=' . rawurlencode((string) ($proxy['ticket'] ?? ''));
    }

    private function resolveNode(?string $node): string
    {
        return $node ?? $this->defaultNode;
    }
}
