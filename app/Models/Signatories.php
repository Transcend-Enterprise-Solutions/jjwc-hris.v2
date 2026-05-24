<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Signatories extends Model
{
    use HasFactory;

    protected $table = 'signatories';

    protected $fillable = [
        'user_id',
        'signatory',
        'signatory_type',
        'signature',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function scopeSearch($query, $term){
        $term = "%$term%";
        $query->where(function ($query) use ($term) {
            $query->where('signatories.signatory', 'like', $term)
            ->orWhere('signatories.signatory_type', 'like', $term);
        });
    }
}
