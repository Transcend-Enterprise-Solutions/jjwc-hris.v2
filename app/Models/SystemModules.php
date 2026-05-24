<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemModules extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_module_id',
        'module_name',
        'module_key',
        'component_class',
        'route',
        'icon',
    ];

    public function parentModule(){
        return $this->belongsTo(ParentModules::class);
    }
}
