<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $payment->invoice_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #fff; color: #1a1a1a; padding: 40px; max-width: 700px; margin: 0 auto; }
        .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 40px; }
        .logo { font-size: 24px; font-weight: 800; color: #6d28d9; }
        .invoice-meta { text-align: right; }
        .invoice-meta h2 { font-size: 28px; font-weight: 700; color: #1a1a1a; }
        .invoice-meta p { color: #666; font-size: 14px; }
        .badge { display: inline-block; padding: 4px 12px; border-radius: 9999px; font-size: 12px; font-weight: 600; background: #d1fae5; color: #065f46; }
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 32px; }
        .section { padding: 20px; border: 1px solid #e5e7eb; border-radius: 8px; }
        .section h3 { font-size: 11px; text-transform: uppercase; letter-spacing: 0.05em; color: #9ca3af; margin-bottom: 12px; }
        .section p { font-size: 14px; color: #374151; line-height: 1.6; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
        th { background: #f3f4f6; text-align: left; padding: 10px 16px; font-size: 12px; text-transform: uppercase; color: #6b7280; letter-spacing: 0.05em; }
        td { padding: 12px 16px; border-bottom: 1px solid #f3f4f6; font-size: 14px; }
        .total-row td { font-size: 16px; font-weight: 700; border-top: 2px solid #e5e7eb; border-bottom: none; }
        .text-right { text-align: right; }
        .footer { text-align: center; color: #9ca3af; font-size: 12px; margin-top: 48px; padding-top: 24px; border-top: 1px solid #e5e7eb; }
        @media print {
            body { padding: 20px; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>

    {{-- Print button --}}
    <div class="no-print" style="margin-bottom:24px;text-align:right;">
        <button onclick="window.print()" style="background:#6d28d9;color:#fff;padding:8px 20px;border:none;border-radius:8px;font-weight:600;cursor:pointer;">🖨 Cetak Invoice</button>
        <a href="{{ route('portal.billing.show', $payment) }}" style="margin-left:8px;background:#f3f4f6;color:#374151;padding:8px 20px;border:none;border-radius:8px;font-weight:600;cursor:pointer;text-decoration:none;">← Kembali</a>
    </div>

    <div class="header">
        <div>
            <div class="logo">{{ $siteSettings->app_name ?? config('app.name') }}</div>
            <p style="color:#666;font-size:13px;margin-top:4px;">{{ $siteSettings->contact_address ?? '' }}</p>
            <p style="color:#666;font-size:13px;">{{ $siteSettings->contact_email ?? '' }}</p>
        </div>
        <div class="invoice-meta">
            <h2>INVOICE</h2>
            <p class="font-mono">{{ $payment->invoice_number }}</p>
            <p style="margin-top:8px;">{{ $payment->created_at->format('d M Y') }}</p>
            <div style="margin-top:8px;"><span class="badge">LUNAS</span></div>
        </div>
    </div>

    <div class="grid-2">
        <div class="section">
            <h3>Kepada:</h3>
            <p><strong>{{ $payment->user->name }}</strong></p>
            <p>{{ $payment->user->email }}</p>
        </div>
        <div class="section">
            <h3>Detail Pembayaran:</h3>
            <p>Metode: <strong>{{ ucfirst($payment->payment_method ?? 'Transfer') }}</strong></p>
            <p>Tanggal Bayar: <strong>{{ $payment->paid_at?->format('d M Y') ?? '—' }}</strong></p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Deskripsi</th>
                <th>Periode</th>
                <th class="text-right">Harga</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <strong>{{ $payment->order->package->name ?? 'Layanan Hosting' }}</strong><br>
                    <span style="font-size:12px;color:#6b7280;">
                        {{ $payment->order->package ? $payment->order->package->cores.'C / '.round($payment->order->package->memory_mb/1024,1).'GB RAM / '.$payment->order->package->disk_gb.'GB Disk' : '' }}
                    </span>
                </td>
                <td style="color:#6b7280;font-size:13px;">
                    {{ $payment->order->starts_at ? $payment->order->starts_at->format('d M Y') : '—' }}
                    —
                    {{ $payment->order->expires_at ? $payment->order->expires_at->format('d M Y') : '—' }}
                </td>
                <td class="text-right">Rp {{ number_format((float)$payment->amount,0,',','.') }}</td>
            </tr>
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="2">Total</td>
                <td class="text-right">Rp {{ number_format((float)$payment->amount,0,',','.') }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <p>Terima kasih telah menggunakan layanan <strong>{{ $siteSettings->app_name ?? config('app.name') }}</strong>.</p>
        <p style="margin-top:4px;">Invoice ini dicetak secara otomatis dan sah tanpa tanda tangan.</p>
    </div>

</body>
</html>
