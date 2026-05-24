<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Wfh extends Model
{
    use HasFactory;
    
    protected $table = 'wfh';
    
    protected $fillable = [
        'wfhDay',
        'status',
        'user_id',
        'approved_at',
        'rejected_at',
        'rejection_reason',
        'wfh_reason',
        'attachment'
    ];

    protected $casts = [
        'wfhDay' => 'date',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getAttachmentUrlAttribute()
    {
        if ($this->attachment) {
            return Storage::disk('public')->url($this->attachment);
        }
        return null;
    }

    protected static function boot()
    {
        parent::boot();

        // Delete attachment file when model is deleted
        static::deleting(function ($wfh) {
            if ($wfh->attachment && Storage::disk('public')->exists($wfh->attachment)) {
                Storage::disk('public')->delete($wfh->attachment);
            }
        });
    }
}