<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Pending = 'pending';
    case Active = 'active';
    case Suspended = 'suspended';
    case Cancelled = 'cancelled';
    case ProvisioningFailed = 'provisioning_failed';

    public function label(): string
    {
        return match ($this) {
            self::Pending            => 'Menunggu',
            self::Active             => 'Aktif',
            self::Suspended          => 'Ditangguhkan',
            self::Cancelled          => 'Dibatalkan',
            self::ProvisioningFailed => 'Gagal Provisioning',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Pending            => 'yellow',
            self::Active             => 'green',
            self::Suspended          => 'orange',
            self::Cancelled          => 'red',
            self::ProvisioningFailed => 'red',
        };
    }
}
