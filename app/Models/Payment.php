<?php

namespace App\Models;

use App\Enums\PaymentGateway;
use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    /** @use HasFactory<\Database\Factories\PaymentFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_id',
        'amount',
        'currency',
        'method',
        'payment_method',
        'status',
        'reference',
        'invoice_number',
        'gateway',
        'gateway_payload',
        'proof_file',
        'notes',
        'paid_at',
        'expires_at',
    ];

    /**
     * Generate an invoice number like INV-20260301-00001.
     */
    public static function generateInvoiceNumber(): string
    {
        $prefix = 'INV-' . now()->format('Ymd');
        $latest = static::query()
            ->where('invoice_number', 'like', $prefix . '%')
            ->count();

        return $prefix . '-' . str_pad((string) ($latest + 1), 5, '0', STR_PAD_LEFT);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function isPaid(): bool
    {
        return $this->status === PaymentStatus::Paid;
    }

    public function isPending(): bool
    {
        return $this->status === PaymentStatus::Pending;
    }

    public function getFormattedAmountAttribute(): string
    {
        return 'Rp ' . number_format((float) $this->amount, 0, ',', '.');
    }

    protected function casts(): array
    {
        return [
            'amount'          => 'decimal:2',
            'gateway_payload' => 'array',
            'paid_at'         => 'datetime',
            'expires_at'      => 'datetime',
            'status'          => PaymentStatus::class,
            'gateway'         => PaymentGateway::class,
        ];
    }
}
