<?php

namespace App\Livewire\Admin;

use App\Mail\LeaveStatusMail;
use App\Models\LeaveApplication;
use App\Models\LeaveApprovals;
use App\Models\LeaveApprover;
use App\Models\LeaveCredits;
use App\Models\LeaveCreditsCalculation;
use App\Models\SickLeaveDetails;
use App\Models\User;
use App\Models\VacationLeaveDetails;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Leave Request')]
class AdminLeaveRequest extends Component
{
    use WithPagination;

    public $currentView = 'default';

    public $showApproveModal = false;

    public $showDisapproveModal = false;

    public $selectedApplication;

    public $status;

    public $otherReason;

    public $days;

    public $disapproveReason;

    public $balance;

    public $listOfDates = [];

    public $selectedDates = [];

    public $nonEmployeeUsers = [];

    public $search;

    public $leaveApplicationDetails;

    public $pdfContent;

    public $showPDFPreview = false;

    public $pageSize = 5;

    public $pageSizes = [5, 10, 20, 30, 50, 100];

    protected $rules = [
        'status' => 'required_if:showApproveModal,true',
        'otherReason' => 'required_if:status,Other|string',
        'days' => 'required_if:status,With Pay,Without Pay|numeric|min:1',
        'disapproveReason' => 'required_if:showDisapproveModal,true',
    ];

    public function mount()
    {
        $this->checkApproverStatus();
    }

    private function checkApproverStatus()
    {
        $userId = Auth::id();

        // Check if user is directly an approver or an OIC
        $this->isApprover = LeaveApprover::where(function ($query) use ($userId) {
            $query->where('user_id', $userId)
                ->orWhere('oic_user_id', $userId);
        })
            ->where('is_active', true)
            ->exists();
    }

    public function getCurrentUserApproverLevel()
    {
        $currentUser = Auth::user();

        // Check if user is directly assigned as an approver
        $directApprover = LeaveApprover::where('user_id', $currentUser->id)
            ->where('is_active', true)
            ->first();

        if ($directApprover) {
            return $directApprover->approver_level;
        }

        // Check if user is OIC for any approver
        $oicApprover = LeaveApprover::where('oic_user_id', $currentUser->id)
            ->where('is_active', true)
            ->first();

        if ($oicApprover) {
            return $oicApprover->approver_level;
        }

        return null;
    }

    public function getApproverIdForCurrentUser()
    {
        $currentUser = Auth::user();

        // Check if user is directly assigned as an approver
        $directApprover = LeaveApprover::where('user_id', $currentUser->id)
            ->where('is_active', true)
            ->first();

        if ($directApprover) {
            return $directApprover->user_id;
        }

        // Check if user is OIC - return the PRIMARY approver's ID
        $oicApprover = LeaveApprover::where('oic_user_id', $currentUser->id)
            ->where('is_active', true)
            ->first();

        if ($oicApprover) {
            return $oicApprover->user_id; // Return primary approver's ID
        }

        return null;
    }

    public function toggleView()
    {
        $this->currentView = $this->currentView === 'default' ? 'alternate' : 'default';
    }

    public function openApproveModal($applicationId)
    {
        $this->selectedApplication = LeaveApplication::find($applicationId);
        $this->listOfDates = explode(',', $this->selectedApplication->list_of_dates);
        $this->selectedDates = [];
        $this->days = 0;
        $this->status = '';
        $this->otherReason = '';
        $this->showApproveModal = true;
    }

    public function openDisapproveModal($applicationId)
    {
        $this->selectedApplication = LeaveApplication::find($applicationId);
        $this->reset(['disapproveReason']);
        $this->showDisapproveModal = true;
    }

    public function closeApproveModal()
    {
        $this->showApproveModal = false;
        $this->resetVariables();
    }

    public function closeDisapproveModal()
    {
        $this->showDisapproveModal = false;
        $this->resetVariables();
    }

    public function calculateWeekdaysInRange($startDate, $endDate)
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        $weekdays = 0;

        for ($date = $start; $date->lte($end); $date->addDay()) {
            if (! $date->isWeekend()) {
                $weekdays++;
            }
        }

        return $weekdays;
    }

    public function updatedSelectedDates($value)
    {
        $totalDays = 0;

        foreach ($this->selectedDates as $date) {
            if (strpos($date, ' - ') !== false) {
                [$startDate, $endDate] = explode(' - ', $date);
                $totalDays += $this->calculateWeekdaysInRange($startDate, $endDate);
            } else {
                $carbonDate = Carbon::parse($date);
                if (! $carbonDate->isWeekend()) {
                    $totalDays++;
                }
            }
        }

        $this->days = $totalDays;
    }

    public function approveLeave()
    {
        $this->validate([
            'status' => 'required',
            'days' => 'required|numeric|min:1',
        ]);

        if ($this->selectedApplication) {
            $currentUser = Auth::user();
            $approverId = $this->getApproverIdForCurrentUser();
            $approval = LeaveApprovals::where('application_id', $this->selectedApplication->id)->first();

            if (! $approval) {
                $this->dispatch('swal', [
                    'title' => 'Approval record not found.',
                    'icon' => 'error',
                ]);

                return;
            }

            // Check if current user is authorized to approve at this stage
            $currentStage = $approval->stage;
            $currentUserLevel = $this->getCurrentUserApproverLevel();

            if ($currentStage === 1 && $currentUserLevel === 'first' && $approval->first_approver === $approverId) {
                // First approver approving
                $approval->stage = 2; // Move to second approver
            } elseif ($currentStage === 2 && $currentUserLevel === 'second' && $approval->second_approver === $approverId) {
                // Second approver approving
                $approval->stage = 3; // Move to third approver
            } elseif ($currentStage === 3 && $currentUserLevel === 'third' && $approval->third_approver === $approverId) {
                // Third approver approving - final approval
                $approval->stage = 4; // Completed

                // Update the main application status
                if ($this->status === 'Other') {
                    $this->validate(['otherReason' => 'required|string']);
                    $this->selectedApplication->status = 'Approved';
                    $this->selectedApplication->remarks = $this->otherReason;
                    $this->selectedApplication->approved_days = 0;
                } else {
                    $this->selectedApplication->status = 'Approved';
                    $this->selectedApplication->approved_days = $this->days;
                    $this->selectedApplication->remarks = $this->status;

                    // if ($this->status === 'With Pay') {
                    //     if (!$this->checkLeaveCredits($this->days)) {
                    //         return;
                    //     }
                    //     $this->updateLeaveDetails($this->days, $this->status);
                    // }

                    $allApprovedDates = [];
                    foreach ($this->selectedDates as $date) {
                        if (strpos($date, ' - ') !== false) {
                            $allApprovedDates[] = $date;
                        } else {
                            $allApprovedDates[] = $date;
                        }
                    }
                    $this->selectedApplication->approved_dates = implode(',', $allApprovedDates);
                }

                $this->selectedApplication->save();

                try {
                    Mail::to($this->selectedApplication->user->email)->send(
                        new LeaveStatusMail($this->selectedApplication, 'approved', $this->selectedApplication->user->name)
                    );
                } catch (\Exception $e) {
                    Log::error('Failed to send leave approval email: '.$e->getMessage());
                }
            } else {
                $this->dispatch('swal', [
                    'title' => 'You are not authorized to approve this application at this stage.',
                    'icon' => 'error',
                ]);

                $this->closeApproveModal();

                return;
            }

            $approval->save();

            $this->dispatch('swal', [
                'title' => 'Leave application approved successfully!',
                'icon' => 'success',
            ]);

            $this->closeApproveModal();
        }
    }

    public function disapproveLeave()
    {
        $this->validate([
            'disapproveReason' => 'required',
        ]);

        if ($this->selectedApplication) {
            $currentUser = Auth::user();
            $approverId = $this->getApproverIdForCurrentUser(); // Get primary approver ID (works for both direct approvers and OICs)
            $approval = LeaveApprovals::where('application_id', $this->selectedApplication->id)->first();

            if (! $approval) {
                $this->dispatch('swal', [
                    'title' => 'Approval record not found.',
                    'icon' => 'error',
                ]);

                return;
            }

            // Check if current user is authorized to disapprove
            $currentStage = $approval->stage;
            $currentUserLevel = $this->getCurrentUserApproverLevel();

            $authorized = false;
            if ($currentStage === 1 && $currentUserLevel === 'first' && $approval->first_approver === $approverId) {
                $authorized = true;
            } elseif ($currentStage === 2 && $currentUserLevel === 'second' && $approval->second_approver === $approverId) {
                $authorized = true;
            } elseif ($currentStage === 3 && $currentUserLevel === 'third' && $approval->third_approver === $approverId) {
                $authorized = true;
            }

            if (! $authorized) {
                $this->dispatch('swal', [
                    'title' => 'You are not authorized to disapprove this application.',
                    'icon' => 'error',
                ]);

                return;
            }

            $this->selectedApplication->status = 'Disapproved';
            $this->selectedApplication->remarks = $this->disapproveReason;
            $this->selectedApplication->approved_days = 0;
            $this->selectedApplication->save();

            // Reset the approval stage
            $approval->stage = 0;
            $approval->save();

            try {
                Mail::to($this->selectedApplication->user->email)->send(
                    new LeaveStatusMail($this->selectedApplication, 'disapproved', $this->selectedApplication->user->name)
                );
            } catch (\Exception $e) {
                Log::error('Failed to send leave disapproval email: '.$e->getMessage());
            }

            $this->dispatch('swal', [
                'title' => "Reason: {$this->disapproveReason}",
                'icon' => 'success',
            ]);

            $this->closeDisapproveModal();
        }
    }

    protected function checkLeaveCredits($days)
    {
        $user_id = $this->selectedApplication->user_id;
        $leaveCredits = LeaveCredits::where('user_id', $user_id)->first();

        if (! $leaveCredits) {
            $this->addError('days', 'Leave credits not found for this user.');

            return false;
        }

        $leaveTypes = explode(',', $this->selectedApplication->type_of_leave);
        foreach ($leaveTypes as $leaveType) {
            $leaveType = trim($leaveType);

            // Check for Mandatory/Forced Leave
            if ($leaveType === 'Mandatory/Forced Leave') {
                if ($leaveCredits->fl_claimable_credits < $days || $leaveCredits->vl_claimable_credits < $days) {
                    $this->addError('days', 'Insufficient Forced Leave Credits. Available FL: '.number_format($leaveCredits->fl_claimable_credits ?? 0.000, 3));

                    return false;
                }
            }

            // Check individual leave types
            elseif ($leaveType === 'Vacation Leave') {
                if ($leaveCredits->vl_claimable_credits < $days) {
                    $this->addError('days', 'Insufficient Vacation Leave Credits. Available VL: '.number_format($leaveCredits->vl_claimable_credits ?? 0.000, 3));

                    return false;
                }
            } elseif ($leaveType === 'Sick Leave') {
                if ($leaveCredits->sl_claimable_credits < $days) {
                    $this->addError('days', 'Insufficient Sick Leave Credits. Available SL: '.number_format($leaveCredits->sl_claimable_credits ?? 0.000, 3));

                    return false;
                }
            } elseif ($leaveType === 'Special Privilege Leave') {
                if ($leaveCredits->spl_claimable_credits < $days) {
                    $this->addError('days', 'Insufficient Special Privilege Leave Credits. Available SPL: '.number_format($leaveCredits->spl_claimable_credits ?? 0.000, 3));

                    return false;
                }
            }
        }

        return true;
    }

    // protected function updateLeaveDetails($days, $status)
    // {
    //     $user_id = $this->selectedApplication->user_id;
    //     $leaveCredits = LeaveCredits::where('user_id', $user_id)->first();

    //     if (!$leaveCredits) {
    //         $this->addError('days', "Leave credits not found for this user.");
    //         return;
    //     }

    //     $leaveTypes = explode(',', $this->selectedApplication->type_of_leave);
    //     $updatedLeaveTypes = [];

    //     foreach ($leaveTypes as $leaveType) {
    //         $leaveType = trim($leaveType);
    //         $originalLeaveType = $leaveType;

    //         if ($leaveType === "Mandatory/Forced Leave") {
    //             // For Mandatory Leave, deduct from both FL and VL
    //             if ($leaveCredits->fl_claimable_credits >= $days && $leaveCredits->vl_claimable_credits >= $days) {
    //                 // Deduct from FL
    //                 $leaveCredits->fl_claimable_credits -= $days;
    //                 $leaveCredits->fl_claimed_credits += $days;

    //                 // Deduct from VL
    //                 $leaveCredits->vl_claimable_credits -= $days;
    //                 $leaveCredits->vl_claimed_credits += $days;
    //             } else {
    //                 $this->addError('days', "Insufficient Mandatory/Forced Leave credits. Available FL: {$leaveCredits->fl_claimable_credits}, VL: {$leaveCredits->vl_claimable_credits}");
    //                 return;
    //             }
    //         }
    //         else if ($leaveType === "Vacation Leave") {
    //             // Directly check and deduct from VL credits
    //             if ($leaveCredits->vl_claimable_credits >= $days) {
    //                 $leaveCredits->vl_claimable_credits -= $days;
    //                 $leaveCredits->vl_claimed_credits += $days;
    //             } else {
    //                 $this->addError('days', "Insufficient Vacation Leave credits. Available VL: {$leaveCredits->vl_claimable_credits}");
    //                 return;
    //             }
    //         }
    //         else if ($leaveType === "Sick Leave") {
    //             // Directly check and deduct from SL credits
    //             if ($leaveCredits->sl_claimable_credits >= $days) {
    //                 $leaveCredits->sl_claimable_credits -= $days;
    //                 $leaveCredits->sl_claimed_credits += $days;
    //             } else {
    //                 $this->addError('days', "Insufficient Sick Leave credits. Available SL: {$leaveCredits->sl_claimable_credits}");
    //                 return;
    //             }
    //         }
    //         else if ($leaveType === "Special Privilege Leave") {
    //             if ($leaveCredits->spl_claimable_credits >= $days) {
    //                 $leaveCredits->spl_claimable_credits -= $days;
    //                 $leaveCredits->spl_claimed_credits += $days;
    //             } else {
    //                 $this->addError('days', "Insufficient Special Privilege Leave credits. Available SPL: {$leaveCredits->spl_claimable_credits}");
    //                 return;
    //             }
    //         }

    //         $updatedLeaveTypes[] = $leaveType;
    //     }

    //     $leaveCredits->save();

    //     // Updating LeaveCreditsCalculation
    //     $month = date('m', strtotime($this->selectedApplication->start_date));
    //     $year = date('Y', strtotime($this->selectedApplication->start_date));

    //     $leaveCreditsCalculation = LeaveCreditsCalculation::where('user_id', $user_id)
    //         ->where('month', $month)
    //         ->where('year', $year)
    //         ->first();

    //     if ($leaveCreditsCalculation) {
    //         $leaveCreditsCalculation->leave_credits_earned -= $days;
    //         $leaveCreditsCalculation->save();
    //     }

    //     $this->selectedApplication->type_of_leave = implode(',', $updatedLeaveTypes);
    //     $this->selectedApplication->save();
    // }

    public function fetchNonEmployeeUsers()
    {
        $this->nonEmployeeUsers = User::where('user_role', '!=', 'emp')
            ->where('user_role', '!=', 'hr')
            ->where('user_role', '!=', 'sa')
            ->get();
    }

    public function render()
    {
        $currentUser = Auth::user();
        $currentUserLevel = $this->getCurrentUserApproverLevel();
        $approverId = $this->getApproverIdForCurrentUser();

        $query = LeaveApplication::query()
            ->leftJoin('users', 'users.id', 'leave_application.user_id')
            ->leftJoin('leave_approvals', 'leave_approvals.application_id', 'leave_application.id')
            ->orderBy('leave_application.created_at', 'desc')
            ->whereHas('user', function ($query) {
                $query->where('users.name', 'like', '%'.$this->search.'%');
            })
            ->select(
                'leave_application.*',
                'users.profile_photo_path',
                'leave_approvals.stage',
                'leave_approvals.first_approver',
                'leave_approvals.second_approver',
                'leave_approvals.third_approver'
            );

        // Filter based on user role and approver level
        if (in_array($currentUser->user_role, ['hr', 'sa'])) {
            // HR/SA can see all pending applications
            $query->where('leave_application.status', 'Pending');
        } elseif ($currentUserLevel && $approverId) {
            // Approvers and OICs can only see applications at their stage
            // OICs will use the primary approver's ID for filtering
            $query->where('leave_application.status', 'Pending')
                ->where('leave_approvals.stage', $this->getStageForApproverLevel($currentUserLevel))
                ->where(function ($q) use ($approverId, $currentUserLevel) {
                    if ($currentUserLevel === 'first') {
                        $q->where('leave_approvals.first_approver', $approverId);
                    } elseif ($currentUserLevel === 'second') {
                        $q->where('leave_approvals.second_approver', $approverId);
                    } elseif ($currentUserLevel === 'third') {
                        $q->where('leave_approvals.third_approver', $approverId);
                    }
                });
        } else {
            // Non-approvers see nothing
            $query->whereRaw('1=0');
        }

        $leaveApplications = $query->paginate($this->pageSize)
            ->through(function ($leaveApplication) use ($currentUserLevel) {
                $leaveApplication->actionsVisible = $leaveApplication->status === 'Pending' &&
                    $this->canUserApprove($leaveApplication, $currentUserLevel);

                return $leaveApplication;
            });

        return view('livewire.admin.admin-leave-request', [
            'leaveApplications' => $leaveApplications,
            'vacationLeaveDetails' => VacationLeaveDetails::orderBy('created_at', 'desc')->paginate(10),
            'sickLeaveDetails' => SickLeaveDetails::orderBy('created_at', 'desc')->paginate(10),
            'currentUserLevel' => $currentUserLevel,
        ]);
    }

    private function getStageForApproverLevel($level)
    {
        switch ($level) {
            case 'first': return 1;
            case 'second': return 2;
            case 'third': return 3;
            default: return 0;
        }
    }

    private function canUserApprove($leaveApplication, $userLevel)
    {
        if (in_array(Auth::user()->user_role, ['hr', 'sa'])) {
            return true;
        }

        if (! $userLevel) {
            return false;
        }

        $approverId = $this->getApproverIdForCurrentUser();
        $currentStage = $leaveApplication->stage;

        // Check if at correct stage
        if ($currentStage !== $this->getStageForApproverLevel($userLevel)) {
            return false;
        }

        // Check if assigned to this approver (works for both direct approvers and OICs)
        if ($userLevel === 'first' && $leaveApplication->first_approver == $approverId) {
            return true;
        } elseif ($userLevel === 'second' && $leaveApplication->second_approver == $approverId) {
            return true;
        } elseif ($userLevel === 'third' && $leaveApplication->third_approver == $approverId) {
            return true;
        }

        return false;
    }

    public function resetVariables()
    {
        $this->status = null;
        $this->otherReason = null;
        $this->days = null;
        $this->listOfDates = [];
        $this->disapproveReason = null;
    }

    public function closeLeaveDetails()
    {
        $this->showPDFPreview = false;
        $this->pdfContent = null;
    }

    public function showPDF($leaveApplicationId)
    {
        $leaveApplication = LeaveApplication::with('user.userData')->findOrFail($leaveApplicationId);

        $selectedLeaveTypes = $leaveApplication->type_of_leave ? explode(',', $leaveApplication->type_of_leave) : [];

        $otherLeave = '';
        foreach ($selectedLeaveTypes as $leaveType) {
            if (strpos($leaveType, 'Others: ') === 0) {
                $otherLeave = str_replace('Others: ', '', $leaveType);
                break;
            }
        }

        $detailsOfLeave = $leaveApplication->details_of_leave ? array_map('trim', explode(',', $leaveApplication->details_of_leave)) : [];

        $isDetailPresent = function ($detail) use ($detailsOfLeave) {
            foreach ($detailsOfLeave as $item) {
                $parts = explode('=', $item, 2);
                $key = trim($parts[0]);
                if ($key === $detail) {
                    return true;
                }
            }

            return false;
        };

        $getDetailValue = function ($detail) use ($detailsOfLeave) {
            foreach ($detailsOfLeave as $item) {
                $parts = explode('=', $item, 2);
                if (count($parts) === 2) {
                    $key = trim($parts[0]);
                    $value = trim($parts[1]);
                    if ($key === $detail) {
                        return $value;
                    }
                }
            }

            return '';
        };

        $daysWithPay = '';
        $daysWithoutPay = '';
        $otherRemarks = '';

        if ($leaveApplication->status === 'Approved') {
            if ($leaveApplication->remarks === 'With Pay') {
                $daysWithPay = $leaveApplication->approved_days;
            } elseif ($leaveApplication->remarks === 'Without Pay') {
                $daysWithoutPay = $leaveApplication->approved_days;
            } else {
                $otherRemarks = $leaveApplication->remarks;
            }
        }

        $leaveCredits = LeaveCredits::where('user_id', $leaveApplication->user_id)->first();

        $firstPagePDF = PDF::loadView('pdf.leave-application', [
            'leaveApplication' => $leaveApplication,
            'selectedLeaveTypes' => $selectedLeaveTypes,
            'otherLeave' => $otherLeave,
            'detailsOfLeave' => $detailsOfLeave,
            'isDetailPresent' => $isDetailPresent,
            'getDetailValue' => $getDetailValue,
            'daysWithPay' => $daysWithPay,
            'daysWithoutPay' => $daysWithoutPay,
            'otherRemarks' => $otherRemarks,
            'leaveCredits' => $leaveCredits,
        ]);

        // Save the first page PDF to a temporary file
        $tempFirstPagePath = storage_path('app/temp_first_page.pdf');
        file_put_contents($tempFirstPagePath, $firstPagePDF->output());

        // Path to the second page template
        $secondPageTemplatePath = storage_path('app/public/pdf_template/secondpage.pdf');
        if (! file_exists($secondPageTemplatePath)) {
            throw new \Exception('Second page template not found at: '.$secondPageTemplatePath);
        }

        // Create a multi-page PDF using FPDI
        $pdf = new \setasign\Fpdi\Fpdi();

        // Add the first page
        $pdf->AddPage();
        $pdf->SetTitle('Leave Application');
        $pdf->setSourceFile($tempFirstPagePath);
        $tplId = $pdf->importPage(1);
        $pdf->useTemplate($tplId);

        // Add the second page
        $pdf->AddPage();
        $pdf->setSourceFile($secondPageTemplatePath);
        $tplId = $pdf->importPage(1);
        $pdf->useTemplate($tplId);

        // Clean up temporary file
        unlink($tempFirstPagePath);

        // Define a user-friendly filename
        $fileName = 'Leave_Application_'.$leaveApplication->id.'.pdf';

        // Output the final PDF with the specified filename
        $this->pdfContent = base64_encode($pdf->Output($fileName, 'S'));
        $this->showPDFPreview = true;
    }

    public function downloadFile($filePath)
    {
        if (Storage::disk('public')->exists($filePath)) {
            $fullPath = Storage::disk('public')->path($filePath);
            $fileName = basename($filePath);
            $mimeType = Storage::disk('public')->mimeType($filePath);

            return response()->download($fullPath, $fileName, [
                'Content-Type' => $mimeType,
                'Content-Disposition' => 'attachment; filename="'.$fileName.'"',
            ]);
        }

        $this->dispatch('swal', [
            'title' => 'File not found!',
            'icon' => 'error',
        ]);
    }
}
