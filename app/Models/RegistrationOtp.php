<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegistrationOtp extends Model
{
    use HasFactory;

    protected $fillable = [
        'otp',
        'email',
        'status',
        'user_id',
        'provided_by',
        'date_provided',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function scopeSearch($query, $term){
        $term = "%$term%";
        $query->where(function ($query) use ($term) {
            $query->where('email', 'like', $term);
        });
    }
}
