<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\GeneratedDocument;
use App\Models\GeneratedNotice;
use App\Models\CaseTracking;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Storage;
use App\Models\NoticeTemplates;
use App\Models\CorrectiveAction;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

#[Layout('layouts.app')]
#[Title('Administrative Cases')]
class AdministrativeCases extends Component
{
    use WithPagination;

    public $showCaseModal = false;
    public $employeeId;
    public $documentId;
    public $showDeleteCaseModal = false;
    public $caseIdToDelete;
    public $caseSearch = '';
    public $casePageSize = 5;
    public $casePageSizes = [5, 10, 20, 30, 50, 100];

    public $editingCaseId;
    public $editStatus;
    public $editRemarks;
    public $editAdminCaseNumber;
    public $showAdminCaseNumberField = false;

    public $showCorrectiveActionFields = false;
    public $actionTaken = '';
    public $dateIssued = '';
    public $dateReceivedOffice = '';
    public $dateReceivedEmployee = '';

    public $actionOptions = [
        'written_reprimand' => 'Written Reprimand',
        'suspension' => 'Notice of Suspension',
        'hearing' => 'Notice of Hearing/Conference',
        'no_violation' => 'Notice of No Violation',
        'stern_warning' => 'Stern Warning',
        'preventive_suspension' => 'Preventive Suspension'
    ];

    private function checkAndUpdateAttendanceCases()
    {
        // Get all attendance cases that need automatic status update
        $attendanceCases = CaseTracking::with(['notice'])
            ->whereHas('notice', function ($query) {
                $query->whereIn('notice_type', ['AWOL', 'Tardiness']);
            })
            ->where(function ($query) {
                $query->where('remarks', 'For Evaluation of HRD')
                      ->orWhere('remarks', null)
                      ->orWhere('remarks', '');
            })
            ->where('deadline_for_explanation', '<=', Carbon::now()->toDateString())
            ->get();

        foreach ($attendanceCases as $case) {
            $this->updateAttendanceCaseStatus($case);
        }
    }

    private function updateAttendanceCaseStatus($case)
    {
        $currentDate = Carbon::now();
        $deadlineDate = Carbon::parse($case->deadline_for_explanation);

        // Check if deadline has passed
        if ($currentDate->greaterThan($deadlineDate)) {
            // Check if there's a submitted explanation file
            if (!empty($case->answered_file_path) && Storage::exists($case->answered_file_path)) {
                // File exists, set to "For Evaluation of HRD"
                $case->update([
                    'remarks' => 'For Evaluation of HRD',
                    'explanation_submitted_date' => $case->answered_date ?? $currentDate->toDateString(),
                    'status_date' => $currentDate
                ]);
            } else {
                // No file submitted, set to "No Written Explanation Submitted"
                $case->update([
                    'remarks' => 'No Written Explanation Submitted',
                    'status_date' => $currentDate
                ]);
            }
        }
    }

    private function setDeadlineForExplanation($caseId, $issuedDate)
    {
        $case = CaseTracking::find($caseId);
        if ($case) {
            $deadline = Carbon::parse($issuedDate)->addBusinessDays(5);
            $case->update([
                'deadline_for_explanation' => $deadline->toDateString()
            ]);
        }
    }

    protected function getTemplateContent($action)
    {
        $template = NoticeTemplates::where('code', $action)->first();
        return $template ? $template->content : '';
    }

    public function render()
    {
        // Check and update attendance cases before rendering
        $this->checkAndUpdateAttendanceCases();

        $casesQuery = CaseTracking::with(['employee', 'document', 'notice'])
            ->when($this->caseSearch, function ($query) {
                $query->whereHas('employee', function ($q) {
                    $q->where('name', 'like', '%' . $this->caseSearch . '%');
                })
                ->orWhereHas('document', function ($q) {
                    $q->where('document_type', 'like', '%' . $this->caseSearch . '%')
                    ->orWhere('file_name', 'like', '%' . $this->caseSearch . '%');
                })
                ->orWhereHas('notice', function ($q) {
                    $q->where('notice_type', 'like', '%' . $this->caseSearch . '%');
                })
                ->orWhere('status', 'like', '%' . $this->caseSearch . '%');
            })
            ->latest();

        return view('livewire.admin.administrative-cases', [
            'cases' => $casesQuery->paginate($this->casePageSize, ['*'], 'casePage'),
            'employees' => User::where('user_role', 'emp')->get(),
            'documents' => GeneratedDocument::orderBy('created_at', 'desc')->get(),
            'notices' => GeneratedNotice::with('employee')->orderBy('created_at', 'desc')->get(),
        ]);
    }

    public function openCaseModal()
    {
        $this->reset(['employeeId', 'documentId']);
        $this->showCaseModal = true;
    }

    public function createCase()
    {
        $this->validate([
            'employeeId' => 'required|exists:users,id',
            'documentId' => 'required',
        ]);

        try {
            [$type, $id] = explode('_', $this->documentId);

            $issuedDate = now();
            
            $case = CaseTracking::create([
                'user_id' => auth()->id(),
                'employee_id' => $this->employeeId,
                'document_id' => $type === 'doc' ? $id : null,
                'notice_id' => $type === 'notice' ? $id : null,
                'issued_date' => $issuedDate,
                'status' => 'For Submission of Written Explanation',
                'remarks' => 'For Evaluation of HRD' // Default for attendance cases
            ]);

            // Check if this is an attendance case and set deadline
            if ($type === 'notice') {
                $notice = GeneratedNotice::find($id);
                if ($notice && in_array($notice->notice_type, ['AWOL', 'Tardiness'])) {
                    $this->setDeadlineForExplanation($case->id, $issuedDate);
                }
            }

            $this->showCaseModal = false;
            $this->dispatch('swal', [
                'title' => "Case Issued Successfully",
                'icon' => 'success'
            ]);

            $this->reset(['employeeId', 'documentId']);

        } catch (\Exception $e) {
            logger()->error('Case creation failed: ' . $e->getMessage());
            $this->dispatch('swal', [
                'title' => "Error Creating Case",
                'text' => $e->getMessage(),
                'icon' => 'error'
            ]);
        }
    }

    public function confirmDeleteCase($caseId)
    {
        $this->caseIdToDelete = $caseId;
        $this->showDeleteCaseModal = true;
    }

    public function deleteCase()
    {
        $case = CaseTracking::findOrFail($this->caseIdToDelete);
        $case->delete();
        
        $this->showDeleteCaseModal = false;
        $this->dispatch('swal', [
            'title' => "Case Deleted",
            'icon' => 'success'
        ]);
    }

    public function editCase($caseId)
    {
        // Check and update attendance cases before editing
        $this->checkAndUpdateAttendanceCases();
        
        $case = CaseTracking::with(['notice', 'correctiveActions'])->findOrFail($caseId);
        $this->editingCaseId = $case->id;
        $this->editStatus = $case->status;
        $this->editAdminCaseNumber = $case->admin_case_number;
        $this->showAdminCaseNumberField = false;
        
        // For attendance cases
        if ($case->notice && in_array($case->notice->notice_type, ['AWOL', 'Tardiness'])) {
            $this->showCorrectiveActionFields = true;
            $correctiveAction = $case->correctiveActions->first();
            
            if ($correctiveAction) {
                $this->actionTaken = $correctiveAction->action_taken;
                $this->dateIssued = $correctiveAction->date_issued?->format('Y-m-d');
                $this->dateReceivedOffice = $correctiveAction->date_received_office?->format('Y-m-d');
                $this->dateReceivedEmployee = $correctiveAction->date_received_employee?->format('Y-m-d');
            }
            
            // Use the current remarks (which may have been auto-updated)
            $this->editRemarks = $case->remarks ?: 'For Evaluation of HRD';
        } else {
            $this->showCorrectiveActionFields = false;
            $this->editRemarks = '';
        }
    }

    public function generateNotice()
    {
        $this->validate([
            'actionTaken' => 'required',
            'dateIssued' => 'required|date',
        ]);

        $case = CaseTracking::with('employee')->findOrFail($this->editingCaseId);
        $employee = $case->employee;
        $supervisor = $employee->supervisor ?? User::find($employee->supervisor_id);

        // Get the template - content_blocks is already an array
        $template = NoticeTemplates::where('code', $this->actionTaken)->firstOrFail();
        
        // No need to decode since it's already an array
        $contentBlocks = $template->content_blocks;

        // Prepare variables for replacement
        $variables = [
            'employee_name' => $employee->name,
            'employee_position' => $employee->position->position ?? 'N/A',
            'employee_department' => $employee->officeDivision->office_division ?? 'N/A',
            'current_date' => now()->format('F j, Y'),
            'supervisor_name' => $supervisor->name ?? 'Supervisor Name',
            'supervisor_title' => $supervisor->position->position ?? 'Department Head',
            'action_taken' => $this->actionOptions[$this->actionTaken],
            'date_issued' => \Carbon\Carbon::parse($this->dateIssued)->format('F j, Y')
        ];

        // Process content blocks
        $generatedContent = [];
        foreach ($contentBlocks as $key => $block) {
            $content = $block['content'] ?? '';
            foreach ($variables as $var => $value) {
                $content = str_replace("{{$var}}", $value, $content);
            }
            $generatedContent[$key] = $content;
        }

        // Generate PDF
        $fileName = "Notice_{$this->actionTaken}_{$employee->id}_".now()->format('YmdHis').'.pdf';
        $filePath = 'notices/'.$fileName;

        $pdf = Pdf::loadView('pdf.notice-pdf', [
            'template' => $template,
            'employee' => $employee,
            'content' => $generatedContent,
            'title' => $this->actionOptions[$this->actionTaken]
        ]);

        Storage::put($filePath, $pdf->output());

        // Create notice record
        $notice = GeneratedNotice::create([
            'employee_id' => $employee->id,
            'template_id' => $template->id,
            'notice_type' => $this->actionOptions[$this->actionTaken],
            'content' => $generatedContent,
            'generated_by' => auth()->id(),
            'status' => 'generated',
            'file_name' => $fileName,
            'file_path' => $filePath
        ]);

        // Update corrective action with PDF reference
        $correctiveActionData = [
            'action_taken' => $this->actionTaken,
            'date_issued' => $this->dateIssued,
            'date_received_office' => $this->dateReceivedOffice,
            'date_received_employee' => $this->dateReceivedEmployee,
            'notice_id' => $notice->id,
            'pdf_path' => $filePath
        ];

        if ($case->correctiveActions->count() > 0) {
            $case->correctiveActions()->first()->update($correctiveActionData);
        } else {
            $case->correctiveActions()->create($correctiveActionData);
        }

        $this->dispatch('swal', [
            'title' => "Notice Generated",
            'text' => "The {$this->actionOptions[$this->actionTaken]} has been generated successfully.",
            'icon' => 'success'
        ]);

        $this->resetEditFields();
    }

    public function updatedEditStatus($value)
    {
        $case = CaseTracking::find($this->editingCaseId);
        if ($case && $case->status === 'Endorsed to AdminCom' && $value === 'For Preliminary Conference') {
            $this->showAdminCaseNumberField = true;
        } else {
            $this->showAdminCaseNumberField = false;
        }
    }

    public function getStatusOptions($currentStatus, $caseType = null)
    {
        // For attendance cases with corrective action, show standard status flow
        if (in_array($caseType, ['AWOL', 'Tardiness']) && $currentStatus !== 'N/A') {
            $statusFlow = [
                'For Submission of Written Explanation' => [
                    'For Endorsement to AdminCom',
                    'For Evaluation of HRD',
                    'Endorsed to AdminCom'
                ],
                'For Endorsement to AdminCom' => [
                    'For Evaluation of HRD',
                    'Endorsed to AdminCom'
                ],
                'For Evaluation of HRD' => [
                    'Endorsed to AdminCom'
                ],
                'Endorsed to AdminCom' => [
                    'For Preliminary Conference',
                    'For Administrative Hearing',
                    'For Deliberation',
                    'For Approval of the PCEO',
                    'Resolved'
                ],
            ];
            
            return $statusFlow[$currentStatus] ?? [];
        }

        if (in_array($caseType, ['AWOL', 'Tardiness'])) {
            return [
                'For Evaluation of HRD',
                'Written Explanation Submitted',
                'No Written Explanation Submitted'
            ];
        }

        $statusFlow = [
            'For Submission of Written Explanation' => [
                'For Endorsement to AdminCom',
                'For Evaluation of HRD',
                'Endorsed to AdminCom'
            ],
            'For Endorsement to AdminCom' => [
                'For Evaluation of HRD',
                'Endorsed to AdminCom'
            ],
            'For Evaluation of HRD' => [
                'Endorsed to AdminCom'
            ],
            'Endorsed to AdminCom' => [
                'For Preliminary Conference',
                'For Administrative Hearing',
                'For Deliberation',
                'For Approval of the PCEO',
                'Resolved'
            ],
            'For Preliminary Conference' => [
                'For Administrative Hearing',
                'For Deliberation',
                'For Approval of the PCEO',
                'Resolved'
            ],
            'For Administrative Hearing' => [
                'For Deliberation',
                'For Approval of the PCEO',
                'Resolved'
            ],
            'For Deliberation' => [
                'For Approval of the PCEO',
                'Resolved'
            ],
            'For Approval of the PCEO' => [
                'Resolved'
            ]
        ];

        return $statusFlow[$currentStatus] ?? [];
    }

    public function updateCase()
    {
        $case = CaseTracking::with('notice')->findOrFail($this->editingCaseId);
        
        // For attendance cases
        if ($case->notice && in_array($case->notice->notice_type, ['AWOL', 'Tardiness'])) {
            // Check if we're manually overriding the automatic status
            if ($this->editRemarks) {
                $validRemarks = ['For Evaluation of HRD', 'Written Explanation Submitted', 'No Written Explanation Submitted'];
                
                if (!in_array($this->editRemarks, $validRemarks)) {
                    $this->dispatch('swal', [
                        'title' => "Invalid Remark",
                        'text' => "Please select a valid remark",
                        'icon' => 'error'
                    ]);
                    return;
                }
                
                $case->remarks = $this->editRemarks;
                
                // If manually setting to "Written Explanation Submitted", update the submission date
                if ($this->editRemarks === 'Written Explanation Submitted') {
                    $case->explanation_submitted_date = Carbon::now()->toDateString();
                }
            }

            // Handle status update if it was changed
            if ($this->editStatus && $this->editStatus !== $case->status) {
                $validOptions = $this->getStatusOptions($case->status, $case->notice->notice_type);
                
                if (!in_array($this->editStatus, $validOptions)) {
                    $this->dispatch('swal', [
                        'title' => "Invalid Status Transition",
                        'text' => "Cannot change status from {$case->status} to {$this->editStatus}",
                        'icon' => 'error'
                    ]);
                    return;
                }
                
                $case->status = $this->editStatus;
            }

            // Handle corrective actions
            if ($this->actionTaken) {
                $correctiveActionData = [
                    'action_taken' => $this->actionTaken,
                    'date_issued' => $this->dateIssued,
                    'date_received_office' => $this->dateReceivedOffice,
                    'date_received_employee' => $this->dateReceivedEmployee,
                ];

                if ($case->correctiveActions->count() > 0) {
                    $case->correctiveActions()->first()->update($correctiveActionData);
                } else {
                    // Only set initial status when first creating corrective action
                    if ($case->status === 'For Evaluation of HRD') {
                        $case->status = 'For Submission of Written Explanation';
                    }
                    $case->correctiveActions()->create($correctiveActionData);
                }
            }
            
            $case->status_date = now();
            $case->save();
            
        } else {
            // Original non-attendance case handling
            $validOptions = $this->getStatusOptions($case->status, $case->notice?->notice_type);
            
            if (!in_array($this->editStatus, $validOptions)) {
                $this->dispatch('swal', [
                    'title' => "Invalid Status Transition",
                    'text' => "Cannot change status from {$case->status} to {$this->editStatus}",
                    'icon' => 'error'
                ]);
                return;
            }

            $case->update([
                'status' => $this->editStatus,
                'status_date' => now(),
            ]);
        }

        $this->dispatch('swal', [
            'title' => "Case Updated",
            'icon' => 'success'
        ]);
        
        $this->resetEditFields();
    }

    public function resetEditFields()
    {
        $this->reset([
            'editingCaseId',
            'editStatus',
            'editAdminCaseNumber',
            'showAdminCaseNumberField',
            'showCorrectiveActionFields',
            'actionTaken',
            'dateIssued',
            'dateReceivedOffice',
            'dateReceivedEmployee'
        ]);
    }

    public function downloadDocument($documentId)
    {
        $document = GeneratedDocument::findOrFail($documentId);
        
        if (!Storage::exists($document->file_path)) {
            $this->dispatch('swal', [
                'title' => "File Not Found",
                'icon' => 'error'
            ]);
            return;
        }

        return Storage::download($document->file_path, $document->file_name);
    }

    public function downloadNotice($noticeId)
    {
        $notice = GeneratedNotice::findOrFail($noticeId);
        
        if (!Storage::exists($notice->file_path)) {
            $this->dispatch('swal', [
                'title' => "File Not Found",
                'icon' => 'error'
            ]);
            return;
        }

        return Storage::download($notice->file_path, $notice->file_name);
    }

    public function resetVariables()
    {
        $this->reset([
            'showCaseModal',
            'employeeId',
            'documentId',
            'showDeleteCaseModal',
            'caseIdToDelete',
            'caseSearch',
            'casePageSize',
            'editingCaseId',
            'editStatus',
            'editAdminCaseNumber',
            'showAdminCaseNumberField',
            'showCorrectiveActionFields',
            'actionTaken',
            'dateIssued',
            'dateReceivedOffice',
            'dateReceivedEmployee'
        ]);
    }

    public function refreshAttendanceCases()
    {
        $this->checkAndUpdateAttendanceCases();
        
        $this->dispatch('swal', [
            'title' => "Cases Updated",
            'text' => "Attendance cases have been checked and updated automatically.",
            'icon' => 'success'
        ]);
    }
}