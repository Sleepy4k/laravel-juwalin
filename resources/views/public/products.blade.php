<x-layouts.public title="Produk">
    <section class="py-20">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h1 class="text-4xl font-extrabold text-white mb-4">Produk Hosting LXC</h1>
                <p class="text-gray-400 text-lg">Container dedicat dengan akses root penuh dan resource terjamin.</p>
            </div>

            {{-- Info bar --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-12">
                @foreach([['Akses Root', 'Kontrol penuh ke OS'], ['IP Publik', 'Dedicated IP untuk setiap container'], ['Backup', 'Backup otomatis terjadwal']] as [$title, $desc])
                <div class="card card-body text-center">
                    <h3 class="font-semibold text-white">{{ $title }}</h3>
                    <p class="text-sm text-gray-400 mt-1">{{ $desc }}</p>
                </div>
                @endforeach
            </div>

            {{-- Packages --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @forelse($packages as $pkg)
                <div class="card card-body flex flex-col {{ $pkg->is_featured ? 'ring-2 ring-brand-500' : '' }}">
                    @if($pkg->is_featured)
                    <span class="badge-blue self-start mb-3">Terpopuler</span>
                    @endif

                    <h2 class="text-xl font-bold text-white">{{ $pkg->name }}</h2>
                    <p class="text-sm text-gray-400 mt-1 mb-4">{{ $pkg->description }}</p>

                    <div class="grid grid-cols-3 gap-3 mb-6 bg-gray-800/50 rounded-lg p-3">
                        <div class="text-center">
                            <p class="text-lg font-bold text-white">{{ $pkg->cores }}</p>
                            <p class="text-xs text-gray-400">vCPU</p>
                        </div>
                        <div class="text-center border-x border-gray-700">
                            <p class="text-lg font-bold text-white">{{ $pkg->memory_gb }}GB</p>
                            <p class="text-xs text-gray-400">RAM</p>
                        </div>
                        <div class="text-center">
                            <p class="text-lg font-bold text-white">{{ $pkg->disk_gb }}GB</p>
                            <p class="text-xs text-gray-400">SSD</p>
                        </div>
                    </div>

                    <ul class="space-y-2 mb-6 flex-1">
                        @foreach(($pkg->features ?? []) as $feature)
                        <li class="flex items-center gap-2 text-sm text-gray-300">
                            <svg class="h-3.5 w-3.5 text-brand-400 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            {{ $feature }}
                        </li>
                        @endforeach
                    </ul>

                    <div class="flex items-center justify-between pt-4 border-t border-gray-800">
                        <div>
                            <span class="text-xl font-bold text-white">Rp {{ $pkg->formatted_price }}</span>
                            <span class="text-gray-400 text-xs">/bln</span>
                        </div>
                        <a href="{{ route('portal.orders.create') }}?package={{ $pkg->slug }}" class="{{ $pkg->is_featured ? 'btn-primary' : 'btn-secondary' }} btn-sm">
                            Order
                        </a>
                    </div>
                </div>
                @empty
                    <div class="col-span-3">
                        <x-ui.empty-state title="Belum ada paket" description="Silakan cek kembali nanti."/>
                    </div>
                @endforelse
            </div>
        </div>
    </section>
</x-layouts.public>
