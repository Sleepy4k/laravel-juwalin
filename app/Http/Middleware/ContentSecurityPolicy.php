<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ContentSecurityPolicy
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Derive wss:// origin from the WebSocket proxy URL for the terminal.
        $proxyParts = parse_url(rtrim((string) config('proxmox.ws_proxy_url'), '/'));
        $wsScheme = ($proxyParts['scheme'] ?? 'https') === 'https' ? 'wss' : 'ws';
        $wsHost = $proxyParts['host'] ?? '127.0.0.1';
        $wsPort = isset($proxyParts['port']) ? ":{$proxyParts['port']}" : '';
        $wsOrigin = "{$wsScheme}://{$wsHost}{$wsPort}";

        $csp = implode('; ', [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://fonts.bunny.net https://cdn.jsdelivr.net https://static.cloudflareinsights.com",
            "style-src 'self' 'unsafe-inline' https://fonts.bunny.net https://cdn.jsdelivr.net",
            "font-src 'self' https://fonts.bunny.net data:",
            "img-src 'self' data: blob: https:",
            "connect-src 'self' http://adip.store https://adip.store https://app.pakasir.com https://cdn.jsdelivr.net {$wsOrigin}",
            "frame-ancestors 'none'",
            "base-uri 'self'",
            "form-action 'self' https://app.pakasir.com",
            "worker-src 'self' blob:",
        ]);

        $response->headers->set('Content-Security-Policy', $csp);
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), payment=()');

        return $response;
    }
}
