<x-layouts.admin title="Detail Pembayaran">

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="card card-body space-y-4">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs text-gray-500">Invoice Number</p>
                        <h2 class="text-lg font-bold font-mono text-gray-100">{{ $payment->invoice_number }}</h2>
                    </div>
                    <x-ui.badge :color="$payment->status==='paid'?'green':($payment->status==='failed'?'red':'yellow')" class="text-sm">{{ $payment->status }}</x-ui.badge>
                </div>

                <dl class="grid grid-cols-2 gap-4 text-sm border-t border-gray-800 pt-4">
                    <div><dt class="text-gray-500">Jumlah</dt><dd class="text-xl font-bold text-gray-100">Rp {{ number_format((float)$payment->amount,0,',','.') }}</dd></div>
                    <div><dt class="text-gray-500">Gateway</dt><dd class="text-gray-200 capitalize">{{ $payment->gateway ?? 'Manual' }}</dd></div>
                    <div><dt class="text-gray-500">Metode</dt><dd class="text-gray-200 capitalize">{{ $payment->payment_method ?? '—' }}</dd></div>
                    <div><dt class="text-gray-500">Bukti Transfer</dt>
                        <dd>
                            @if($payment->proof_file)
                                <a href="{{ Storage::url($payment->proof_file) }}" target="_blank" class="text-brand-400 hover:underline text-xs">Lihat Bukti</a>
                            @else
                                <span class="text-gray-500">—</span>
                            @endif
                        </dd>
                    </div>
                    <div><dt class="text-gray-500">Dibuat</dt><dd class="text-gray-200">{{ $payment->created_at->format('d M Y H:i') }}</dd></div>
                    <div><dt class="text-gray-500">Dibayar</dt><dd class="text-gray-200">{{ $payment->paid_at ? $payment->paid_at->format('d M Y H:i') : '—' }}</dd></div>
                    <div><dt class="text-gray-500">Kadaluarsa</dt><dd class="text-gray-200">{{ $payment->expires_at ? $payment->expires_at->format('d M Y H:i') : '—' }}</dd></div>
                    <div><dt class="text-gray-500">Pesanan</dt><dd><a href="{{ route('admin.orders.show', $payment->order_id) }}" class="text-brand-400 hover:underline">#{{ $payment->order_id }}</a></dd></div>
                </dl>

                @if($payment->notes)
                <div class="border-t border-gray-800 pt-3">
                    <p class="text-xs text-gray-500 mb-1">Catatan</p>
                    <p class="text-sm text-gray-300">{{ $payment->notes }}</p>
                </div>
                @endif

                @if($payment->gateway_payload)
                <div class="border-t border-gray-800 pt-3">
                    <p class="text-xs text-gray-500 mb-2">Gateway Payload</p>
                    <pre class="text-xs text-gray-400 bg-gray-900 rounded p-3 overflow-auto max-h-40">{{ json_encode($payment->gateway_payload, JSON_PRETTY_PRINT) }}</pre>
                </div>
                @endif
            </div>
        </div>

        <div class="space-y-4">
            <div class="card card-body">
                <h3 class="font-semibold text-gray-100 mb-2">User</h3>
                <a href="{{ route('admin.users.show', $payment->user_id) }}" class="flex items-center gap-3 hover:opacity-80">
                    <div class="h-10 w-10 rounded-full bg-brand-600 flex items-center justify-center text-white font-bold">{{ strtoupper(substr($payment->user->name??'?',0,1)) }}</div>
                    <div>
                        <p class="text-sm font-medium text-gray-200">{{ $payment->user->name ?? '—' }}</p>
                        <p class="text-xs text-gray-500">{{ $payment->user->email ?? '' }}</p>
                    </div>
                </a>
            </div>

            @if($payment->isPending())
            <div class="card card-body space-y-2">
                <h3 class="font-semibold text-gray-100 mb-3">Tindakan</h3>
                <form method="POST" action="{{ route('admin.payments.confirm', $payment) }}">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn-primary btn-sm w-full">✓ Konfirmasi Pembayaran</button>
                </form>
                <form method="POST" action="{{ route('admin.payments.reject', $payment) }}">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn-danger btn-sm w-full">✕ Tolak Pembayaran</button>
                </form>
            </div>
            @endif

            <a href="{{ route('admin.payments.index') }}" class="btn-secondary btn-sm w-full block text-center">← Kembali</a>
        </div>
    </div>

</x-layouts.admin>
