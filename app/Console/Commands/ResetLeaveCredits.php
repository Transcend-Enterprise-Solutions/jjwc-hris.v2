<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\LeaveCredits;

class ResetLeaveCredits extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leave-credits:reset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Resets leave credits at the start of each year while transferring used FL credits into VL credits.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Get all users' leave credits
        $leaveCreditsList = LeaveCredits::all();

        foreach ($leaveCreditsList as $leaveCredits) {
            // Get the used FL credits from `fl_claimed_credits`
            $fl_used = $leaveCredits->fl_claimed_credits;

            // Transfer used FL credits into VL credits
            $leaveCredits->vl_claimable_credits += $fl_used;

            // Reset FL and SPL credits
            $leaveCredits->fl_claimable_credits = 5;
            $leaveCredits->fl_claimed_credits = 0;
            $leaveCredits->spl_claimable_credits = 2;
            $leaveCredits->spl_claimed_credits = 0;

            // Save the updated leave credits
            $leaveCredits->save();
        }

        $this->info('Leave credits have been reset successfully. Used FL credits have been added to VL credits.');
        return Command::SUCCESS;
    }
}
