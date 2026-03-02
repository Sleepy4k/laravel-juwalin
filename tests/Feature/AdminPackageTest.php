<?php

namespace Tests\Feature;

use App\Models\Package;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 */
class AdminPackageTest extends TestCase
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
    public function adminCanViewPackagesList(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.packages.index'))
            ->assertStatus(200);
    }

    /**
     * @test
     */
    public function adminCanViewCreatePackageForm(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.packages.create'))
            ->assertStatus(200);
    }

    /**
     * @test
     */
    public function adminCanCreatePackage(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.packages.store'), $this->validPackageData())
            ->assertRedirect(route('admin.packages.index'));

        $this->assertDatabaseHas('packages', ['name' => 'Test Package']);
    }

    /**
     * @test
     */
    public function packageCreationRequiresName(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.packages.store'), $this->validPackageData(['name' => '']))
            ->assertSessionHasErrors('name');
    }

    /**
     * @test
     */
    public function packageCreationRequiresCores(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.packages.store'), $this->validPackageData(['cores' => '']))
            ->assertSessionHasErrors('cores');
    }

    /**
     * @test
     */
    public function adminCanViewPackageEditForm(): void
    {
        $package = Package::factory()->create();

        $this->actingAs($this->admin)
            ->get(route('admin.packages.edit', $package))
            ->assertStatus(200);
    }

    /**
     * @test
     */
    public function adminCanUpdatePackage(): void
    {
        $package = Package::factory()->create();

        $this->actingAs($this->admin)
            ->put(route('admin.packages.update', $package), $this->validPackageData(['name' => 'Updated Package']))
            ->assertRedirect(route('admin.packages.index'));

        $this->assertDatabaseHas('packages', ['id' => $package->id, 'name' => 'Updated Package']);
    }

    /**
     * @test
     */
    public function adminCanDeletePackage(): void
    {
        $package = Package::factory()->create();

        $this->actingAs($this->admin)
            ->delete(route('admin.packages.destroy', $package))
            ->assertRedirect(route('admin.packages.index'));

        $this->assertDatabaseMissing('packages', ['id' => $package->id]);
    }

    private function validPackageData(array $overrides = []): array
    {
        return array_merge([
            'name'          => 'Test Package',
            'description'   => 'A test package',
            'cores'         => 2,
            'memory_mb'     => 1024,
            'disk_gb'       => 20,
            'price_monthly' => 65000,
            'price_setup'   => 0,
            'is_active'     => 1,
            'is_featured'   => 0,
            'sort_order'    => 1,
            'features'      => "Feature 1\nFeature 2",
        ], $overrides);
    }
}
