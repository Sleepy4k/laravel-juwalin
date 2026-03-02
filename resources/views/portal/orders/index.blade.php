<x-layouts.portal title="Pesanan Saya">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-lg font-semibold text-gray-100">Semua Pesanan</h2>
            <p class="text-sm text-gray-400">Kelola pesanan hosting Anda</p>
        </div>
        <a href="{{ route('portal.orders.create') }}" class="btn-primary btn-sm">+ Buat Pesanan</a>
    </div>

    @if($orders->isEmpty())
        <x-ui.empty-state title="Belum ada pesanan" description="Mulai dengan memesan paket hosting pertama Anda.">
            <x-slot:action>
                <a href="{{ route('portal.orders.create') }}" class="btn-primary">Order Sekarang</a>
            </x-slot:action>
        </x-ui.empty-state>
    @else
    <div class="card overflow-hidden">
        <table class="table">
            <thead>
                <tr>
                    <th>Paket</th>
                    <th>Spesifikasi</th>
                    <th>Harga</th>
                    <th>Status</th>
                    <th>Pembayaran</th>
                    <th>Tanggal</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($orders as $order)
                <tr>
                    <td class="font-medium text-gray-200">{{ $order->package->name ?? 'Custom' }}</td>
                    <td class="font-mono text-xs text-gray-400">{{ $order->cores }}C / {{ round($order->memory_mb/1024, 0) }}GB / {{ $order->disk_gb }}GB</td>
                    <td>Rp {{ number_format((float)$order->price, 0, ',', '.') }}</td>
                    <td>
                        <x-ui.badge :color="match($order->status) { 'active' => 'green', 'cancelled' => 'red', 'pending' => 'yellow', default => 'gray' }">
                            {{ $order->status }}
                        </x-ui.badge>
                    </td>
                    <td>
                        <x-ui.badge :color="$order->isPaid() ? 'green' : 'yellow'">{{ $order->payment_status }}</x-ui.badge>
                    </td>
                    <td class="text-gray-400 text-xs">{{ $order->created_at->format('d M Y') }}</td>
                    <td>
                        <a href="{{ route('portal.orders.show', $order) }}" class="text-xs text-brand-400 hover:text-brand-300">Detail</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $orders->links() }}
    </div>
    @endif

</x-layouts.portal>
