<?php

namespace App\Livewire\User\PersonalData;

use App\Models\PdsGovIssuedId;
use Exception;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class GovernmentId extends Component
{
    public $govId;
    public $idNumber;
    public $dateIssued;
    public $editGovId = false;
    public $delete = false;

    public function render()
    {
        $pdsGovId = PdsGovIssuedId::where('user_id', Auth::user()->id)->first();
        if($pdsGovId){
            $this->govId = $pdsGovId->gov_id;
            $this->idNumber = $pdsGovId->id_number;
            $this->dateIssued = $pdsGovId->date_of_issuance;
        }

        return view('livewire.user.personal-data.government-id');
    }

    public function toggleEditGovId(){
        if($this->editGovId){
            $this->editGovId = null;
        }else{
            $this->editGovId = true;
        }
    }

    public function saveGovId(){
        try{
            $user = Auth::user();
            if($user){
                $message = "";
                $govId = PdsGovIssuedId::where('user_id', $user->id)->first();
                $this->validate([
                    'govId' => 'required',
                    'idNumber' => 'required',
                    'dateIssued' => 'required',
                ]);
                if($govId){
                    $govId->update([
                        'gov_id' => $this->govId,
                        'id_number' => $this->idNumber,
                        'date_of_issuance' => $this->dateIssued,
                    ]);
                    $message = "Government Issued ID updated successfully!";
                }else{
                    PdsGovIssuedId::create([
                        'user_id' => $user->id,
                        'gov_id' => $this->govId,
                        'id_number' => $this->idNumber,
                        'date_of_issuance' => $this->dateIssued,
                    ]);
                    $message = "Government Issued ID added successfully!";
                }
                $this->dispatch('swal', [
                    'title' => $message,
                    'icon' => 'success'
                ]);
            }
            $this->resetVariables();
        }catch(Exception $e){
            throw $e;
        }
    }

    public function cancelEdit()
    {
        $this->resetVariables();
    }

    public function resetVariables(){
        $this->editGovId = false;
        $this->resetValidation();
    }

    public function toggleDelete(){
        $this->delete = true;
    }

    public function deleteData(){
        try{
            $user = Auth::user();
            if($user){
                $id = PdsGovIssuedId::where('user_id', $user->id)->first();

                if ($id) {
                    $id->delete();
                }

                $this->dispatch('swal', [
                    'title' => "Government issued id deleted successfully!",
                    'icon' => 'success'
                ]);

                $this->delete = false;
                $this->govId = null;
                $this->idNumber = null;
                $this->dateIssued = null;
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
