<?php

namespace App\Livewire\Admin;

use App\Models\EmployeesEducation;
use App\Models\LearningAndDevelopment;
use Livewire\Component;
use App\Models\User;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\EmployeesExport;
use App\Models\Eligibility;
use App\Models\PhilippineProvinces;
use App\Models\PhilippineCities;
use App\Models\PhilippineBarangays;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Livewire\Attributes\Url;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('Employees')]
class Employees extends Component
{
    use WithPagination;

    #[Url(as: 'employeeTab', except: 'personal-details')]
    public $employeeTab = 'personal-details';

    #[Url(as: 'employee', except: 'all')]
    public $employee = 'all';

    public $pageSize = 10; 
    public $pageSizes = [10, 20, 30, 50, 100]; 

    public $filters = [
        'name' => true,
        'emp_code' => true,
        'surname' => true,
        'first_name' => true,
        'middle_name' => true,
        'name_extension' => true,
        'date_of_birth' => false,
        'place_of_birth' => false,
        'sex' => false,
        'citizenship' => false,
        'civil_status' => false,
        'height' => false,
        'weight' => false,
        'blood_type' => false,
        'ethnicity' => false,
        'is_solo_parent' => false,
        'pwd' => false,
        'gsis' => false,
        'pagibig' => false,
        'philhealth' => false,
        'sss' => false,
        'tin' => false,
        'agency_employee_no' => false,
        'permanent_selectedProvince' => false,
        'permanent_selectedCity' => false,
        'permanent_selectedBarangay' => false,
        'p_house_street' => false,
        'permanent_selectedZipcode' => false,
        'residential_selectedProvince' => false,
        'residential_selectedCity' => false,
        'residential_selectedBarangay' => false,
        'r_house_street' => false,
        'residential_selectedZipcode' => false,
        'active_status' => true,
        'position' => true,
        'appointment' => true,
        'date_hired' => false,
        'years_in_gov_service' => false,
        'learning_and_development' => false,
        'eligibility' => false,
        'ld_title' => false,
        'educational_background' => false,
        'course' => false,
        'name_of_school' => false,
        'year_graduated' => false,
        // 'tel_number' => false,
        // 'mobile_number' => false,
        // 'email' => false,
    ];

    public $sex;
    public $civil_status;
    public $selectedCivilStatuses = [];
    public $selectedProvinces = [];
    public $selectedCities = [];
    public $selectedBarangays = [];
    public $selectedLD = [];
    public $selectedEligibility = [];
    public $selectedEduc = [];
    public $provinces;
    public $cities;
    public $barangays;
    public $selectAllProvinces = false;
    public $selectAllCities = false;
    public $selectAllBarangays = false;
    public $corporateInfo;

    public $selectedUser = null;
    public $dropdownForCategoryOpen = false;
    public $dropdownForFilter = false;
    public $dropdownForSexOpen = false;
    public $dropdownForCivilStatusOpen = false;
    public $dropdownForLDOpen = false;
    public $dropdownForEligOpen = false;
    public $dropdownForEducOpen = false;
    public $dropdownForProvinceOpen = false;
    public $dropdownForCityOpen = false;
    public $dropdownForBarangayOpen = false;
    public $personalDataSheetOpen = false;
    public $showToggleActiveModal = false;
    public $showDeleteModal = false;

    public $eligibilities;

    public $search = '';

    protected $listeners = [
        'exportUsers'
    ];

    public $appointment;
    public $posLevel;

    public function mount(){
        $this->eligibilities = Eligibility::distinct()->pluck('eligibility');
        $this->getProvicesAndCities();
        $this->search = request()->query('search', $this->search);
    }

    public function render()
    {
        $this->checkFilter();

        if($this->employee != 'all' && $this->employee != null){
            $this->showUser((int) $this->employee);
        }
        

        $query = User::join('user_data', 'user_data.user_id', '=', 'users.id')
                ->leftJoin('learning_and_development', 'learning_and_development.user_id', 'users.id')
                ->leftJoin('eligibility', 'eligibility.user_id', 'users.id')
                ->leftJoin('employees_education', 'employees_education.user_id', 'users.id')
                ->leftJoin('positions', 'positions.id', 'users.position_id')
                ->select('users.id', 'users.emp_code', 'users.profile_photo_path')
                ->groupBy('users.id', 'users.emp_code', 'users.profile_photo_path')
                ->when($this->filters['name'], function ($query) {
                    $query->addSelect('users.name');
                    $query->groupBy('users.name');
                })
                ->when($this->filters['date_of_birth'], function ($query) {
                    $query->addSelect('user_data.date_of_birth');
                    $query->groupBy('user_data.date_of_birth');
                })
                ->when($this->filters['place_of_birth'], function ($query) {
                    $query->addSelect('user_data.place_of_birth');
                    $query->groupBy('user_data.place_of_birth');
                })
                ->when($this->filters['sex'], function ($query) {
                    $query->addSelect('user_data.sex');
                    $query->groupBy('user_data.sex');
                })
                ->when($this->filters['civil_status'], function ($query) {
                    $query->addSelect('user_data.civil_status');
                    $query->groupBy('user_data.civil_status');
                })
                ->when($this->filters['citizenship'], function ($query) {
                    $query->addSelect('user_data.citizenship');
                    $query->groupBy('user_data.citizenship');
                })
                ->when($this->filters['height'], function ($query) {
                    $query->addSelect('user_data.height');
                    $query->groupBy('user_data.height');
                })
                ->when($this->filters['weight'], function ($query) {
                    $query->addSelect('user_data.weight');
                    $query->groupBy('user_data.weight');
                })
                ->when($this->filters['blood_type'], function ($query) {
                    $query->addSelect('user_data.blood_type');
                    $query->groupBy('user_data.blood_type');
                })
                ->when($this->filters['ethnicity'], function ($query) {
                    $query->addSelect('user_data.ethnicity');
                    $query->groupBy('user_data.ethnicity');
                })
                ->when($this->filters['is_solo_parent'], function ($query) {
                    $query->addSelect('user_data.is_solo_parent');
                    $query->groupBy('user_data.is_solo_parent');
                })
                ->when($this->filters['pwd'], function ($query) {
                    $query->addSelect('user_data.pwd');
                    $query->groupBy('user_data.pwd');
                })
                ->when($this->filters['gsis'], function ($query) {
                    $query->addSelect('user_data.gsis');
                    $query->groupBy('user_data.gsis');
                })
                ->when($this->filters['pagibig'], function ($query) {
                    $query->addSelect('user_data.pagibig');
                    $query->groupBy('user_data.pagibig');
                })
                ->when($this->filters['philhealth'], function ($query) {
                    $query->addSelect('user_data.philhealth');
                    $query->groupBy('user_data.philhealth');
                })
                ->when($this->filters['sss'], function ($query) {
                    $query->addSelect('user_data.sss');
                    $query->groupBy('user_data.sss');
                })
                ->when($this->filters['tin'], function ($query) {
                    $query->addSelect('user_data.tin');
                    $query->groupBy('user_data.tin');
                })
                ->when($this->filters['agency_employee_no'], function ($query) {
                    $query->addSelect('user_data.agency_employee_no');
                    $query->groupBy('user_data.agency_employee_no');
                })
                ->when($this->filters['permanent_selectedProvince'], function ($query) {
                    $query->addSelect('user_data.permanent_selectedProvince');
                    $query->groupBy('user_data.permanent_selectedProvince');
                })
                ->when($this->filters['permanent_selectedCity'], function ($query) {
                    $query->addSelect('user_data.permanent_selectedCity');
                    $query->groupBy('user_data.permanent_selectedCity');
                })
                ->when($this->filters['permanent_selectedBarangay'], function ($query) {
                    $query->addSelect('user_data.permanent_selectedBarangay');
                    $query->groupBy('user_data.permanent_selectedBarangay');
                })
                ->when($this->filters['p_house_street'], function ($query) {
                    $query->addSelect('user_data.p_house_street');
                    $query->groupBy('user_data.p_house_street');
                })
                ->when($this->filters['permanent_selectedZipcode'], function ($query) {
                    $query->addSelect('user_data.permanent_selectedZipcode');
                    $query->groupBy('user_data.permanent_selectedZipcode');
                })
                ->when($this->filters['residential_selectedProvince'], function ($query) {
                    $query->addSelect('user_data.residential_selectedProvince');
                    $query->groupBy('user_data.residential_selectedProvince');
                })
                ->when($this->filters['residential_selectedCity'], function ($query) {
                    $query->addSelect('user_data.residential_selectedCity');
                    $query->groupBy('user_data.residential_selectedCity');
                })
                ->when($this->filters['residential_selectedBarangay'], function ($query) {
                    $query->addSelect('user_data.residential_selectedBarangay');
                    $query->groupBy('user_data.residential_selectedBarangay');
                })
                ->when($this->filters['r_house_street'], function ($query) {
                    $query->addSelect('user_data.r_house_street');
                    $query->groupBy('user_data.r_house_street');
                })
                ->when($this->filters['residential_selectedZipcode'], function ($query) {
                    $query->addSelect('user_data.residential_selectedZipcode');
                    $query->groupBy('user_data.residential_selectedZipcode');
                })
                ->when($this->filters['active_status'], function ($query) {
                    $query->addSelect('users.active_status');
                    $query->groupBy('users.active_status');
                })
                ->when($this->filters['appointment'], function ($query) {
                    $query->addSelect('user_data.appointment');
                    $query->groupBy('user_data.appointment');
                })
                ->when($this->filters['position'], function ($query) {
                    $query->addSelect('positions.position');
                    $query->groupBy('positions.position');
                })
                ->when($this->filters['date_hired'], function ($query) {
                    $query->addSelect('user_data.date_hired');
                    $query->groupBy('user_data.date_hired');
                })
                ->when($this->search, function ($query) {
                    $query->where('users.name', 'like', '%' . $this->search . '%');
                })
                ->when($this->sex, function ($query) {
                    if($this->sex == 'others'){
                        $query->where('user_data.sex', '!=', 'Female')
                            ->where('user_data.sex', '!=', 'Male');
                    }else{
                        $query->where('user_data.sex', $this->sex);
                    }
                })
                ->when($this->civil_status, function ($query) {
                    $query->where('user_data.civil_status', $this->civil_status);
                })
                ->when(!empty($this->selectedCivilStatuses), function ($query) {
                    $query->whereIn('user_data.civil_status', $this->selectedCivilStatuses);
                })
                ->when(!empty($this->selectedProvinces), function ($query) {
                    $query->whereIn('user_data.permanent_selectedProvince', $this->selectedProvinces);
                })
                ->when(!empty($this->selectedCities), function ($query) {
                    $query->whereIn('user_data.permanent_selectedCity', $this->selectedCities);
                })
                ->when(!empty($this->selectedBarangays), function ($query) {
                    $query->whereIn('user_data.permanent_selectedBarangay', $this->selectedBarangays);
                })
                ->when(!empty($this->selectedLD), function ($query) {
                    if($this->selectedLD == ['Others']){
                        $query->whereNotIn('learning_and_development.type_of_ld', ['Technical', 'Supervisory', 'Leadership']);
                    }else{
                        $query->whereIn('learning_and_development.type_of_ld', $this->selectedLD);
                    }
                })
                ->when(!empty($this->selectedEligibility), function ($query) {
                    $query->whereIn('eligibility.eligibility', $this->selectedEligibility);
                })
                ->when(!empty($this->selectedEduc), function ($query) {
                    $query->where(function($subQuery) {
                        $isBachelor = in_array('b', $this->selectedEduc);
                        $isMaster = in_array('m', $this->selectedEduc);
                        $isDoctor = in_array('d', $this->selectedEduc);
                
                        if ($isBachelor) {
                            $subQuery->orWhere('employees_education.is_bachelor', 1);
                        }
                        if ($isMaster) {
                            $subQuery->orWhere('employees_education.is_master', 1);
                        }
                        if ($isDoctor) {
                            $subQuery->orWhere('employees_education.is_doctor', 1);
                        }
                    });
                })
                ->when($this->filters['years_in_gov_service'], function ($query) {
                    $query->addSelect(DB::raw('(
                        SELECT FLOOR(SUM(
                            CASE
                                WHEN work_experience.toPresent = "Present" THEN TIMESTAMPDIFF(MONTH, work_experience.start_date, CURDATE())
                                WHEN work_experience.end_date IS NOT NULL THEN TIMESTAMPDIFF(MONTH, work_experience.start_date, work_experience.end_date)
                                ELSE 0
                            END
                        ) / 12)
                        FROM work_experience
                        WHERE work_experience.user_id = users.id AND work_experience.gov_service = 1
                    ) as years_in_gov_service'));
                })
                ->when($this->appointment, function ($query) {
                    $query->where('user_data.appointment', $this->appointment);
                })
                ->when($this->posLevel, function ($query) {
                    $query->where('positions.level', (int) $this->posLevel);
                })
                ->where('users.user_role', '=','emp')
                ->paginate($this->pageSize);

            $query->getCollection()->transform(function ($user) {
                $statusMapping = [
                    0 => 'Inactive',
                    1 => 'Active',
                    2 => 'Retired',
                    3 => 'Resigned'
                ];
                $user->active_status_label = $statusMapping[$user->active_status] ?? 'Unknown';
                
                return $user;
            });

            if($this->dropdownForCategoryOpen){
                $this->dropdownForFilter = null;
            }

            if($this->dropdownForFilter){
                $this->dropdownForCategoryOpen = null;
            }

            $userIds = $query->pluck('id');
            $learnDev = LearningAndDevelopment::whereIn('user_id', $userIds)->get()->groupBy('user_id');
            $eligs = Eligibility::whereIn('user_id', $userIds)->get()->groupBy('user_id');
            $educBg = EmployeesEducation::whereIn('user_id', $userIds)->get()->groupBy('user_id');

            return view('livewire.admin.employees', [
                'users' => $query,
                'cities' => $this->cities,
                'barangays' => $this->barangays,
                'learnDev' => $learnDev,
                'eligs' => $eligs,
                'educBg' => $educBg,
            ]);
    }

    public function toggleDropdown(){
        $this->dropdownForCategoryOpen = !$this->dropdownForCategoryOpen;
        $this->dropdownForSexOpen = false;
        $this->dropdownForCivilStatusOpen = false;
        $this->dropdownForProvinceOpen = false;
        $this->dropdownForCityOpen = false;
        $this->dropdownForBarangayOpen = false;
        $this->dropdownForEligOpen = false;
        $this->dropdownForEducOpen = false;
        $this->dropdownForLDOpen = false;
    }

    public function toggleDropdownFilter(){
        $this->dropdownForFilter = !$this->dropdownForFilter;
    }

    public function toggleDropdownSex()
    {
        $this->dropdownForSexOpen = !$this->dropdownForSexOpen;
        $this->dropdownForCategoryOpen = false;
        $this->dropdownForCivilStatusOpen = false;
        $this->dropdownForProvinceOpen = false;
        $this->dropdownForCityOpen = false;
        $this->dropdownForBarangayOpen = false;
        $this->dropdownForLDOpen = false;
        $this->dropdownForEligOpen = false;
        $this->dropdownForEducOpen = false;
    }

    public function toggleDropdownCivilStatus()
    {
        $this->dropdownForCivilStatusOpen = !$this->dropdownForCivilStatusOpen;
        $this->dropdownForCategoryOpen = false;
        $this->dropdownForSexOpen = false;
        $this->dropdownForProvinceOpen = false;
        $this->dropdownForCityOpen = false;
        $this->dropdownForBarangayOpen = false;
        $this->dropdownForLDOpen = false;
        $this->dropdownForEligOpen = false;
        $this->dropdownForEducOpen = false;
    }

    public function toggleDropdownLD()
    {
        $this->dropdownForLDOpen = !$this->dropdownForLDOpen;
        $this->dropdownForEligOpen = false;
        $this->dropdownForCivilStatusOpen = false;
        $this->dropdownForCategoryOpen = false;
        $this->dropdownForSexOpen = false;
        $this->dropdownForProvinceOpen = false;
        $this->dropdownForCityOpen = false;
        $this->dropdownForBarangayOpen = false;
        $this->dropdownForEducOpen = false;
    }

    
    public function toggleDropdownElig()
    {
        $this->dropdownForEligOpen = !$this->dropdownForEligOpen;
        $this->dropdownForLDOpen = false;
        $this->dropdownForCivilStatusOpen = false;
        $this->dropdownForCategoryOpen = false;
        $this->dropdownForSexOpen = false;
        $this->dropdownForProvinceOpen = false;
        $this->dropdownForCityOpen = false;
        $this->dropdownForBarangayOpen = false;
        $this->dropdownForEducOpen = false;
    }

    public function toggleDropdownEduc()
    {
        $this->dropdownForEducOpen = !$this->dropdownForEducOpen;
        $this->dropdownForLDOpen = false;
        $this->dropdownForEligOpen = false;
        $this->dropdownForCivilStatusOpen = false;
        $this->dropdownForCategoryOpen = false;
        $this->dropdownForSexOpen = false;
        $this->dropdownForProvinceOpen = false;
        $this->dropdownForCityOpen = false;
        $this->dropdownForBarangayOpen = false;
    }

    public function toggleDropdownProvince()
    {
        $this->dropdownForProvinceOpen = !$this->dropdownForProvinceOpen;
        $this->dropdownForCategoryOpen = false;
        $this->dropdownForSexOpen = false;
        $this->dropdownForCivilStatusOpen = false;
        $this->dropdownForCityOpen = false;
        $this->dropdownForBarangayOpen = false;
        $this->dropdownForLDOpen = false;
        $this->dropdownForEligOpen = false;
        $this->dropdownForEducOpen = false;
    }

    public function toggleDropdownCity()
    {
        $this->dropdownForCityOpen = !$this->dropdownForCityOpen;
        $this->dropdownForCategoryOpen = false;
        $this->dropdownForSexOpen = false;
        $this->dropdownForEligOpen = false;
        $this->dropdownForCivilStatusOpen = false;
        $this->dropdownForProvinceOpen = false;
        $this->dropdownForBarangayOpen = false;
        $this->dropdownForLDOpen = false;
        $this->dropdownForEducOpen = false;
    }

    public function toggleDropdownBarangay()
    {
        $this->dropdownForBarangayOpen = !$this->dropdownForBarangayOpen;
        $this->dropdownForCategoryOpen = false;
        $this->dropdownForSexOpen = false;
        $this->dropdownForCivilStatusOpen = false;
        $this->dropdownForCityOpen = false;
        $this->dropdownForProvinceOpen = false;
        $this->dropdownForEligOpen = false;
        $this->dropdownForLDOpen = false;
        $this->dropdownForEducOpen = false;
    }

    public function updatedSelectAllProvinces($value)
    {
        if ($value) {
            $this->selectedProvinces = $this->provinces->pluck('province_description')->toArray();
        } else {
            $this->selectedProvinces = [];
        }
    }

    public function updatedSelectAllCities($value)
    {
        if ($value) {
            $this->selectedCities = $this->cities->pluck('city_municipality_description')->toArray();
        } else {
            $this->selectedCities = [];
        }
    }

    public function updatedSelectAllBarangays($value)
    {
        if ($value) {
            $this->selectedBarangays = $this->barangays->pluck('barangay_description')->toArray();
        } else {
            $this->selectedBarangays = [];
        }
    }

    public function updatedFilters()
    {
        $this->resetPage();
    }

    public function showUser($userId)
    {
        $this->employee = (string) $userId;
        $this->selectedUser = User::find($userId);
        $this->personalDataSheetOpen = true;
    }

    public function closePersonalDataSheet()
    {
        $this->selectedUser = null;
        $this->corporateInfo = null;
        $this->personalDataSheetOpen = false;
        $this->employee = 'all';
        $this->employeeTab = 'personal-details';
    }

    public function exportUsers()
    {
        $filterConditions = [
            'sex' => $this->sex ?? null,
            'civil_status' => $this->selectedCivilStatuses ?? [],
            'selectedProvince' => $this->selectedProvinces ?? [],
            'selectedCity' => $this->selectedCities ?? [],
            'selectedBarangay' => $this->selectedBarangays ?? [],
            'selectedLD' => $this->selectedLD ?? [],
            'selectedElig' => $this->selectedEligibility ?? [],
            'selectedEduc' => $this->selectedEduc ?? [],
            'appointment' => $this->appointment,
            'posLevel' => $this->posLevel,
        ];
    
        $this->filters['name'] = false;
        $selectedColumns = array_keys(array_filter($this->filters));
        $this->filters['name'] = true;

        $fieldsToFormat = ['gsis', 'pagibig', 'philhealth', 'sss', 'tin', 'agency_employee_no'];

        foreach ($fieldsToFormat as $field) {
            if (isset($user->$field) && is_numeric($user->$field)) {
                // Prepend a single quote to make Excel treat the value as text
                $user->$field = "'" . $user->$field;
            }
        }
    
        $selectedColumns = array_unique($selectedColumns);
    
        return Excel::download(new EmployeesExport($filterConditions, $selectedColumns), 'EmployeesList.xlsx');
    }
    
    public function checkFilter()
    {
        if (!empty($this->selectedProvinces)) {
            $provinceCodes = PhilippineProvinces::whereIn('province_description', $this->selectedProvinces)
                ->pluck('province_code');
            $this->cities = PhilippineCities::whereIn('province_code', $provinceCodes)->get();
        } else {
            $this->cities = collect();
            $this->barangays = collect();
        }
    
        if (!empty($this->selectedCities)) {
            $cityCodes = PhilippineCities::whereIn('city_municipality_description', $this->selectedCities)
                ->pluck('city_municipality_code');
            $this->barangays = PhilippineBarangays::whereIn('city_municipality_code', $cityCodes)->get();
        } else {
            $this->barangays = collect();
        }

        if($this->filters['educational_background']){
            $this->filters['course'] = true;
            $this->filters['name_of_school'] = true;
            $this->filters['year_graduated'] = true;
        }else{
            $this->filters['course'] = false;
            $this->filters['name_of_school'] = false;
            $this->filters['year_graduated'] = false;
        }

        if($this->filters['learning_and_development']){
            $this->filters['ld_title'] = true;
        }else{
            $this->filters['ld_title'] = false;
        }
    }
    
    public function getProvicesAndCities(){
        $this->provinces = PhilippineProvinces::all();
        $this->cities = collect();
        $this->barangays = collect();
    }

    public function downloadCertificate($documentId)
    {
        $document = LearningAndDevelopment::findOrFail($documentId);
        $filePath = $document->certificate;
        $fileName = basename($filePath);

        if (!Storage::disk('public')->exists($filePath)) {
            throw new NotFoundHttpException("The file does not exist.");
        }

        $fileSize = Storage::disk('public')->size($filePath);

        $headers = [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            'Content-Length' => $fileSize,
        ];

        return new StreamedResponse(function () use ($filePath) {
            $stream = Storage::disk('public')->readStream($filePath);
            fpassthru($stream);
            if (is_resource($stream)) {
                fclose($stream);
            }
        }, 200, $headers);
    }

    public function confirmToggleActive()
    {
        $this->showToggleActiveModal = true;
    }

    public function toggleActiveStatus()
    {
        if (!$this->selectedUser) return;

        $this->selectedUser->active_status = $this->selectedUser->active_status == 1 ? 0 : 1;
        $this->selectedUser->save();

        // Refresh the selectedUser instance so the button label updates immediately
        $this->selectedUser = User::find($this->selectedUser->id);

        $this->showToggleActiveModal = false;

        session()->flash('message', 'Employee status updated successfully.');
    }

    public function confirmDelete()
    {
        $this->showDeleteModal = true;
    }

    public function deleteEmployeePermanently()
    {
        if (!$this->selectedUser) return;

        $user = $this->selectedUser;

        // Close the sheet first, then delete
        $this->closePersonalDataSheet();

        $user->delete();

        $this->showDeleteModal = false;

        session()->flash('message', 'Employee permanently deleted.');
    }
}
