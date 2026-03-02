<x-layouts.public title="Harga">
    @push('head')
        @vite('resources/js/pages/order.js')
    @endpush

    <section class="py-20">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-4">
                <h1 class="text-4xl font-extrabold text-white mb-3">Harga Transparan</h1>
                <p class="text-gray-400">Tidak ada biaya setup. Tidak ada biaya tersembunyi.</p>
            </div>

            {{-- Toggle period (static for now) --}}
            <div class="flex justify-center mb-12">
                <div class="inline-flex gap-1 rounded-lg bg-gray-800 p-1 text-sm">
                    <button class="px-4 py-2 rounded-md bg-gray-700 text-white font-medium">Bulanan</button>
                    <button class="px-4 py-2 rounded-md text-gray-400 hover:text-white transition-colors" disabled>Tahunan (diskon 10%) <span class="text-xs text-yellow-400 ml-1">Segera</span></button>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 items-start">
                @foreach($packages as $pkg)
                <div class="card card-body relative flex flex-col {{ $pkg->is_featured ? 'ring-2 ring-brand-500 scale-[1.02]' : '' }}">
                    @if($pkg->is_featured)
                    <div class="absolute -top-4 inset-x-0 flex justify-center">
                        <span class="rounded-full bg-brand-500 px-4 py-1 text-xs font-semibold text-white">✨ Paling Populer</span>
                    </div>
                    @endif

                    <div class="mb-6">
                        <h2 class="text-lg font-bold text-white">{{ $pkg->name }}</h2>
                        <p class="text-sm text-gray-400 mt-1">{{ $pkg->description }}</p>
                    </div>

                    <div class="mb-6">
                        <div class="text-4xl font-extrabold text-white">Rp {{ $pkg->formatted_price }}</div>
                        <div class="text-gray-400 text-sm mt-1">per bulan, tagih bulanan</div>
                        @if((float)$pkg->price_setup === 0.0)
                        <div class="text-green-400 text-xs mt-1">✓ Tanpa biaya setup</div>
                        @endif
                    </div>

                    <ul class="space-y-3 mb-8 flex-1">
                        @foreach(($pkg->features ?? []) as $feature)
                        <li class="flex items-start gap-2.5 text-sm">
                            <svg class="h-4 w-4 text-brand-400 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span class="text-gray-300">{{ $feature }}</span>
                        </li>
                        @endforeach
                    </ul>

                    <a href="{{ route('register') }}" class="{{ $pkg->is_featured ? 'btn-primary' : 'btn-secondary' }} w-full">
                        Mulai dengan {{ $pkg->name }}
                    </a>
                </div>
                @endforeach
            </div>

            {{-- FAQ --}}
            <div class="mt-20">
                <h2 class="text-2xl font-bold text-white text-center mb-8">Pertanyaan Umum</h2>
                <div class="max-w-3xl mx-auto space-y-4">
                    @foreach([
                        ['Berapa lama waktu aktivasi?', 'Container aktif dalam 5–15 menit setelah pembayaran dikonfirmasi oleh admin.'],
                        ['Apakah bisa upgrade paket?', 'Ya, Anda bisa mengajukan upgrade paket kapan saja melalui portal.'],
                        ['Metode pembayaran apa yang diterima?', 'Transfer bank, QRIS, dan beberapa e-wallet. Lebih banyak metode segera hadir.'],
                        ['Apakah ada kontrak jangka panjang?', 'Tidak. Layanan bersifat pay-as-you-go bulanan tanpa kontrak jangka panjang.'],
                    ] as [$q, $a])
                    <div class="card card-body">
                        <h3 class="font-semibold text-white mb-2">{{ $q }}</h3>
                        <p class="text-sm text-gray-400">{{ $a }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
</x-layouts.public>
