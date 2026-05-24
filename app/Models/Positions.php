<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Positions extends Model
{
    use HasFactory;

    protected $table = 'positions';

    protected $fillable = [
        'office_division_id',
        'unit_id',
        'position',
        'level'
    ];

    public function officeDivisions(){
        return $this->belongsTo(OfficeDivisions::class);
    }

    public function user(){
        return $this->hasOne(User::class, 'position_id');
    }

    public function idSignatories()
    {
        return $this->hasMany(IdSignatory::class, 'position_id');
    }
}
