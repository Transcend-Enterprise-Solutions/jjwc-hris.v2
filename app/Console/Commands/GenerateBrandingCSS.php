<?php

namespace App\Console\Commands;

use App\Models\BrandingConfigurations;
use Illuminate\Console\Command;

class GenerateBrandingCSS extends Command
{
    protected $signature = 'branding:generate-css';
    protected $description = 'Generate dynamic CSS based on branding configuration';

    public function handle()
    {
        $branding = BrandingConfigurations::current();
        
        $css = <<<CSS
        :root {
            --color-primary-light: {$branding->primary_color_light};
            --color-primary-dark: {$branding->primary_color_dark};
            --color-secondary-light: {$branding->secondary_color_light};
            --color-secondary-dark: {$branding->secondary_color_dark};
            --color-font-primary-light: {$branding->primary_font_color_light};
            --color-font-primary-dark: {$branding->primary_font_color_dark};
            --color-font-secondary-light: {$branding->secondary_font_color_light};
            --color-font-secondary-dark: {$branding->secondary_font_color_dark};
        }
        CSS;
        
        file_put_contents(public_path('css/branding.css'), $css);
        
        $this->info('Branding CSS generated successfully!');
    }
}