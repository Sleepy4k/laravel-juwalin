<x-layouts.portal title="Order Container">
<div class="mx-auto max-w-2xl space-y-6">
    <h1 class="text-2xl font-bold text-white">Order a Container</h1>

    {{-- Server availability banner (populated by JS) --}}
    <div id="resources-banner"
         class="hidden rounded-xl border border-indigo-700/40 bg-indigo-900/20 px-4 py-3 text-sm text-indigo-300">
        Loading server availability...
    </div>

    <form method="POST" action="{{ route('portal.order.store') }}" id="order-form"
          class="rounded-xl border border-gray-800 bg-gray-900 p-6 space-y-8">
        @csrf

        {{-- ── Cores slider ──────────────────────────────────── --}}
        <div class="space-y-3">
            <div class="flex items-center justify-between">
                <label for="cores" class="text-sm font-medium text-gray-300">
                    vCPU Cores
                </label>
                <span id="cores-display"
                      class="rounded-full bg-indigo-600/30 px-3 py-0.5 text-sm font-semibold text-indigo-300">
                    1 core
                </span>
            </div>
            <input
                type="range"
                id="cores"
                name="cores"
                min="1"
                max="{{ $limits['max_cores'] }}"
                value="{{ old('cores', 1) }}"
                data-display="cores-display"
                data-unit-singular="core"
                data-unit-plural="cores"
                class="slider w-full"
            >
            <div class="flex justify-between text-xs text-gray-500">
                <span>1 core</span><span>{{ $limits['max_cores'] }} cores</span>
            </div>
            @error('cores')
                <p class="text-xs text-red-400">{{ $message }}</p>
            @enderror
        </div>

        {{-- ── RAM slider ─────────────────────────────────────── --}}
        <div class="space-y-3">
            <div class="flex items-center justify-between">
                <label for="memory_mb" class="text-sm font-medium text-gray-300">
                    RAM
                </label>
                <span id="memory-display"
                      class="rounded-full bg-indigo-600/30 px-3 py-0.5 text-sm font-semibold text-indigo-300">
                    256 MB
                </span>
            </div>
            <input
                type="range"
                id="memory_mb"
                name="memory_mb"
                min="256"
                step="256"
                max="{{ $limits['max_memory_mb'] }}"
                value="{{ old('memory_mb', 512) }}"
                data-display="memory-display"
                data-unit-singular="MB"
                data-unit-plural="MB"
                class="slider w-full"
            >
            <div class="flex justify-between text-xs text-gray-500">
                <span>256 MB</span><span>{{ $limits['max_memory_mb'] }} MB</span>
            </div>
            @error('memory_mb')
                <p class="text-xs text-red-400">{{ $message }}</p>
            @enderror
        </div>

        {{-- ── Disk slider ─────────────────────────────────────── --}}
        <div class="space-y-3">
            <div class="flex items-center justify-between">
                <label for="disk_gb" class="text-sm font-medium text-gray-300">
                    Disk Storage
                </label>
                <span id="disk-display"
                      class="rounded-full bg-indigo-600/30 px-3 py-0.5 text-sm font-semibold text-indigo-300">
                    5 GB
                </span>
            </div>
            <input
                type="range"
                id="disk_gb"
                name="disk_gb"
                min="5"
                step="5"
                max="{{ $limits['max_disk_gb'] }}"
                value="{{ old('disk_gb', 10) }}"
                data-display="disk-display"
                data-unit-singular="GB"
                data-unit-plural="GB"
                class="slider w-full"
            >
            <div class="flex justify-between text-xs text-gray-500">
                <span>5 GB</span><span>{{ $limits['max_disk_gb'] }} GB</span>
            </div>
            @error('disk_gb')
                <p class="text-xs text-red-400">{{ $message }}</p>
            @enderror
        </div>

        {{-- ── Estimated price ────────────────────────────────── --}}
        <div class="rounded-lg border border-gray-700 bg-gray-800/50 p-4">
            <p class="text-xs text-gray-400">Estimated monthly price</p>
            <p id="price-estimate" class="text-2xl font-bold text-white">Rp —</p>
            <p class="text-xs text-gray-500">* Price may change at checkout after resource validation.</p>
        </div>

        <button type="submit" id="submit-btn"
                class="w-full rounded-xl bg-indigo-600 px-4 py-3 font-semibold text-white transition-colors hover:bg-indigo-500 disabled:cursor-not-allowed disabled:opacity-50">
            Order Now
        </button>
    </form>
</div>

@push('data')
{{-- Pass server-side limits to JS as a data island --}}
<script id="order-limits-data" type="application/json">
{
    "resources_url": "{{ route('api.proxmox.resources') }}",
    "price_per_core": 25000,
    "price_per_gb_ram": 15000,
    "price_per_gb_disk": 2000
}
</script>
@endpush

</x-layouts.portal>
