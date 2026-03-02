<?php

namespace App\Models;

use App\Enums\ActivityCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'category',
        'event',
        'description',
        'metadata',
        'ip_address',
        'user_agent',
    ];

    /**
     * @param array<string, mixed>|null $metadata
     */
    public static function record(
        ActivityCategory $category,
        string $event,
        string $description,
        ?int $userId = null,
        ?array $metadata = null,
    ): self {
        return static::create([
            'user_id'     => $userId ?? Auth::id(),
            'category'    => $category,
            'event'       => $event,
            'description' => $description,
            'metadata'    => $metadata,
            'ip_address'  => request()->ip(),
            'user_agent'  => request()->userAgent(),
        ]);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'category' => ActivityCategory::class,
        ];
    }
}
