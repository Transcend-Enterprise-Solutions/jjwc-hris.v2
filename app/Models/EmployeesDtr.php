<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class EmployeesDtr extends Model implements Auditable
{
    use HasFactory, AuditableTrait;

    protected $table = 'employees_dtr';

    protected $fillable = [
        'user_id',
        'emp_code',
        'date',
        'day_of_week',
        'location',
        'time_in',
        'time_out',
        'break_in',
        'break_out',
        'late',
        'overtime',
        'ut',
        'total_hours_rendered',
        'remarks',
        'attachment',
        'up_remarks',
        'updated_by',
        'up_time_in',
        'up_time_out',
        'up_break_in',
        'up_break_out',
        'up_late',
        'up_ut',
        'up_ot',
        'up_total_hours_rendered',
        'ot_approval_status',
        'ot_type',
    ];

    /**
     * Columns to audit - only up_ prefixed columns
     */
    protected $auditInclude = [
        'up_time_in',
        'up_time_out',
        'up_break_in',
        'up_break_out',
        'up_late',
        'up_ut',
        'up_ot',
        'up_total_hours_rendered',
        'up_remarks',
    ];

    /**
     * Prevent auditing of these columns
     */
    protected $auditExclude = [];

    protected $casts = [
        'late' => 'string',
        'overtime' => 'string',
        'ut' => 'string',
        'total_hours_rendered' => 'string',
    ];

    protected $dates = [
        'date',
    ];

    // Define relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function leaveApplication()
    {
        return $this->hasMany(LeaveApplication::class);
    }

    public function vacationLeaveDetails()
    {
        return $this->hasMany(VacationLeaveDetails::class);
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function sickLeaveDetails()
    {
        return $this->hasMany(SickLeaveDetails::class);
    }


}