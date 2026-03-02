<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;

class BillingController extends Controller
{
    public function index(Request $request)
    {
        $payments = $request->user()
            ->payments()
            ->with('order.package')
            ->latest()
            ->paginate(15);

        return view('portal.billing.index', compact('payments'));
    }

    public function show(Request $request, Payment $payment)
    {
        abort_unless($payment->user_id === $request->user()->id, 403);

        $payment->load('order.package');

        return view('portal.billing.show', compact('payment'));
    }

    public function invoice(Request $request, Payment $payment)
    {
        abort_unless($payment->user_id === $request->user()->id, 403);

        $payment->load('order.package', 'user');

        return view('portal.billing.invoice', compact('payment'));
    }

    public function pay(Request $request, Payment $payment)
    {
        abort_unless($payment->user_id === $request->user()->id, 403);
        abort_unless($payment->isPending(), 422);

        $validated = $request->validate([
            'payment_method' => ['required', 'in:transfer,qris,ewallet'],
            'proof_file'     => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
        ]);

        $proofPath = null;
        if ($request->hasFile('proof_file')) {
            $proofPath = $request->file('proof_file')->store('proofs', 'public');
        }

        $payment->update([
            'payment_method' => $validated['payment_method'],
            'proof_file'     => $proofPath ?? $payment->proof_file,
            'status'         => 'pending',
        ]);

        return redirect()->route('portal.billing.show', $payment)
            ->with('success', 'Bukti pembayaran berhasil dikirim, menunggu konfirmasi admin.');
    }
}
