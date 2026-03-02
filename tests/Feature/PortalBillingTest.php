<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 */
class PortalBillingTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
        $this->user = User::factory()->create();
        $this->user->assignRole('user');
    }

    /**
     * @test
     */
    public function guestCannotAccessBilling(): void
    {
        $this->get(route('portal.billing.index'))->assertRedirect(route('login'));
    }

    /**
     * @test
     */
    public function userCanViewBillingList(): void
    {
        $this->actingAs($this->user)
            ->get(route('portal.billing.index'))
            ->assertStatus(200);
    }

    /**
     * @test
     */
    public function userCanViewTheirOwnPayment(): void
    {
        $payment = $this->makePayment();

        $this->actingAs($this->user)
            ->get(route('portal.billing.show', $payment))
            ->assertStatus(200);
    }

    /**
     * @test
     */
    public function userCannotViewAnotherUsersPayment(): void
    {
        $other = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $other->id]);
        $payment = Payment::factory()->create(['user_id' => $other->id, 'order_id' => $order->id]);

        $this->actingAs($this->user)
            ->get(route('portal.billing.show', $payment))
            ->assertStatus(403);
    }

    /**
     * @test
     */
    public function userCanViewInvoice(): void
    {
        $payment = $this->makePayment();

        $this->actingAs($this->user)
            ->get(route('portal.billing.invoice', $payment))
            ->assertStatus(200);
    }

    /**
     * @test
     */
    public function userCanSubmitPaymentProof(): void
    {
        $payment = $this->makePayment(['status' => 'pending']);

        $this->actingAs($this->user)
            ->post(route('portal.billing.pay', $payment), [
                'payment_method' => 'transfer',
            ])
            ->assertRedirect(route('portal.billing.show', $payment));

        $this->assertDatabaseHas('payments', [
            'id'             => $payment->id,
            'payment_method' => 'transfer',
        ]);
    }

    /**
     * @test
     */
    public function billingPayRequiresPaymentMethod(): void
    {
        $payment = $this->makePayment(['status' => 'pending']);

        $this->actingAs($this->user)
            ->post(route('portal.billing.pay', $payment), [])
            ->assertSessionHasErrors('payment_method');
    }

    /**
     * @test
     */
    public function billingPayRejectsAlreadyPaidPayment(): void
    {
        $payment = $this->makePayment(['status' => 'paid']);

        $this->actingAs($this->user)
            ->post(route('portal.billing.pay', $payment), [
                'payment_method' => 'transfer',
            ])
            ->assertStatus(422);
    }

    /**
     * @test
     */
    public function billingPayRejectsOtherUsersPayment(): void
    {
        $other = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $other->id]);
        $payment = Payment::factory()->create(['user_id' => $other->id, 'order_id' => $order->id, 'status' => 'pending']);

        $this->actingAs($this->user)
            ->post(route('portal.billing.pay', $payment), [
                'payment_method' => 'transfer',
            ])
            ->assertStatus(403);
    }

    private function makePayment(array $overrides = []): Payment
    {
        $order = Order::factory()->create(['user_id' => $this->user->id]);

        return Payment::factory()->create(array_merge([
            'user_id'  => $this->user->id,
            'order_id' => $order->id,
            'status'   => 'pending',
        ], $overrides));
    }
}
