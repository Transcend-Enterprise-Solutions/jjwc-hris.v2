<?php

namespace App\Livewire\User;

use App\Exports\LeaveCardExport;
use App\Models\EmployeeSalary;
use App\Models\ESignature;
use App\Models\LeaveApplication as LA;
use App\Models\LeaveApprovals;
use App\Models\LeaveCredits;
use App\Models\MandatoryFormRequest;
use App\Models\OfficeDivisions;
use App\Models\Positions;
use App\Models\ULCredit;
use App\Models\User;
// use App\Services\NotificationService;
use App\Models\UserData;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Component\HttpFoundation\StreamedResponse;

#[Layout('layouts.app')]
#[Title('Leave Application')]
class LeaveApplication extends Component
{
    use WithFileUploads, WithPagination;

    public $applyForLeave = false;

    public $name;

    public $office_or_department;

    public $date_of_filing;

    public $position;

    // public $salary;

    public $salary = '';

    public $number_of_days;

    public $type_of_leave = [];

    public $details_of_leave = [];

    public $philippines;

    public $abroad;

    public $inHospital;

    public $outPatient;

    public $specialIllnessForWomen;

    public $commutation;

    public $files = [];

    public $other_leave;

    public $start_date;

    public $end_date;

    public $list_of_dates = [];

    public $new_date;

    public $selectedYear;

    public $isDisabled = false;

    public $activeTab = 'pending';

    public $showDropdown = false;

    public $requestSent = false;

    public $requestApproved = false;

    public $pageSize = 5;

    public $pageSizes = [5, 10, 20, 30, 50, 100];

    public $exportingPDF = false;

    public $showingProgress = false;

    public $currentPDFId = null;

    public $showCancelModal = false;

    public $applicationToCancel = null;

    public $hasAllApprovers = true;

    public $showApproverWarning = false;

    public $dateMode = 'single'; // 'single' or 'range'

    // protected NotificationService $notificationService;

    // public function __construct()
    // {
    //     $this->notificationService = app(NotificationService::class);
    // }

    protected $rules = [
        'office_or_department' => 'required|string|max:255',
        'position' => 'required|string|max:255',
        'salary' => 'nullable|string|max:255',
        'type_of_leave' => 'required|array|min:1',
        'files.*' => 'file|mimes:jpeg,png,jpg,pdf|max:2048',
        'number_of_days' => 'required|numeric|min:1',
    ];

    public function toggleDropdown()
    {
        $this->showDropdown = ! $this->showDropdown;
    }

    public function closeDropdown()
    {
        $this->showDropdown = false;
    }

    public function openLeaveForm()
    {
        // Check if all approvers are set before opening the form
        if (! $this->checkApproversSetup()) {
            $this->showApproverWarning = true;

            return;
        }

        $this->loadUserData();
        $this->applyForLeave = true;
    }

    public function closeLeaveForm()
    {
        $this->applyForLeave = false;
        $this->resetForm();
        $this->resetValidation();
    }

    public function openCancelModal($applicationId)
    {
        $this->applicationToCancel = $applicationId;
        $this->showCancelModal = true;
    }

    public function confirmCancelApplication()
    {
        if ($this->applicationToCancel) {
            $this->cancelLeaveApplication($this->applicationToCancel);
            $this->showCancelModal = false;
            $this->applicationToCancel = null;
        }
    }

    public function resetVariables()
    {
        $this->office_or_department = null;
        $this->position = null;
        $this->salary = null;
        $this->number_of_days = null;
        $this->type_of_leave = [];
        $this->details_of_leave = [];
        $this->commutation = null;
        $this->philippines = null;
        $this->abroad = null;
        $this->inHospital = null;
        $this->outPatient = null;
        $this->specialIllnessForWomen = null;
        $this->files = [];
        $this->list_of_dates = [];
        $this->new_date = null;
        $this->new_date = null;
        $this->start_date = null;
        $this->end_date = null;
        $this->other_leave = null;
    }

    public function loadUserData()
    {
        $user = Auth::user();
        $userData = UserData::where('user_id', $user->id)->first();

        if ($userData) {
            $this->name = $user->name;
            $this->date_of_filing = now()->toDateString();
        }

        $officeDivision = OfficeDivisions::find($user->office_division_id);
        $position = Positions::find($user->position_id);

        $this->office_or_department = $officeDivision ? $officeDivision->office_division : 'N/A';
        $this->position = $position ? $position->position : 'N/A';

        $this->loadEmployeeSalary();
    }

    protected function loadEmployeeSalary()
    {
        $userId = Auth::id();
        $employeeSalary = EmployeeSalary::where('user_id', $userId)->first();

        if ($employeeSalary) {
            // Store the raw numeric value for database operations
            $this->salary = $employeeSalary->monthly_basic_salary;
        } else {
            $this->salary = 0; // Default value if no salary record found
        }
    }

    public function resetOtherFields($field)
    {
        $fields = ['philippines', 'abroad', 'inHospital', 'outPatient', 'specialIllnessForWomen'];
        foreach ($fields as $f) {
            if ($f !== $field) {
                $this->{$f} = '';
            }
        }
    }

    // public function updatedTypeOfLeave($value)
    // {
    //     if ($value === 'Union Leave') {
    //         $this->number_of_days = 1;

    //         $this->details_of_leave = '';
    //         $this->philippines = '';
    //         $this->abroad = '';
    //         $this->inHospital = '';
    //         $this->outPatient = '';
    //         $this->specialIllnessForWomen = '';
    //         $this->start_date = '';
    //         $this->end_date = '';

    //         $this->list_of_dates = [];
    //     } else {
    //         $this->number_of_days = count($this->list_of_dates);
    //     }
    // }
    public function updatedTypeOfLeave($value)
    {
        $this->list_of_dates = [];
        $this->number_of_days = 0;
        $this->start_date = null;
        $this->end_date = null;
        $this->dateMode = 'single';
    }

    public function setDateMode($mode)
    {
        $this->dateMode = $mode;
        $this->list_of_dates = [];
        $this->number_of_days = 0;
        $this->start_date = null;
        $this->end_date = null;
        $this->new_date = null;
    }

    public function submitLeaveApplication()
    {
        $rules = [
            'office_or_department' => 'required',
            'position' => 'required',
            // 'salary' => '',
            'type_of_leave' => 'required|string',
            'number_of_days' => 'required',
            'commutation' => 'required',
        ];

        $leaveTypesRequiringDetails = [
            'Vacation Leave',
            'Special Privilege Leave',
            'Sick Leave',
            'Special Leave Benefits for Women',
            'Study Leave',
            'Others',
        ];

        if (in_array($this->type_of_leave, $leaveTypesRequiringDetails)) {
            $rules['details_of_leave'] = 'required|string';

            if ($this->details_of_leave === 'Within the Philippines') {
                $rules['philippines'] = 'required|string';
            }
            if ($this->details_of_leave === 'Abroad') {
                $rules['abroad'] = 'required|string';
            }
            if ($this->details_of_leave === 'In Hospital') {
                $rules['inHospital'] = 'required|string';
            }
            if ($this->details_of_leave === 'Out Patient') {
                $rules['outPatient'] = 'required|string';
            }
            if ($this->details_of_leave === 'Women Special Illness') {
                $rules['specialIllnessForWomen'] = 'required|string';
            }
        }

        $leaveTypesRequiringDates = [
            'Vacation Leave',
            'Sick Leave',
            'Paternity Leave',
            'Special Privilege Leave',
            'Mandatory/Forced Leave',
            'Solo Parent Leave',
            '10-Day VAWC Leave',
            'Special Emergency (Calamity) Leave',
            'Adoption Leave',
            'CTO Leave',
        ];

        if (in_array($this->type_of_leave, $leaveTypesRequiringDates)) {
            $rules['list_of_dates'] = 'required|array|min:1';

            if ($this->type_of_leave === 'Union Leave') {
                $rules['list_of_dates'] = 'required|array|size:1';
            }
        }

        $this->validate($rules);

        if ($this->type_of_leave === 'Union Leave') {
            $ulCredit = ULCredit::where('user_id', Auth::id())->first();

            if (! $ulCredit || ($ulCredit->acces_credits <= 0 && $ulCredit->acsp_credits <= 0)) {
                $this->addError('type_of_leave', 'You do not have any Union Leave credits available.');

                return;
            }

            $totalRequestedDays = 1;
            $availableCredits = max($ulCredit->acces_credits, $ulCredit->acsp_credits);

            if ($totalRequestedDays > $availableCredits) {
                $this->addError('number_of_days', "You only have {$availableCredits} Union Leave credits available.");

                return;
            }

            $this->number_of_days = 1;
        }

        if (! $this->checkLeaveCreditsBeforeSubmission($this->number_of_days, $this->type_of_leave)) {
            return;
        }

        if ($this->type_of_leave === 'Others') {
            $this->validate([
                'other_leave' => 'required|string',
            ]);
        }

        $filePaths = [];
        $fileNames = [];

        if ($this->files) {
            foreach ($this->files as $file) {
                $originalFilename = $file->getClientOriginalName();
                $filePath = $file->storeAs('leavedocu', $originalFilename, 'public');
                $filePaths[] = $filePath;
                $fileNames[] = $originalFilename;
            }
        }

        $leaveDetails = null;

        if (in_array($this->type_of_leave, $leaveTypesRequiringDetails)) {
            $leaveDetails = $this->details_of_leave;

            if ($this->details_of_leave === 'Within the Philippines') {
                $leaveDetails .= ' = '.$this->philippines;
            } elseif ($this->details_of_leave === 'Abroad') {
                $leaveDetails .= ' = '.$this->abroad;
            } elseif ($this->details_of_leave === 'In Hospital') {
                $leaveDetails .= ' = '.$this->inHospital;
            } elseif ($this->details_of_leave === 'Out Patient') {
                $leaveDetails .= ' = '.$this->outPatient;
            } elseif ($this->details_of_leave === 'Women Special Illness') {
                $leaveDetails .= ' = '.$this->specialIllnessForWomen;
            }
        }

        $datesString = '';
        if ($this->start_date && $this->end_date) {
            $datesString = $this->start_date.' - '.$this->end_date;
        } elseif (! empty($this->list_of_dates)) {
            $datesString = implode(',', $this->list_of_dates);
        }

        if ($this->type_of_leave === 'Others') {
            $this->type_of_leave = 'Others = '.$this->other_leave;
        }

        $userId = Auth::id();

        $leaveApplication = LA::create([
            'user_id' => $userId,
            'name' => $this->name,
            'office_or_department' => $this->office_or_department,
            'date_of_filing' => $this->date_of_filing,
            'position' => $this->position,
            'salary' => $this->salary,
            'number_of_days' => $this->number_of_days,
            'type_of_leave' => $this->type_of_leave,
            'details_of_leave' => $leaveDetails,
            'commutation' => $this->commutation,
            'status' => 'Pending',
            'file_path' => implode(',', $filePaths),
            'file_name' => implode(',', $fileNames),
            'list_of_dates' => $datesString,
        ]);

        $this->deductLeaveCreditsOnSubmission($leaveApplication->id, $this->number_of_days, $this->type_of_leave);

        $approvers = $this->getAutomaticApprovers();

        LeaveApprovals::create([
            'user_id' => $userId,
            'application_id' => $leaveApplication->id,
            'first_approver' => $approvers['first_approver'],
            'second_approver' => $approvers['second_approver'],
            'third_approver' => $approvers['third_approver'],
            'stage' => 1,
        ]);

        // Create notification for admin/HR
        // $this->notificationService->create(
        //     null,
        //     'request',
        //     'leave_application',
        //     $leaveApplication->id,
        //     'leave_application'
        // );

        $this->dispatch('swal', [
            'title' => 'Leave application sent successfully!',
            'icon' => 'success',
        ]);

        $this->resetForm();
        $this->closeLeaveForm();
    }

    protected function checkLeaveCreditsBeforeSubmission($days, $leaveType)
    {
        $userId = Auth::id();
        $leaveCredits = LeaveCredits::where('user_id', $userId)->first();

        if (! $leaveCredits) {
            $errorMessage = 'Leave credits not found for your account. Please contact HR.';
            $this->addError('number_of_days', $errorMessage);

            $this->dispatch('swal', [
                'title' => 'Leave Credits Not Found',
                'text' => $errorMessage,
                'icon' => 'error',
            ]);

            return false;
        }

        $actualLeaveType = $leaveType;
        if (strpos($leaveType, 'Others = ') === 0) {
            $actualLeaveType = trim(str_replace('Others = ', '', $leaveType));
        }

        if ($actualLeaveType === 'Mandatory/Forced Leave') {
            if ($leaveCredits->fl_claimable_credits < $days || $leaveCredits->vl_claimable_credits < $days) {
                $errorMessage = 'Insufficient Forced Leave Credits. Available FL: '.number_format($leaveCredits->fl_claimable_credits ?? 0.000, 3).', VL: '.number_format($leaveCredits->vl_claimable_credits ?? 0.000, 3);

                $this->addError('number_of_days', $errorMessage);

                $this->dispatch('swal', [
                    'title' => 'Insufficient Forced Leave Credits',
                    'html' => 'Available FL Credits: <strong>'.number_format($leaveCredits->fl_claimable_credits ?? 0.000, 3).'</strong><br>Available VL Credits: <strong>'.number_format($leaveCredits->vl_claimable_credits ?? 0.000, 3)."</strong><br><br>Requested Days: <strong>{$days}</strong><br><br>You need at least {$days} credits in both FL and VL.",
                    'icon' => 'error',
                ]);

                return false;
            }
        } elseif ($actualLeaveType === 'Vacation Leave') {
            if ($leaveCredits->vl_claimable_credits < $days) {
                $errorMessage = 'Insufficient Vacation Leave Credits. Available: '.number_format($leaveCredits->vl_claimable_credits ?? 0.000, 3);

                $this->addError('number_of_days', $errorMessage);

                $this->dispatch('swal', [
                    'title' => 'Insufficient Vacation Leave Credits',
                    'html' => 'Available VL Credits: <strong>'.number_format($leaveCredits->vl_claimable_credits ?? 0.000, 3)."</strong><br>Requested Days: <strong>{$days}</strong><br><br>You don't have enough Vacation Leave credits.",
                    'icon' => 'error',
                ]);

                return false;
            }
        } elseif ($actualLeaveType === 'Sick Leave') {
            if ($leaveCredits->sl_claimable_credits < $days) {
                $errorMessage = 'Insufficient Sick Leave Credits. Available: '.number_format($leaveCredits->sl_claimable_credits ?? 0.000, 3);

                $this->addError('number_of_days', $errorMessage);

                $this->dispatch('swal', [
                    'title' => 'Insufficient Sick Leave Credits',
                    'html' => 'Available SL Credits: <strong>'.number_format($leaveCredits->sl_claimable_credits ?? 0.000, 3)."</strong><br>Requested Days: <strong>{$days}</strong><br><br>You don't have enough Sick Leave credits.",
                    'icon' => 'error',
                ]);

                return false;
            }
        } elseif ($actualLeaveType === 'Special Privilege Leave') {
            if ($leaveCredits->spl_claimable_credits < $days) {
                $errorMessage = 'Insufficient Special Privilege Leave Credits. Available: '.number_format($leaveCredits->spl_claimable_credits ?? 0.000, 3);

                $this->addError('number_of_days', $errorMessage);

                $this->dispatch('swal', [
                    'title' => 'Insufficient SPL Credits',
                    // 'html' => 'Available SPL Credits: <strong>'.number_format($leaveCredits->spl_claimable_credits ?? 0.000, 3)."</strong><br>Requested Days: <strong>{$days}</strong><br><br>You don't have enough Special Privilege Leave credits.',
                    'icon' => 'error',
                ]);

                return false;
            }
        } elseif ($actualLeaveType === 'CTO Leave') {
            if ($leaveCredits->cto_claimable_credits < $days) {
                $errorMessage = 'Insufficient CTO Credits. Available: '.number_format($leaveCredits->cto_claimable_credits ?? 0.000, 3);

                $this->addError('number_of_days', $errorMessage);

                $this->dispatch('swal', [
                    'title' => 'Insufficient CTO Credits',
                    // 'html' => 'Available CTO Credits: <strong>'.number_format($leaveCredits->cto_claimable_credits ?? 0.000, 3)."</strong><br>Requested Days: <strong>{$days}</strong><br><br>You dont have enough CTO credits.",
                    'icon' => 'error',
                ]);

                return false;
            }
        }

        return true;
    }

    protected function deductLeaveCreditsOnSubmission($applicationId, $days, $leaveType)
    {
        $userId = Auth::id();
        $leaveCredits = LeaveCredits::where('user_id', $userId)->first();

        if (! $leaveCredits) {
            return;
        }

        $leaveApplication = LA::find($applicationId);
        if (! $leaveApplication) {
            return;
        }

        $actualLeaveType = $leaveType;
        if (strpos($leaveType, 'Others = ') === 0) {
            $actualLeaveType = trim(str_replace('Others = ', '', $leaveType));
        }

        $daysToDeduct = $days;

        if ($actualLeaveType === 'Mandatory/Forced Leave') {
            $leaveCredits->fl_claimable_credits -= $daysToDeduct;
            $leaveCredits->fl_claimed_credits += $daysToDeduct;

            $leaveCredits->vl_claimable_credits -= $daysToDeduct;
            $leaveCredits->vl_claimed_credits += $daysToDeduct;
        } elseif ($actualLeaveType === 'Vacation Leave') {
            $leaveCredits->vl_claimable_credits -= $daysToDeduct;
            $leaveCredits->vl_claimed_credits += $daysToDeduct;
        } elseif ($actualLeaveType === 'Sick Leave') {
            $leaveCredits->sl_claimable_credits -= $daysToDeduct;
            $leaveCredits->sl_claimed_credits += $daysToDeduct;
        } elseif ($actualLeaveType === 'Special Privilege Leave') {
            $leaveCredits->spl_claimable_credits -= $daysToDeduct;
            $leaveCredits->spl_claimed_credits += $daysToDeduct;
        } elseif ($actualLeaveType === 'CTO Leave') {
            $leaveCredits->cto_claimable_credits -= $daysToDeduct;
            $leaveCredits->cto_claimed_credits += $daysToDeduct;
        }

        $leaveCredits->save();
    }

    private function getAutomaticApprovers()
    {
        $approvers = [
            'first_approver' => null,
            'second_approver' => null,
            'third_approver' => null,
        ];

        $leaveApprovers = \App\Models\LeaveApprover::with('user')
            ->where('is_active', true)
            ->orderBy('approver_level')
            ->get();

        foreach ($leaveApprovers as $approver) {
            if ($approver->user_id) {
                switch ($approver->approver_level) {
                    case 'first':
                        $approvers['first_approver'] = $approver->user_id;
                        break;
                    case 'second':
                        $approvers['second_approver'] = $approver->user_id;
                        break;
                    case 'third':
                        $approvers['third_approver'] = $approver->user_id;
                        break;
                }
            }
        }

        return $approvers;
    }

    public function removeFile($index)
    {
        if (isset($this->files[$index])) {
            unset($this->files[$index]);
            $this->files = array_values($this->files);
        }
    }

    public function updatedStartDate($value)
    {
        if ($this->start_date && $this->end_date) {
            $this->calculateWorkingDays();
        }
    }

    public function updatedEndDate($value)
    {
        if ($this->start_date && $this->end_date) {
            $this->calculateWorkingDays();
        }
    }

    protected function calculateWorkingDays()
    {
        if (! $this->start_date || ! $this->end_date) {
            return;
        }

        $start = Carbon::parse($this->start_date);
        $end = Carbon::parse($this->end_date);

        if ($end->lt($start)) {
            $this->addError('end_date', 'End date cannot be before start date');

            return;
        }

        $workingDays = 0;
        $current = $start->copy();

        while ($current->lte($end)) {
            if (! $current->isWeekend()) {
                $workingDays++;
            }
            $current->addDay();
        }

        $this->number_of_days = $workingDays;

        $this->list_of_dates = [$this->start_date.' - '.$this->end_date];
    }

    public function addDate()
    {
        $this->validate([
            'new_date' => 'required|date',
        ]);

        if ($this->type_of_leave === 'Union Leave' && count($this->list_of_dates) >= 1) {
            $this->addError('new_date', 'Union Leave allows only one date per application.');

            return;
        }

        if ($this->start_date && $this->end_date) {
            $this->start_date = null;
            $this->end_date = null;
        }

        if (! in_array($this->new_date, $this->list_of_dates)) {
            $date = Carbon::parse($this->new_date);
            if ($date->isWeekend()) {
                $this->addError('new_date', 'Weekends cannot be selected as leave days');

                return;
            }

            $this->list_of_dates[] = $this->new_date;

            if ($this->type_of_leave === 'Union Leave') {
                $this->number_of_days = 1;
            } else {
                $this->number_of_days = count($this->list_of_dates);
            }
        }

        $this->new_date = '';
    }

    public function removeDate($index)
    {
        if ($this->start_date && $this->end_date) {
            $this->start_date = null;
            $this->end_date = null;
        }

        unset($this->list_of_dates[$index]);
        $this->list_of_dates = array_values($this->list_of_dates);

        if ($this->type_of_leave === 'Union Leave') {
            $this->number_of_days = count($this->list_of_dates) > 0 ? 1 : 0;
        } else {
            $this->number_of_days = count($this->list_of_dates);
        }
    }

    public function resetForm()
    {
        $this->reset([
            'office_or_department',
            'position',
            'salary',
            'number_of_days',
            'type_of_leave',
            'details_of_leave',
            'commutation',
            'philippines',
            'abroad',
            'inHospital',
            'outPatient',
            'specialIllnessForWomen',
            'files',
            'other_leave',
            'list_of_dates',
            'new_date',
            'start_date',
            'end_date',
            'dateMode',
        ]);
    }

    public function exportPDF($leaveApplicationId)
    {
        $this->exportingPDF = true;
        $this->currentPDFId = $leaveApplicationId;

        try {
            $leaveApplication = LA::with('user.userData')->findOrFail($leaveApplicationId);

            $eSignature = ESignature::where('user_id', $leaveApplication->user_id)->first();

            $signatureImagePath = null;
            if ($eSignature && $eSignature->file_path) {
                $signatureImagePath = Storage::disk('public')->path($eSignature->file_path);
            }

            $selectedLeaveTypes = [];
            if ($leaveApplication->type_of_leave) {
                if (strpos($leaveApplication->type_of_leave, 'Union Leave') !== false) {
                    $selectedLeaveTypes[] = 'Union Leave';
                }

                $otherTypes = explode(',', $leaveApplication->type_of_leave);
                foreach ($otherTypes as $type) {
                    $trimmedType = trim($type);
                    if (! empty($trimmedType) && strpos($trimmedType, 'Union Leave') === false) {
                        $selectedLeaveTypes[] = $trimmedType;
                    }
                }
            }

            $otherLeave = '';
            if (in_array($leaveApplication->type_of_leave, ['Birthday Leave', 'Emergency Leave'])) {
                $otherLeave = $leaveApplication->type_of_leave;
            } elseif ($leaveApplication->type_of_leave === 'Others') {
                $otherLeave = $leaveApplication->other_leave_text ?? '';
            } else {
                foreach ($selectedLeaveTypes as $leaveType) {
                    if (strpos($leaveType, 'Others = ') === 0) {
                        $otherLeave = str_replace('Others = ', '', $leaveType);
                        break;
                    } elseif (strpos($leaveType, 'Others: ') === 0) {
                        $otherLeave = str_replace('Others: ', '', $leaveType);
                        break;
                    }
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

            $leaveApproval = LeaveApprovals::where('application_id', $leaveApplicationId)->first();

            $firstApproverSignature = null;
            $secondApproverSignature = null;
            $thirdApproverSignature = null;

            if ($leaveApproval && $leaveApproval->first_approver) {
                $firstApprover = User::find($leaveApproval->first_approver);
                $firstApproverName = $firstApprover ? $firstApprover->name : 'N/A';

                $empCode = preg_replace('/^[^-]+-/', '', $firstApprover->emp_code);

                $empUser = User::where('emp_code', $empCode)
                    ->where('user_role', 'emp')
                    ->first();

                if ($empUser) {
                    $empSignature = ESignature::where('user_id', $empUser->id)->first();
                    if ($empSignature && $empSignature->file_path) {
                        $firstApproverSignature = Storage::disk('public')->path($empSignature->file_path);
                    }
                }
            } else {
                $firstApproverName = 'N/A';
            }

            if ($leaveApproval && $leaveApproval->second_approver) {
                $secondApprover = User::find($leaveApproval->second_approver);
                $secondApproverName = $secondApprover ? $secondApprover->name : 'N/A';

                $empCode = preg_replace('/^[^-]+-/', '', $secondApprover->emp_code);

                $empUser = User::where('emp_code', $empCode)
                    ->where('user_role', 'emp')
                    ->first();

                if ($empUser) {
                    $empSignature = ESignature::where('user_id', $empUser->id)->first();
                    if ($empSignature && $empSignature->file_path) {
                        $secondApproverSignature = Storage::disk('public')->path($empSignature->file_path);
                    }
                }
            } else {
                $secondApproverName = 'N/A';
            }

            if ($leaveApproval && $leaveApproval->third_approver) {
                $thirdApprover = User::find($leaveApproval->third_approver);
                $thirdApproverName = $thirdApprover ? $thirdApprover->name : 'N/A';

                $empCode = preg_replace('/^[^-]+-/', '', $thirdApprover->emp_code);

                $empUser = User::where('emp_code', $empCode)
                    ->where('user_role', 'emp')
                    ->first();

                if ($empUser) {
                    $empSignature = ESignature::where('user_id', $empUser->id)->first();
                    if ($empSignature && $empSignature->file_path) {
                        $thirdApproverSignature = Storage::disk('public')->path($empSignature->file_path);
                    }
                }
            } else {
                $thirdApproverName = 'N/A';
            }

            $leaveCredits = LeaveCredits::where('user_id', $leaveApplication->user_id)->first();

            $firstPagePath = storage_path('app/temp/first-page.pdf');
            if (! file_exists(dirname($firstPagePath))) {
                mkdir(dirname($firstPagePath), 0755, true);
            }

            $pdf = PDF::loadView('pdf.leave-application', [
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
                'firstApproverName' => $firstApproverName,
                'secondApproverName' => $secondApproverName,
                'thirdApproverName' => $thirdApproverName,
                'eSignature' => $eSignature,
                'signatureImagePath' => $signatureImagePath,
                'firstApproverSignature' => $firstApproverSignature,
                'secondApproverSignature' => $secondApproverSignature,
                'thirdApproverSignature' => $thirdApproverSignature,
            ]);

            $pdf->save($firstPagePath);

            $secondPageTemplatePath = storage_path('app/public/pdf_template/secondpage.pdf');

            $outputPdfPath = storage_path('app/temp/combined-output.pdf');
            $fpdi = new \setasign\Fpdi\Fpdi;

            $fpdi->setSourceFile($firstPagePath);
            $firstPageId = $fpdi->importPage(1);
            $fpdi->addPage();
            $fpdi->useTemplate($firstPageId);

            $fpdi->setSourceFile($secondPageTemplatePath);
            $secondPageId = $fpdi->importPage(1);
            $fpdi->addPage();
            $fpdi->useTemplate($secondPageId);

            $fpdi->output($outputPdfPath, 'F');

            return response()->download($outputPdfPath, 'LeaveApplication'.$leaveApplicationId.'.pdf')->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            $this->exportingPDF = false;
            $this->currentPDFId = null;

            $this->dispatch('swal', [
                'title' => 'Error!',
                'text' => 'Failed to generate PDF: '.$e->getMessage(),
                'icon' => 'error',
            ]);
        }
    }

    public function render()
    {
        $userId = Auth::id();

        $request = MandatoryFormRequest::where('user_id', $userId)
            ->orderBy('date_requested', 'desc')
            ->first();

        $this->requestSent = $request !== null;
        $this->requestApproved = $request && $request->status === 'approved';

        $leaveCredits = LeaveCredits::where('user_id', $userId)->first();

        $leaveApplications = LA::query()
            ->where('user_id', $userId)
            ->when($this->activeTab === 'pending', function ($query) {
                return $query->where('status', 'Pending');
            })
            ->when($this->activeTab === 'approved', function ($query) {
                return $query->where('status', 'Approved');
            })
            ->when($this->activeTab === 'disapproved', function ($query) {
                return $query->where('status', 'Disapproved');
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->pageSize);

        return view('livewire.user.leave-application', [
            'leaveApplications' => $leaveApplications,
            // 'hasULCredits' => $this->hasULCredits,
        ]);
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function mount()
    {
        $this->selectedYear = date('Y');
        $this->list_of_dates = [];
        $this->number_of_days = 0;

        // $this->checkULCredits();
        $this->checkApproversSetup();
    }

    public function updatedSelectedYear()
    {
        $this->isDisabled = false;
    }

    public function exportExcel()
    {
        $export = new LeaveCardExport(Auth::id(), $this->selectedYear);

        return $export->export();
    }

    public function requestForm()
    {
        $userId = Auth::id();

        $existingRequest = MandatoryFormRequest::where('user_id', $userId)
            ->whereDate('date_requested', now()->toDateString())
            ->first();

        if (! $existingRequest) {
            MandatoryFormRequest::create([
                'user_id' => $userId,
                'status' => 'pending',
                'date_requested' => now(),
            ]);
        }
    }

    public function exportMandatoryLeaveForm()
    {
        try {
            $user = Auth::user();
            $userData = UserData::where('user_id', $user->id)->first();

            if (! $userData) {
                $this->dispatch('swal', [
                    'title' => 'Error!',
                    'text' => 'User data not found.',
                    'icon' => 'error',
                ]);

                return;
            }

            $officeDivision = OfficeDivisions::find($user->office_division_id);
            $office = $officeDivision ? $officeDivision->office_division : 'N/A';

            $leaveApplications = LA::where('user_id', $user->id)
                ->where('type_of_leave', 'like', '%Mandatory/Forced Leave%')
                ->where('status', 'Approved')
                ->where('remarks', 'With Pay')
                ->get();

            if ($leaveApplications->isEmpty()) {
                $this->dispatch('swal', [
                    'title' => 'No Records Found',
                    'text' => "You don't have any approved Mandatory/Forced Leave applications.",
                    'icon' => 'info',
                ]);

                return;
            }

            $mandatoryFormRequest = MandatoryFormRequest::where('user_id', $user->id)
                ->where('status', 'approved')
                ->latest()
                ->first();

            $approverName = null;
            if ($mandatoryFormRequest && $mandatoryFormRequest->approved_by) {
                $approver = User::find($mandatoryFormRequest->approved_by);
                if ($approver) {
                    $approverName = $approver->name;
                }
            }

            $templatePath = storage_path('app/public/leave_template/Mandatory Leave Form.xls');
            $spreadsheet = IOFactory::load($templatePath);
            $worksheet = $spreadsheet->getActiveSheet();

            $year = Carbon::now()->format('Y');

            $worksheet->setCellValue('A8', 'FOR CALENDAR YEAR '.$year);
            $worksheet->setCellValue('B11', $user->name);
            $worksheet->setCellValue('B12', $office);

            $allDates = [];
            foreach ($leaveApplications as $leave) {
                $dates = explode(',', $leave->approved_dates);
                foreach ($dates as $date) {
                    if (trim($date) !== '') {
                        if (strpos($date, ' - ') !== false) {
                            [$startDate, $endDate] = explode(' - ', $date);
                            $start = Carbon::parse($startDate);
                            $end = Carbon::parse($endDate);

                            for ($current = $start; $current->lte($end); $current->addDay()) {
                                if (! $current->isWeekend()) {
                                    $allDates[] = $current->format('Y-m-d');
                                }
                            }
                        } else {
                            $allDates[] = $date;
                        }
                    }
                }
            }

            $allDates = array_unique($allDates);
            sort($allDates);

            foreach ($allDates as $index => $date) {
                $formattedDate = ($index + 1).'. '.Carbon::parse($date)->format('F d, Y');
                $cellRow = 16 + $index;
                $worksheet->setCellValue("A{$cellRow}", $formattedDate);

                if ($index >= 10) {
                    $worksheet->insertNewRowBefore($cellRow + 1, 1);
                }
            }

            $signatureRow = 24 + max(0, count($allDates) - 10);
            $worksheet->setCellValue("C{$signatureRow}", $approverName ?: 'Not available');
            $worksheet->getStyle("C{$signatureRow}")->getFont()->setBold(true);

            $fileName = 'MandatoryLeaveForm'.$year.'.xlsx';

            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');

            $response = new StreamedResponse(
                function () use ($writer) {
                    $writer->save('php://output');
                }
            );

            $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            $response->headers->set('Content-Disposition', 'attachment;filename="'.$fileName.'"');
            $response->headers->set('Cache-Control', 'max-age=0');

            return $response;

        } catch (\Exception $e) {
            $this->dispatch('swal', [
                'title' => 'Error!',
                'text' => 'An error occurred while generating the Excel file: '.$e->getMessage(),
                'icon' => 'error',
            ]);

            return null;
        }
    }

    public function cancelLeaveApplication($applicationId)
    {
        try {
            $leaveApplication = LA::findOrFail($applicationId);
            $userId = Auth::id();

            // Check if the user owns this application
            if ($leaveApplication->user_id !== $userId) {
                $this->dispatch('swal', [
                    'title' => 'Unauthorized!',
                    'text' => 'You can only cancel your own leave applications.',
                    'icon' => 'error',
                ]);

                return;
            }

            // Check if application is still pending
            if ($leaveApplication->status !== 'Pending') {
                $this->dispatch('swal', [
                    'title' => 'Cannot Cancel!',
                    'text' => 'You can only cancel pending applications.',
                    'icon' => 'error',
                ]);

                return;
            }

            // Restore leave credits
            $this->restoreLeaveCredits($leaveApplication);

            // Update application status to Cancelled
            $leaveApplication->status = 'Cancelled';
            $leaveApplication->save();

            $this->dispatch('swal', [
                'title' => 'Application Cancelled!',
                'text' => 'Your leave application has been cancelled and credits have been restored.',
                'icon' => 'success',
            ]);

        } catch (\Exception $e) {
            $this->dispatch('swal', [
                'title' => 'Error!',
                'text' => 'Failed to cancel application: '.$e->getMessage(),
                'icon' => 'error',
            ]);
        }
    }

    protected function restoreLeaveCredits($leaveApplication)
    {
        $userId = $leaveApplication->user_id;
        $leaveType = $leaveApplication->type_of_leave;

        $leaveCredits = LeaveCredits::where('user_id', $userId)->first();

        if (! $leaveCredits) {
            return;
        }

        $actualLeaveType = $leaveType;
        if (strpos($leaveType, 'Others = ') === 0) {
            $actualLeaveType = trim(str_replace('Others = ', '', $leaveType));
        }

        // For hourly leave, recalculate from hours to ensure precision consistency
        $daysToRestore = $leaveApplication->number_of_days;

        if ($actualLeaveType === 'Mandatory/Forced Leave') {
            $leaveCredits->fl_claimable_credits += $daysToRestore;
            $leaveCredits->fl_claimed_credits -= $daysToRestore;

            $leaveCredits->vl_claimable_credits += $daysToRestore;
            $leaveCredits->vl_claimed_credits -= $daysToRestore;
        } elseif ($actualLeaveType === 'Vacation Leave') {
            $leaveCredits->vl_claimable_credits += $daysToRestore;
            $leaveCredits->vl_claimed_credits -= $daysToRestore;
        } elseif ($actualLeaveType === 'Sick Leave') {
            $leaveCredits->sl_claimable_credits += $daysToRestore;
            $leaveCredits->sl_claimed_credits -= $daysToRestore;
        } elseif ($actualLeaveType === 'Special Privilege Leave') {
            $leaveCredits->spl_claimable_credits += $daysToRestore;
            $leaveCredits->spl_claimed_credits -= $daysToRestore;
        } elseif ($actualLeaveType === 'CTO Leave') {
            $leaveCredits->cto_claimable_credits += $daysToRestore;
            $leaveCredits->cto_claimed_credits -= $daysToRestore;
        }

        $leaveCredits->save();
    }

    public function applicationProgress($leaveApplicationId)
    {
        $this->currentPDFId = $leaveApplicationId;
        $this->showingProgress = true;
    }

    public function getApplicationDetails($applicationId)
    {
        return LA::with([
            'user',
            'leaveApprovals.firstApproverUser',
            'leaveApprovals.secondApproverUser',
            'leaveApprovals.thirdApproverUser',
        ])->find($applicationId);
    }

    public function getLatestApproval($application)
    {
        return $application->leaveApprovals->sortByDesc('created_at')->first();
    }

    public function getApprovalProgress($application)
    {
        $latestApproval = $this->getLatestApproval($application);

        if (! $latestApproval) {
            return [
                'first_approved' => false,
                'second_approved' => false,
                'third_approved' => false,
                'current_stage' => 1,
                'status' => 'pending',
            ];
        }

        // Check if the application is fully approved
        $isFullyApproved = $application->status === 'Approved';

        return [
            'first_approved' => $latestApproval->stage >= 2 || $isFullyApproved,
            'second_approved' => $latestApproval->stage >= 3 || $isFullyApproved,
            'third_approved' => $latestApproval->stage >= 4 || $isFullyApproved,
            'current_stage' => $latestApproval->stage,
            'status' => $application->status,
        ];
    }

    public function checkApproversSetup()
    {
        $approvers = \App\Models\LeaveApprover::where('is_active', true)
            ->orderBy('approver_level')
            ->get();

        $firstApprover = $approvers->where('approver_level', 'first')->first();
        $secondApprover = $approvers->where('approver_level', 'second')->first();
        $thirdApprover = $approvers->where('approver_level', 'third')->first();

        $this->hasAllApprovers = $firstApprover && $firstApprover->user_id
            && $secondApprover && $secondApprover->user_id
            && $thirdApprover && $thirdApprover->user_id;

        // if (! $this->hasAllApprovers) {
        //     $this->showApproverWarning = true;
        // }

        return $this->hasAllApprovers;
    }

    public function closeApproverWarning()
    {
        $this->showApproverWarning = false;
    }
}
