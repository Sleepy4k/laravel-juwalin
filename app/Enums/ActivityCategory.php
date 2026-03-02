<?php

namespace App\Enums;

enum ActivityCategory: string
{
    case Auth = 'auth';
    case Payment = 'payment';
    case Order = 'order';
    case Container = 'container';
    case Admin = 'admin';
    case System = 'system';
    case Profile = 'profile';

    public function label(): string
    {
        return match ($this) {
            self::Auth      => 'Autentikasi',
            self::Payment   => 'Pembayaran',
            self::Order     => 'Pesanan',
            self::Container => 'Container',
            self::Admin     => 'Admin',
            self::System    => 'Sistem',
            self::Profile   => 'Profil',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Auth      => 'shield-check',
            self::Payment   => 'credit-card',
            self::Order     => 'shopping-cart',
            self::Container => 'server',
            self::Admin     => 'cog',
            self::System    => 'chip',
            self::Profile   => 'user',
        };
    }
}
