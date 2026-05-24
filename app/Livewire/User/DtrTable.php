<?php

namespace App\Livewire\User;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\EmployeesDtr;
use App\Models\Holiday;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;

class DtrTable extends Component
{
    use WithPagination;
    use WithFileUploads;

    public $searchTerm = '';
    public $startDate;
    public $endDate;
    public $sortField = 'date';
    public $sortDirection = 'asc';
    public $eSignature;
    public $pageSize = 15;
    public $pageSizes = [10, 20, 30, 50, 100];

    protected $queryString = [
        'searchTerm' => ['except' => ''],
        'startDate' => ['except' => ''],
        'endDate' => ['except' => ''],
        'sortField' => ['except' => 'date'],
        'sortDirection' => ['except' => 'asc'],
    ];

    public function placeholder()
    {
        return <<<'HTML'
        <div class="flex w-full flex-col gap-2">
            <livewire:skeleton/>
        </div>
        HTML;
    }

    public function mount()
    {
        $this->startDate = Carbon::now()->startOfMonth()->toDateString();
        $this->endDate = Carbon::now()->endOfMonth()->toDateString();
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

    public function render()
    {
        $query = EmployeesDtr::query()
            ->where('user_id', Auth::id())
            ->select('employees_dtr.*',
                DB::raw("COALESCE(employees_dtr.up_remarks, employees_dtr.remarks) as effective_remarks")
            );

        if ($this->searchTerm) {
            $query->where(function ($q) {
                $q->where('date', 'like', '%' . $this->searchTerm . '%')
                  ->orWhere('day_of_week', 'like', '%' . $this->searchTerm . '%')
                  ->orWhere('location', 'like', '%' . $this->searchTerm . '%');
            });
        }

        if ($this->startDate) {
            $query->where('date', '>=', $this->startDate);
        }

        if ($this->endDate) {
            $query->where('date', '<=', $this->endDate);
        }

        $query->orderBy($this->sortField, $this->sortDirection);

        $dtrs = $query->paginate($this->pageSize);

        return view('livewire.user.dtr-table', ['dtrs' => $dtrs]);
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

    public function exportToPdf($signatoryName)
    {
        $user = Auth::user();

        $this->validate([
            'eSignature' => 'nullable|image|max:1024', // 1MB Max
        ]);

        // Validate date range
        if (!$this->startDate || !$this->endDate) {
            $this->dispatch('swal', [
                'title' => 'Error',
                'text' => 'Please select a valid date range.',
                'icon' => 'error'
            ]);
            return null;
        }

        $query = EmployeesDtr::query()
            ->where('user_id', $user->id)
            ->whereBetween('date', [$this->startDate, $this->endDate])
            ->select(
                'employees_dtr.*',
                DB::raw("COALESCE(employees_dtr.up_remarks, employees_dtr.remarks) as effective_remarks")
            );

        if ($this->searchTerm) {
            $query->where(function($q) {
                $q->where('date', 'like', '%'.$this->searchTerm.'%')
                  ->orWhere('day_of_week', 'like', '%'.$this->searchTerm.'%')
                  ->orWhere('location', 'like', '%'.$this->searchTerm.'%');
            });
        }

        $dtrs = $query->orderBy('date')->get();

        if ($dtrs->isEmpty()) {
            $this->dispatch('swal', [
                'title' => 'No DTR records found for the selected date range.',
                'icon' => 'error'
            ]);
            return null;
        }

        // Process each DTR record to use updated values when available
        $processedDtrs = $dtrs->map(function ($dtr) {
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

            return $dtr;
        });

        // Calculate summary statistics
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

        // Prepare DTRs with summary
        $dtrsWithSummary = [
            $user->name => [
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
                    'name' => '',
                    'position' => ''
                ]
            ]
        ];

        $eSignaturePath = null;
        if ($this->eSignature) {
            $eSignaturePath = $this->eSignature->store('temp', 'public');
        }

        try {
            $pdf = Pdf::loadView('pdf.dtr', [
                'dtrsWithSummary' => $dtrsWithSummary,
                'startDate' => $this->startDate,
                'endDate' => $this->endDate,
                'signatoryName' => $signatoryName,
                'eSignaturePath' => $eSignaturePath,
                'userName' => $user->name,
                'empCode' => $user->emp_code,
            ])->setPaper('legal', 'portrait');

            $this->dispatch('swal', [
                'title' => 'DTR Exported Successfully!',
                'icon' => 'success'
            ]);

            return response()->streamDownload(function () use ($pdf) {
                echo $pdf->output();
            }, 'dtr_report_'.now()->format('YmdHis').'.pdf');

        } catch (\Exception $e) {
            $this->dispatch('swal', [
                'title' => 'Error',
                'text' => 'Failed to generate PDF: ' . $e->getMessage(),
                'icon' => 'error'
            ]);
            return null;
        } finally {
            if ($eSignaturePath) {
                Storage::disk('public')->delete($eSignaturePath);
            }
        }
    }
}
