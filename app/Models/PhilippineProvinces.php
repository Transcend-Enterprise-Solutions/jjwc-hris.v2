<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhilippineProvinces extends Model
{
    use HasFactory;
    protected $table = 'philippine_provinces';
    protected $fillable = [
        'psgc_code',
        'province_description',
        'region_code',
        'province_code',
    ];

    public function philippineCities(){
        return $this->hasMany(PhilippineCities::class);
    }

    public function philippineRegion(){
        return $this->belongsTo(PhilippineRegions::class);
    }
}
