<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalaryGrade extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $table = 'salary_grades';
    protected $fillable = [
        'salary_grade',
        'step1',
        'step2',
        'step3',
        'step4',
        'step5',
        'step6',
        'step7',
        'step8',
    ];
}
