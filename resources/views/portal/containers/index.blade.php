<x-layouts.portal :title="'Container Saya'">
    @push('head')
        @vite('resources/js/pages/portal/containers.js')
    @endpush

    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-lg font-semibold text-gray-100">Container Saya</h2>
            <p class="text-sm text-gray-400">Kelola semua container LXC Anda</p>
        </div>
        <a href="{{ route('portal.orders.create') }}" class="btn-primary btn-sm">+ Order Baru</a>
    </div>

    @if($containers->isEmpty())
        <x-ui.empty-state title="Belum ada container" description="Buat pesanan untuk mendapatkan container pertama Anda.">
            <x-slot:action>
                <a href="{{ route('portal.orders.create') }}" class="btn-primary">Order Sekarang</a>
            </x-slot:action>
        </x-ui.empty-state>
    @else
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        @foreach($containers as $ct)
        @php
            $statusColor = match($ct->status) { 'running' => 'green', 'stopped' => 'red', 'provisioning' => 'yellow', default => 'gray' };
        @endphp
        <div class="card card-body"
             @if($ct->status === 'provisioning') data-poll-status="{{ route('portal.containers.show', $ct) }}" @endif>
            <div class="flex items-start justify-between mb-3">
                <div>
                    <h3 class="font-semibold text-white">{{ $ct->hostname }}</h3>
                    <p class="text-xs font-mono text-gray-400">{{ $ct->ip_address ?? 'Menunggu IP...' }}</p>
                </div>
                <x-ui.badge :color="$statusColor">{{ $ct->status }}</x-ui.badge>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('portal.containers.show', $ct) }}" class="btn-secondary btn-sm flex-1">Detail</a>
                @if($ct->isRunning())
                <form method="POST" action="{{ route('portal.containers.stop', $ct) }}">@csrf
                    <button class="btn-danger btn-sm" data-confirm="Yakin stop container?">Stop</button>
                </form>
                @elseif($ct->isStopped())
                <form method="POST" action="{{ route('portal.containers.start', $ct) }}">@csrf
                    <button class="btn-primary btn-sm">Start</button>
                </form>
                @endif
            </div>
        </div>
        @endforeach
    </div>
    <div class="mt-4">{{ $containers->links() }}</div>
    @endif
</x-layouts.portal>