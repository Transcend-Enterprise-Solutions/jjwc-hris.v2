<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PdsPhoto extends Model
{
    use HasFactory;

    protected $table = 'pds_photo';

    protected $fillable = [
        'user_id',
        'photo',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }
}
