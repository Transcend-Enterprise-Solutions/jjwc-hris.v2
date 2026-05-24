<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeesSpouse extends Model
{
    use HasFactory;

    protected $table = 'employees_spouse';

    protected $fillable = [
        'user_id',
        'first_name',
        'middle_name',
        'surname',
        'name_extension',
        'birth_date',
        'occupation',
        'employer',
        'business_address',
        'tel_number',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
