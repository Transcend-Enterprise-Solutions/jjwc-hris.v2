<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonthlyCredits extends Model
{
    use HasFactory;

    protected $table = 'monthly_credits';

    protected $fillable = [
        'user_id',
        'month',
        'year',
        'vl_latest_credits',
        'vl_latest_claimed',
        'sl_latest_credits',
        'sl_latest_claimed',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
