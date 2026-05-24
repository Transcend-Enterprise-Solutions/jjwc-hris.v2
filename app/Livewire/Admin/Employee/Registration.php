<?php

namespace App\Livewire\Admin\Employee;

use App\Models\Countries;
use App\Models\PhilippineRegions; // Add this
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
use App\Models\UserData;
use Carbon\Carbon;

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
    // ADD THESE REGION PROPERTIES
    public $permanent_selectedRegion;
    public $residential_selectedRegion;
    
    public $permanent_selectedProvince;
    public $permanent_selectedCity;
    public $permanent_selectedBarangay;
    public $p_house;
    public $p_street;
    public $p_subdivision;
    
    public $residential_selectedProvince;
    public $residential_selectedCity;
    public $residential_selectedBarangay;
    public $r_house;
    public $r_street;
    public $r_subdivision;
    public $permanent_selectedZipcode;
    public $residential_selectedZipcode;
    
    // ADD THESE COLLECTION PROPERTIES
    public $regions;
    public $pprovinces;
    public $rprovinces;
    public $pcities;
    public $rcities;
    public $pbarangays;
    public $rbarangays;
    
    public $tel_number;
    public $mobile_number;
    public $same_as_above = false;

    public $email;
    public $password;
    public $fullName;

    public $isSuccessful = false;
    public $step = 1;

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
            'blood_type' => 'required|max:3',
        ]);

        $this->step++;
    }

    public function toStep3()
    {
        $this->gsis = ($this->gsis1 && $this->gsis2 && $this->gsis3) ? ($this->gsis1 . '-' . $this->gsis2 . '-' . $this->gsis3) : ''; 
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

    public function saveEmployeee()
    {
        $this->validate([
            'permanent_selectedRegion' => 'required',
            'permanent_selectedZipcode' => 'required',
            'permanent_selectedProvince' => 'required',
            'permanent_selectedCity' => 'required',
            'permanent_selectedBarangay' => 'required',
            'mobile_number' => ['required', 'regex:/^\+639\d{9}$|^\d{11}$/'],
            'email' => 'required|email|unique:users,email',
            'emp_code' => 'required|unique:users,emp_code|regex:/^[0-9]+$/',
            'selectedPosition' => 'required|exists:positions,id',
            'selectedOfficeDivision' => 'required|exists:office_divisions,id',
            'date_hired' => 'required|date',
            'appointment' => 'required',
        ]);

        if($this->same_as_above){
            $this->residential_selectedRegion = $this->permanent_selectedRegion;
            $this->residential_selectedZipcode = $this->permanent_selectedZipcode;
            $this->residential_selectedProvince = $this->permanent_selectedProvince;
            $this->residential_selectedCity = $this->permanent_selectedCity;
            $this->residential_selectedBarangay = $this->permanent_selectedBarangay;
            $this->r_house = $this->p_house;
            $this->r_street = $this->p_street;
            $this->r_subdivision = $this->p_subdivision;
        }else{
            $this->validate([
                'residential_selectedRegion' => 'required',
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
    
        DB::beginTransaction();

        $this->first_name = ucwords(strtolower($this->first_name));
        $this->middle_name = ucwords(strtolower($this->middle_name));
        $this->surname = ucwords(strtolower($this->surname));

        $middleInitial = $this->middle_name ? strtoupper(substr($this->middle_name, 0, 1)) . '.' : '';
        $this->fullName = trim($this->first_name . ' ' . ($middleInitial ? $middleInitial . ' ' : '') . $this->surname) . ($this->name_extension ? ' ' . $this->name_extension : '');

        try {
            $this->password = $this->surname . Carbon::parse(now())->format('mdY');

            $user = User::create([
                'name' => $this->fullName,
                'email' => $this->email,
                'password' => $this->password,
                'user_role' => 'emp',
                'active_status' => $this->active_status,
                'emp_code' => $this->emp_code,
                'position_id' => $this->selectedPosition,
                'office_division_id' => $this->selectedOfficeDivision,
                'unit_id' => $this->selectedUnit,
            ]);
    
            $p_house_street = $this->p_house ?? "N/A" . ',' . $this->p_street ?? "N/A" . ',' . $this->p_subdivision ?? "N/A";
            $r_house_street = $this->r_house ?? "N/A" . ',' . $this->r_street ?? "N/A" . ',' . $this->r_subdivision ?? "N/A";
            $sexValue = $this->getSexValue();
    
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
                'gsis' => $this->gsis ?: 'N/A',
                'pagibig' => $this->pagibig ?: 'N/A',
                'philhealth' => $this->philhealth ?: 'N/A',
                'sss' => $this->sss ?: 'N/A',
                'tin' => $this->tin ?: 'N/A',
                'agency_employee_no' => $this->agency_employee_no ?: 'N/A',
                'umid' => $this->umid ?: 'N/A',
                'philsys' => $this->philsys ?: 'N/A',
                'permanent_selectedRegion' => $this->permanent_selectedRegion,
                'permanent_selectedZipcode' => $this->permanent_selectedZipcode,
                'permanent_selectedProvince' => $this->permanent_selectedProvince,
                'permanent_selectedCity' => $this->permanent_selectedCity,
                'permanent_selectedBarangay' => $this->permanent_selectedBarangay,
                'p_house_street' => $p_house_street,
                'residential_selectedRegion' => $this->residential_selectedRegion,
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
    
            DB::commit();
            
            $this->isSuccessful = true;
        } catch (Exception $e) {
            DB::rollBack();
            $this->addError('submit', 'There was an error processing your registration. Please try again. Error: ' . $e->getMessage());
        }
    }

    public function closePopup(){
        $this->reset();
        $this->resetValidation();
        return redirect()->route('/employee-management/employee-registrations');
    }
    
    public function mount(){
        $this->getRegionsProvincesAndCities();
        $this->officeDivisions = OfficeDivisions::all();
        $this->positions = collect();
        $this->countries = Countries::all();
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

    public function getRegionsProvincesAndCities(){
        $this->regions = PhilippineRegions::orderBy('region_description', 'asc')->get();
        $this->pprovinces = collect();
        $this->rprovinces = collect();
        $this->pcities = collect();
        $this->rcities = collect();
        $this->pbarangays = collect();
        $this->rbarangays = collect();
    }

    // ADD THESE NEW METHODS FOR CASCADE
    public function updatedPermanentSelectedRegion($regionDescription)
    {
        if ($regionDescription) {
            $region = PhilippineRegions::where('region_description', $regionDescription)->first();
            if ($region) {
                $this->pprovinces = PhilippineProvinces::where('region_code', $region->region_code)
                    ->orderBy('province_description', 'asc')
                    ->get();
            }
        } else {
            $this->pprovinces = collect();
        }
        
        $this->permanent_selectedProvince = null;
        $this->permanent_selectedCity = null;
        $this->permanent_selectedBarangay = null;
        $this->pcities = collect();
        $this->pbarangays = collect();
    }

    public function updatedResidentialSelectedRegion($regionDescription)
    {
        if ($regionDescription) {
            $region = PhilippineRegions::where('region_description', $regionDescription)->first();
            if ($region) {
                $this->rprovinces = PhilippineProvinces::where('region_code', $region->region_code)
                    ->orderBy('province_description', 'asc')
                    ->get();
            }
        } else {
            $this->rprovinces = collect();
        }
        
        $this->residential_selectedProvince = null;
        $this->residential_selectedCity = null;
        $this->residential_selectedBarangay = null;
        $this->rcities = collect();
        $this->rbarangays = collect();
    }

    public function updatedPermanentSelectedProvince($provinceDescription)
    {
        if ($provinceDescription) {
            $province = PhilippineProvinces::where('province_description', $provinceDescription)->first();
            if ($province) {
                $this->pcities = PhilippineCities::where('province_code', $province->province_code)->get();
            }
        } else {
            $this->pcities = collect();
        }
        
        $this->permanent_selectedCity = null;
        $this->permanent_selectedBarangay = null;
        $this->pbarangays = collect();
    }

    public function updatedResidentialSelectedProvince($provinceDescription)
    {
        if ($provinceDescription) {
            $province = PhilippineProvinces::where('province_description', $provinceDescription)->first();
            if ($province) {
                $this->rcities = PhilippineCities::where('province_code', $province->province_code)->get();
            }
        } else {
            $this->rcities = collect();
        }
        
        $this->residential_selectedCity = null;
        $this->residential_selectedBarangay = null;
        $this->rbarangays = collect();
    }

    public function updatedPermanentSelectedCity($cityDescription)
    {
        if ($cityDescription) {
            $city = PhilippineCities::where('city_municipality_description', $cityDescription)->first();
            if ($city) {
                $this->pbarangays = PhilippineBarangays::where('city_municipality_code', $city->city_municipality_code)->get();
            }
        } else {
            $this->pbarangays = collect();
        }
        
        $this->permanent_selectedBarangay = null;
    }

    public function updatedResidentialSelectedCity($cityDescription)
    {
        if ($cityDescription) {
            $city = PhilippineCities::where('city_municipality_description', $cityDescription)->first();
            if ($city) {
                $this->rbarangays = PhilippineBarangays::where('city_municipality_code', $city->city_municipality_code)->get();
            }
        } else {
            $this->rbarangays = collect();
        }
        
        $this->residential_selectedBarangay = null;
    }

    public function updatedSameAsAbove($value)
    {
        if ($value) {
            $this->residential_selectedRegion = $this->permanent_selectedRegion;
            $this->residential_selectedZipcode = $this->permanent_selectedZipcode;
            $this->residential_selectedProvince = $this->permanent_selectedProvince;
            $this->residential_selectedCity = $this->permanent_selectedCity;
            $this->residential_selectedBarangay = $this->permanent_selectedBarangay;
            $this->r_house = $this->p_house;
            $this->r_street = $this->p_street;
            $this->r_subdivision = $this->p_subdivision;
            
            // Trigger cascading for residential
            if ($this->residential_selectedRegion) {
                $this->updatedResidentialSelectedRegion($this->residential_selectedRegion);
                if ($this->residential_selectedProvince) {
                    $this->updatedResidentialSelectedProvince($this->residential_selectedProvince);
                    if ($this->residential_selectedCity) {
                        $this->updatedResidentialSelectedCity($this->residential_selectedCity);
                    }
                }
            }
        } else {
            $this->residential_selectedRegion = null;
            $this->residential_selectedZipcode = null;
            $this->residential_selectedProvince = null;
            $this->residential_selectedCity = null;
            $this->residential_selectedBarangay = null;
            $this->r_house = null;
            $this->r_street = null;
            $this->r_subdivision = null;
            $this->rprovinces = collect();
            $this->rcities = collect();
            $this->rbarangays = collect();
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
        $containsSpecialChar = preg_match('/[^A-Za-z0-9]/', $password);
        return $containsUppercase && $containsNumber && $containsSpecialChar;
    }

    public function getSexValue()
    {
        if ($this->sex === 'Others' && $this->otherSex) {
            return $this->otherSex;
        }
        return $this->sex;
    }

    public function render()
    {
        return view('livewire.admin.employee.registration', [
            'regions' => $this->regions,
            'pprovinces' => $this->pprovinces,
            'rprovinces' => $this->rprovinces,
            'pcities' => $this->pcities,
            'rcities' => $this->rcities,
            'pbarangays' => $this->pbarangays,
            'rbarangays' => $this->rbarangays,
        ]);
    }
}