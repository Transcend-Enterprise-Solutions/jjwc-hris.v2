<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\DTRSchedule;
use App\Models\User;
use Carbon\Carbon;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Illuminate\Support\Facades\Storage;

#[Layout('layouts.app')]
#[Title('Schedule')]
class AdminSchedule extends Component
{
    use WithPagination, WithFileUploads;

    public $schedules;
    public $employees;
    public $scheduleId = null;
    public $thisEmployeeName, $emp_code, $wfh_days = [], $rest_days = [];
    public $default_start_time = '07:00', $default_end_time = '18:30';
    public $start_date, $end_date;
    public $isModalOpen = false;
    public $isEditMode = false;
    public $confirmingScheduleDeletion = false;
    public $scheduleToDelete;
    public $selectedTab = 'current';
    public $perPage = 10;
    public $search = '';
    public $is_flexi = false;
    public $has_break = false;
    public $is_overnight = false;
    public $is_24hours = false;

    // Import/Export properties
    public $importFile;
    public $isImportModalOpen = false;
    public $importErrors = [];
    public $importSummary = null;

    protected $queryString = ['search'];

    protected $rules = [
        'emp_code' => 'required|string',
        'wfh_days' => 'nullable|array',
        'rest_days' => 'nullable|array',
        'default_start_time' => 'required|date_format:H:i',
        'default_end_time' => 'required|date_format:H:i',
        'start_date' => 'required|date',
        'end_date' => 'required|date|after_or_equal:start_date',
        'is_flexi' => 'boolean',
        'has_break' => 'boolean',
        'is_overnight' => 'boolean',
        'is_24hours' => 'boolean',
    ];

    public function mount()
    {
        // Load all employees with their userData relationship
        $employees = User::where('user_role', 'emp')
            ->with('userData')
            ->get();

        // Sort by surname, then by first_name using the relationship data
        $this->employees = $employees->sort(function($a, $b) {
            $surnameA = strtolower($a->userData?->surname ?? '');
            $surnameB = strtolower($b->userData?->surname ?? '');
            
            // Compare surnames
            $surnameCompare = strcmp($surnameA, $surnameB);
            if ($surnameCompare !== 0) {
                return $surnameCompare;
            }
            
            // If surnames are same, compare first names
            $firstNameA = strtolower($a->userData?->first_name ?? '');
            $firstNameB = strtolower($b->userData?->first_name ?? '');
            return strcmp($firstNameA, $firstNameB);
        })->values();
    }

    public function render()
    {
        return view('livewire.admin.admin-schedule', [
            'filteredSchedules' => $this->filterSchedules()
        ]);
    }

    // ========== NAME FORMATTING FUNCTIONS ==========

    /**
     * Format name as "Lastname, Firstname Mi."
     */
    public function getFormattedName($user)
    {
        if (!$user) {
            return 'Unknown';
        }

        // Get the userData relationship
        $userData = $user->userData;
        
        if (!$userData) {
            // Fallback if userData doesn't exist
            return $user->name ?? 'Unknown';
        }

        // Extract name parts
        $surname = trim($userData->surname ?? '');
        $firstName = trim($userData->first_name ?? '');
        $middleName = trim($userData->middle_name ?? '');
        
        // Build middle initial
        $middleInitial = '';
        if (!empty($middleName)) {
            $middleInitial = strtoupper(substr($middleName, 0, 1)) . '.';
        }
        
        // Build formatted name
        if (!empty($surname) && !empty($firstName)) {
            $formatted = ucwords($surname) . ', ' . ucwords($firstName);
            if (!empty($middleInitial)) {
                $formatted .= ' ' . $middleInitial;
            }
            return $formatted;
        } elseif (!empty($surname)) {
            return ucwords($surname);
        } elseif (!empty($firstName)) {
            return ucwords($firstName);
        }
        
        return $user->name ?? 'Unknown';
    }

    // ========== IMPORT/EXPORT FUNCTIONS ==========

    /**
     * Download Excel template for schedule import
     */
    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers with styling
        $headers = [
            'A1' => 'Employee ID',
            'B1' => 'Employee Name',
            'C1' => 'Start Time (HH:MM)',
            'D1' => 'End Time (HH:MM)',
            'E1' => 'Start Date (YYYY-MM-DD)',
            'F1' => 'End Date (YYYY-MM-DD)',
            'G1' => 'Rest Days (comma separated)',
            'H1' => 'WFH Days (comma separated)',
            'I1' => 'Is Flexible (YES/NO)',
            'J1' => 'Has Break (YES/NO)',
            'K1' => 'Is Overnight (YES/NO)',
            'L1' => 'Is 24 Hours (YES/NO)',
        ];

        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }

        // Style header row
        $sheet->getStyle('A1:L1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ]);

        // Add instructions in row 2
        $sheet->setCellValue('A2', 'Example: E001');
        $sheet->setCellValue('B2', 'John Doe');
        $sheet->setCellValue('C2', '08:00');
        $sheet->setCellValue('D2', '17:00');
        $sheet->setCellValue('E2', date('Y-m-d'));
        $sheet->setCellValue('F2', date('Y-m-d', strtotime('+30 days')));
        $sheet->setCellValue('G2', 'Saturday,Sunday');
        $sheet->setCellValue('H2', 'Monday,Friday');
        $sheet->setCellValue('I2', 'NO');
        $sheet->setCellValue('J2', 'YES');
        $sheet->setCellValue('K2', 'NO');
        $sheet->setCellValue('L2', 'NO');

        // Style example row
        $sheet->getStyle('A2:L2')->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E7E6E6']],
            'font' => ['italic' => true, 'color' => ['rgb' => '666666']]
        ]);

        // Add notes section
        $sheet->setCellValue('A4', 'INSTRUCTIONS:');
        $sheet->setCellValue('A5', '1. Employee ID is required and must match existing employee records');
        $sheet->setCellValue('A6', '2. Employee Name is for reference only (will be auto-filled from Employee ID)');
        $sheet->setCellValue('A7', '3. Time format: HH:MM (24-hour format, e.g., 08:00, 17:30)');
        $sheet->setCellValue('A8', '4. Date format: YYYY-MM-DD (e.g., 2025-01-15)');
        $sheet->setCellValue('A9', '5. Days: Use full day names separated by commas (Monday,Tuesday,Wednesday)');
        $sheet->setCellValue('A10', '6. Valid days: Monday, Tuesday, Wednesday, Thursday, Friday, Saturday, Sunday');
        $sheet->setCellValue('A11', '7. Rest Days are required (at least one)');
        $sheet->setCellValue('A12', '8. WFH Days are optional');
        $sheet->setCellValue('A13', '9. YES/NO fields: Enter either YES or NO');
        $sheet->setCellValue('A14', '10. If "Is 24 Hours" is YES, start/end times will be ignored');

        $sheet->getStyle('A4')->getFont()->setBold(true);

        // Auto-size columns
        foreach (range('A', 'L') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Generate file
        $writer = new Xlsx($spreadsheet);
        $fileName = 'schedule_template_' . date('Y-m-d') . '.xlsx';
        $tempFile = tempnam(sys_get_temp_dir(), $fileName);
        $writer->save($tempFile);

        return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
    }

    /**
     * Export existing schedules to Excel
     */
    public function exportSchedules()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $headers = ['Employee ID', 'Employee Name', 'Start Time (HH:MM)', 'End Time (HH:MM)',
                   'Start Date (YYYY-MM-DD)', 'End Date (YYYY-MM-DD)', 'Rest Days (comma separated)',
                   'WFH Days (comma separated)', 'Is Flexible (YES/NO)', 'Has Break (YES/NO)',
                   'Is Overnight (YES/NO)', 'Is 24 Hours (YES/NO)'];

        $sheet->fromArray($headers, null, 'A1');

        // Style header
        $sheet->getStyle('A1:L1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ]);

        // Get schedules
        $schedules = DTRSchedule::with(['user' => function ($query) {
            $query->leftJoin('user_data', 'users.id', '=', 'user_data.user_id')
                ->select('users.*', 'user_data.surname', 'user_data.first_name', 'user_data.middle_name');
        }])->get();

        $row = 2;
        foreach ($schedules as $schedule) {
            $sheet->setCellValue('A' . $row, $schedule->emp_code);
            $sheet->setCellValue('B' . $row, $this->getFormattedName($schedule->user) ?? 'N/A');
            $sheet->setCellValue('C' . $row, $schedule->default_start_time);
            $sheet->setCellValue('D' . $row, $schedule->default_end_time);
            $sheet->setCellValue('E' . $row, $schedule->start_date->format('Y-m-d'));
            $sheet->setCellValue('F' . $row, $schedule->end_date->format('Y-m-d'));
            $sheet->setCellValue('G' . $row, $schedule->rest_days);
            $sheet->setCellValue('H' . $row, $schedule->wfh_days);
            $sheet->setCellValue('I' . $row, $schedule->is_flexi ? 'YES' : 'NO');
            $sheet->setCellValue('J' . $row, $schedule->has_break ? 'YES' : 'NO');
            $sheet->setCellValue('K' . $row, $schedule->is_overnight ? 'YES' : 'NO');
            $sheet->setCellValue('L' . $row, $schedule->is_24hours ? 'YES' : 'NO');
            $row++;
        }

        // Auto-size columns
        foreach (range('A', 'L') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Generate file
        $writer = new Xlsx($spreadsheet);
        $fileName = 'schedules_export_' . date('Y-m-d_His') . '.xlsx';
        $tempFile = tempnam(sys_get_temp_dir(), $fileName);
        $writer->save($tempFile);

        return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
    }

    /**
     * Open import modal
     */
    public function openImportModal()
    {
        $this->isImportModalOpen = true;
        $this->importErrors = [];
        $this->importSummary = null;
        $this->importFile = null;
    }

    /**
     * Close import modal
     */
    public function closeImportModal()
    {
        $this->isImportModalOpen = false;
        $this->importErrors = [];
        $this->importSummary = null;
        $this->importFile = null;
    }

    /**
     * Process imported Excel file
     */
    public function importSchedules()
    {
        $this->validate([
            'importFile' => 'required|mimes:xlsx,xls|max:2048'
        ]);

        try {
            $this->importErrors = [];
            $this->importSummary = [
                'total' => 0,
                'created' => 0,
                'updated' => 0,
                'skipped' => 0,
                'errors' => 0
            ];

            $spreadsheet = IOFactory::load($this->importFile->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            // Remove header row (Row 1)
            array_shift($rows);

            // Remove example row if it exists (Row 2)
            if (!empty($rows)) {
                $firstCell = strtolower(trim($rows[0][0] ?? ''));
                // Check if it's an example row
                if (strpos($firstCell, 'example') !== false ||
                    strpos($firstCell, 'e001') !== false ||
                    empty($firstCell)) {
                    array_shift($rows);
                }
            }

            DB::beginTransaction();

            foreach ($rows as $index => $row) {
                $actualRowNumber = $index + 3; // Excel row number (after header + example)

                // Skip completely empty rows
                $hasData = false;
                foreach ($row as $cell) {
                    if (!empty(trim($cell))) {
                        $hasData = true;
                        break;
                    }
                }

                if (!$hasData) {
                    continue;
                }

                // Skip instruction rows (starting from row 4 in template)
                $firstCell = strtolower(trim($row[0] ?? ''));
                if (strpos($firstCell, 'instruction') !== false ||
                    strpos($firstCell, 'note') !== false ||
                    is_numeric($firstCell) === false && strlen($firstCell) < 3) {
                    continue;
                }

                $this->importSummary['total']++;

                // Validate and process row
                $result = $this->processImportRow($row, $actualRowNumber);

                if ($result['success']) {
                    if ($result['action'] === 'created') {
                        $this->importSummary['created']++;
                    } else {
                        $this->importSummary['updated']++;
                    }
                } else {
                    $this->importSummary['errors']++;
                    $this->importErrors[] = "Row {$actualRowNumber}: " . $result['message'];
                }
            }

            if (empty($this->importErrors)) {
                DB::commit();
                $this->dispatch('swal', [
                    'title' => 'Import Successful!',
                    'text' => "Created: {$this->importSummary['created']}, Updated: {$this->importSummary['updated']}",
                    'icon' => 'success'
                ]);
                $this->closeImportModal();
            } else {
                DB::rollBack();
                $this->dispatch('swal', [
                    'title' => 'Import Failed',
                    'text' => 'Please check the errors below',
                    'icon' => 'error'
                ]);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Schedule import error: ' . $e->getMessage());
            $this->dispatch('swal', [
                'title' => 'Import Error',
                'text' => 'An error occurred while processing the file: ' . $e->getMessage(),
                'icon' => 'error'
            ]);
        }
    }

    /**
     * Process a single import row
     */
    private function processImportRow($row, $rowNumber)
    {
        // Extract data from row
        $empCode = trim($row[0] ?? '');
        $startTime = trim($row[2] ?? '');
        $endTime = trim($row[3] ?? '');
        $startDate = trim($row[4] ?? '');
        $endDate = trim($row[5] ?? '');
        $restDays = trim($row[6] ?? '');
        $wfhDays = trim($row[7] ?? '');
        $isFlexi = strtoupper(trim($row[8] ?? '')) === 'YES';
        $hasBreak = strtoupper(trim($row[9] ?? '')) === 'YES';
        $isOvernight = strtoupper(trim($row[10] ?? '')) === 'YES';
        $is24Hours = strtoupper(trim($row[11] ?? '')) === 'YES';

        // Validate required fields
        if (empty($empCode)) {
            return ['success' => false, 'message' => 'Employee ID is required'];
        }

        // Verify employee exists
        $user = User::where('emp_code', $empCode)->first();
        if (!$user) {
            return ['success' => false, 'message' => "Employee ID '{$empCode}' not found"];
        }

        // Validate dates
        if (empty($startDate) || empty($endDate)) {
            return ['success' => false, 'message' => 'Start Date and End Date are required'];
        }

        try {
            $startDate = Carbon::parse($startDate);
            $endDate = Carbon::parse($endDate);

            if ($endDate->lt($startDate)) {
                return ['success' => false, 'message' => 'End Date must be after Start Date'];
            }
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Invalid date format. Use YYYY-MM-DD'];
        }

        // Validate times
        if ($is24Hours) {
            $startTime = '00:00';
            $endTime = '23:59';
        } else {
            if (empty($startTime) || empty($endTime)) {
                return ['success' => false, 'message' => 'Start Time and End Time are required'];
            }

            // Normalize time format - accept HH:MM or HH:MM:SS
            $startTime = trim($startTime);
            $endTime = trim($endTime);

            // If time has seconds, remove them
            if (substr_count($startTime, ':') === 2) {
                $startTime = substr($startTime, 0, 5); // Keep only HH:MM
            }
            if (substr_count($endTime, ':') === 2) {
                $endTime = substr($endTime, 0, 5); // Keep only HH:MM
            }

            // Validate time format
            if (!preg_match('/^([0-1][0-9]|2[0-3]):[0-5][0-9]$/', $startTime) ||
                !preg_match('/^([0-1][0-9]|2[0-3]):[0-5][0-9]$/', $endTime)) {
                return ['success' => false, 'message' => 'Invalid time format. Use HH:MM (24-hour format)'];
            }
        }

        // Validate rest days
        if (empty($restDays)) {
            return ['success' => false, 'message' => 'Rest Days are required'];
        }

        $validDays = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        $restDaysArray = array_map('trim', explode(',', $restDays));
        $wfhDaysArray = !empty($wfhDays) ? array_map('trim', explode(',', $wfhDays)) : [];

        foreach ($restDaysArray as $day) {
            if (!in_array($day, $validDays)) {
                return ['success' => false, 'message' => "Invalid rest day: '{$day}'"];
            }
        }

        foreach ($wfhDaysArray as $day) {
            if (!empty($day) && !in_array($day, $validDays)) {
                return ['success' => false, 'message' => "Invalid WFH day: '{$day}'"];
            }
        }

        // Check for overlapping schedules
        $overlapQuery = DTRSchedule::where('emp_code', $empCode)
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate])
                    ->orWhereBetween('end_date', [$startDate, $endDate])
                    ->orWhere(function ($q) use ($startDate, $endDate) {
                        $q->where('start_date', '<=', $startDate)
                          ->where('end_date', '>=', $endDate);
                    });
            });

        $existingSchedule = $overlapQuery->first();

        // Create or update schedule
        $scheduleData = [
            'emp_code' => $empCode,
            'wfh_days' => !empty($wfhDaysArray) ? implode(',', $wfhDaysArray) : null,
            'rest_days' => implode(',', $restDaysArray),
            'default_start_time' => $startTime,
            'default_end_time' => $endTime,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'is_flexi' => $isFlexi,
            'has_break' => $hasBreak,
            'is_overnight' => $isOvernight,
            'is_24hours' => $is24Hours,
        ];

        if ($existingSchedule) {
            $existingSchedule->update($scheduleData);
            return ['success' => true, 'action' => 'updated'];
        } else {
            DTRSchedule::create($scheduleData);
            return ['success' => true, 'action' => 'created'];
        }
    }

    // ========== EXISTING FUNCTIONS ==========

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedDefaultStartTime()
    {
        if (!$this->is_24hours) {
            $this->checkOvernightShift();
        }
    }

    public function updatedDefaultEndTime()
    {
        if (!$this->is_24hours) {
            $this->checkOvernightShift();
        }
    }

    public function updatedIs24hours($value)
    {
        if ($value) {
            $this->default_start_time = '00:00';
            $this->default_end_time = '23:59';
            $this->is_overnight = false;
            $this->has_break = false;
        } else {
            $this->default_start_time = '07:00';
            $this->default_end_time = '18:30';
            $this->checkOvernightShift();
        }
    }

    private function checkOvernightShift()
    {
        if ($this->default_start_time && $this->default_end_time && !$this->is_24hours) {
            $startTime = strtotime($this->default_start_time);
            $endTime = strtotime($this->default_end_time);
            $this->is_overnight = $endTime <= $startTime;
        }
    }

    public function filterSchedules()
    {
        $now = Carbon::now()->startOfDay();
        $search = '%' . $this->search . '%';

        return DTRSchedule::with(['user' => function ($query) {
                $query->leftJoin('user_data', 'users.id', '=', 'user_data.user_id')
                    ->select('users.*', 'user_data.appointment', 'user_data.surname', 'user_data.first_name', 'user_data.middle_name');
            }])
            ->whereHas('user', function($query) use ($search) {
                $query->leftJoin('user_data', 'users.id', '=', 'user_data.user_id')
                    ->where('users.name', 'like', $search)
                    ->orWhere('user_data.surname', 'like', $search)
                    ->orWhere('user_data.first_name', 'like', $search);
            })
            ->when($this->selectedTab, function ($query) use ($now) {
                switch ($this->selectedTab) {
                    case 'current':
                        return $query->where('start_date', '<=', $now)
                                     ->where('end_date', '>=', $now);
                    case 'incoming':
                        return $query->where('start_date', '>', $now);
                    case 'expired':
                        return $query->where('end_date', '<', $now);
                }
            })
            ->when($this->selectedTab === 'expired', function ($query) {
                return $query->orderBy('end_date', 'desc');
            }, function ($query) {
                return $query->orderBy('start_date', 'asc');
            })
            ->paginate($this->perPage);
    }

    public function getDisplayEmpCode($empCode, $appointment)
    {
        if ($appointment === 'cos' && strpos($empCode, '1') === 0) {
            return 'D-' . substr($empCode, 1);
        }
        return $empCode;
    }

    public function getSortedWfhDays($wfhDays)
    {
        if (!$wfhDays) return 'None';

        $dayOrder = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        $wfhDaysArray = explode(',', $wfhDays);

        usort($wfhDaysArray, function ($a, $b) use ($dayOrder) {
            return array_search($a, $dayOrder) - array_search($b, $dayOrder);
        });

        return implode(', ', array_map(function($day) {
            return substr($day, 0, 3);
        }, $wfhDaysArray));
    }

    public function getSortedRestDays($restDays)
    {
        if (!$restDays) return 'None';

        $dayOrder = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        $restDaysArray = explode(',', $restDays);

        usort($restDaysArray, function ($a, $b) use ($dayOrder) {
            return array_search($a, $dayOrder) - array_search($b, $dayOrder);
        });

        return implode(', ', array_map(function($day) {
            return substr($day, 0, 3);
        }, $restDaysArray));
    }

    public function formatScheduleTime($startTime, $endTime, $isOvernight = false, $hasBreak = false, $is24hours = false)
    {
        if ($is24hours) {
            return '24 Hours';
        }

        $timeDisplay = $startTime . ' - ' . $endTime;

        if ($isOvernight) {
            $timeDisplay .= ' (Next Day)';
        }

        if ($hasBreak) {
            $timeDisplay .= ' (1hr break)';
        }

        return $timeDisplay;
    }

    public function setTab($tab)
    {
        $this->selectedTab = $tab;
        $this->resetPage();
    }

    public function openModal()
    {
        $this->resetValidation();
        $this->resetInputFields();
        $this->isModalOpen = true;
        $this->isEditMode = false;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetInputFields();
    }

    public function saveSchedule()
    {
        $this->default_start_time = date('H:i', strtotime($this->default_start_time));
        $this->default_end_time = date('H:i', strtotime($this->default_end_time));

        $this->validate();

        if (empty($this->rest_days)) {
            $this->addError('rest_days', 'Please select at least one rest day.');
            return;
        }

        $wfhDaysString = !empty($this->wfh_days) ? implode(',', $this->wfh_days) : null;
        $restDaysString = !empty($this->rest_days) ? implode(',', $this->rest_days) : null;

        $originalEmpCode = $this->emp_code;

        $overlapQuery = DTRSchedule::where('emp_code', $originalEmpCode)
            ->where(function ($query) {
                $query->whereBetween('start_date', [$this->start_date, $this->end_date])
                    ->orWhereBetween('end_date', [$this->start_date, $this->end_date])
                    ->orWhere(function ($q) {
                        $q->where('start_date', '<=', $this->start_date)
                          ->where('end_date', '>=', $this->end_date);
                    });
            });

        if ($this->scheduleId !== null && $this->scheduleId !== '') {
            $overlapQuery->where('id', '!=', (int)$this->scheduleId);
        }

        $overlappingSchedule = $overlapQuery->first();

        if ($overlappingSchedule) {
            $this->addError('date_range', 'This schedule overlaps with an existing schedule for this employee.');
            return;
        }

        DTRSchedule::updateOrCreate(
            ['id' => $this->scheduleId],
            [
                'emp_code' => $originalEmpCode,
                'wfh_days' => $wfhDaysString,
                'rest_days' => $restDaysString,
                'default_start_time' => $this->default_start_time,
                'default_end_time' => $this->default_end_time,
                'start_date' => $this->start_date,
                'end_date' => $this->end_date,
                'is_flexi' => $this->is_flexi,
                'has_break' => $this->has_break,
                'is_overnight' => $this->is_overnight,
                'is_24hours' => $this->is_24hours,
            ]
        );

        $this->dispatch('swal', [
            'title' => $this->scheduleId ? 'Schedule updated successfully.' : 'Schedule created successfully.',
            'icon' => 'success'
        ]);

        $this->closeModal();
    }

    public function edit($id)
    {
        $this->resetValidation();

        $schedule = DTRSchedule::findOrFail($id);
        $this->scheduleId = $id;
        $this->isEditMode = true;

        $user = User::where('emp_code', $schedule->emp_code)
            ->leftJoin('user_data', 'users.id', '=', 'user_data.user_id')
            ->select('users.*', 'user_data.surname', 'user_data.first_name', 'user_data.middle_name')
            ->first();

        $this->emp_code = $schedule->emp_code;
        $this->thisEmployeeName = $this->getFormattedName($user) ?? 'Unknown User';
        $this->wfh_days = !empty($schedule->wfh_days) ? explode(',', $schedule->wfh_days) : [];
        $this->rest_days = !empty($schedule->rest_days) ? explode(',', $schedule->rest_days) : [];
        $this->default_start_time = date('H:i', strtotime($schedule->default_start_time));
        $this->default_end_time = date('H:i', strtotime($schedule->default_end_time));
        $this->start_date = $schedule->start_date->format('Y-m-d');
        $this->end_date = $schedule->end_date->format('Y-m-d');
        $this->is_flexi = (bool)$schedule->is_flexi;
        $this->has_break = (bool)($schedule->has_break ?? false);
        $this->is_overnight = (bool)($schedule->is_overnight ?? false);
        $this->is_24hours = (bool)($schedule->is_24hours ?? false);

        $this->isModalOpen = true;
    }

    public function confirmDelete($id)
    {
        $this->scheduleToDelete = $id;
        $this->confirmingScheduleDeletion = true;
    }

    public function deleteConfirmed()
    {
        DTRSchedule::find($this->scheduleToDelete)->delete();
        $this->confirmingScheduleDeletion = false;
        $this->dispatch('swal', [
            'title' => 'Schedule deleted successfully!',
            'icon' => 'success'
        ]);
    }

    public function closeConfirmationModal()
    {
        $this->confirmingScheduleDeletion = false;
    }

    public function resetVariables()
    {
        $this->resetInputFields();
    }

    private function resetInputFields()
    {
        $this->scheduleId = null;
        $this->emp_code = '';
        $this->thisEmployeeName = '';
        $this->wfh_days = [];
        $this->rest_days = [];
        $this->default_start_time = '07:00';
        $this->default_end_time = '18:30';
        $this->start_date = null;
        $this->end_date = null;
        $this->is_flexi = false;
        $this->has_break = false;
        $this->is_overnight = false;
        $this->is_24hours = false;
        $this->isEditMode = false;
    }
}