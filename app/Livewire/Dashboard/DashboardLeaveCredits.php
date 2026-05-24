<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\LeaveCredits;
use Illuminate\Support\Facades\Auth;

class DashboardLeaveCredits extends Component
{
    public $vlClaimableCredits;
    public $slClaimableCredits;
    public $splClaimableCredits;
    // public $ctoCredits;
    public $flClaimableCredits;

    public function render()
    {
        $leaveCredits = LeaveCredits::where('user_id', Auth::id())->first();

        // Assign values to the component's properties
        $this->vlClaimableCredits = $leaveCredits->vl_claimable_credits ?? 0;
        $this->slClaimableCredits = $leaveCredits->sl_claimable_credits ?? 0;
        $this->splClaimableCredits = $leaveCredits->spl_claimable_credits ?? 0;
        // $this->ctoCredits = $leaveCredits->cto_claimable_credits ?? 0;
        $this->flClaimableCredits = $leaveCredits->fl_claimable_credits ?? 0;

        return view('livewire.dashboard.dashboard-leave-credits', [
            'vlClaimableCredits' => $this->vlClaimableCredits,
            'slClaimableCredits' => $this->slClaimableCredits,
            'splClaimableCredits' => $this->splClaimableCredits,
            // 'ctoCredits' => $this->ctoCredits,
            'flClaimableCredits' => $this->flClaimableCredits,
        ]);
    }
}

