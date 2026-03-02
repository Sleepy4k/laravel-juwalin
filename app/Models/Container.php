<?php

namespace App\Models;

use App\Enums\ContainerStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Container extends Model
{
    /** @use HasFactory<\Database\Factories\ContainerFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_id',
        'vmid',
        'node',
        'hostname',
        'cores',
        'memory_mb',
        'disk_gb',
        'storage',
        'status',
        'provision_task_upid',
        'provisioned_at',
        'ip_address',
        'gateway',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function portForwardingRequests(): HasMany
    {
        return $this->hasMany(PortForwardingRequest::class);
    }

    public function isRunning(): bool
    {
        return $this->status === ContainerStatus::Running;
    }

    public function isStopped(): bool
    {
        return $this->status === ContainerStatus::Stopped;
    }

    public function isProvisioning(): bool
    {
        return $this->status === ContainerStatus::Provisioning;
    }

    protected function casts(): array
    {
        return [
            'vmid'           => 'integer',
            'cores'          => 'integer',
            'memory_mb'      => 'integer',
            'disk_gb'        => 'integer',
            'provisioned_at' => 'datetime',
            'status'         => ContainerStatus::class,
        ];
    }
}
