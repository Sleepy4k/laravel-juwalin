<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 */
class PublicPagesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function homePageReturns200(): void
    {
        $this->get('/')->assertStatus(200);
    }

    /**
     * @test
     */
    public function aboutPageReturns200(): void
    {
        $this->get('/about')->assertStatus(200);
    }

    /**
     * @test
     */
    public function productsPageReturns200(): void
    {
        $this->get('/products')->assertStatus(200);
    }

    /**
     * @test
     */
    public function pricingPageReturns200(): void
    {
        $this->get('/pricing')->assertStatus(200);
    }

    /**
     * @test
     */
    public function contactPageReturns200(): void
    {
        $this->get('/contact')->assertStatus(200);
    }

    /**
     * @test
     */
    public function contactFormCanBeSubmitted(): void
    {
        $this->post('/contact', [
            'name'    => 'Test User',
            'email'   => 'test@example.com',
            'subject' => 'Test Subject',
            'message' => 'This is a test message.',
        ])->assertRedirect();
    }

    /**
     * @test
     */
    public function contactFormValidationRequiresName(): void
    {
        $this->post('/contact', [
            'email'   => 'test@example.com',
            'subject' => 'Test Subject',
            'message' => 'Test message',
        ])->assertSessionHasErrors('name');
    }

    /**
     * @test
     */
    public function contactFormValidationRequiresValidEmail(): void
    {
        $this->post('/contact', [
            'name'    => 'Test User',
            'email'   => 'not-an-email',
            'subject' => 'Test Subject',
            'message' => 'Test message',
        ])->assertSessionHasErrors('email');
    }
}
