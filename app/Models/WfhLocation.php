<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WfhLocation extends Model
{
    use HasFactory;

    protected $table = 'wfh_locations';

    protected $fillable = [
        'user_id',
        'address',
        'latitude',
        'longitude',
        'wfh_loc_req_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
