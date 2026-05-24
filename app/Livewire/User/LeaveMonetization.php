<?php

namespace App\Livewire\User;

use Livewire\Component;
use App\Models\LeaveCredits;
use App\Models\MonetizationRequest;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('Leave Monetization')]
class LeaveMonetization extends Component
{
    use WithPagination;

    public $monetizationForm = false;
    public $vlCredits;
    public $slCredits;
    public $availableVLCredits;
    public $availableSLCredits;
    public $flClaimableCredits;

    public $activeTab = 'pending';

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    // protected $rules = [
    //     'vlCredits' => 'nullable|numeric|min:0',
    //     'slCredits' => 'nullable|numeric|min:0',
    // ];

    protected function rules()
    {
        // Get current leave credits
        $leaveCredits = LeaveCredits::where('user_id', auth()->id())->first();
        
        // Calculate actual VL credits by subtracting FL credits
        $actualVLCredits = max(($leaveCredits->vl_claimable_credits ?? 0) - ($leaveCredits->fl_claimable_credits ?? 0), 0);
        
        return [
            'vlCredits' => [
                'nullable',
                'numeric',
                'min:0',
                "max:$actualVLCredits",
            ],
            'slCredits' => [
                'nullable',
                'numeric',
                'min:0',
                "max:" . ($leaveCredits->sl_claimable_credits ?? 0),
            ],
        ];
    }

    public function openRequestForm()
    {
        $this->resetVariables();
        $this->monetizationForm = true;
    }

    public function closeRequestForm()
    {
        $this->monetizationForm = false;
    }

    public function resetVariables()
    {
        $this->vlCredits = null;
        $this->slCredits = null;
    }

    protected $messages = [
        'vlCredits.max' => 'You don\'t have enough VL credits to monetize.',
        'slCredits.max' => 'You don\'t have enough SL credits to monetize.',
    ];

    public function mount()
    {
        $this->loadAvailableCredits();
    }

    public function loadAvailableCredits()
    {
        $leaveCredits = LeaveCredits::where('user_id', auth()->id())->first();
        
        // Calculate actual VL credits by subtracting FL credits
        $this->availableVLCredits = max(($leaveCredits->vl_claimable_credits ?? 0) - ($leaveCredits->fl_claimable_credits ?? 0), 0);
        $this->availableSLCredits = $leaveCredits->sl_claimable_credits ?? 0;
        $this->flClaimableCredits = $leaveCredits->fl_claimable_credits ?? 0;
    }

    public function submitMonetizationRequest()
    {
        $this->validate();

        // Fetch the current user's leave credits
        $leaveCredits = LeaveCredits::where('user_id', auth()->id())->firstOrFail();

        // Calculate actual available VL credits
        $actualVLCredits = max($leaveCredits->vl_claimable_credits - $leaveCredits->fl_claimable_credits, 0);

        // Validate if the requested credits are available
        $requestedVL = $this->vlCredits ?? 0;
        $requestedSL = $this->slCredits ?? 0;

        if ($requestedVL > $actualVLCredits) {
            $this->addError('vlCredits', 'Insufficient VL credits (after subtracting FL credits).');
            return;
        }

        if ($requestedSL > $leaveCredits->sl_claimable_credits) {
            $this->addError('slCredits', 'Insufficient SL credits.');
            return;
        }

        // Save the monetization request with status 'Pending'
        MonetizationRequest::create([
            'user_id' => auth()->id(),
            'vl_credits_requested' => $requestedVL,
            'sl_credits_requested' => $requestedSL,
            'status' => 'Pending',
        ]);

        // Close the form
        $this->closeRequestForm();

        // Dispatch success notification
        $this->dispatch('swal', [
            'title' => "Monetization Request Submitted!",
            'text' => "VL Credits: $requestedVL, SL Credits: $requestedSL",
            'icon' => 'success'
        ]);
    }

    public function render()
    {
        $requests = MonetizationRequest::where('user_id', auth()->id())
                ->where('status', ucfirst($this->activeTab))  // Added this line
                ->orderBy('created_at', 'desc')
                ->paginate(10);

        return view('livewire.user.leave-monetization', [
            'requests' => $requests,
        ]);
    }
}

