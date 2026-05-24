<?php

namespace App\Livewire\User\PersonalData;

use App\Models\AssOrgMemberships;
use App\Models\Hobbies;
use App\Models\NonAcadDistinctions;
use App\Models\Skills;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class BackgroundAndInterests extends Component
{
    public $editingSkills = false; 
    public $addingSkills = false; 
    public $editingHobbies = false; 
    public $addingHobbies = false; 
    public $editingDistinctions = false; 
    public $addingDistinctions = false; 
    public $editingMemberships = false; 
    public $addingMemberships = false;  

    public $skills = []; 
    public $hobbies = []; 
    public $distinctions = []; 
    public $memberships = []; 

    public $deleteId;
    public $toDelete;

    public function render()
    {
        $user = User::with(['skills', 'hobbies', 'nonAcadDistinctions', 'assOrgMembership'])->find(Auth::user()->id);

        return view('livewire.user.personal-data.background-and-interests', [
            'mySkills' => $user->skills,
            'myHobbies' => $user->hobbies,
            'myDistinctions' => $user->nonAcadDistinctions,
            'myMemberships' => $user->assOrgMembership,
        ]);
    }

    /*
    * Edit Data
    **/
    public function editInfo($type, $edit = true)  {
        switch($type){
            case 'skills':
                $this->editingSkills = true;
                if($edit){
                    $this->toggleEditSkills();
                    return;
                }

                $this->addingSkills = true;
                $this->skills[] = [
                    'skill' => '',
                ];
                break;
            case 'hobbies':
                $this->editingHobbies = true;
                if($edit){
                    $this->toggleEditHobbies();
                    return;
                }

                $this->addingHobbies = true;
                $this->hobbies[] = [
                    'hobby' => '',
                ];
                break;
            case 'distinctions':
                $this->editingDistinctions = true;
                if($edit){
                    $this->toggleEditNonAcads();
                    return;
                }

                $this->addingDistinctions = true;
                $this->distinctions[] = [
                    'award' => '',
                    'ass_org_name	' => '',
                    'date_received' => '',
                ];
                break;
            case 'memberships':
                $this->editingMemberships = true;
                 if($edit){
                    $this->toggleEditMemberships();
                    return;
                }

                $this->addingMemberships = true;
                $this->memberships[] = [
                    'ass_org_name	' => '',
                    'position' => '',
                ];
                break;
        }
    }

    public function cancelEdit($type)  {
        switch($type){
            case 'skills':
                $this->editingSkills = false;
                $this->skills = [];
                break;
            case 'hobbies':
                $this->editingHobbies = false;
                $this->hobbies = [];
                break;
            case 'distinctions':
                $this->editingDistinctions = false;
                $this->distinctions = [];
                break;
            case 'memberships':
                $this->editingMemberships = false;
                $this->memberships = [];
                break;
        }
    }

    public function toggleEditSkills(){
        try{
            $user = User::with(['skills'])->find(Auth::user()->id);
            $this->skills = $user->skills->toArray();
        }catch(Exception $e){
            throw $e;
        }
    }

    public function toggleEditHobbies(){
        try{
            $user = User::with(['hobbies'])->find(Auth::user()->id);
            $this->hobbies = $user->hobbies->toArray();
        }catch(Exception $e){
            throw $e;
        }
    }

    public function toggleEditNonAcads(){
        try{
            $user = User::with(['nonAcadDistinctions'])->find(Auth::user()->id);
            $this->distinctions = $user->nonAcadDistinctions->toArray();
        }catch(Exception $e){
            throw $e;
        }
    }

    public function toggleEditMemberships(){
        try{
            $user = User::with(['assOrgMembership'])->find(Auth::user()->id);
            $this->memberships = $user->assOrgMembership->toArray();
        }catch(Exception $e){
            throw $e;
        }
    }

    /**
     * Helpers
     */
    public function addSkill(){
        $this->skills[] = [
            'skill' => '',
        ];
    }

    public function removeSkill($index){
        unset($this->skills[$index]);
        $this->skills = array_values($this->skills);
    }

    public function addHobby(){
        $this->hobbies[] = [
            'hobby' => '',
        ];
    }

    public function removeHobby($index){
        unset($this->hobbies[$index]);
        $this->hobbies = array_values($this->hobbies);
    }

    public function addDistinction(){
        $this->distinctions[] = [
            'award' => '',
            'ass_org_name	' => '',
            'date_received' => '',
        ];
    }

    public function removeDistinction($index){
        unset($this->distinctions[$index]);
        $this->distinctions = array_values($this->distinctions);
    }

    public function addMembership(){
        $this->memberships[] = [
            'ass_org_name	' => '',
            'position' => '',
        ];
    }

    public function removeMembership($index){
        unset($this->memberships[$index]);
        $this->memberships = array_values($this->memberships);
    }


    /**
     * Save Data
     */
    public function saveInfo($type)  {
        switch($type){
            case 'skills':
                $this->saveSkills();
                break;
            case 'hobbies':
                $this->saveHobbies();
                break;
            case 'distinctions':
                $this->saveNonAcad();
                break;
            case 'memberships':
                $this->saveMemberships();
                break;
        }
    }

    public function saveSkills(){
        try {
            $user = Auth::user();
            if ($user) {
                if(!$this->addingSkills){
                    $this->validate([
                        'skills.*.skill' => 'required|string|max:255',
                    ]);
    
                    foreach ($this->skills as $skill) {
                        $skillRecord = $user->skills->find($skill['id']);
                        if ($skillRecord) {
                            $skillRecord->update([
                                'skill' => $skill['skill'],
                            ]);
                        }
                    }
    
                    $this->resetVariables();
                    $this->dispatch('swal', [
                        'title' => "Skills updated successfully!",
                        'icon' => 'success'
                    ]);
                }else{
                    $this->validate([
                        'skills.*.skill' => 'required|string|max:255',
                    ]);
    
                    foreach ($this->skills as $skill) {
                        Skills::create([
                            'user_id' => $user->id,
                            'skill' => $skill['skill'],
                        ]);
                    }
    
                    $this->resetVariables();
                    $this->dispatch('swal', [
                        'title' => "Skills added successfully!",
                        'icon' => 'success'
                    ]);
                }
            }
        } catch (Exception $e) {
            $this->resetValidation();
            $this->dispatch('swal', [
                'title' => "Skills update was unsuccessful!",
                'icon' => 'error'
            ]);
            throw $e;
        }
    }

    public function saveHobbies(){
        try {
            $user = Auth::user();
            if ($user) {

                if(!$this->addingHobbies){
                    $this->validate([
                        'hobbies.*.hobby' => 'required|string|max:255',
                    ]);
    
                    foreach ($this->hobbies as $hobby) {
                        $hobbyRecord = $user->hobbies->find($hobby['id']);
                        if ($hobbyRecord) {
                            $hobbyRecord->update([
                                'hobby' => $hobby['hobby'],
                            ]);
                        }
                    }
    
                    $this->resetVariables();
                    $this->dispatch('swal', [
                        'title' => "Hobbies updated successfully!",
                        'icon' => 'success'
                    ]);
                }else{
                    $this->validate([
                        'hobbies.*.hobby' => 'required|string|max:255',
                    ]);
    
                    foreach ($this->hobbies as $hobby) {
                        Hobbies::create([
                            'user_id' => $user->id,
                            'hobby' => $hobby['hobby'],
                        ]);
                    }
    
                    $this->resetVariables();
                    $this->dispatch('swal', [
                        'title' => "Hobbies added successfully!",
                        'icon' => 'success'
                    ]);
                }
            }
        } catch (Exception $e) {
            $this->resetValidation();
            $this->dispatch('swal', [
                'title' => "Hobbies update was unsuccessful!",
                'icon' => 'error'
            ]);
            throw $e;
        }
    }

    public function saveNonAcad(){
        try {
            $user = Auth::user();
            if ($user) {

                if(!$this->addingDistinctions){
                    $this->validate([
                        'distinctions.*.award' => 'required|string|max:255',
                        'distinctions.*.ass_org_name' => 'required|string|max:255',
                        'distinctions.*.date_received' => 'required|date',
                    ]);
    
                    foreach ($this->distinctions as $nonAcad) {
                        $nonAcadRecord = $user->nonAcadDistinctions->find($nonAcad['id']);
                        if ($nonAcadRecord) {
                            $nonAcadRecord->update([
                                'award' => $nonAcad['award'],
                                'ass_org_name' => $nonAcad['ass_org_name'],
                                'date_received' => $nonAcad['date_received'],
                            ]);
                        }
                    }
    
                    $this->resetVariables();
                    $this->dispatch('swal', [
                        'title' => "Non-Academic Distinction/Recognition updated successfully!",
                        'icon' => 'success'
                    ]);
                }else{
                    $this->validate([
                        'distinctions.*.award' => 'required|string|max:255',
                        'distinctions.*.ass_org_name' => 'required|string|max:255',
                        'distinctions.*.date_received' => 'required|date',
                    ]);
    
                    foreach ($this->distinctions as $nonAcad) {
                        NonAcadDistinctions::create([
                            'user_id' => $user->id,
                            'award' => $nonAcad['award'],
                            'ass_org_name' => $nonAcad['ass_org_name'],
                            'date_received' => $nonAcad['date_received'],
                        ]);
                    }
    
                    $this->resetVariables();
                    $this->dispatch('swal', [
                        'title' => "Non-Academic Distinction/Recognition added successfully!",
                        'icon' => 'success'
                    ]);
                }
            }
        } catch (Exception $e) {
            $this->resetValidation();
            $this->dispatch('swal', [
                'title' => "Non-Academic Distinction/Recognition update was unsuccessful!",
                'icon' => 'error'
            ]);
            throw $e;
        }
    }

    public function saveMemberships(){
        try {
            $user = Auth::user();
            if ($user) {
                if(!$this->addingMemberships){
                    $this->validate([
                        'memberships.*.position' => 'required|string|max:255',
                        'memberships.*.ass_org_name' => 'required|string|max:255',
                    ]);
    
                    foreach ($this->memberships as $member) {
                        $memberRecord = $user->assOrgMembership->find($member['id']);
                        if ($memberRecord) {
                            $memberRecord->update([
                                'ass_org_name' => $member['ass_org_name'],
                                'position' => $member['position'],
                            ]);
                        }
                    }
    
                    $this->resetVariables();
                    $this->dispatch('swal', [
                        'title' => "Membership in Association/Organization updated successfully!",
                        'icon' => 'success'
                    ]);
                }else{
                    $this->validate([
                        'memberships.*.position' => 'required|string|max:255',
                        'memberships.*.ass_org_name' => 'required|string|max:255',
                    ]);
    
                    foreach ($this->memberships as $member) {
                        AssOrgMemberships::create([
                            'user_id' => $user->id,
                            'ass_org_name' => $member['ass_org_name'],
                            'position' => $member['position'],
                        ]);
                    }
    
                    $this->resetVariables();
                    $this->dispatch('swal', [
                        'title' => "Membership in Association/Organization added successfully!",
                        'icon' => 'success'
                    ]);
                }
            }
        } catch (Exception $e) {
            $this->resetValidation();
            $this->dispatch('swal', [
                'title' => "Membership in Association/Organization update was unsuccessful!",
                'icon' => 'error'
            ]);
            throw $e;
        }
    }

    public function resetVariables(){
        $this->editingSkills = false; 
        $this->editingHobbies = false; 
        $this->editingDistinctions = false; 
        $this->editingMemberships = false; 

        $this->addingSkills = false; 
        $this->addingHobbies = false; 
        $this->addingDistinctions = false; 
        $this->addingMemberships = false; 

        $this->skills = []; 
        $this->hobbies = []; 
        $this->distinctions = []; 
        $this->memberships = []; 

        $this->resetValidation();
    }

    /**
     * Delete Data
     */
    public function toggleDelete($id, $type){
        $this->deleteId = $id;
        $this->toDelete = $type;
    }

    public function deleteData(){
        try{
            $user = Auth::user();
            if($user){
                $data = null;

                switch($this->toDelete){
                    case 'skill':
                        $data = $user->skills->find($this->deleteId);
                        break;
                    case 'hobby':
                        $data = $user->hobbies->find($this->deleteId);
                        break;
                    case 'distinction':
                        $data = $user->nonAcadDistinctions->find($this->deleteId);
                        break;
                    case 'membership':
                        $data = $user->assOrgMembership->find($this->deleteId);
                        break;
                }


                if ($data) {
                    $data->delete();
                }

                $this->dispatch('swal', [
                    'title' => strtoupper($this->toDelete) . " deleted successfully!",
                    'icon' => 'success'
                ]);

                $this->deleteId = null;
                $this->toDelete = null;
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
