<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkExperienceSheetTable extends Model
{
    use HasFactory;

    protected $table = 'work_experience_sheet_tables';

    protected $fillable = [
        'user_id',
        'start_date',
        'end_date',
        'toPresent',
        'position',
        'office_unit',
        'supervisor',
        'agency_org',
        'list_accomp_cont',
        'sum_of_duties',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
