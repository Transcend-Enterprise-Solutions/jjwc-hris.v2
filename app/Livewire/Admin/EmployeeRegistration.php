<?php

namespace App\Livewire\Admin;

use App\Mail\RegistrationNotification;
use App\Models\RegistrationOtp;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('Registrations')]
class EmployeeRegistration extends Component
{
    use WithPagination;

    public $search;
    public $genOtp;
    public $email;
    public $deleteId;
    public $pageSize = 10; 
    public $pageSizes = [10, 20, 30, 50, 100]; 
    public $registerEmployee = false;

    public function render()
    {
        $registrations = RegistrationOtp::when($this->search, function ($query) {
                            return $query->search(trim($this->search));
                        })
                        ->paginate($this->pageSize);

        foreach($registrations as $reg){
            $reg->admin = User::where('id', $reg->provided_by)->first()->name;
            $user = User::where('id', $reg->user_id)->first();
            $userName = $user ? $user->name : '';
            $reg->user = $reg->user_id ? $userName : '';
        }

        return view('livewire.admin.employee-registration' , [
            'registrations' => $registrations,
        ]);
    }

    public function toggleAddRegOtp(){
        $this->genOtp = true;
    }

    public function toggleRegEmployee(){
        $this->registerEmployee = !$this->registerEmployee;
    }

    public function toggleDelete($id){
        $this->deleteId = $id;
    }

    public function deleteData(){
        try {
            $user = RegistrationOtp::where('id', $this->deleteId)->first();
            if ($user) {
                $user->delete();
                $this->resetVariables();
                $this->dispatch('swal', [
                    'title' => 'OTP deleted successfully',
                    'icon' => 'success'
                ]);            
            }
        } catch (Exception $e) {
            $this->dispatch('swal', [
                'title' => "OTP deletion was unsuccessful!",
                'icon' => 'error'
            ]);
            $this->resetVariables();
            throw $e;
        }
    }

    public function submitRegOtp(){
        $admin = Auth::user();
        $otp = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);

        try{
            $this->validate([
                'email' => 'required:email',
            ]);

            $mailed = Mail::to($this->email)->send(new RegistrationNotification($admin->email, $otp));

            if($mailed){
                RegistrationOtp::create([
                    'otp' => $otp,
                    'email' => $this->email,
                    'status' => 0,
                    'provided_by' => $admin->id,
                    'date_provided' => now(),
                ]);

                $this->dispatch('swal', [
                    'title' => 'Registration OTP mailed successfully',
                    'icon' => 'success'
                ]);
                $this->resetVariables();
            }else{
                $this->dispatch('swal', [
                    'title' => 'Unexpected error. Please try again later',
                    'icon' => 'error'
                ]);
                $this->resetVariables();
            }
        }catch(Exception $e){
            throw $e;
        }
    }

    public function resetVariables(){
        $this->email = null;
        $this->genOtp = null;
        $this->deleteId= null;
    }
}
