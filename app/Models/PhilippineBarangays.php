<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhilippineBarangays extends Model
{
    use HasFactory;
    protected $table = 'philippine_barangays';
    protected $fillable = [
        'barangay_code',
        'barangay_description',
        'region_code',
        'province_code',
        'city_municipality_code',
    ];
    
    public function philippineCity(){
        return $this->belongsTo(PhilippineCities::class);
    }
}
