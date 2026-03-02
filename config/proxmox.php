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

    'token_id' => env('PROXMOX_TOKEN_ID'),   // e.g. root@pam!laravel

    'secret' => env('PROXMOX_SECRET_KEY'),   // UUID generated in Datacenter > API Tokens

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
