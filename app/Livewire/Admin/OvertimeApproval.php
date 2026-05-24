<?php

namespace App\Livewire\Admin;

use App\Models\EmployeesDtr;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ApprovedOvertimeExport;

#[Layout('layouts.app')]
#[Title('Overtime Approval')]

class OvertimeApproval extends Component
{
    use WithPagination;

    public $searchTerm = '';
    public $selectedMonth;
    public $selectedYear;
    public $approvalStatusFilter = 'all';
    public $selectedDivision = '';

    public $showOvertimeTypeModal = false;
    public $selectedDtrId;
    public $selectedOvertimeType = 'regular';
    public $availableOvertimeTypes = [
        'regular' => 'Regular',
        'night_differential' => 'Night Differential'
    ];

    public function mount()
    {
        $this->selectedMonth = Carbon::now()->month;
        $this->selectedYear = Carbon::now()->year;
    }

    public function updatedSearchTerm()
    {
        $this->resetPage();
    }

    public function updatedSelectedMonth()
    {
        $this->resetPage();
    }

    public function updatedSelectedYear()
    {
        $this->resetPage();
    }

    public function updatedApprovalStatusFilter()
    {
        $this->resetPage();
    }

    public function updatedSelectedDivision()
    {
        $this->resetPage();
    }

    public function openOvertimeTypeModal($dtrId)
    {
        $this->selectedDtrId = $dtrId;

        $dtr = EmployeesDtr::find($dtrId);
        if ($dtr && in_array($dtr->ot_type, ['regular', 'night_differential'])) {
            $this->selectedOvertimeType = $dtr->ot_type;
        } else {
            $this->selectedOvertimeType = 'regular';
        }

        $this->showOvertimeTypeModal = true;
    }

    public function closeOvertimeTypeModal()
    {
        $this->showOvertimeTypeModal = false;
        $this->selectedDtrId = null;
        $this->selectedOvertimeType = 'regular';
    }

    public function approveWithOvertimeType()
    {
        try {
            $dtr = EmployeesDtr::findOrFail($this->selectedDtrId);

            $dtr->update([
                'ot_approval_status' => 'approved',
                'ot_type' => $this->selectedOvertimeType,
                'updated_by' => Auth::user()->name ?? 'System'
            ]);

            $this->closeOvertimeTypeModal();
            session()->flash('message', "Overtime Approved successfully!");
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to approve overtime: ' . $e->getMessage());
        }
    }

    public function updateOvertimeStatus($dtrId, $status)
    {
        try {
            $dtr = EmployeesDtr::findOrFail($dtrId);

            if (!in_array($status, ['approved', 'rejected', 'pending'])) {
                session()->flash('error', 'Invalid status provided.');
                return;
            }

            if ($status === 'approved') {
                $this->openOvertimeTypeModal($dtrId);
                return;
            }

            $updateData = [
                'ot_approval_status' => $status,
                'updated_by' => Auth::user()->name ?? 'System'
            ];

            if ($status === 'rejected' || $status === 'pending') {
                $updateData['ot_type'] = null;
            }

            $dtr->update($updateData);

            $statusText = ucfirst($status);
            session()->flash('message', "Overtime {$statusText} successfully!");
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update overtime status: ' . $e->getMessage());
        }
    }

    public function approveOvertime($dtrId)
    {
        $this->openOvertimeTypeModal($dtrId);
    }

    public function rejectOvertime($dtrId)
    {
        $this->updateOvertimeStatus($dtrId, 'rejected');
    }

    public function resetToPending($dtrId)
    {
        $this->updateOvertimeStatus($dtrId, 'pending');
    }

    public function exportOvertimeToExcel()
    {
        try {
            $query = $this->getBaseQuery();

            $records = $query->orderBy('employees_dtr.date', 'desc')->get();

            if ($records->isEmpty()) {
                session()->flash('error', 'No overtime records to export based on current filters.');
                return;
            }

            // Generate filename based on status filter
            $statusText = $this->approvalStatusFilter === 'all' ? 'all' : $this->approvalStatusFilter;
            $filename = 'overtime_' . $statusText . '_' .
                        Carbon::parse($this->selectedYear . '-' . $this->selectedMonth . '-01')->format('F_Y') .
                        '_' . Carbon::now()->format('Y-m-d_His') . '.xlsx';

            return Excel::download(
                new ApprovedOvertimeExport(
                    $records,
                    $this->selectedMonth,
                    $this->selectedYear,
                    $this->approvalStatusFilter
                ),
                $filename
            );
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to export: ' . $e->getMessage());
        }
    }

    private function getBaseQuery()
    {
        $query = EmployeesDtr::query()
            ->leftJoin('users', 'employees_dtr.user_id', '=', 'users.id')
            ->leftJoin('office_divisions', 'users.office_division_id', '=', 'office_divisions.id')
            ->select([
                'employees_dtr.*',
                'users.name as user_name',
                'users.profile_photo_path',
                'office_divisions.office_division'
            ])
            ->where(function ($q) {
                $q->where(function ($q2) {
                    $q2->whereNotNull('employees_dtr.overtime')
                       ->where('employees_dtr.overtime', '!=', '')
                       ->where('employees_dtr.overtime', '!=', '00:00')
                       ->where('employees_dtr.overtime', '!=', '00:00:00')
                       ->whereRaw("TIME_TO_SEC(employees_dtr.overtime) >= 3600");
                })->orWhere(function ($q2) {
                    $q2->whereNotNull('employees_dtr.up_ot')
                       ->where('employees_dtr.up_ot', '!=', '')
                       ->where('employees_dtr.up_ot', '!=', '00:00')
                       ->where('employees_dtr.up_ot', '!=', '00:00:00')
                       ->whereRaw("TIME_TO_SEC(employees_dtr.up_ot) >= 3600");
                });
            });

        if ($this->selectedMonth && $this->selectedYear) {
            $query->whereMonth('employees_dtr.date', $this->selectedMonth)
                  ->whereYear('employees_dtr.date', $this->selectedYear);
        }

        if ($this->searchTerm) {
            $query->where(function ($q) {
                $q->where('users.name', 'like', '%' . $this->searchTerm . '%')
                  ->orWhere('employees_dtr.emp_code', 'like', '%' . $this->searchTerm . '%');
            });
        }

        if ($this->approvalStatusFilter === 'pending') {
            $query->where(function($q) {
                $q->where('employees_dtr.ot_approval_status', 'pending')
                  ->orWhereNull('employees_dtr.ot_approval_status');
            });
        } elseif ($this->approvalStatusFilter === 'approved') {
            $query->where('employees_dtr.ot_approval_status', 'approved');
        } elseif ($this->approvalStatusFilter === 'rejected') {
            $query->where('employees_dtr.ot_approval_status', 'rejected');
        }

        if ($this->selectedDivision) {
            $query->where('users.office_division_id', $this->selectedDivision);
        }

        return $query;
    }

    public function render()
    {
        $overtimeRecords = $this->getBaseQuery()
                                ->orderBy('employees_dtr.date', 'desc')
                                ->paginate(20);

        $officeDivisions = \App\Models\OfficeDivisions::all();

        return view('livewire.admin.overtime-approval', [
            'overtimeRecords' => $overtimeRecords,
            'officeDivisions' => $officeDivisions,
        ]);
    }
}
