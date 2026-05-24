<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveSettings extends Model
{
    use HasFactory;

    protected $table = 'leave_settings';

    protected $fillable = [
        'name',
        'duration',
        'duration_type',
        'requires_document',
    ];

    protected $casts = [
        'requires_document' => 'boolean',
    ];

    // Accessor to get formatted duration
    public function getFormattedDurationAttribute()
    {
        return $this->duration . ' ' . $this->duration_type;
    }

    // Example of a query scope
    public function scopeRequiresDocument($query)
    {
        return $query->where('requires_document', true);
    }
}