<?php

namespace Database\Seeders;

use App\Settings\SiteSettings;
use Illuminate\Database\Seeder;

class SiteSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = app(SiteSettings::class);

        $settings->app_name = 'ADIP';
        $settings->app_tagline = 'VPS & Container Hosting Cepat & Terjangkau';
        $settings->app_description = 'Platform hosting berbasis LXC Container dengan Proxmox. Dapatkan server virtual dengan performa tinggi, harga bersaing, dan panel yang mudah digunakan.';
        $settings->app_logo = null;
        $settings->app_favicon = null;

        $settings->contact_email = 'support@adip.store';
        $settings->contact_phone = '0800-123-4567';
        $settings->contact_address = 'Jakarta, Indonesia';

        $settings->social_instagram = 'https://instagram.com/adipstore';
        $settings->social_twitter = 'https://twitter.com/adipstore';
        $settings->social_facebook = null;
        $settings->social_youtube = null;

        $settings->currency = 'IDR';
        $settings->currency_symbol = 'Rp';

        $settings->payment_gateway = 'manual';
        $settings->payment_midtrans_server_key = null;
        $settings->payment_midtrans_client_key = null;
        $settings->payment_sandbox = true;

        $settings->maintenance_mode = false;
        $settings->maintenance_message = 'Sistem sedang dalam pemeliharaan. Mohon kembali beberapa saat lagi.';

        $settings->meta_title = 'ADIP Store — VPS Container Hosting Indonesia';
        $settings->meta_description = 'Sewa VPS dan container hosting berbasis Proxmox LXC dengan harga mulai Rp 25.000/bulan. Tanpa kontrak, bayar bulanan.';
        $settings->meta_keywords = 'hosting, vps, container, proxmox, lxc, indonesia, murah';

        $settings->proxmox_node_strategy = 'first-available';

        $settings->save();

        $this->command->info('SiteSettings seeded successfully.');
    }
}
