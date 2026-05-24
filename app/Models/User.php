<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use App\Notifications\CustomResetPasswordNotification;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'user_role',
        'active_status',
        'emp_code',
        'position_id',
        'office_division_id',
        'unit_id',
        'profile_photo_path',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        // 'name',
        // 'email',
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    public function userData()
    {
        return $this->hasOne(UserData::class);
    }

    public function officeDivision()
    {
        return $this->belongsTo(OfficeDivisions::class, 'office_division_id', 'id');
    }    

    public function officeDivisionUnit()
    {
        return $this->belongsTo(OfficeDivisionUnits::class, 'unit_id', 'id');
    }    

    public function position()
    {
        return $this->belongsTo(Positions::class, 'position_id');
    }

    public function admin()
    {
        return $this->hasOne(Admin::class);
    }

    public function eligibility()
    {
        return $this->hasMany(Eligibility::class)->orderBy('date', 'DESC');
    }

    public function workExperience()
    {
        return $this->hasMany(WorkExperience::class)
            ->orderByRaw("
                CASE 
                    WHEN toPresent = 'Present' OR end_date IS NULL THEN 0 
                    ELSE 1 
                END ASC
            ")
            ->orderBy('start_date', 'DESC') 
            ->orderBy('end_date', 'DESC'); 
    }

    public function employeesChildren()
    {
        return $this->hasMany(EmployeesChildren::class)->orderBy('childs_birth_date', 'ASC');
    }

    public function employeesSpouse()
    {
        return $this->hasOne(EmployeesSpouse::class);
    }

    public function employeesFather()
    {
        return $this->hasOne(EmployeesFather::class);
    }

    public function employeesMother()
    {
        return $this->hasOne(EmployeesMother::class);
    }

    public function employeesEducation()
    {
        return $this->hasMany(EmployeesEducation::class)->orderBy('level_code', 'ASC');
    }

    public function voluntaryWorks()
    {
        return $this->hasMany(VoluntaryWorks::class)->orderBy('end_date', 'DESC');
    }

    public function learningAndDevelopment()
    {
        return $this->hasMany(LearningAndDevelopment::class)->orderBy('end_date', 'DESC');
    }

    public function skills()
    {
        return $this->hasMany(Skills::class);
    }

    public function hobbies()
    {
        return $this->hasMany(Hobbies::class);
    }

    public function nonAcadDistinctions()
    {
        return $this->hasMany(NonAcadDistinctions::class)->orderBy('date_received', 'DESC');
    }

    public function assOrgMembership()
    {
        return $this->hasMany(AssOrgMemberships::class);
    }

    public function charReferences()
    {
        return $this->hasMany(CharReferences::class);
    }

    public function workExperienceSheet()
    {
        return $this->hasMany(WorkExperienceSheetTable::class, 'user_id');
    }

    public function serviceRecords()
    {
        return $this->hasMany(ServiceRecords::class, 'user_id');
    }

    public function employeeDocuments()
    {
        return $this->hasMany(EmployeeDocument::class);
    }

    public function docRequests()
    {
        return $this->hasMany(DocRequest::class);
    }

    public function userSchedule()
    {
        return $this->hasMany(Schedule::class);
    }

    public function leaveApplication()
    {
        return $this->hasMany(LeaveApplication::class);
    }

    public function dtrSchedules()
    {
        return $this->hasMany(DTRSchedule::class, 'emp_code', 'emp_code');
    }
    
    public function employeesDtr()
    {
        return $this->hasMany(EmployeesDtr::class);
    }

    public function leaveCredits()
    {
        return $this->hasOne(LeaveCredits::class);
    }

    public function wfhLocations()
    {
        return $this->hasOne(WfhLocation::class);
    }
    public function wfhLocationRequests()
    {
        return $this->hasMany(WfhLocationRequests::class);
    }

    public function wfhMonitoringSessions()
    {
        return $this->hasMany(WfhMonitoringSessionRecord::class);
    }

    public function signatories()
    {
        return $this->hasMany(Signatories::class);
    }
    public function notifications()
    {
        // Assuming notifications are related to user via `user_id`
        return $this->hasMany(Notification::class, 'user_id');
    }

    public function pdsC4Answers(){
        return $this->hasMany(PdsC4Answers::class);
    }

    public function pdsGovIssuedId(){
        return $this->hasOne(PdsGovIssuedId::class);
    }

    public function pdsPhoto(){
        return $this->hasOne(PdsPhoto::class);
    }

    public function monetizationRequest()
    {
        return $this->hasMany(MonetizationRequest::class);
    }

    public function leaveApprovals()
    {
        return $this->hasMany(LeaveApprovals::class, 'application_id');
    }

    public function monthlyCredits()
    {
        return $this->hasMany(MonthlyCredits::class);
    }

    public function officialBusiness()
    {
        return $this->hasMany(OfficialBusiness::class);
    }
    
    public function mandatoryFormRequest()
    {
        return $this->hasMany(MandatoryFormRequest::class);

    }
    
    public function registrationOtp()
    {
        return $this->hasMany(RegistrationOtp::class);

    }

    public function emergencyContact()
    {
        return $this->hasMany(EmergencyContact::class, 'user_id');
    }

    public function dataPrivacyConsent()
    {
        return $this->hasOne(UserDataPrivacyConsent::class, 'user_id');
    }

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];


    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    public function scopeSearch($query, $term){
        $term = "%$term%";
        $query->where(function ($query) use ($term) {
            $query->where('positions.position', 'like', $term)
                ->orWhere('users.emp_code', 'like', $term)
                ->orWhere('users.name', 'like', $term)
                ->orWhere('office_divisions.office_division', 'like', $term);
        });
    }

    public function scopeSearch2($query, $term){
        $term = "%$term%";
        $query->where(function ($query) use ($term) {
            $query->where('users.emp_code', 'like', $term)
                ->orWhere('users.name', 'like', $term);
        });
    }

    public function scopeSearch3($query, $term){
        $term = "%$term%";
        $query->where(function ($query) use ($term) {
            $query->where('official_businesses.reference_number', 'like', $term)
                ->orWhere('user_data.first_name', 'like', $term)
                ->orWhere('user_data.middle_name', 'like', $term)
                ->orWhere('user_data.name_extension', 'like', $term)
                ->orWhere('user_data.surname', 'like', $term);
        });
    }

    public function scopeSearch4($query, $term){
        $term = "%$term%";
        $query->where(function ($query) use ($term) {
            $query->where('users.name', 'like', $term)
                ->orWhere('users.emp_code', 'like', $term);
        });
    }

    public function scopeSearch5($query, $term){
        $term = "%$term%";
        $query->where(function ($query) use ($term) {
            $query->where('official_businesses.reference_number', 'like', $term)
                ->orWhere('official_businesses.company', 'like', $term)
                ->orWhere('user_data.surname', 'like', $term)
                ->orWhere('user_data.first_name', 'like', $term)
                ->orWhere('user_data.middle_name', 'like', $term)
                ->orWhere('official_businesses.address', 'like', $term)
                ->orWhere('official_businesses.purpose', 'like', $term);
        });
    }

    public function scopeSearch6($query, $term){
        $term = "%$term%";
        $query->where(function ($query) use ($term) {
            $query->where('users.emp_code', 'like', $term)
                ->orWhere('users.name', 'like', $term);
        });
    }

    public function adminAccount(){
        return $this->hasOne(User::class, 'name', 'name')->where('user_role', '!=', 'emp');
    }

    public function contracts()
    {
        return $this->hasMany(EmployeesContract::class)->orderBy('start_date', 'desc');
    }

    public function currentContract()
    {
        return $this->hasOne(EmployeesContract::class)
                    ->where('status', 'active')
                    ->where('start_date', '<=', now())
                    ->where(function($query) {
                        $query->whereNull('end_date')
                              ->orWhere('end_date', '>=', now());
                    })
                    ->orderBy('start_date', 'desc');
    }

    public function activeContracts()
    {
        return $this->hasMany(EmployeesContract::class)
                    ->where('status', 'active')
                    ->orderBy('start_date', 'desc');
    }

    public function salary()
    {
        return $this->hasOne(EmployeeSalary::class, 'user_id');
    }

    // Helper methods
    public function hasCurrentContract(): bool
    {
        return $this->currentContract()->exists();
    }

    public function getCurrentContractType(): ?string
    {
        return $this->currentContract?->contract_type;
    }


    // Account switching section ------------------------------------------------------------------------------------------------------------ //

     /**
     * Get the base employee code (without prefix)
     */
    public function getBaseEmpCodeAttribute()
    {
        // Handle cases like 'sv-12345', 'hr-12345', etc.
        $parts = explode('-', $this->emp_code);
        return count($parts) > 1 ? $parts[1] : $this->emp_code;
    }

    /**
     * Check if this is an admin account
     */
    public function isAdminAccount()
    {
        return strpos($this->emp_code, '-') !== false;
    }

    /**
     * Get the admin account prefix (sv, hr, etc.)
     */
    public function getAdminPrefixAttribute()
    {
        if ($this->isAdminAccount()) {
            $parts = explode('-', $this->emp_code);
            return $parts[0];
        }
        return null;
    }

    /**
     * Find the corresponding employee account for this admin
     */
    public function getEmployeeAccount()
    {
        if ($this->isAdminAccount()) {
            return self::where('emp_code', $this->base_emp_code)->first();
        }
        return null;
    }

    /**
     * Find all admin accounts for this employee
     */
    public function getAdminAccounts()
    {
        if (!$this->isAdminAccount()) {
            return self::where('emp_code', 'LIKE', '%-' . $this->emp_code)->get();
        }
        return collect();
    }

    /**
     * Get the switchable account (employee <-> admin)
     */
    public function getSwitchableAccounts()
    {
        if ($this->isAdminAccount()) {
            // If current is admin, return employee account
            $employee = $this->getEmployeeAccount();
            return $employee ? collect([$employee]) : collect();
        } else {
            // If current is employee, return all admin accounts
            return $this->getAdminAccounts();
        }
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new CustomResetPasswordNotification($token));
    }
}
