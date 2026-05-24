<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PdsC4Answers extends Model
{
    use HasFactory;

    protected $table = 'pds_c4_answers';

    protected $fillable = [
        'user_id',
        'question_number',
        'question_letter',
        'answer',
        'details',        
        'date_filed',        
        'status',  
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }
}
