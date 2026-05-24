<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfficialBusiness extends Model
{
    use HasFactory;

    protected $table = 'official_businesses';

    protected $fillable = [
        'user_id',
        'reference_number',
        'company',
        'address',
        'lat',        
        'lng',        
        'date',  
        'duration',  
        'time_start',  
        'time_end',  
        'time_in',  
        'time_out',  
        'purpose',  
        'status',  
        'attachment',
        'attachment_original_name',
        'sup_approver',  
        'date_sup_approved',  
        'sup_disapprover',  
        'date_sup_disapproved',  
        'approver',  
        'date_approved',  
        'disapprover',  
        'date_disapproved',  
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function dates()
    {
        return $this->hasMany(OfficialBusinessDate::class, 'official_business_id');
    }

    public function scopeSearch($query, $term){
        $term = "%$term%";
        $query->where(function ($query) use ($term) {
            $query->where('reference_number', 'like', $term)
                ->orWhere('company', 'like', $term)
                ->orWhere('address', 'like', $term)
                ->orWhere('purpose', 'like', $term);
        });
    }
}
