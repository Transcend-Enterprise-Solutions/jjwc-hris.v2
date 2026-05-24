<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\EmployeesDtr;
use App\Models\OfficeDivisions;
use App\Models\OfficeDivisionUnits;
use App\Models\Holiday;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class AdminDtrTable extends Component
{
    use WithPagination;

    public $searchTerm;
    public $startDate;
    public $endDate;
    public $sortField = 'date';
    public $sortDirection = 'asc';
    public $signatoryName = '';
    public $eSignaturePath = '';
    public $pageSize = 10;
    public $pageSizes = [10, 20, 30, 50, 100];
    public $selectedAppointment = '';

    // Division Signatory Properties
    public $selectedDivision = null;
    public $signName = '';
    public $signPos = '';
    public $showSignatoryModal = false;

    // Unit Signatory Properties
    public $selectedUnit = null;
    public $unitSignName = '';
    public $unitSignPos = '';
    public $showUnitSignatoryModal = false;

    // Edit Modal Properties
    public $showEditModal = false;
    public $editId;
    public $editData = [
        'time_in' => '',
        'time_out' => '',
        'break_in' => '',
        'break_out' => '',
        'late' => '',
        'ut' => '',
        'overtime' => '',
        'total_hours_rendered' => '',
        'effective_remarks' => '',
    ];

    protected $queryString = [
        'searchTerm' => ['except' => ''],
        'startDate' => ['except' => ''],
        'endDate' => ['except' => ''],
        'sortField' => ['except' => 'date'],
        'sortDirection' => ['except' => 'asc'],
        'pageSize' => ['except' => 30],
        'selectedAppointment' => ['except' => ''],
    ];

    public function mount()
    {
        $this->startDate = Carbon::now()->startOfMonth()->toDateString();
        $this->endDate = Carbon::now()->endOfMonth()->toDateString();
    }

    // Division Signatory Methods
    public function openSignatoryModal($divisionId)
    {
        $this->selectedDivision = $divisionId;
        $division = OfficeDivisions::find($divisionId);

        if ($division) {
            $this->signName = $division->sign_name;
            $this->signPos = $division->sign_pos;
        }

        $this->showSignatoryModal = true;
    }

    public function saveSignatory()
    {
        $this->validate([
            'signName' => 'required',
            'signPos' => 'required',
            'selectedDivision' => 'required'
        ]);

        $division = OfficeDivisions::find($this->selectedDivision);
        $division->update([
            'sign_name' => $this->signName,
            'sign_pos' => $this->signPos
        ]);

        $this->showSignatoryModal = false;
        $this->dispatch('swal', [
            'title' => 'Division Signatory Updated Successfully!',
            'icon' => 'success'
        ]);
    }

    // Unit Signatory Methods
    public function openUnitSignatoryModal($unitId)
    {
        $this->selectedUnit = $unitId;
        $unit = OfficeDivisionUnits::find($unitId);

        if ($unit) {
            $this->unitSignName = $unit->sign_name;
            $this->unitSignPos = $unit->sign_pos;
        }

        $this->showUnitSignatoryModal = true;
    }

    public function saveUnitSignatory()
    {
        $this->validate([
            'unitSignName' => 'required',
            'unitSignPos' => 'required',
            'selectedUnit' => 'required'
        ]);

        $unit = OfficeDivisionUnits::find($this->selectedUnit);
        $unit->update([
            'sign_name' => $this->unitSignName,
            'sign_pos' => $this->unitSignPos
        ]);

        $this->showUnitSignatoryModal = false;
        $this->dispatch('swal', [
            'title' => 'Unit Signatory Updated Successfully!',
            'icon' => 'success'
        ]);
    }

    // Helper method to convert time format
    private function convertTimeFormat($time)
    {
        if (empty($time)) return '';

        // If it's already in H:i format, return as is
        if (preg_match('/^\d{2}:\d{2}$/', $time)) {
            return $time;
        }

        // Try to parse and convert from various formats
        try {
            return date('H:i', strtotime($time));
        } catch (\Exception $e) {
            return '';
        }
    }

    // Edit Modal Methods
    public function openEditModal($id)
    {
        $dtr = EmployeesDtr::findOrFail($id);

        $this->editId = $id;
        $this->editData = [
            'time_in' => $this->convertTimeFormat($dtr->up_time_in ?? $dtr->time_in),
            'time_out' => $this->convertTimeFormat($dtr->up_time_out ?? $dtr->time_out),
            'break_in' => $this->convertTimeFormat($dtr->up_break_in ?? $dtr->break_in),
            'break_out' => $this->convertTimeFormat($dtr->up_break_out ?? $dtr->break_out),
            'late' => $dtr->up_late ?? $dtr->late,
            'ut' => $dtr->up_ut ?? $dtr->ut,
            'overtime' => $dtr->up_ot ?? $dtr->overtime,
            'total_hours_rendered' => $dtr->up_total_hours_rendered ?? $dtr->total_hours_rendered,
            'effective_remarks' => $dtr->up_remarks ?? $dtr->remarks,
        ];

        $this->showEditModal = true;
    }

    public function saveEdit()
    {
        $this->validate([
            'editData.time_in' => 'nullable|date_format:H:i',
            'editData.time_out' => 'nullable|date_format:H:i',
            'editData.break_in' => 'nullable|date_format:H:i',
            'editData.break_out' => 'nullable|date_format:H:i',
            'editData.late' => 'nullable|string|max:255',
            'editData.ut' => 'nullable|string|max:255',
            'editData.overtime' => 'nullable|string|max:255',
            'editData.total_hours_rendered' => 'nullable|string|max:255',
            'editData.effective_remarks' => 'nullable|string|max:255',
        ], [
            'editData.time_in.date_format' => 'Time In must be in HH:MM format',
            'editData.time_out.date_format' => 'Time Out must be in HH:MM format',
            'editData.break_in.date_format' => 'Break In must be in HH:MM format',
            'editData.break_out.date_format' => 'Break Out must be in HH:MM format',
        ]);

        try {
            $dtr = EmployeesDtr::findOrFail($this->editId);

            $dtr->update([
                'up_time_in' => $this->editData['time_in'],
                'up_time_out' => $this->editData['time_out'],
                'up_break_in' => $this->editData['break_in'],
                'up_break_out' => $this->editData['break_out'],
                'up_late' => $this->editData['late'],
                'up_ut' => $this->editData['ut'],
                'up_ot' => $this->editData['overtime'],
                'up_remarks' => $this->editData['effective_remarks'],
                'up_total_hours_rendered' => $this->editData['total_hours_rendered'],
                'updated_by' => Auth::user()->name,
                'updated_at' => now(),
            ]);

            $this->showEditModal = false;
            $this->reset(['editData', 'editId']);

            $this->dispatch('swal', [
                'title' => 'DTR Updated Successfully!',
                'icon' => 'success'
            ]);

            // Refresh the component to show updated data
            $this->dispatch('$refresh');

        } catch (\Exception $e) {
            $this->dispatch('swal', [
                'title' => 'Error',
                'text' => 'Failed to update DTR: ' . $e->getMessage(),
                'icon' => 'error'
            ]);
        }
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->reset(['editData', 'editId']);
    }

    public function updatedPageSize()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }

        $this->sortField = $field;
    }

    public function placeholder()
    {
        return <<<'HTML'
        <div class="flex w-full flex-col gap-2">
            <livewire:skeleton/>
        </div>
        HTML;
    }

    public function render()
    {
        $query = EmployeesDtr::query()
            ->join('users', 'employees_dtr.user_id', '=', 'users.id')
            ->join('user_data', 'users.id', '=', 'user_data.user_id')
            ->select('employees_dtr.*', 'users.name as user_name', 'users.profile_photo_path',
                DB::raw("COALESCE(employees_dtr.up_remarks, employees_dtr.remarks) as effective_remarks")
            );

        // Apply search filter
        if ($this->searchTerm) {
            $query->where(function($q) {
                $q->where('users.emp_code', 'like', '%'.$this->searchTerm.'%')
                  ->orWhere('users.name', 'like', '%'.$this->searchTerm.'%');
            });
        }

        // Apply office division filter
        if ($this->selectedDivision) {
            $query->where('users.office_division_id', $this->selectedDivision);
        }

        // Apply appointment filter
        if ($this->selectedAppointment) {
            $query->where('user_data.appointment', $this->selectedAppointment);
        }

        // Apply date filters
        if ($this->startDate) {
            $query->where('employees_dtr.date', '>=', $this->startDate);
        }

        if ($this->endDate) {
            $query->where('employees_dtr.date', '<=', $this->endDate);
        }

        // Apply sorting
        if ($this->sortField === 'date') {
            $query->orderBy('employees_dtr.date', $this->sortDirection)
                  ->orderBy('users.name', 'asc');
        } elseif ($this->sortField === 'user.name') {
            $query->orderBy('users.name', $this->sortDirection);
        } elseif ($this->sortField === 'emp_code') {
            $query->orderByRaw("CASE
                WHEN user_data.appointment = 'cos' THEN CONCAT('D-', SUBSTRING(users.emp_code, 2))
                ELSE users.emp_code
            END " . $this->sortDirection);
        } else {
            $query->orderBy('employees_dtr.' . $this->sortField, $this->sortDirection);
        }

        $dtrs = $query->paginate($this->pageSize);
        $officeDivisions = OfficeDivisions::with('units')->get();

        return view('livewire.admin.admin-dtr-table', [
            'dtrs' => $dtrs,
            'officeDivisions' => $officeDivisions
        ]);
    }

    public function exportToPdf()
    {
        // Validate date range
        if (!$this->startDate || !$this->endDate) {
            $this->dispatch('swal', [
                'title' => 'Error',
                'text' => 'Please select a valid date range.',
                'icon' => 'error'
            ]);
            return null;
        }

        // Prepare the base query with unit join
        $query = EmployeesDtr::query()
            ->join('users', 'employees_dtr.user_id', '=', 'users.id')
            ->join('user_data', 'users.id', '=', 'user_data.user_id')
            ->leftJoin('office_divisions', 'users.office_division_id', '=', 'office_divisions.id')
            ->leftJoin('office_division_units', 'users.unit_id', '=', 'office_division_units.id')
            ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->select(
                'employees_dtr.*',
                'users.name as user_name',
                'users.unit_id',
                'positions.position as user_position',
                'office_divisions.office_division as user_department',
                'office_divisions.sign_name as division_sign_name',
                'office_divisions.sign_pos as division_sign_pos',
                'office_division_units.sign_name as unit_sign_name',
                'office_division_units.sign_pos as unit_sign_pos',
                DB::raw("CASE
                    WHEN user_data.appointment = 'cos' THEN CONCAT('D-', SUBSTRING(users.emp_code, 2))
                    ELSE users.emp_code
                END as emp_code"),
                DB::raw("COALESCE(employees_dtr.up_remarks, employees_dtr.remarks) as effective_remarks")
            )
            ->whereBetween('employees_dtr.date', [$this->startDate, $this->endDate]);

        // Apply search filter
        if ($this->searchTerm) {
            $query->where(function($q) {
                $q->where('users.emp_code', 'like', '%'.$this->searchTerm.'%')
                ->orWhere('users.name', 'like', '%'.$this->searchTerm.'%');
            });
        }

        // Apply office division filter
        if ($this->selectedDivision) {
            $query->where('users.office_division_id', $this->selectedDivision);
        }

        // Apply appointment filter
        if ($this->selectedAppointment) {
            $query->where('user_data.appointment', $this->selectedAppointment);
        }

        // Order the results
        $dtrs = $query->orderBy('users.name')
                    ->orderBy('employees_dtr.date')
                    ->get()
                    ->groupBy('user_name');

        // Prepare DTRs with summary
        $dtrsWithSummary = [];

        foreach ($dtrs as $employeeName => $employeeDtrs) {
            // Process each DTR record to use updated values when available
            $processedDtrs = $employeeDtrs->map(function ($dtr) {
                // Use updated values if available, otherwise use original values
                $dtr->effective_time_in = $dtr->up_time_in ?: $dtr->time_in;
                $dtr->effective_time_out = $dtr->up_time_out ?: $dtr->time_out;
                $dtr->effective_break_in = $dtr->up_break_in ?: $dtr->break_in;
                $dtr->effective_break_out = $dtr->up_break_out ?: $dtr->break_out;

                // Handle 10-minute late rule
                $lateTime = $dtr->up_late ?: $dtr->late;
                if ($lateTime && $lateTime !== '00:00') {
                    list($hours, $minutes) = explode(':', $lateTime);
                    $totalMinutes = (intval($hours) * 60) + intval($minutes);
                    // If late is 10 minutes or less, don't count it as late
                    if ($totalMinutes <= 10) {
                        $dtr->effective_late = '00:00';
                    } else {
                        $dtr->effective_late = $lateTime;
                    }
                } else {
                    $dtr->effective_late = $lateTime;
                }

                $dtr->effective_ut = $dtr->up_ut ?: $dtr->ut;

                // CRITICAL: Only show overtime if it has approved status
                $overtimeValue = $dtr->up_ot ?: $dtr->overtime;
                if ($dtr->ot_approval_status === 'approved' && $overtimeValue && $overtimeValue !== '00:00') {
                    $dtr->effective_overtime = $overtimeValue;
                } else {
                    $dtr->effective_overtime = '00:00';
                }

                $dtr->effective_total_hours_rendered = $dtr->up_total_hours_rendered ?: $dtr->total_hours_rendered;

                // Enhanced remarks logic with holiday type handling and overtime check
                $remarks = $dtr->up_remarks ?: $dtr->remarks;

                // Check if it's a special holiday type (non-Regular/Special)
                $holiday = Holiday::whereDate('holiday_date', $dtr->date)->first();
                if ($holiday && !in_array($holiday->type, ['Regular', 'Special'])) {
                    $dtr->effective_remarks = $holiday->description;
                } else if (strtolower($remarks) === 'late' && $dtr->effective_late === '00:00') {
                    // If late is <= 10 minutes and no other issues, consider as Present
                    $dtr->effective_remarks = 'Present';
                } else if (str_contains(strtolower($remarks), 'overtime') && $dtr->ot_approval_status !== 'approved') {
                    // If remark contains overtime but not approved, change to Present
                    $dtr->effective_remarks = 'Present';
                } else {
                    $dtr->effective_remarks = $remarks;
                }

                $dtr->effective_updated_by = $dtr->updated_by;

                return $dtr;
            });

            // Calculate days with time entries
            $daysWithTimeEntries = $processedDtrs->filter(function($dtr) {
                return $dtr->effective_time_in || $dtr->effective_time_out ||
                    $dtr->effective_break_in || $dtr->effective_break_out;
            })->count();

            // Calculate days worked (excluding absents and rest days)
            $daysWorked = $processedDtrs->filter(function($dtr) {
                $hasTimeEntries = $dtr->effective_time_in || $dtr->effective_time_out ||
                                $dtr->effective_break_in || $dtr->effective_break_out;

                $remarksLower = strtolower($dtr->effective_remarks);

                // Don't count as worked if absent or rest day
                if ($remarksLower === 'absent' || $remarksLower === 'rest day') {
                    return false;
                }

                return $hasTimeEntries;
            })->count();

            // Calculate absences
            $absences = $processedDtrs->filter(function($dtr) {
                return strtolower($dtr->effective_remarks) === 'absent';
            })->count();

            // Calculate leave days
            $leaveDays = $processedDtrs->filter(function($dtr) {
                return str_contains(strtolower($dtr->effective_remarks), 'leave');
            })->count();

            // Calculate holidays (including special types)
            $holidays = $processedDtrs->filter(function($dtr) {
                $remarksLower = strtolower($dtr->effective_remarks);
                return str_contains($remarksLower, 'holiday') ||
                       str_contains($remarksLower, 'suspension') ||
                       str_contains($remarksLower, 'half-day');
            })->count();

            // Calculate rest days
            $restDays = $processedDtrs->filter(function($dtr) {
                return strtolower($dtr->effective_remarks) === 'rest day';
            })->count();

            // Calculate overtime hours (ONLY APPROVED OVERTIME)
            $totalOvertimeMinutes = 0;
            foreach ($processedDtrs as $dtr) {
                if ($dtr->ot_approval_status === 'approved' && !empty($dtr->effective_overtime) && $dtr->effective_overtime !== '00:00') {
                    list($hours, $minutes) = explode(':', $dtr->effective_overtime);
                    $totalOvertimeMinutes += (intval($hours) * 60) + intval($minutes);
                }
            }
            $overtime = sprintf("%02d:%02d", floor($totalOvertimeMinutes / 60), $totalOvertimeMinutes % 60);

            // Calculate late hours (excluding 10 minutes or less)
            $totalLateMinutes = 0;
            foreach ($processedDtrs as $dtr) {
                $hasTimeEntries = $dtr->effective_time_in || $dtr->effective_time_out ||
                                $dtr->effective_break_in || $dtr->effective_break_out;
                if ($hasTimeEntries && !empty($dtr->effective_late) && $dtr->effective_late !== '00:00') {
                    list($hours, $minutes) = explode(':', $dtr->effective_late);
                    $lateMinutes = (intval($hours) * 60) + intval($minutes);
                    // Only count late if more than 10 minutes
                    if ($lateMinutes > 10) {
                        $totalLateMinutes += $lateMinutes;
                    }
                }
            }
            $late = sprintf("%02d:%02d", floor($totalLateMinutes / 60), $totalLateMinutes % 60);

            // Calculate undertime hours
            $totalUndertimeMinutes = 0;
            foreach ($processedDtrs as $dtr) {
                $hasTimeEntries = $dtr->effective_time_in || $dtr->effective_time_out ||
                                $dtr->effective_break_in || $dtr->effective_break_out;
                if ($hasTimeEntries && !empty($dtr->effective_ut) && $dtr->effective_ut !== '00:00') {
                    list($hours, $minutes) = explode(':', $dtr->effective_ut);
                    $totalUndertimeMinutes += (intval($hours) * 60) + intval($minutes);
                }
            }
            $undertime = sprintf("%02d:%02d", floor($totalUndertimeMinutes / 60), $totalUndertimeMinutes % 60);

            // Calculate total tardiness
            $totalTardinessMinutes = $totalLateMinutes + $totalUndertimeMinutes;
            $tardiness = sprintf("%02d:%02d", floor($totalTardinessMinutes / 60), $totalTardinessMinutes % 60);

            // Determine the correct signatory
            $employee = $employeeDtrs->first();
            if ($employee->unit_id) {
                // Use unit signatory if employee belongs to a unit
                $signName = $employee->unit_sign_name ?? '';
                $signPos = $employee->unit_sign_pos ?? '';
            } else {
                // Use division signatory if no unit
                $signName = $employee->division_sign_name ?? '';
                $signPos = $employee->division_sign_pos ?? '';
            }

            // Store the DTRs and summary for this employee
            $dtrsWithSummary[$employeeName] = [
                'dtrs' => $processedDtrs,
                'summary' => [
                    'days_worked' => $daysWorked,
                    'absences' => $absences,
                    'overtime' => $overtime,
                    'late' => $late,
                    'undertime' => $undertime,
                    'tardiness' => $tardiness,
                    'leave_days' => $leaveDays,
                    'holidays' => $holidays,
                    'rest_days' => $restDays
                ],
                'signatory' => [
                    'name' => $signName,
                    'position' => $signPos
                ]
            ];
        }

        // Get division name for PDF title if division is selected
        $divisionName = '';
        if ($this->selectedDivision) {
            $division = OfficeDivisions::find($this->selectedDivision);
            if ($division) {
                $divisionName = $division->office_division;
            }
        }

        // Get the authenticated user's e-signature path
        $this->eSignaturePath = auth()->user()->esignature_path ?? null;

        // Generate PDF
        try {
            $pdf = Pdf::loadView('pdf.dtr', [
                'dtrsWithSummary' => $dtrsWithSummary,
                'startDate' => $this->startDate,
                'endDate' => $this->endDate,
                'eSignaturePath' => $this->eSignaturePath,
                'divisionName' => $divisionName,
            ])->setPaper('legal', 'portrait');

            // Dispatch success notification
            $this->dispatch('swal', [
                'title' => 'DTR Exported Successfully!',
                'icon' => 'success'
            ]);

            // Stream the PDF download
            return response()->streamDownload(function () use ($pdf) {
                echo $pdf->output();
            }, 'dtr_report_'.now()->format('YmdHis').'.pdf');

        } catch (\Exception $e) {
            // Handle any PDF generation errors
            $this->dispatch('swal', [
                'title' => 'Error',
                'text' => 'Failed to generate PDF: ' . $e->getMessage(),
                'icon' => 'error'
            ]);

            return null;
        }
    }

    public function downloadFile($dtrId)
    {
        $dtr = EmployeesDtr::find($dtrId);
        if ($dtr && $dtr->attachment) {
            $originalExtension = pathinfo($dtr->attachment, PATHINFO_EXTENSION);
            $friendlyFilename = "DTR_" . $dtr->date . "." . $originalExtension;
            return Storage::download($dtr->attachment, $friendlyFilename);
        } else {
            $this->dispatch('swal', [
                'title' => 'File not found!',
                'icon' => 'error'
            ]);
        }
    }
}
