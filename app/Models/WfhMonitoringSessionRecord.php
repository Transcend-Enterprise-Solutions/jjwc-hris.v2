<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WfhMonitoringSessionRecord extends Model
{
    use HasFactory;

    protected $table = 'wfh_monitoring_sessions';

    protected $fillable = [
        'user_id',
        'status',
        'work_status',
        'screen_share_active',
        'screen_share_started_at',
        'screen_share_ended_at',
        'consented_at',
        'consent_version',
        'browser_tab_title',
        'browser_url',
        'started_at',
        'ended_at',
        'shift_end_at',
        'grace_period_minutes',
        'total_monitored_minutes',
        'online_seconds',
        'last_activity_at',
        'last_latitude',
        'last_longitude',
        'last_location_accuracy',
        'last_geofence_distance',
        'geofence_status',
        'visibility_state',
        'last_focused_at',
        'last_blurred_at',
        'offline_alerted_at',
        'tamper_alerted_at',
        'device_platform',
        'is_pwa',
        'user_agent',
        'activity_count',
        'active_seconds',
        'idle_seconds',
        'keystroke_count',
        'mouse_activity_count',
        'click_count',
        'touch_count',
        'activity_score',
        'url_classification',
        'afk_threshold_minutes',
        'afk_started_at',
        'afk_responded_at',
        'afk_response',
        'afk_excused',
        'afk_excuse_notes',
        'screenshot_interval_minutes',
        'location_interval_minutes',
        'screenshot_request_pending',
        'screenshot_requested_at',
        'screenshot_requested_by',
        'field_location_label',
        'field_photo_path',
        'notes',
        'meta',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'last_activity_at' => 'datetime',
        'screen_share_active' => 'boolean',
        'screen_share_started_at' => 'datetime',
        'screen_share_ended_at' => 'datetime',
        'consented_at' => 'datetime',
        'shift_end_at' => 'datetime',
        'last_focused_at' => 'datetime',
        'last_blurred_at' => 'datetime',
        'offline_alerted_at' => 'datetime',
        'tamper_alerted_at' => 'datetime',
        'is_pwa' => 'boolean',
        'afk_started_at' => 'datetime',
        'afk_responded_at' => 'datetime',
        'afk_excused' => 'boolean',
        'screenshot_request_pending' => 'boolean',
        'screenshot_requested_at' => 'datetime',
        'meta' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(WfhMonitoringEvent::class, 'wfh_monitoring_session_id');
    }

    public function locationPings(): HasMany
    {
        return $this->hasMany(WfhMonitoringLocationPing::class, 'wfh_monitoring_session_id');
    }

    public function screenshots(): HasMany
    {
        return $this->hasMany(WfhMonitoringScreenshot::class, 'wfh_monitoring_session_id');
    }
}
