<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 */
class AdminOrderTest extends TestCase
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
    public function adminCanViewOrdersList(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.orders.index'))
            ->assertStatus(200);
    }

    /**
     * @test
     */
    public function adminCanViewOrderDetail(): void
    {
        $order = Order::factory()->create();

        $this->actingAs($this->admin)
            ->get(route('admin.orders.show', $order))
            ->assertStatus(200);
    }

    /**
     * @test
     */
    public function adminCanUpdateOrderStatus(): void
    {
        $order = Order::factory()->create(['status' => 'pending']);

        $this->actingAs($this->admin)
            ->patch(route('admin.orders.status', $order), ['status' => 'active'])
            ->assertRedirect();

        $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => 'active']);
    }

    /**
     * @test
     */
    public function orderStatusUpdateRejectsInvalidStatus(): void
    {
        $order = Order::factory()->create();

        $this->actingAs($this->admin)
            ->patch(route('admin.orders.status', $order), ['status' => 'invalid_status'])
            ->assertSessionHasErrors('status');
    }

    /**
     * @test
     */
    public function adminCanDeleteOrder(): void
    {
        $order = Order::factory()->create();

        $this->actingAs($this->admin)
            ->delete(route('admin.orders.destroy', $order))
            ->assertRedirect(route('admin.orders.index'));

        $this->assertDatabaseMissing('orders', ['id' => $order->id]);
    }

    /**
     * @test
     */
    public function ordersListCanBeFilteredByStatus(): void
    {
        Order::factory()->create(['status' => 'active']);
        Order::factory()->create(['status' => 'pending']);

        $this->actingAs($this->admin)
            ->get(route('admin.orders.index', ['status' => 'active']))
            ->assertStatus(200);
    }
}
