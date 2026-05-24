<?php

namespace App\Livewire\Admin;

use App\Models\Wfh;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('Work From Home')]
class WfhSched extends Component
{
    public $selectedTab = 'pending';
    public $requests = [];
    public $reason = '';

    protected $listeners = ['refreshRequests' => 'loadRequests'];

    public function mount()
    {
        $this->loadRequests();
    }

    public function loadRequests()
    {
        $this->requests = Wfh::with('user')
            ->orderBy('wfhDay', 'desc')
            ->get();
    }

    public function approveRequest($id)
    {
        $request = Wfh::findOrFail($id);

        if ($request->status == 'pending') {
            $request->update([
                'status' => 'approved',
                'approved_at' => now()
            ]);

            $this->loadRequests();
            
            $this->dispatch('swal', [
                'title' => 'Success!',
                'text' => 'WFH request approved successfully.',
                'icon' => 'success'
            ]);
        } else {
            $this->dispatch('swal', [
                'title' => 'Error',
                'text' => 'Unable to approve this request.',
                'icon' => 'error'
            ]);
        }
    }

    public function rejectRequest($id)
    {
        $request = Wfh::findOrFail($id);

        if ($request->status == 'pending') {
            $request->update([
                'status' => 'rejected',
                'rejected_at' => now(),
                'rejection_reason' => $this->reason
            ]);

            $this->reset('reason');
            $this->loadRequests();
            
            $this->dispatch('swal', [
                'title' => 'Success!',
                'text' => 'WFH request rejected successfully.',
                'icon' => 'success'
            ]);
        } else {
            $this->dispatch('swal', [
                'title' => 'Error',
                'text' => 'Unable to reject this request.',
                'icon' => 'error'
            ]);
        }
    }

    // Download attachment method
    public function downloadAttachment($id)
    {
        $request = Wfh::findOrFail($id);

        // Check if attachment exists
        if (!$request->attachment || !Storage::disk('public')->exists($request->attachment)) {
            abort(404, 'File not found');
        }

        // Get the file path
        $filePath = storage_path('app/public/' . $request->attachment);
        $fileName = basename($request->attachment);

        // Return download response
        return response()->download($filePath, $fileName);
    }

    public function setSelectedTab($tab)
    {
        $this->selectedTab = $tab;
    }

    public function render()
    {
        return view('livewire.admin.wfh-sched');
    }
}