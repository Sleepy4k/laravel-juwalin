<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Container Siap Digunakan</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f4f4f8; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .header { background: #059669; padding: 32px; text-align: center; }
        .header h1 { color: white; margin: 0; font-size: 24px; }
        .body { padding: 32px; }
        .detail-row { display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #e5e7eb; }
        .detail-label { color: #6b7280; font-size: 14px; }
        .detail-value { font-weight: 600; color: #111827; font-family: monospace; }
        .footer { background: #f9fafb; padding: 20px 32px; text-align: center; color: #9ca3af; font-size: 13px; }
        .btn { display: inline-block; background: #059669; color: white; padding: 12px 24px; border-radius: 6px; text-decoration: none; font-weight: 600; margin-top: 20px; }
        .spec-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; margin: 20px 0; }
        .spec-card { background: #f0fdf4; border-radius: 6px; padding: 12px; text-align: center; }
        .spec-label { font-size: 11px; color: #6b7280; text-transform: uppercase; }
        .spec-value { font-size: 18px; font-weight: 700; color: #065f46; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🚀 Container Siap Digunakan!</h1>
        </div>
        <div class="body">
            <p>Halo <strong>{{ $container->user->name }}</strong>,</p>
            <p>Container LXC Anda telah berhasil diprovisioning dan siap digunakan. Container saat ini dalam keadaan <strong>berhenti</strong> — silakan masuk ke panel dan klik <strong>Start</strong> untuk menyalakannya.</p>

            <div class="spec-grid">
                <div class="spec-card">
                    <div class="spec-value">{{ $container->cores }}</div>
                    <div class="spec-label">vCPU</div>
                </div>
                <div class="spec-card">
                    <div class="spec-value">{{ $container->memory_mb }}</div>
                    <div class="spec-label">RAM (MB)</div>
                </div>
                <div class="spec-card">
                    <div class="spec-value">{{ $container->disk_gb }}</div>
                    <div class="spec-label">Disk (GB)</div>
                </div>
            </div>

            <div class="detail-row">
                <span class="detail-label">Hostname</span>
                <span class="detail-value">{{ $container->hostname }}</span>
            </div>
            @if($container->ip_address)
            <div class="detail-row">
                <span class="detail-label">IP Address</span>
                <span class="detail-value">{{ $container->ip_address }}</span>
            </div>
            @endif
            <div class="detail-row">
                <span class="detail-label">VMID</span>
                <span class="detail-value">{{ $container->vmid }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Node</span>
                <span class="detail-value">{{ $container->node }}</span>
            </div>

            <a href="{{ url('/portal/containers/'.$container->id) }}" class="btn">Kelola Container</a>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}</p>
        </div>
    </div>
</body>
</html>
