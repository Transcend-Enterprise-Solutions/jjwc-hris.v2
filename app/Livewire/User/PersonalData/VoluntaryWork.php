<?php

namespace App\Livewire\User\PersonalData;

use App\Models\User;
use App\Models\VoluntaryWorks;
use Exception;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class VoluntaryWork extends Component
{
    public $voluntaryWork = [];
    public $newVoluntaryWorks = [];
    public $deleteId;
    public $addVoluntaryWork;
    public $editVoluntaryWork;

    public function render()
    {
        return view('livewire.user.personal-data.voluntary-work', [
            'voluntaryWorks' => $this->getVoluntaryWorks(),
        ]);
    }

    protected function getVoluntaryWorks(){
        $user = User::with(['voluntaryWorks'])->find(Auth::user()->id);
        return $user->voluntaryWorks;
    }

    public function toggleEditVoluntaryWork(){
        $this->editVoluntaryWork = true;
        try{
            $user = User::with(['voluntaryWorks'])->find(Auth::user()->id);
            $this->voluntaryWork = $user->voluntaryWorks->toArray();
        }catch(Exception $e){
            throw $e;
        }
    }

    public function toggleAddVoluntaryWork(){
        $this->editVoluntaryWork = true;
        $this->addVoluntaryWork = true;
        $this->newVoluntaryWorks[] = [
            'org_name' => '', 
            'org_address' => '',
            'start_date' => '',
            'end_date' => '',
            'toPresent' => '',
            'no_of_hours' => '',
            'position_nature' => '',
        ];
    }

    public function addNewVoluntaryWork(){
        $this->newVoluntaryWorks[] = [
            'org_name' => '', 
            'org_address' => '',
            'start_date' => '',
            'toPresent' => '',
            'end_date' => '',
            'no_of_hours' => '',
            'position_nature' => '',
        ];
    }
    public function removeNewVoluntaryWork($index){
        unset($this->newVoluntaryWorks[$index]);
        $this->newVoluntaryWorks = array_values($this->newVoluntaryWorks);
    }
    
    public function saveVoluntaryWork(){
        try {
            $user = Auth::user();
            if ($user) {

                if($this->addVoluntaryWork != true){
                    foreach ($this->voluntaryWork as $index => $work) {
                        $validationRules = [
                            'voluntaryWork.'.$index.'.org_name' => 'required|string',
                            'voluntaryWork.'.$index.'.org_address' => 'required|string',
                            'voluntaryWork.'.$index.'.no_of_hours' => 'required|numeric',
                            'voluntaryWork.'.$index.'.start_date' => 'required|date',
                            'voluntaryWork.'.$index.'.position_nature' => 'required|string',
                        ];
                
                        if (!$work['toPresent']) {
                            $validationRules['voluntaryWork.'.$index.'.end_date'] = 'required|date';
                            $work['toPresent'] = null;
                        } else {
                            $validationRules['voluntaryWork.'.$index.'.toPresent'] = 'required';
                            $work['toPresent'] = 'Present';
                            $work['end_date'] = null;
                        }
                
                        $this->validate($validationRules);

                        $workRecord = $user->voluntaryWorks->find($work['id']);
                        if ($workRecord) {
                            $workRecord->update([
                                'start_date' => $work['start_date'],
                                'end_date' => $work['end_date'] ?: null,
                                'toPresent' => $work['toPresent'] ?: null,
                                'org_name' => $work['org_name'],
                                'org_address' => $work['org_address'],
                                'no_of_hours' => $work['no_of_hours'],
                                'position_nature' => $work['position_nature'],
                            ]);
                        }
                    }
                    $this->editVoluntaryWork = null;
                    $this->addVoluntaryWork = null;
                    $this->dispatch('swal', [
                        'title' => "Voluntary Works updated successfully!",
                        'icon' => 'success'
                    ]);
                }else{
                    foreach ($this->newVoluntaryWorks as $index => $work) {
                        $validationRules = [
                            'newVoluntaryWorks.'.$index.'.org_name' => 'required|string',
                            'newVoluntaryWorks.'.$index.'.org_address' => 'required|string',
                            'newVoluntaryWorks.'.$index.'.no_of_hours' => 'required|numeric',
                            'newVoluntaryWorks.'.$index.'.start_date' => 'required|date',
                            'newVoluntaryWorks.'.$index.'.position_nature' => 'required|string',
                        ];
                
                        if (!$work['toPresent']) {
                            $validationRules['newVoluntaryWorks.'.$index.'.end_date'] = 'required|date';
                            $work['toPresent'] = null;
                        } else {
                            $validationRules['newVoluntaryWorks.'.$index.'.toPresent'] = 'required';
                            $work['toPresent'] = 'Present';
                            $work['end_date'] = null;
                        }
                
                        $this->validate($validationRules);

                        VoluntaryWorks::create([
                            'user_id' => $user->id,
                            'start_date' => $work['start_date'],
                            'end_date' => $work['end_date'],
                            'toPresent' => $work['toPresent'],
                            'org_name' => $work['org_name'],
                            'org_address' => $work['org_address'],
                            'no_of_hours' => $work['no_of_hours'],
                            'position_nature' => $work['position_nature'],
                        ]);
                    }
                    $this->editVoluntaryWork = null;
                    $this->addVoluntaryWork = null;
                    $this->dispatch('swal', [
                        'title' => "Voluntary Works added successfully!",
                        'icon' => 'success'
                    ]);
                }
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function cancelEdit()
    {
        $this->resetVariables();
    }

    public function resetVariables(){
        $this->editVoluntaryWork = false;
        $this->addVoluntaryWork  = false;
        $this->newVoluntaryWorks = [];
        $this->voluntaryWork = [];
        $this->resetValidation();
    }

    public function toggleDelete($id){
        $this->deleteId = $id;
    }

    public function deleteData(){
        try{
            $user = Auth::user();
            if($user){
                $vWork = $user->voluntaryWorks->find($this->deleteId);

                if ($vWork) {
                    $vWork->delete();
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
