<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfficeDivisionUnits extends Model
{
    use HasFactory;

    protected $table = 'office_division_units';

    protected $fillable = [
        'office_division_id',
        'unit',
        'sign_name',
        'sign_pos'
    ];

    public function division()
    {
        return $this->belongsTo(OfficeDivisions::class, 'office_division_id');
    }

    public function positions()
    {
        return $this->hasMany(Positions::class, 'unit_id');
    }
}
