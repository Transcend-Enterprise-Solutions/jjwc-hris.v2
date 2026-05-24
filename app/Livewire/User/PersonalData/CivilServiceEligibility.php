<?php

namespace App\Livewire\User\PersonalData;

use App\Models\Eligibility;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CivilServiceEligibility extends Component
{
    public $eligibilities = [];
    public $newEligibilities = [];
    public $addEligibility = false;
    public $editEligibility = false;
    public $editingEligibility = false;
    public $deleteId;


    public function render()
    {
        $user = User::with(['eligibility'])->find(Auth::user()->id);
        return view('livewire.user.personal-data.civil-service-eligibility', [
            'eligibility' => $user->eligibility,
        ]);
    }

    public function toggleEditEligibility(){
        $this->editEligibility = true;
        try{
            $user = User::with(['eligibility'])->find(Auth::user()->id);
            $this->eligibilities = $user->eligibility->toArray();
        }catch(Exception $e){
            throw $e;
        }
    }

    public function toggleAddEligibility(){
        $this->editEligibility = true;
        $this->addEligibility = true;
        $this->newEligibilities[] = [
            'eligibility' => '', 
            'rating' => '',
            'date' => '',
            'place_of_exam' => '',
            'license' => '',
            'date_of_validity' => '',
        ];
    }

    public function cancelEdit()
    {
        $this->editEligibility = false;
        $this->addEligibility = false;
        $this->newEligibilities = [];
        $this->eligibilities = [];
        $this->resetValidation();
    }

    public function addNewEligibility(){
        $this->newEligibilities[] = [
            'eligibility' => '', 
            'rating' => '',
            'date' => '',
            'place_of_exam' => '',
            'license' => '',
            'date_of_validity' => '',
        ];
    }

    public function removeNewEligibility($index){
        unset($this->newEligibilities[$index]);
        $this->newEligibilities = array_values($this->newEligibilities);
    }

    public function saveEligibility(){
        $this->validate([
            'eligibilities.*.eligibility' => 'required|string|max:255',
            'eligibilities.*.rating' => 'required|numeric',
            'eligibilities.*.date' => 'required|date',
            'eligibilities.*.place_of_exam' => 'required|string',
        ]);

        try {
            $user = Auth::user();
            if ($user) {
                if($this->addEligibility != true){

                    foreach ($this->eligibilities as $elig) {
                        $eligRecord = $user->eligibility->find($elig['id']);
                        if ($eligRecord) {
                            $eligRecord->update([
                                'eligibility' => $elig['eligibility'],
                                'rating' => $elig['rating'],
                                'date' => $elig['date'],
                                'place_of_exam' => $elig['place_of_exam'],
                                'license' => $elig['license'],
                                'date_of_validity' => $elig['date_of_validity'],
                            ]);
                        }
                    }
                    $this->editEligibility = null;
                    $this->addEligibility = null;
                    $this->dispatch('swal', [
                        'title' => "Eligibilities updated successfully!",
                        'icon' => 'success'
                    ]);
                }else{
                    $this->validate([
                        'newEligibilities.*.eligibility' => 'required|string|max:255',
                        'newEligibilities.*.rating' => 'required|numeric',
                        'newEligibilities.*.date' => 'required|date',
                        'newEligibilities.*.place_of_exam' => 'required|string',
                    ]);
                    foreach ($this->newEligibilities as $elig) {
                        Eligibility::create([
                            'user_id' => $user->id,
                            'eligibility' => $elig['eligibility'],
                            'rating' => $elig['rating'],
                            'date' => $elig['date'],
                            'place_of_exam' => $elig['place_of_exam'],
                            'license' => $elig['license'],
                            'date_of_validity' => $elig['date_of_validity'],
                        ]);
                    }
                    $this->editEligibility = null;
                    $this->addEligibility = null;
                    $this->newEligibilities = [];
                    $this->dispatch('swal', [
                        'title' => "Eligibilities added successfully!",
                        'icon' => 'success'
                    ]);
                }
            }
        } catch (Exception $e) {
            $this->resetValidation();
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
                $educ = $user->eligibility->find($this->deleteId);

                if ($educ) {
                    $educ->delete();
                }

                $this->dispatch('swal', [
                    'title' => "Eligibility deleted successfully!",
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
