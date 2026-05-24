<?php

namespace App\Livewire\Admin\Employee;

use App\Models\EmployeesContract;
use App\Models\EmploymentAppointment;
use App\Models\EmploymentStatus;
use App\Models\OfficeDivisions;
use App\Models\OfficeDivisionUnits;
use App\Models\Positions;
use App\Models\User;
use App\Models\UserData;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;


class Job extends Component
{
    use WithPagination, WithFileUploads;

    public $selectedUser;
    public $selectedUserId;

    public $editContract = false;
    public $addContract = false;
    public $contractId;
    public $deleteId;
    public $contract_number;
    public $contract_type;
    public $start_date;
    public $end_date;
    public $contract_details;
    public $status = 'active';
    public $date_created;
    public $oldContractDetails;


    public $userId;
    public $name;
    public $employeeId;
    public $editPosition;
    public $employmentStatus;
    public $statusRemarks;
    public $positionId;
    public $appointment;
    public $officeDivisionId;
    public $unitId;


    public $officeDivisions;
    public $divsUnits;
    public $positions;
    public $appointments;
    public $statuses;



    public function mount($userId)
    {
        $user = User::with(['position', 'officeDivision', 'officeDivisionUnit', 'userData', 'contracts'])
            ->find($userId);

        if ($user) {
            $activeContracts = $user->contracts->filter(function($contract) {
                return $contract->status === 'active' && 
                    $contract->start_date <= now() && 
                    ($contract->end_date === null || $contract->end_date >= now());
            });

            $expiredContracts = $user->contracts->filter(function($contract) {
                return $contract->status !== 'active' || 
                    ($contract->end_date !== null && $contract->end_date < now());
            });

            $user->active_contracts = $activeContracts;
            $user->expired_contracts = $expiredContracts;

            $this->selectedUser = $user;
        }

        $this->positions = Positions::where('position', '!=', 'Super Admin')->get();
        $this->officeDivisions = OfficeDivisions::all();
        $this->appointments = EmploymentAppointment::all();
        $this->statuses = EmploymentStatus::all();
    }

    public function render()
    {
        if($this->selectedUser) {
             $user = User::with(['position', 'officeDivision', 'officeDivisionUnit', 'userData', 'contracts'])
                ->find($this->selectedUser->id);

            if ($user) {
                $activeContracts = $user->contracts->filter(function($contract) {
                    return $contract->status === 'active' && 
                        $contract->start_date <= now() && 
                        ($contract->end_date === null || $contract->end_date >= now());
                });

                $expiredContracts = $user->contracts->filter(function($contract) {
                    return $contract->status !== 'active' || 
                        ($contract->end_date !== null && $contract->end_date < now());
                });

                $user->active_contracts = $activeContracts;
                $user->expired_contracts = $expiredContracts;

                $this->selectedUser = $user;
            }
        }

        if($this->officeDivisionId){
            $this->divsUnits = OfficeDivisionUnits::where('office_division_id' , $this->officeDivisionId)->get();
        }

        return view('livewire.admin.employee.job');
    }

    protected function contractRules()
    {
        return [
            'contract_number' => 'required|string|max:255',
            'contract_type' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'contract_details' => 'nullable|file|max:5120|mimes:pdf,png,jpg,jpeg,doc,docx', // 5MB max
            'status' => 'required|string|max:255',
            'date_created' => 'required|date',
        ];
    }

    public function addNewContract($userId = null)
    {
        $this->addContract = true;
        $this->editContract = true;
        $this->date_created = now()->format('Y-m-d');
        
        if ($userId) {
            $this->selectedUserId = $userId;
        }
    }

    public function editContractModal($contractId)
    {
        $this->addContract = false;
        $this->editContract = true;
        $this->contractId = $contractId;

        $contract = EmployeesContract::find($contractId);
        
        if ($contract) {
            $this->contract_number = $contract->contract_number;
            $this->contract_type = $contract->contract_type;
            $this->start_date = $contract->start_date ?? null;
            $this->end_date = $contract->end_date ?? null;
            $this->oldContractDetails = $contract->contract_details;
            $this->status = $contract->status;
            $this->date_created = $contract->date_created ?? null;
        }
    }

    public function saveContract()
    {
        $this->validate($this->contractRules());

        try {
            if ($this->addContract) {
                $contract = new EmployeesContract();
                $contract->user_id = $this->selectedUserId ?? $this->selectedUser->id;
                $contract->created_by = auth()->id();
            } else {
                $contract = EmployeesContract::find($this->contractId);
                
                if (!$contract) {
                    $this->dispatch('showAlert', [
                        'type' => 'error',
                        'message' => 'Contract not found!'
                    ]);
                    return;
                }
            }

            // Handle file upload
            if ($this->contract_details) {
                if (!$this->addContract && $contract->contract_details && Storage::disk('public')->exists($contract->contract_details)) {
                    Storage::disk('public')->delete($contract->contract_details);
                }

                $originalName = $this->contract_details->getClientOriginalName();
                $contractDetailsPath = $this->contract_details->storeAs('contract_documents', $originalName, 'public');
            } else {
                $contractDetailsPath = $this->addContract ? null : ($contract->contract_details ?? null);
            }

            // Set contract data
            $contract->contract_number = $this->contract_number;
            $contract->contract_type = $this->contract_type;
            $contract->start_date = $this->start_date;
            $contract->end_date = $this->end_date;
            $contract->contract_details = $contractDetailsPath;
            $contract->status = $this->status;
            $contract->date_created = $this->date_created;

            $contract->save();

            $this->dispatch('showAlert', [
                'type' => 'success',
                'message' => $this->addContract ? 'Contract added successfully!' : 'Contract updated successfully!'
            ]);

            $this->resetVariables();
            $this->editContract = false;

            // Refresh user data if viewing a specific user
            if ($this->selectedUser) {
                $this->showUser($this->selectedUser->id);
            }

        } catch (Exception $e) {
            $this->dispatch('showAlert', [
                'type' => 'error',
                'message' => 'Error saving contract: ' . $e->getMessage()
            ]);
        }
    }

    public function downloadContractDocument($contractId)
    {
        $contract = EmployeesContract::find($contractId);
        
        if ($contract && $contract->contract_details && Storage::disk('public')->exists($contract->contract_details)) {
            $fileContent = Storage::disk('public')->get($contract->contract_details);
            $fileName = basename($contract->contract_details);

            return response()->streamDownload(function () use ($fileContent) {
                echo $fileContent;
            }, $fileName);
        }
        
        $this->dispatch('showAlert', [
            'type' => 'error',
            'message' => 'Contract document not found!'
        ]);
    }

    public function toggleDelete($id){
        $this->deleteId = $id;
    }

    public function deleteData(){
        try {
            $contract = EmployeesContract::where('id', $this->deleteId)->first();

            if (!$contract) {
                $this->dispatch('swal', [
                    'title' => "Contract not found!",
                    'icon' => 'error'
                ]);
                return;
            }

            if ($contract->contract_details && Storage::disk('public')->exists($contract->contract_details)) {
                Storage::disk('public')->delete($contract->contract_details);
            }

            $contract->delete();
            $message = "Contract deleted successfully!";

            $this->resetVariables();
            $this->dispatch('swal', [
                'title' => $message,
                'icon' => 'success'
            ]);
        } catch (Exception $e) {
            $this->dispatch('swal', [
                'title' => "Deletion of contract was unsuccessful!",
                'icon' => 'error'
            ]);
            $this->resetVariables();
            throw $e;
        }
    }

    public function toggleEditPosition($userId){
        $this->editPosition = true;
        try {
            $empPos = User::where('users.id', $userId)
                    ->leftJoin('positions', 'positions.id', 'users.position_id')
                    ->leftJoin('office_divisions', 'office_divisions.id', 'users.office_division_id')
                    ->leftJoin('office_division_units', 'office_division_units.id', 'users.unit_id')
                    ->select('users.*', 'positions.position', 'office_divisions.office_division', 'office_division_units.unit')
                    ->first();
            if ($empPos) {
                $this->userId = $empPos->id;
                $this->name = $empPos->name;
                $this->employeeId = $empPos->emp_code;
                $this->positionId = $empPos->position_id;
                $this->officeDivisionId = $empPos->office_division_id;
                $this->unitId = $empPos->unit_id;
                $this->employmentStatus = $empPos->active_status;
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function savePosition() {
        try {
            $this->validate([
                'employeeId' => 'required|string|max:255|unique:users,emp_code,' . $this->userId,
                'positionId' => 'required',
                'officeDivisionId' => 'required',
            ]);

            $empPos = User::where('users.id', $this->userId)->first();

            if ($empPos) {
                $userData = UserData::where('user_id', $empPos->id)->first();

                DB::beginTransaction();
                try {
                    $oldEmpCode = $empPos->emp_code;
                    $newEmpCode = $this->employeeId;

                    // Only update related tables if emp_code is being changed
                    if ($oldEmpCode != $newEmpCode) {
                        // Update transactions table
                        DB::table('transactions')
                            ->where('emp_code', $oldEmpCode)
                            ->update(['emp_code' => $newEmpCode]);

                        // Update transactions_wfh table
                        DB::table('transactions_wfh')
                            ->where('emp_code', $oldEmpCode)
                            ->update(['emp_code' => $newEmpCode]);

                        // Update dtrschedules table
                        DB::table('dtrschedules')
                            ->where('emp_code', $oldEmpCode)
                            ->update(['emp_code' => $newEmpCode]);
                    }

                    $empPos->update([
                        'emp_code' => $newEmpCode,
                        'position_id' => $this->positionId,
                        'office_division_id' => $this->officeDivisionId,
                        'unit_id' => $this->unitId,
                        'active_status' => $this->employmentStatus,
                    ]);

                    if($this->appointment){
                        $userData->update([
                            'appointment' =>$this->appointment,
                        ]);
                    }

                    DB::commit();

                    $this->dispatch('swal', [
                        'title' => 'Employee settings updated successfully!',
                        'icon' => 'success'
                    ]);
                    $this->resetVariables();
                } catch (Exception $e) {
                    DB::rollBack();
                    throw $e;
                }
            }
        } catch (Exception $e) {
            throw $e;
        }
    }  

    public function resetVariables()
    {
        $this->deleteId = null;
        $this->contractId = null;
        $this->contract_number = null;
        $this->contract_type = null;
        $this->start_date = null;
        $this->end_date = null;
        $this->contract_details = null;
        $this->oldContractDetails = null;
        $this->status = 'active';
        $this->date_created = null;
        $this->addContract = false;
        $this->editContract = false;
        $this->editPosition = null;
        $this->unitId = null;
        $this->officeDivisionId = null;
        $this->positionId = null;
        $this->employmentStatus = null;
        $this->appointment = null;
        $this->employeeId = null;
        $this->resetValidation();
    }

}