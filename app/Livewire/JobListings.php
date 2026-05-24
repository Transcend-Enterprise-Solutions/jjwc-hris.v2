<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\JobPosting;
use App\Models\JobApplication;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

#[Layout('layouts.career')]
#[Title('Job Opportunities - Philippine Government')]

class JobListings extends Component
{
    use WithPagination, WithFileUploads;

    public $search = '';
    public $showApplicationModal = false;
    public $selectedJob = null;
    public $showJobDetails = false;

    // Personal Information
    public $first_name;
    public $middle_name;
    public $last_name;
    public $email;
    public $phone;
    public $address;
    public $cover_letter;

    // Document uploads
    public $resume;
    public $pds;
    public $transcript;
    public $diploma;
    public $birth_cert;
    public $barangay_clearance;

    protected $rules = [
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'phone' => 'required|string|max:20',
        'address' => 'required|string|max:500',
        'cover_letter' => 'nullable|string|max:2000',
        'resume' => 'required|file|mimes:pdf|max:5120',
        'pds' => 'required|file|mimes:pdf|max:5120',
        'transcript' => 'required|file|mimes:pdf|max:5120',
        'diploma' => 'required|file|mimes:pdf|max:5120',
        'birth_cert' => 'required|file|mimes:pdf|max:5120',
        'barangay_clearance' => 'required|file|mimes:pdf|max:5120',
    ];

    protected $messages = [
        '*.required' => 'This field is required.',
        '*.mimes' => 'File must be a PDF document.',
        '*.max' => 'File size must not exceed 5MB.',
        'email.email' => 'Please enter a valid email address.',
    ];

    public function render()
    {
        $jobs = JobPosting::where('is_active', true)
            ->where('closing_date', '>=', now())
            ->when($this->search, function($query) {
                $query->where(function($q) {
                    $q->where('posting_title', 'like', '%'.$this->search.'%')
                      ->orWhere('position', 'like', '%'.$this->search.'%')
                      ->orWhere('division_office_department', 'like', '%'.$this->search.'%');
                });
            })
            ->orderBy('closing_date', 'asc')
            ->paginate(10);

        return view('livewire.job-listings', [
            'jobs' => $jobs
        ]);
    }

    public function showApplicationForm($jobId)
    {
        $this->selectedJob = JobPosting::findOrFail($jobId);
        $this->showApplicationModal = true;
        $this->showJobDetails = false;
        $this->resetApplicationForm();
    }

    public function showJobDetailsModal($jobId)
    {
        $this->selectedJob = JobPosting::findOrFail($jobId);
        $this->showJobDetails = true;
        $this->showApplicationModal = false;
    }

    public function submitApplication()
    {
        $this->validate();

        if (!$this->selectedJob) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'No job selected. Please try again.'
            ]);
            return;
        }

        try {
            DB::beginTransaction();

            // Validate file types
            $requiredFiles = ['resume', 'pds', 'transcript', 'diploma', 'birth_cert', 'barangay_clearance'];
            foreach ($requiredFiles as $field) {
                if ($this->$field && strtolower($this->$field->getClientOriginalExtension()) !== 'pdf') {
                    throw new \Exception("$field must be a PDF file.");
                }
            }

            // Store documents
            $documentPaths = $this->storeApplicationDocuments();

            // Create application with final tracking code
            $application = JobApplication::create([
                'job_posting_id' => $this->selectedJob->id,
                'first_name' => $this->first_name,
                'middle_name' => $this->middle_name,
                'last_name' => $this->last_name,
                'email' => $this->email,
                'phone' => $this->phone,
                'address' => $this->address,
                'cover_letter' => $this->cover_letter,
                'resume_path' => $documentPaths['resume_path'],
                'pds_path' => $documentPaths['pds_path'],
                'transcript_path' => $documentPaths['transcript_path'],
                'diploma_path' => $documentPaths['diploma_path'],
                'birth_cert_path' => $documentPaths['birth_cert_path'],
                'barangay_clearance_path' => $documentPaths['barangay_clearance_path'],
                'status' => JobApplication::STATUS_PENDING,
                'applied_at' => now(),
                'tracking_code' => $this->generateTrackingCode()
            ]);

            DB::commit();

            $this->resetApplicationForm();
            $this->showApplicationModal = false;

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => "Application submitted successfully! Your tracking code is: {$application->tracking_code}"
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            // Clean up uploaded files on error
            if (isset($documentPaths)) {
                foreach ($documentPaths as $path) {
                    if ($path && Storage::exists($path)) {
                        Storage::delete($path);
                    }
                }
            }

            Log::error('Application submission failed: ' . $e->getMessage());

            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Error submitting application: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Generate a secure, unique tracking code (format: APP-XXXX-YYYY)
     * Where XXXX = Job ID, YYYY = Random string
     */
    private function generateTrackingCode()
    {
        $jobId = str_pad($this->selectedJob->id, 4, '0', STR_PAD_LEFT);
        $random = strtoupper(Str::random(4));
        return "APP-{$jobId}-{$random}";
    }

    private function storeApplicationDocuments()
    {
        $applicantName = Str::slug($this->first_name . ' ' . $this->last_name);
        $jobTitle = Str::slug($this->selectedJob->position);
        $timestamp = now()->format('Ymd_His');

        $paths = [];
        $documents = [
            'resume' => 'resume',
            'pds' => 'pds',
            'transcript' => 'transcript',
            'diploma' => 'diploma',
            'birth_cert' => 'birth_cert',
            'barangay_clearance' => 'barangay_clearance'
        ];

        foreach ($documents as $field => $prefix) {
            if ($this->$field) {
                $filename = "{$prefix}_{$applicantName}_{$jobTitle}_{$timestamp}.pdf";
                $paths[$field . '_path'] = $this->$field->storeAs(
                    'applications/documents',
                    $filename,
                    'public'
                );
            }
        }

        return $paths;
    }

    public function closeModal()
    {
        $this->showApplicationModal = false;
        $this->showJobDetails = false;
        $this->resetApplicationForm();
    }

    private function resetApplicationForm()
    {
        $this->reset([
            'first_name', 'middle_name', 'last_name',
            'email', 'phone', 'address', 'cover_letter',
            'resume', 'pds', 'transcript', 'diploma',
            'birth_cert', 'barangay_clearance'
        ]);
        $this->resetErrorBag();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }
}
