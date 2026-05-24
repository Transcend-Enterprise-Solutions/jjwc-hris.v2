<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LearningAndDevelopment extends Model
{
    use HasFactory;

    protected $table = 'learning_and_development';

    protected $fillable = [
        'user_id',
        'title',
        'start_date',
        'end_date',
        'toPresent',
        'no_of_hours',
        'type_of_ld',
        'conducted_by',
        'certificate',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
