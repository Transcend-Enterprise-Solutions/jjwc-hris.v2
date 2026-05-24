<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\LeaveCredits;
use App\Models\MonthlyCredits;
use Carbon\Carbon;

class TotalMonthlyCredits extends Command
{
    protected $signature = 'credits:calculate-monthly';
    protected $description = 'Store monthly VL and SL claimable credits to monthly_credits table.';

    public function handle()
    {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // Get all users with leave credits
        $leaveCredits = LeaveCredits::all();

        foreach ($leaveCredits as $credit) {
            // Create or update monthly credits record
            $monthlyCredits = MonthlyCredits::firstOrCreate(
                [
                    'user_id' => $credit->user_id,
                    'month' => $currentMonth,
                    'year' => $currentYear
                ],
                [
                    'vl_latest_credits' => 0,
                    'vl_latest_claimed' => 0,
                    'sl_latest_credits' => 0,
                    'sl_latest_claimed' => 0
                ]
            );

            // Update the latest credits with claimable credits
            $monthlyCredits->update([
                'vl_latest_credits' => $credit->vl_claimable_credits,
                'vl_latest_claimed' => $credit->vl_claimed_credits,
                'sl_latest_credits' => $credit->sl_claimable_credits,
                'sl_latest_claimed' => $credit->sl_claimed_credits,
            ]);

            // $this->info("Updated credits for user_id: {$credit->user_id} - VL: {$credit->vl_claimable_credits}, SL: {$credit->sl_claimable_credits}");
        }

        $this->info('Monthly credits calculation completed successfully.');
    }
}