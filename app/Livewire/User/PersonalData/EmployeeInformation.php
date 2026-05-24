<?php

namespace App\Livewire\User\PersonalData;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class EmployeeInformation extends Component
{
    public function render()
    {
        $user = User::with(['position', 'officeDivision', 'officeDivisionUnit', 'userData', 'contracts'])
            ->find(Auth::user()->id);
        return view('livewire.user.personal-data.employee-information', [
            'user' => $user
        ]);
    }
}
