<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BrandingConfigurations extends Model
{
    use HasFactory;

    protected $fillable = [
        'primary_color_light',
        'primary_color_dark',
        'secondary_color_light',
        'secondary_color_dark',
        'primary_font_color_light',
        'primary_font_color_dark',
        'secondary_font_color_light',
        'secondary_font_color_dark',
        'logo_light_path',
        'logo_dark_path',
        'site_icon_path',
    ];
    
    public static function current()
    {
        return cache()->rememberForever('current_branding', function () {
            return self::firstOrCreate([]);
        });
    }
}
