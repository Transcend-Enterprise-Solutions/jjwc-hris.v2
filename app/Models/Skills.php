<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Skills extends Model
{
    use HasFactory;

    protected $table = 'skills';

    protected $fillable = [
        'user_id',
        'skill',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
