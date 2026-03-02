<?php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    /** @use HasFactory<\Database\Factories\OrderFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'package_id',
        'cores',
        'memory_mb',
        'disk_gb',
        'price',
        'currency',
        'payment_status',
        'payment_reference',
        'status',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function container(): HasOne
    {
        return $this->hasOne(Container::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function isPaid(): bool
    {
        return $this->payment_status === PaymentStatus::Paid;
    }

    public function isPending(): bool
    {
        return $this->status === OrderStatus::Pending;
    }

    public function isActive(): bool
    {
        return $this->status === OrderStatus::Active;
    }

    protected function casts(): array
    {
        return [
            'cores'          => 'integer',
            'memory_mb'      => 'integer',
            'disk_gb'        => 'integer',
            'price'          => 'decimal:2',
            'status'         => OrderStatus::class,
            'payment_status' => PaymentStatus::class,
        ];
    }
}
