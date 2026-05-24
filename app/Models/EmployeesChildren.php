<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeesChildren extends Model
{
    use HasFactory;

    protected $table = 'employees_children';

    protected $fillable = [
        'user_id',
        'childs_name',
        'childs_birth_date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
