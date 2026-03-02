<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

/**
 * Internal-only endpoint used by the VNC WebSocket proxy (vnc-proxy.mjs)
 * to resolve a one-time token into the actual Proxmox vncwebsocket URL.
 *
 * Protected by the `localhost.only` middleware — only reachable from 127.0.0.1.
 */
class VncTokenController extends Controller
{
    public function show(string $token): JsonResponse
    {
        $url = Cache::get("vnc_token:{$token}");

        if ($url === null) {
            return response()->json(['error' => 'Token not found or expired'], 404);
        }

        return response()->json(['url' => $url]);
    }
}
