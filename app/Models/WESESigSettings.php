<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WESESigSettings extends Model
{
    use HasFactory;

    protected $table = 'w_e_s_e_sig_settings';

    protected $fillable = [
        'user_id',
        'pos_x',
        'pos_y',
        'size',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
