<?php

namespace Database\Factories;

use App\Models\Package;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id'           => User::factory(),
            'package_id'        => Package::factory(),
            'cores'             => 1,
            'memory_mb'         => 1024,
            'disk_gb'           => 20,
            'price'             => 65000,
            'currency'          => 'IDR',
            'status'            => 'pending',
            'payment_status'    => 'pending',
            'payment_reference' => null,
        ];
    }

    public function active(): static
    {
        return $this->state(['status' => 'active', 'payment_status' => 'paid']);
    }

    public function cancelled(): static
    {
        return $this->state(['status' => 'cancelled']);
    }
}
