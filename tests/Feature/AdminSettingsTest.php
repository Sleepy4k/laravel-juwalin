<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\SiteSettingsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 */
class AdminSettingsTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
        $this->seed(SiteSettingsSeeder::class);
        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');
    }

    /**
     * @test
     */
    public function adminCanViewSettingsPage(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.settings.index'))
            ->assertStatus(200);
    }

    /**
     * @test
     */
    public function adminCanUpdateSettings(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.settings.update'), $this->validSettingsData(['app_name' => 'New App Name']))
            ->assertRedirect(route('admin.settings.index'));
    }

    /**
     * @test
     */
    public function settingsUpdateRequiresAppName(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.settings.update'), $this->validSettingsData(['app_name' => '']))
            ->assertSessionHasErrors('app_name');
    }

    /**
     * @test
     */
    public function settingsUpdateRequiresValidContactEmail(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.settings.update'), $this->validSettingsData(['contact_email' => 'not-an-email']))
            ->assertSessionHasErrors('contact_email');
    }

    /**
     * @test
     */
    public function settingsUpdateAcceptsPaymentGateway(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.settings.update'), $this->validSettingsData([
                'payment_gateway' => 'manual',
            ]))
            ->assertRedirect(route('admin.settings.index'))
            ->assertSessionHasNoErrors();
    }

    /**
     * @test
     */
    public function settingsUpdateRejectsInvalidPaymentGateway(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.settings.update'), $this->validSettingsData([
                'payment_gateway' => 'invalid',
            ]))
            ->assertSessionHasErrors('payment_gateway');
    }

    private function validSettingsData(array $overrides = []): array
    {
        return array_merge([
            'app_name'      => 'Test App',
            'contact_email' => 'admin@test.com',
        ], $overrides);
    }
}
