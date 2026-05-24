<?php

namespace App\Jobs;

use App\Models\EmployeesDtr;
use App\Models\OfficeDivisions;
use App\Models\Holiday;
use App\Models\DtrExport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use ZipArchive;
use Illuminate\Support\Facades\Log as Log;

class ExportDtrPdfJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 3600;
    public $tries = 2;
    public $maxExceptions = 2;

    protected $exportId;
    protected $userId;
    protected $startDate;
    protected $endDate;
    protected $searchTerm;
    protected $selectedDivision;
    protected $selectedAppointment;
    protected $eSignaturePath;
    protected $currentProgress = 0;

    const EMPLOYEE_THRESHOLD = 50;
    const EMPLOYEES_PER_PDF = 20;
    const MAX_WORKING_DAYS = 22; // Maximum working days per month

    public function __construct(
        $exportId,
        $userId,
        $startDate,
        $endDate,
        $searchTerm = null,
        $selectedDivision = null,
        $selectedAppointment = null,
        $eSignaturePath = null
    ) {
        $this->exportId = $exportId;
        $this->userId = $userId;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->searchTerm = $searchTerm;
        $this->selectedDivision = $selectedDivision;
        $this->selectedAppointment = $selectedAppointment;
        $this->eSignaturePath = $eSignaturePath;
        
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', '3600');
    }

    public function handle()
    {
        try {
            \Log::info('Starting DTR export job', ['export_id' => $this->exportId]);
            
            $this->updateProgress(5, 'processing', 'Starting DTR export...');
            $this->updateProgress(10, 'processing', 'Fetching DTR records from database...');
            
            $query = $this->buildQuery();
            
            $dtrs = $query->orderBy('office_divisions.office_division')
                          ->orderBy('formatted_user_name')
                          ->orderBy('employees_dtr.date')
                          ->get()
                          ->groupBy('formatted_user_name');

            $totalEmployees = count($dtrs);
            
            \Log::info('DTR records fetched', [
                'export_id' => $this->exportId,
                'employee_count' => $totalEmployees,
                'total_records' => $dtrs->flatten(1)->count()
            ]);

            if ($totalEmployees > self::EMPLOYEE_THRESHOLD) {
                \Log::info('Large export detected, splitting into multiple PDFs', [
                    'export_id' => $this->exportId,
                    'total_employees' => $totalEmployees
                ]);
                $this->handleLargeExport($dtrs);
            } else {
                $this->handleNormalExport($dtrs);
            }

            $this->updateProgress(100, 'completed', 'Export completed successfully!');

        } catch (\Exception $e) {
            \Log::error('DTR export job failed', [
                'export_id' => $this->exportId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->updateProgress(0, 'failed', 'Export failed: ' . $e->getMessage());
            
            DtrExport::where('id', $this->exportId)->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    protected function buildQuery()
    {
        $query = EmployeesDtr::query()
            ->join('users', 'employees_dtr.user_id', '=', 'users.id')
            ->join('user_data', 'users.id', '=', 'user_data.user_id')
            ->leftJoin('office_divisions', 'users.office_division_id', '=', 'office_divisions.id')
            ->leftJoin('office_division_units', 'users.unit_id', '=', 'office_division_units.id')
            ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->select(
                'employees_dtr.*',
                'users.name as user_name',
                'user_data.surname',
                'user_data.first_name',
                'user_data.middle_name',
                'users.unit_id',
                'positions.position as user_position',
                'office_divisions.office_division as user_department',
                'office_divisions.sign_name as division_sign_name',
                'office_divisions.sign_pos as division_sign_pos',
                'office_division_units.sign_name as unit_sign_name',
                'office_division_units.sign_pos as unit_sign_pos',
                'user_data.permanent_selectedRegion',
                'user_data.residential_selectedRegion',
                DB::raw("CASE
                    WHEN user_data.appointment = 'cos' THEN CONCAT('D-', SUBSTRING(users.emp_code, 2))
                    ELSE users.emp_code
                END as emp_code"),
                DB::raw("COALESCE(employees_dtr.up_remarks, employees_dtr.remarks) as effective_remarks"),
                DB::raw("CONCAT(COALESCE(user_data.surname, ''), ', ', COALESCE(user_data.first_name, ''), ' ', SUBSTRING(COALESCE(user_data.middle_name, ''), 1, 1)) as formatted_user_name")
            )
            ->whereBetween('employees_dtr.date', [$this->startDate, $this->endDate]);

        if ($this->searchTerm) {
            $query->where(function($q) {
                $q->where('users.emp_code', 'like', '%'.$this->searchTerm.'%')
                  ->orWhere('users.name', 'like', '%'.$this->searchTerm.'%')
                  ->orWhere('user_data.surname', 'like', '%'.$this->searchTerm.'%')
                  ->orWhere('user_data.first_name', 'like', '%'.$this->searchTerm.'%');
            });
        }

        if ($this->selectedDivision) {
            $query->where('users.office_division_id', $this->selectedDivision);
        }

        if ($this->selectedAppointment) {
            $query->where('user_data.appointment', $this->selectedAppointment);
        }

        return $query;
    }

    protected function handleNormalExport($dtrs)
    {
        $totalEmployees = count($dtrs);
        $this->updateProgress(20, 'processing', "Processing {$totalEmployees} employees...");

        $dtrsWithSummary = $this->processDtrs($dtrs, $totalEmployees, 20, 70);

        $divisionName = $this->getDivisionName();

        $this->updateProgress(70, 'processing', 'Generating single PDF document...');
        
        $filename = $this->generateSinglePdf($dtrsWithSummary, $divisionName);

        DtrExport::where('id', $this->exportId)->update([
            'status' => 'completed',
            'file_path' => $filename,
            'completed_at' => now(),
        ]);

        \Log::info('Normal DTR export completed', [
            'export_id' => $this->exportId,
            'filename' => $filename,
            'total_employees' => $totalEmployees
        ]);
    }

    protected function handleLargeExport($dtrs)
    {
        $totalEmployees = count($dtrs);
        $this->updateProgress(20, 'processing', "Processing large export with {$totalEmployees} employees...");

        $chunks = $dtrs->chunk(self::EMPLOYEES_PER_PDF);
        $totalChunks = $chunks->count();
        $pdfFiles = [];
        $chunkIndex = 0;

        \Log::info('Starting chunked PDF generation', [
            'export_id' => $this->exportId,
            'total_chunks' => $totalChunks,
            'employees_per_chunk' => self::EMPLOYEES_PER_PDF
        ]);

        foreach ($chunks as $chunkIndex => $chunk) {
            $currentChunk = $chunkIndex + 1;
            $progressPercent = 20 + (($chunkIndex / $totalChunks) * 55);
            
            $this->updateProgress($progressPercent, 'processing', "Generating PDF {$currentChunk} of {$totalChunks}...");

            $dtrsWithSummary = $this->processDtrs($chunk, self::EMPLOYEES_PER_PDF, 0, 0);
            $divisionName = $this->getDivisionName();
            
            $filename = $this->generateChunkedPdf($dtrsWithSummary, $divisionName, $currentChunk, $totalChunks);
            $pdfFiles[] = $filename;

            \Log::info('Chunk PDF generated', [
                'export_id' => $this->exportId,
                'chunk' => $currentChunk,
                'filename' => $filename
            ]);
        }

        $this->updateProgress(75, 'processing', 'Creating ZIP archive...');
        
        $zipFilename = $this->createZipArchive($pdfFiles);

        $this->cleanupTemporaryFiles($pdfFiles);

        $this->updateProgress(90, 'processing', 'Finalizing export...');

        DtrExport::where('id', $this->exportId)->update([
            'status' => 'completed',
            'file_path' => $zipFilename,
            'completed_at' => now(),
        ]);

        \Log::info('Large DTR export completed', [
            'export_id' => $this->exportId,
            'zip_file' => $zipFilename,
            'total_pdfs' => count($pdfFiles),
            'total_employees' => $totalEmployees
        ]);
    }

    protected function processDtrs($dtrs, $totalCount, $startProgress, $endProgress)
    {
        $progressPerEmployee = ($endProgress - $startProgress) / max($totalCount, 1);
        $processed = [];
        $index = 0;

        foreach ($dtrs as $employeeName => $employeeDtrs) {
            $index++;
            
            if ($startProgress > 0 || $endProgress > 0) {
                $progressPercent = $startProgress + ($index * $progressPerEmployee);
                $this->updateProgress($progressPercent, 'processing', "Processing employee {$index} of {$totalCount}...");
            }

            $processedEmployeeDtrs = $employeeDtrs->map(function($dtr) {
                return $this->processDtrRecord($dtr);
            })->values();

            $summary = $this->calculateSummary($processedEmployeeDtrs);

            $processed[$employeeName] = [
                'dtrs' => $processedEmployeeDtrs,
                'summary' => $summary,
                'working_days' => $summary['working_days'] // Use calculated working days
            ];
        }

        return $processed;
    }

    protected function generateSinglePdf($dtrsWithSummary, $divisionName)
    {
        $pdf = Pdf::loadView('pdf.dtr', [
            'dtrs' => $dtrsWithSummary,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'workingDays' => self::MAX_WORKING_DAYS,
            'divisionName' => $divisionName,
            'eSignaturePath' => $this->eSignaturePath,
        ])->setPaper('a4', 'portrait');

        if (!Storage::disk('local')->exists('dtr_exports')) {
            Storage::disk('local')->makeDirectory('dtr_exports');
        }

        $filename = 'dtr_exports/DTR_' . Carbon::now()->format('Y-m-d_H-i-s') . '.pdf';
        Storage::disk('local')->put($filename, $pdf->output());

        return $filename;
    }

    protected function generateChunkedPdf($dtrsWithSummary, $divisionName, $currentChunk, $totalChunks)
    {
        $pdf = Pdf::loadView('pdf.dtr', [
            'dtrs' => $dtrsWithSummary,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'workingDays' => self::MAX_WORKING_DAYS,
            'divisionName' => $divisionName,
            'eSignaturePath' => $this->eSignaturePath,
            'isChunk' => true,
            'chunkInfo' => "Part {$currentChunk} of {$totalChunks}",
        ])->setPaper('a4', 'portrait');

        if (!Storage::disk('local')->exists('dtr_exports/temp')) {
            Storage::disk('local')->makeDirectory('dtr_exports/temp');
        }

        $filename = 'dtr_exports/temp/DTR_Part_' . str_pad($currentChunk, 3, '0', STR_PAD_LEFT) . '.pdf';
        Storage::disk('local')->put($filename, $pdf->output());

        return $filename;
    }

    protected function createZipArchive($pdfFiles)
    {
        if (!Storage::disk('local')->exists('dtr_exports')) {
            Storage::disk('local')->makeDirectory('dtr_exports');
        }

        $zipFilename = 'dtr_exports/DTR_' . Carbon::now()->format('Y-m-d_H-i-s') . '.zip';
        $zipPath = Storage::path($zipFilename);

        $zip = new ZipArchive();
        $zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        foreach ($pdfFiles as $index => $file) {
            $realPath = Storage::path($file);
            if (file_exists($realPath)) {
                $arcname = 'DTR_Part_' . str_pad($index + 1, 3, '0', STR_PAD_LEFT) . '.pdf';
                $zip->addFile($realPath, $arcname);
            }
        }

        $zip->close();

        \Log::info('ZIP archive created', [
            'export_id' => $this->exportId,
            'zip_file' => $zipFilename,
            'file_count' => count($pdfFiles)
        ]);

        return $zipFilename;
    }

    protected function cleanupTemporaryFiles($pdfFiles)
    {
        foreach ($pdfFiles as $file) {
            try {
                if (Storage::disk('local')->exists($file)) {
                    Storage::delete($file);
                    \Log::info('Temporary file deleted', [
                        'export_id' => $this->exportId,
                        'file' => $file
                    ]);
                }
            } catch (\Exception $e) {
                \Log::warning('Failed to delete temporary file', [
                    'export_id' => $this->exportId,
                    'file' => $file,
                    'error' => $e->getMessage()
                ]);
            }
        }

        try {
            $tempDir = Storage::path('dtr_exports/temp');
            if (file_exists($tempDir) && count(scandir($tempDir)) === 2) {
                rmdir($tempDir);
            }
        } catch (\Exception $e) {
            \Log::warning('Failed to remove temp directory', [
                'export_id' => $this->exportId,
                'error' => $e->getMessage()
            ]);
        }
    }

    protected function getDivisionName()
    {
        if ($this->selectedDivision) {
            $division = OfficeDivisions::find($this->selectedDivision);
            if ($division) {
                return $division->office_division;
            }
        }
        return '';
    }

    /**
     * Get the user's active region ID for holiday matching
     */
    protected function getUserRegionId($dtr): ?int
    {
        return $dtr->permanent_selectedRegion ?? $dtr->residential_selectedRegion;
    }

    protected function processDtrRecord($dtr)
    {
        if (!is_object($dtr)) {
            \Log::error('Invalid DTR record - not an object', [
                'export_id' => $this->exportId,
                'dtr_type' => gettype($dtr)
            ]);
            throw new \Exception('Invalid DTR record received');
        }

        $dtr->effective_time_in = $dtr->up_time_in ?? $dtr->time_in ?? null;
        $dtr->effective_time_out = $dtr->up_time_out ?? $dtr->time_out ?? null;
        $dtr->effective_break_in = $dtr->up_break_in ?? $dtr->break_in ?? null;
        $dtr->effective_break_out = $dtr->up_break_out ?? $dtr->break_out ?? null;

        $dtr->display_time_in = $this->formatTimeToStandard($dtr->effective_time_in);
        $dtr->display_time_out = $this->formatTimeToStandard($dtr->effective_time_out);
        $dtr->display_break_in = $this->formatTimeToStandard($dtr->effective_break_in);
        $dtr->display_break_out = $this->formatTimeToStandard($dtr->effective_break_out);

        $lateTime = $dtr->up_late ?? $dtr->late ?? null;
        if ($lateTime && $lateTime !== '00:00') {
            list($hours, $minutes) = explode(':', $lateTime);
            $totalMinutes = (intval($hours) * 60) + intval($minutes);
            $dtr->effective_late = ($totalMinutes <= 10) ? '00:00' : $lateTime;
        } else {
            $dtr->effective_late = $lateTime;
        }

        $dtr->effective_ut = $dtr->up_ut ?? $dtr->ut ?? null;

        $overtimeValue = $dtr->up_ot ?? $dtr->overtime ?? null;
        $dtr->effective_overtime = (($dtr->ot_approval_status ?? null) === 'approved' && $overtimeValue && $overtimeValue !== '00:00') 
            ? $overtimeValue 
            : '00:00';

        $dtr->effective_total_hours_rendered = $dtr->up_total_hours_rendered ?? $dtr->total_hours_rendered ?? null;

        $remarks = $dtr->up_remarks ?? $dtr->remarks ?? '';
        
        // Identify holiday for user's region
        $userRegionId = $this->getUserRegionId($dtr);
        $holiday = Holiday::whereDate('holiday_date', $dtr->date)
            ->where(function ($query) use ($userRegionId) {
                $query->whereNull('region_id');
                if ($userRegionId) {
                    $query->orWhere('region_id', $userRegionId);
                }
            })->first();

        // Tag as holiday day for summary counting
        $dtr->is_holiday_day = !is_null($holiday);
        
        if ($holiday && !in_array($holiday->type, ['Regular', 'Special'])) {
            $dtr->effective_remarks = $holiday->description;
        } else if ($holiday) {
            // For Regular/Special, ensure the remark matches the holiday name if it was empty
            $dtr->effective_remarks = !empty($remarks) ? $remarks : $holiday->description;
        } else if (strtolower($remarks) === 'late' && $dtr->effective_late === '00:00') {
            $dtr->effective_remarks = 'Present';
        } else if (str_contains(strtolower($remarks), 'overtime') && ($dtr->ot_approval_status ?? null) !== 'approved') {
            $dtr->effective_remarks = 'Present';
        } else {
            $dtr->effective_remarks = $remarks;
        }

        $dtr->effective_updated_by = $dtr->updated_by ?? null;

        return $dtr;
    }

    protected function calculateSummary($processedDtrs)
    {
        $daysWorked = $processedDtrs->filter(function($dtr) {
            $hasTimeEntries = $dtr->effective_time_in || $dtr->effective_time_out ||
                            $dtr->effective_break_in || $dtr->effective_break_out;
            $remarksLower = strtolower($dtr->effective_remarks);
            return $hasTimeEntries && !in_array($remarksLower, ['absent', 'rest day']);
        })->count();

        $absences = $processedDtrs->filter(fn($dtr) => strtolower($dtr->effective_remarks) === 'absent')->count();
        $leaveDays = $processedDtrs->filter(fn($dtr) => str_contains(strtolower($dtr->effective_remarks), 'leave'))->count();
        
        $holidays = $processedDtrs->filter(function($dtr) {
            if ($dtr->is_holiday_day) return true;
            
            $remarksLower = strtolower($dtr->effective_remarks);
            return str_contains($remarksLower, 'holiday') ||
                   str_contains($remarksLower, 'suspension') ||
                   str_contains($remarksLower, 'half-day');
        })->count();

        $restDays = $processedDtrs->filter(fn($dtr) => strtolower($dtr->effective_remarks) === 'rest day')->count();

        $totalOvertimeMinutes = 0;
        $totalLateMinutes = 0;
        $totalUndertimeMinutes = 0;

        foreach ($processedDtrs as $dtr) {
            $hasTimeEntries = $dtr->effective_time_in || $dtr->effective_time_out ||
                            $dtr->effective_break_in || $dtr->effective_break_out;

            if ($dtr->ot_approval_status === 'approved' && !empty($dtr->effective_overtime) && $dtr->effective_overtime !== '00:00') {
                list($hours, $minutes) = explode(':', $dtr->effective_overtime);
                $totalOvertimeMinutes += (intval($hours) * 60) + intval($minutes);
            }

            if ($hasTimeEntries && !empty($dtr->effective_late) && $dtr->effective_late !== '00:00') {
                list($hours, $minutes) = explode(':', $dtr->effective_late);
                $lateMinutes = (intval($hours) * 60) + intval($minutes);
                if ($lateMinutes > 10) {
                    $totalLateMinutes += $lateMinutes;
                }
            }

            if ($hasTimeEntries && !empty($dtr->effective_ut) && $dtr->effective_ut !== '00:00') {
                list($hours, $minutes) = explode(':', $dtr->effective_ut);
                $totalUndertimeMinutes += (intval($hours) * 60) + intval($minutes);
            }
        }

        // Calculate working days: Start with MAX (22), subtract absences
        // Ensure it never goes below 0
        $calculatedWorkingDays = max(0, self::MAX_WORKING_DAYS - $absences);
        
        // If actual days worked is less than calculated, use actual days worked
        $finalWorkingDays = min($daysWorked, $calculatedWorkingDays);

        return [
            'days_worked' => $daysWorked,
            'absences' => $absences,
            'overtime' => sprintf("%02d:%02d", floor($totalOvertimeMinutes / 60), $totalOvertimeMinutes % 60),
            'late' => sprintf("%02d:%02d", floor($totalLateMinutes / 60), $totalLateMinutes % 60),
            'undertime' => sprintf("%02d:%02d", floor($totalUndertimeMinutes / 60), $totalUndertimeMinutes % 60),
            'tardiness' => sprintf("%02d:%02d", floor(($totalLateMinutes + $totalUndertimeMinutes) / 60), ($totalLateMinutes + $totalUndertimeMinutes) % 60),
            'leave_days' => $leaveDays,
            'holidays' => $holidays,
            'rest_days' => $restDays,
            'working_days' => $finalWorkingDays
        ];
    }

    /**
     * Format time to 12-hour format with AM/PM
     */
    protected function formatTimeToStandard($time)
    {
        if (empty($time) || $time === '00:00' || $time === '--:--' || $time === '-') {
            return '';
        }

        try {
            $timestamp = strtotime($time);
            if ($timestamp === false) {
                return '';
            }
            return date('g:i A', $timestamp); // 12-hour format with AM/PM
        } catch (\Exception $e) {
            return '';
        }
    }

    protected function updateProgress($progress, $status, $message)
    {
        $progress = max($progress, $this->currentProgress);
        $this->currentProgress = $progress;
        
        DtrExport::where('id', $this->exportId)->update([
            'progress' => round($progress, 2),
            'status' => $status,
            'status_message' => $message,
        ]);
        
        \Log::info('Progress updated', [
            'export_id' => $this->exportId,
            'progress' => round($progress, 2),
            'message' => $message
        ]);
    }

    public function failed(\Exception $exception)
    {
        \Log::error('DTR export job failed completely', [
            'export_id' => $this->exportId,
            'error' => $exception->getMessage()
        ]);

        DtrExport::where('id', $this->exportId)->update([
            'status' => 'failed',
            'error_message' => $exception->getMessage(),
        ]);
    }
}