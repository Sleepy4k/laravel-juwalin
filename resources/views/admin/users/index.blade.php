<x-layouts.admin title="Manajemen Pengguna">

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-xl font-bold text-gray-100">Pengguna</h1>
        <a href="{{ route('admin.users.create-admin') }}" class="btn-primary btn-sm">+ Tambah Admin</a>
    </div>

    <form method="GET" class="flex gap-3 mb-6">
        <x-form.input name="search" :value="request('search')" placeholder="Cari nama / email…" class="w-72"/>
        <x-form.select name="role">
            <option value="">Semua Role</option>
            <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
            <option value="user" {{ request('role') === 'user' ? 'selected' : '' }}>User</option>
        </x-form.select>
        <button type="submit" class="btn-secondary btn-sm">Filter</button>
        <a href="{{ route('admin.users.index') }}" class="btn-ghost btn-sm">Reset</a>
    </form>

    <div class="card overflow-hidden">
        <table class="table">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Container</th>
                    <th>Pesanan</th>
                    <th>Bergabung</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td>
                        <div class="flex items-center gap-2">
                            <div class="h-8 w-8 rounded-full bg-brand-600 flex items-center justify-center text-white text-xs font-bold shrink-0">{{ strtoupper(substr($user->name,0,1)) }}</div>
                            <span class="font-medium text-gray-200">{{ $user->name }}</span>
                        </div>
                    </td>
                    <td class="text-sm text-gray-400">{{ $user->email }}</td>
                    <td>
                        @foreach($user->roles as $role)
                        <x-ui.badge :color="$role->name === 'admin' ? 'blue' : 'gray'">{{ $role->name }}</x-ui.badge>
                        @endforeach
                    </td>
                    <td class="text-gray-300">{{ $user->containers_count ?? $user->containers()->count() }}</td>
                    <td class="text-gray-300">{{ $user->orders_count ?? $user->orders()->count() }}</td>
                    <td class="text-xs text-gray-400">{{ $user->created_at->format('d M Y') }}</td>
                    <td>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.users.show', $user) }}" class="text-xs text-brand-400 hover:text-brand-300">Detail</a>
                            @if($user->id !== auth()->id())
                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}" data-confirm-form="Hapus user {{ $user->name }}?">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs text-red-400 hover:text-red-300">Hapus</button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center py-8 text-gray-500">Tidak ada pengguna.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $users->withQueryString()->links() }}</div>

</x-layouts.admin>
