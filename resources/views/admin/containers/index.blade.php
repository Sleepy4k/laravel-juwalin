<x-layouts.admin title="Manajemen Container">

    <form method="GET" class="flex flex-wrap gap-3 mb-6">
        <x-form.input name="search" :value="request('search')" placeholder="Cari hostname / user…" class="w-60"/>
        <x-form.select name="status">
            <option value="">Semua Status</option>
            @foreach(['running','stopped','paused','error','provisioning'] as $s)
                <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
            @endforeach
        </x-form.select>
        <button type="submit" class="btn-secondary btn-sm">Filter</button>
        <a href="{{ route('admin.containers.index') }}" class="btn-ghost btn-sm">Reset</a>
    </form>

    <div class="card overflow-hidden">
        <table class="table">
            <thead>
                <tr>
                    <th>Hostname</th>
                    <th>User</th>
                    <th>VMID</th>
                    <th>Node</th>
                    <th>Spesifikasi</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($containers as $container)
                <tr>
                    <td class="font-mono text-sm text-gray-200">{{ $container->hostname }}</td>
                    <td>
                        <a href="{{ route('admin.users.show', $container->user_id) }}" class="text-xs text-brand-400 hover:underline">{{ $container->user->name ?? '—' }}</a>
                    </td>
                    <td class="font-mono text-xs text-gray-400">{{ $container->vmid ?? '—' }}</td>
                    <td class="text-xs text-gray-400">{{ $container->node ?? '—' }}</td>
                    <td class="text-xs font-mono text-gray-400">{{ $container->cores }}C / {{ round($container->memory_mb/1024,1) }}G / {{ $container->disk_gb }}G</td>
                    <td>
                        <x-ui.badge :color="match($container->status){ 'running'=>'green','stopped'=>'gray','error'=>'red',default=>'yellow'}">{{ $container->status }}</x-ui.badge>
                    </td>
                    <td>
                        <a href="{{ route('admin.containers.show', $container) }}" class="text-xs text-brand-400 hover:text-brand-300">Detail</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center py-8 text-gray-500">Tidak ada container.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $containers->withQueryString()->links() }}</div>

</x-layouts.admin>
