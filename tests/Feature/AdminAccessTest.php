<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 */
class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    /**
     * @test
     */
    public function guestCannotAccessAdminDashboard(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
    }

    /**
     * @test
     */
    public function regularUserCannotAccessAdminDashboard(): void
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        $this->actingAs($user)
            ->get(route('admin.dashboard'))
            ->assertStatus(403);
    }

    /**
     * @test
     */
    public function adminCanAccessAdminDashboard(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertStatus(200);
    }

    /**
     * @test
     */
    public function regularUserCannotAccessAdminPackages(): void
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        $this->actingAs($user)
            ->get(route('admin.packages.index'))
            ->assertStatus(403);
    }

    /**
     * @test
     */
    public function adminCanAccessAdminPackages(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin)
            ->get(route('admin.packages.index'))
            ->assertStatus(200);
    }

    /**
     * @test
     */
    public function regularUserCannotAccessAdminOrders(): void
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        $this->actingAs($user)
            ->get(route('admin.orders.index'))
            ->assertStatus(403);
    }

    /**
     * @test
     */
    public function regularUserCannotAccessAdminPayments(): void
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        $this->actingAs($user)
            ->get(route('admin.payments.index'))
            ->assertStatus(403);
    }

    /**
     * @test
     */
    public function regularUserCannotAccessAdminSettings(): void
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        $this->actingAs($user)
            ->get(route('admin.settings.index'))
            ->assertStatus(403);
    }
}
