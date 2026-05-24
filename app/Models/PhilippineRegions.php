<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhilippineRegions extends Model
{
    use HasFactory;
    protected $table = 'philippine_regions';
    protected $fillable = [
        'psgc_code',
        'region_description',
        'region_code',
    ];

    public function philippinesProvince(){
        return $this->hasMany(PhilippineProvinces::class);
    }
}
