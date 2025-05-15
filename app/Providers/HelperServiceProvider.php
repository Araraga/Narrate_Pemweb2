<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class HelperServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register helper files
        $helperPath = app_path('Helpers');
        if (is_dir($helperPath)) {
            foreach (glob($helperPath . '/*.php') as $file) {
                require_once $file;
            }
        }
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}