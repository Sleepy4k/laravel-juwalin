<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id'        => User::factory(),
            'order_id'       => Order::factory(),
            'amount'         => 65000,
            'currency'       => 'IDR',
            'method'         => 'bank_transfer',
            'payment_method' => 'BCA',
            'status'         => 'pending',
            'invoice_number' => 'INV-' . now()->format('Ymd') . '-' . str_pad((string) fake()->unique()->numberBetween(1, 99999), 5, '0', STR_PAD_LEFT),
            'gateway'        => 'manual',
        ];
    }

    public function paid(): static
    {
        return $this->state(['status' => 'paid', 'paid_at' => now()]);
    }
}
