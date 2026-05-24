<?php

namespace App\Livewire\User\PersonalData;

use App\Models\EmployeesEducation;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class EducationalBackground extends Component
{
    public $editEducBackground;
    public $addEducBackground;
    public $education = [];
    public $newEducation = [];
    public $deleteId;

    public function render()
    {
        $user = User::with(['employeesEducation'])->where('id', Auth::user()->id)->first();
        return view('livewire.user.personal-data.educational-background', [
            'educBackground' => $user->employeesEducation,
        ]);
    }

    public function toggleEditEducBackground(){
        $this->editEducBackground = true;
        try{
            $education = User::with(['employeesEducation'])->where('id', Auth::user()->id)->first()->toArray();
            $this->education = isset($education['employees_education']) ? $education['employees_education'] : [];

            foreach ($this->education as $index => $educ) {
                if (!empty($educ['toPresent']) && $educ['toPresent'] === 'Present') {
                    $this->education[$index]['toPresent'] = true;
                } else {
                    $this->education[$index]['toPresent'] = false;
                }
            }
        }catch(Exception $e){
            throw $e;
        }
    }

    public function cancelEduc(){
        $this->editEducBackground = false;
        $this->addEducBackground = false;
        $this->newEducation = [];
        $this->education = [];
    }

    public function toggleAddEducBackground(){
        $this->editEducBackground = true;
        $this->addEducBackground = true;
        $this->newEducation[] = [
            'level' => '', 
            'level_code' => '', 
            'name_of_school' => '',
            'basic_educ_degree_course' => '',
            'from' => '',
            'to' => '',
            'toPresent' => '',
            'highest_level_unit_earned' => '',
            'year_graduated' => '',
            'award' => '',
            'is_bachelor' => 0,
            'graduateStudy' => '',
        ];
    }

    public function addNewEducation(){
        $this->newEducation[] = [
            'level' => '', 
            'level_code' => '', 
            'name_of_school' => '',
            'basic_educ_degree_course' => '',
            'from' => '',
            'to' => '',
            'highest_level_unit_earned' => '',
            'year_graduated' => '',
            'award' => '',
            'is_bachelor' => 0,
            'graduateStudy' => '',
        ];
    }

    public function removeNewEducation($index){
        unset($this->newEducation[$index]);
        $this->newEducation = array_values($this->newEducation);
    }

    public function saveEducationBackground(){
        try {
            $user = Auth::user();
            if ($user) {

                if($this->addEducBackground != true){
                    foreach ($this->education as $index => $educ) {
                        $validationRules = [
                            'education.'.$index.'.level_code' => 'required|numeric',
                            'education.'.$index.'.name_of_school' => 'required|string',
                            'education.'.$index.'.from' => 'required|date',
                        ];
                
                        if (!$educ['toPresent']) {
                            $validationRules['education.'.$index.'.to'] = 'required|date';
                            $validationRules['education.'.$index.'.year_graduated'] = 'required|numeric';
                            $educ['toPresent'] = null;
                        } else {
                            $validationRules['education.'.$index.'.toPresent'] = 'required';
                            $educ['toPresent'] = 'Present';
                            $educ['to'] = null;
                        }
                
                        $this->validate($validationRules);

                        $educRecord = $user->employeesEducation->find($educ['id']);
                        if ($educRecord) {
                            $educRecord->update([
                                'level' => $educ['level'],
                                'name_of_school' => $educ['name_of_school'],
                                'from' => $educ['from'],
                                'to' => $educ['to'] ?: null,
                                'toPresent' => $educ['toPresent'] ?: null,
                                'basic_educ_degree_course' => $educ['basic_educ_degree_course'],
                                'award' => $educ['award'],
                                'highest_level_unit_earned' => $educ['highest_level_unit_earned'],
                                'year_graduated' => $educ['year_graduated'] ?: null,
                            ]);
                        }
                    }
                    
                    $this->editEducBackground = null;
                    $this->addEducBackground = null;
                    $this->newEducation = [];
                    $this->dispatch('swal', [
                        'title' => "Education background updated successfully!",
                        'icon' => 'success'
                    ]);
                }else{
                    foreach ($this->newEducation as $index => $educ) {
                        $validationRules = [
                            'newEducation.'.$index.'.level_code' => 'required|numeric',
                            'newEducation.'.$index.'.name_of_school' => 'required|string',
                            'newEducation.'.$index.'.from' => 'required|date',
                        ];
                
                        if (!$educ['toPresent']) {
                            $validationRules['newEducation.'.$index.'.to'] = 'required|date';
                            $validationRules['newEducation.'.$index.'.year_graduated'] = 'required|numeric';
                            $educ['toPresent'] = null;
                        }else {
                            $validationRules['newEducation.'.$index.'.toPresent'] = 'required';
                            $educ['toPresent'] = 'Present';
                            $educ['to'] = null;
                        }

                        if($educ['level_code'] == 5){
                            $validationRules['newEducation.'.$index.'.graduateStudy'] = 'required';
                        }
                
                        $this->validate($validationRules);

                        $level = '';
                        switch($educ['level_code']){
                            case 1:
                                $level = 'Elementary';
                                break;
                            case 2:
                                $level = 'Secondary';
                                break;
                            case 3:
                                $level = 'Vocational/Trade Course';
                                break;
                            case 4:
                                $level = 'College';
                                break;
                            case 5:
                                $level = 'Graduate Studies';
                                break;
                            default:
                                break;
                        }

                        $isMaster = 0;
                        $isDoctor = 0;
                        if($educ['graduateStudy'] == 'm'){
                            $isMaster = 1;
                            $isDoctor = 0;
                        }elseif($educ['graduateStudy'] == 'd'){
                            $isMaster = 0;
                            $isDoctor = 1;
                        }

                        EmployeesEducation::create([
                            'user_id' => $user->id,
                            'level_code' => $educ['level_code'],
                            'level' => $level,
                            'name_of_school' => $educ['name_of_school'],
                            'from' => $educ['from'],
                            'to' => $educ['to'] ?: null,
                            'toPresent' => $educ['toPresent'] ?: null,
                            'basic_educ_degree_course' => $educ['basic_educ_degree_course'],
                            'award' => $educ['award'],
                            'highest_level_unit_earned' => $educ['highest_level_unit_earned'],
                            'year_graduated' => $educ['year_graduated'] ?: null,
                            'is_bachelor' => $educ['is_bachelor'] ?: 0,
                            'is_master' => $isMaster,
                            'is_doctor' => $isDoctor,
                        ]);
                    }
                    
                    $this->editEducBackground = null;
                    $this->addEducBackground = null;
                    $this->newEducation = [];
                    $this->dispatch('swal', [
                        'title' => "Education background added successfully!",
                        'icon' => 'success'
                    ]);
                }
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function toggleDelete($id){
        $this->deleteId = $id;
    }

    public function deleteData(){
        try{
            $user = Auth::user();
            if($user){
                $educ = $user->employeesEducation->find($this->deleteId);

                if ($educ) {
                    $educ->delete();
                }

                $this->dispatch('swal', [
                    'title' => "Education deleted successfully!",
                    'icon' => 'success'
                ]);

                $this->deleteId = null;
            }
        }catch(Exception $e){
            $this->dispatch('swal', [
                'title' => "Deletion was unsuccessful!",
                'icon' => 'success'
            ]);
            throw $e;
        }
    }
}
