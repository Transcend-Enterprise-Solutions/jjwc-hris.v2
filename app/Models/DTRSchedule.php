<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;
use Carbon\Carbon;

class DTRSchedule extends Model implements AuditableContract
{
    use HasFactory, Auditable;

    protected $table = 'dtrschedules';

    protected $fillable = [
        'emp_code',
        'wfh_days',
        'default_start_time',
        'default_end_time',
        'start_date',
        'end_date',
        'is_flexi',
        'has_break',
        'is_overnight',
        'rest_days',
        'is_24hours',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_flexi' => 'boolean',
        'has_break' => 'boolean',
        'is_overnight' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'emp_code', 'emp_code');
    }

    /**
     * Get the effective work hours considering breaks
     */
    public function getEffectiveWorkHours()
    {
        $start = Carbon::createFromTimeString($this->default_start_time);
        $end = Carbon::createFromTimeString($this->default_end_time);

        // Handle overnight shifts
        if ($this->is_overnight && $end->lessThan($start)) {
            $end->addDay();
        }

        $totalHours = $start->diffInHours($end);

        // Subtract break time if applicable
        if ($this->has_break) {
            $totalHours -= 1;
        }

        return $totalHours;
    }

    /**
     * Get the end time for overnight shifts with proper date calculation
     */
    public function getEffectiveEndTime($date)
    {
        $endTime = Carbon::createFromTimeString($this->default_end_time);
        $startTime = Carbon::createFromTimeString($this->default_start_time);

        if ($this->is_overnight && $endTime->lessThan($startTime)) {
            // For overnight shifts, end time is the next day
            return Carbon::parse($date)->addDay()->setTimeFromTimeString($this->default_end_time);
        }

        return Carbon::parse($date)->setTimeFromTimeString($this->default_end_time);
    }

    /**
     * Check if a given date falls within WFH days
     */
    public function isWFHDay($date)
    {
        if (!$this->wfh_days) {
            return false;
        }

        $dayName = Carbon::parse($date)->format('l'); // e.g., "Monday"
        $wfhDaysArray = explode(',', $this->wfh_days);

        return in_array($dayName, $wfhDaysArray);
    }

    /**
     * Get formatted schedule display
     */
    public function getScheduleDisplayAttribute()
    {
        $display = $this->default_start_time . ' - ' . $this->default_end_time;

        if ($this->is_overnight) {
            $display .= ' (Next Day)';
        }

        if ($this->has_break) {
            $display .= ' (1hr break)';
        }

        return $display;
    }

    /**
     * Scope for active schedules
     */
    public function scopeActive($query, $date = null)
    {
        $date = $date ?? now();

        return $query->where('start_date', '<=', $date)
                    ->where('end_date', '>=', $date);
    }

    /**
     * Scope for overnight schedules
     */
    public function scopeOvernight($query)
    {
        return $query->where('is_overnight', true);
    }

    /**
     * Scope for schedules with breaks
     */
    public function scopeWithBreak($query)
    {
        return $query->where('has_break', true);
    }

    /**
     * Scope for flexible schedules
     */
    public function scopeFlexi($query)
    {
        return $query->where('is_flexi', true);
    }

    /**
     * Customize the description of the audit log.
     */
    public function getAuditDescriptionAttribute()
    {
        $userName = $this->user->name ?? 'System';
        $action = ucfirst($this->auditable_type) . ' ' . $this->event;
        $id = $this->auditable_id;

        return "User $userName $action a new schedule (ID: $id).";
    }
}
