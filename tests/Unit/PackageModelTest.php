<?php

namespace Tests\Unit;

use App\Models\Package;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 */
class PackageModelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function scopeActiveReturnsOnlyActivePackages(): void
    {
        Package::factory()->create(['is_active' => true, 'sort_order' => 1]);
        Package::factory()->create(['is_active' => true, 'sort_order' => 2]);
        Package::factory()->inactive()->create();

        $activePackages = Package::active()->get();

        $this->assertCount(2, $activePackages);
        $this->assertTrue($activePackages->every(static fn($p) => $p->is_active));
    }

    /**
     * @test
     */
    public function scopeActiveOrdersBySortOrder(): void
    {
        Package::factory()->create(['is_active' => true, 'sort_order' => 3]);
        Package::factory()->create(['is_active' => true, 'sort_order' => 1]);
        Package::factory()->create(['is_active' => true, 'sort_order' => 2]);

        $packages = Package::active()->get();

        $this->assertEquals(1, $packages->first()->sort_order);
        $this->assertEquals(3, $packages->last()->sort_order);
    }

    /**
     * @test
     */
    public function formattedPriceAttributeFormatsCorrectly(): void
    {
        $package = Package::factory()->make(['price_monthly' => 65000]);

        $this->assertEquals('65.000', $package->formatted_price);
    }

    /**
     * @test
     */
    public function memoryGbAttributeConvertsCorrectly(): void
    {
        $package = Package::factory()->make(['memory_mb' => 1024]);

        $this->assertEquals(1.0, $package->memory_gb);
    }

    /**
     * @test
     */
    public function memoryGbRoundsToOneDecimal(): void
    {
        $package = Package::factory()->make(['memory_mb' => 512]);

        $this->assertEquals(0.5, $package->memory_gb);
    }

    /**
     * @test
     */
    public function packageFeaturesCastedAsArray(): void
    {
        $package = Package::factory()->create([
            'features' => ['Feature 1', 'Feature 2'],
        ]);

        $this->assertIsArray($package->fresh()->features);
        $this->assertContains('Feature 1', $package->fresh()->features);
    }

    /**
     * @test
     */
    public function packageIsActiveCastedAsBoolean(): void
    {
        $package = Package::factory()->create(['is_active' => true]);

        $this->assertIsBool($package->fresh()->is_active);
    }
}
