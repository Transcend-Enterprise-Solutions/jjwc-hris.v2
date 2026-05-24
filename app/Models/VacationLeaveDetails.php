<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VacationLeaveDetails extends Model
{
    use HasFactory;

    protected $table = 'vacation_leave_details';

    protected $fillable = [
        'application_id',
        'total_earned',
        'less_this_application',
        // 'balance',
        'month',
        'late',
        // 'totalCreditsEarned',
        // 'leave_credits_earned',
        'recommendation',
        'status',
    ];

    public function leaveApplication()
    {
        return $this->belongsTo(LeaveApplication::class, 'application_id');
    }

    public function employeesDtr()
    {
        return $this->belongsTo(EmployeesDtr::class);
    }
}
