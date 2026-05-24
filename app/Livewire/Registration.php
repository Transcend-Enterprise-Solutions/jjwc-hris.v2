<?php

namespace App\Livewire;

use App\Models\Countries;
use Exception;
use Livewire\Component;
use App\Models\User;
use App\Models\PhilippineProvinces;
use App\Models\PhilippineCities;
use App\Models\PhilippineBarangays;
use App\Models\Positions;
use App\Models\OfficeDivisions;
use App\Models\OfficeDivisionUnits;
use Illuminate\Support\Facades\DB;
use App\Models\LeaveCredits;
use App\Models\RegistrationOtp;
use App\Models\UserData;
use App\Models\UserDataPrivacyConsent;

class Registration extends Component
{
    private $active_status = 1;
    public $emp_code;
    public $pwd=0;
    public $positions = [];
    public $units = [];
    public $officeDivisions;
    public $selectedPosition= null;
    public $selectedOfficeDivision= null;
    public $selectedUnit = null;
    public $date_hired;
    public $appointment;
    public $itemNumber;
    public $data_of_assumption;
    public $countries;

    #Step 1
    public $first_name;
    public $middle_name;
    public $surname;
    public $name_extension;
    public $sex;
    public $otherSex;
    public $date_of_birth;
    public $place_of_birth;
    public $citizenship;
    public $dual_citizenship_type;
    public $dual_citizenship_country;
    public $civil_status;
    public $height;
    public $weight;
    public $blood_type;

    #Step 2
    public $gsis;
    public $gsis1;
    public $gsis2;
    public $gsis3;

    public $umid;
    public $umid1;
    public $umid2;
    public $umid3;


    public $pagibig;
    public $pagibig1;
    public $pagibig2;
    public $pagibig3;


    public $philhealth;
    public $philhealth1;
    public $philhealth2;
    public $philhealth3;

    public $philsys;

    public $sss;
    public $sss1;
    public $sss2;
    public $sss3;

    public $tin;
    public $tin1;
    public $tin2;
    public $tin3;
    public $tin4;

    public $agency_employee_no;

    #Step 3
    public $permanent_selectedRegion;
    public $permanent_selectedProvince;
    public $permanent_selectedCity;
    public $permanent_selectedBarangay;
    public $p_house;
    public $p_street;
    public $p_subdivision;
    public $residential_selectedRegion;
    public $residential_selectedProvince;
    public $residential_selectedCity;
    public $residential_selectedBarangay;
    public $r_house;
    public $r_street;
    public $r_subdivision;
    public $permanent_selectedZipcode;
    public $residential_selectedZipcode;
    public $regions;
    public $pprovinces;
    public $rprovinces;
    public $pcities;
    public $rcities;
    public $pbarangays;
    public $rbarangays;
    public $tel_number;
    public $mobile_number;
    public $email;
    public $same_as_above = false;
    public $password;
    public $c_password;
    public $step = 3;
    public $otp = ['', '', '', '', '', ''];
    public $verificationError;
    public $verificationEmail;
    public $canRegister = true;
    public $consented = false;

    public function mount(){
        $this->getProvicesAndCities();
        $this->officeDivisions = OfficeDivisions::all();
        $this->positions = collect();
        $this->countries = Countries::all();
    }

    public function toStep2()
    {
        if($this->citizenship == 'Dual Citizenship'){
            $this->validate([
                'dual_citizenship_type' => 'required',
                'dual_citizenship_country' => 'required'
            ]);
        }else{
            $this->dual_citizenship_type = null;
            $this->dual_citizenship_country = null;
        }

        $this->validate([
            'first_name' => 'required|min:2',
            'middle_name' => 'nullable',
            'surname' => 'required|min:2',
            'name_extension' => 'nullable',
            'sex' => 'required',
            'otherSex' => [
                'required_if:sex,Others',
                'nullable',
            ],
            'date_of_birth' => 'required|date|before:today',
            'place_of_birth' => 'required',
            'citizenship' => 'required',
            'civil_status' => 'required',
            'height' => 'required|numeric',
            'weight' => 'required|numeric',
        ]);

        $this->step++;

    }

    public function toStep3()
    {
        // $this->validate([
        //     'gsis' => 'required',
        //     'pagibig' => 'required',
        //     'philhealth' => 'required',
        //     'sss' => 'required',
        //     'tin' => 'required',
        //     'agency_employee_no' => 'required',
        // ]);

        $this->gsis = ($this->gsis1 && $this->gsis2 && $this->gsis3) ? ($this->gsis1 . '-' . $this->gsis2 . '-' . $this->gsis3) : ''; 
        $this->umid = ($this->umid1 && $this->umid2 && $this->umid3) ? ($this->umid1 . '-' . $this->umid2 . '-' . $this->umid3) : ''; 
        $this->pagibig = ($this->pagibig1 && $this->pagibig2 && $this->pagibig3) ? ($this->pagibig1 . '-' . $this->pagibig2 . '-' . $this->pagibig3) : '';
        $this->philhealth = ($this->philhealth1 && $this->philhealth2 && $this->philhealth3) ? ($this->philhealth1 . '-' . $this->philhealth2 . '-' . $this->philhealth3) : '';
        $this->sss = ($this->sss1 && $this->sss2 && $this->sss3) ? ($this->sss1 . '-' . $this->sss2 . '-' . $this->sss3) : '';
        $this->tin = ($this->tin1 && $this->tin2 && $this->tin3) ? ($this->tin1 . '-' . $this->tin2 . '-' . $this->tin3 . '-' . ($this->tin4 ?: '00000')) : '';

        $this->step++;
    }

    public function prevStep()
    {
        $this->step--;
    }

    public function submit()
    {
        $this->validate([
            'permanent_selectedZipcode' => 'required',
            'permanent_selectedProvince' => 'required',
            'permanent_selectedCity' => 'required',
            'permanent_selectedBarangay' => 'required',
            'mobile_number' => ['required', 'regex:/^\+639\d{9}$|^\d{11}$/'],
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'c_password' => 'required|same:password',
            'emp_code' => 'required|unique:users,emp_code',
            'selectedPosition' => 'required|exists:positions,id',
            'selectedOfficeDivision' => 'required|exists:office_divisions,id',
            'date_hired' => 'required|date',
            'appointment' => 'required',
            'consented' => 'accepted',
        ]);

        if($this->same_as_above){
            $this->residential_selectedZipcode = $this->permanent_selectedZipcode;
            $this->residential_selectedProvince = $this->permanent_selectedProvince;
            $this->residential_selectedCity = $this->permanent_selectedCity;
            $this->residential_selectedBarangay = $this->permanent_selectedBarangay;
            $this->r_house = $this->p_house;
            $this->r_street = $this->p_street;
            $this->r_subdivision = $this->p_subdivision;
        }else{ 
            $this->validate([
                'residential_selectedZipcode' => 'required',
                'residential_selectedProvince' => 'required',
                'residential_selectedCity' => 'required',
                'residential_selectedBarangay' => 'required',
            ]);
        }
    
        if ($this->p_house == null && $this->p_street == null && $this->p_subdivision == null) {
            $this->addError('p_subdivision', 'Please add either House/Block/Lot No. or Street or Subdivision/Village.');
            return;
        }
    
        if ($this->r_house == null && $this->r_street == null && $this->r_subdivision == null) {
            $this->addError('r_subdivision', 'Please add either House/Block/Lot No. or Street or Subdivision/Village.');
            return;
        }
    
        if (!$this->isPasswordComplex($this->password)) {
            $this->addError('password', 'The password must contain at least one uppercase letter, one number, and one special character.');
            return;
        }
    
        DB::beginTransaction();

        // Capitalize the first letter of each word for the names
        $this->first_name = ucwords(strtolower($this->first_name));
        $this->middle_name = ucwords(strtolower($this->middle_name));
        $this->surname = ucwords(strtolower($this->surname));

        $middleInitial = $this->middle_name ? strtoupper(substr($this->middle_name, 0, 1)) . '.' : '';
        $fullName = trim($this->first_name . ' ' . ($middleInitial ? $middleInitial . ' ' : '') . $this->surname) . ($this->name_extension ? ' ' . $this->name_extension : '');

        try {
            // $this->emp_code = str_replace('-', '', $this->emp_code); // Remove hyphens
            // $this->emp_code = str_replace('D', '1', $this->emp_code); // Replace 'D' with '1'
            
            // Creating the user
            $user = User::create([
                'name' => $fullName,
                'email' => $this->email,
                'password' => $this->password,
                'user_role' => 'emp',
                'active_status' => $this->active_status,
                'emp_code' => $this->emp_code,
                'position_id' => $this->selectedPosition,
                'office_division_id' => $this->selectedOfficeDivision,
                'unit_id' => $this->selectedUnit,
            ]);
    
            // Preparing user data fields
            $p_house_street = $this->p_house ?? "N/A" . ',' . $this->p_street ?? "N/A" . ',' . $this->p_subdivision ?? "N/A";
            $r_house_street = $this->r_house ?? "N/A" . ',' . $this->r_street ?? "N/A" . ',' . $this->r_subdivision ?? "N/A";
            $sexValue = $this->getSexValue();
    
            // Creating user data
            UserData::create([
                'user_id' => $user->id,
                'first_name' => $this->first_name,
                'middle_name' => $this->middle_name,
                'surname' => $this->surname,
                'name_extension' => $this->name_extension,
                'sex' => $sexValue,
                'email' => $this->email,
                'date_of_birth' => $this->date_of_birth,
                'place_of_birth' => $this->place_of_birth,
                'citizenship' => $this->citizenship,
                'dual_citizenship_type' => $this->dual_citizenship_type,
                'dual_citizenship_country' => $this->dual_citizenship_country,
                'civil_status' => $this->civil_status,
                'height' => $this->height,
                'weight' => $this->weight,
                'blood_type' => $this->blood_type,
                'umid' => $this->umid ?: null,
                'gsis' => $this->gsis ?: null,
                'pagibig' => $this->pagibig ?: null,
                'philhealth' => $this->philhealth ?: null,
                'philsys' => $this->philsys ?: null,
                'sss' => $this->sss ?: null,
                'tin' => $this->tin ?: null,
                'agency_employee_no' => $this->agency_employee_no ?: null,
                'permanent_selectedZipcode' => $this->permanent_selectedZipcode,
                'permanent_selectedProvince' => $this->permanent_selectedProvince,
                'permanent_selectedCity' => $this->permanent_selectedCity,
                'permanent_selectedBarangay' => $this->permanent_selectedBarangay,
                'p_house_street' => $p_house_street,
                'residential_selectedZipcode' => $this->residential_selectedZipcode,
                'residential_selectedProvince' => $this->residential_selectedProvince,
                'residential_selectedCity' => $this->residential_selectedCity,
                'residential_selectedBarangay' => $this->residential_selectedBarangay,
                'r_house_street' => $r_house_street,
                'tel_number' => $this->tel_number,
                'mobile_number' => $this->mobile_number,
                'pwd' => $this->pwd,
                'date_hired' => $this->date_hired,
                'appointment' => $this->appointment,
                'item_number' => $this->itemNumber,
            ]);

            // Create data privacy consent record
            $this->giveConsent($user->id);

            // Creating Leave Credits
            LeaveCredits::create([
                'user_id' => $user->id,
                'vl_total_credits' => 0,
                'sl_total_credits' => 0,
                'spl_total_credits' => 0,
                'vl_claimable_credits' => 0,
                'sl_claimable_credits' => 0,
                'spl_claimable_credits' => 0,
                'vl_claimed_credits' => 0,
                'sl_claimed_credits' => 0,
                'spl_claimed_credits' => 0,
                'cto_total_credits' => 0,
                'cto_claimable_credits' => 0,
                'cto_claimed_credits' => 0,
                'fl_claimable_credits' => 0,
                'fl_claimed_credits' => 0,
                'vlbalance_brought_forward' => null,
                'slbalance_brought_forward' => null,
                'date_forwarded' => null,
                'credits_transferred' => 0,
                'credits_inputted' => 0,
            ]);

            $enteredOtp = implode('', $this->otp);
            $verified = RegistrationOtp::where('otp', $enteredOtp)
                        ->where('email', $this->verificationEmail)
                        ->first();

            if($verified){
                $verified->update([
                    'status' => 1,
                    'user_id' => $user->id,
                ]);
            }


    
            DB::commit();
            session()->flash('message', 'Registration successful!');
            return redirect()->route('login');
        } catch (Exception $e) {
            DB::rollBack();
            $this->addError('submit', 'There was an error processing your registration. Please try again.');
        }
    }

    private function giveConsent($userId){
        UserDataPrivacyConsent::create([
            'user_id' => $userId,
            'consent_status' => true
        ]);
    }

    public function updatedSelectedOfficeDivision($officeDivisionId)
    {

        $this->units = OfficeDivisionUnits::where('office_division_id', $officeDivisionId)->get();

        $this->selectedUnit = null;
        $this->fetchPositions();
    }

    public function updatedSelectedUnit($unitId)
    {
        $this->fetchPositions();
    }
    private function fetchPositions()
    {
        if ($this->selectedUnit) {
            $this->positions = Positions::where('unit_id', $this->selectedUnit)->get();
        } else {
            $this->positions = Positions::where('office_division_id', $this->selectedOfficeDivision)
                ->whereNull('unit_id')
                ->get();
        }
    }

    public function render()
    {
        if ($this->permanent_selectedProvince != null) {
            $pprovinceCode = PhilippineProvinces::where('province_description', $this->permanent_selectedProvince)
                            // ->where('region_code', isset($regionCode['region_code']) ? $regionCode['region_code'] : '')
                            ->select('province_code')->first();
            $pprovinceCode = $pprovinceCode ? $pprovinceCode->getAttributes() : [];
            $this->pcities = PhilippineCities::where('province_code', isset($pprovinceCode['province_code']) ? $pprovinceCode['province_code'] : '')->get();
        }

        if ($this->residential_selectedProvince != null) {
            $rprovinceCode = PhilippineProvinces::where('province_description', $this->residential_selectedProvince)
                            // ->where('region_code', isset($regionCode['region_code']) ? $regionCode['region_code'] : '')
                            ->select('province_code')->first();
            $rprovinceCode = $rprovinceCode ? $rprovinceCode->getAttributes() : [];
            $this->rcities = PhilippineCities::where('province_code', isset($rprovinceCode['province_code']) ? $rprovinceCode['province_code'] : '')->get();
        }

        if ($this->permanent_selectedCity != null) {
            $pcityCode = PhilippineCities::where('city_municipality_description', $this->permanent_selectedCity)
                            ->where('province_code', isset($pprovinceCode['province_code']) ? $pprovinceCode['province_code'] : '')
                            ->select('city_municipality_code')->first();
            $pcityCode = $pcityCode ? $pcityCode->getAttributes() : [];
            $this->pbarangays = PhilippineBarangays::where('city_municipality_code', isset($pcityCode['city_municipality_code']) ? $pcityCode['city_municipality_code'] : '')->get();
        }

        if ($this->residential_selectedCity != null) {
            $rcityCode = PhilippineCities::where('city_municipality_description', $this->residential_selectedCity)
                            ->where('province_code', isset($rprovinceCode['province_code']) ? $rprovinceCode['province_code'] : '')
                            ->select('city_municipality_code')->first();
            $rcityCode = $rcityCode ? $rcityCode->getAttributes() : [];
            $this->rbarangays = PhilippineBarangays::where('city_municipality_code', isset($rcityCode['city_municipality_code']) ? $rcityCode['city_municipality_code'] : '')->get();
        }

        return view('livewire.registration',[
            'pprovinces' => $this->pprovinces,
            'rprovinces' => $this->pprovinces,
            'pcities' => $this->pcities,
            'rcities' => $this->rcities,
            'pbarangays' => $this->pbarangays,
            'rbarangays' => $this->rbarangays,
        ]);
    }

    public function getProvicesAndCities(){
        $this->pprovinces = PhilippineProvinces::all();
        $this->pcities = collect();
        $this->rcities = collect();
        $this->pbarangays = collect();
        $this->rbarangays = collect();
    }

    public function updatedSameAsAbove($value)
    {
        if ($value) {
            $this->residential_selectedZipcode = $this->permanent_selectedZipcode;
            $this->residential_selectedProvince = $this->permanent_selectedProvince;
            $this->residential_selectedCity = $this->permanent_selectedCity;
            $this->residential_selectedBarangay = $this->permanent_selectedBarangay;
            $this->r_house = $this->p_house;
            $this->r_street = $this->p_street;
            $this->r_subdivision = $this->p_subdivision;
        } else {
            $this->residential_selectedZipcode = null;
            $this->residential_selectedProvince = null;
            $this->residential_selectedCity = null;
            $this->residential_selectedBarangay = null;
            $this->r_house = null;
            $this->r_street = null;
            $this->r_subdivision = null;
        }
    }

    protected $messages = [
        'password.required' => 'The password field is required.',
        'password.min' => 'The password must be at least 8 characters long.',
        'c_password.required' => 'The password confirmation field is required.',
        'c_password.same' => 'The password confirmation does not match the password.',
    ];

    private function isPasswordComplex($password){
        $containsUppercase = preg_match('/[A-Z]/', $password);
        $containsNumber = preg_match('/\d/', $password);
        $containsSpecialChar = preg_match('/[^A-Za-z0-9]/', $password); // Changed regex to include special characters
        return $containsUppercase && $containsNumber && $containsSpecialChar;
    }

    public function getSexValue()
    {
        if ($this->sex === 'Others' && $this->otherSex) {
            return $this->otherSex;
        }
        return $this->sex;
    }

    public function verify(){
        try{
            $this->validate([
                'verificationEmail' => 'required|email',
                'otp' => ['required', function ($attribute, $value, $fail) {
                    if (count($value) !== 6 || in_array(null, $value, true) || in_array('', $value, true)) {
                        $fail('The OTP must be fully entered.');
                    }
                }]
            ]);

            $enteredOtp = implode('', $this->otp);

            $verified = RegistrationOtp::where('otp', $enteredOtp)
                            ->where('email', $this->verificationEmail)
                            ->first();

            if($verified){
                $this->canRegister = true;
            }else{
                $this->verificationError = 'Incorrect email or one-time password (OTP).';
            }
        }catch(Exception $e){
            throw $e;
        }
    }
}
