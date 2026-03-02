<x-layouts.portal title="Tagihan & Pembayaran">

    <div class="card overflow-hidden">
        <table class="table">
            <thead>
                <tr>
                    <th>Invoice</th>
                    <th>Paket / Pesanan</th>
                    <th>Jumlah</th>
                    <th>Status</th>
                    <th>Tgl</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payments as $payment)
                <tr>
                    <td class="font-mono text-xs text-gray-400">{{ $payment->invoice_number }}</td>
                    <td class="text-sm text-gray-300">{{ $payment->order->package->name ?? 'Pesanan #'.$payment->order_id }}</td>
                    <td class="font-medium text-gray-200">Rp {{ number_format((float)$payment->amount,0,',','.') }}</td>
                    <td>
                        <x-ui.badge :color="$payment->isPaid()?'green':($payment->status==='failed'?'red':'yellow')">{{ $payment->status }}</x-ui.badge>
                    </td>
                    <td class="text-xs text-gray-400">{{ $payment->created_at->format('d M Y') }}</td>
                    <td>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('portal.billing.show', $payment) }}" class="text-xs text-brand-400 hover:text-brand-300">Detail</a>
                            @if($payment->isPaid())
                            <a href="{{ route('portal.billing.invoice', $payment) }}" class="text-xs text-gray-400 hover:text-gray-300">Invoice</a>
                            @endif
                            @if($payment->isPending())
                            <a href="{{ route('portal.billing.pay', $payment) }}" class="text-xs text-green-400 hover:text-green-300 font-medium">Bayar</a>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6">
                        <x-ui.empty-state title="Belum ada tagihan" description="Tagihan akan muncul setelah kamu memesan layanan hosting.">
                            <x-slot:action>
                                <a href="{{ route('portal.orders.create') }}" class="btn-primary btn-sm">Pesan Sekarang</a>
                            </x-slot:action>
                        </x-ui.empty-state>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $payments->links() }}</div>

</x-layouts.portal>
