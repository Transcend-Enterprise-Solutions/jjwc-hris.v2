<?php

namespace App\Livewire\User;

use App\Models\ESignature;
use App\Models\WESESigSettings;
use App\Models\WorkExperienceSheetTable as WES;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('Work Experience Sheet')]
class WorkExperienceSheet extends Component
{
    public $pdfContent;
    public $sigXPos;
    public $sigYPos;
    public $sigSize;
    public $moveResizeSig = false;
    public $addWorkExp;
    public $editWorkExp;
    public $accoms_cont;
    public $workExperiences = [];
    public $newWorkExperiences = [];
    public $deleteId;

    public function mount(){
        // $userId = Auth::user()->id;
        // $wesSetting = WESESigSettings::where('user_id', $userId)->first();
        // if(!$wesSetting){
        //     WESESigSettings::create([
        //         'user_id' => $userId,
        //         'pos_x' => 110,
        //         'pos_y' => -50,
        //         'size' => 100,
        //     ]);
        // }
    }


    public function render()
    {
        $this->showPDF();

        return view('livewire.user.work-experience-sheet');
    }

    public function toggleMoveResizeSig(){
        $this->moveResizeSig = !$this->moveResizeSig;
    }

    public function showPDF(){
        $userId = Auth::user()->id;
        $name = Auth::user()->name;
        $eSignature = ESignature::where('user_id', $userId)->first();
        $signatureImagePath = null;
        if ($eSignature && $eSignature->file_path) {
            $signatureImagePath = Storage::disk('public')->path($eSignature->file_path);
        }

        $myWorkExperiences = WES::where('user_id', $userId)
                ->orderBy('toPresent', 'desc')
                ->orderBy('start_date', 'desc')
                ->get();

        $wesSetting = WESESigSettings::where('user_id', $userId)->first();
        if(!$wesSetting){
            $this->sigXPos = 110;
            $this->sigYPos = -50;
            $this->sigSize = 100;
        }else{
            $this->sigXPos = $wesSetting->pos_x;
            $this->sigYPos = $wesSetting->pos_y;
            $this->sigSize = $wesSetting->size;
        }


        $pdf = PDF::loadView('pdf.wes', [
            'name' => $name,
            'myWorkExperiences' => $myWorkExperiences,
            'signatureImagePath' => $signatureImagePath,
            'sigXPos' => $this->sigXPos,
            'sigYPos' => $this->sigYPos,
            'sigSize' => $this->sigSize,
        ]);

        $this->pdfContent = base64_encode($pdf->output());
    }

    public function addNewWorkExp(){
        $this->newWorkExperiences[] = [
            'start_date' => '', 
            'end_date'=> '',
            'toPresent'=> '',
            'position'=> '',
            'office_unit'=> '',
            'supervisor'=> '',
            'agency_org'=> '',
            'list_accomp_cont'=> [],
            'sum_of_duties'=> '',
        ];
    }

    public function toggleAddWorkExp(){
        $this->editWorkExp = true;
        $this->addWorkExp = true;
        $this->newWorkExperiences[] = [
            'start_date' => '', 
            'end_date'=> '',
            'toPresent'=> '',
            'position'=> '',
            'office_unit'=> '',
            'supervisor'=> '',
            'agency_org'=> '',
            'list_accomp_cont'=> [],
            'sum_of_duties'=> '',
        ];
    }

    public function removeWorkExp($index){
        unset($this->workExperiences[$index]);
        $this->workExperiences = array_values($this->workExperiences);
    }

    public function removeNewWorkExp($index){
        unset($this->newWorkExperiences[$index]);
        $this->newWorkExperiences = array_values($this->newWorkExperiences);
    }

    public function addWorkAccomplishment($index){
        $this->workExperiences[$index]['list_accomp_cont'][] = $this->accoms_cont;
        $this->accoms_cont = '';
    }

    public function addAccomplishment($index){
        $this->newWorkExperiences[$index]['list_accomp_cont'][] = $this->accoms_cont;
        $this->accoms_cont = '';
    }

    public function removeWorkAccomplishment($index, $i){
        unset($this->workExperiences[$index]['list_accomp_cont'][$i]);
        $this->workExperiences = array_values($this->workExperiences);
    }

    public function removeAccomplishment($index, $i){
        unset($this->newWorkExperiences[$index]['list_accomp_cont'][$i]);
        $this->newWorkExperiences = array_values($this->newWorkExperiences);
    }

    public function saveWorkExp(){
        try {
            $user = Auth::user();
            if ($user) {
                if(!$this->addWorkExp){
                    foreach ($this->workExperiences as $index => $exp) {
                        $validationRules = [
                            'workExperiences.'.$index.'.position' => 'required',
                            'workExperiences.'.$index.'.start_date' => 'required|date',
                            'workExperiences.'.$index.'.office_unit' => 'required',
                            'workExperiences.'.$index.'.supervisor' => 'required',
                            'workExperiences.'.$index.'.agency_org' => 'required',
                            'workExperiences.'.$index.'.sum_of_duties' => 'required',
                        ];
                
                        if (!$exp['toPresent']) {
                            $validationRules['workExperiences.'.$index.'.end_date'] = 'required|date';
                            $exp['toPresent'] = null;
                        } else {
                            $validationRules['workExperiences.'.$index.'.toPresent'] = 'required';
                            $exp['toPresent'] = 1;
                            $exp['end_date'] = null;
                        }
                
                        $this->validate($validationRules);

                        $expRecord = $user->workExperienceSheet->find($exp['id']);
                        if ($expRecord) {
                            $expRecord->update([
                                'start_date' => $exp['start_date'],
                                'end_date' => $exp['end_date'] ?: null,
                                'toPresent' => $exp['toPresent'] ?: null,
                                'position' => $exp['position'],
                                'office_unit'=> $exp['office_unit'],
                                'supervisor'=> $exp['supervisor'],
                                'agency_org'=> $exp['agency_org'],
                                'list_accomp_cont'=> implode('|', $exp['list_accomp_cont']),
                                'sum_of_duties'=> $exp['sum_of_duties'],
                            ]);
                        }
                    }

                    $this->editWorkExp = null;
                    $this->addWorkExp = null;
                    $this->dispatch('swal', [
                        'title' => "Work Experience updated successfully!",
                        'icon' => 'success'
                    ]);
                }else{
                    foreach ($this->newWorkExperiences as $index => $exp) {
                        $validationRules = [
                            'newWorkExperiences.'.$index.'.position' => 'required',
                            'newWorkExperiences.'.$index.'.start_date' => 'required|date',
                            'newWorkExperiences.'.$index.'.office_unit' => 'required',
                            'newWorkExperiences.'.$index.'.supervisor' => 'required',
                            'newWorkExperiences.'.$index.'.agency_org' => 'required',
                            'newWorkExperiences.'.$index.'.sum_of_duties' => 'required',
                        ];
                
                        if (!$exp['toPresent']) {
                            $validationRules['newWorkExperiences.'.$index.'.end_date'] = 'required|date';
                            $exp['toPresent'] = 0;
                        } else {
                            $validationRules['newWorkExperiences.'.$index.'.toPresent'] = 'required';
                            $exp['toPresent'] = 1;
                            $exp['end_date'] = null;
                        }

                        $this->validate($validationRules);

                        WES::create([
                            'user_id' => $user->id,
                            'start_date' => $exp['start_date'],
                            'end_date' => $exp['end_date'] ?: null,
                            'toPresent' => $exp['toPresent'] ?: null,
                            'position' => $exp['position'],
                            'office_unit'=> $exp['office_unit'],
                            'supervisor'=> $exp['supervisor'],
                            'agency_org'=> $exp['agency_org'],
                            'list_accomp_cont'=> implode('|', $exp['list_accomp_cont']),
                            'sum_of_duties'=> $exp['sum_of_duties'],
                        ]);
                        
                    }

                    $this->editWorkExp = null;
                    $this->addWorkExp = null;
                    $this->newWorkExperiences = [];
                    $this->dispatch('swal', [
                        'title' => "Work Experience added successfully!",
                        'icon' => 'success'
                    ]);
                }
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function toggleEditWorkExp(){
        $this->editWorkExp = true;
        try{
            $workExperiences = WES::where('user_id', Auth::user()->id)
                ->orderBy('toPresent', 'desc')
                ->orderBy('start_date', 'desc')
                ->get()->toArray();

            // Convert 'list_accomp_cont' from string to array
            foreach ($workExperiences as &$workExperience) {
                if (isset($workExperience['list_accomp_cont']) && is_string($workExperience['list_accomp_cont'])) {
                    $workExperience['list_accomp_cont'] = explode('|', $workExperience['list_accomp_cont']);
                } else {
                    $workExperience['list_accomp_cont'] = [];
                }
            }

            $this->workExperiences = $workExperiences;
        
        }catch(Exception $e){
            throw $e;
        }
    }

    public function toggleDelete($id){
        $this->deleteId = $id;
    }

    public function deleteData(){
        try{
            $workExp = WES::where('id', $this->workExperiences[$this->deleteId]['id'])->first();
            if($workExp){
                $workExp->delete();
                $this->dispatch('swal', [
                    'title' => "Work Experience deleted successfully!",
                    'icon' => 'success'
                ]);
                $this->resetVariables();
            }else{
                $this->dispatch('swal', [
                    'title' => "Work Experience deletion was unsuccessful!",
                    'icon' => 'error'
                ]);
                $this->resetVariables();
            }
        }catch(Exception $e){
            throw $e;
        }
    }

    public function resetVariables(){
        $this->addWorkExp = null;
        $this->editWorkExp = null;
        $this->accoms_cont = null;
        $this->workExperiences = [];
        $this->newWorkExperiences = [];
        $this->deleteId = null;
    }
}
