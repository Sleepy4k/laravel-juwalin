<x-layouts.admin title="Admin Dashboard">

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 mb-8">
        <x-ui.stat label="Total User" :value="number_format($stats['users'])" color="blue"/>
        <x-ui.stat label="Total Order" :value="number_format($stats['orders'])" color="yellow"/>
        <x-ui.stat label="Container" :value="number_format($stats['containers'])" color="green"/>
        <x-ui.stat label="Container Aktif" :value="number_format($stats['running_containers'])" color="green"/>
        <x-ui.stat label="Pembayaran Pending" :value="number_format($stats['pending_payments'])" color="red"/>
        <x-ui.stat label="Total Revenue" :value="'Rp '.number_format((float)$stats['revenue'],0,',','.')" color="blue"/>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Recent orders --}}
        <div class="card">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-800">
                <h2 class="font-semibold text-gray-100">Pesanan Terbaru</h2>
                <a href="{{ route('admin.orders.index') }}" class="text-xs text-brand-400 hover:text-brand-300">Lihat semua</a>
            </div>
            <div class="divide-y divide-gray-800">
                @foreach($recentOrders as $order)
                <div class="flex items-center gap-3 px-6 py-3">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-200 truncate">{{ $order->user->name ?? '—' }}</p>
                        <p class="text-xs text-gray-500">{{ $order->package->name ?? 'Custom' }} • {{ $order->created_at->diffForHumans() }}</p>
                    </div>
                    <x-ui.badge :color="$order->isPaid() ? 'green' : 'yellow'">{{ $order->payment_status }}</x-ui.badge>
                    <a href="{{ route('admin.orders.show', $order) }}" class="text-xs text-brand-400">Detail</a>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Recent payments --}}
        <div class="card">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-800">
                <h2 class="font-semibold text-gray-100">Pembayaran Terbaru</h2>
                <a href="{{ route('admin.payments.index') }}" class="text-xs text-brand-400 hover:text-brand-300">Lihat semua</a>
            </div>
            <div class="divide-y divide-gray-800">
                @foreach($recentPayments as $payment)
                <div class="flex items-center gap-3 px-6 py-3">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-200 truncate">{{ $payment->user->name ?? '—' }}</p>
                        <p class="text-xs text-gray-500">{{ $payment->invoice_number }} • {{ $payment->created_at->diffForHumans() }}</p>
                    </div>
                    <span class="text-sm font-medium text-gray-200">Rp {{ number_format((float)$payment->amount,0,',','.') }}</span>
                    <x-ui.badge :color="$payment->isPaid() ? 'green' : ($payment->status === 'failed' ? 'red' : 'yellow')">{{ $payment->status }}</x-ui.badge>
                </div>
                @endforeach
            </div>
        </div>
    </div>

</x-layouts.admin>
