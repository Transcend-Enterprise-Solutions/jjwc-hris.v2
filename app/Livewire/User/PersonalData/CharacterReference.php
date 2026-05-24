<?php

namespace App\Livewire\User\PersonalData;

use App\Models\CharReferences;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CharacterReference extends Component
{
    public $addReferences = false;
    public $editReferences = false;
    public $myReferences = [];
    public $myNewReferences = [];
    public $deleteId;

    public function render()
    {
        return view('livewire.user.personal-data.character-reference', [
            'references' => $this->getReferences(),
        ]);
    }

    protected function getReferences(){
        $user = User::with(['charReferences'])->find(Auth::user()->id);
        return $user->charReferences;
    }


    public function toggleEditReferences(){
        $this->editReferences = true;
        try{
            $user = User::with(['charReferences'])->find(Auth::user()->id);
            $this->myReferences = $user->charReferences->toArray();
        }catch(Exception $e){
            throw $e;
        }
    }

    public function toggleAddReferences(){
        $this->editReferences = true;
        $this->addReferences = true;
        $this->myNewReferences[] = [
            'firstname	' => '',
            'middle_name' => '',
            'surname' => '',
            'address' => '',
            'tel_number' => '',
            'mobile_number' => '',
        ];
    }

    public function addNewReference(){
        $this->myNewReferences[] = [
            'firstname	' => '',
            'middle_name' => '',
            'surname' => '',
            'address' => '',
            'tel_number' => '',
            'mobile_number' => '',
        ];
    }

    public function removeNewReference($index){
        unset($this->myNewReferences[$index]);
        $this->myNewReferences = array_values($this->myNewReferences);
    }

    public function saveReferences(){
        try {
            $user = Auth::user();
            $charRefs = CharReferences::where("user_id", $user->id)->get();
            if ($user) {

                if($this->addReferences != true){
                    $this->validate([
                        'myReferences.*.firstname' => 'required|string|max:255',
                        'myReferences.*.middle_initial' => 'required|string|max:255',
                        'myReferences.*.surname' => 'required|string|max:255',
                        'myReferences.*.address' => 'required|string|max:255',
                        'myReferences.*.mobile_number' => 'required|numeric',
                    ]);
    
                    foreach ($this->myReferences as $refs) {
                        $refsRecord = $user->charReferences->find($refs['id']);
                        if ($refsRecord) {
                            $refsRecord->update([
                                'firstname' => $refs['firstname'],
                                'middle_initial' => $refs['middle_initial'],
                                'surname' => $refs['surname'],
                                'address' => $refs['address'],
                                'tel_number' => $refs['tel_number'],
                                'mobile_number' => $refs['mobile_number'],
                            ]);
                        }
                    }
                    
                    $this->resetVariables();
                    $this->dispatch('swal', [
                        'title' => "Character References updated successfully!",
                        'icon' => 'success'
                    ]);
                }else{
                    if(count($charRefs) >= 3){
                        $this->editReferences = null;
                        $this->addReferences = null;
                        $this->myReferences = [];
                        $this->myNewReferences = [];
                        $this->dispatch('swal', [
                            'title' => "Character references are only up to 3 persons.",
                            'icon' => 'error'
                        ]);
                        return;
                    }

                    $this->validate([
                        'myNewReferences.*.firstname' => 'required|string|max:255',
                        'myNewReferences.*.middle_initial' => 'required|string|max:255',
                        'myNewReferences.*.surname' => 'required|string|max:255',
                        'myNewReferences.*.address' => 'required|string|max:255',
                        'myNewReferences.*.mobile_number' => 'required|numeric',
                    ]);
    
                    foreach ($this->myNewReferences as $refs) {
                        CharReferences::create([
                            'user_id' => $user->id,
                            'firstname' => $refs['firstname'],
                            'middle_initial' => $refs['middle_initial'],
                            'surname' => $refs['surname'],
                            'address' => $refs['address'],
                            'tel_number' => $refs['tel_number'],
                            'mobile_number' => $refs['mobile_number'],
                        ]);
                    }
                    
                    $this->resetVariables();
                    $this->dispatch('swal', [
                        'title' => "Character References added successfully!",
                        'icon' => 'success'
                    ]);
                }
            }
        } catch (Exception $e) {
            $this->resetValidation();
            throw $e;
        }
    }

    public function cancelEdit()
    {
        $this->resetVariables();
    }

    public function resetVariables(){
        $this->editReferences = false;
        $this->addReferences  = false;
        $this->myNewReferences = [];
        $this->myReferences = [];
        $this->resetValidation();
    }


    public function toggleDelete($id){
        $this->deleteId = $id;
    }

    public function deleteData(){
        try{
            $user = Auth::user();
            if($user){
                $charRef = $user->charReferences->find($this->deleteId);

                if ($charRef) {
                    $charRef->delete();
                }

                $this->dispatch('swal', [
                    'title' => "Character reference deleted successfully!",
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
