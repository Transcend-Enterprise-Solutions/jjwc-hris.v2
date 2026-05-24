<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class UserData extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'user_data';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'surname',
        'first_name',
        'middle_name',
        'name_extension',
        'date_of_birth',
        'place_of_birth',
        'civil_status',
        'citizenship',
        'dual_citizenship_type',
        'dual_citizenship_country',
        'age',
        'tel_number',
        'mobile_number',
        'email',
        'blood_type',
        'sex',
        'height',
        'weight',
        'umid',
        'pagibig',
        'philhealth',
        'philsys',
        'tin',
        'gsis',
        'sss',
        'agency_employee_no',
        'permanent_selectedZipcode',
        'permanent_selectedProvince',
        'permanent_selectedCity',
        'permanent_selectedBarangay',
        'p_house_street',
        'residential_selectedProvince',
        'residential_selectedCity',
        'residential_selectedBarangay',
        'r_house_street',
        'residential_selectedZipcode',
        'pwd',
        'ethnicity',
        'is_solo_parent',
        'solo_parent_id_no',
        'solo_parent_valid_until',
        'date_hired',
        'appointment',
        'permanent_selectedRegion',
        'residential_selectedRegion',
        'item_number',
    ];
    protected $attributes = [
        'pwd' => 0,
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function leaveApplication()
    {
        return $this->hasMany(LeaveApplication::class);
    }
}
