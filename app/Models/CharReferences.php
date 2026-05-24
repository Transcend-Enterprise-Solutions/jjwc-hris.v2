<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CharReferences extends Model
{
    use HasFactory;

    protected $table = 'char_references';

    protected $fillable = [
        'user_id',
        'firstname',
        'middle_initial',
        'surname',
        'address',
        'tel_number',
        'mobile_number',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
