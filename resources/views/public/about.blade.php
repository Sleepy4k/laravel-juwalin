<x-layouts.public title="Tentang Kami">
    <section class="py-20">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h1 class="text-4xl font-extrabold text-white mb-4">Tentang {{ $siteSettings->app_name ?? 'ADIP' }}</h1>
                <p class="text-lg text-gray-400">{{ $siteSettings->app_tagline ?? '' }}</p>
            </div>

            <div class="card card-body prose prose-invert max-w-none">
                <p class="text-gray-300">
                    {{ $siteSettings->app_description ?? '' }}
                </p>
                <p class="text-gray-300 mt-4">
                    Kami menyediakan layanan container LXC berbasis Proxmox VE dengan harga terjangkau untuk kebutuhan
                    developer, startup, dan proyek kampus. Setiap container dikelola secara profesional dengan
                    infrastruktur yang stabil dan tim dukungan yang responsif.
                </p>

                <h2 class="text-xl font-bold text-white mt-8 mb-4">Misi Kami</h2>
                <p class="text-gray-300">
                    Memberikan akses infrastruktur server yang terjangkau dan mudah digunakan untuk semua kalangan,
                    dari pelajar yang baru belajar server hingga bisnis yang membutuhkan solusi hosting handal.
                </p>

                <h2 class="text-xl font-bold text-white mt-8 mb-4">Teknologi</h2>
                <ul class="text-gray-300 space-y-2">
                    <li>Proxmox VE — platform virtualisasi enterprise</li>
                    <li>LXC (Linux Containers) — virtualisasi ringan dan efisien</li>
                    <li>NVMe SSD — storage ultra-cepat</li>
                    <li>Jaringan 1Gbps dengan IP publik dedicated</li>
                </ul>
            </div>

            <div class="mt-12 text-center">
                <a href="{{ route('contact') }}" class="btn-primary btn-lg">Hubungi Kami</a>
            </div>
        </div>
    </section>
</x-layouts.public>
