<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'related_id',
        'type',
        'notif',
        'read',
    ];

    /**
     * Get the user that owns the notification.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the document request associated with the notification.
     */
    public function docRequest()
    {
        return $this->belongsTo(DocRequest::class);
    }

    public function leaveApplication()
    {
        return $this->belongsTo(LeaveApplication::class);
    }
}
