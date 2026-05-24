<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WfhMonitoringScreenshot extends Model
{
    use HasFactory;

    protected $fillable = [
        'wfh_monitoring_session_id',
        'user_id',
        'path',
        'capture_type',
        'mime_type',
        'size_bytes',
        'captured_at',
        'flagged',
        'flag_notes',
        'flagged_by',
        'flagged_at',
    ];

    protected $casts = [
        'captured_at' => 'datetime',
        'flagged' => 'boolean',
        'flagged_at' => 'datetime',
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
