<?php

namespace App\Livewire\Admin\Reports;

use Livewire\Component;
use App\Models\LeaveApplication;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LeaveApplicationsExport;
use App\Exports\LeaveAvailmentExport;
use App\Exports\VacationLeaveExport;
use App\Exports\SickLeaveExport;
use Carbon\Carbon;

class LeaveAvailment extends Component
{
    public $statuses = ['Pending', 'Approved', 'Approved by HR', 'Approved by Supervisor', 'Disapproved'];
    public $statusesForVL = ['Pending', 'Approved', 'Approved by HR', 'Approved by Supervisor', 'Disapproved'];
    public $statusesForSL = ['Pending', 'Approved', 'Approved by HR', 'Approved by Supervisor', 'Disapproved'];

    public $month;

    public function leaveAvailmentExport()
    {
        if (!$this->month) {
            $this->addError('month', 'Please select a month before exporting.');
            return;
        }
    
        return Excel::download(new LeaveAvailmentExport($this->month), 'LeaveAvailment-' . $this->month . '.xlsx');
    }

    public function getFormattedMonth()
    {
        if ($this->month) {
            return Carbon::createFromFormat('Y-m', $this->month)->translatedFormat('F Y');
        }

        return null;
    }

    public function exportToExcel()
    {
        return Excel::download(new LeaveApplicationsExport($this->statuses), 'TotalLeaveApplications.xlsx');
    }
    
    public function exportVacationLeaveToExcel()
    {
        return Excel::download(new VacationLeaveExport($this->statusesForVL), 'VacationLeaveApplications.xlsx');
    }

    public function exportSickLeaveToExcel()
    {
        return Excel::download(new SickLeaveExport($this->statusesForSL), 'SickLeaveApplications.xlsx');
    }

    public function mount()
    {
        $this->month = now()->format('Y-m');
    }

    public function render()
    {
        $leaveCount = LeaveApplication::when($this->statuses, function ($query) {
            $query->whereIn('status', $this->statuses);
        })->count();

        $vacationLeaveCount = LeaveApplication::where('type_of_leave', 'LIKE', '%Vacation Leave%')
            ->when($this->statusesForVL, function ($query) {
                $query->whereIn('status', $this->statusesForVL);
            })->count();

        $sickLeaveCount = LeaveApplication::where('type_of_leave', 'LIKE', '%Sick Leave%')
            ->when($this->statusesForSL, function ($query) {
                $query->whereIn('status', $this->statusesForSL);
            })->count();

        // $totalLeaveCount = LeaveApplication::count();
        $totalLeaveCount = $this->month 
            ? $this->getLeaveCountForMonth($this->month)
            : LeaveApplication::count();

        return view('livewire.admin.reports.leave-availment', [
            'leaveCount' => $leaveCount,
            'totalLeaveCount' => $totalLeaveCount,
            'vacationLeaveCount' => $vacationLeaveCount,
            'sickLeaveCount' => $sickLeaveCount,
        ]);
    }

    private function getLeaveCountForMonth($month)
    {
        list($year, $month) = explode('-', $month);
        return LeaveApplication::whereYear('date_of_filing', $year)
            ->whereMonth('date_of_filing', $month)
            ->count();
    }
}
