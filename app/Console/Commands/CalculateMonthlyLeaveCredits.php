<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\EmployeesDtr;
use App\Models\LeaveCreditsCalculation;
use App\Models\LeaveCredits;
use Carbon\Carbon;

class CalculateMonthlyLeaveCredits extends Command
{
    protected $signature = 'calculate:monthly-leave-credits';
    protected $description = 'Calculate total late minutes and leave credits for each user and each month';

    public function handle()
    {
        // Get the just-ended month and year
        $date = Carbon::now();
        $month = $date->month;
        $year = $date->year;
    
        // Get users who have entries in EmployeesDtr for the specified month and year
        $activeUserIds = EmployeesDtr::whereMonth('date', $month)
            ->whereYear('date', $year)
            ->pluck('user_id')
            ->unique();
    
        $users = User::join('user_data', 'users.id', '=', 'user_data.user_id')
            ->whereIn('users.id', $activeUserIds)
            ->where('user_data.appointment', '!=', 'cos')
            ->select('users.*')  // Select only users table columns to avoid conflicts
            ->get();
    
        $this->info("Processing " . $users->count() . " active users for " . $date->format('F Y') . ".");
    
        foreach ($users as $user) {
            // Fetch the current vl_claimable_credits from the leave_credits table
            $currentVlBalance = LeaveCredits::where('user_id', $user->id)->value('vl_claimable_credits');
    
            // Get all DTR records for the user in the current month/year
            $dtrRecords = EmployeesDtr::where('user_id', $user->id)
                ->whereMonth('date', $month)
                ->whereYear('date', $year)
                ->whereIn('remarks', ['Late', 'Undertime', 'Late/Undertime'])
                ->get();
    
            $totalLateMinutes = 0;
            
            foreach ($dtrRecords as $dtr) {
                switch ($dtr->remarks) {
                    case 'Late/Undertime':
                        // Sum both late and undertime minutes
                        $totalLateMinutes += $this->timeToMinutes($dtr->late) + $this->timeToMinutes($dtr->ut);
                        break;
                    case 'Late':
                        // Add only late minutes
                        $totalLateMinutes += $this->timeToMinutes($dtr->late);
                        break;
                    case 'Undertime':
                        // Add only undertime minutes
                        $totalLateMinutes += $this->timeToMinutes($dtr->ut);
                        break;
                }
            }
    
            $totalLateTime = $this->convertMinutesToHoursAndMinutes($totalLateMinutes);
            $totalCreditsEarned = $this->calculateTotalCreditsEarned($totalLateMinutes);
    
            // Calculate the adjusted credits by subtracting late_in_credits from vl_claimable_credits
            $adjustedCredits = $currentVlBalance - $totalCreditsEarned;
    
            // Determine leave_credits_earned based on the adjusted credits
            if ($adjustedCredits < 0) {
                // If adjusted credits is negative, use the absolute value to determine leave_credits_earned
                $leaveCreditsEarned = $this->calculateLeaveCreditsEarned(abs($adjustedCredits));
            } else {
                // If adjusted credits is not negative, set leave_credits_earned to 1.250
                $leaveCreditsEarned = 1.250;
            }
    
            // Update or create the LeaveCreditsCalculation record
            LeaveCreditsCalculation::updateOrCreate(
                ['user_id' => $user->id, 'month' => $month, 'year' => $year],
                [
                    'late_time' => $totalLateTime,
                    'late_in_credits' => $totalCreditsEarned,
                    'latest_vl_credits' => $currentVlBalance,
                    'leave_credits_earned' => $leaveCreditsEarned,
                ]
            );
        }
    
        $this->info("Monthly leave credits calculated and updated successfully for " . $date->format('F Y') . ".");
    }

    private function timeToMinutes($time)
    {
        if (empty($time)) {
            return 0;
        }
        
        [$hours, $minutes] = explode(':', $time);
        return ($hours * 60) + $minutes;
    }
    
    private function calculateTotalCreditsEarned($totalLateMinutes)
    {
        // Assuming 8 hours (480 minutes) equals 1 credit
        $credits = $totalLateMinutes / 480;
        return round($credits, 3); // Round to 3 decimal places
    }
    
    private function calculateLeaveCreditsEarned($adjustedCredits)
    {
        $leaveCreditsTable = [
            ['DaysPresent' => 30.00, 'DaysAbsent' => 0.00, 'LeaveCreditsEarned' => 1.250],
            ['DaysPresent' => 29.50, 'DaysAbsent' => 0.50, 'LeaveCreditsEarned' => 1.229],
            ['DaysPresent' => 29.00, 'DaysAbsent' => 1.00, 'LeaveCreditsEarned' => 1.208],
            ['DaysPresent' => 28.50, 'DaysAbsent' => 1.50, 'LeaveCreditsEarned' => 1.188],
            ['DaysPresent' => 28.00, 'DaysAbsent' => 2.00, 'LeaveCreditsEarned' => 1.167],
            ['DaysPresent' => 27.50, 'DaysAbsent' => 2.50, 'LeaveCreditsEarned' => 1.146],
            ['DaysPresent' => 27.00, 'DaysAbsent' => 3.00, 'LeaveCreditsEarned' => 1.125],
            ['DaysPresent' => 26.50, 'DaysAbsent' => 3.50, 'LeaveCreditsEarned' => 1.104],
            ['DaysPresent' => 26.00, 'DaysAbsent' => 4.00, 'LeaveCreditsEarned' => 1.083],
            ['DaysPresent' => 25.50, 'DaysAbsent' => 4.50, 'LeaveCreditsEarned' => 1.063],
            ['DaysPresent' => 25.00, 'DaysAbsent' => 5.00, 'LeaveCreditsEarned' => 1.042],
            ['DaysPresent' => 24.50, 'DaysAbsent' => 5.50, 'LeaveCreditsEarned' => 1.021],
            ['DaysPresent' => 24.00, 'DaysAbsent' => 6.00, 'LeaveCreditsEarned' => 1.000],
            ['DaysPresent' => 23.50, 'DaysAbsent' => 6.50, 'LeaveCreditsEarned' => 0.979],
            ['DaysPresent' => 23.00, 'DaysAbsent' => 7.00, 'LeaveCreditsEarned' => 0.958],
            ['DaysPresent' => 22.50, 'DaysAbsent' => 7.50, 'LeaveCreditsEarned' => 0.938],
            ['DaysPresent' => 22.00, 'DaysAbsent' => 8.00, 'LeaveCreditsEarned' => 0.917],
            ['DaysPresent' => 21.50, 'DaysAbsent' => 8.50, 'LeaveCreditsEarned' => 0.896],
            ['DaysPresent' => 21.00, 'DaysAbsent' => 9.00, 'LeaveCreditsEarned' => 0.875],
            ['DaysPresent' => 20.50, 'DaysAbsent' => 9.50, 'LeaveCreditsEarned' => 0.854],
            ['DaysPresent' => 20.00, 'DaysAbsent' => 10.00, 'LeaveCreditsEarned' => 0.833],
            ['DaysPresent' => 19.50, 'DaysAbsent' => 10.50, 'LeaveCreditsEarned' => 0.813],
            ['DaysPresent' => 19.00, 'DaysAbsent' => 11.00, 'LeaveCreditsEarned' => 0.792],
            ['DaysPresent' => 18.50, 'DaysAbsent' => 11.50, 'LeaveCreditsEarned' => 0.771],
            ['DaysPresent' => 18.00, 'DaysAbsent' => 12.00, 'LeaveCreditsEarned' => 0.750],
            ['DaysPresent' => 17.50, 'DaysAbsent' => 12.50, 'LeaveCreditsEarned' => 0.729],
            ['DaysPresent' => 17.00, 'DaysAbsent' => 13.00, 'LeaveCreditsEarned' => 0.708],
            ['DaysPresent' => 16.50, 'DaysAbsent' => 13.50, 'LeaveCreditsEarned' => 0.687],
            ['DaysPresent' => 16.00, 'DaysAbsent' => 14.00, 'LeaveCreditsEarned' => 0.667],
            ['DaysPresent' => 15.50, 'DaysAbsent' => 14.50, 'LeaveCreditsEarned' => 0.646],
            ['DaysPresent' => 15.00, 'DaysAbsent' => 15.00, 'LeaveCreditsEarned' => 0.625],
            ['DaysPresent' => 14.50, 'DaysAbsent' => 15.50, 'LeaveCreditsEarned' => 0.604],
            ['DaysPresent' => 14.00, 'DaysAbsent' => 16.00, 'LeaveCreditsEarned' => 0.583],
            ['DaysPresent' => 13.50, 'DaysAbsent' => 16.50, 'LeaveCreditsEarned' => 0.562],
            ['DaysPresent' => 13.00, 'DaysAbsent' => 17.00, 'LeaveCreditsEarned' => 0.542],
            ['DaysPresent' => 12.50, 'DaysAbsent' => 17.50, 'LeaveCreditsEarned' => 0.521],
            ['DaysPresent' => 12.00, 'DaysAbsent' => 18.00, 'LeaveCreditsEarned' => 0.500],
            ['DaysPresent' => 11.50, 'DaysAbsent' => 18.50, 'LeaveCreditsEarned' => 0.479],
            ['DaysPresent' => 11.00, 'DaysAbsent' => 19.00, 'LeaveCreditsEarned' => 0.458],
            ['DaysPresent' => 10.50, 'DaysAbsent' => 19.50, 'LeaveCreditsEarned' => 0.437],
            ['DaysPresent' => 10.00, 'DaysAbsent' => 20.00, 'LeaveCreditsEarned' => 0.417],
            ['DaysPresent' => 9.50, 'DaysAbsent' => 20.50, 'LeaveCreditsEarned' => 0.396],
            ['DaysPresent' => 9.00, 'DaysAbsent' => 21.00, 'LeaveCreditsEarned' => 0.375],
            ['DaysPresent' => 8.50, 'DaysAbsent' => 21.50, 'LeaveCreditsEarned' => 0.354],
            ['DaysPresent' => 8.00, 'DaysAbsent' => 22.00, 'LeaveCreditsEarned' => 0.333],
            ['DaysPresent' => 7.50, 'DaysAbsent' => 22.50, 'LeaveCreditsEarned' => 0.312],
            ['DaysPresent' => 7.00, 'DaysAbsent' => 23.00, 'LeaveCreditsEarned' => 0.292],
            ['DaysPresent' => 6.50, 'DaysAbsent' => 23.50, 'LeaveCreditsEarned' => 0.271],
            ['DaysPresent' => 6.00, 'DaysAbsent' => 24.00, 'LeaveCreditsEarned' => 0.250],
            ['DaysPresent' => 5.50, 'DaysAbsent' => 24.50, 'LeaveCreditsEarned' => 0.229],
            ['DaysPresent' => 5.00, 'DaysAbsent' => 25.00, 'LeaveCreditsEarned' => 0.208],
            ['DaysPresent' => 4.50, 'DaysAbsent' => 25.50, 'LeaveCreditsEarned' => 0.187],
            ['DaysPresent' => 4.00, 'DaysAbsent' => 26.00, 'LeaveCreditsEarned' => 0.167],
            ['DaysPresent' => 3.50, 'DaysAbsent' => 26.50, 'LeaveCreditsEarned' => 0.146],
            ['DaysPresent' => 3.00, 'DaysAbsent' => 27.00, 'LeaveCreditsEarned' => 0.125],
            ['DaysPresent' => 2.50, 'DaysAbsent' => 27.50, 'LeaveCreditsEarned' => 0.104],
            ['DaysPresent' => 2.00, 'DaysAbsent' => 28.00, 'LeaveCreditsEarned' => 0.083],
            ['DaysPresent' => 1.50, 'DaysAbsent' => 28.50, 'LeaveCreditsEarned' => 0.062],
            ['DaysPresent' => 1.00, 'DaysAbsent' => 29.00, 'LeaveCreditsEarned' => 0.042],
            ['DaysPresent' => 0.50, 'DaysAbsent' => 29.50, 'LeaveCreditsEarned' => 0.021],
            ['DaysPresent' => 0.00, 'DaysAbsent' => 30.00, 'LeaveCreditsEarned' => 0.000],
        ];
    
        foreach ($leaveCreditsTable as $entry) {
            if ($adjustedCredits <= $entry['DaysAbsent']) {
                return $entry['LeaveCreditsEarned'];
            }
        }
    
        return 0; // Default value if no match is found
    }
    
    private function convertMinutesToHoursAndMinutes($totalMinutes)
    {
        $hours = intdiv($totalMinutes, 60);
        $minutes = $totalMinutes % 60;
        return sprintf('%02d:%02d', $hours, $minutes);
    }
}
