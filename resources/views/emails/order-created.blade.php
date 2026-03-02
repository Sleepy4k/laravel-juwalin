<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Baru Dibuat</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f4f4f8; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .header { background: #2563eb; padding: 32px; text-align: center; }
        .header h1 { color: white; margin: 0; font-size: 24px; }
        .body { padding: 32px; }
        .detail-row { display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #e5e7eb; }
        .detail-label { color: #6b7280; font-size: 14px; }
        .detail-value { font-weight: 600; color: #111827; }
        .footer { background: #f9fafb; padding: 20px 32px; text-align: center; color: #9ca3af; font-size: 13px; }
        .btn { display: inline-block; background: #2563eb; color: white; padding: 12px 24px; border-radius: 6px; text-decoration: none; font-weight: 600; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Pesanan Baru Dibuat</h1>
        </div>
        <div class="body">
            <p>Halo <strong>{{ $order->user->name }}</strong>,</p>
            <p>Pesanan Anda telah berhasil dibuat. Silakan lakukan pembayaran untuk mengaktifkan layanan.</p>

            <div class="detail-row">
                <span class="detail-label">Order ID</span>
                <span class="detail-value">#{{ $order->id }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Paket</span>
                <span class="detail-value">{{ $order->package->name ?? '-' }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Total</span>
                <span class="detail-value">Rp {{ number_format((float)$order->price, 0, ',', '.') }}</span>
            </div>

            <a href="{{ url('/portal/orders/'.$order->id) }}" class="btn">Lihat Detail Pesanan</a>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}</p>
        </div>
    </div>
</body>
</html>
