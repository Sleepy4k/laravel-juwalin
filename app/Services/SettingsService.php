<?php

namespace App\Services;

use App\Settings\SiteSettings;

class SettingsService
{
    public function __construct(private readonly SiteSettings $settings) {}

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->settings->{$key} ?? $default;
    }

    public function set(string $key, mixed $value): void
    {
        $this->settings->{$key} = $value;
        $this->settings->save();
    }

    public function setMany(array $values): void
    {
        foreach ($values as $key => $value) {
            if (property_exists($this->settings, $key)) {
                $this->settings->{$key} = $value;
            }
        }
        $this->settings->save();
    }

    public function all(): SiteSettings
    {
        return $this->settings;
    }

    public function appName(): string
    {
        return $this->settings->app_name;
    }

    public function currency(): string
    {
        return $this->settings->currency;
    }

    public function currencySymbol(): string
    {
        return $this->settings->currency_symbol;
    }

    public function isMaintenanceMode(): bool
    {
        return $this->settings->maintenance_mode;
    }
}
