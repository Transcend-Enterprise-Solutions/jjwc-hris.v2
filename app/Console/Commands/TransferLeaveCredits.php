<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\LeaveCredits;
use App\Models\LeaveCreditsCalculation;
use Carbon\Carbon;

class TransferLeaveCredits extends Command
{
    protected $signature = 'leave-credits:transfer';
    protected $description = 'Transfer leave credits from leave_credits_calculation to leave_credits for the current month';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
    
        // Fetch leave credits calculation for the current month
        $leaveCreditsCalculations = LeaveCreditsCalculation::where('month', $currentMonth)
            ->where('year', $currentYear)
            ->get();
    
        foreach ($leaveCreditsCalculations as $calculation) {
            $userId = $calculation->user_id;
            $leaveCreditsEarned = $calculation->leave_credits_earned;
            $totalCreditsEarned = $calculation->late_in_credits;
    
            // Fetch or create LeaveCredits record for the user
            $leaveCredits = LeaveCredits::firstOrCreate(
                ['user_id' => $userId],
                [
                    'vl_claimable_credits' => 0,
                    'sl_claimable_credits' => 0,
                    'vl_claimed_credits' => 0,
                    'sl_claimed_credits' => 0,
                ]
            );
    
            // Step 1: Subtract late_in_credits from vl_claimable_credits (but not below 0)
            $vlBeforeSubtraction = $leaveCredits->vl_claimable_credits;
            $subtractedCredits = min($totalCreditsEarned, $vlBeforeSubtraction); // Actual amount subtracted
            $leaveCredits->vl_claimable_credits = max(0, $vlBeforeSubtraction - $totalCreditsEarned);
    
            // Step 2: Add leave_credits_earned to vl_claimable_credits
            $leaveCredits->vl_claimable_credits += $leaveCreditsEarned;
    
            // Step 3: Add the subtracted credits to vl_claimed_credits
            $leaveCredits->vl_claimed_credits += $subtractedCredits;
    
            // Step 4: Add leave_credits_earned to sl_claimable_credits
            $leaveCredits->sl_claimable_credits += $leaveCreditsEarned;
    
            $leaveCredits->save();
        }
    
        $this->info('Leave credits have been successfully transferred and adjusted for the current month.');
    }
}