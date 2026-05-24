<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WfhMonitoringEvent extends Model
{
    use HasFactory;

    protected $table = 'wfh_monitoring_events';

    protected $fillable = [
        'wfh_monitoring_session_id',
        'user_id',
        'event_type',
        'label',
        'details',
        'payload',
        'occurred_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'occurred_at' => 'datetime',
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(WfhMonitoringSessionRecord::class, 'wfh_monitoring_session_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
