<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfficialBusinessDate extends Model
{
    use HasFactory;

     protected $table = 'official_business_dates';

    protected $fillable = [
        'official_business_id',
        'date',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function officialBusiness()
    {
        return $this->belongsTo(OfficialBusiness::class, 'official_business_id');
    }
}
