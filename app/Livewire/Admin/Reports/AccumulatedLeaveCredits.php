<?php

namespace App\Livewire\Admin\Reports;

use Livewire\Component;
use App\Models\LeaveCredits;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LeaveCreditsExport;
use App\Exports\VacationLeaveCreditsExport;

class AccumulatedLeaveCredits extends Component
{
    public $selectedLeaveTypes = ['Vacation Leave', 'Sick Leave', 'Special Privilege Leave'];

    public function exportTotalCredits()
    {
        return Excel::download(new LeaveCreditsExport($this->selectedLeaveTypes), 'LeaveCredits.xlsx');
    }

    public function render()
    {
        $totalLeaveCreditsCount = LeaveCredits::count();
        return view('livewire.admin.reports.accumulated-leave-credits',[
            'totalLeaveCreditsCount' => $totalLeaveCreditsCount,
        ]);
    }
}
