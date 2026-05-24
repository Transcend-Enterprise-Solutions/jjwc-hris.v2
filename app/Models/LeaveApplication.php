<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class LeaveApplication extends Model implements AuditableContract
{
    use HasFactory, Auditable;

    protected $table = 'leave_application';

    protected $fillable = [
        'user_id',
        'endorser1_id',
        'endorser2_id',
        'name',
        'office_or_department',
        'date_of_filing',
        'position',
        'salary',
        'type_of_leave',
        'details_of_leave',
        'number_of_days',
        'list_of_dates',
        'commutation',
        'file_name',
        'file_path',
        'approved_days',
        'approved_dates',
        'remarks',
        'status',
        'stage',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function userData()
    {
        return $this->belongsTo(UserData::class);
    }

    public function vacationLeaveDetails()
    {
        return $this->hasMany(VacationLeaveDetails::class, 'application_id');
    }

    public function sickLeaveDetails()
    {
        return $this->hasMany(SickLeaveDetails::class, 'application_id');
    }

    public function employeesDtr()
    {
        return $this->belongsTo(EmployeesDtr::class);
    }

    public function leaveApprovals()
    {
        return $this->hasMany(LeaveApprovals::class, 'application_id');
    }
}