<x-layouts.admin title="Manajemen Pembayaran">

    <form method="GET" class="flex flex-wrap gap-3 mb-6">
        <x-form.input name="search" :value="request('search')" placeholder="Cari invoice / user…" class="w-60"/>
        <x-form.select name="status">
            <option value="">Semua Status</option>
            @foreach(['pending','paid','failed','cancelled'] as $s)
                <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
            @endforeach
        </x-form.select>
        <button type="submit" class="btn-secondary btn-sm">Filter</button>
        <a href="{{ route('admin.payments.index') }}" class="btn-ghost btn-sm">Reset</a>
    </form>

    <div class="card overflow-hidden">
        <table class="table">
            <thead>
                <tr>
                    <th>Invoice</th>
                    <th>User</th>
                    <th>Jumlah</th>
                    <th>Metode</th>
                    <th>Status</th>
                    <th>Tgl</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payments as $payment)
                <tr>
                    <td class="font-mono text-xs text-gray-400">{{ $payment->invoice_number }}</td>
                    <td>
                        <a href="{{ route('admin.users.show', $payment->user_id) }}" class="text-xs text-brand-400 hover:underline">{{ $payment->user->name ?? '—' }}</a>
                    </td>
                    <td class="text-sm font-medium text-gray-200">Rp {{ number_format((float)$payment->amount,0,',','.') }}</td>
                    <td class="text-xs text-gray-400 capitalize">{{ $payment->payment_method ?? '—' }}</td>
                    <td>
                        <x-ui.badge :color="$payment->status==='paid'?'green':($payment->status==='failed'?'red':'yellow')">{{ $payment->status }}</x-ui.badge>
                    </td>
                    <td class="text-xs text-gray-400">{{ $payment->created_at->format('d M Y') }}</td>
                    <td>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.payments.show', $payment) }}" class="text-xs text-brand-400 hover:text-brand-300">Detail</a>
                            @if($payment->isPending())
                            <form method="POST" action="{{ route('admin.payments.confirm', $payment) }}">
                                @csrf @method('PATCH')
                                <button type="submit" class="text-xs text-green-400 hover:text-green-300">Konfirmasi</button>
                            </form>
                            <form method="POST" action="{{ route('admin.payments.reject', $payment) }}">
                                @csrf @method('PATCH')
                                <button type="submit" class="text-xs text-red-400 hover:text-red-300">Tolak</button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center py-8 text-gray-500">Tidak ada data pembayaran.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $payments->withQueryString()->links() }}</div>

</x-layouts.admin>
