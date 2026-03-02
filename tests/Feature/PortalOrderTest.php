<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Package;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 */
class PortalOrderTest extends TestCase
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
    public function guestCannotAccessPortalOrders(): void
    {
        $this->get(route('portal.orders.index'))->assertRedirect(route('login'));
    }

    /**
     * @test
     */
    public function authenticatedUserCanViewOrdersList(): void
    {
        $this->actingAs($this->user)
            ->get(route('portal.orders.index'))
            ->assertStatus(200);
    }

    /**
     * @test
     */
    public function authenticatedUserCanViewOrderCreatePage(): void
    {
        $this->actingAs($this->user)
            ->get(route('portal.orders.create'))
            ->assertStatus(200);
    }

    /**
     * @test
     */
    public function userCanCreateOrderWithValidPackage(): void
    {
        $package = Package::factory()->create([
            'is_active'     => true,
            'cores'         => 1,
            'memory_mb'     => 1024,
            'disk_gb'       => 20,
            'price_monthly' => 65000,
            'currency'      => 'IDR',
        ]);

        $response = $this->actingAs($this->user)
            ->post(route('portal.orders.store'), ['package_id' => $package->id]);

        $response->assertRedirect();
        $this->assertDatabaseHas('orders', [
            'user_id'    => $this->user->id,
            'package_id' => $package->id,
            'status'     => 'pending',
        ]);
    }

    /**
     * @test
     */
    public function orderStoreFailsWithInvalidPackageId(): void
    {
        $this->actingAs($this->user)
            ->post(route('portal.orders.store'), ['package_id' => 9999])
            ->assertSessionHasErrors('package_id');
    }

    /**
     * @test
     */
    public function orderStoreFailsWithoutPackageId(): void
    {
        $this->actingAs($this->user)
            ->post(route('portal.orders.store'), [])
            ->assertSessionHasErrors('package_id');
    }

    /**
     * @test
     */
    public function userCanViewTheirOwnOrder(): void
    {
        $order = Order::factory()->create(['user_id' => $this->user->id]);

        $this->actingAs($this->user)
            ->get(route('portal.orders.show', $order))
            ->assertStatus(200);
    }

    /**
     * @test
     */
    public function userCannotViewAnotherUsersOrder(): void
    {
        $otherUser = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $otherUser->id]);

        $this->actingAs($this->user)
            ->get(route('portal.orders.show', $order))
            ->assertStatus(403);
    }

    /**
     * @test
     */
    public function userCanCancelPendingOrder(): void
    {
        $order = Order::factory()->create([
            'user_id' => $this->user->id,
            'status'  => 'pending',
        ]);

        $this->actingAs($this->user)
            ->delete(route('portal.orders.destroy', $order))
            ->assertRedirect(route('portal.orders.index'));

        $this->assertDatabaseMissing('orders', ['id' => $order->id]);
    }

    /**
     * @test
     */
    public function userCannotCancelActiveOrder(): void
    {
        $order = Order::factory()->active()->create(['user_id' => $this->user->id]);

        $this->actingAs($this->user)
            ->delete(route('portal.orders.destroy', $order))
            ->assertStatus(422);
    }

    /**
     * @test
     */
    public function userCannotCancelAnotherUsersOrder(): void
    {
        $otherUser = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $otherUser->id]);

        $this->actingAs($this->user)
            ->delete(route('portal.orders.destroy', $order))
            ->assertStatus(403);
    }
}
