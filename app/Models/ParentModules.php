<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParentModules extends Model
{
    use HasFactory;

    protected $fillable = [
        'module_name',
        'module_key',
        'icon',
    ];

    public function systemModules(){
        return $this->hasMany(SystemModules::class, 'parent_module_id');
    }
}
