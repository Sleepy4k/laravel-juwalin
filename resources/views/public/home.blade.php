<x-layouts.public :title="$siteSettings->app_name ?? null">

    {{-- ── Hero ── --}}
    <section class="relative overflow-hidden pt-24 pb-20">
        {{-- Background glow --}}
        <div class="absolute inset-0 -z-10">
            <div class="absolute top-0 left-1/2 -translate-x-1/2 h-96 w-96 rounded-full bg-brand-500/10 blur-3xl"></div>
        </div>

        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 text-center">
            <span class="inline-flex items-center gap-1.5 rounded-full border border-brand-500/30 bg-brand-500/10 px-3 py-1 text-xs font-medium text-brand-400 mb-6">
                <span class="h-1.5 w-1.5 rounded-full bg-brand-400 animate-pulse"></span>
                Powered by Proxmox VE
            </span>

            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-white leading-tight mb-6">
                Hosting LXC<br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-brand-400 to-cyan-400">Cepat & Terjangkau</span>
            </h1>

            <p class="max-w-2xl mx-auto text-lg text-gray-400 mb-10">
                Sewa container LXC dengan spesifikasi fleksibel, kontrol penuh, dan harga yang relatif murah.
                Cocok untuk developer, startup, maupun kebutuhan tugas kampus.
            </p>

            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ route('register') }}" class="btn-primary btn-lg">
                    Mulai Sekarang
                </a>
                <a href="{{ route('pricing') }}" class="btn-secondary btn-lg">
                    Lihat Harga
                </a>
            </div>

            {{-- Stats --}}
            <div class="mt-16 grid grid-cols-3 gap-8 max-w-lg mx-auto">
                <div>
                    <p class="text-3xl font-bold text-white">99.9%</p>
                    <p class="text-sm text-gray-400 mt-1">Uptime SLA</p>
                </div>
                <div>
                    <p class="text-3xl font-bold text-white">NVMe</p>
                    <p class="text-sm text-gray-400 mt-1">Storage</p>
                </div>
                <div>
                    <p class="text-3xl font-bold text-white">24/7</p>
                    <p class="text-sm text-gray-400 mt-1">Support</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ── Packages ── --}}
    <section class="py-20 bg-gray-900/50">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-white">Pilih Paket Anda</h2>
                <p class="text-gray-400 mt-3">Semua harga sudah termasuk IP publik dan support teknis</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($packages as $pkg)
                <div class="card card-body relative flex flex-col {{ $pkg->is_featured ? 'ring-2 ring-brand-500' : '' }}">
                    @if($pkg->is_featured)
                    <div class="absolute -top-3 left-1/2 -translate-x-1/2">
                        <span class="badge-blue text-xs px-3 py-1 rounded-full">Terpopuler</span>
                    </div>
                    @endif

                    <h3 class="text-lg font-bold text-white">{{ $pkg->name }}</h3>
                    <p class="text-sm text-gray-400 mt-1">{{ $pkg->description }}</p>

                    <div class="mt-4 mb-6">
                        <span class="text-3xl font-extrabold text-white">Rp {{ $pkg->formatted_price }}</span>
                        <span class="text-gray-400 text-sm">/bulan</span>
                    </div>

                    <ul class="space-y-2.5 mb-8 flex-1">
                        @foreach(($pkg->features ?? []) as $feature)
                        <li class="flex items-center gap-2 text-sm text-gray-300">
                            <svg class="h-4 w-4 text-brand-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            {{ $feature }}
                        </li>
                        @endforeach
                    </ul>

                    <a href="{{ route('register') }}" class="{{ $pkg->is_featured ? 'btn-primary' : 'btn-secondary' }} w-full">
                        Pilih {{ $pkg->name }}
                    </a>
                </div>
                @endforeach
            </div>

            <div class="text-center mt-8">
                <a href="{{ route('pricing') }}" class="text-sm text-brand-400 hover:text-brand-300 transition-colors">
                    Lihat perbandingan lengkap →
                </a>
            </div>
        </div>
    </section>

    {{-- ── Features ── --}}
    <section class="py-20">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-white">Mengapa ADIP?</h2>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @php
                    $features = [
                        ['title' => 'Kontrol Penuh',    'desc' => 'Akses root ke container Anda. Install software apa saja tanpa batasan.',  'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'],
                        ['title' => 'Infrastruktur Proxmox', 'desc' => 'Ditenagai oleh Proxmox VE — platform virtualisasi enterprise open-source.',  'icon' => 'M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2'],
                        ['title' => 'Deploy Cepat',     'desc' => 'Container siap digunakan dalam hitungan menit setelah pembayaran dikonfirmasi.',  'icon' => 'M13 10V3L4 14h7v7l9-11h-7z'],
                        ['title' => 'Harga Transparan', 'desc' => 'Tidak ada biaya tersembunyi. Bayar hanya yang Anda gunakan.',  'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                        ['title' => 'Port Forwarding',  'desc' => 'Request port forwarding langsung dari panel tanpa hubungi support.',  'icon' => 'M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4'],
                        ['title' => 'Support Responsif','desc' => 'Tim support siap membantu Anda melalui email dan WhatsApp.',  'icon' => 'M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z'],
                    ];
                @endphp

                @foreach($features as $f)
                <div class="card card-body">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-500/10 text-brand-400 mb-4">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $f['icon'] }}"/>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-white mb-1.5">{{ $f['title'] }}</h3>
                    <p class="text-sm text-gray-400">{{ $f['desc'] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ── CTA ── --}}
    <section class="py-20 bg-gradient-to-r from-brand-600 to-brand-800">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl font-bold text-white mb-4">Siap memulai?</h2>
            <p class="text-brand-100 mb-8">Daftar sekarang dan dapatkan container pertama Anda dalam 5 menit.</p>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ route('register') }}" class="btn bg-white text-brand-700 hover:bg-brand-50 btn-lg font-bold">
                    Daftar Sekarang
                </a>
                <a href="{{ route('contact') }}" class="btn border border-brand-300 text-white hover:bg-brand-700 btn-lg">
                    Hubungi Kami
                </a>
            </div>
        </div>
    </section>

</x-layouts.public>
