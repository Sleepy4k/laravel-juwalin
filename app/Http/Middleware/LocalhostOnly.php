<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Reject any request that did not originate from the local machine.
 * Used to protect internal API endpoints (e.g. the VNC token resolver).
 */
class LocalhostOnly
{
    public function handle(Request $request, Closure $next): Response
    {
        $ip = $request->server('REMOTE_ADDR') ?? '';

        abort_unless(in_array($ip, ['127.0.0.1', '::1'], true), 403);

        return $next($request);
    }
}
