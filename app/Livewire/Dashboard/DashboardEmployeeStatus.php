<?php

namespace App\Livewire\Dashboard;

use App\Models\User;
use Livewire\Component;

class DashboardEmployeeStatus extends Component
{
    public $statusCounts = [];

    public function mount()
    {
        // Fetch the count of each status
        $this->statusCounts = User::selectRaw('active_status, COUNT(*) as count')
            ->where('user_role', 'emp')
            ->groupBy('active_status')
            ->pluck('count', 'active_status')
            ->toArray();
    }

    public function render()
    {
        return view('livewire.dashboard.dashboard-employee-status', [
            'statusCounts' => $this->statusCounts,
        ]);
    }
}
