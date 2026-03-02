<x-layouts.portal title="Detail Pesanan #{{ $order->id }}">

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="card card-body space-y-4">
                <h2 class="font-semibold text-gray-100 border-b border-gray-800 pb-3">Detail Pesanan</h2>
                <dl class="grid grid-cols-2 gap-4 text-sm">
                    <div><dt class="text-gray-500">ID Pesanan</dt><dd class="font-mono text-gray-200">#{{ $order->id }}</dd></div>
                    <div><dt class="text-gray-500">Paket</dt><dd class="text-gray-200">{{ $order->package->name ?? 'Custom' }}</dd></div>
                    <div>
                        <dt class="text-gray-500">Status</dt>
                        <dd><x-ui.badge :color="match($order->status){ 'active'=>'green','pending'=>'yellow','suspended'=>'red',default=>'gray'}">{{ $order->status }}</x-ui.badge></dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Pembayaran</dt>
                        <dd><x-ui.badge :color="$order->payment_status==='paid'?'green':'yellow'">{{ $order->payment_status }}</x-ui.badge></dd>
                    </div>
                    <div><dt class="text-gray-500">Tgl Dibuat</dt><dd class="text-gray-200">{{ $order->created_at->format('d M Y H:i') }}</dd></div>
                    <div><dt class="text-gray-500">Aktif Sampai</dt><dd class="text-gray-200">{{ $order->expires_at ? $order->expires_at->format('d M Y') : '—' }}</dd></div>
                    @if($order->container)
                    <div class="col-span-2"><dt class="text-gray-500">Container</dt>
                        <dd><a href="{{ route('portal.containers.show', $order->container) }}" class="text-brand-400 font-mono hover:underline">{{ $order->container->hostname }}</a></dd>
                    </div>
                    @endif
                </dl>

                @if($order->package)
                <div class="border-t border-gray-800 pt-4">
                    <p class="text-xs text-gray-500 mb-3">Spesifikasi Paket</p>
                    <div class="grid grid-cols-3 gap-3 text-center">
                        <div class="bg-gray-900 rounded-lg py-3"><p class="text-xl font-bold text-brand-400">{{ $order->package->cores }}</p><p class="text-xs text-gray-500">vCPU</p></div>
                        <div class="bg-gray-900 rounded-lg py-3"><p class="text-xl font-bold text-brand-400">{{ round($order->package->memory_mb/1024,1) }} GB</p><p class="text-xs text-gray-500">RAM</p></div>
                        <div class="bg-gray-900 rounded-lg py-3"><p class="text-xl font-bold text-brand-400">{{ $order->package->disk_gb }} GB</p><p class="text-xs text-gray-500">Disk</p></div>
                    </div>
                </div>
                @endif
            </div>

            {{-- Payments history --}}
            <div class="card overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-800 font-semibold text-gray-100">Riwayat Pembayaran</div>
                <table class="table">
                    <thead><tr><th>Invoice</th><th>Jumlah</th><th>Status</th><th>Tgl</th><th></th></tr></thead>
                    <tbody>
                        @forelse($order->payments as $payment)
                        <tr>
                            <td class="font-mono text-xs text-gray-400">{{ $payment->invoice_number }}</td>
                            <td class="text-gray-200">Rp {{ number_format((float)$payment->amount,0,',','.') }}</td>
                            <td><x-ui.badge :color="$payment->isPaid()?'green':($payment->status==='failed'?'red':'yellow')">{{ $payment->status }}</x-ui.badge></td>
                            <td class="text-xs text-gray-400">{{ $payment->created_at->format('d M Y') }}</td>
                            <td>
                                <a href="{{ route('portal.billing.show', $payment) }}" class="text-xs text-brand-400">Detail</a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center py-4 text-gray-500">Belum ada pembayaran.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="space-y-4">
            @if($order->isPending())
            <div class="card card-body">
                <p class="text-sm text-yellow-300 mb-3">⏳ Menunggu pembayaran untuk mengaktifkan layanan.</p>
                <a href="{{ route('portal.billing.index') }}" class="btn-primary btn-sm w-full block text-center">Bayar Sekarang</a>
            </div>
            @endif

            @if(!$order->isPending())
            <div class="card card-body">
                <form method="POST" action="{{ route('portal.orders.destroy', $order) }}" data-confirm-form="Batalkan pesanan ini? Container terkait akan dihapus.">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn-danger btn-sm w-full">Batalkan Pesanan</button>
                </form>
            </div>
            @endif

            <a href="{{ route('portal.orders.index') }}" class="btn-secondary btn-sm w-full block text-center">← Kembali</a>
        </div>
    </div>

</x-layouts.portal>
