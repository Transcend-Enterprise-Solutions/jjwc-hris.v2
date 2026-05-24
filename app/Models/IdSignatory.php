<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IdSignatory extends Model
{
    use HasFactory;

    protected $table = 'id_signatories';
    protected $fillable = [
        'name',
        'position_id',
        'office_division_id',
        'signature_path',
        'is_default',
    ];
    
        public function position()
    {
        return $this->belongsTo(Positions::class);
    }

    public function officeDivision()
    {
        return $this->belongsTo(OfficeDivisions::class);
    }
}
