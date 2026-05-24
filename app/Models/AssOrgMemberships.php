<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssOrgMemberships extends Model
{
    use HasFactory;

    protected $table = 'ass_org_memberships';

    protected $fillable = [
        'user_id',
        'ass_org_name',
        'position',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
