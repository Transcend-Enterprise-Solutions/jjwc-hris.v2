<?php

namespace App\Livewire\Admin;

use App\Exports\AdminRolesExport;
use App\Exports\PerOfficeDivisionExport;
use App\Exports\PerUnitExport;
use App\Imports\SalaryGradeImport;
use App\Models\AdminRoleAccess;
use App\Models\OfficeDivisions;
use App\Models\OfficeDivisionUnits;
use App\Models\ParentModules;
use App\Models\SystemModules;
use Livewire\Attributes\Url;
use App\Models\Positions;
use App\Models\SalaryGrade;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SalaryGradeExport;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('Organization')]
class Organization extends Component
{
    use WithPagination, WithFileUploads;

    #[Url(as: 'tab')]
    public $selectedTab = 'org';

    #[Url(as: 'sub_tab')]
    public $selectedSubTab = 'headcount';

    #[Url(as: 'admin_tab')]
    public $adminSubTab = 'admin';

    #[Url(as: 'sg_tab')]
    public $sgTab = 'plantilla';


    public $addRole;
    public $editRole;
    public $employees;
    public $roleEmployees;
    public $positionsByUnit;
    public $positions;
    public $officeDivisions;
    public $unit;
    public $unitName;
    public $divsUnits;
    public $userId;
    public $name;
    public $employee_number;
    public $position;
    public $user_role;
    public $admin_email;
    public $office_division;
    public $password;
    public $cpassword;
    public $search;
    public $search2;
    public $search3;
    public $search4;
    public $deleteId;
    public $deleteMessage;
    public $add;
    public $data;
    public $settings;
    public $settingsId;
    public $settings_data;
    public $settingsData = [['value' => '']];
    public $units = [['value' => '']];

    public $salaryGrades;
    public $editingId = null;
    public $isEditing = false;
    public $editedData = [];
    public $showSGModal = false;
    public $salaryGradeData = [
        'salary_grade' => '',
        'step1' => '', 'step2' => '', 'step3' => '', 'step4' => '',
        'step5' => '', 'step6' => '', 'step7' => '', 'step8' => '',
    ];

    public $addPosition;
    public $editPosition;
    public $dropdownForStatus;
    public $allStat = true;
    public $activeStatus;
    public $positionId;
    public $employeeId;
    public $officeDivisionId;
    public $unitId;
    public $file;
    public $divId;
    public $pageSize = 10; 
    public $pageSizes = [10, 20, 30, 50, 100]; 

    public $status = [
        'active' => true,
        'inactive' => true,
        'resigned' => true,
        'retired' => true,
    ];

    public $adminRoles;
    public $parentModules;
    public $systemModules;
    public $topLevelModules;
    public $showRoleAccessModal = false;
    public $editingRoleAccess = null;
    public $roleName;
    public $roleCode;
    public $roleAccessModules = [];
    public $selectAll = false;


    public function mount(){
        $this->employees = User::where('user_role', '=', 'emp')->get();

        $this->roleEmployees = User::where('user_role', '=', 'emp')
            ->whereDoesntHave('adminAccount')
            ->get();

        $this->salaryGrades = SalaryGrade::orderBy('salary_grade')->get();
        $this->positions = Positions::where('position', '!=', 'Super Admin')->get();
        
        
        $this->parentModules = ParentModules::with('systemModules')->get();
        $this->topLevelModules = SystemModules::whereNull('parent_module_id')->get();
        $this->systemModules = SystemModules::all();

        $this->adminRoles = AdminRoleAccess::orderBy('hierarchy', 'asc')
                        ->get();
    }

    public function render(){
        if($this->office_division){
            $this->divsUnits = OfficeDivisionUnits::where('office_division_id' , $this->office_division)->get();
        }
        if($this->divId){
            $this->divsUnits = OfficeDivisionUnits::where('office_division_id' , $this->divId)->get();
        }


        $admins = User::leftJoin('positions', 'positions.id', 'users.position_id')
                ->where('positions.position', '!=', 'Super Admin')
                ->leftJoin('office_divisions', 'office_divisions.id', 'users.office_division_id')
                ->leftJoin('office_division_units', 'office_division_units.id', 'users.unit_id')
                ->where('users.user_role', '!=', 'emp')
                ->where('users.active_status', '!=', 4)
                ->when($this->search, function ($query) {
                    return $query->search(trim($this->search));
                })
                ->select(
                    'users.id',
                    'users.name',
                    'users.profile_photo_path', 
                    'users.user_role',
                    'users.emp_code',
                    'positions.position',
                    'office_divisions.office_division',
                    'office_division_units.unit'
                )
                ->paginate($this->pageSize);

        foreach($admins as $admin){
            $empCode = $admin->emp_code;
            $appt = User::where('users.emp_code', $empCode)
                        ->join('user_data', 'user_data.user_id', 'users.id')
                        ->select('user_data.appointment')
                        ->first();
                        
            if($appt){
                $admin->appointment = $appt->appointment;
            }

            $adminAccess = AdminRoleAccess::where('role_code', $admin->user_role)->first();
            if($adminAccess){
                $admin->role_name = $adminAccess->role_name;
            }
        }
                

        $empPos = User::where('user_role', 'emp')
            ->join('user_data', 'user_data.user_id', 'users.id')
            ->join('positions', 'positions.id', 'users.position_id')
            ->join('office_divisions', 'office_divisions.id', 'users.office_division_id')
            ->leftJoin('office_division_units', 'office_division_units.id', 'users.unit_id')
            ->where('users.active_status', '!=', 4)
            ->select(
                'users.id', 
                'users.name', 
                'users.emp_code', 
                'users.profile_photo_path', 
                'users.active_status', 
                'positions.position', 
                'user_data.appointment',
                'user_data.surname', 
                'user_data.first_name', 
                'user_data.middle_name', 
                'user_data.name_extension',
                'office_divisions.office_division',
                'office_division_units.unit',
            )
            ->when($this->search3, function ($query) {
                return $query->search(trim($this->search3));
            })
            ->orderBy('user_data.surname', 'ASC')
            ->paginate($this->pageSize);
            

        $organizations = $this->getOrganization();

        $this->officeDivisions = OfficeDivisions::with(['officeDivisionUnits', 'positions' => function($query) {
            $query->where('position', '!=', 'Super Admin')->whereNull('unit_id');
            }])
            ->when($this->search4, function ($query) {
                return $query->search(trim($this->search4));
            })
            ->get();
        
        $this->positionsByUnit = OfficeDivisionUnits::with(['positions' => function($query) {
            $query->where('position', '!=', 'Super Admin')->whereNotNull('unit_id');
            }])->get();


        if($this->file){
            $this->importFromExcel();
            $this->salaryGrades = SalaryGrade::orderBy('salary_grade')->get();
        }

        
        $roleAccesses = AdminRoleAccess::orderBy('hierarchy', 'asc')
                        ->paginate($this->pageSize);
        $systemModules = SystemModules::all();


        return view('livewire.admin.organization',[
            'organizations' => $organizations,
            'admins' => $admins,
            'empPos' => $empPos,
            'roleAccesses' => $roleAccesses,
            'systemModules' => $systemModules,
        ]);
    }

    protected function getOrganization(){
        $users = User::where('user_role', 'emp')
                ->join('user_data', 'user_data.user_id', 'users.id')
                ->join('positions', 'positions.id', 'users.position_id')
                ->join('office_divisions', 'office_divisions.id', 'users.office_division_id')
                ->where('users.active_status', '!=', 4)
                ->select(
                    'users.name', 
                    'users.emp_code', 
                    'users.active_status', 
                    'positions.position', 
                    'user_data.appointment',  
                    'office_divisions.office_division',
                )
                ->when($this->search2, function ($query) {
                    return $query->search(trim($this->search2));
                })
                ->when(!$this->allStat, function ($query) {
                    return $query->where(function ($subQuery) {
                        if ($this->status['active']) {
                            $subQuery->orWhere('active_status', 1);
                        }
                        if ($this->status['inactive']) {
                            $subQuery->orWhere('active_status', 0);
                        }
                        if ($this->status['resigned']) {
                            $subQuery->orWhere('active_status', 2);
                        }
                        if ($this->status['retired']) {
                            $subQuery->orWhere('active_status', 3);
                        }
                    });
                })
                ->orderBy('user_data.surname', 'asc') // Alphabetical order
                ->get();

        // Group by office division and then by appointment type
        $groupedData = [];
        
        foreach ($users as $user) {
            $division = $user->office_division;
            
            // Determine appointment category
            $appointmentCategory = 'Plantilla'; // Default
            
            if ($user->appointment == "cos") {
                $appointmentCategory = 'COS';
            }
            
            // Add appointment_category to user object for template use
            $user->appointment_category = $appointmentCategory;
            
            if (!isset($groupedData[$division])) {
                $groupedData[$division] = [
                    'users' => [],
                    'totals' => [
                        'Plantilla' => 0,
                        'COS' => 0,
                    ],
                    'by_appointment' => [
                        'Plantilla' => [],
                        'COS' => [],
                    ]
                ];
            }
            
            $groupedData[$division]['users'][] = $user;
            $groupedData[$division]['totals'][$appointmentCategory]++;
            $groupedData[$division]['by_appointment'][$appointmentCategory][] = $user;
        }
        
        return $groupedData;
    }


    public function toggleAllStats() {
        if ($this->allStat) {
            $this->allStat = null;
            foreach (array_keys($this->status) as $stat) {
                $this->status[$stat] = false;
            }
            $this->allStat = false;
        } else {
            $this->allStat = true;
            foreach (array_keys($this->status) as $stat) {
                $this->status[$stat] = true;
            }
            $this->allStat = true;
        }
    }

    public function exportRoles(){
        try{
            $admins = User::join('positions', 'positions.id', 'users.position_id')
                ->where('positions.position', '!=', 'Super Admin')
                ->leftJoin('office_divisions', 'office_divisions.id', 'users.office_division_id')
                ->leftJoin('office_division_units', 'office_division_units.id', 'users.unit_id')
                ->where('users.user_role', '!=', 'emp')
                ->where('users.active_status', '!=', 4)
                ->when($this->search, function ($query) {
                    return $query->search(trim($this->search));
                })
                ->select(
                    'users.id',
                    'users.name',
                    'users.user_role',
                    'users.emp_code',
                    'positions.position',
                    'office_divisions.office_division',
                    'office_division_units.unit'
                );

            $filters = [
                'admins' => $admins,
            ];
            return Excel::download(new AdminRolesExport($filters), 'Admin_Roles_List.xlsx');
            
        }catch(Exception $e){
            throw $e;
        }
    }

    public function exportEmployees($division)
    {
        try {
            $organizations = User::where('user_role', 'emp')
                ->join('user_data', 'user_data.user_id', 'users.id')
                ->join('positions', 'positions.id', 'users.position_id')
                ->join('office_divisions', 'office_divisions.id', 'users.office_division_id')
                ->leftJoin('office_division_units', 'office_division_units.id', 'users.unit_id')
                ->leftJoin('employee_salaries', 'employee_salaries.user_id', 'users.id')
                ->where('users.active_status', '!=', 4)
                ->select(
                    'users.name', 
                    'users.email', 
                    'users.emp_code', 
                    'users.active_status', 
                    'positions.position', 
                    'user_data.appointment', 
                    'user_data.date_hired', 
                    'user_data.first_name', 
                    'user_data.middle_name', 
                    'user_data.surname', 
                    'user_data.name_extension', 
                    'office_divisions.office_division',
                    'office_division_units.unit',
                    'employee_salaries.*',
                )
                ->where('office_divisions.office_division', $division)
                ->when($this->search2, function ($query) {
                    return $query->search(trim($this->search2));
                })
                ->when(!$this->allStat, function ($query) {
                    return $query->where(function ($subQuery) {
                        if ($this->status['active']) {
                            $subQuery->orWhere('active_status', 1);
                        }
                        if ($this->status['inactive']) {
                            $subQuery->orWhere('active_status', 0);
                        }
                        if ($this->status['resigned']) {
                            $subQuery->orWhere('active_status', 2);
                        }
                        if ($this->status['retired']) {
                            $subQuery->orWhere('active_status', 3);
                        }
                    });
                })
                ->orderBy('user_data.surname', 'ASC');

            $selectedStatuses = $this->allStat ? ['All'] : array_keys(array_filter($this->status));
            $statusLabels = [
                'active' => 'Active',
                'inactive' => 'Inactive',
                'resigned' => 'Resigned',
                'retired' => 'Retired',
                'promoted' => 'Promoted'
            ];
    
            $filters = [
                'organizations' => $organizations,
                'office_division' => $division,
                'statuses' => $selectedStatuses == ['All'] ? ['All'] : array_map(function($status) use ($statusLabels) {
                    return $statusLabels[$status];
                }, $selectedStatuses)
            ];
            return Excel::download(new PerOfficeDivisionExport($filters), $division . '_EmployeesList.xlsx');
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function exportEmployeesPerUnit($unitId = null, $divId){
        try{
            $users = User::where('users.office_division_id', $divId)
                        ->join('positions', 'positions.id', 'users.position_id')
                        ->where('positions.position', '!=', 'Super Admin')
                        ->join('office_divisions', 'office_divisions.id', 'users.office_division_id')
                        ->leftJoin('office_division_units', 'office_division_units.id', 'users.unit_id')
                        ->join('user_data', 'user_data.user_id', 'users.id')
                        ->leftJoin('employee_salaries', 'employee_salaries.user_id', 'users.id')
                        ->select(
                            'users.name', 
                            'users.email', 
                            'users.emp_code', 
                            'users.active_status', 
                            'positions.position', 
                            'user_data.appointment', 
                            'user_data.date_hired', 
                            'user_data.first_name', 
                            'user_data.middle_name', 
                            'user_data.surname', 
                            'user_data.name_extension', 
                            'office_divisions.office_division',
                            'office_division_units.unit',
                            'employee_salaries.*',
                        )->orderBy('user_data.surname', 'ASC');

            if ($unitId === null) {
                $users->whereNull('users.unit_id');
            } else {
                $users->where('users.unit_id', $unitId);
            }

            $unit = OfficeDivisionUnits::where('id', $unitId)->first();
            $officeDivison = OfficeDivisions::where('id', $divId)->first();
            if($users){
                $filters = [
                    'users' => $users,
                    'unit' => $unit ? $unit->unit : '',
                    'office_division' => $officeDivison->office_division,
                ];
                $filename = $officeDivison->office_division . "-" . ($unit ? $unit->unit : '') . " EmployeesList.xlsx";
                return Excel::download(new PerUnitExport($filters), $filename);
            }
        }catch(Exception $e){
            throw $e;
        }
    }

    public function toggleAddSettings($data)
    {
        // $this->officeDivisionId = $divisionId;
        $this->data = $data;
        $this->settings = true;
        $this->add = true;
        $this->settingsData = [['value' => '']];
        $this->units = [['value' => '']];
    }

    public function toggleAddPos($id, $data){
        $this->officeDivisionId = $id;
        $this->data = $data;
        $this->settings = true;
        $this->add = true;
        $this->settingsData = [['value' => '', 'level' => 1]]; // Default level 1
    }

    public function toggleEditPos($id, $data){
        $this->officeDivisionId = $id;
        $positions = Positions::where('office_division_id', $id)
                    ->where('position', '!=', 'Super Admin')
                    ->where('unit_id', null)
                    ->get();
        $this->data = $data;
        $this->settings = true;
        if ($positions->isNotEmpty()) {
            $this->settingsData = $positions->map(function($pos) {
                return [
                    'value' => $pos->position,
                    'level' => $pos->level
                ];
            })->toArray();
        } else {
            $this->settingsData = [['value' => '', 'level' => 1]];
        }
    }

    public function toggleAddUnitPos($divId, $unitId, $data){
        $this->officeDivisionId = $divId;
        $this->unitId = $unitId;
        $this->data = $data;
        $this->settings = true;
        $this->add = true;
        $this->settingsData = [['value' => '', 'level' => 1]]; // Default level 1
    }

    public function toggleEditUnitPos($divId, $unitId, $data){
        $this->officeDivisionId = $divId;
        $this->unitId = $unitId;
        $positions = Positions::where('office_division_id', $divId)
                    ->where('position', '!=', 'Super Admin')
                    ->where('unit_id', $unitId)
                    ->get();
        $this->data = $data;
        $this->settings = true;
        if ($positions->isNotEmpty()) {
            $this->settingsData = $positions->map(function($pos) {
                return [
                    'value' => $pos->position,
                    'level' => $pos->level
                ];
            })->toArray();
        } else {
            $this->settingsData = [['value' => '', 'level' => 1]];
        }
    }

    public function addNewSetting()
    {
        $this->settingsData[] = ['value' => '', 'level' => 1];
    }

    public function addNewUnit()
    {
        $this->units[] = ['value' => ''];
    }

    public function removeSetting($index)
    {
        unset($this->settingsData[$index]);
        $this->settingsData = array_values($this->settingsData);
    }

    public function removeUnit($index)
    {
        unset($this->units[$index]);
        $this->units = array_values($this->units);
    }

    public function toggleDeleteSettings($id, $data){ 
        $this->deleteId = $id;
        $this->data = $data;
        $this->deleteMessage = $data;
    }

    public function toggleEditSettings($id, $data){
        $this->settings = true;  
        $this->settingsId = $id;
        $this->data = $data;
        if($data == "office/division"){
            $officeDivisions = OfficeDivisions::where('id', $this->settingsId)->first();
            $this->settings_data = $officeDivisions->office_division;

            if ($officeDivisions->officeDivisionUnits->isNotEmpty()) {
                $this->units = $officeDivisions->officeDivisionUnits->map(function($unit) {
                    return ['value' => $unit->unit];
                })->toArray();
            } else {
                $this->units = [['value' => '']];
            }
        }else if($data == "position"){
            $positions = Positions::where('id', $this->settingsId)->first();
            $this->settings_data = $positions->position;
        }
    }

    public function saveSettings(){
        try {
            $message = null;
            if($this->add){
                if ($this->data == "office/division") {
       
                    $officeDiv = OfficeDivisions::create([
                        'office_division' => $this->settings_data,
                    ]);

                    if (!empty($this->units) && $this->units !== [['value' => '']]){
                        foreach($this->units as $unit){
                            OfficeDivisionUnits::create([
                                'office_division_id' => $officeDiv->id,
                                'unit' => $unit['value'],
                            ]);
                        }
                    }

                    $message = "Office/Division added successfully!";
                } else if ($this->data == "position") {
                    $this->validate([
                        'settingsData.*.value' => 'required|string|max:255',
                        'settingsData.*.level' => 'required|integer|min:1',
                    ]);

                    foreach ($this->settingsData as $setting) {
                        Positions::create([
                            'office_division_id' => $this->officeDivisionId,
                            'position' => $setting['value'],
                            'level' => $setting['level'],
                        ]);
                    }
                    $message = "Position/s added successfully!";
                } else if ($this->data == "unit-position") {
                    $this->validate([
                        'settingsData.*.value' => 'required|string|max:255',
                        'settingsData.*.level' => 'required|integer|min:1',
                    ]);

                    foreach ($this->settingsData as $setting) {
                        Positions::create([
                            'office_division_id' => $this->officeDivisionId,
                            'unit_id' => $this->unitId,
                            'position' => $setting['value'],
                            'level' => $setting['level'],
                        ]);
                    }
                    $message = "Position/s added successfully!";
                }
            }else{
                  // Update existing work group or Position
                if ($this->data == "office/division") {
                    $officeDivisions = OfficeDivisions::where('id', $this->settingsId)->first();
                    $officeDivisions->update([
                        'office_division' => $this->settings_data,
                    ]);

                    // Track existing units
                    $existingUnitIds = $officeDivisions->officeDivisionUnits->pluck('id')->toArray();
                    $updatedUnitIds = [];

                    if (!empty($this->units) && $this->units !== [['value' => '']]){
                        foreach ($this->units as $index => $unit) {
                            if (isset($officeDivisions->officeDivisionUnits[$index])) {
                                $officeDivisionUnit = $officeDivisions->officeDivisionUnits[$index];
                                $officeDivisionUnit->update([
                                    'unit' => $unit['value'],
                                ]);
                                $updatedUnitIds[] = $officeDivisionUnit->id;
                            } else {
                                $newUnit = OfficeDivisionUnits::create([
                                    'office_division_id' => $officeDivisions->id,
                                    'unit' => $unit['value'],
                                ]);
                                $updatedUnitIds[] = $newUnit->id;
                            }
                        }
                        // Detect removed units and delete them
                        $removedUnitIds = array_diff($existingUnitIds, $updatedUnitIds);
                        OfficeDivisionUnits::whereIn('id', $removedUnitIds)->delete();
                    }   


                    $message = "Office/Division updated successfully!";
                } else if ($this->data == "position") {
                    $this->validate([
                        'settingsData.*.value' => 'required|string|max:255',
                        'settingsData.*.level' => 'required|integer|min:1',
                    ]);
                    
                    $officeDivisions = OfficeDivisions::where('id', $this->officeDivisionId)->first();
                    
                    // Track existing positions
                    $existingPositionIds = $officeDivisions->positions->pluck('id')->toArray();
                    $updatedPositionIds = [];

                    foreach($this->settingsData as $index => $data) {
                        if (isset($officeDivisions->positions[$index])) {
                            $position = $officeDivisions->positions[$index];
                            $position->update([
                                'position' => $data['value'],
                                'level' => $data['level'],
                            ]);
                            $updatedPositionIds[] = $position->id;
                        } else {
                            $newPosition = Positions::create([
                                'office_division_id' => $officeDivisions->id,
                                'position' => $data['value'],
                                'level' => $data['level'],
                            ]);
                            $updatedPositionIds[] = $newPosition->id;
                        }
                    }

                    // Detect removed positions and delete them
                    $removedPositionIds = array_diff($existingPositionIds, $updatedPositionIds);
                    Positions::whereIn('id', $removedPositionIds)->delete();

                    $message = "Position/s updated successfully!";
                } else if ($this->data == "unit-position") {
                    $this->validate([
                        'settingsData.*.value' => 'required|string|max:255',
                        'settingsData.*.level' => 'required|integer|min:1',
                    ]);
                    
                    $officeDivisionsUnits = OfficeDivisionUnits::where('id', $this->unitId)->first();
                    
                    // Track existing positions
                    $existingPositionIds = $officeDivisionsUnits->positions->pluck('id')->toArray();
                    $updatedPositionIds = [];

                    foreach($this->settingsData as $index => $data) {
                        if (isset($officeDivisionsUnits->positions[$index])) {
                            $position = $officeDivisionsUnits->positions[$index];
                            $position->update([
                                'position' => $data['value'],
                                'level' => $data['level'],
                            ]);
                            $updatedPositionIds[] = $position->id;
                        } else {
                            $newPosition = Positions::create([
                                'office_division_id' => $this->officeDivisionId,
                                'unit_id' => $officeDivisionsUnits->id,
                                'position' => $data['value'],
                                'level' => $data['level'],
                            ]);
                            $updatedPositionIds[] = $newPosition->id;
                        }
                    }

                    // Detect removed positions and delete them
                    $removedPositionIds = array_diff($existingPositionIds, $updatedPositionIds);
                    Positions::whereIn('id', $removedPositionIds)->delete();

                    $message = "Position/s updated successfully!";
                }
            }

            $this->resetVariables();
            $this->dispatch('swal', [
                'title' => $message,
                'icon' => 'success'
            ]);
        } catch(Exception $e) {
            throw $e;
        }
    }

    public function toggleEditRole($userId){
        $this->editRole = true;
        $this->userId = $userId;
        try {
            $admin = User::where('users.id', $userId)
                ->join('positions', 'positions.id', 'users.position_id')
                ->where('positions.position', '!=', 'Super Admin')
                ->leftJoin('office_divisions', 'office_divisions.id', 'users.office_division_id')
                ->leftJoin('office_division_units', 'office_division_units.id', 'users.unit_id')
                ->where('users.user_role', '!=', 'emp')
                ->where('users.active_status', '!=', 4)
                ->when($this->search, function ($query) {
                    return $query->search(trim($this->search));
                })
                ->select(
                    'users.id',
                    'users.name',
                    'users.email',
                    'users.user_role',
                    'users.emp_code',
                    'users.unit_id',
                    'positions.position',
                    'office_divisions.office_division',
                    'office_divisions.id as divId',
                    'office_division_units.unit',
                    'office_division_units.id as unitId'
                )
                ->first();
            if ($admin) {
                $this->divsUnits = OfficeDivisionUnits::where('office_division_id' , $admin->divId)->get();
                $this->name = $admin->name;
                $this->user_role = $admin->user_role;
                $this->admin_email = $admin->email;
                $this->office_division = $admin->office_division;
                $this->unitName = $admin->unit;
                $this->unit = $admin->unitId;
                $this->position = $admin->position;
                $this->divId = $admin->divId;
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function toggleAddRole(){
        $this->editRole = true;
        $this->addRole = true;
    }

    public function saveRole(){
        try {
            $this->validate([
                'userId' => 'required',
                'user_role' => 'required',
            ]);

            $user = User::where('users.id', $this->userId)
                ->join('positions', 'positions.id', 'users.position_id')
                ->select('users.id', 'users.name', 'users.emp_code','positions.id as posId')
                ->first();

            if($user){
                if($this->addRole){
                    $admin = User::create([
                        'name' => $user->name,
                        'email' => $this->admin_email ?? null,
                        'password' => rand(100000, 999999),
                        'emp_code' => $this->user_role . '-' .$user->emp_code,
                        'user_role' => $this->user_role ?? null,
                        'active_status' => 1,
                        'position_id' => $user->posId ?? null,
                        'office_division_id' => $this->divId ?? null,
                        'unit_id' => is_numeric($this->unit) ? $this->unit : null,
                    ]);
                }else{
                    $admin = User::where('users.id', $this->userId)
                            ->first();

                    $this->validate([
                        'user_role' => 'required',
                    ]);

                    $admin->update([
                        'email' => $this->admin_email ?? null,
                        'user_role' => $this->user_role ?? null,
                        'office_division_id' => $this->divId ?? null,
                        'unit_id' => is_numeric($this->unit) ? $this->unit : null,
                    ]);
                }
            }
            $this->resetVariables();
            $this->dispatch('swal', [
                'title' => "Account role updated successfully!",
                'icon' => 'success'
            ]);
    
        } catch (Exception $e) {
            $this->resetVariables();
            $this->dispatch('swal', [
                'title' => "Account role update was unsuccessful! " . $e->getMessage(),
                'icon' => 'error'
            ]);
            throw $e;
        }
    } 


    public function toggleDelete($id, $message){
        $this->deleteMessage = $message;
        $this->deleteId = $id;
    }

    public function deleteData(){
        try {
            $message = null;

            if($this->data){
                if($this->data == "office/division"){
                    $officeDivisions = OfficeDivisions::where('id', $this->deleteId)->first();
                    $officeDivisions->delete();
                    $message = "Office/Division deleted successfully!";
                }else if($this->data == "position"){
                    $positions = Positions::where('id', $this->deleteId)->first();
                    $positions->delete();
                    $message = "Position deleted successfully!";
                }else if($this->data == "Salary Grade"){
                    $sg = SalaryGrade::where('id', $this->deleteId)->first();
                    $sg->delete();
                    $message = "Salary Grade deleted successfully!";
                }
            }else{
                $user = User::where('id', $this->deleteId)->first();
                if ($user) {
                    DB::statement('SET FOREIGN_KEY_CHECKS=0;');
                    switch($this->deleteMessage){
                        case "role":
                            $user->delete();
                            $user->admin()->delete();
                            $message = "Role deleted successfully!";
                            break;
                        case "payroll signatory":
                            $user->signatories()->where('signatory_type', 'payroll')->delete();
                            $message = "Payroll signatory deleted successfully!";
                            break;
                        case "payslip signatory":
                            $user->signatories()->where('signatory_type', 'payslip')->delete();
                            $message = "Payslip signatory deleted successfully!";
                            break;
                        default:
                            break;
                    }   
                    DB::statement('SET FOREIGN_KEY_CHECKS=1;');          
                }else if($this->deleteMessage == 'role access'){
                    $role = AdminRoleAccess::find($this->deleteId);
                    if($role){
                        $role->delete();
                        $message = "Admin role deleted successfully!";
                    }else{
                        $this->dispatch('swal', [
                            'title' => 'Something went wrong. Deletion of admin role was unsuccessful.',
                            'icon' => 'error'
                        ]);
                        return;
                    }
                }
            }

            $this->resetVariables();
            $this->dispatch('swal', [
                'title' => $message,
                'icon' => 'success'
            ]);
        } catch (Exception $e) {
            $this->dispatch('swal', [
                'title' => "Deletion of " . $this->deleteMessage . "was unsuccessful!",
                'icon' => 'error'
            ]);
            $this->resetVariables();
            throw $e;
        }
    }

    public function editSG($id){
        $this->isEditing = true;
        $this->editingId = $id;
        $salaryGrade = $this->salaryGrades->firstWhere('id', $id);
        
        $this->salaryGradeData = [
            'salary_grade' => $salaryGrade->salary_grade,
            'step1' => $salaryGrade->step1,
            'step2' => $salaryGrade->step2,
            'step3' => $salaryGrade->step3,
            'step4' => $salaryGrade->step4,
            'step5' => $salaryGrade->step5,
            'step6' => $salaryGrade->step6,
            'step7' => $salaryGrade->step7,
            'step8' => $salaryGrade->step8,
        ];
        
        $this->showSGModal = true;
    }

    public function openSGModal(){
        $this->showSGModal = true;
    }

    public function saveSalaryGrade(){
        try{
            $message = null;
            $this->validate([
                'salaryGradeData.salary_grade' => 'required|integer',
                'salaryGradeData.step1' => 'required|numeric',
                'salaryGradeData.step2' => 'required|numeric',
                'salaryGradeData.step3' => 'required|numeric',
                'salaryGradeData.step4' => 'required|numeric',
                'salaryGradeData.step5' => 'required|numeric',
                'salaryGradeData.step6' => 'required|numeric',
                'salaryGradeData.step7' => 'required|numeric',
                'salaryGradeData.step8' => 'required|numeric',
            ]);
            if ($this->isEditing) {
                SalaryGrade::find($this->editingId)->update($this->salaryGradeData);
                $message = "Salary Grade updated successfully!";
            } else {
                SalaryGrade::create($this->salaryGradeData);
                $message = "Salary Grade added successfully!";
            }
            $this->resetVariables();
            $this->dispatch('swal', [
                'title' => $message,
                'icon' => 'success'
            ]);
        }catch(Exception $e){
            throw $e;
        }
    }

    public function toggleDeleteSG($id, $data){
        $this->deleteId = $id;
        $this->data = $data;
        $this->deleteMessage = $data;
    }

    public function exportSalaryGrade(){
        $sgStep = SalaryGrade::all();
        $filters = [
            'sgStep' => $sgStep,
        ];
        return Excel::download(new SalaryGradeExport ($filters), 'Salary-Grades.xlsx');
    }

    public function importFromExcel(){
        $this->validate([
            'file' => 'required|file|mimes:xlsx,xls',
        ]);

        try {
            DB::beginTransaction();
            
            Excel::import(new SalaryGradeImport, $this->file);
            
            DB::commit();
            
            $this->dispatch('swal', [
                'title' => "Salary Grade imported successfully!",
                'icon' => 'success'
            ]);
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            DB::rollBack();
            $failures = $e->failures();
            $errorMessages = collect($failures)->map(function ($failure) {
                return "Row {$failure->row()}: {$failure->errors()[0]}";
            })->implode(', ');
            
            $this->dispatch('swal', [
                'title' => "Please upload the correct Salary Grade excel file!",
                'icon' => 'error'
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            $this->dispatch('swal', [
                'title' => 'An error occurred during import: ' . $e->getMessage(),
                'icon' => 'error'
            ]);
        }

        $this->file = null;
    }

    private function isPasswordComplex($password){
        $containsUppercase = preg_match('/[A-Z]/', $password);
        $containsNumber = preg_match('/\d/', $password);
        $containsSpecialChar = preg_match('/[^A-Za-z0-9]/', $password); // Changed regex to include special characters
        return $containsUppercase && $containsNumber && $containsSpecialChar;
    }

    public function resetVariables(){
        $this->resetValidation();
        $this->userId = null;
        $this->name = null;
        $this->employee_number = null;
        $this->position = null;
        $this->editRole = null;
        $this->addRole = null;
        $this->admin_email = null;
        $this->password = null;
        $this->cpassword = null;
        $this->office_division = null;
        $this->deleteId = null;
        $this->deleteMessage = null;
        $this->settings = null;
        $this->settingsId = null;
        $this->add = null;
        $this->settings_data = null;
        $this->settingsData = [['value' => '']];
        $this->units = [['value' => '']];
        $this->data = null;
        $this->showSGModal = null;
        $this->editingId = null;
        $this->salaryGradeData = [
            'salary_grade' => '',
            'step1' => '', 'step2' => '', 'step3' => '', 'step4' => '',
            'step5' => '', 'step6' => '', 'step7' => '', 'step8' => '',
        ];
        $this->activeStatus = null;
        $this->editPosition = null;
        $this->officeDivisionId = null;
        $this->unitId = null;
        $this->unit = null;
        $this->unitName = null;
        $this->roleName = null;
        $this->roleCode = null;
        $this->roleAccessModules = [];
        $this->resetRoleAccessForm();
    }

    public function toggleAddRoleAccess()
    {
        $this->showRoleAccessModal = true;
    }

    public function toggleEditRoleAccess($id)
    {
        $roleAccess = AdminRoleAccess::find($id);
        $this->editingRoleAccess = $roleAccess;

        if($roleAccess){
            $this->roleName = $roleAccess->role_name;
            $this->roleCode = $roleAccess->role_code;
            $this->roleAccessModules = explode(',', $roleAccess->modules);
        }

        $this->showRoleAccessModal = true;
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->roleAccessModules = $this->systemModules->pluck('id')->toArray();
        } else {
            $this->roleAccessModules = [];
        }
    }

    public function updatedRoleAccessModules()
    {
        $this->selectAll = count($this->roleAccessModules) === $this->systemModules->count();
    }

    public function toggleParentModule($parentModuleId)
    {
        $parentModule = $this->parentModules->find($parentModuleId);
        if (!$parentModule) return;

        $childModuleIds = $parentModule->systemModules->pluck('id')->toArray();
        
        $allChildrenSelected = !array_diff($childModuleIds, $this->roleAccessModules);
        
        if ($allChildrenSelected) {
            $this->roleAccessModules = array_diff($this->roleAccessModules, $childModuleIds);
        } else {
            $this->roleAccessModules = array_unique(array_merge($this->roleAccessModules, $childModuleIds));
        }
        
        $this->updatedRoleAccessModules();
    }

    public function isParentModuleChecked($parentModuleId)
    {
        $parentModule = $this->parentModules->find($parentModuleId);
        if (!$parentModule) return false;

        $childModuleIds = $parentModule->systemModules->pluck('id')->toArray();
        return count($childModuleIds) > 0 && !array_diff($childModuleIds, $this->roleAccessModules);
    }

    public function isParentModuleIndeterminate($parentModuleId)
    {
        $parentModule = $this->parentModules->find($parentModuleId);
        if (!$parentModule) return false;

        $childModuleIds = $parentModule->systemModules->pluck('id')->toArray();
        $selectedChildIds = array_intersect($childModuleIds, $this->roleAccessModules);
        
        return count($selectedChildIds) > 0 && count($selectedChildIds) < count($childModuleIds);
    }

    public function saveRoleAccess()
    { 
        try{
            $this->validate([
                'roleName' => 'required|string|max:255',
                'roleCode' => 'required|string|max:255|unique:admin_role_accesses,role_code,' . ($this->editingRoleAccess->id ?? 'NULL'),
                'roleAccessModules' => 'array'
            ]);

            $cleanModules = array_filter($this->roleAccessModules);
            $modulesString = implode(',', $cleanModules);
    
            if ($this->editingRoleAccess) {
                $this->editingRoleAccess->update([
                    'role_name' => $this->roleName,
                    'role_code' => $this->roleCode,
                    'modules' => $modulesString
                ]);
            } else {
                AdminRoleAccess::create([
                    'role_name' => $this->roleName,
                    'role_code' => $this->roleCode,
                    'modules' => $modulesString,
                    'hierarchy' => 5
                ]);
            }
    
            $this->resetRoleAccessForm();
            $this->dispatch('swal', [
                'title' => 'Admin role and access saved successfully.',
                'icon' => 'success'
            ]);
        }catch(Exception $e){
            $this->dispatch('swal', [
                'title' => 'Something went wrong. Admin role and access update was unsuccessful.',
                'icon' => 'error'
            ]);
            throw $e;
        }
    }

    public function getModulesForRoleAccess($roleAccess)
    {
        if (empty($roleAccess->modules)) {
            return collect();
        }
        
        $moduleIds = array_filter(explode(',', $roleAccess->modules));
        return SystemModules::whereIn('id', $moduleIds)->get();
    }

    private function resetRoleAccessForm()
    {
        $this->editingRoleAccess = null;
        $this->showRoleAccessModal = false;
        $this->roleName = null;
        $this->roleCode = null;
        $this->roleAccessModules = [];
    }
}
