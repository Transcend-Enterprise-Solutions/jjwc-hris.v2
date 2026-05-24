<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\MonetizationRequest;
use App\Models\LeaveCredits;
use App\Models\Payrolls;
use App\Models\CosRegPayrolls;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('Leave Monetization')]
class AdminLeaveMonetization extends Component
{
    use WithPagination;

    public $showConfirmModal = false;
    public $showDisapproveModal = false;
    public $requestIdToApprove;
    public $requestId;
    public $search;

    public function openDisapproveModal($id)
    {
        $this->requestId = $id;
        $this->showDisapproveModal = true;
    }

    public function disapproveRequest()
    {
        $request = MonetizationRequest::find($this->requestId);
        $request->status = 'Disapproved';
        $request->save();

        $this->dispatch('swal', [
            'title' => "Request Disapproved Successfully!",
            'icon' => 'success'
        ]);

        $this->showDisapproveModal = false;
    }

    public function confirmApprove($requestId)
    {
        $this->requestIdToApprove = $requestId;
        $this->showConfirmModal = true;
    }

    public function approveRequest()
    {
        $request = MonetizationRequest::find($this->requestIdToApprove);

        if ($request) {
            $leaveCredits = LeaveCredits::where('user_id', $request->user_id)->first();

            if ($leaveCredits) {
                $leaveCredits->vl_claimable_credits -= $request->vl_credits_requested;
                $leaveCredits->sl_claimable_credits -= $request->sl_credits_requested;
                
                // $leaveCredits->vl_claimed_credits += $request->vl_credits_requested;
                // $leaveCredits->sl_claimed_credits += $request->sl_credits_requested;
                $leaveCredits->save();

                $vlMonetizedValue = $this->calculateMonetizedValue($request->vl_credits_requested, $request->user_id);
                $slMonetizedValue = $this->calculateMonetizedValue($request->sl_credits_requested, $request->user_id);

                $request->vl_monetize_credits = $vlMonetizedValue;
                $request->sl_monetize_credits = $slMonetizedValue;
                $request->status = 'Approved';
                $request->date_approved = now()->toDateString();
                $request->save();

                $this->dispatch('swal', [
                    'title' => "Request Approved Successfully!",
                    'icon' => 'success'
                ]);

                $this->showConfirmModal = false;
            }
        }
    }

    private function calculateMonetizedValue($creditsRequested, $userId)
    {
        $ratePerMonth = 0;

        if (Payrolls::where('user_id', $userId)->exists()) {
            $payroll = Payrolls::where('user_id', $userId)->first();
            if ($payroll) {
                $ratePerMonth = $payroll->rate_per_month;
            }
        } elseif (CosRegPayrolls::where('user_id', $userId)->exists()) {
            $cosPayroll = CosRegPayrolls::where('user_id', $userId)->first();
            if ($cosPayroll) {
                $ratePerMonth = $cosPayroll->rate_per_month;
            }
        }

        $monetizedValue = ($creditsRequested * 0.5) * ($ratePerMonth / 22);

        return $monetizedValue;
    }

    public function resetVariables()
    {
        $this->showDisapproveModal = false;
        $this->showConfirmModal = false;
    }

    public function render()
    {
        $monetizationRequests = MonetizationRequest::with('user')
            ->whereHas('user', function ($query) {
                $query->where('users.name', 'like', '%' . $this->search . '%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.admin.admin-leave-monetization', [
            'monetizationRequests' => $monetizationRequests,
        ]);
    }
}
