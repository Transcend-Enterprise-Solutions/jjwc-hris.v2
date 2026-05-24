<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class EmployeesContract extends Model
{
    use HasFactory;

     protected $table = 'employees_contracts';

    protected $fillable = [
        'user_id',
        'contract_number',
        'contract_type',
        'start_date',
        'end_date',
        'contract_details',
        'status',
        'created_by',
        'date_created',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function employee()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByEmployee($query, $userId)
    {
        return $query->where('user_id', $userId)
                    ->orderBy('start_date', 'desc');
    }

    public function scopeCurrent($query)
    {
        return $query->where('status', 'active')
                    ->where('start_date', '<=', now())
                    ->where(function($q) {
                        $q->whereNull('end_date')
                          ->orWhere('end_date', '>=', now());
                    });
    }

    // Helper methods
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isCurrent(): bool
    {
        return $this->isActive() 
            && $this->start_date <= now()
            && ($this->end_date === null || $this->end_date >= now());
    }

    public function isExpired(): bool
    {
        return $this->end_date !== null && $this->end_date < now();
    }

    // File handling methods
    public function getContractFileUrl(): ?string
    {
        return $this->contract_details ? Storage::url($this->contract_details) : null;
    }

    public function hasContractFile(): bool
    {
        return !empty($this->contract_details) && Storage::exists($this->contract_details);
    }

    public function deleteContractFile(): bool
    {
        if ($this->contract_details && Storage::exists($this->contract_details)) {
            return Storage::delete($this->contract_details);
        }
        return false;
    }

    // Boot method to handle file cleanup on deletion
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($contract) {
            $contract->deleteContractFile();
        });
    }
}
