<?php

namespace Tests\Feature;

use App\Jobs\ProvisionContainerJob;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

/**
 * @internal
 */
class AdminPaymentTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');
    }

    /**
     * @test
     */
    public function adminCanViewPaymentsList(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.payments.index'))
            ->assertStatus(200);
    }

    /**
     * @test
     */
    public function adminCanViewPaymentDetail(): void
    {
        $payment = $this->makePendingPayment();

        $this->actingAs($this->admin)
            ->get(route('admin.payments.show', $payment))
            ->assertStatus(200);
    }

    /**
     * @test
     */
    public function adminCanConfirmPayment(): void
    {
        Queue::fake();

        $payment = $this->makePendingPayment();

        $this->actingAs($this->admin)
            ->patch(route('admin.payments.confirm', $payment))
            ->assertRedirect();

        $this->assertDatabaseHas('payments', [
            'id'     => $payment->id,
            'status' => 'paid',
        ]);

        Queue::assertPushed(ProvisionContainerJob::class);
    }

    /**
     * @test
     */
    public function confirmingPaymentActivatesOrder(): void
    {
        Queue::fake();

        $payment = $this->makePendingPayment();

        $this->actingAs($this->admin)
            ->patch(route('admin.payments.confirm', $payment));

        $this->assertDatabaseHas('orders', [
            'id'             => $payment->order_id,
            'payment_status' => 'paid',
        ]);

        Queue::assertPushed(ProvisionContainerJob::class);
    }

    /**
     * @test
     */
    public function adminCanRejectPayment(): void
    {
        $payment = $this->makePendingPayment();

        $this->actingAs($this->admin)
            ->patch(route('admin.payments.reject', $payment))
            ->assertRedirect();

        $this->assertDatabaseHas('payments', [
            'id'     => $payment->id,
            'status' => 'failed',
        ]);
    }

    private function makePendingPayment(): Payment
    {
        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id]);

        return Payment::factory()->create([
            'user_id'  => $user->id,
            'order_id' => $order->id,
            'status'   => 'pending',
        ]);
    }
}
