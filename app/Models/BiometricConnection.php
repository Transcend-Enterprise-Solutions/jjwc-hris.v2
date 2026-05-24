<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BiometricConnection extends Model
{
    use HasFactory;

    protected $fillable = [
        'username',
        'password',
        'auth_url',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'username',
        'password',
        'auth_url',
    ];
}
