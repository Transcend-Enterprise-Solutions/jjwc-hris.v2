<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PdsGovIssuedId extends Model
{
    use HasFactory;

    protected $table = 'pds_gov_issued_id';

    protected $fillable = [
        'user_id',
        'gov_id',
        'id_number',
        'date_of_issuance', 
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }
}
