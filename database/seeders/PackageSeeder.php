<?php

namespace Database\Seeders;

use App\Models\Package;
use Illuminate\Database\Seeder;

class PackageSeeder extends Seeder
{
    public function run(): void
    {
        $packages = [
            [
                'name'          => 'Starter',
                'slug'          => 'starter',
                'description'   => 'Cocok untuk proyek kecil, blog, atau belajar server.',
                'cores'         => 1,
                'memory_mb'     => 512,
                'disk_gb'       => 10,
                'price_monthly' => 25000,
                'price_setup'   => 0,
                'currency'      => 'IDR',
                'is_active'     => true,
                'is_featured'   => false,
                'sort_order'    => 1,
                'features'      => [
                    '1 vCPU Core',
                    '512 MB RAM',
                    '10 GB SSD Storage',
                    'Bandwidth Unlimited',
                    'IP Publik 1',
                    'Panel Kontrol',
                ],
            ],
            [
                'name'          => 'Pro',
                'slug'          => 'pro',
                'description'   => 'Ideal untuk aplikasi web, API, atau staging environment.',
                'cores'         => 2,
                'memory_mb'     => 1024,
                'disk_gb'       => 25,
                'price_monthly' => 65000,
                'price_setup'   => 0,
                'currency'      => 'IDR',
                'is_active'     => true,
                'is_featured'   => true,
                'sort_order'    => 2,
                'features'      => [
                    '2 vCPU Core',
                    '1 GB RAM',
                    '25 GB SSD Storage',
                    'Bandwidth Unlimited',
                    'IP Publik 1',
                    'Panel Kontrol',
                    'Backup Mingguan',
                ],
            ],
            [
                'name'          => 'Business',
                'slug'          => 'business',
                'description'   => 'Untuk aplikasi produksi dengan traffic tinggi.',
                'cores'         => 4,
                'memory_mb'     => 4096,
                'disk_gb'       => 80,
                'price_monthly' => 175000,
                'price_setup'   => 0,
                'currency'      => 'IDR',
                'is_active'     => true,
                'is_featured'   => false,
                'sort_order'    => 3,
                'features'      => [
                    '4 vCPU Core',
                    '4 GB RAM',
                    '80 GB SSD Storage',
                    'Bandwidth Unlimited',
                    'IP Publik 1',
                    'Panel Kontrol',
                    'Backup Harian',
                    'Prioritas Support',
                ],
            ],
        ];

        foreach ($packages as $pkg) {
            Package::firstOrCreate(['slug' => $pkg['slug']], $pkg);
        }
    }
}
