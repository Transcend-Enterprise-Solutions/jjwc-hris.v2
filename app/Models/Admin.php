<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Auth\User as AuthenticatableUser;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class Admin extends AuthenticatableUser implements Authenticatable, Auditable
{
    use HasApiTokens, HasFactory, Notifiable, AuditableTrait;

    protected $table = 'admin';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'user_id',
        'payroll_id',
        'department',
        'office_division',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Attributes to include in the Audit.
     *
     * @var array
     */
    protected $auditInclude = [
        'user_id',
        'payroll_id',
        'department',
        'office_division',
    ];

    /**
     * Audit events to record.
     *
     * @var array
     */
    protected $auditEvents = [
        'created',
        'updated',
        'deleted',
    ];

    public function scopeSearch($query, $term){
        $term = "%$term%";
        $query->where(function ($query) use ($term) {
            $query->where('admin.office_division', 'like', $term)
                ->orWhere(function ($query) use ($term) {
                    $query->where('payrolls.employee_number', 'like', $term)
                        ->orWhere('payrolls.position', 'like', $term)
                        ->orWhere('payrolls.office_division', 'like', $term)
                        ->orWhere('payrolls.department', 'like', $term);
                })
                ->orWhere(function ($query) use ($term) {
                    $query->where('cos_payrolls.employee_number', 'like', $term)
                        ->orWhere('cos_payrolls.position', 'like', $term)
                        ->orWhere('cos_payrolls.office_division', 'like', $term)
                        ->orWhere('cos_payrolls.department', 'like', $term);
                })
                ->orWhere('users.name', 'like', $term)
                ->orWhere('users.email', 'like', $term);
        });
    }
}
