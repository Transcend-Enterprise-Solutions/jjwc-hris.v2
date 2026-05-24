<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\LeaveApplication;
use App\Models\LeaveCredits;
use Carbon\Carbon;

class DeductLeaveCredits extends Command
{
    protected $signature = 'leave:deduct-credits';
    protected $description = 'Deduct leave credits based on approved leave applications on the approved date';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $today = Carbon::today()->format('Y-m-d');
        // $today = '2025-03-07'; // For testing purposes
        $this->info("Running credit deduction for date: {$today}");

        // Fetch approved leave applications with "With Pay" status
        $leaveApplications = LeaveApplication::where('status', 'Approved')
            ->where('remarks', 'With Pay')
            ->get();

        $this->info("Found " . $leaveApplications->count() . " approved leave applications with 'With Pay'");

        foreach ($leaveApplications as $leave) {
            $this->info("Processing leave ID: {$leave->id} for user ID: {$leave->user_id}");
            
            // Skip if approved_dates is empty
            if (empty($leave->approved_dates)) {
                $this->warn("No approved dates found for leave ID: {$leave->id}");
                continue;
            }

            // Convert approved_dates string to an array and trim spaces
            $approvedDates = array_map('trim', explode(',', $leave->approved_dates));
            $this->info("Approved dates: " . implode(', ', $approvedDates));

            // Check if today's date exists in the approved dates (with different format considerations)
            $todayMatches = array_filter($approvedDates, function($date) use ($today) {
                // Try to normalize the date format
                try {
                    $formattedDate = Carbon::parse($date)->format('Y-m-d');
                    return $formattedDate === $today;
                } catch (\Exception $e) {
                    $this->error("Error parsing date: {$date}. Error: {$e->getMessage()}");
                    return false;
                }
            });

            if (!empty($todayMatches)) {
                $this->info("Today's date found in approved dates for leave ID: {$leave->id}");
                
                // Find the leave credits for the user
                $leaveCredit = LeaveCredits::where('user_id', $leave->user_id)->first();

                if ($leaveCredit) {
                    // Deduct only 1 day for today's leave date
                    $deductedDays = 1;
                    $leaveTypes = explode(',', $leave->type_of_leave);
                    
                    $this->info("Leave types: " . implode(', ', $leaveTypes));
                    
                    // Track if any deduction was made
                    $deductionMade = false;
                    
                    foreach ($leaveTypes as $leaveType) {
                        $leaveType = trim($leaveType);
                        
                        switch ($leaveType) {
                            case 'Vacation Leave':
                                if ($leaveCredit->vl_claimable_credits >= $deductedDays) {
                                    $leaveCredit->vl_claimable_credits -= $deductedDays;
                                    $leaveCredit->vl_claimed_credits += $deductedDays;
                                    $deductionMade = true;
                                    $this->info("Deducted {$deductedDays} day from Vacation Leave. New balance: {$leaveCredit->vl_claimable_credits}");
                                } else {
                                    $this->warn("Insufficient Vacation Leave credits for user ID {$leave->user_id}");
                                }
                                break;

                            case 'Sick Leave':
                                if ($leaveCredit->sl_claimable_credits >= $deductedDays) {
                                    $leaveCredit->sl_claimable_credits -= $deductedDays;
                                    $leaveCredit->sl_claimed_credits += $deductedDays;
                                    $deductionMade = true;
                                    $this->info("Deducted {$deductedDays} day from Sick Leave. New balance: {$leaveCredit->sl_claimable_credits}");
                                } else {
                                    $this->warn("Insufficient Sick Leave credits for user ID {$leave->user_id}");
                                }
                                break;

                            case 'Mandatory/Forced Leave':
                                if ($leaveCredit->fl_claimable_credits >= $deductedDays) {
                                    $leaveCredit->fl_claimable_credits -= $deductedDays;
                                    $leaveCredit->fl_claimed_credits += $deductedDays;
                                    
                                    // Also deduct from VL for Mandatory Leave
                                    if ($leaveCredit->vl_claimable_credits >= $deductedDays) {
                                        $leaveCredit->vl_claimable_credits -= $deductedDays;
                                        $leaveCredit->vl_claimed_credits += $deductedDays;
                                        $deductionMade = true;
                                        $this->info("Deducted {$deductedDays} day from Mandatory/Forced Leave. New FL balance: {$leaveCredit->fl_claimable_credits}, VL balance: {$leaveCredit->vl_claimable_credits}");
                                    } else {
                                        $this->warn("Insufficient VL credits for Mandatory Leave for user ID {$leave->user_id}");
                                    }
                                } else {
                                    $this->warn("Insufficient Forced Leave credits for user ID {$leave->user_id}");
                                }
                                break;

                            case 'Special Privilege Leave':
                                if ($leaveCredit->spl_claimable_credits >= $deductedDays) {
                                    $leaveCredit->spl_claimable_credits -= $deductedDays;
                                    $leaveCredit->spl_claimed_credits += $deductedDays;
                                    $deductionMade = true;
                                    $this->info("Deducted {$deductedDays} day from Special Privilege Leave. New balance: {$leaveCredit->spl_claimable_credits}");
                                } else {
                                    $this->warn("Insufficient Special Privilege Leave credits for user ID {$leave->user_id}");
                                }
                                break;

                            default:
                                $this->warn("Unknown leave type: {$leaveType}");
                                continue 2; // Skip if leave type does not match
                        }
                    }
                    
                    if ($deductionMade) {
                        try {
                            $leaveCredit->save();
                            $this->info("Successfully saved deductions for user ID {$leave->user_id}");
                            
                            // Log the deduction in a separate table if needed
                            // $this->logDeduction($leave->user_id, $today, $leave->type_of_leave, $deductedDays);
                        } catch (\Exception $e) {
                            $this->error("Failed to save deductions: " . $e->getMessage());
                        }
                    } else {
                        $this->warn("No deductions were made for user ID {$leave->user_id}");
                    }
                } else {
                    $this->error("No leave credits found for user ID {$leave->user_id}");
                }
            } else {
                $this->info("Today's date not found in approved dates for leave ID: {$leave->id}");
            }
        }

        $this->info('Leave credits deduction process completed.');
    }
    
    /*
    // Optional method to log deductions if needed
    private function logDeduction($userId, $date, $leaveType, $days)
    {
        // Implementation to log deductions in a separate table
    }
    */
}