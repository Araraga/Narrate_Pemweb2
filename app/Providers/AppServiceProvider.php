<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use App\Livewire\Admin\ArticleModeration;
use App\Livewire\Admin\DashboardAnalytics;

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
        // Register Livewire Components
        Livewire::component('admin.article-moderation', ArticleModeration::class);
        Livewire::component('admin.dashboard-analytics', DashboardAnalytics::class);
        
    }
}