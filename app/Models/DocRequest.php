<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class DocRequest extends Model implements Auditable
{
    use HasFactory, AuditableTrait;

    protected $fillable = [
        'user_id',
        'document_type',
        'date_requested',
        'status',
        'date_completed',
        'file_path',
        'filename',
    ];

    protected $casts = [
        'date_requested' => 'datetime',
        'date_completed' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function rating()
    {
        return $this->hasOne(Rating::class);
    }

    /**
     * Customize the Audit message for updates
     */
    public function generateTags(): array
    {
        return ['document', 'request', 'user:' . $this->user_id];
    }

    /**
     * Get description for audit logs.
     */
    public function getAuditDescriptionForEvent(string $eventName): string
    {
        switch ($eventName) {
            case 'created':
                return "Document request created by user {$this->user->name}";
            case 'updated':
                return "Document request updated by user {$this->user->name}";
            case 'deleted':
                return "Document request deleted by user {$this->user->name}";
            default:
                return '';
        }
    }
}
