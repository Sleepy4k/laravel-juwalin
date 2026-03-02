<x-layouts.portal title="Detail Tagihan">

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="card card-body space-y-4">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs text-gray-500">Invoice</p>
                        <h2 class="text-lg font-bold font-mono text-gray-100">{{ $payment->invoice_number }}</h2>
                    </div>
                    <x-ui.badge :color="$payment->isPaid()?'green':($payment->status==='failed'?'red':'yellow')">{{ $payment->status }}</x-ui.badge>
                </div>

                <dl class="grid grid-cols-2 gap-4 text-sm border-t border-gray-800 pt-4">
                    <div><dt class="text-gray-500">Jumlah</dt><dd class="text-2xl font-bold text-gray-100">Rp {{ number_format((float)$payment->amount,0,',','.') }}</dd></div>
                    <div><dt class="text-gray-500">Pesanan</dt><dd><a href="{{ route('portal.orders.show', $payment->order_id) }}" class="text-brand-400 hover:underline">#{{ $payment->order_id }}</a></dd></div>
                    <div><dt class="text-gray-500">Dibuat</dt><dd class="text-gray-200">{{ $payment->created_at->format('d M Y H:i') }}</dd></div>
                    <div><dt class="text-gray-500">Batas Bayar</dt><dd class="{{ $payment->expires_at?->isPast() ? 'text-red-400' : 'text-gray-200' }}">{{ $payment->expires_at ? $payment->expires_at->format('d M Y H:i') : '—' }}</dd></div>
                    @if($payment->isPaid())
                    <div><dt class="text-gray-500">Dibayar pada</dt><dd class="text-green-400">{{ $payment->paid_at?->format('d M Y H:i') ?? '—' }}</dd></div>
                    <div><dt class="text-gray-500">Metode</dt><dd class="text-gray-200 capitalize">{{ $payment->payment_method ?? '—' }}</dd></div>
                    @endif
                </dl>
            </div>

            @if($payment->isPending())
            <div class="card card-body space-y-4">
                <h3 class="font-semibold text-gray-100">Lakukan Pembayaran</h3>
                <form method="POST" action="{{ route('portal.billing.pay', $payment) }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div>
                        <x-form.label for="payment_method" required>Metode Pembayaran</x-form.label>
                        <x-form.select id="payment_method" name="payment_method">
                            <option value="transfer">Transfer Bank</option>
                            <option value="qris">QRIS</option>
                            <option value="ewallet">E-Wallet</option>
                        </x-form.select>
                        <x-form.error field="payment_method"/>
                    </div>
                    <div class="p-4 bg-gray-900 rounded-lg border border-gray-700">
                        <p class="text-xs text-gray-500 mb-2">Transfer ke rekening:</p>
                        <p class="font-mono text-lg text-gray-100">BCA 1234 5678 90</p>
                        <p class="text-sm text-gray-400">a.n. PT Hosting Indonesia</p>
                        <p class="text-xs text-gray-500 mt-2">Jumlah: <span class="font-bold text-yellow-400">Rp {{ number_format((float)$payment->amount,0,',','.') }}</span></p>
                    </div>
                    <div>
                        <x-form.label for="proof_file">Upload Bukti Transfer (JPG/PNG, maks 2MB)</x-form.label>
                        <input id="proof_file" name="proof_file" type="file" accept="image/*"
                               class="block w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:bg-brand-600 file:text-white hover:file:bg-brand-700 cursor-pointer"/>
                        <x-form.error field="proof_file"/>
                    </div>
                    <button type="submit" class="btn-primary">Konfirmasi Pembayaran</button>
                </form>
            </div>
            @endif
        </div>

        <div class="space-y-4">
            @if($payment->isPaid())
            <div class="card card-body text-center">
                <div class="h-12 w-12 bg-green-900/50 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </div>
                <p class="text-sm text-green-400 font-medium">Pembayaran Berhasil</p>
                <a href="{{ route('portal.billing.invoice', $payment) }}" class="btn-secondary btn-sm mt-3 w-full block text-center">Download Invoice</a>
            </div>
            @elseif($payment->isPending())
            <div class="card card-body">
                <p class="text-xs text-yellow-300">⏳ Menunggu konfirmasi admin. Proses 1×24 jam kerja.</p>
            </div>
            @endif

            <a href="{{ route('portal.billing.index') }}" class="btn-secondary btn-sm w-full block text-center">← Kembali</a>
        </div>
    </div>

</x-layouts.portal>
