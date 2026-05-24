<?php

namespace App\Livewire\Admin;

use App\Models\LeaveApprover;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Leave Settings')]
class LeaveSettings extends Component
{
    use WithPagination;

    public $showApproverModal = false;

    public $showOICModal = false;

    public $editingApproverId = null;

    public $selectedApproverId = null;

    // Form fields
    public $approver_level;

    public $required_role;

    public $user_id;

    public $oic_user_id;

    // Available options
    public $approverLevels = [
        'first' => 'First Approver (SV)',
        'second' => 'Second Approver (SV)',
        'third' => 'Third Approver (HR)',
    ];

    // Role requirements for each level
    public $roleRequirements = [
        'first' => ['sv'],
        'second' => ['sv'],
        'third' => ['hr'],
    ];

    // Display names for required roles
    public $roleDisplayNames = [
        'first' => 'SV',
        'second' => 'SV',
        'third' => 'HR',
    ];

    protected $rules = [
        'approver_level' => 'required|in:first,second,third',
        'user_id' => 'required|exists:users,id',
    ];

    public function mount()
    {
        // Initialize with default settings if none exist
        $this->initializeDefaultApprovers();
    }

    private function initializeDefaultApprovers()
    {
        $existingApprovers = LeaveApprover::count();

        if ($existingApprovers === 0) {
            // Only 3 default approvers - one for each level
            $defaultApprovers = [
                ['approver_level' => 'first', 'required_role' => 'sv'],
                ['approver_level' => 'second', 'required_role' => 'sv'],
                ['approver_level' => 'third', 'required_role' => 'hr'],
            ];

            foreach ($defaultApprovers as $approver) {
                LeaveApprover::create($approver);
            }
        }
    }

    public function openApproverModal($approverId = null)
    {
        if ($approverId) {
            $approver = LeaveApprover::findOrFail($approverId);
            $this->editingApproverId = $approver->id;
            $this->approver_level = $approver->approver_level;
            $this->required_role = $approver->required_role;
            $this->user_id = $approver->user_id;
        } else {
            $this->resetForm();
        }

        $this->showApproverModal = true;
    }

    public function openOICModal($approverId)
    {
        $approver = LeaveApprover::findOrFail($approverId);

        // Check if current user is the assigned approver
        if ($approver->user_id !== Auth::id()) {
            $this->dispatch('swal', [
                'title' => 'Access Denied',
                'text' => 'Only the assigned approver can set their own OIC.',
                'icon' => 'error',
            ]);

            return;
        }

        $this->selectedApproverId = $approverId;
        $this->oic_user_id = $approver->oic_user_id;
        $this->showOICModal = true;
    }

    public function saveApprover()
    {
        $this->validate();

        // Additional validation to prevent duplicate SV assignment
        if (in_array($this->approver_level, ['first', 'second'])) {
            $otherApproverLevel = $this->approver_level === 'first' ? 'second' : 'first';

            $otherApprover = LeaveApprover::where('approver_level', $otherApproverLevel)
                ->where('user_id', $this->user_id)
                ->first();

            if ($otherApprover && $otherApprover->id != $this->editingApproverId) {
                $this->addError('user_id', 'This supervisor is already assigned as the '.$this->approverLevels[$otherApproverLevel]);

                return;
            }
        }

        $data = [
            'approver_level' => $this->approver_level,
            'required_role' => $this->roleDisplayNames[$this->approver_level],
            'user_id' => $this->user_id,
        ];

        if ($this->editingApproverId) {
            LeaveApprover::findOrFail($this->editingApproverId)->update($data);
            $message = 'Approver updated successfully!';
        } else {
            LeaveApprover::create($data);
            $message = 'Approver added successfully!';
        }

        $this->showApproverModal = false;
        $this->resetForm();

        $this->dispatch('swal', [
            'title' => $message,
            'icon' => 'success',
        ]);
    }

    public function saveOIC()
    {
        $this->validate([
            'oic_user_id' => 'nullable|exists:users,id',
        ]);

        $approver = LeaveApprover::findOrFail($this->selectedApproverId);

        // Double-check authorization
        if ($approver->user_id !== Auth::id()) {
            $this->dispatch('swal', [
                'title' => 'Access Denied',
                'text' => 'Only the assigned approver can set their own OIC.',
                'icon' => 'error',
            ]);

            return;
        }

        // Get the previous OIC user (if any)
        $previousOicUserId = $approver->oic_user_id;

        // Convert empty string to null for database
        $newOicUserId = $this->oic_user_id ?: null;

        // Update the OIC assignment
        $approver->update(['oic_user_id' => $newOicUserId]);

        // Handle OIC flag updates
        if ($previousOicUserId && $previousOicUserId != $newOicUserId) {
            // Remove OIC flag from previous user if they're being replaced
            // But only if they're not OIC for any other approver
            $isOicElsewhere = LeaveApprover::where('oic_user_id', $previousOicUserId)
                ->where('id', '!=', $approver->id)
                ->exists();

            if (! $isOicElsewhere) {
                User::where('id', $previousOicUserId)->update(['is_oic' => false]);
            }
        }

        // Add OIC flag to new user if selected
        if ($newOicUserId) {
            User::where('id', $newOicUserId)->update(['is_oic' => true]);
        }

        $this->showOICModal = false;
        $this->reset(['oic_user_id', 'selectedApproverId']);

        $message = $newOicUserId ? 'OIC assigned successfully!' : 'OIC removed successfully!';

        $this->dispatch('swal', [
            'title' => $message,
            'icon' => 'success',
        ]);
    }

    public function toggleApproverStatus($approverId)
    {
        $approver = LeaveApprover::findOrFail($approverId);
        $approver->update(['is_active' => ! $approver->is_active]);

        $status = $approver->is_active ? 'activated' : 'deactivated';
        $this->dispatch('swal', [
            'title' => "Approver {$status} successfully!",
            'icon' => 'success',
        ]);
    }

    public function deleteApprover($approverId)
    {
        $approver = LeaveApprover::findOrFail($approverId);
        $approver->delete();

        $this->dispatch('swal', [
            'title' => 'Approver deleted successfully!',
            'icon' => 'success',
        ]);
    }

    public function closeModal()
    {
        $this->showApproverModal = false;
        $this->resetForm();
    }

    public function closeOICModal()
    {
        $this->showOICModal = false;
        $this->reset(['oic_user_id', 'selectedApproverId']);
    }

    private function resetForm()
    {
        $this->reset([
            'editingApproverId',
            'approver_level',
            'required_role',
            'user_id',
        ]);
    }

    public function getAvailableUsers($approverLevel)
    {
        $roles = $this->roleRequirements[$approverLevel] ?? [];

        if (empty($roles)) {
            return collect();
        }

        $query = User::whereIn('user_role', $roles);

        // Exclude supervisors that are already assigned to the other SV positions
        if (in_array($approverLevel, ['first', 'second'])) {
            $otherApproverLevel = $approverLevel === 'first' ? 'second' : 'first';

            $assignedUserIds = LeaveApprover::where('approver_level', $otherApproverLevel)
                ->whereNotNull('user_id')
                ->pluck('user_id');

            if ($assignedUserIds->isNotEmpty()) {
                $query->whereNotIn('id', $assignedUserIds);
            }
        }

        return $query->orderBy('name')->get();
    }

    public function getAvailableOICUsers()
    {
        // Show all employees for selection
        return User::where('user_role', 'emp')
            ->orderBy('name')
            ->get();
    }

    // Helper method to check if current user can manage OIC for an approver
    public function canManageOIC($approverId)
    {
        $approver = LeaveApprover::find($approverId);

        return $approver && $approver->user_id === Auth::id();
    }

    public function render()
    {
        $approvers = LeaveApprover::with(['user', 'oicUser'])
            ->orderBy('approver_level')
            ->get();

        return view('livewire.admin.leave-settings', [
            'approvers' => $approvers,
        ]);
    }
}
