<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    use HasFactory;

    protected $fillable = [
        'description',
        'holiday_date',
        'type',
        'region_id'
    ];

    protected $casts = [
        'holiday_date' => 'date',
    ];

    public function region()
    {
        return $this->belongsTo(PhilippineRegions::class, 'region_id');
    }
}