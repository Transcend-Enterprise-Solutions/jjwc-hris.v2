<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeesFather extends Model
{
    use HasFactory;

    protected $table = 'employees_father';

    protected $fillable = [
        'user_id',
        'first_name',
        'middle_name',
        'surname',
        'name_extension'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
