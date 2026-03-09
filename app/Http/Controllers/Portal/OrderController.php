<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Package;
use App\Models\Payment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    /**
     * GET /portal/orders.
     */
    public function index(Request $request): View
    {
        $orders = $request->user()
            ->orders()
            ->with(['package', 'payments'])
            ->latest()
            ->paginate(10);

        return view('portal.orders.index', compact('orders'));
    }

    /**
     * GET /portal/orders/create.
     */
    public function create(): View
    {
        $packages = Package::active()->orderBy('sort_order')->get();

        return view('portal.orders.create', compact('packages'));
    }

    /**
     * POST /portal/orders.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'package_id' => ['required', 'exists:packages,id'],
        ]);

        /** @var Package $package */
        $package = Package::findOrFail($request->integer('package_id'));

        $order = $request->user()->orders()->create([
            'package_id'     => $package->id,
            'cores'          => $package->cores,
            'memory_mb'      => $package->memory_mb,
            'disk_gb'        => $package->disk_gb,
            'price'          => $package->price_monthly,
            'currency'       => $package->currency ?? 'IDR',
            'status'         => 'pending',
            'payment_status' => 'pending',
        ]);

        $payment = $order->payments()->create([
            'user_id'        => $request->user()->id,
            'amount'         => $package->price_monthly,
            'status'         => 'pending',
            'invoice_number' => Payment::generateInvoiceNumber(),
            'expires_at'     => now()->addDays(3),
        ]);

        return redirect()->route('portal.billing.show', $payment)
            ->with('success', 'Pesanan berhasil dibuat! Silakan lakukan pembayaran untuk mengaktifkan layanan.');
    }

    /**
     * GET /portal/orders/{order}.
     */
    public function show(Request $request, Order $order): View
    {
        abort_unless($order->user_id === $request->user()->id, 403);

        $order->load(['package', 'payments', 'container']);

        return view('portal.orders.show', compact('order'));
    }

    /**
     * DELETE /portal/orders/{order}.
     */
    public function destroy(Request $request, Order $order): RedirectResponse
    {
        abort_unless($order->user_id === $request->user()->id, 403);
        abort_if($order->isActive(), 422, 'Pesanan yang sudah aktif tidak dapat dihapus.');

        $order->delete();

        return redirect()->route('portal.orders.index')
            ->with('success', 'Pesanan berhasil dibatalkan.');
    }
}
