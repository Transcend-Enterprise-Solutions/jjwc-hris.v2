<?php

namespace App\Observers;

use App\Models\BrandingConfigurations;
use Illuminate\Support\Facades\Artisan;

class BrandingConfigurationObserver
{
    public function saved(BrandingConfigurations $branding)
    {
        cache()->forget('current_branding');
        Artisan::call('branding:generate-css');
    }
}