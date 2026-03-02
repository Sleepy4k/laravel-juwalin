<x-layouts.portal title="Order Paket Baru">
    @push('head')
        @vite('resources/js/pages/order.js')
    @endpush

    <div class="max-w-5xl">
        <p class="text-gray-400 text-sm mb-8">Pilih paket yang sesuai kebutuhan Anda.</p>

        <form method="POST" action="{{ route('portal.orders.store') }}" id="order-form">
            @csrf

            <input type="hidden" name="package_id" id="selected_package_id">

            {{-- Package cards --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                @foreach($packages as $pkg)
                <div
                    tabindex="0"
                    role="button"
                    data-package-card
                    data-package-id="{{ $pkg->id }}"
                    data-package-name="{{ $pkg->name }}"
                    data-package-price="Rp {{ $pkg->formatted_price }}/bulan"
                    data-package-specs="{{ $pkg->cores }}C {{ $pkg->memory_gb }}GB RAM {{ $pkg->disk_gb }}GB SSD"
                    data-featured="{{ $pkg->is_featured ? 'true' : 'false' }}"
                    class="card card-body cursor-pointer select-none transition-all duration-150 hover:border-brand-500/50 relative"
                >
                    {{-- Check badge --}}
                    <div data-check class="hidden absolute top-3 right-3 flex h-5 w-5 items-center justify-center rounded-full bg-brand-500">
                        <svg class="h-3 w-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                    </div>

                    @if($pkg->is_featured)
                    <span class="badge-blue self-start mb-2 text-xs">Populer</span>
                    @endif

                    <h3 class="font-bold text-white">{{ $pkg->name }}</h3>
                    <p class="text-xs text-gray-400 mt-1 mb-3">{{ $pkg->description }}</p>

                    <div class="text-xl font-bold text-white mb-3">Rp {{ $pkg->formatted_price }}<span class="text-xs text-gray-400">/bln</span></div>

                    <div class="grid grid-cols-3 gap-2 text-center bg-gray-800/50 rounded-lg p-2 mb-3">
                        <div><p class="text-sm font-bold text-white">{{ $pkg->cores }}</p><p class="text-xs text-gray-500">CPU</p></div>
                        <div><p class="text-sm font-bold text-white">{{ $pkg->memory_gb }}G</p><p class="text-xs text-gray-500">RAM</p></div>
                        <div><p class="text-sm font-bold text-white">{{ $pkg->disk_gb }}G</p><p class="text-xs text-gray-500">SSD</p></div>
                    </div>
                </div>
                @endforeach
            </div>

            @error('package_id')
                <x-ui.alert type="error" class="mb-4">{{ $message }}</x-ui.alert>
            @enderror

            {{-- Order summary --}}
            <div id="order-summary" class="hidden card card-body mb-6">
                <h3 class="font-semibold text-white mb-3">Ringkasan Pesanan</h3>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-400">Paket</span>
                    <span class="text-gray-100 font-medium" data-pkg-name></span>
                </div>
                <div class="flex justify-between text-sm mt-1">
                    <span class="text-gray-400">Spesifikasi</span>
                    <span class="text-gray-100" data-pkg-specs></span>
                </div>
                <div class="flex justify-between text-sm mt-1 pt-2 border-t border-gray-800">
                    <span class="text-gray-400 font-medium">Harga/Bulan</span>
                    <span class="text-white font-bold" data-pkg-price></span>
                </div>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="btn-primary btn-lg">Lanjutkan Pembayaran →</button>
                <a href="{{ route('portal.orders.index') }}" class="btn-secondary btn-lg">Batal</a>
            </div>
        </form>
    </div>

</x-layouts.portal>
