<?php

namespace App\Livewire\User\PersonalData;

use App\Models\EmployeesSpouse;
use App\Models\EmployeesFather;
use App\Models\EmployeesMother;
use App\Models\EmployeesChildren;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class FamilyBackground extends Component
{
    public $editingSpouse = false;
    public $editingFather = false;
    public $editingMother = false;
    public $editingChildren = false;

    // Spouse
    public $spouse_surname;
    public $spouse_first_name;
    public $spouse_middle_name;
    public $spouse_name_extension;
    public $spouse_birth_date;
    public $spouse_occupation;
    public $spouse_employer;
    public $spouse_tel_number;
    public $spouse_business_address;

    // Father
    public $father_surname;
    public $father_first_name;
    public $father_middle_name;
    public $father_name_extension;

    // Mother
    public $mother_surname;
    public $mother_first_name;
    public $mother_middle_name;
    public $mother_name_extension;

    // Children
    public $children = [];

    public function render()
    {
        $user = User::with(['employeesSpouse', 'employeesMother', 'employeesFather', 'employeesChildren'])->find( Auth::user()->id);
        return view('livewire.user.personal-data.family-background',[
            'userSpouse' => $user->employeesSpouse,
            'userMother' => $user->employeesMother,
            'userFather' => $user->employeesFather,
            'userChildren' => $user->employeesChildren,
        ]);
    }

    public function toggleEditFamily($type)  {
        switch($type){
            case 'spouse':
                $this->editingSpouse = true;
                $spouse = EmployeesSpouse::where('user_id', Auth::user()->id)->first();
                if($spouse){
                    $this->spouse_first_name = $spouse->first_name;
                    $this->spouse_middle_name = $spouse->middle_name;
                    $this->spouse_surname = $spouse->surname;
                    $this->spouse_name_extension = $spouse->name_extension;
                    $this->spouse_birth_date = $spouse->birth_date;
                    $this->spouse_occupation = $spouse->occupation;
                    $this->spouse_employer = $spouse->employer;
                    $this->spouse_business_address = $spouse->business_address;
                    $this->spouse_tel_number = $spouse->tel_number;
                }
                break;
            case 'father':
                $this->editingFather = true;
                $father = EmployeesFather::where('user_id', Auth::user()->id)->first();
                if($father){
                    $this->father_first_name = $father->first_name;
                    $this->father_middle_name = $father->middle_name;
                    $this->father_surname = $father->surname;
                    $this->father_name_extension = $father->name_extension;
                }
                break;
            case 'mother':
                $this->editingMother = true;
                $mother = EmployeesMother::where('user_id', Auth::user()->id)->first();
                if($mother){
                    $this->mother_first_name = $mother->first_name;
                    $this->mother_middle_name = $mother->middle_name;
                    $this->mother_surname = $mother->surname;
                    $this->mother_name_extension = $mother->name_extension;
                }
                break;
            case 'children':
                $this->editingChildren = true;
                $children = EmployeesChildren::where('user_id', Auth::user()->id)->get();
                $this->children = [];
                foreach($children as $child){
                    $this->children[] = [
                        'id' => $child->id,
                        'childs_name' => $child->childs_name,
                        'childs_birth_date' => $child->childs_birth_date,
                    ];
                }
                if(empty($this->children)){
                    $this->children[] = [
                        'id' => null,
                        'childs_name' => '',
                        'childs_birth_date' => '',
                    ];
                }
                break;
        }
    }

    public function cancelEdit($type)  {
        switch($type){
            case 'spouse':
                $this->editingSpouse = false;
                $this->resetSpouseFields();
                break;
            case 'father':
                $this->editingFather = false;
                $this->resetFatherFields();
                break;
            case 'mother':
                $this->editingMother = false;
                $this->resetMotherFields();
                break;
            case 'children':
                $this->editingChildren = false;
                $this->children = [];
                break;
        }
    }

    public function saveFamily($type)  {
        switch($type){
            case 'spouse':
                $this->saveSpouse();
                break;
            case 'father':
                $this->saveFather();
                break;
            case 'mother':
                $this->saveMother();
                break;
            case 'children':
                $this->saveChildren();
                break;
        }
    }

    protected function saveSpouse(){
        try{
            $userId = Auth::user()->id;

            $this->validate([
                'spouse_first_name' => 'required',
                'spouse_surname' => 'required',
                'spouse_birth_date' => 'required',
            ]);

            EmployeesSpouse::updateOrCreate(['user_id' => $userId], [
                'first_name' => $this->spouse_first_name ?? null,
                'middle_name' => $this->spouse_middle_name ?? null,
                'surname' => $this->spouse_surname ?? null,
                'name_extension' => $this->spouse_name_extension ?? null,
                'birth_date' => $this->spouse_birth_date ?? null,
                'occupation' => $this->spouse_occupation ?? null,
                'employer' => $this->spouse_employer ?? null,
                'business_address' => $this->spouse_business_address ?? null,
                'tel_number' => $this->spouse_tel_number ?? null,
            ]);

            $this->editingSpouse = false;
            $this->dispatch('swal', [
                'title' => "Spouse's info updated successfully!", 
                'icon' => 'success'
            ]);
        }catch(Exception $e){
            throw $e;
        }
    }

    protected function saveFather(){
        try{
            $userId = Auth::user()->id;

            $this->validate([
                'father_first_name' => 'required',
                'father_surname' => 'required',
            ]);

            EmployeesFather::updateOrCreate(['user_id' => $userId], [
                'first_name' => $this->father_first_name ?? null,
                'middle_name' => $this->father_middle_name ?? null,
                'surname' => $this->father_surname ?? null,
                'name_extension' => $this->father_name_extension ?? null,
            ]);

            $this->editingFather = false;
            $this->dispatch('swal', [
                'title' => "Father's info updated successfully!", 
                'icon' => 'success'
            ]);
        }catch(Exception $e){
            throw $e;
        }
    }

    protected function saveMother(){
        try{
            $userId = Auth::user()->id;

            $this->validate([
                'mother_first_name' => 'required',
                'mother_surname' => 'required',
            ]);

            EmployeesMother::updateOrCreate(['user_id' => $userId], [
                'first_name' => $this->mother_first_name ?? null,
                'middle_name' => $this->mother_middle_name ?? null,
                'surname' => $this->mother_surname ?? null,
                'name_extension' => $this->mother_name_extension ?? null,
            ]);

            $this->editingMother = false;
            $this->dispatch('swal', [
                'title' => "Mother's info updated successfully!", 
                'icon' => 'success'
            ]);
        }catch(Exception $e){
            throw $e;
        }
    }

    protected function saveChildren(){
        try{
            $userId = Auth::user()->id;

            foreach($this->children as $index => $child){
                if(!empty($child['childs_name']) || !empty($child['childs_birth_date'])){
                    $this->validate([
                        "children.{$index}.childs_name" => 'required',
                        "children.{$index}.childs_birth_date" => 'required|date',
                    ]);
                }
            }

            EmployeesChildren::where('user_id', $userId)->delete();

            foreach($this->children as $child){
                if(!empty($child['childs_name']) && !empty($child['childs_birth_date'])){
                    EmployeesChildren::create([
                        'user_id' => $userId,
                        'childs_name' => $child['childs_name'],
                        'childs_birth_date' => $child['childs_birth_date'],
                    ]);
                }
            }

            $this->editingChildren = false;
            $this->dispatch('swal', [
                'title' => "Children's info updated successfully!", 
                'icon' => 'success'
            ]);
        }catch(Exception $e){
            throw $e;
        }
    }

    public function addChild(){
        $this->children[] = [
            'id' => null,
            'childs_name' => '',
            'childs_birth_date' => '',
        ];
    }

    public function removeChild($index){
        unset($this->children[$index]);
        $this->children = array_values($this->children);
    }

    protected function resetSpouseFields(){
        $this->spouse_first_name = null;
        $this->spouse_middle_name = null;
        $this->spouse_surname = null;
        $this->spouse_name_extension = null;
        $this->spouse_birth_date = null;
        $this->spouse_occupation = null;
        $this->spouse_employer = null;
        $this->spouse_business_address = null;
        $this->spouse_tel_number = null;
    }

    protected function resetFatherFields(){
        $this->father_first_name = null;
        $this->father_middle_name = null;
        $this->father_surname = null;
        $this->father_name_extension = null;
    }

    protected function resetMotherFields(){
        $this->mother_first_name = null;
        $this->mother_middle_name = null;
        $this->mother_surname = null;
        $this->mother_name_extension = null;
    }
}