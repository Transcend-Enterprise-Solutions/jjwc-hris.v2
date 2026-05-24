<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\EmployeesDtr;
use App\Models\User;
use App\Models\CaseTracking;
use App\Models\NoticeTemplates;
use App\Models\GeneratedNotice;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class GenerateAttendanceNotices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-attendance-notices';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates attendance notices for employees with AWOL or Tardiness violations';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting attendance notices generation...');
        
        // Get the previous month's data (since we're running at end of month)
        $startDate = Carbon::now()->subMonth()->startOfMonth();
        $endDate = Carbon::now()->subMonth()->endOfMonth();
        
        $this->processAbsentEmployees($startDate, $endDate);
        $this->processTardyEmployees($startDate, $endDate);
        
        $this->info('Attendance notices generation completed.');
    }
    
    protected function processAbsentEmployees($startDate, $endDate)
    {
        $this->info('Processing AWOL cases...');
        
        // REPLACE THIS PART - Get employees with Absent remarks (checking both remarks and up_remarks)
        $absentEmployees = EmployeesDtr::whereBetween('date', [$startDate, $endDate])
            ->where(function($query) {
                $query->where('up_remarks', 'Absent')
                    ->orWhere(function($subQuery) {
                        $subQuery->whereNull('up_remarks')
                                ->where('remarks', 'Absent');
                    });
            })
            ->with('user')
            ->get()
            ->groupBy('user_id');
            
        foreach ($absentEmployees as $userId => $absences) {
            $employee = $absences->first()->user;
            
            // Check if this employee already has an AWOL case for this period
            $existingCase = $this->checkExistingAttendanceCase($userId, 'AWOL', $startDate, $endDate);
            
            if (!$existingCase) {
                $this->createAttendanceCase($employee, 'AWOL', $absences);
            }
        }
    }

    protected function processTardyEmployees($startDate, $endDate)
    {
        $this->info('Processing Tardiness cases...');
        
        // REPLACE THIS PART - Get employees with Late or Late/Undertime remarks (checking both remarks and up_remarks)
        $tardyEmployees = EmployeesDtr::whereBetween('date', [$startDate, $endDate])
            ->where(function($query) {
                $query->whereIn('up_remarks', ['Late', 'Late/Undertime'])
                    ->orWhere(function($subQuery) {
                        $subQuery->whereNull('up_remarks')
                                ->whereIn('remarks', ['Late', 'Late/Undertime']);
                    });
            })
            ->with('user')
            ->get()
            ->groupBy('user_id');
            
        foreach ($tardyEmployees as $userId => $tardies) {
            // Only create case if more than 5 tardies in the month
            if ($tardies->count() > 5) {
                $employee = $tardies->first()->user;
                
                // Check if this employee already has a Tardiness case for this period
                $existingCase = $this->checkExistingAttendanceCase($userId, 'Tardiness', $startDate, $endDate);
                
                if (!$existingCase) {
                    $this->createAttendanceCase($employee, 'Tardiness', $tardies);
                }
            }
        }
    }
    
    protected function checkExistingAttendanceCase($userId, $noticeType, $startDate, $endDate)
    {
        // Check if a case was already created this month for the previous month's violations
        return CaseTracking::where('employee_id', $userId)
            ->whereHas('notice', function($query) use ($noticeType) {
                $query->where('notice_type', $noticeType);
            })
            ->whereBetween('created_at', [
                Carbon::now()->startOfMonth(), 
                Carbon::now()->endOfMonth()
            ])
            ->exists();
    }
    
    protected function createAttendanceCase($employee, $noticeType, $violations)
    {
        $this->info("Creating {$noticeType} case for employee: {$employee->name}");
        
        $templateCode = ($noticeType === 'AWOL') ? 'awol' : 'tardiness';
        $template = NoticeTemplates::where('code', $templateCode)->first();
        
        if (!$template) {
            $this->error("Template not found for code: {$templateCode}");
            return;
        }

        $supervisor = $employee->supervisor ?? User::where('user_role', 'supervisor')->first();

        $variables = [
            'employee_name' => $employee->name,
            'employee_position' => $employee->position->position ?? 'N/A',
            'employee_department' => $employee->officeDivision->office_division ?? 'N/A',
            'current_date' => now()->format('F j, Y'),
            'supervisor_name' => $supervisor->name ?? 'Supervisor Name',
            'supervisor_title' => $supervisor->position->position ?? 'Department Head',
            'violation_dates' => $violations->pluck('date')->map(function($dateString) {
                // Handle both string and Carbon date formats
                try {
                    return is_string($dateString) 
                        ? Carbon::createFromFormat('Y-m-d', $dateString)->format('F j, Y')
                        : $dateString->format('F j, Y');
                } catch (\Exception $e) {
                    return 'Unknown date';
                }
            })->implode(', '),
            'violation_count' => $violations->count(),
        ];

        // Rest of your method remains unchanged...
        $generatedContent = [];
        foreach ($template->content_blocks as $key => $block) {
            $content = $block['content'] ?? '';
            foreach ($variables as $var => $value) {
                $content = str_replace("{{$var}}", $value, $content);
            }
            $generatedContent[$key] = $content;
        }

        // Generate PDF
        $fileName = "Notice_{$noticeType}_{$employee->id}_".now()->format('YmdHis').'.pdf';
        $filePath = 'notices/'.$fileName;
        
        $pdf = Pdf::loadView('pdf.notice-pdf', [
            'template' => $template,
            'employee' => $employee,
            'content' => $generatedContent,
            'title' => $noticeType
        ]);
        
        Storage::put($filePath, $pdf->output());
        
        // Create notice record
        $notice = GeneratedNotice::create([
            'employee_id' => $employee->id,
            'template_id' => $template->id,
            'notice_type' => $noticeType,
            'content' => $generatedContent,
            'generated_by' => 1, // System user
            'status' => 'generated',
            'file_name' => $fileName,
            'file_path' => $filePath
        ]);
        
        // Create case tracking record
        $case = CaseTracking::create([
            'user_id' => 1, // System user
            'employee_id' => $employee->id,
            'notice_id' => $notice->id,
            'issued_date' => now(),
            'status' => 'N/A',
            'remarks' => 'For Evaluation of HRD'
        ]);
        
        $this->info("Created {$noticeType} case for {$employee->name} with ID: {$case->id}");
    }
}