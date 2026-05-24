<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhilippineCities extends Model
{
    use HasFactory;
    protected $table = 'philippine_cities';
    protected $fillable = [
        'psgc_code',
        'city_municipality_description',
        'region_description',
        'province_code',
        'city_municipality_code',
    ];

    public function philippineBarangay(){
        return $this->hasMany(PhilippineBarangays::class);
    }
    
    public function philippineProvince(){
        return $this->belongsTo(PhilippineProvinces::class);
    }
}
