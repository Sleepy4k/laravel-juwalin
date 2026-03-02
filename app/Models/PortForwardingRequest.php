<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PortForwardingRequest extends Model
{
    /** @use HasFactory<\Database\Factories\PortForwardingRequestFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'container_id',
        'protocol',
        'source_port',
        'destination_port',
        'status',
        'reason',
        'admin_note',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function container(): BelongsTo
    {
        return $this->belongsTo(Container::class);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    protected function casts(): array
    {
        return [
            'source_port'      => 'integer',
            'destination_port' => 'integer',
        ];
    }
}
