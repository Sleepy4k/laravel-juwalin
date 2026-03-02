<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran Dikonfirmasi</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f4f4f8; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .header { background: #6d28d9; padding: 32px; text-align: center; }
        .header h1 { color: white; margin: 0; font-size: 24px; }
        .body { padding: 32px; }
        .detail-row { display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #e5e7eb; }
        .detail-label { color: #6b7280; font-size: 14px; }
        .detail-value { font-weight: 600; color: #111827; }
        .badge { display: inline-block; background: #d1fae5; color: #065f46; padding: 4px 12px; border-radius: 20px; font-size: 13px; font-weight: 600; }
        .footer { background: #f9fafb; padding: 20px 32px; text-align: center; color: #9ca3af; font-size: 13px; }
        .btn { display: inline-block; background: #6d28d9; color: white; padding: 12px 24px; border-radius: 6px; text-decoration: none; font-weight: 600; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>✓ Pembayaran Dikonfirmasi</h1>
        </div>
        <div class="body">
            <p>Halo <strong>{{ $payment->user->name }}</strong>,</p>
            <p>Pembayaran Anda telah berhasil dikonfirmasi. Container sedang dipersiapkan dan akan segera aktif.</p>

            <div class="detail-row">
                <span class="detail-label">Nomor Invoice</span>
                <span class="detail-value">{{ $payment->invoice_number }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Jumlah</span>
                <span class="detail-value">{{ $payment->formatted_amount }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Status</span>
                <span class="detail-value"><span class="badge">Lunas</span></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Tanggal</span>
                <span class="detail-value">{{ $payment->paid_at?->format('d M Y, H:i') ?? now()->format('d M Y, H:i') }} WIB</span>
            </div>

            <p style="margin-top: 24px;">Container Anda akan siap dalam beberapa menit. Anda akan menerima notifikasi lanjutan ketika container aktif.</p>

            <a href="{{ url('/portal/billing/'.$payment->id) }}" class="btn">Lihat Detail Invoice</a>
        </div>
        <div class="footer">
            <p>Email ini dikirim secara otomatis. Jangan membalas email ini.</p>
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}</p>
        </div>
    </div>
</body>
</html>
