<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NonAcadDistinctions extends Model
{
    use HasFactory;

    protected $table = 'non_acad_distinctions';

    protected $fillable = [
        'user_id',
        'award',
        'ass_org_name',
        'date_received',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
