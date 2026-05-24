<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\LeaveCredits; // Adjust based on your model namespace

class CalculateTotalCredits extends Command
{
    // Command signature and description
    protected $signature = 'credits:calculate';
    protected $description = 'Calculate total credits for VL, SL, and SPL';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // Fetch all leave credits records (adjust query as necessary)
        $leaveCredits = LeaveCredits::all();

        foreach ($leaveCredits as $credits) {
            // Calculate totals for VL, SL, and SPL
            $credits->vl_total_credits = $credits->vl_claimable_credits + $credits->vl_claimed_credits;
            $credits->sl_total_credits = $credits->sl_claimable_credits + $credits->sl_claimed_credits;
            $credits->spl_total_credits = $credits->spl_claimable_credits + $credits->spl_claimed_credits;

            // Save the updated values
            $credits->save();
        }

        // Output a success message
        $this->info('Total credits calculated and updated successfully.');
    }
}
