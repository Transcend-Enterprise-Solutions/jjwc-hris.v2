<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceRecords extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'from',
        'to',
        'toPresent',
        'designation',
        'status',
        'salary_annum',
        'station_place_of_assignment',
        'branch',
        'lv_abs_wo_pay',
        'remarks',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
