<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\LeaveApplication;
use App\Exports\LeaveCardExport; 
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('Leave Records')]
class AdminLeaveRecords extends Component
{
    use WithPagination;

    public $search = '';
    public $activeTab = 'pending';
    public $pageSize = 5; 
    public $pageSizes = [5, 10, 20, 30, 50, 100]; 

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function render()
    {
        $leaveApplications = LeaveApplication::query()
            ->whereHas('user', function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
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

        return view('livewire.admin.admin-leave-records', [
            'leaveApplications' => $leaveApplications,
        ]);
    }
}
