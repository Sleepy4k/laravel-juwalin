<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Package extends Model
{
    /** @use HasFactory<\Database\Factories\PackageFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'cores',
        'memory_mb',
        'disk_gb',
        'storage_pool',
        'network_bridge',
        'price_monthly',
        'price_setup',
        'currency',
        'is_active',
        'is_featured',
        'sort_order',
        'features',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function containers(): HasMany
    {
        return $this->hasMany(Container::class);
    }

    public function getFormattedPriceAttribute(): string
    {
        return number_format((float) $this->price_monthly, 0, ',', '.');
    }

    public function getMemoryGbAttribute(): float
    {
        return round($this->memory_mb / 1024, 1);
    }

    public function scopeActive($query): void
    {
        $query->where('is_active', true)->orderBy('sort_order');
    }

    protected function casts(): array
    {
        return [
            'cores'         => 'integer',
            'memory_mb'     => 'integer',
            'disk_gb'       => 'integer',
            'price_monthly' => 'decimal:2',
            'price_setup'   => 'decimal:2',
            'is_active'     => 'boolean',
            'is_featured'   => 'boolean',
            'sort_order'    => 'integer',
            'features'      => 'array',
        ];
    }
}
