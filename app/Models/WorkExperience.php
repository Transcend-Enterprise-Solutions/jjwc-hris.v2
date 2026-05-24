<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkExperience extends Model
{
    use HasFactory;

    protected $table = 'work_experience';

    protected $fillable = [
        'user_id',
        'start_date',
        'end_date',
        'toPresent',
        'position',
        'department',
        'monthly_salary',
        'sg_step',
        'status_of_appointment',
        'gov_service',
        'pera',
        'branch',
        'leave_absence_wo_pay',
        'separation_date',
        'separation_cause',
        'remarks',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
