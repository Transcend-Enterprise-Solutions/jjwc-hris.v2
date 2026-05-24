<?php

namespace App\Livewire\Admin\Reports;

use App\Exports\AttendanceExport;
use App\Exports\PerOfficeDivisionExport;
use App\Models\EmployeesDtr;
use Livewire\Component;
use App\Models\User;
use App\Models\DocRequest;
use App\Models\Rating;
use Carbon\Carbon;
use Exception;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\EmployeeReportExport;
use App\Exports\DocRequestExport;
use App\Exports\AverageOverallRatingsExport;

class HeadCount extends Component
{
    public $date;
    public $month;
    public $docRequestMonth;
    public $status = [
        'active' => true,
        'inactive' => true,
        'resigned' => true,
        'retired' => true,
    ];
    public $allStat;

    public $ratingsMonth;

    public function mount()
    {
        $this->date = now();
        $this->ratingsMonth = Carbon::now()->format('Y-m');
        $this->docRequestMonth = Carbon::now()->format('Y-m');
        $this->month = Carbon::now()->format('Y-m');
    }

    public function render()
    {
        $totalEmployees = User::where('user_role', 'emp')->count();

        $newEmployeesThisMonth = User::where('user_role', 'emp')->join('user_data', 'user_data.user_id', 'users.id');
        if ($this->month) {
            $date = Carbon::createFromFormat('Y-m', $this->month);
            $newEmployeesThisMonth = $newEmployeesThisMonth
                ->whereYear('user_data.date_hired', $date->year)
                ->whereMonth('user_data.date_hired', $date->month)
                ->count();
        } else {
            $newEmployeesThisMonth = $newEmployeesThisMonth
                ->whereYear('user_data.date_hired', Carbon::now()->year)
                ->whereMonth('user_data.date_hired', Carbon::now()->month)
                ->count();
        }

        $officeDivisionCounts = User::where('user_role', 'emp')
        ->join('positions', 'positions.id', 'users.position_id')
        ->join('office_divisions', 'office_divisions.id', 'users.office_division_id')
        ->when($this->status, function ($query) {
            return $query->where(function ($subQuery) {
                if ($this->status['active']) {
                    $subQuery->orWhere('active_status', 1);
                }
                if ($this->status['inactive']) {
                    $subQuery->orWhere('active_status', 0);
                }
                if ($this->status['resigned']) {
                    $subQuery->orWhere('active_status', 2);
                }
                if ($this->status['retired']) {
                    $subQuery->orWhere('active_status', 3);
                }
            });
        })
        ->selectRaw('office_divisions.office_division, count(*) as count')
        ->groupBy('office_division')
        ->get();

        // Daily attendance
        $dailyAttendance = $this->date ? EmployeesDtr::where('date', $this->date)
                            ->where(function ($query) {
                                $query->where('remarks', 'Present')
                                    ->orWhere('remarks', 'Late');
                            })
                            ->count() : 0;

        // Document requests count
        $docRequestsCount = DocRequest::when($this->docRequestMonth, function ($query) {
            $date = Carbon::createFromFormat('Y-m', $this->docRequestMonth);
            return $query->whereYear('date_requested', $date->year)
                         ->whereMonth('date_requested', $date->month);
        }, function ($query) {
            return $query->whereYear('date_requested', Carbon::now()->year)
                         ->whereMonth('date_requested', Carbon::now()->month);
        })->count();

        // Average overall rating per month
        $averageOverallRatings = Rating::selectRaw(
            'YEAR(created_at) as year, MONTH(created_at) as month, AVG(overall) as avg_overall'
        )
        ->when($this->ratingsMonth, function ($query) {
            $date = Carbon::createFromFormat('Y-m', $this->ratingsMonth);
            return $query->whereYear('created_at', $date->year)
                         ->whereMonth('created_at', $date->month);
        }, function ($query) {
            return $query->whereYear('created_at', Carbon::now()->year)
                         ->whereMonth('created_at', Carbon::now()->month);
        })
        ->groupBy('year', 'month')
        ->orderBy('year', 'desc')
        ->orderBy('month', 'desc')
        ->get();

        // Calculate the average overall rating
        $averageOverall = $averageOverallRatings->avg('avg_overall');

        return view('livewire.admin.reports.head-count', [
            'totalEmployees' => $totalEmployees,
            'newEmployeesThisMonth' => $newEmployeesThisMonth,
            'officeDivisionCounts' => $officeDivisionCounts,
            'dailyAttendance' => $dailyAttendance,
            'docRequestsCount' => $docRequestsCount,
            'averageOverallRatings' => $averageOverallRatings,
            'averageOverall' => $averageOverall,
        ]);
    }

    public function exportTotalEmployee()
    {
        try {
            $employees = User::where('user_role', 'emp')
                ->join('user_data', 'user_data.user_id', 'users.id')
                ->leftJoin('positions', 'positions.id', 'users.position_id')
                ->leftJoin('office_divisions', 'office_divisions.id', 'users.office_division_id')
                ->where('users.active_status', '!=', 4)
                ->select(
                    'users.name', 
                    'users.email', 
                    'users.emp_code', 
                    'users.active_status', 
                    'positions.position', 
                    'user_data.appointment', 
                    'user_data.date_hired',
                    'user_data.surname', 
                    'user_data.first_name', 
                    'user_data.middle_name', 
                    'user_data.name_extension',  
                    'office_divisions.office_division',
                )->orderBy('user_data.surname', 'asc');
            $filters = [
                'employees' => $employees,
            ];
            return Excel::download(new EmployeeReportExport($filters), 'EmployeesList.xlsx');
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function exportTotalEmployeeThisMonth()
    {
        try {
            $month = $this->month ? Carbon::createFromFormat('Y-m', $this->month)->format('Y-m') : now()->format('Y-m');
            $thisYear = null;
            $thisMonth = null;
            if(!$this->month){
                $thisYear = Carbon::now()->year;
                $thisMonth = Carbon::now()->month;
            }else{
                $date = Carbon::createFromFormat('Y-m', $this->month);
                $thisYear = $date->year;
                $thisMonth = $date->month;
            }
            $employees = User::where('user_role', 'emp')
                ->join('user_data', 'user_data.user_id', 'users.id')
                ->join('positions', 'positions.id', 'users.position_id')
                ->join('office_divisions', 'office_divisions.id', 'users.office_division_id')
                ->whereYear('user_data.date_hired', $thisYear)
                ->whereMonth('user_data.date_hired', $thisMonth)
                ->where('users.active_status', '!=', 4)
                ->select(
                    'users.name', 
                    'users.email', 
                    'users.emp_code', 
                    'users.active_status', 
                    'positions.position', 
                    'user_data.appointment', 
                    'user_data.date_hired', 
                    'user_data.surname', 
                    'user_data.first_name', 
                    'user_data.middle_name', 
                    'user_data.name_extension', 
                    'office_divisions.office_division',
                )->orderBy('user_data.surname', 'asc');
            $filters = [
                'employees' => $employees,
                'month' => $month,
            ];
            $filename = $month . ' EmployeesList.xlsx';
            return Excel::download(new EmployeeReportExport($filters), $filename);
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function exportTotalEmployeeInOfficeDivision($division)
    {
        try {
            $organizations = User::where('user_role', 'emp')
                ->join('user_data', 'user_data.user_id', 'users.id')
                ->join('positions', 'positions.id', 'users.position_id')
                ->join('office_divisions', 'office_divisions.id', 'users.office_division_id')
                ->leftJoin('office_division_units', 'office_division_units.id', 'users.unit_id')
                ->where('users.active_status', '!=', 4)
                ->select(
                    'users.name', 
                    'users.email', 
                    'users.emp_code', 
                    'users.active_status', 
                    'positions.position', 
                    'user_data.appointment', 
                    'user_data.date_hired', 
                    'user_data.surname', 
                    'user_data.first_name', 
                    'user_data.middle_name', 
                    'user_data.name_extension', 
                    'office_divisions.office_division',
                    'office_division_units.unit',
                )
                ->where('office_divisions.office_division', $division)
                ->when($this->status, function ($query) {
                    return $query->where(function ($subQuery) {
                        if ($this->status['active']) {
                            $subQuery->orWhere('active_status', 1);
                        }
                        if ($this->status['inactive']) {
                            $subQuery->orWhere('active_status', 0);
                        }
                        if ($this->status['resigned']) {
                            $subQuery->orWhere('active_status', 2);
                        }
                        if ($this->status['retired']) {
                            $subQuery->orWhere('active_status', 3);
                        }
                    });
                })->orderBy('user_data.surname', 'asc');

            $selectedStatuses = $this->allStat ? ['All'] : array_keys(array_filter($this->status));
            $statusLabels = [
                'active' => 'Active',
                'inactive' => 'Inactive',
                'resigned' => 'Resigned',
                'retired' => 'Retired',
                'promoted' => 'Promoted'
            ];
    
            $filters = [
                'organizations' => $organizations,
                'office_division' => $division,
                'statuses' => $selectedStatuses == ['All'] ? ['All'] : array_map(function($status) use ($statusLabels) {
                    return $statusLabels[$status];
                }, $selectedStatuses)
            ];
            $filename = $division . ' EmployeesList.xlsx';
            return Excel::download(new PerOfficeDivisionExport($filters), $filename);
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function exportTotalEmployeeDaily()
    {
        try {
            $filters = [
                'date' => $this->date,
            ];
            $filename = $this->date . ' EmployeesList.xlsx';
            return Excel::download(new AttendanceExport($filters), $filename);
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function exportDocRequests()
    {
        try {
            $month = $this->docRequestMonth ? Carbon::createFromFormat('Y-m', $this->docRequestMonth)->format('Y-m') : now()->format('Y-m');
            $filters = [
                'month' => $month,
            ];
            $filename = $month . ' DocRequestsList.xlsx';
            return Excel::download(new DocRequestExport($filters), $filename);
        } catch (Exception $e) {
            throw $e;
        }
    }
    public function exportAverageOverallRatings()
    {
        try {
            $month = $this->ratingsMonth ? Carbon::createFromFormat('Y-m', $this->ratingsMonth)->format('Y-m') : now()->format('Y-m');
            $filters = [
                'month' => $month,
            ];
            $filename = $month . ' AverageOverallRatings.xlsx';
            return Excel::download(new AverageOverallRatingsExport($filters), $filename);
        } catch (Exception $e) {
            throw $e;
        }
    }
}
