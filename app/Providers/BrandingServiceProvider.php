<?php

namespace App\Providers;

use App\Models\BrandingConfigurations;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class BrandingServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        View::composer('*', function ($view) {
            $view->with('branding', BrandingConfigurations::current());
        });
    }
}