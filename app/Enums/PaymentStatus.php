<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case Pending = 'pending';
    case Paid = 'paid';
    case Failed = 'failed';
    case Refunded = 'refunded';
    case Expired = 'expired';

    public function label(): string
    {
        return match ($this) {
            self::Pending  => 'Menunggu',
            self::Paid     => 'Dibayar',
            self::Failed   => 'Gagal',
            self::Refunded => 'Dikembalikan',
            self::Expired  => 'Kedaluwarsa',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Pending  => 'yellow',
            self::Paid     => 'green',
            self::Failed   => 'red',
            self::Refunded => 'blue',
            self::Expired  => 'gray',
        };
    }
}
