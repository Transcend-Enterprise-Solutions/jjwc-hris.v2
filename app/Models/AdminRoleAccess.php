<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminRoleAccess extends Model
{
    use HasFactory;

    protected $table = 'admin_role_accesses';
    
    protected $fillable = [
        'role_name',
        'role_code',
        'hierarchy',
        'modules'
    ];

    /**
     * Convert comma-separated module IDs to array
     */
    public function getAccessibleModulesAttribute()
    {
        if (empty($this->modules)) {
            return [];
        }
        
        return array_map('intval', explode(',', $this->modules));
    }

    /**
     * Get SystemModule objects for accessible modules
     */
    public function getModuleObjectsAttribute()
    {
        $moduleIds = $this->accessible_modules;
        
        if (empty($moduleIds)) {
            return collect();
        }
        
        return SystemModules::whereIn('id', $moduleIds)->get();
    }

    /**
     * Set modules from array to comma-separated string
     */
    public function setModulesAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['modules'] = implode(',', $value);
        } else {
            $this->attributes['modules'] = $value;
        }
    }
}
