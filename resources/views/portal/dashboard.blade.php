<x-layouts.portal title="Dashboard">

    {{-- Stats row --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <x-ui.stat label="Container" :value="$stats['containers']" color="blue"
            icon='<svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2"/></svg>'/>
        <x-ui.stat label="Container Aktif" :value="$stats['active']" color="green"
            icon='<svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'/>
        <x-ui.stat label="Total Pesanan" :value="$stats['orders']" color="yellow"
            icon='<svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>'/>
        <x-ui.stat label="Pesanan Pending" :value="$stats['pending_orders']" color="red"
            icon='<svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'/>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Recent containers --}}
        <div class="card">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-800">
                <h2 class="font-semibold text-gray-100">Container Terbaru</h2>
                <a href="{{ route('portal.containers.index') }}" class="text-xs text-brand-400 hover:text-brand-300">Lihat semua</a>
            </div>
            <div class="divide-y divide-gray-800">
                @forelse($recentContainers as $ct)
                <a href="{{ route('portal.containers.show', $ct) }}" class="flex items-center gap-3 px-6 py-3 hover:bg-gray-800/30 transition-colors">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-gray-800 text-gray-400 text-xs font-mono shrink-0">
                        {{ $ct->vmid ?? '—' }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-200 truncate">{{ $ct->hostname }}</p>
                        <p class="text-xs text-gray-500">{{ $ct->ip_address }}</p>
                    </div>
                    @php
                        $statusColor = match($ct->status) {
                            'running' => 'green',
                            'stopped' => 'red',
                            'provisioning' => 'yellow',
                            default => 'gray',
                        };
                    @endphp
                    <x-ui.badge :color="$statusColor">{{ $ct->status }}</x-ui.badge>
                </a>
                @empty
                <div class="px-6 py-8 text-center text-sm text-gray-500">
                    Belum ada container.
                    <a href="{{ route('portal.orders.create') }}" class="text-brand-400 hover:text-brand-300 ml-1">Order sekarang →</a>
                </div>
                @endforelse
            </div>
        </div>

        {{-- Recent orders --}}
        <div class="card">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-800">
                <h2 class="font-semibold text-gray-100">Pesanan Terbaru</h2>
                <a href="{{ route('portal.orders.index') }}" class="text-xs text-brand-400 hover:text-brand-300">Lihat semua</a>
            </div>
            <div class="divide-y divide-gray-800">
                @forelse($recentOrders as $order)
                <a href="{{ route('portal.orders.show', $order) }}" class="flex items-center gap-3 px-6 py-3 hover:bg-gray-800/30 transition-colors">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-200">{{ $order->package->name ?? 'Custom' }}</p>
                        <p class="text-xs text-gray-500">{{ $order->created_at->diffForHumans() }}</p>
                    </div>
                    @php
                        $payColor = $order->isPaid() ? 'green' : 'yellow';
                    @endphp
                    <x-ui.badge :color="$payColor">{{ $order->payment_status }}</x-ui.badge>
                </a>
                @empty
                <div class="px-6 py-8 text-center text-sm text-gray-500">Belum ada pesanan.</div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Quick action --}}
    <div class="mt-6">
        <a href="{{ route('portal.orders.create') }}" class="btn-primary btn-lg">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Order Container Baru
        </a>
    </div>

</x-layouts.portal>
