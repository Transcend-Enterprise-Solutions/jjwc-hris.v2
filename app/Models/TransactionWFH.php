<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionWFH extends Model
{
    use HasFactory;

    protected $table = 'transactions_wfh';
    protected $fillable = [
        'emp_code',
        'punch_time',
        'punch_state',
        'punch_state_display',
        'verify_type',
        'verify_type_display',
        'area_alias',
        'upload_time',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'emp_code', 'emp_code');
    }
}
