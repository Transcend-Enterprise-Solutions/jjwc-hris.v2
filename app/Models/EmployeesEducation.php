<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeesEducation extends Model
{
    use HasFactory;

    protected $table = 'employees_education';

    protected $fillable = [
        'user_id',
        'level_code',
        'level',
        'name_of_school',
        'basic_educ_degree_course',
        'from',
        'to',
        'toPresent',
        'highest_level_unit_earned',
        'year_graduated',
        'award',
        'is_bachelor',
        'is_master',
        'is_doctor',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
