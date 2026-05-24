<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonetizationRequest extends Model
{
    use HasFactory;

    protected $table = 'monetization_request';

    protected $fillable = [
        'user_id',
        'vl_credits_requested',
        'sl_credits_requested',
        'vl_monetize_credits',
        'sl_monetize_credits',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
