<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ESignature extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'file_path', 'profile_photo_path', 'emergency_contact_name', 'emergency_contact_number'];
    protected $table = 'e_signatures';

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
