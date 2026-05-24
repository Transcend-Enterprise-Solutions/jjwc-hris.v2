<?php

namespace App\Livewire\Admin\Employee;

use App\Models\EmergencyContact;
use App\Models\User;
use Exception;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;

class ContactDetails extends Component
{
    public $selectedUser;
    public $emergencyContacts;
    public $editContact;
    public $addContact;
    public $contactId;
    public $deleteId;

    public $name;
    public $relationship;
    public $telNumber;
    public $mobileNumber;

    public function mount($userId)
    {
        $user = User::with(['position', 'officeDivision', 'officeDivisionUnit', 'userData', 'contracts'])
            ->find($userId);

        if ($user) {
            $this->selectedUser = $user;
        }
    }

    public function render()
    {
        if($this->selectedUser){
            $this->emergencyContacts = EmergencyContact::where('user_id', $this->selectedUser->id)->get();
        }

        return view('livewire.admin.employee.contact-details');
    }

    public function addNewContact(){
        $this->addContact = true;
        $this->editContact = true;
    }

    public function editNewContact($id){
        $contact = EmergencyContact::find($id);
        if($contact){
            $this->contactId = $contact->id;
            $this->name = $contact->name;
            $this->relationship = $contact->relationship;
            $this->telNumber = $contact->tel_number;
            $this->mobileNumber = $contact->mobile_number;
        }
        $this->addContact = false;
        $this->editContact = true;
    }

    public function saveContact()  {
        try{
            $this->validate([
                'name' => 'required',
                'relationship' => 'required',
                'mobileNumber' => 'required',
            ]);

            $contact = EmergencyContact::find($this->contactId);
            if($contact){
                $contact->update([
                    'name' => $this->name,
                    'relationship' => $this->relationship,
                    'tel_number' => $this->telNumber,
                    'mobile_number' => $this->mobileNumber,
                ]);
            }else{
                EmergencyContact::create([
                    'user_id' => $this->selectedUser->id,
                    'name' => $this->name,
                    'relationship' => $this->relationship,
                    'tel_number' => $this->telNumber,
                    'mobile_number' => $this->mobileNumber,
                ]);
            }

            $this->dispatch('swal', [
                'title' => 'Emergency contact saved successfully',
                'icon' => 'success'
            ]);
            $this->resetVariables();
        }catch(Exception $e){
            $this->dispatch('swal', [
                'title' => 'Something went wrong. Emergency contact was not saved.',
                'icon' => 'error'
            ]);
            throw $e;
        }
    }

    public function toggleDelete($id){
        $this->deleteId = $id;
    }

    public function deleteData(){
        try {
            $contact = EmergencyContact::where('id', $this->deleteId)->first();

            if (!$contact) {
                $this->dispatch('swal', [
                    'title' => "Contract not found!",
                    'icon' => 'error'
                ]);
                return;
            }

            $contact->delete();
            $message = "Contact deleted successfully!";

            $this->resetVariables();
            $this->dispatch('swal', [
                'title' => $message,
                'icon' => 'success'
            ]);
        } catch (Exception $e) {
            $this->dispatch('swal', [
                'title' => "Deletion of contact was unsuccessful!",
                'icon' => 'error'
            ]);
            $this->resetVariables();
            throw $e;
        }
    }

    public function resetVariables(){
        $this->addContact = null;
        $this->editContact = null;
        $this->name = null;
        $this->relationship = null;
        $this->telNumber = null;
        $this->mobileNumber = null;
        $this->contactId = null;
        $this->deleteId = null;
        $this->resetValidation();
    }
}
