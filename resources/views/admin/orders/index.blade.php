<x-layouts.admin title="Manajemen Pesanan">

    {{-- Filters --}}
    <form method="GET" class="flex flex-wrap gap-3 mb-6">
        <x-form.input name="search" :value="request('search')" placeholder="Cari user / ID pesanan…" class="w-60"/>
        <x-form.select name="status">
            <option value="">Semua Status</option>
            @foreach(['pending','active','suspended','cancelled'] as $s)
                <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
            @endforeach
        </x-form.select>
        <x-form.select name="payment_status">
            <option value="">Semua Pembayaran</option>
            @foreach(['pending','paid','refunded'] as $s)
                <option value="{{ $s }}" {{ request('payment_status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
            @endforeach
        </x-form.select>
        <button type="submit" class="btn-secondary btn-sm">Filter</button>
        <a href="{{ route('admin.orders.index') }}" class="btn-ghost btn-sm">Reset</a>
    </form>

    <div class="card overflow-hidden">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>User</th>
                    <th>Paket</th>
                    <th>Status</th>
                    <th>Pembayaran</th>
                    <th>Tgl Dibuat</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                <tr>
                    <td class="font-mono text-xs text-gray-400">#{{ $order->id }}</td>
                    <td>
                        <a href="{{ route('admin.users.show', $order->user_id) }}" class="text-brand-400 hover:underline text-sm">{{ $order->user->name ?? '—' }}</a>
                        <p class="text-xs text-gray-500">{{ $order->user->email ?? '' }}</p>
                    </td>
                    <td class="text-sm text-gray-300">{{ $order->package->name ?? 'Custom' }}</td>
                    <td>
                        <x-ui.badge :color="match($order->status) { 'active'=>'green','pending'=>'yellow','suspended'=>'red',default=>'gray' }">{{ $order->status }}</x-ui.badge>
                    </td>
                    <td>
                        <x-ui.badge :color="$order->payment_status === 'paid' ? 'green' : ($order->payment_status === 'refunded' ? 'blue' : 'yellow')">{{ $order->payment_status }}</x-ui.badge>
                    </td>
                    <td class="text-xs text-gray-400">{{ $order->created_at->format('d M Y') }}</td>
                    <td>
                        <a href="{{ route('admin.orders.show', $order) }}" class="text-xs text-brand-400 hover:text-brand-300">Detail</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center py-8 text-gray-500">Tidak ada pesanan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $orders->withQueryString()->links() }}</div>

</x-layouts.admin>
