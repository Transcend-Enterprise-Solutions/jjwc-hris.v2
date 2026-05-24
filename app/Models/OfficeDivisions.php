<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfficeDivisions extends Model
{
    use HasFactory;

    protected $table = 'office_divisions';

    protected $fillable = [
        'office_division',
        'sign_name',
        'sign_pos'
    ];

    public function officeDivisionUnits(){
        return $this->hasMany(OfficeDivisionUnits::class, 'office_division_id');
    }

    public function positions(){
        return $this->hasMany(Positions::class, 'office_division_id')->whereNull('unit_id')->where('position', '!=', 'Super Admin');
    }

    public function idSignatories()
    {
        return $this->hasMany(IdSignatory::class, 'office_division_id');
    }


    public function scopeSearch($query, $term){
        $term = "%$term%";
        $query->where(function ($query) use ($term) {
            $query->where('office_divisions.office_division', 'like', $term);
        });
    }
    public function units()
    {
        return $this->hasMany(OfficeDivisionUnits::class, 'office_division_id');
    }

}
