<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class() extends SettingsMigration {
    public function up(): void
    {
        $this->migrator->add('site.app_name', 'ADIP');
        $this->migrator->add('site.app_tagline', 'VPS & Container Hosting Cepat & Terjangkau');
        $this->migrator->add('site.app_description', 'Platform hosting berbasis LXC Container dengan Proxmox.');
        $this->migrator->add('site.app_logo', null);
        $this->migrator->add('site.app_favicon', null);

        $this->migrator->add('site.contact_email', 'support@adip.store');
        $this->migrator->add('site.contact_phone', null);
        $this->migrator->add('site.contact_address', null);

        $this->migrator->add('site.social_instagram', null);
        $this->migrator->add('site.social_twitter', null);
        $this->migrator->add('site.social_facebook', null);
        $this->migrator->add('site.social_youtube', null);

        $this->migrator->add('site.currency', 'IDR');
        $this->migrator->add('site.currency_symbol', 'Rp');

        $this->migrator->add('site.payment_gateway', 'manual');
        $this->migrator->add('site.payment_midtrans_client_key', null);
        $this->migrator->add('site.payment_midtrans_server_key', null);
        $this->migrator->add('site.payment_sandbox', true);

        $this->migrator->add('site.maintenance_mode', false);
        $this->migrator->add('site.maintenance_message', null);

        $this->migrator->add('site.meta_title', 'ADIP Store — VPS Container Hosting Indonesia');
        $this->migrator->add('site.meta_description', 'Sewa VPS dan container hosting berbasis Proxmox LXC.');
        $this->migrator->add('site.meta_keywords', null);

        $this->migrator->add('site.proxmox_node_strategy', 'first-available');
    }
};
