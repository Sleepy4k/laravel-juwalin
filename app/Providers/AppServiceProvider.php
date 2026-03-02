<?php

namespace App\Providers;

use App\Contracts\ContainerRepositoryInterface;
use App\Repositories\EloquentContainerRepository;
use App\Settings\SiteSettings;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Throwable;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            ContainerRepositoryInterface::class,
            EloquentContainerRepository::class,
        );
    }

    public function boot(): void
    {
        Paginator::useTailwind();

        // Share site settings with all views
        View::composer('*', static function($view): void {
            try {
                $settings = app(SiteSettings::class);
                $view->with('siteSettings', $settings);
            } catch (Throwable) {
                // settings table may not exist yet (artisan commands during install)
            }
        });
    }
}
