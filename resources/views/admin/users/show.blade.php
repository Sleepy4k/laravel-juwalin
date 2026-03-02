<x-layouts.admin title="Detail Pengguna">

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Profile card --}}
        <div class="space-y-6">
            <div class="card card-body text-center">
                <div class="h-16 w-16 rounded-full bg-brand-600 flex items-center justify-center text-white text-2xl font-bold mx-auto mb-3">{{ strtoupper(substr($user->name,0,1)) }}</div>
                <h2 class="font-bold text-gray-100">{{ $user->name }}</h2>
                <p class="text-sm text-gray-400">{{ $user->email }}</p>
                <div class="flex justify-center gap-2 mt-3">
                    @foreach($user->roles as $role)
                    <x-ui.badge :color="$role->name === 'admin' ? 'blue' : 'gray'">{{ $role->name }}</x-ui.badge>
                    @endforeach
                </div>
                <p class="text-xs text-gray-500 mt-4">Bergabung {{ $user->created_at->format('d M Y') }}</p>
            </div>

            {{-- Change role --}}
            @if($user->id !== auth()->id())
            <div class="card card-body">
                <h3 class="font-semibold text-gray-100 mb-3">Ubah Role</h3>
                <form method="POST" action="{{ route('admin.users.role', $user) }}">
                    @csrf @method('PATCH')
                    <x-form.select name="role">
                        <option value="user" {{ $user->hasRole('user') ? 'selected' : '' }}>User</option>
                        <option value="admin" {{ $user->hasRole('admin') ? 'selected' : '' }}>Admin</option>
                    </x-form.select>
                    <button type="submit" class="btn-primary btn-sm mt-3 w-full">Simpan Role</button>
                </form>
            </div>
            @endif

            @if($user->id !== auth()->id())
            <div class="card card-body">
                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" data-confirm-form="Hapus akun {{ $user->name }}? Data terkait akan ikut terhapus.">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn-danger btn-sm w-full">Hapus Pengguna</button>
                </form>
            </div>
            @endif

            <a href="{{ route('admin.users.index') }}" class="btn-secondary btn-sm w-full block text-center">← Kembali</a>
        </div>

        {{-- Orders & Containers --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="card overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-800 font-semibold text-gray-100">Pesanan ({{ $user->orders->count() }})</div>
                <table class="table">
                    <thead><tr><th>ID</th><th>Paket</th><th>Status</th><th>Pembayaran</th><th>Tgl</th><th></th></tr></thead>
                    <tbody>
                        @forelse($user->orders->take(10) as $order)
                        <tr>
                            <td class="font-mono text-xs text-gray-400">#{{ $order->id }}</td>
                            <td class="text-sm text-gray-300">{{ $order->package->name ?? 'Custom' }}</td>
                            <td><x-ui.badge :color="match($order->status){ 'active'=>'green','pending'=>'yellow','suspended'=>'red',default=>'gray'}">{{ $order->status }}</x-ui.badge></td>
                            <td><x-ui.badge :color="$order->payment_status==='paid'?'green':'yellow'">{{ $order->payment_status }}</x-ui.badge></td>
                            <td class="text-xs text-gray-400">{{ $order->created_at->format('d M Y') }}</td>
                            <td><a href="{{ route('admin.orders.show', $order) }}" class="text-xs text-brand-400">Detail</a></td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center py-4 text-gray-500">Belum ada pesanan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-800 font-semibold text-gray-100">Container ({{ $user->containers->count() }})</div>
                <table class="table">
                    <thead><tr><th>VMID</th><th>Hostname</th><th>Status</th><th>Node</th><th></th></tr></thead>
                    <tbody>
                        @forelse($user->containers->take(10) as $container)
                        <tr>
                            <td class="font-mono text-xs text-gray-400">{{ $container->vmid ?? '—' }}</td>
                            <td class="text-sm text-gray-300">{{ $container->hostname }}</td>
                            <td><x-ui.badge :color="$container->status==='running'?'green':($container->status==='stopped'?'gray':'yellow')">{{ $container->status }}</x-ui.badge></td>
                            <td class="text-xs text-gray-400">{{ $container->node ?? '—' }}</td>
                            <td><a href="{{ route('admin.containers.show', $container) }}" class="text-xs text-brand-400">Detail</a></td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center py-4 text-gray-500">Belum ada container.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</x-layouts.admin>
