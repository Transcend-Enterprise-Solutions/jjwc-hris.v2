<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DtrExport extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'start_date',
        'end_date',
        'status',
        'progress',
        'status_message',
        'file_path',
        'error_message',
        'completed_at',
        'filters',
    ];

    protected $casts = [
        'filters' => 'array',
        'completed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getDownloadUrlAttribute()
    {
        if ($this->file_path) {
            return route('dtr-export.download', $this->id);
        }
        return null;
    }
}