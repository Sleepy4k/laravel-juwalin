<?php

namespace App\Enums;

enum PaymentGateway: string
{
    case Manual = 'manual';
    case Pakasir = 'pakasir';

    public function label(): string
    {
        return match ($this) {
            self::Manual  => 'Transfer Manual',
            self::Pakasir => 'Pakasir (QRIS / VA)',
        };
    }
}
