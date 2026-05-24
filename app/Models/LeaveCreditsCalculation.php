<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveCreditsCalculation extends Model
{
    use HasFactory;

    protected $table = 'leave_credits_calculation';

    protected $fillable = [
        'user_id',
        'month',
        'year',
        'late_time',
        // 'total_credits_earned',
        'late_in_credits',
        'latest_vl_credits',
        'leave_credits_earned',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
