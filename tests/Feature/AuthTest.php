<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 */
class AuthTest extends TestCase
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
    public function loginPageIsAccessible(): void
    {
        $this->get('/login')->assertStatus(200);
    }

    /**
     * @test
     */
    public function registerPageIsAccessible(): void
    {
        $this->get('/register')->assertStatus(200);
    }

    /**
     * @test
     */
    public function userCanLoginAndIsRedirectedToPortalDashboard(): void
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        $this->post('/login', [
            'email'    => $user->email,
            'password' => 'password',
        ])->assertRedirect(route('portal.dashboard'));
    }

    /**
     * @test
     */
    public function adminCanLoginAndIsRedirectedToAdminDashboard(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->post('/login', [
            'email'    => $admin->email,
            'password' => 'password',
        ])->assertRedirect(route('admin.dashboard'));
    }

    /**
     * @test
     */
    public function loginWithWrongPasswordFails(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email'    => $user->email,
            'password' => 'wrong-password',
        ])->assertSessionHasErrors('email');
    }

    /**
     * @test
     */
    public function loginWithNonexistentEmailFails(): void
    {
        $this->post('/login', [
            'email'    => 'notexist@example.com',
            'password' => 'password',
        ])->assertSessionHasErrors('email');
    }

    /**
     * @test
     */
    public function userCanRegisterAndIsRedirectedToPortalDashboard(): void
    {
        $response = $this->post('/register', [
            'name'                  => 'Test User',
            'email'                 => 'newuser@example.com',
            'password'              => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertRedirect(route('portal.dashboard'));
        $this->assertDatabaseHas('users', ['email' => 'newuser@example.com']);
    }

    /**
     * @test
     */
    public function newRegisteredUserHasUserRole(): void
    {
        $this->post('/register', [
            'name'                  => 'Role User',
            'email'                 => 'roleuser@example.com',
            'password'              => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $user = User::where('email', 'roleuser@example.com')->first();
        $this->assertTrue($user->hasRole('user'));
    }

    /**
     * @test
     */
    public function registerValidationRequiresName(): void
    {
        $this->post('/register', [
            'email'                 => 'test@example.com',
            'password'              => 'Password123!',
            'password_confirmation' => 'Password123!',
        ])->assertSessionHasErrors('name');
    }

    /**
     * @test
     */
    public function registerRequiresUniqueEmail(): void
    {
        $user = User::factory()->create(['email' => 'exists@example.com']);

        $this->post('/register', [
            'name'                  => 'Test',
            'email'                 => 'exists@example.com',
            'password'              => 'Password123!',
            'password_confirmation' => 'Password123!',
        ])->assertSessionHasErrors('email');
    }

    /**
     * @test
     */
    public function authenticatedUserCannotAccessLoginPage(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/login')->assertRedirect();
    }

    /**
     * @test
     */
    public function userCanLogout(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('/logout')
            ->assertRedirect('/');

        $this->assertGuest();
    }
}
