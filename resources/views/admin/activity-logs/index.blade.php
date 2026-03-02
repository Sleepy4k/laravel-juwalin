<x-layouts.admin title="Activity Logs">

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-xl font-bold text-gray-100">Activity Logs</h1>
        <span class="text-sm text-gray-400">Total: {{ $logs->total() }} entri</span>
    </div>

    {{-- Filters --}}
    <form method="GET" class="flex flex-wrap gap-3 mb-6">
        <x-form.select name="category" class="w-44">
            <option value="">Semua Kategori</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->value }}" {{ request('category') === $cat->value ? 'selected' : '' }}>
                    {{ $cat->label() }}
                </option>
            @endforeach
        </x-form.select>
        <x-form.input name="search" :value="request('search')" placeholder="Cari event / deskripsi..." class="w-72" />
        <button type="submit" class="btn-secondary btn-sm">Filter</button>
        <a href="{{ route('admin.activity-logs.index') }}" class="btn-ghost btn-sm">Reset</a>
    </form>

    <div class="card overflow-hidden">
        <table class="table">
            <thead>
                <tr>
                    <th>Waktu</th>
                    <th>User</th>
                    <th>Kategori</th>
                    <th>Event</th>
                    <th>Deskripsi</th>
                    <th>IP</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                <tr>
                    <td class="text-xs text-gray-400 whitespace-nowrap">{{ $log->created_at->format('d M Y H:i:s') }}</td>
                    <td>
                        @if($log->user)
                            <div class="text-sm text-gray-300">{{ $log->user->name }}</div>
                            <div class="text-xs text-gray-500">{{ $log->user->email }}</div>
                        @else
                            <span class="text-xs text-gray-600">System</span>
                        @endif
                    </td>
                    <td>
                        <x-ui.badge :color="match($log->category->value ?? '') {
                            'auth' => 'blue',
                            'payment' => 'green',
                            'order' => 'yellow',
                            'container' => 'purple',
                            'admin' => 'red',
                            'profile' => 'gray',
                            default => 'gray'
                        }">{{ $log->category?->label() ?? $log->category }}</x-ui.badge>
                    </td>
                    <td class="text-xs font-mono text-gray-300">{{ $log->event }}</td>
                    <td class="text-sm text-gray-300 max-w-xs truncate">{{ $log->description }}</td>
                    <td class="text-xs text-gray-500 font-mono">{{ $log->ip_address }}</td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center py-8 text-gray-500">Tidak ada log aktivitas.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $logs->withQueryString()->links() }}</div>

</x-layouts.admin>
