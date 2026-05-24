<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeSalary extends Model
{
    use HasFactory;

    protected $table = 'employee_salaries';

     protected $fillable = [
        'user_id',
        'sg',
        'step',
        'monthly_basic_salary',
        'pera',
        'other_allowances',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
