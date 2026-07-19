<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

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
        // Shared hosting DB defaults to MyISAM (1000-byte key limit);
        // cap indexed string columns so utf8mb4 unique keys fit.
        Schema::defaultStringLength(191);
    }
}
