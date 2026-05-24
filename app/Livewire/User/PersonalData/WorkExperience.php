<?php

namespace App\Livewire\User\PersonalData;

use App\Models\ServiceRecords;
use App\Models\WorkExperience as Exp;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class WorkExperience extends Component
{
    public $workExperiences = [];
    public $newWorkExperiences = [];
    public $addWorkExperience = false;
    public $editWorkExperience = false;
    public $deleteId;

    public function render()
    {
        return view('livewire.user.personal-data.work-experience', [
            'workExperience' => $this->getWorkExp(),
        ]);
    }

    protected function getWorkExp(){
        $user = User::with(['workExperience'])->find(Auth::user()->id);
        return $user->workExperience;
    }

    public function toggleEditWorkExperience(){
        $this->resetVariables();
        
        $this->editWorkExperience = true;
        try{
            $user = User::with(['workExperience'])->find(Auth::user()->id);
            $this->workExperiences = $user->workExperience->toArray();
        }catch(Exception $e){
            throw $e;
        }
    }

    public function toggleAddWorkExperience(){
        $this->addWorkExperience = true;
        $this->newWorkExperiences[] = [
            'start_date' => '', 
            'end_date' => '',
            'toPresent' => '',
            'position' => '',
            'department' => '',
            'monthly_salary' => '',
            'status_of_appointment' => '',
            'gov_service' => '',
            'sg_step' => '',
            'pera' => '',
            'branch' => '',
            'leave_absence_wo_pay' => '',
            // 'separation_date' => '',
            // 'separation_cause' => '',
            'remarks' => '',
        ];
    }

    public function addNewWorkExp(){
        $this->newWorkExperiences[] = [
            'start_date' => '', 
            'end_date' => '',
            'toPresent' => '',
            'position' => '',
            'department' => '',
            'monthly_salary' => '',
            'status_of_appointment' => '',
            'gov_service' => '',
            'sg_step' => '',
            'pera' => '',
            'branch' => '',
            'leave_absence_wo_pay' => '',
            // 'separation_date' => '',
            // 'separation_cause' => '',
            'remarks' => '',
        ];
    }

    public function removeNewWorkExp($index){
        unset($this->newWorkExperiences[$index]);
        $this->newWorkExperiences = array_values($this->newWorkExperiences);
    }

    public function cancelEdit()
    {
        $this->resetVariables();
    }

    public function saveWorkExp(){
        try {
            $user = Auth::user();
            if ($user) {
                if($this->addWorkExperience && !empty($this->newWorkExperiences)){
                    foreach ($this->newWorkExperiences as $index => $exp) {
                        $validationRules = [
                            'newWorkExperiences.'.$index.'.position' => 'required|string',
                            'newWorkExperiences.'.$index.'.department' => 'required',
                            'newWorkExperiences.'.$index.'.start_date' => 'required|date',
                            'newWorkExperiences.'.$index.'.gov_service' => 'required',
                            'newWorkExperiences.'.$index.'.status_of_appointment' => 'required|string',
                        ];

                        $this->validate($validationRules);

                        Exp::create([
                            'user_id' => $user->id,
                            'start_date' => $exp['start_date'],
                            'end_date' => $exp['end_date'] ?: null,
                            'toPresent' => $exp['end_date'] ? null : 'Present',
                            'position' => $exp['position'] ?: null,
                            'department' => $exp['department'] ?: null,
                            'monthly_salary' => $exp['monthly_salary'] ?: null,
                            'sg_step' => $exp['sg_step'] ?: null,
                            'status_of_appointment' => $exp['status_of_appointment'] ?: null,
                            'gov_service' => $exp['gov_service'],
                            'pera' => $exp['gov_service'] && $exp['pera'] ? $exp['pera'] : null,
                            'branch' => $exp['gov_service'] && $exp['branch'] ? $exp['branch'] : null,
                            'leave_absence_wo_pay' => $exp['gov_service'] && $exp['leave_absence_wo_pay'] ? $exp['leave_absence_wo_pay'] : null,
                            // 'separation_date' => $exp['separation_date'],
                            // 'separation_cause' => $exp['separation_cause'],
                            'remarks' => $exp['gov_service'] && $exp['remarks'] ? $exp['remarks'] : null,
                        ]);

                        // if($exp['gov_service']){
                        //     ServiceRecords::create([
                        //         'user_id' => $user->id,
                        //         'from' => $exp['start_date'] ?: null,
                        //         'to' => $exp['end_date'] ?: null,
                        //         'toPresent' => $exp['toPresent'] ?: null,
                        //         'designation' => $exp['position'] ?: '--do--',
                        //         'status' => $exp['status_of_appointment'] ?: '--do--',
                        //         'salary_annum' => $exp['monthly_salary'] * 12,
                        //         'station_place_of_assignment' => $exp['department'] ?: '--do--',
                        //         'branch' => $exp['branch'] ?: '--do--',
                        //         'lv_abs_wo_pay' => $exp['leave_absence_wo_pay'] ?: '--do--',
                        //         'remarks' => $exp['remarks'] ?: '--do--',
                        //     ]);
                        // }
                    }
                    
                    $this->resetVariables();
                    $this->dispatch('swal', [
                        'title' => "Work Experience added successfully!",
                        'icon' => 'success'
                    ]);
                    
                } elseif($this->editWorkExperience && !empty($this->workExperiences)){
                    foreach ($this->workExperiences as $index => $exp) {
                        $validationRules = [
                            'workExperiences.'.$index.'.position' => 'required|string',
                            'workExperiences.'.$index.'.department' => 'required|string',
                            'workExperiences.'.$index.'.start_date' => 'required|date',
                            'workExperiences.'.$index.'.gov_service' => 'required',
                            'workExperiences.'.$index.'.status_of_appointment' => 'required|string',
                        ];

                        $this->validate($validationRules);

                        $expRecord = $user->workExperience->find($exp['id']);
                        if ($expRecord) {
                            $expRecord->update([
                                'start_date' => $exp['start_date'],
                                'end_date' => $exp['end_date'] ?: null,
                                'toPresent' => $exp['end_date'] ? null : 'Present',
                                'position' => $exp['position'] ?: null,
                                'department' => $exp['department'] ?: null,
                                'monthly_salary' => $exp['monthly_salary'] ?: null,
                                'sg_step' => $exp['sg_step'] ?: null,
                                'status_of_appointment' => $exp['status_of_appointment'] ?: null,
                                'gov_service' => $exp['gov_service'],
                                'pera' => $exp['gov_service'] ? $exp['pera'] : null,
                                'branch' => $exp['gov_service'] ? $exp['branch'] : null,
                                'leave_absence_wo_pay' => $exp['gov_service'] ? $exp['leave_absence_wo_pay'] : null,
                                // 'separation_date' => $exp['separation_date'],
                                // 'separation_cause' => $exp['separation_cause'],
                                'remarks' => $exp['gov_service'] ? $exp['remarks'] : null,
                            ]);
                        }

                        // $existingRecord = ServiceRecords::where('user_id', $user->id)
                        //     ->where('from', $exp['start_date'])
                        //     ->first();
                
                        // if ($existingRecord) {
                        //     $existingRecord->update([
                        //         'from' => $exp['start_date'] ?: null,
                        //         'to' => $exp['end_date'] ?: null,
                        //         'toPresent' => $exp['toPresent'] ?: null,
                        //         'designation' => $exp['position'] ?: '--do--',
                        //         'status' => $exp['status_of_appointment'] ?: '--do--',
                        //         'salary_annum' => $exp['monthly_salary'] * 12,
                        //         'station_place_of_assignment' => $exp['department'] ?: '--do--',
                        //         'branch' => $exp['branch'] ?: '--do--',
                        //         'lv_abs_wo_pay' => $exp['leave_absence_wo_pay'] ?: '--do--',
                        //         'remarks' => $exp['remarks'] ?: '--do--',
                        //     ]);
                        // }
                    }

                    $this->resetVariables();
                    $this->dispatch('swal', [
                        'title' => "Work Experience updated successfully!",
                        'icon' => 'success'
                    ]);
                }

                $this->getWorkExp();
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function resetVariables(){
        $this->editWorkExperience = false;
        $this->addWorkExperience = false;
        $this->newWorkExperiences = [];
        $this->workExperiences = [];
        $this->resetValidation();
    }

    public function toggleDelete($id){
        $this->deleteId = $id;
    }

    public function deleteData(){
        try{
            $user = Auth::user();
            if($user){
                $exp = $user->workExperience->find($this->deleteId);

                if ($exp) {
                    $exp->delete();
                }

                $this->dispatch('swal', [
                    'title' => "Work experience deleted successfully!",
                    'icon' => 'success'
                ]);

                $this->deleteId = null;
            }
        }catch(Exception $e){
            $this->dispatch('swal', [
                'title' => "Deletion was unsuccessful!",
                'icon' => 'error'
            ]);
            throw $e;
        }
    }
}