<x-layouts.admin title="Detail Paket">

    <div class="max-w-2xl space-y-6">
        <div class="card card-body space-y-4">
            <div class="flex items-start justify-between">
                <div>
                    <h2 class="text-xl font-bold text-gray-100">{{ $package->name }}</h2>
                    <p class="text-sm text-gray-400 mt-1">{{ $package->description }}</p>
                </div>
                <div class="flex gap-2">
                    <x-ui.badge :color="$package->is_active ? 'green' : 'gray'">{{ $package->is_active ? 'Aktif' : 'Non-aktif' }}</x-ui.badge>
                    @if($package->is_featured) <x-ui.badge color="blue">Populer</x-ui.badge> @endif
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4 border-t border-gray-800 pt-4">
                <div class="text-center"><p class="text-2xl font-bold text-brand-400">{{ $package->cores }}</p><p class="text-xs text-gray-500">vCPU</p></div>
                <div class="text-center"><p class="text-2xl font-bold text-brand-400">{{ $package->memory_gb }} GB</p><p class="text-xs text-gray-500">RAM</p></div>
                <div class="text-center"><p class="text-2xl font-bold text-brand-400">{{ $package->disk_gb }} GB</p><p class="text-xs text-gray-500">Disk</p></div>
            </div>

            <div class="border-t border-gray-800 pt-4">
                <p class="text-xs text-gray-500 mb-1">Harga / Bulan</p>
                <p class="text-2xl font-bold text-gray-100">Rp {{ $package->formatted_price }}</p>
            </div>

            @if(is_array($package->features) && count($package->features))
            <div class="border-t border-gray-800 pt-4">
                <p class="text-xs text-gray-500 mb-2">Fitur</p>
                <ul class="space-y-1">
                    @foreach($package->features as $feature)
                    <li class="flex items-center gap-2 text-sm text-gray-300">
                        <svg class="w-4 h-4 text-brand-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        {{ $feature }}
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="border-t border-gray-800 pt-4 flex items-center gap-4">
                <div><p class="text-xs text-gray-500">Total Pesanan</p><p class="text-lg font-bold text-gray-100">{{ $package->orders_count ?? $package->orders()->count() }}</p></div>
                <div><p class="text-xs text-gray-500">Urutan Tampilan</p><p class="text-lg font-bold text-gray-100">{{ $package->sort_order }}</p></div>
                <div><p class="text-xs text-gray-500">Dibuat</p><p class="text-sm text-gray-300">{{ $package->created_at->format('d M Y') }}</p></div>
            </div>
        </div>

        <div class="flex gap-3">
            <a href="{{ route('admin.packages.edit', $package) }}" class="btn-primary">Edit Paket</a>
            <a href="{{ route('admin.packages.index') }}" class="btn-secondary">← Kembali</a>
        </div>
    </div>

</x-layouts.admin>
