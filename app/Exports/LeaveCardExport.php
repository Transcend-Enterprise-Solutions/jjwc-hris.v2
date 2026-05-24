<?php

namespace App\Exports;

use App\Models\LeaveApplication;
use App\Models\LeaveCredits;
use App\Models\LeaveCreditsCalculation;
use App\Models\OfficeDivisions;
use App\Models\Positions;
use App\Models\UserData;
use App\Models\OfficeDivisionUnits;
use App\Models\MonetizationRequest;
use App\Models\MonthlyCredits;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class LeaveCardExport
{
    protected $leaveApplicationId;
    protected $year;

    public function __construct($leaveApplicationId, $year)
    {
        $this->leaveApplicationId = $leaveApplicationId;
        $this->year = (int)$year;
    }

    private function setBoldLabelWithValue($sheet, $cell, $label, $value)
    {
        $richText = new RichText();
    
        $boldLabel = $richText->createTextRun($label);
        $boldLabel->getFont()->setBold(true)->setName('Arial')->setSize(12);
        $boldValue = $richText->createTextRun($value);
        $boldValue->getFont()->setBold(true)->setName('Arial')->setSize(12);
        $sheet->setCellValue($cell, $richText);
    }

    private function getMonthlyLeaveDetails($userId, $month, $leaveType)
    {
        // Get all approved leaves with pay for the specified type
        $leaves = LeaveApplication::where('user_id', $userId)
            ->where('type_of_leave', $leaveType)
            ->where('status', 'Approved')
            ->where('remarks', 'With Pay')
            ->whereNotNull('approved_dates')
            ->get();
    
        if ($leaves->isEmpty()) {
            return [
                'dates' => '',
                'total_days' => 0
            ];
        }
    
        $dates = [];
        $totalDays = 0;
    
        foreach ($leaves as $leave) {
            $approvedDatesString = trim($leave->approved_dates, '[]"');
            $approvedDates = array_map('trim', explode(',', $approvedDatesString));
    
            foreach ($approvedDates as $date) {
                $date = trim($date, '"\'');
                
                try {
                    $leaveDate = Carbon::parse($date);
                    
                    if ($leaveDate->year == $this->year && $leaveDate->month == $month) {
                        $dates[] = $leaveDate->format('d');
                        $totalDays += 1;
                    }
                } catch (\Exception $e) {
                    continue;
                }
            }
        }
    
        if (empty($dates)) {
            return [
                'dates' => '',
                'total_days' => 0
            ];
        }
    
        sort($dates, SORT_NUMERIC);
        $monthName = Carbon::create()->month($month)->format('M');
        $datesString = count($dates) > 0 ? $monthName . '. ' . implode(', ', $dates) : '';
    
        return [
            'dates' => $datesString,
            'total_days' => $totalDays
        ];
    }

    private function getMonthlyWithoutPayLeaveDetails($userId, $month, $leaveType)
    {
        // Get all approved leaves without pay for the specified type
        $leaves = LeaveApplication::where('user_id', $userId)
            ->where('type_of_leave', $leaveType)
            ->where('status', 'Approved')
            ->where('remarks', 'Without Pay')
            ->whereNotNull('approved_dates')
            ->get();
    
        if ($leaves->isEmpty()) {
            return [
                'dates' => '',
                'total_days' => 0
            ];
        }
    
        $dates = [];
        $totalDays = 0;
    
        foreach ($leaves as $leave) {
            $approvedDatesString = trim($leave->approved_dates, '[]"');
            $approvedDates = array_map('trim', explode(',', $approvedDatesString));
    
            foreach ($approvedDates as $date) {
                $date = trim($date, '"\'');
                
                try {
                    $leaveDate = Carbon::parse($date);
                    
                    if ($leaveDate->year == $this->year && $leaveDate->month == $month) {
                        $dates[] = $leaveDate->format('d');
                        $totalDays += 1;
                    }
                } catch (\Exception $e) {
                    continue;
                }
            }
        }
    
        if (empty($dates)) {
            return [
                'dates' => '',
                'total_days' => 0
            ];
        }
    
        sort($dates, SORT_NUMERIC);
        $monthName = Carbon::create()->month($month)->format('M');
        $datesString = count($dates) > 0 ? $monthName . '. ' . implode(', ', $dates) : '';
    
        return [
            'dates' => $datesString,
            'total_days' => $totalDays
        ];
    }

    private $lateTimeMatrix = [
        1 => 0.002, 2 => 0.004, 3 => 0.006, 4 => 0.008, 5 => 0.010,
        6 => 0.012, 7 => 0.015, 8 => 0.017, 9 => 0.019, 10 => 0.021,
        11 => 0.023, 12 => 0.025, 13 => 0.027, 14 => 0.029, 15 => 0.031,
        16 => 0.033, 17 => 0.035, 18 => 0.037, 19 => 0.040, 20 => 0.042,
        21 => 0.044, 22 => 0.046, 23 => 0.048, 24 => 0.050, 25 => 0.052,
        26 => 0.054, 27 => 0.056, 28 => 0.058, 29 => 0.060, 30 => 0.062,
        31 => 0.065, 32 => 0.067, 33 => 0.069, 34 => 0.071, 35 => 0.073,
        36 => 0.075, 37 => 0.077, 38 => 0.079, 39 => 0.081, 40 => 0.083,
        41 => 0.085, 42 => 0.087, 43 => 0.090, 44 => 0.092, 45 => 0.094,
        46 => 0.096, 47 => 0.098, 48 => 0.100, 49 => 0.102, 50 => 0.104,
        51 => 0.106, 52 => 0.108, 53 => 0.110, 54 => 0.112, 55 => 0.115,
        56 => 0.117, 57 => 0.119, 58 => 0.121, 59 => 0.123, 60 => 0.125
    ];

    private function getMonthlyLateDetails($userId, $month)
    {
        $lateRecord = LeaveCreditsCalculation::where('user_id', $userId)
            ->where('month', (string)$month)
            ->where('year', (string)$this->year)
            ->first();

        if (!$lateRecord || !$lateRecord->late_time) {
            return null;
        }

        // Parse the time string (HH:mm format)
        list($hours, $minutes) = explode(':', $lateRecord->late_time);
        $hours = (int)$hours;
        $minutes = (int)$minutes;

        // Calculate total deduction
        $deduction = 0;
        
        // Add hours deduction (0.125 per hour)
        if ($hours > 0) {
            $deduction += ($hours * 0.125);
        }

        // Add minutes deduction from matrix
        if ($minutes > 0) {
            $deduction += $this->lateTimeMatrix[$minutes];
        }

        return [
            'time' => $lateRecord->late_time,
            'deduction' => round($deduction, 3)
        ];
    }

    private function getMonthlyEarnedCredits($userId, $month)
    {
        $creditRecord = LeaveCreditsCalculation::where('user_id', $userId)
            ->where('month', (string)$month)
            ->where('year', (string)$this->year)
            ->first();

        return $creditRecord ? $creditRecord->leave_credits_earned : null;
    }

    private function formatLeaveDetails($leaveDetails, $leaveType, $month)
    {
        if (empty($leaveDetails['dates'])) {
            return '';
        }

        $monthName = Carbon::create()->month($month)->format('M');
        $dates = explode(', ', substr($leaveDetails['dates'], strlen($monthName . '. ')));
        return $leaveType . ' - ' . $monthName . '. ' . implode(', ', $dates);
    }

    private function getApprovedMonetizationRequests($userId, $year)
    {
        return MonetizationRequest::where('user_id', $userId)
            ->where('status', 'Approved')
            ->whereYear('date_approved', $year) // Use `date_approved` for filtering
            ->get();
    }

    private function formatMonetizationLeave($date, $vlCredits, $slCredits)
    {
        $month = Carbon::parse($date)->format('M');
        $day = Carbon::parse($date)->format('d');
        return "Monetization Leave - $month. $day";
    }

    public function export(): StreamedResponse
    {
        $user = Auth::user();
        
        // Fetch approved monetization requests for the year
        $monetizationRequests = $this->getApprovedMonetizationRequests($user->id, $this->year);
    
        // Fetching user data
        $position = Positions::find($user->position_id)->position ?? 'N/A';
        $department = OfficeDivisions::find($user->office_division_id)->office_division ?? 'N/A';
        $dateHired = UserData::where('user_id', $user->id)->value('date_hired');
    
        if ($dateHired) {
            $formattedDateHired = Carbon::createFromFormat('Y-m-d', $dateHired)->format('m/d/Y');
        } else {
            $formattedDateHired = 'N/A';
        }
    
        $templatePath = storage_path('app/public/leave_template/LEAVE-LEDGER-TEMPLATE.xlsx');
        $spreadsheet = IOFactory::load($templatePath);
        $sheet = $spreadsheet->getActiveSheet();
    
        $richText = new RichText();
        $yearText = $richText->createTextRun(strtoupper("FOR THE YEAR " . $this->year));
        $yearText->getFont()->setBold(true)->setName('Arial')->setSize(12);
        $sheet->setCellValue('A3', $richText);

        // Fill in the basic user information and set font size
        $this->setBoldLabelWithValue($sheet, 'B5', ': ', strtoupper($user->name ?? 'N/A'));
        $this->setBoldLabelWithValue($sheet, 'B6', ': ', strtoupper($formattedDateHired ?? 'N/A'));
        $this->setBoldLabelWithValue($sheet, 'I5', ': ', strtoupper($position ?? 'N/A'));
        $this->setBoldLabelWithValue($sheet, 'I6', ': ', strtoupper($department ?? 'N/A'));
    
        $previousYear = $this->year - 1;
        $previousDecBalance = MonthlyCredits::where('user_id', $user->id)
            ->where('year', $previousYear)
            ->where('month', 12)
            ->first();
    
        // Set the "Leave balance as of" text in D15
        $richText = new RichText();
        $balanceText = $richText->createTextRun("Leave balance as of Dec {$previousYear}");
        $balanceText->getFont()->setBold(true);
        $balanceText->getFont()->setName('Arial');
        $sheet->setCellValue('B11', $richText);
    
        // Set the balances
        if ($previousDecBalance) {
            $sheet->setCellValue('E11', $previousDecBalance->vl_latest_credits);
            $sheet->setCellValue('I11', $previousDecBalance->sl_latest_credits);
        } else {
            $sheet->setCellValue('E11', 0);
            $sheet->setCellValue('I11', 0);
        }
    
        // Get current balance from L15
        $currentVLBalance  = floatval($sheet->getCell('E11')->getValue());
        $currentSLBalance = floatval($sheet->getCell('I11')->getValue());
    
        // Array of months
        $months = [
            1 => 'JANUARY',
            2 => 'FEBRUARY',
            3 => 'MARCH',
            4 => 'APRIL',
            5 => 'MAY',
            6 => 'JUNE',
            7 => 'JULY',
            8 => 'AUGUST',
            9 => 'SEPTEMBER',
            10 => 'OCTOBER',
            11 => 'NOVEMBER',
            12 => 'DECEMBER'
        ];
    
        // Determine the last month to process
        $currentYear = Carbon::now()->year;
        $currentMonth = Carbon::now()->month;
    
        $lastMonthToProcess = ($this->year == $currentYear) ? $currentMonth - 1 : 12;
    
        // Process all months up to the last month to process
        $currentRow = 13;
        for ($month = 1; $month <= $lastMonthToProcess; $month++) {
            $monthName = $months[$month];
            $hasEntries = false;
            $isMonthDisplayed = false; // Flag to track if the month name has been displayed
    
            // Process Vacation Leave (VL)
            // $vlLeaves = $this->getMonthlyLeaveDetails($user->id, $month, 'Vacation Leave');
            // if (!empty($vlLeaves['dates'])) {
            //     // Display the month name only once
            //     if (!$isMonthDisplayed) {
            //         $sheet->setCellValue('A' . $currentRow, $monthName);
            //         $isMonthDisplayed = true;
            //     }
            //     $sheet->setCellValue('B' . $currentRow, $this->formatLeaveDetails($vlLeaves, 'VL', $month));
    
            //     // Handle VL balance with per-row excess
            //     if ($vlLeaves['total_days'] > 0) {
            //         $sheet->setCellValue('D' . $currentRow, $vlLeaves['total_days']);
            //         $newVLBalance = $currentVLBalance - $vlLeaves['total_days'];
    
            //         if ($newVLBalance < 0) {
            //             $sheet->setCellValue('F' . $currentRow, round(abs($newVLBalance), 3));
            //             $currentVLBalance = 0;
            //         } else {
            //             $currentVLBalance = $newVLBalance;
            //         }
    
            //         $sheet->setCellValue('E' . $currentRow, round($currentVLBalance, 3));
            //     }
    
            //     $currentRow++;
            //     $hasEntries = true;
            // }
    
            // // Process Sick Leave (SL)
            // $slLeaves = $this->getMonthlyLeaveDetails($user->id, $month, 'Sick Leave');
            // if (!empty($slLeaves['dates'])) {
            //     // Display the month name only once
            //     if (!$isMonthDisplayed) {
            //         $sheet->setCellValue('A' . $currentRow, $monthName);
            //         $isMonthDisplayed = true;
            //     }
            //     $sheet->setCellValue('B' . $currentRow, $this->formatLeaveDetails($slLeaves, 'SL', $month));
    
            //     // Handle SL balance with per-row excess
            //     if ($slLeaves['total_days'] > 0) {
            //         $sheet->setCellValue('H' . $currentRow, $slLeaves['total_days']);
            //         $newSLBalance = $currentSLBalance - $slLeaves['total_days'];
    
            //         if ($newSLBalance < 0) {
            //             $sheet->setCellValue('J' . $currentRow, round(abs($newSLBalance), 3));
            //             $currentSLBalance = 0;
            //         } else {
            //             $currentSLBalance = $newSLBalance;
            //         }
    
            //         $sheet->setCellValue('I' . $currentRow, round($currentSLBalance, 3));
            //     }
    
            //     $currentRow++;
            //     $hasEntries = true;
            // }
            // Process Vacation Leave (VL) - With Pay
            $vlLeaves = $this->getMonthlyLeaveDetails($user->id, $month, 'Vacation Leave');
            if (!empty($vlLeaves['dates'])) {
                // Display the month name only once
                if (!$isMonthDisplayed) {
                    $sheet->setCellValue('A' . $currentRow, $monthName);
                    $isMonthDisplayed = true;
                }
                $sheet->setCellValue('B' . $currentRow, $this->formatLeaveDetails($vlLeaves, 'VL', $month));
                $sheet->getStyle('B' . $currentRow)->getAlignment()->setWrapText(true); // Enable text wrapping
                $sheet->getRowDimension($currentRow)->setRowHeight(-1); // Auto-adjust row height

                // Handle VL balance with per-row excess
                if ($vlLeaves['total_days'] > 0) {
                    $sheet->setCellValue('D' . $currentRow, $vlLeaves['total_days']);
                    $newVLBalance = $currentVLBalance - $vlLeaves['total_days'];

                    if ($newVLBalance < 0) {
                        $sheet->setCellValue('F' . $currentRow, round(abs($newVLBalance), 3));
                        $sheet->getStyle('F' . $currentRow)
                            ->getFont()
                            ->getColor()
                            ->setARGB('FFFF0000'); // Red color
                        $currentVLBalance = 0;
                    } else {
                        $currentVLBalance = $newVLBalance;
                    }

                    $sheet->setCellValue('E' . $currentRow, round($currentVLBalance, 3));
                }

                $currentRow++;
                $hasEntries = true;
            }

            // Process Vacation Leave (VL) - Without Pay
            $vlWithoutPayLeaves = $this->getMonthlyWithoutPayLeaveDetails($user->id, $month, 'Vacation Leave');
            if (!empty($vlWithoutPayLeaves['dates'])) {
                // Display the month name only once
                if (!$isMonthDisplayed) {
                    $sheet->setCellValue('A' . $currentRow, $monthName);
                    $isMonthDisplayed = true;
                }
                $sheet->setCellValue('B' . $currentRow, $this->formatLeaveDetails($vlWithoutPayLeaves, 'VL', $month));
                $sheet->getStyle('B' . $currentRow)->getAlignment()->setWrapText(true); // Enable text wrapping
                $sheet->getRowDimension($currentRow)->setRowHeight(-1); // Auto-adjust row height

                // Display Without Pay leaves in column F
                $sheet->setCellValue('F' . $currentRow, $vlWithoutPayLeaves['total_days']);
                $currentRow++;
                $hasEntries = true;
            }

            // Process Sick Leave (SL) - With Pay
            $slLeaves = $this->getMonthlyLeaveDetails($user->id, $month, 'Sick Leave');
            if (!empty($slLeaves['dates'])) {
                // Display the month name only once
                if (!$isMonthDisplayed) {
                    $sheet->setCellValue('A' . $currentRow, $monthName);
                    $isMonthDisplayed = true;
                }
                $sheet->setCellValue('B' . $currentRow, $this->formatLeaveDetails($slLeaves, 'SL', $month));
                $sheet->getStyle('B' . $currentRow)->getAlignment()->setWrapText(true); // Enable text wrapping
                $sheet->getRowDimension($currentRow)->setRowHeight(-1); // Auto-adjust row height

                // Handle SL balance with per-row excess
                if ($slLeaves['total_days'] > 0) {
                    $sheet->setCellValue('H' . $currentRow, $slLeaves['total_days']);
                    $newSLBalance = $currentSLBalance - $slLeaves['total_days'];

                    if ($newSLBalance < 0) {
                        $sheet->setCellValue('J' . $currentRow, round(abs($newSLBalance), 3));
                        $sheet->getStyle('J' . $currentRow)
                            ->getFont()
                            ->getColor()
                            ->setARGB('FFFF0000'); // Red color
                        $currentSLBalance = 0;
                    } else {
                        $currentSLBalance = $newSLBalance;
                    }

                    $sheet->setCellValue('I' . $currentRow, round($currentSLBalance, 3));
                }

                $currentRow++;
                $hasEntries = true;
            }

            // Process Sick Leave (SL) - Without Pay
            $slWithoutPayLeaves = $this->getMonthlyWithoutPayLeaveDetails($user->id, $month, 'Sick Leave');
            if (!empty($slWithoutPayLeaves['dates'])) {
                // Display the month name only once
                if (!$isMonthDisplayed) {
                    $sheet->setCellValue('A' . $currentRow, $monthName);
                    $isMonthDisplayed = true;
                }
                $sheet->setCellValue('B' . $currentRow, $this->formatLeaveDetails($slWithoutPayLeaves, 'SL', $month));
                $sheet->getStyle('B' . $currentRow)->getAlignment()->setWrapText(true); // Enable text wrapping
                $sheet->getRowDimension($currentRow)->setRowHeight(-1); // Auto-adjust row height

                // Display Without Pay leaves in column J
                $sheet->setCellValue('J' . $currentRow, $slWithoutPayLeaves['total_days']);
                $currentRow++;
                $hasEntries = true;
            }
    
            // Process Mandatory/Forced Leave (ML)
            $mlLeaves = $this->getMonthlyLeaveDetails($user->id, $month, 'Mandatory/Forced Leave');
            if (!empty($mlLeaves['dates'])) {
                // Display the month name only once
                if (!$isMonthDisplayed) {
                    $sheet->setCellValue('A' . $currentRow, $monthName);
                    $isMonthDisplayed = true;
                }
                $sheet->setCellValue('B' . $currentRow, $this->formatLeaveDetails($mlLeaves, 'ML', $month));
    
                // Handle ML balance (deduct from VL balance)
                if ($mlLeaves['total_days'] > 0) {
                    $sheet->setCellValue('D' . $currentRow, $mlLeaves['total_days']);
                    $newVLBalance = $currentVLBalance - $mlLeaves['total_days'];
    
                    if ($newVLBalance < 0) {
                        $sheet->setCellValue('F' . $currentRow, round(abs($newVLBalance), 3));
                        $currentVLBalance = 0;
                    } else {
                        $currentVLBalance = $newVLBalance;
                    }
    
                    $sheet->setCellValue('E' . $currentRow, round($currentVLBalance, 3));
                }
    
                $currentRow++;
                $hasEntries = true;
            }
    
            // Process Special Privilege Leave (SPL)
            $splLeaves = $this->getMonthlyLeaveDetails($user->id, $month, 'Special Privilege Leave');
            if (!empty($splLeaves['dates'])) {
                // Display the month name only once
                if (!$isMonthDisplayed) {
                    $sheet->setCellValue('A' . $currentRow, $monthName);
                    $isMonthDisplayed = true;
                }
                $sheet->setCellValue('B' . $currentRow, $this->formatLeaveDetails($splLeaves, 'SPL', $month));
    
                // No deductions for SPL
                $currentRow++;
                $hasEntries = true;
            }
    
            // Process undertime/lates (VL only)
            $monthLates = $this->getMonthlyLateDetails($user->id, $month);
            if ($monthLates && $monthLates['deduction'] > 0) { // Only display if deduction > 0
                // Display the month name only once
                if (!$isMonthDisplayed) {
                    $sheet->setCellValue('A' . $currentRow, $monthName);
                    $isMonthDisplayed = true;
                }
                $sheet->setCellValue('B' . $currentRow, 'Undertime/Late');
                $sheet->setCellValue('D' . $currentRow, $monthLates['deduction']);
    
                $newVLBalance = $currentVLBalance - $monthLates['deduction'];
                if ($newVLBalance < 0) {
                    $sheet->setCellValue('F' . $currentRow, round(abs($newVLBalance), 3));
                    $currentVLBalance = 0;
                } else {
                    $currentVLBalance = $newVLBalance;
                }
    
                $sheet->setCellValue('E' . $currentRow, round($currentVLBalance, 3));
                $currentRow++;
                $hasEntries = true;
            }
    
            // Process approved monetization requests
            foreach ($monetizationRequests as $request) {
                $approvalDate = Carbon::parse($request->date_approved);
                if ($approvalDate->year == $this->year && $approvalDate->month == $month) {
                    // Display the month name only once
                    if (!$isMonthDisplayed) {
                        $sheet->setCellValue('A' . $currentRow, $monthName);
                        $isMonthDisplayed = true;
                    }

                    // Format the monetization leave entry
                    $sheet->setCellValue('B' . $currentRow, $this->formatMonetizationLeave($request->date_approved, $request->vl_credits_requested, $request->sl_credits_requested));

                    // Deduct VL credits
                    if ($request->vl_credits_requested > 0) {
                        $sheet->setCellValue('D' . $currentRow, $request->vl_credits_requested);
                        $newVLBalance = $currentVLBalance - $request->vl_credits_requested;

                        if ($newVLBalance < 0) {
                            $sheet->setCellValue('F' . $currentRow, round(abs($newVLBalance), 3));
                            $currentVLBalance = 0;
                        } else {
                            $currentVLBalance = $newVLBalance;
                        }

                        $sheet->setCellValue('E' . $currentRow, round($currentVLBalance, 3));
                    }

                    // Deduct SL credits
                    if ($request->sl_credits_requested > 0) {
                        $sheet->setCellValue('H' . $currentRow, $request->sl_credits_requested);
                        $newSLBalance = $currentSLBalance - $request->sl_credits_requested;

                        if ($newSLBalance < 0) {
                            $sheet->setCellValue('J' . $currentRow, round(abs($newSLBalance), 3));
                            $currentSLBalance = 0;
                        } else {
                            $currentSLBalance = $newSLBalance;
                        }

                        $sheet->setCellValue('I' . $currentRow, round($currentSLBalance, 3));
                    }

                    // Apply Blue, Accent 1, Darker 25% font color to columns B to J for the Monetization Leave row
                    $sheet->getStyle('B' . $currentRow . ':J' . $currentRow)
                        ->getFont()
                        ->getColor()
                        ->setARGB('FF5B9BD5'); // Blue, Accent 1, Darker 25%

                    $currentRow++;
                    $hasEntries = true;
                }
            }
    
            // Process earned credits (affects both VL and SL)
            $earnedCredits = $this->getMonthlyEarnedCredits($user->id, $month);
            if ($earnedCredits !== null) {
                // Display the month name only once
                if (!$isMonthDisplayed) {
                    $sheet->setCellValue('A' . $currentRow, $monthName);
                    $isMonthDisplayed = true;
                }
                $sheet->setCellValue('B' . $currentRow, 'Earned Credits');
    
                // Add earned credits to both VL and SL
                $sheet->setCellValue('C' . $currentRow, $earnedCredits); // VL earned
                $currentVLBalance += $earnedCredits;
                $sheet->setCellValue('E' . $currentRow, round($currentVLBalance, 3));
    
                $sheet->setCellValue('G' . $currentRow, $earnedCredits); // SL earned
                $currentSLBalance += $earnedCredits;
                $sheet->setCellValue('I' . $currentRow, round($currentSLBalance, 3));
    
                $currentRow++;
                $hasEntries = true;
            }
    
            // Add a blank row between months if there were any entries
            if ($hasEntries && $month < $lastMonthToProcess) {
                $currentRow++;
            }
        }

        // Determine the last row with data in column A
        $lastRow = $currentRow - 1; // Subtract 1 because $currentRow is incremented after the last data row

        // Apply borders to cells from A10 to K{lastRow}
        $borderStyle = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ];
    
        // Apply borders to the range A10:K{lastRow}
        $sheet->getStyle('A10:K' . $lastRow)->applyFromArray($borderStyle);
    
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        return new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="LEAVE LEDGER ' . $this->year . '.xlsx"',
        ]);
    }

    private function calculateBalanceBroughtForward($userId, Carbon $startDate)
    {
        // Get previous year's December balance
        $previousYear = $this->year - 1;
        $previousDecBalance = MonthlyCredits::where('user_id', $userId)
            ->where('year', $previousYear)
            ->where('month', 12)
            ->first();

        if ($previousDecBalance) {
            return [
                'vl' => $previousDecBalance->vl_latest_credits,
                'sl' => $previousDecBalance->sl_latest_credits,
                'found_date' => $startDate->format('Y-m-d')
            ];
        }

        return [
            'vl' => 0,
            'sl' => 0,
            'found_date' => null
        ];
    }
}