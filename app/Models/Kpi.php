<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kpi extends Model
{
    use HasFactory;

    protected $table = 'kpi';

    protected $fillable = [
        'position_id',
        'kpi',
        'definition',
        'min_rating',
        'max_rating',
    ];

     /**
     * Get the position that this KPI belongs to
     */
    public function position()
    {
        return $this->belongsTo(Positions::class, 'position_id');
    }

    /**
     * Scope to get KPIs applicable to a specific position
     */
    public function scopeApplicableToPosition($query, $positionId)
    {
        return $query->where(function ($q) use ($positionId) {
            $q->where('position_id', $positionId)
              ->orWhere('position_id', 'all');
        });
    }
}
