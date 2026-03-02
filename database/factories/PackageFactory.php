<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Package>
 */
class PackageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        return [
            'name'           => ucwords($name),
            'slug'           => Str::slug($name),
            'description'    => fake()->sentence(),
            'cores'          => fake()->randomElement([1, 2, 4]),
            'memory_mb'      => fake()->randomElement([512, 1024, 2048]),
            'disk_gb'        => fake()->randomElement([10, 20, 50]),
            'storage_pool'   => 'local-lvm',
            'network_bridge' => 'vmbr0',
            'price_monthly'  => fake()->randomElement([25000, 65000, 175000]),
            'price_setup'    => 0,
            'currency'       => 'IDR',
            'is_active'      => true,
            'is_featured'    => false,
            'sort_order'     => fake()->numberBetween(1, 10),
            'features'       => ['1 CPU Core', '1 GB RAM'],
        ];
    }

    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }

    public function featured(): static
    {
        return $this->state(['is_featured' => true]);
    }
}
