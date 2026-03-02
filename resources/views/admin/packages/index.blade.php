<x-layouts.admin title="Manajemen Paket">

    <div class="flex items-center justify-between mb-6">
        <p class="text-sm text-gray-400">{{ $packages->total() }} paket terdaftar</p>
        <a href="{{ route('admin.packages.create') }}" class="btn-primary btn-sm">+ Tambah Paket</a>
    </div>

    <div class="card overflow-hidden">
        <table class="table">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Spesifikasi</th>
                    <th>Harga/Bulan</th>
                    <th>Status</th>
                    <th>Urutan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($packages as $pkg)
                <tr>
                    <td>
                        <div class="font-medium text-gray-200">{{ $pkg->name }}</div>
                        @if($pkg->is_featured) <span class="badge-blue text-xs">Populer</span> @endif
                    </td>
                    <td class="text-xs font-mono text-gray-400">{{ $pkg->cores }}C / {{ round($pkg->memory_mb/1024,1) }}GB / {{ $pkg->disk_gb }}GB</td>
                    <td class="text-gray-200">Rp {{ $pkg->formatted_price }}</td>
                    <td>
                        <x-ui.badge :color="$pkg->is_active ? 'green' : 'gray'">{{ $pkg->is_active ? 'Aktif' : 'Non-aktif' }}</x-ui.badge>
                    </td>
                    <td class="text-gray-400">{{ $pkg->sort_order }}</td>
                    <td>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.packages.edit', $pkg) }}" class="text-xs text-brand-400 hover:text-brand-300">Edit</a>
                            <form method="POST" action="{{ route('admin.packages.destroy', $pkg) }}" data-confirm-form="Hapus paket {{ $pkg->name }}?">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs text-red-400 hover:text-red-300">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center py-8 text-gray-500">Belum ada paket.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $packages->links() }}</div>

</x-layouts.admin>
