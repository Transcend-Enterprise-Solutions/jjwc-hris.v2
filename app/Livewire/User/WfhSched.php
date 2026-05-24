<?php

namespace App\Livewire\User;

use App\Models\Wfh;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
#[Title('WFH Request')]
class WfhSched extends Component
{
    use WithFileUploads;

    public $selectedDates = [];
    public $wfh_reason = '';
    public $attachment = null;
    public $selectedTab = 'pending';
    public $requests = [];
    public $isModalOpen = false;

    // Validation rules
    protected $rules = [
        'selectedDates' => 'required|array|min:1',
        'selectedDates.*' => 'required|date|after_or_equal:today',
        'wfh_reason' => 'required|string|min:5|max:500',
        'attachment' => 'nullable|file|max:10240|mimes:pdf,jpg,jpeg,png,doc,docx',
    ];

    protected $messages = [
        'selectedDates.required' => 'Please select at least one WFH date.',
        'selectedDates.min' => 'Please select at least one WFH date.',
        'selectedDates.*.required' => 'Each date must be valid.',
        'selectedDates.*.date' => 'Each date must be a valid date.',
        'selectedDates.*.after_or_equal' => 'Each date must be today or a future date.',
        'wfh_reason.required' => 'Please provide a reason for your WFH request.',
        'wfh_reason.min' => 'Your reason must be at least 5 characters.',
        'wfh_reason.max' => 'Your reason cannot exceed 500 characters.',
        'attachment.file' => 'The attachment must be a valid file.',
        'attachment.max' => 'The attachment size cannot exceed 10MB.',
        'attachment.mimes' => 'The attachment must be a PDF, JPG, PNG, DOC, or DOCX file.',
    ];

    public function mount()
    {
        $this->loadRequests();
    }

    public function loadRequests()
    {
        $this->requests = Wfh::where('user_id', Auth::id())
            ->orderBy('wfhDay', 'desc')
            ->get();
    }

    public function openModal()
    {
        $this->isModalOpen = true;
        $this->reset(['selectedDates', 'wfh_reason', 'attachment']);
        $this->resetValidation();
    }

    public function requestWfh()
    {
        // Get dates from JSON string if needed
        if (is_string($this->selectedDates)) {
            $this->selectedDates = json_decode($this->selectedDates, true) ?? [];
        }
        
        // Validate
        $this->validate();
        
        // Check for duplicates with existing requests
        $duplicateDates = [];
        foreach ($this->selectedDates as $date) {
            $existingRequest = Wfh::where('user_id', Auth::id())
                ->where('wfhDay', $date)
                ->whereIn('status', ['pending', 'approved'])
                ->first();
                
            if ($existingRequest) {
                $duplicateDates[] = $date;
            }
        }
        
        if (!empty($duplicateDates)) {
            $formattedDates = implode(', ', array_map(function($date) {
                return Carbon::parse($date)->format('M d, Y');
            }, $duplicateDates));
            
            $this->dispatch('swal', [
                'title' => 'Duplicate Requests',
                'text' => "You already have pending/approved requests for: $formattedDates",
                'icon' => 'error'
            ]);
            return;
        }
        
        // Handle file upload (same file for all dates)
        $attachmentPath = null;
        if ($this->attachment) {
            $fileName = time() . '_' . Auth::id() . '_' . $this->attachment->getClientOriginalName();
            $attachmentPath = $this->attachment->storeAs('wfh-attachments', $fileName, 'public');
        }
        
        // Create separate records for each date
        $createdCount = 0;
        foreach ($this->selectedDates as $date) {
            Wfh::create([
                'wfhDay' => $date,
                'wfh_reason' => $this->wfh_reason,
                'attachment' => $attachmentPath,
                'status' => 'pending',
                'user_id' => Auth::id()
            ]);
            $createdCount++;
        }
        
        // Reset form, close modal and reload requests
        $this->reset(['selectedDates', 'wfh_reason', 'attachment']);
        $this->loadRequests();
        
        // Close modal
        $this->isModalOpen = false;

        $this->dispatch('swal', [
            'title' => 'Success!',
            'text' => "WFH requests for $createdCount date(s) submitted successfully.",
            'icon' => 'success'
        ]);
    }

    public function downloadAttachment($id)
    {
        $request = Wfh::findOrFail($id);
        $user = Auth::user();

        $adminRoles = ['sa', 'hr', 'sv', 'pa'];
        $isAdmin = in_array($user->user_role, $adminRoles);
        
        if ($request->user_id != Auth::id() && !$isAdmin) {
            abort(403, 'Unauthorized access');
        }

        if (!$request->attachment || !Storage::disk('public')->exists($request->attachment)) {
            abort(404, 'File not found');
        }

        $filePath = storage_path('app/public/' . $request->attachment);
        $fileName = basename($request->attachment);

        return response()->download($filePath, $fileName);
    }

    public function cancelRequest($id)
    {
        $request = Wfh::findOrFail($id);

        if ($request->user_id == Auth::id() && $request->status == 'pending') {
            $attachmentInUse = Wfh::where('attachment', $request->attachment)
                ->where('id', '!=', $id)
                ->exists();
            
            if ($request->attachment && !$attachmentInUse && Storage::disk('public')->exists($request->attachment)) {
                Storage::disk('public')->delete($request->attachment);
            }

            $request->delete();
            $this->loadRequests();

            $this->dispatch('swal', [
                'title' => 'Request Cancelled',
                'text' => 'WFH request cancelled successfully.',
                'icon' => 'success'
            ]);
        } else {
            $this->dispatch('swal', [
                'title' => 'Error',
                'text' => 'Unable to cancel this request.',
                'icon' => 'error'
            ]);
        }
    }

    public function setSelectedTab($tab)
    {
        $this->selectedTab = $tab;
    }

    public function render()
    {
        return view('livewire.user.wfh-sched');
    }
}