<?php

namespace App\Livewire\Dashboard;

use App\Models\UserData;
use Carbon\Carbon;
use Livewire\Component;

class DashboardMonthBirthdays extends Component
{
    public function render()
    {
        $birthdayEmployees = UserData::whereMonth('user_data.date_of_birth', now()->month)
                ->join('users', 'users.id', 'user_data.user_id')
                ->select('user_data.*', 'users.profile_photo_path', 'users.name')
                ->orderByRaw('DAY(date_of_birth) ASC') 
                ->get();

        if($birthdayEmployees){
            foreach($birthdayEmployees as $emp){
                $emp->age = Carbon::parse($emp->date_of_birth)->age;
            }
        }

        return view('livewire.dashboard.dashboard-month-birthdays', [
            'birthdayEmployees' => $birthdayEmployees,
        ]);
    }
}
