<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\User;
use DB;
use Illuminate\Support\Facades\DB as FacadesDB;
class DashboardTotalEmployee extends Component
{
    public $totalEmployees;
    public $months;
    public $monthlyCreations;

    public function mount()
    {
        $currentYear = date('Y');

        $this->totalEmployees = User::where('user_role', '=','emp')->count();

        $createdByMonth = User::whereYear('created_at', $currentYear)
            ->where('user_role', '=','emp')
            ->selectRaw('COUNT(*) as count, MONTH(created_at) as month')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $this->months = array_map(function ($month) {
            return date('M', mktime(0, 0, 0, $month, 1));
        }, range(1, 12));

        $monthlyData = $createdByMonth->pluck('count', 'month')->toArray();
        $this->monthlyCreations = array_map(function ($month) use ($monthlyData) {
            return $monthlyData[$month] ?? 0;
        }, range(1, 12));
    }

    public function render()
    {
        return view('livewire.dashboard.dashboard-total-employee');
    }
}
