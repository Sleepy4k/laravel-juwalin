<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ActivityCategory;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Jobs\ProvisionContainerJob;
use App\Mail\PaymentConfirmed;
use App\Models\ActivityLog;
use App\Models\Payment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with(['user', 'order.package'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $payments = $query->paginate(20)->withQueryString();

        return view('admin.payments.index', compact('payments'));
    }

    public function show(Payment $payment)
    {
        $payment->load(['user', 'order.package']);

        return view('admin.payments.show', compact('payment'));
    }

    public function confirm(Payment $payment): RedirectResponse
    {
        $payment->update(['status' => PaymentStatus::Paid, 'paid_at' => now()]);
        $payment->order()->update([
            'payment_status' => PaymentStatus::Paid,
            'status'         => OrderStatus::Active,
        ]);

        $payment->loadMissing(['order', 'user']);

        // Dispatch LXC provisioning job
        if ($payment->order) {
            $hostname = 'ct-' . $payment->order->id . '-'
                . str_replace(' ', '-', strtolower($payment->order->user->name ?? 'user'));
            ProvisionContainerJob::dispatch($payment->order, $hostname);
        }

        // Send email notification
        if ($payment->user) {
            Mail::to($payment->user->email)->queue(new PaymentConfirmed($payment));
        }

        // Activity log
        ActivityLog::record(
            category: ActivityCategory::Payment,
            event: 'payment_confirmed_admin',
            description: "Pembayaran {$payment->invoice_number} dikonfirmasi oleh admin.",
            metadata: ['invoice' => $payment->invoice_number, 'payment_id' => $payment->id],
        );

        return redirect()->back()->with('success', 'Pembayaran dikonfirmasi dan container sedang diprovisioning.');
    }

    public function reject(Payment $payment): RedirectResponse
    {
        $payment->update(['status' => PaymentStatus::Failed]);

        ActivityLog::record(
            category: ActivityCategory::Payment,
            event: 'payment_rejected',
            description: "Pembayaran {$payment->invoice_number} ditolak oleh admin.",
            metadata: ['invoice' => $payment->invoice_number, 'payment_id' => $payment->id],
        );

        return redirect()->back()->with('success', 'Pembayaran ditolak.');
    }
}
