<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class SiteSettings extends Settings
{
    public string $app_name = 'ADIP';

    public string $app_tagline = 'Solusi Hosting Terpercaya';

    public string $app_description = 'Layanan sewa hosting LXC berkualitas tinggi dengan harga terjangkau.';

    public ?string $app_logo = null;

    public ?string $app_favicon = null;

    public string $contact_email = 'info@adip.store';

    public ?string $contact_phone = null;

    public ?string $contact_address = null;

    public ?string $social_instagram = null;

    public ?string $social_twitter = null;

    public ?string $social_facebook = null;

    public ?string $social_youtube = null;

    public string $currency = 'IDR';

    public string $currency_symbol = 'Rp';

    public string $payment_gateway = 'manual';

    public ?string $payment_pakasir_project = null;

    public ?string $payment_pakasir_api_key = null;

    public bool $payment_sandbox = true;

    public bool $maintenance_mode = false;

    public ?string $maintenance_message = null;

    public string $meta_title = 'ADIP Store';

    public string $meta_description = 'Sewa container LXC dengan spesifikasi fleksibel, harga transparan, dan dukungan 24/7.';

    public ?string $meta_keywords = null;

    /** Proxmox node selection strategy: first-available | round-robin */
    public string $proxmox_node_strategy = 'first-available';

    public static function group(): string
    {
        return 'site';
    }
}
