<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'document_type',
        'file_name',
        'file_path',
        'mime_type',
        'file_size',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
