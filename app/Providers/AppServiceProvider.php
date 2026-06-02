<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Force secure HTTPS URL layouts if running in GitHub Codespaces
        if (isset($_SERVER['HTTP_X_FORWARDED_HOST']) || (config('app.url') && str_contains(config('app.url'), '.github.dev'))) {
            URL::forceScheme('https');
        }
    }
}