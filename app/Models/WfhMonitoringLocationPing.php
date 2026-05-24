<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WfhMonitoringLocationPing extends Model
{
    use HasFactory;

    protected $fillable = [
        'wfh_monitoring_session_id',
        'user_id',
        'latitude',
        'longitude',
        'accuracy',
        'distance_from_geofence',
        'geofence_status',
        'source',
        'location_label',
        'photo_path',
        'occurred_at',
    ];

    protected $casts = [
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
