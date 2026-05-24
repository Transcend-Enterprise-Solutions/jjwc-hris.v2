<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\EmployeesDtr;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class DashboardDtr extends Component
{
    public $attendanceData;
    public $overtimeData;
    public $lateData;
    public $totalPresent;
    public $totalAbsent;
    public $totalLate;
    public $avgOvertime;

    public function mount()
    {
        $this->loadData();
    }

    private function loadData()
    {
        $endDate = Carbon::today();
        $startDate = $endDate->copy()->subDays(29);
        $dateRange = CarbonPeriod::create($startDate, $endDate);

        $attendanceData = EmployeesDtr::select(
            DB::raw('DATE(date) as date'),
            DB::raw('COUNT(*) as count'),
            DB::raw("SUM(CASE WHEN remarks = 'Present' THEN 1 ELSE 0 END) as present_count"),
            DB::raw("SUM(CASE WHEN remarks = 'Absent' THEN 1 ELSE 0 END) as absent_count"),
            DB::raw("SUM(CASE WHEN remarks = 'Late/Undertime' THEN 1 ELSE 0 END) as late_count")
        )
            ->whereBetween('date', [$startDate, $endDate])
            ->groupBy('date')
            ->get()
            ->keyBy('date');

        $overtimeData = EmployeesDtr::select(
            DB::raw('DATE(date) as date'),
            DB::raw('SEC_TO_TIME(SUM(TIME_TO_SEC(overtime))) as total_overtime')
        )
            ->whereBetween('date', [$startDate, $endDate])
            ->groupBy('date')
            ->get()
            ->keyBy('date');

        $lateData = EmployeesDtr::select(
            DB::raw('DATE(date) as date'),
            DB::raw('SEC_TO_TIME(SUM(TIME_TO_SEC(late))) as total_late')
        )
            ->whereBetween('date', [$startDate, $endDate])
            ->where('remarks', 'Late/Undertime')
            ->groupBy('date')
            ->get()
            ->keyBy('date');

        $this->attendanceData = collect($dateRange)->map(function ($date) use ($attendanceData) {
            $dateString = $date->toDateString();
            return [
                'date' => $dateString,
                'present_count' => $attendanceData[$dateString]->present_count ?? 0,
                'absent_count' => $attendanceData[$dateString]->absent_count ?? 0,
                'late_count' => $attendanceData[$dateString]->late_count ?? 0,
            ];
        });

        $this->overtimeData = collect($dateRange)->map(function ($date) use ($overtimeData) {
            $dateString = $date->toDateString();
            return [
                'date' => $dateString,
                'total_overtime' => $overtimeData[$dateString]->total_overtime ?? '00:00:00',
            ];
        });

        $this->lateData = collect($dateRange)->map(function ($date) use ($lateData) {
            $dateString = $date->toDateString();
            return [
                'date' => $dateString,
                'total_late' => $lateData[$dateString]->total_late ?? '00:00:00',
            ];
        });

        $this->calculateSummary();
    }

    private function calculateSummary()
    {
        $this->totalPresent = $this->attendanceData->sum('present_count');
        $this->totalAbsent = $this->attendanceData->sum('absent_count');
        $this->totalLate = $this->attendanceData->sum('late_count');

        $totalOvertimeSeconds = $this->overtimeData->sum(function ($day) {
            list($hours, $minutes, $seconds) = explode(':', $day['total_overtime']);
            return ($hours * 3600) + ($minutes * 60) + $seconds;
        });

        $avgOvertimeSeconds = $this->overtimeData->count() > 0 ? $totalOvertimeSeconds / $this->overtimeData->count() : 0;
        $this->avgOvertime = sprintf('%02d:%02d', floor($avgOvertimeSeconds / 3600), floor(($avgOvertimeSeconds % 3600) / 60));
    }

    public function render()
    {
        return view('livewire.dashboard.dashboard-dtr');
    }
}
