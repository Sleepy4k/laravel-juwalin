<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Proxmox VE API Connection
    |--------------------------------------------------------------------------
    |
    | Base URL must include HTTPS and the port, e.g. https://192.168.1.1:8006
    | Token auth uses the format "USER@REALM!TOKENID" + a UUID secret key.
    |
    */

    'url' => rtrim((string) env('PROXMOX_URL', 'https://127.0.0.1:8006'), '/'),

    /*
    |--------------------------------------------------------------------------
    | Console URL (public-facing Proxmox URL for browsers)
    |--------------------------------------------------------------------------
    |
    | URL that end-user browsers will use to open WebSocket console sessions
    | directly against Proxmox (port 8006). This must be reachable from the
    | user's browser. If left empty, falls back to the api `url` above.
    |
    | In most single-server VPS setups the PROXMOX_URL is already public,
    | so no extra env var is needed. Set PROXMOX_CONSOLE_URL only when the
    | internal API URL differs from the public-facing Proxmox address.
    |
    | Example: https://proxmox.yourdomain.com:8006
    |
    */
    'console_url' => rtrim((string) env('PROXMOX_CONSOLE_URL', env('PROXMOX_URL', 'https://127.0.0.1:8006')), '/'),

    /*
    |--------------------------------------------------------------------------
    | WebSocket Proxy URL
    |--------------------------------------------------------------------------
    |
    | The URL that browser WebSocket connections go to for console sessions.
    | This server must be able to forward the WebSocket to Proxmox from the
    | SAME IP that created the termproxy ticket (the app server's IP).
    |
    | Typically this is a proxy running on the same machine as the Laravel app
    | (e.g. nginx/haproxy at proxy.yourdomain.com) configured to forward
    | /api2/ traffic to the Proxmox API server.
    |
    | Example: https://proxy.juwalin.cloud
    |
    */

    'ws_proxy_url' => rtrim((string) env('PROXMOX_WS_PROXY_URL', env('PROXMOX_CONSOLE_URL', env('PROXMOX_URL', 'https://127.0.0.1:8006'))), '/'),

    'token_id' => env('PROXMOX_TOKEN_ID'),   // e.g. root@pam!laravel

    'secret' => env('PROXMOX_SECRET_KEY'),   // UUID generated in Datacenter > API Tokens

    /*
    |--------------------------------------------------------------------------
    | Console Auth (User/Password)
    |--------------------------------------------------------------------------
    |
    | Credentials used exclusively for the termproxy/vncproxy login flow.
    | API Token auth cannot be used for termproxy because Proxmox embeds the
    | full token identity (e.g. "KWU@pve!laravel") as the username, which its
    | own WebSocket handshake rejects. A real user account (e.g. "KWU@pve")
    | with password must be used here instead.
    |
    */

    'console_username' => env('PROXMOX_CONSOLE_USERNAME'),  // e.g. KWU@pve

    'console_password' => env('PROXMOX_CONSOLE_PASSWORD'),  // plain-text password for that user

    'verify_tls' => (bool) env('PROXMOX_VERIFY_TLS', false), // set true in production with valid cert

    /*
    |--------------------------------------------------------------------------
    | Default Node & Template
    |--------------------------------------------------------------------------
    */

    'node' => env('PROXMOX_NODE', 'pve'),

    'template_vmid' => (int) env('PROXMOX_TEMPLATE_VMID', 9000), // CT template VMID to clone from

    'vmid_start' => (int) env('PROXMOX_VMID_START', 1000), // starting VMID range for new containers

    /*
    |--------------------------------------------------------------------------
    | Resource Limits (Guard against overselling)
    |--------------------------------------------------------------------------
    */

    'max_cores_per_container' => (int) env('PROXMOX_MAX_CORES', 4),

    'max_memory_mb_per_container' => (int) env('PROXMOX_MAX_MEMORY_MB', 4096),

    'max_disk_gb_per_container' => (int) env('PROXMOX_MAX_DISK_GB', 50),

    /*
    |--------------------------------------------------------------------------
    | HTTP Client Defaults
    |--------------------------------------------------------------------------
    */

    'timeout' => (int) env('PROXMOX_TIMEOUT', 30),
];
