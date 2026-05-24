<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WfhLocationRequests extends Model
{
    use HasFactory;

    protected $table = 'wfh_location_requests';

    protected $fillable = [
        'user_id',
        'message',
        'attachment',
        'address',
        'curr_lat',
        'curr_lng',
        'status',
        'approver',
        'date_approved',
        'disapprover',
        'date_disapproved',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeSearch($query, $term){
        $term = "%$term%";
        $query->where(function ($query) use ($term) {
            $query->where('address', 'like', $term);
        });
    }
}
