<x-layouts.admin title="Detail Pesanan #{{ $order->id }}">

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Main detail --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="card card-body space-y-4">
                <h2 class="font-semibold text-gray-100 border-b border-gray-800 pb-3">Info Pesanan</h2>
                <dl class="grid grid-cols-2 gap-4 text-sm">
                    <div><dt class="text-gray-500">ID Pesanan</dt><dd class="font-mono text-gray-200">#{{ $order->id }}</dd></div>
                    <div><dt class="text-gray-500">Paket</dt><dd class="text-gray-200">{{ $order->package->name ?? 'Custom' }}</dd></div>
                    <div><dt class="text-gray-500">Status</dt><dd><x-ui.badge :color="match($order->status){ 'active'=>'green','pending'=>'yellow','suspended'=>'red',default=>'gray'}">{{ $order->status }}</x-ui.badge></dd></div>
                    <div><dt class="text-gray-500">Pembayaran</dt><dd><x-ui.badge :color="$order->payment_status==='paid'?'green':'yellow'">{{ $order->payment_status }}</x-ui.badge></dd></div>
                    <div><dt class="text-gray-500">Tgl Mulai</dt><dd class="text-gray-200">{{ $order->starts_at ? $order->starts_at->format('d M Y') : '—' }}</dd></div>
                    <div><dt class="text-gray-500">Tgl Berakhir</dt><dd class="text-gray-200">{{ $order->expires_at ? $order->expires_at->format('d M Y') : '—' }}</dd></div>
                    <div><dt class="text-gray-500">Dibuat</dt><dd class="text-gray-200">{{ $order->created_at->format('d M Y H:i') }}</dd></div>
                    <div><dt class="text-gray-500">Container</dt><dd class="font-mono text-gray-200">{{ $order->container ? '#'.$order->container->id : '—' }}</dd></div>
                </dl>
                @if($order->notes)
                <div class="border-t border-gray-800 pt-3">
                    <p class="text-xs text-gray-500 mb-1">Catatan</p>
                    <p class="text-sm text-gray-300">{{ $order->notes }}</p>
                </div>
                @endif
            </div>

            {{-- Payments --}}
            <div class="card overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-800 font-semibold text-gray-100">Riwayat Pembayaran</div>
                <table class="table">
                    <thead><tr><th>Invoice</th><th>Jumlah</th><th>Metode</th><th>Status</th><th>Tgl</th></tr></thead>
                    <tbody>
                        @forelse($order->payments as $p)
                        <tr>
                            <td class="font-mono text-xs text-gray-400"><a href="{{ route('admin.payments.show', $p) }}" class="text-brand-400">{{ $p->invoice_number }}</a></td>
                            <td class="text-gray-200">Rp {{ number_format((float)$p->amount,0,',','.') }}</td>
                            <td class="text-gray-400 text-xs capitalize">{{ $p->payment_method ?? '—' }}</td>
                            <td><x-ui.badge :color="$p->isPaid()?'green':($p->status==='failed'?'red':'yellow')">{{ $p->status }}</x-ui.badge></td>
                            <td class="text-xs text-gray-400">{{ $p->created_at->format('d M Y') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center py-4 text-gray-500">Belum ada pembayaran.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Actions --}}
        <div class="space-y-6">
            <div class="card card-body">
                <h3 class="font-semibold text-gray-100 mb-4">Info User</h3>
                <a href="{{ route('admin.users.show', $order->user_id) }}" class="flex items-center gap-3 hover:opacity-80">
                    <div class="h-10 w-10 rounded-full bg-brand-600 flex items-center justify-center text-white font-bold">{{ strtoupper(substr($order->user->name??'?',0,1)) }}</div>
                    <div>
                        <p class="text-sm font-medium text-gray-200">{{ $order->user->name ?? '—' }}</p>
                        <p class="text-xs text-gray-500">{{ $order->user->email ?? '' }}</p>
                    </div>
                </a>
            </div>

            <div class="card card-body space-y-3">
                <h3 class="font-semibold text-gray-100 mb-2">Ubah Status</h3>
                <form method="POST" action="{{ route('admin.orders.status', $order) }}">
                    @csrf @method('PATCH')
                    <x-form.select name="status">
                        @foreach(['pending','active','suspended','cancelled'] as $s)
                            <option value="{{ $s }}" {{ $order->status === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                        @endforeach
                    </x-form.select>
                    <button type="submit" class="btn-primary btn-sm mt-3 w-full">Update Status</button>
                </form>
            </div>

            <div class="card card-body">
                <form method="POST" action="{{ route('admin.orders.destroy', $order) }}" data-confirm-form="Hapus pesanan #{{ $order->id }}? Tindakan ini tidak dapat dibatalkan.">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn-danger btn-sm w-full">Hapus Pesanan</button>
                </form>
            </div>

            <a href="{{ route('admin.orders.index') }}" class="btn-secondary btn-sm w-full block text-center">← Kembali</a>
        </div>
    </div>

</x-layouts.admin>
