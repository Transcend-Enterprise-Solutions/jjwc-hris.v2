<?php

namespace App\Livewire;

use App\Models\JobApplication;
use App\Models\JobPosting;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.career')]
#[Title('Track Your Application')]
class ApplicationTracker extends Component
{
    public $trackingCode = '';
    public $application = null;
    public $jobPosting = null;
    public $error = '';

    protected $rules = [
        'trackingCode' => 'required|string|min:12|max:15', // APP-0000-XXXX format
    ];

    protected $messages = [
        'trackingCode.required' => 'Please enter your tracking code.',
        'trackingCode.min' => 'Tracking code must be at least 12 characters.',
        'trackingCode.max' => 'Tracking code must not exceed 15 characters.',
    ];

    public function trackApplication()
    {
        $this->validate();
        $this->resetTracking();

        // Convert to uppercase and trim whitespace
        $cleanCode = strtoupper(trim($this->trackingCode));

        // Validate basic format
        if (!preg_match('/^APP-\d{4}-[A-Z0-9]{4}$/', $cleanCode)) {
            $this->error = 'Invalid tracking code format. Please use format: APP-0000-XXXX';
            return;
        }

        $this->application = JobApplication::findByTrackingCode($cleanCode);

        if (!$this->application) {
            $this->error = 'Application not found. Please verify your tracking code.';
            return;
        }

        $this->jobPosting = $this->application->jobPosting;
    }

    public function resetTracking()
    {
        $this->application = null;
        $this->jobPosting = null;
        $this->error = '';
    }

    public function updatedTrackingCode()
    {
        $this->resetTracking();
    }

    public function getStatusColorClass()
    {
        if (!$this->application) {
            return '';
        }

        return match ($this->application->status) {
            'pending' => 'text-yellow-600 bg-yellow-100',
            'viewed' => 'text-blue-600 bg-blue-100',
            'for interview' => 'text-purple-600 bg-purple-100',
            'waiting' => 'text-indigo-600 bg-indigo-100',
            'hired' => 'text-green-600 bg-green-100',
            'rejected' => 'text-red-600 bg-red-100',
            default => 'text-gray-600 bg-gray-100',
        };
    }

    public function getStatusBorderClass()
    {
        if (!$this->application) {
            return 'border-gray-300';
        }

        return match ($this->application->status) {
            'pending' => 'border-yellow-300',
            'viewed' => 'border-blue-300',
            'for interview' => 'border-purple-300',
            'waiting' => 'border-indigo-300',
            'hired' => 'border-green-300',
            'rejected' => 'border-red-300',
            default => 'border-gray-300',
        };
    }

    public function getStatusSteps()
    {
        $statusFlow = [
            'pending' => 'Application Received',
            'viewed' => 'Application Reviewed',
            'for interview' => 'Interview Scheduled',
            'waiting' => 'Waiting for Decision',
            'hired' => 'Hired',
        ];

        if (!$this->application) {
            return collect($statusFlow)->map(fn ($label, $status) => [
                'status' => $status,
                'label' => $label,
                'completed' => false,
                'active' => false,
            ])->values()->toArray();
        }

        $currentStatus = $this->application->status;
        $isRejected = $currentStatus === 'rejected';
        $steps = [];

        foreach ($statusFlow as $status => $label) {
            $isCompleted = $this->isStatusCompleted($status, $currentStatus);
            $isActive = !$isRejected && $status === $currentStatus;

            $steps[] = [
                'status' => $status,
                'label' => $label,
                'completed' => $isCompleted,
                'active' => $isActive,
            ];

            if ($isActive && $status !== 'hired') {
                break;
            }
        }

        return $steps;
    }

    private function isStatusCompleted($status, $currentStatus)
    {
        $statusOrder = [
            'pending' => 1,
            'viewed' => 2,
            'for interview' => 3,
            'waiting' => 4,
            'hired' => 5,
        ];

        if ($currentStatus === 'rejected') {
            return in_array($status, ['pending', 'viewed']);
        }

        return ($statusOrder[$status] ?? 0) <= ($statusOrder[$currentStatus] ?? 0);
    }

    public function render()
    {
        return view('livewire.application-tracker');
    }
}
