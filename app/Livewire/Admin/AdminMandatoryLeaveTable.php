<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\MandatoryFormRequest;
use Carbon\Carbon;

class AdminMandatoryLeaveTable extends Component
{
    use WithPagination;

    public $activeTab = 'pending';
    public $search = '';
    public $pageSize = 5; 
    public $pageSizes = [5, 10, 20, 30, 50, 100]; 

    public $selectedLeaveId;
    public $showApproveModal = false;
    public $showDisapproveModal = false;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function confirmApproval($id)
    {
        $this->selectedLeaveId = $id;
        $this->showApproveModal = true;
    }

    public function confirmDisapproval($id)
    {
        $this->selectedLeaveId = $id;
        $this->showDisapproveModal = true;
    }

    public function approveLeave()
    {
        if ($this->selectedLeaveId) {
            $leave = MandatoryFormRequest::find($this->selectedLeaveId);
            if ($leave) {
                $leave->status = 'approved';
                $leave->date_completed = Carbon::now();
                $leave->approved_by = auth()->id();
                $leave->save();
            }
        }

        $this->showApproveModal = false;
        $this->selectedLeaveId = null;
    }

    public function disapproveLeave()
    {
        if ($this->selectedLeaveId) {
            $leave = MandatoryFormRequest::find($this->selectedLeaveId);
            if ($leave) {
                $leave->status = 'disapproved';
                $leave->date_completed = null;
                $leave->save();
            }
        }

        $this->showDisapproveModal = false;
        $this->selectedLeaveId = null;
    }

    public function resetVariables()
    {
        $this->showApproveModal = false;
        $this->showDisapproveModal = false;
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function render()
    {
        $mandatoryLeaves = MandatoryFormRequest::with('user')
            ->whereHas('user', function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->when($this->activeTab === 'pending', function ($query) {
                return $query->where('status', 'pending');
            })
            ->when($this->activeTab === 'approved', function ($query) {
                return $query->where('status', 'approved');
            })
            ->when($this->activeTab === 'disapproved', function ($query) {
                return $query->where('status', 'disapproved');
            })
            ->orderBy('date_requested', 'desc')
            ->paginate($this->pageSize);

        return view('livewire.admin.admin-mandatory-leave-table', [
            'mandatoryLeaves' => $mandatoryLeaves
        ]);
    }
}
