<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use App\Services\ModuleAccessService;

class ModuleAccessServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Blade directive to check route access
        Blade::if('hasRouteAccess', function ($routeName) {
            return ModuleAccessService::hasRouteAccess($routeName);
        });

        // Blade directive to check module access
        Blade::if('hasModuleAccess', function ($moduleId) {
            return ModuleAccessService::hasModuleAccess($moduleId);
        });
    }
}