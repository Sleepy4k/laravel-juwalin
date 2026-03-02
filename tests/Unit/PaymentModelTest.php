<?php

namespace Tests\Unit;

use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 */
class PaymentModelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function isPaidReturnsTrueWhenStatusIsPaid(): void
    {
        $payment = $this->makePayment(['status' => 'paid']);

        $this->assertTrue($payment->isPaid());
    }

    /**
     * @test
     */
    public function isPaidReturnsFalseWhenStatusIsPending(): void
    {
        $payment = $this->makePayment(['status' => 'pending']);

        $this->assertFalse($payment->isPaid());
    }

    /**
     * @test
     */
    public function isPendingReturnsTrueWhenStatusIsPending(): void
    {
        $payment = $this->makePayment(['status' => 'pending']);

        $this->assertTrue($payment->isPending());
    }

    /**
     * @test
     */
    public function isPendingReturnsFalseWhenStatusIsPaid(): void
    {
        $payment = $this->makePayment(['status' => 'paid']);

        $this->assertFalse($payment->isPending());
    }

    /**
     * @test
     */
    public function formattedAmountAttributeFormatsCorrectly(): void
    {
        $payment = $this->makePayment(['amount' => 65000]);

        $this->assertEquals('Rp 65.000', $payment->formatted_amount);
    }

    /**
     * @test
     */
    public function generateInvoiceNumberHasCorrectFormat(): void
    {
        $invoiceNumber = Payment::generateInvoiceNumber();

        $this->assertMatchesRegularExpression('/^INV-\d{8}-\d{5}$/', $invoiceNumber);
    }

    /**
     * @test
     */
    public function generateInvoiceNumberIncrementsForSameDay(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id]);

        Payment::factory()->create([
            'user_id'        => $user->id,
            'order_id'       => $order->id,
            'invoice_number' => 'INV-' . now()->format('Ymd') . '-00001',
        ]);

        $nextInvoice = Payment::generateInvoiceNumber();

        $this->assertEquals('INV-' . now()->format('Ymd') . '-00002', $nextInvoice);
    }

    /**
     * @test
     */
    public function paymentBelongsToUser(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id]);
        $payment = Payment::factory()->create(['user_id' => $user->id, 'order_id' => $order->id]);

        $this->assertEquals($user->id, $payment->user->id);
    }

    /**
     * @test
     */
    public function paymentBelongsToOrder(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id]);
        $payment = Payment::factory()->create(['user_id' => $user->id, 'order_id' => $order->id]);

        $this->assertEquals($order->id, $payment->order->id);
    }

    private function makePayment(array $overrides = []): Payment
    {
        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id]);

        return Payment::factory()->create(array_merge([
            'user_id'  => $user->id,
            'order_id' => $order->id,
        ], $overrides));
    }
}
