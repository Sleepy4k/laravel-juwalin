<?php

namespace App\Http\Controllers\Payment;

use App\Enums\ActivityCategory;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Jobs\ProvisionContainerJob;
use App\Mail\PaymentConfirmed;
use App\Models\ActivityLog;
use App\Models\Payment;
use App\Services\PakasirService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PakasirWebhookController extends Controller
{
    public function __construct(private readonly PakasirService $pakasirService) {}

    /**
     * Handle incoming Pakasir webhook (payment completed notification).
     */
    public function handle(Request $request): JsonResponse
    {
        $payload = $request->all();

        Log::info('[Pakasir Webhook] Received payload', $payload);

        if (!$this->pakasirService->verifyWebhook($payload)) {
            Log::warning('[Pakasir Webhook] Invalid payload', $payload);

            return response()->json(['ok' => false, 'message' => 'Invalid webhook'], 400);
        }

        $orderId = $payload['order_id'] ?? null;
        $amount = (int) ($payload['amount'] ?? 0);

        if (!$orderId) {
            return response()->json(['ok' => false, 'message' => 'Missing order_id'], 400);
        }

        $payment = Payment::where('invoice_number', $orderId)
            ->orWhere('reference', $orderId)
            ->with(['order', 'user'])
            ->first();

        if (!$payment) {
            Log::warning('[Pakasir Webhook] Payment not found', ['order_id' => $orderId]);

            return response()->json(['ok' => false, 'message' => 'Payment not found'], 404);
        }

        if ($payment->isPaid()) {
            return response()->json(['ok' => true, 'message' => 'Already processed']);
        }

        // Mark payment as paid
        $payment->update([
            'status'          => PaymentStatus::Paid,
            'paid_at'         => now(),
            'gateway_payload' => $payload,
        ]);

        // Update order status
        $order = $payment->order;
        if ($order) {
            $order->update([
                'payment_status' => PaymentStatus::Paid,
                'status'         => OrderStatus::Active,
            ]);

            // Defer container provisioning to the queue
            $hostname = 'ct-' . $order->id . '-' . str_replace(' ', '-', strtolower($order->user->name ?? 'user'));
            ProvisionContainerJob::dispatch($order, $hostname);
        }

        // Send confirmation email
        if ($payment->user) {
            Mail::to($payment->user->email)->queue(new PaymentConfirmed($payment));
        }

        // Log activity
        ActivityLog::record(
            category: ActivityCategory::Payment,
            event: 'payment_confirmed_pakasir',
            description: "Pembayaran {$payment->invoice_number} dikonfirmasi via Pakasir.",
            userId: $payment->user_id,
            metadata: ['invoice' => $payment->invoice_number, 'amount' => $amount],
        );

        return response()->json(['ok' => true]);
    }
}
