<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\LeaveCredits;
use Livewire\WithPagination;
use App\Models\User;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('Leave Credits')]
class AdminLeaveCredits extends Component
{
    use WithPagination;

    public $search = '';
    public $inputCredits = false;
    public $editCredits = false;
    public $employees = [];
    public $employee;
    public $editEmployeeCredits;
    public $vlClaimableCredits;
    public $vlClaimedCredits;
    public $slClaimableCredits;
    public $slClaimedCredits;
    // public $splClaimableCredits;
    // public $splClaimedCredits;
    // public $ctoClaimableCredits;
    // public $ctoClaimedCredits;
    public $selectedEmployeeId = null;
    public $processedEmployees = [];
    public $credits_inputted;
    public $pageSize = 10; 
    public $pageSizes = [10, 20, 30, 50, 100]; 

    public function openInputCredits()
    {
        $this->inputCredits = true;
    }

    public function closeInputCredits()
    {
        $this->inputCredits = false;
        $this->resetInputFields();
        $this->resetValidation();
    }

    public function resetInputFields()
    {
        $this->employee = null;
        $this->vlClaimableCredits = null;
        $this->vlClaimedCredits = null;
        $this->slClaimableCredits = null;
        $this->slClaimedCredits = null;
        // $this->splClaimableCredits = null;
        // $this->splClaimedCredits = null;
        // $this->ctoClaimableCredits = null;
        // $this->ctoClaimedCredits = null;
        $this->resetValidation();
    }

    public function resetVariables()
    {
        $this->employee = null;
        $this->vlClaimableCredits = null;
        $this->vlClaimedCredits = null;
        $this->slClaimableCredits = null;
        $this->slClaimedCredits = null;
        // $this->splClaimableCredits = null;
        // $this->splClaimedCredits = null;
        // $this->ctoClaimableCredits = null;
        // $this->ctoClaimedCredits = null;
        $this->resetValidation();
    }

    public function saveCredits()
    {
        $this->validate([
            'vlClaimableCredits' => 'required|numeric',
            'slClaimableCredits' => 'required|numeric',
            // 'splClaimableCredits' => 'required|numeric',
            // 'ctoClaimableCredits' => 'required|numeric',
        ]);

        if (is_null($this->employee) ||
            (is_null($this->vlClaimableCredits) && 
            is_null($this->slClaimableCredits) 
            // && 
            // is_null($this->splClaimableCredits) && 
            // is_null($this->ctoClaimableCredits)
            )
        ) {
            session()->flash('error', 'Please fill out all required fields.');
            return;
        }

        $leaveCredits = LeaveCredits::firstOrCreate(
            ['user_id' => $this->employee],
            [
                'vl_claimable_credits' => 0,
                'vl_claimed_credits' => 0,
                'sl_claimable_credits' => 0,
                'sl_claimed_credits' => 0,
                // 'spl_claimable_credits' => 0,
                // 'spl_claimed_credits' => 0,
                // 'cto_claimable_credits' => 0,
                'vl_total_credits' => 0,
                'sl_total_credits' => 0,
                // 'spl_total_credits' => 0,
            ]
        );

        // Update Vacation Leave credits
        if (!is_null($this->vlClaimableCredits)) {
            $leaveCredits->vl_claimable_credits += $this->vlClaimableCredits;

            $leaveCredits->vlbalance_brought_forward = $this->vlClaimableCredits; // Store inputted credits
            $leaveCredits->date_forwarded = Carbon::now(); // Store current date
        }
        if (!is_null($this->vlClaimedCredits)) {
            $leaveCredits->vl_claimed_credits += $this->vlClaimedCredits;
        }
        $leaveCredits->vl_total_credits = $leaveCredits->vl_claimable_credits + $leaveCredits->vl_claimed_credits;

        // Update Sick Leave credits
        if (!is_null($this->slClaimableCredits)) {
            $leaveCredits->sl_claimable_credits += $this->slClaimableCredits;

            $leaveCredits->slbalance_brought_forward = $this->slClaimableCredits; // Store inputted credits
            $leaveCredits->date_forwarded = Carbon::now(); // Store current date
        }
        if (!is_null($this->slClaimedCredits)) {
            $leaveCredits->sl_claimed_credits += $this->slClaimedCredits;
        }
        $leaveCredits->sl_total_credits = $leaveCredits->sl_claimable_credits + $leaveCredits->sl_claimed_credits;

        // Update Special Privilege Leave credits
        // if (!is_null($this->splClaimableCredits)) {
        //     $leaveCredits->spl_claimable_credits += $this->splClaimableCredits;
        // }
        // if (!is_null($this->splClaimedCredits)) {
        //     $leaveCredits->spl_claimed_credits += $this->splClaimedCredits;
        // }
        // $leaveCredits->spl_total_credits = $leaveCredits->spl_claimable_credits + $leaveCredits->spl_claimed_credits;

        // Update Special Privilege Leave credits
        // if (!is_null($this->ctoClaimableCredits)) {
        //     $leaveCredits->cto_claimable_credits += $this->ctoClaimableCredits;
        // }
        // if (!is_null($this->ctoClaimedCredits)) {
        //     $leaveCredits->cto_claimed_credits += $this->ctoClaimedCredits;
        // }
        // $leaveCredits->cto_total_credits = $leaveCredits->cto_claimable_credits + $leaveCredits->cto_claimed_credits;
        
        $leaveCredits->credits_inputted = 1;
        $leaveCredits->save();

        $this->selectedEmployeeId = null;

        $this->dispatch('swal', [
            'title' => "Credits added successfully!",
            'icon' => 'success'
        ]);

        $this->closeInputCredits();
    }

    public function openEditCredits($employeeId)
    {
        $this->editCredits = true;
        $this->editEmployeeCredits = LeaveCredits::where('user_id', $employeeId)->first();
        
        if ($this->editEmployeeCredits) {
            // Fetch the employee's leave credits
            $this->vlClaimableCredits = $this->editEmployeeCredits->vl_claimable_credits;
            $this->slClaimableCredits = $this->editEmployeeCredits->sl_claimable_credits;
            // $this->splClaimableCredits = $this->editEmployeeCredits->spl_claimable_credits;
            // $this->ctoClaimableCredits = $this->editEmployeeCredits->cto_claimable_credits;

            $this->credits_inputted = $this->editEmployeeCredits->credits_inputted;

            $this->selectedEmployeeId = $employeeId;
        } else {
            session()->flash('error', 'Leave credits not found for the selected employee.');
            $this->editCredits = false;
        }
    }

    public function closeEditCredits()
    {
        $this->editCredits = false;
        $this->resetInputFields();
    }

    public function updateCredits()
    {
        $this->validate([
            'vlClaimableCredits' => 'required|numeric',
            'slClaimableCredits' => 'required|numeric',
            // 'splClaimableCredits' => 'required|numeric',
            // 'ctoClaimableCredits' => 'required|numeric',
        ]);

        if (is_null($this->editEmployeeCredits)) {
            session()->flash('error', 'Please select an employee.');
            return;
        }

        $leaveCredits = LeaveCredits::where('user_id', $this->selectedEmployeeId)->first();

        if (!is_null($this->vlClaimableCredits)) {
            $leaveCredits->vl_claimable_credits = $this->vlClaimableCredits;
        }
        if (!is_null($this->slClaimableCredits)) {
            $leaveCredits->sl_claimable_credits = $this->slClaimableCredits;
        }
        // if (!is_null($this->splClaimableCredits)) {
        //     $leaveCredits->spl_claimable_credits = $this->splClaimableCredits;
        // }
        // if (!is_null($this->ctoClaimableCredits)) {
        //     $leaveCredits->cto_claimable_credits = $this->ctoClaimableCredits;
        // }

        $leaveCredits->save();

        $this->dispatch('swal', [
            'title' => "Leave Credits updated successfully!",
            'icon' => 'success'
        ]);

        $this->closeEditCredits();
    }

    public function render()
    {
        $this->processedEmployees = [];
        // Fetch employees who don't have credits inputted yet
        $this->employees = User::where('user_role', 'emp')
            ->whereDoesntHave('leaveCredits', function ($query) {
                $query->where('credits_inputted', 1);
            })
            ->get();
    
        $leaveCredits = LeaveCredits::with('user')
            ->whereHas('user', function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->paginate($this->pageSize);
    
        return view('livewire.admin.admin-leave-credits', [
            'leaveCredits' => $leaveCredits,
        ]);
    }
     
}
