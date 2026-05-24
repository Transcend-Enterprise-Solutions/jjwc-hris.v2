<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MandatoryFormRequest extends Model
{
    use HasFactory;

    protected $table = 'mandatory_form_request';

    protected $fillable = [
        'user_id',
        'approved_by',
        'date_requested',
        'status',
        'date_completed',
    ];

    protected $casts = [
        'date_requested' => 'datetime',
        'date_completed' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
