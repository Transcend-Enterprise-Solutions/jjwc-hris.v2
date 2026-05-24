<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveApprover extends Model
{
    use HasFactory;

    protected $table = 'leave_approvers';

    protected $fillable = [
        'approver_level',
        'required_role',
        'user_id',
        'oic_user_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function oicUser()
    {
        return $this->belongsTo(User::class, 'oic_user_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByLevel($query, $level)
    {
        return $query->where('approver_level', $level);
    }

    public function scopeByRole($query, $role)
    {
        return $query->where('required_role', $role);
    }
}
