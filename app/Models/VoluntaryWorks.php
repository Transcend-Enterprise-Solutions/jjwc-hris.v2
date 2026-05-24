<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VoluntaryWorks extends Model
{
    use HasFactory;

    protected $table = 'voluntary_works';

    protected $fillable = [
        'user_id',
        'org_name',
        'org_address',
        'start_date',
        'end_date',
        'toPresent',
        'no_of_hours',
        'position_nature',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
