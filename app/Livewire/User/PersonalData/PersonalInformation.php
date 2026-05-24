<?php

namespace App\Livewire\User\PersonalData;

use App\Models\Countries;
use App\Models\Ethnicity;
use App\Models\PhilippineBarangays;
use App\Models\PhilippineCities;
use App\Models\PhilippineProvinces;
use App\Models\UserData;
use Exception;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class PersonalInformation extends Component
{
    public $surname;
    public $first_name;
    public $middle_name;
    public $name_extension;
    public $date_of_birth;
    public $place_of_birth;
    public $sex;
    public $civil_status;
    public $citizenship;
    public $height;
    public $weight;
    public $blood_type;
    public $mobile_number;
    public $tel_number;
    public $umid;
    public $gsis;
    public $sss;
    public $pagibig;
    public $philhealth;
    public $philsys;
    public $tin;
    public $agency_employee_no;
    public $email;
    public $p_house_street;
    public $p_barangay;
    public $p_city;
    public $p_province;
    public $p_zipcode;
    public $r_house_street;
    public $r_barangay;
    public $r_city;
    public $r_province;
    public $r_zipcode;
    public $p_house_number;
    public $p_street;
    public $p_subdivision;
    public $r_house_number;
    public $r_street;
    public $r_subdivision;
    public $countries;
    public $dual_citizenship_type;
    public $dual_citizenship_country;
    public $pprovinces;
    public $pcities;
    public $rcities;
    public $pbarangays;
    public $rbarangays;
    public $pwd;
    public $is_solo_parent;
    public $ethnicity;
    public $ethnicities;

    public $editing = false;
    public $same_as_permanent = false;


    public function mount(){
        $this->countries = Countries::all();
        $this->ethnicities = Ethnicity::all();
    }

    public function render()
    {
        if($this->editing === true){
            $this->getProvincesAndCities();
            if ($this->p_province != null) {
                $provinceCode = PhilippineProvinces::where('province_description', $this->p_province)
                                ->select('province_code')->first();
                $provinceCode = $provinceCode->getAttributes();
                $this->pcities = PhilippineCities::where('province_code', $provinceCode['province_code'])->get();
            }
    
            if ($this->r_province != null) {
                $provinceCode = PhilippineProvinces::where('province_description', $this->r_province)
                                ->select('province_code')->first();
                $provinceCode = $provinceCode->getAttributes();
                $this->rcities = PhilippineCities::where('province_code', $provinceCode['province_code'])->get();
            }
    
            if ($this->p_city != null) {
                $cityCode = PhilippineCities::where('city_municipality_description', $this->p_city)
                                ->select('city_municipality_code')->first();
                $cityCode = $cityCode->getAttributes();
                $this->pbarangays = PhilippineBarangays::where('city_municipality_code', $cityCode['city_municipality_code'])->get();
            }
    
            if ($this->r_city != null) {
                $cityCode = PhilippineCities::where('city_municipality_description', $this->r_city)
                                ->select('city_municipality_code')->first();
                $cityCode = $cityCode->getAttributes();
                $this->rbarangays = PhilippineBarangays::where('city_municipality_code', $cityCode['city_municipality_code'])->get();
            }    
        }


        $userData = UserData::where('user_id', Auth::user()->id)->first();
        $this->citizenship = $userData->citizenship ?? '';
        $this->dual_citizenship_type = $userData->dual_citizenship_type ?? '';
        $this->dual_citizenship_country = $userData->dual_citizenship_country ?? '';

        return view('livewire.user.personal-data.personal-information', [
            'userData' => $userData,
        ]);
    }

    public function getProvincesAndCities(){
        $this->pprovinces = PhilippineProvinces::all();
        $this->pcities = collect();
        $this->rcities = collect();
        $this->pbarangays = collect();
        $this->rbarangays = collect();
    }

    public function toggleEditPersonalInfo() {
        try {
            $this->editing = true;
            $userData = UserData::where('user_id', Auth::user()->id)->first();

            $this->surname = $userData->surname ?? '';
            $this->first_name = $userData->first_name ?? '';
            $this->middle_name = $userData->middle_name ?? '';
            $this->name_extension = $userData->name_extension ?? '';
            $this->date_of_birth = $userData->date_of_birth ?? '';
            $this->place_of_birth = $userData->place_of_birth ?? '';
            $this->sex = $userData->sex ?? '';
            $this->civil_status = $userData->civil_status ?? '';
            $this->citizenship = $userData->citizenship ?? '';
            $this->height = $userData->height ?? '';
            $this->weight = $userData->weight ?? '';
            $this->blood_type = $userData->blood_type ?? '';
            $this->is_solo_parent = $userData->is_solo_parent;
            $this->pwd = $userData->pwd;
            $this->ethnicity = $userData->ethnicity ?? '';
            $this->mobile_number = $userData->mobile_number ?? '';
            $this->tel_number = $userData->tel_number ?? '';
            $this->gsis = $userData->gsis ?? '';
            $this->umid = $userData->umid ?? '';
            $this->sss = $userData->sss ?? '';
            $this->pagibig = $userData->pagibig ?? '';
            $this->philhealth = $userData->philhealth ?? '';
            $this->philsys = $userData->philsys ?? '';
            $this->tin = $userData->tin ?? '';
            $this->agency_employee_no = $userData->agency_employee_no ?? '';
            $this->email = $userData->email ?? '';
            $this->p_house_street = $userData->p_house_street ?? '';
            $this->p_zipcode = $userData->permanent_selectedZipcode ?? '';
            $this->p_province = $userData->permanent_selectedProvince ?? '';
            $this->p_city = $userData->permanent_selectedCity ?? '';
            $this->p_barangay = $userData->permanent_selectedBarangay ?? '';
            $this->r_house_street = $userData->r_house_street ?? '';
            $this->r_zipcode = $userData->residential_selectedZipcode ?? '';
            $this->r_province = $userData->residential_selectedProvince ?? '';
            $this->r_city = $userData->residential_selectedCity ?? '';
            $this->r_barangay = $userData->residential_selectedBarangay ?? '';

            $p_address_line1 = explode(',', $this->p_house_street);
            $r_address_line1 = explode(',', $this->r_house_street);

            $this->p_house_number = $p_address_line1[0] ?? '';
            $this->p_street = $p_address_line1[1] ?? '';
            $this->p_subdivision = $p_address_line1[2] ?? '';

            $this->r_house_number = $r_address_line1[0] ?? '';
            $this->r_street = $r_address_line1[1] ?? '';
            $this->r_subdivision = $r_address_line1[2] ?? '';

            $this->dual_citizenship_type = $userData->dual_citizenship_type ?? '';
            $this->dual_citizenship_country = $userData->dual_citizenship_country ?? '';

        } catch (Exception $e) {
            throw $e;
        }
    }

    public function cancelEdit(){
        $this->editing = false;
    }

    public function savePersonalInfo(){
        try{
            $user = Auth::user();
            if($user){

                $this->p_house_street = ($this->p_house_number ?: '') . ',' . ($this->p_street ?: '') . ',' . ($this->p_subdivision ?: '');
                $this->r_house_street = ($this->r_house_number ?: '') . ',' . ($this->r_street ?: '') . ',' . ($this->r_subdivision ?: '');
                if($this->citizenship === 'Dual Citizenship'){
                    $this->validate([
                        'dual_citizenship_type' => 'required',
                        'dual_citizenship_country' => 'required',
                    ]);
                }else{
                    $this->dual_citizenship_type = null;
                    $this->dual_citizenship_country = null;
                }

                $this->validate([
                    'surname' => 'required|string|max:255',
                    'first_name' => 'required|string|max:255',
                    'middle_name' => 'nullable|string|max:255',
                    'name_extension' => 'nullable|string|max:10',
                    'date_of_birth' => 'required|date',
                    'place_of_birth' => 'required|string|max:255',
                    'sex' => 'required|in:Male,Female',
                    'civil_status' => 'required|in:Single,Married,Divorced,Widowed',
                    'citizenship' => 'required',
                    'mobile_number' => 'required|string|max:15',
                    'email' => 'email|max:255',
                ]);
  
                $user->userData->update([
                    'surname' => $this->surname,
                    'first_name' => $this->first_name,
                    'middle_name' => $this->middle_name,
                    'name_extension' => $this->name_extension,
                    'date_of_birth' => $this->date_of_birth,
                    'place_of_birth' => $this->place_of_birth,
                    'sex' => $this->sex,
                    'civil_status' => $this->civil_status,
                    'citizenship' => $this->citizenship,
                    'dual_citizenship_type' => $this->dual_citizenship_type ?: null,
                    'dual_citizenship_country' => $this->dual_citizenship_country ?: null,
                    'height' => $this->height,
                    'weight' => $this->weight,
                    'blood_type' => $this->blood_type,
                    'is_solo_parent' => $this->is_solo_parent,
                    'ethnicity' => $this->ethnicity,
                    'pwd' => $this->pwd,
                    'mobile_number' => $this->mobile_number,
                    'tel_number' => $this->tel_number,
                    'umid' => $this->umid,
                    'gsis' => $this->gsis,
                    'sss' => $this->sss,
                    'pagibig' => $this->pagibig,
                    'philhealth' => $this->philhealth,
                    'philsys' => $this->philsys,
                    'tin' => $this->tin,
                    'agency_employee_no' => $this->agency_employee_no,
                    'email' => $this->email,
                    'p_house_street' => $this->p_house_street,
                    'permanent_selectedBarangay' => $this->p_barangay,
                    'permanent_selectedCity' => $this->p_city,
                    'permanent_selectedProvince' => $this->p_province,
                    'permanent_selectedZipcode' => $this->p_zipcode,
                    'r_house_street' => $this->same_as_permanent ? $this->p_house_street : $this->r_house_street,
                    'residential_selectedBarangay' => $this->same_as_permanent ? $this->p_barangay : $this->r_barangay,
                    'residential_selectedCity' => $this->same_as_permanent ? $this->p_city : $this->r_city,
                    'residential_selectedProvince' => $this->same_as_permanent ? $this->p_province : $this->r_province,
                    'residential_selectedZipcode' => $this->same_as_permanent ? $this->p_zipcode : $this->r_zipcode,
                ]);                

                $this->editing = false;  
                $this->same_as_permanent = false;                                              
                $this->dispatch('swal', [
                    'title' => 'Personal Information updated successfully!', 
                    'icon' => 'success'
                ]);
            }
        }catch(Exception $e){
            $this->dispatch('swal', [
                'title' => 'Personal Information update was unsuccessful!', 
                'icon' => 'error'
            ]);
            throw $e;
        }
    }
}
