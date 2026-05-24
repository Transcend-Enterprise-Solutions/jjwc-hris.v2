<?php
namespace App\Jobs;
use App\Models\{
Transaction,
TransactionWFH,
User,
UserData,
DTRSchedule,
EmployeesDtr,
Holiday,
LeaveApplication,
OfficialBusiness
};
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class AutoSaveDtrRecordsMonthly implements ShouldQueue
{
use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

protected Carbon $startDate;
protected Carbon $endDate;

public function __construct()
    {
$this->startDate = Carbon::now()->startOfMonth();
$this->endDate = Carbon::now()->endOfMonth();
    }

public function handle()
    {
$this->logJobStart();

try {
$this->processAllEmployees();
$this->logJobSuccess();
        } catch (\Exception $e) {
$this->logJobError($e);
        }
    }

protected function processAllEmployees(): void
    {
User::where('user_role', 'emp')->chunk(100, function ($users) {
foreach ($users as $user) {
$this->processEmployeeMonth($user);
            }
        });
    }

protected function processEmployeeMonth(User $user): void
    {
Log::info("Processing user: {$user->emp_code}");

$datePeriod = CarbonPeriod::create($this->startDate, $this->endDate);

foreach ($datePeriod as $date) {
$this->processEmployeeDay($user, $date);
        }
    }

protected function processEmployeeDay(User $user, Carbon $date): void
    {
$currentDate = $date->toDateString();

$schedule = $this->getUserSchedule($user->emp_code, $currentDate);
$isWFH = $this->isWorkFromHomeDay($schedule, $date);

$transactions = $this->getTransactionsForDay($user->emp_code, $currentDate, $isWFH, $schedule);

$approvedLeaves = $this->getApprovedLeaves($user->id, $currentDate);
$pendingApplications = $this->getPendingApplications($user->id, $currentDate);

$this->logTransactionCount($user->emp_code, $currentDate, $transactions->count());

$calculatedData = $this->calculateTimeRecords($transactions, $user->emp_code, $currentDate, $approvedLeaves, $pendingApplications, $user, $isWFH);
$this->logCalculatedData($user->emp_code, $currentDate, $calculatedData);

$this->saveDtrRecord($user, $currentDate, $calculatedData);
    }

protected function getUserSchedule(string $empCode, string $date): ?DTRSchedule
    {
return DTRSchedule::where('emp_code', $empCode)
            ->whereDate('start_date', '<=', $date)
            ->whereDate('end_date', '>=', $date)
            ->first();
    }

protected function isWorkFromHomeDay(?DTRSchedule $schedule, Carbon $date): bool
    {
if (!$schedule || !$schedule->wfh_days) {
return false;
        }

$wfhDays = array_map('ucfirst', array_map('trim', explode(',', $schedule->wfh_days)));
return in_array($date->format('l'), $wfhDays);
    }

protected function isRestDay(?DTRSchedule $schedule, Carbon $date): bool
    {
if (!$schedule || !$schedule->rest_days) {
return false;
        }

$restDays = array_map('ucfirst', array_map('trim', explode(',', $schedule->rest_days)));
return in_array($date->format('l'), $restDays);
    }

protected function getTransactionsForDay(string $empCode, string $date, bool $isWFH, ?DTRSchedule $schedule)
    {
$transactionModel = $isWFH ? TransactionWFH::class : Transaction::class;

$transactions = collect();

// For 24-hour schedules, get a wider range of transactions
if ($schedule && $schedule->is_24hours) {
// Get transactions from 2 days before to 2 days after to ensure we catch all related punches
$startDate = Carbon::parse($date)->subDays(2)->toDateString();
$endDate = Carbon::parse($date)->addDays(2)->toDateString();

$rangeTransactions = $transactionModel::where('emp_code', $empCode)
                ->whereDate('punch_time', '>=', $startDate)
                ->whereDate('punch_time', '<=', $endDate)
                ->orderBy('punch_time')
                ->get();

$transactions = $transactions->merge($rangeTransactions);

Log::info("24H - Got transactions from {$startDate} to {$endDate}: " . $rangeTransactions->count());
        } else {
// Get transactions for current date only for non-24hour schedules
$currentDayTransactions = $transactionModel::where('emp_code', $empCode)
                ->whereDate('punch_time', $date)
                ->orderBy('punch_time')
                ->get();

$transactions = $transactions->merge($currentDayTransactions);

// For overnight schedules, also get next day transactions
if ($schedule && $schedule->is_overnight) {
$nextDay = Carbon::parse($date)->addDay()->toDateString();

$nextDayTransactions = $transactionModel::where('emp_code', $empCode)
                    ->whereDate('punch_time', $nextDay)
                    ->orderBy('punch_time')
                    ->get();

$transactions = $transactions->merge($nextDayTransactions);
            }
        }

return $transactions->sortBy('punch_time');
    }

protected function getApprovedLeaves(int $userId, string $date)
    {
return LeaveApplication::where('user_id', $userId)
            ->where('status', 'Approved')
            ->whereRaw("FIND_IN_SET(?, approved_dates) > 0", [$date])
            ->get();
    }

protected function getPendingApplications(int $userId, string $date): array
    {
$pendingLeaves = LeaveApplication::where('user_id', $userId)
            ->where('status', 'pending')
            ->whereRaw("FIND_IN_SET(?, list_of_dates) > 0", [$date])
            ->exists();

$pendingOB = OfficialBusiness::where('user_id', $userId)
            ->where('status', 'pending')
            ->whereDate('date', $date)
            ->exists();

return [
'has_pending_leave' => $pendingLeaves,
'has_pending_ob' => $pendingOB,
'has_any_pending' => $pendingLeaves || $pendingOB
        ];
    }

/**
     * Get the user's region ID from UserData
     * Priority: permanent_selectedRegion > residential_selectedRegion
     */
protected function getUserRegionId(User $user): ?string
    {
$userData = UserData::where('user_id', $user->id)->first();
        
if (!$userData) {
Log::info("No UserData found for user {$user->emp_code}");
return null;
        }

// Use permanent region if available, otherwise residential region
$regionId = $userData->permanent_selectedRegion ?? $userData->residential_selectedRegion;
        
Log::info("User {$user->emp_code} region_id: " . ($regionId ?? 'none'));
        
return $regionId;
    }

protected function calculateTimeRecords($transactions, $empCode, $date, $approvedLeaves, $pendingApplications = [], $user = null, bool $isWFH = false): array
    {
$carbonDate = Carbon::parse($date);
$dayOfWeek = $carbonDate->format('l');
$schedule = $this->getUserSchedule($empCode, $date);

$location = $this->determineLocation($schedule, $dayOfWeek);
$timeData = $this->extractTimeData($transactions, $empCode, $date, $schedule, $isWFH);

// Check if record is incomplete EARLY
$isIncomplete = $this->hasIncompleteRecord($timeData, $schedule, $date);

// Check if it's a rest day with attendance for overtime calculation
$isRestDayWithWork = $schedule && $this->isRestDay($schedule, $carbonDate) &&
                             ($timeData['time_in'] || $timeData['time_out']);

// If record is incomplete, don't calculate times - just return defaults
if ($isIncomplete) {
$remarks = $this->determineRemarks($timeData, $dayOfWeek, $approvedLeaves, $date, false, $schedule, $pendingApplications, [], $user);
            
return [
'day_of_week' => $dayOfWeek,
'location' => $location,
'remarks' => $remarks,
'time_in' => $timeData['time_in']?->format('H:i:s'),
'time_out' => $timeData['time_out']?->format('H:i:s'),
'break_in' => $timeData['break_in']?->format('H:i:s'),
'break_out' => $timeData['break_out']?->format('H:i:s'),
'total_hours_rendered' => '00:00',
'late' => '00:00',
'overtime' => '00:00',
'ut' => '00:00',
'shift_start_date' => $timeData['shift_start_date'] ?? $carbonDate->toDateString(),
'shift_end_date' => $timeData['shift_end_date'] ?? $carbonDate->toDateString(),
'is_shift_start' => $timeData['is_shift_start'] ?? false,
'is_shift_end' => $timeData['is_shift_end'] ?? false,
            ];
        }

$calculatedTimes = $this->calculateTimes($timeData, $carbonDate, $schedule, $isRestDayWithWork, $user);

$isFlexi = $schedule ? $schedule->is_flexi == 1 : false;
$remarks = $this->determineRemarks($timeData, $dayOfWeek, $approvedLeaves, $date, $isFlexi, $schedule, $pendingApplications, $calculatedTimes, $user);

return array_merge([
'day_of_week' => $dayOfWeek,
'location' => $location,
'remarks' => $remarks,
        ], $calculatedTimes);
    }

protected function determineLocation(?DTRSchedule $schedule, string $dayOfWeek): string
    {
if (!$schedule || !$schedule->wfh_days) {
return 'Onsite';
        }

$wfhDays = array_map('ucfirst', array_map('trim', explode(',', $schedule->wfh_days)));
return in_array($dayOfWeek, $wfhDays) ? 'WFH' : 'Onsite';
    }

protected function getBreakPunchStates(bool $isWFH): array
    {
return $isWFH ? [5, 4] : [4, 5];
    }

protected function extractTimeData($transactions, $empCode, $date, ?DTRSchedule $schedule, bool $isWFH = false): array
    {
$carbonDate = Carbon::parse($date);

$timeData = [
'time_in' => null,
'time_out' => null,
'break_in' => null,
'break_out' => null,
'shift_start_date' => $date,
'shift_end_date' => $date,
'is_shift_start' => false,
'is_shift_end' => false,
        ];

if (!$schedule || $transactions->isEmpty()) {
return $timeData;
        }

// Get default times for reference
$timeData['defaultStartTime'] = Carbon::parse($date)->setTimeFromTimeString($schedule->default_start_time);
$timeData['defaultEndTime'] = Carbon::parse($date)->setTimeFromTimeString($schedule->default_end_time);

// For 24-hour schedules - handle multi-day shifts
if ($schedule->is_24hours) {
return $this->extract24HourTimeData($transactions, $empCode, $date, $schedule, $carbonDate);
        }

// For overnight schedules, end time is next day
if ($schedule->is_overnight) {
$timeData['defaultEndTime'] = $timeData['defaultEndTime']->addDay();
        }

if ($schedule->is_flexi) {
// Flexi schedule: use punch_state to determine time_in and time_out
// Check if transactions have punch_state field
$hasPunchState = $transactions->first() && isset($transactions->first()->punch_state);
            
if ($hasPunchState) {
// Use punch_state logic
$timeInPunch = $transactions->where('punch_state', 0)->first();
$timeOutPunch = $transactions->where('punch_state', 1)->sortBy('punch_time')->last();
                
if ($timeInPunch) {
$timeData['time_in'] = Carbon::parse($timeInPunch->punch_time);
                }
if ($timeOutPunch) {
$timeData['time_out'] = Carbon::parse($timeOutPunch->punch_time);
                }
                
// Log warnings for incomplete records
if ($timeData['time_in'] && !$timeData['time_out']) {
Log::warning("Flexi: Only time_in found (no time_out) for {$empCode} on {$date}");
                } elseif (!$timeData['time_in'] && $timeData['time_out']) {
Log::warning("Flexi: Only time_out found (no time_in) for {$empCode} on {$date}");
                }
                
// Get break times from punch_state if has_break
if ($schedule->has_break) {
                    [$breakOutState, $breakInState] = $this->getBreakPunchStates($isWFH);
                    $breakOutPunch = $transactions->where('punch_state', $breakOutState)->first();
                    $breakInPunch = $transactions->where('punch_state', $breakInState)->first();
                    
if ($breakOutPunch) {
$timeData['break_out'] = Carbon::parse($breakOutPunch->punch_time);
                    }
if ($breakInPunch) {
$timeData['break_in'] = Carbon::parse($breakInPunch->punch_time);
                    }
                }
            } else {
// Fallback: Use first/last transaction for WFH or legacy tables
Log::info("Flexi (WFH): Using first/last transaction fallback for {$empCode} on {$date}");
                
if ($transactions->count() >= 2) {
$timeData['time_in'] = Carbon::parse($transactions->first()->punch_time);
$timeData['time_out'] = Carbon::parse($transactions->last()->punch_time);
                } elseif ($transactions->count() == 1) {
// Only one transaction - incomplete record
$timeData['time_in'] = Carbon::parse($transactions->first()->punch_time);
Log::warning("Flexi (WFH): Only one transaction found for {$empCode} on {$date}");
                }
            }
        } else {
// Regular schedule with specific logic for breaks
$this->extractRegularScheduleTimeData($timeData, $transactions, $schedule, $carbonDate, $isWFH);
        }

// Remove seconds from all times for accurate calculations
if ($timeData['time_in']) {
$timeData['time_in']->second(0);
        }
if ($timeData['time_out']) {
$timeData['time_out']->second(0);
        }
if ($timeData['break_in']) {
$timeData['break_in']->second(0);
        }
if ($timeData['break_out']) {
$timeData['break_out']->second(0);
        }

// ✅ If time_in is in the afternoon (12:00 PM or later), remove break times
if ($timeData['time_in'] && $timeData['time_in']->hour >= 12) {
$timeData['break_out'] = null;
$timeData['break_in'] = null;
Log::info("Removed break times for afternoon arrival at {$timeData['time_in']->format('H:i')} for {$empCode} on {$date}");
        }

// CRITICAL FIX: If time_out equals time_in, it means employee hasn't punched out yet
// This can happen if there's only 1 punch transaction recorded
if ($timeData['time_in'] && $timeData['time_out'] && 
            $timeData['time_in']->format('H:i:s') === $timeData['time_out']->format('H:i:s')) {
Log::warning("WARNING: time_out equals time_in for {$empCode} on {$date} - setting time_out to NULL");
$timeData['time_out'] = null;
        }

return $timeData;
    }

protected function extract24HourTimeData($transactions, $empCode, $date, DTRSchedule $schedule, Carbon $carbonDate): array
    {
$timeData = [
'time_in' => null,
'time_out' => null,
'break_in' => null,
'break_out' => null,
'shift_start_date' => $date,
'shift_end_date' => $date,
'is_shift_start' => false,
'is_shift_end' => false,
        ];

// Get all transactions sorted by time
$sortedTransactions = $transactions->sortBy('punch_time');

Log::info("24H Schedule - Processing {$empCode} on {$date}");
Log::info("All transactions for period:");
foreach ($sortedTransactions as $trans) {
Log::info("  - {$trans->punch_time} (State: {$trans->punch_state})");
        }

// STRATEGY 1: Check if this day has a time_in (shift starts on this day)
$currentDayTimeIn = $sortedTransactions->where('punch_state', 0)
            ->filter(function($trans) use ($date) {
return Carbon::parse($trans->punch_time)->toDateString() === $date;
            })
            ->first();

if ($currentDayTimeIn) {
$timeData['time_in'] = Carbon::parse($currentDayTimeIn->punch_time);
$timeData['is_shift_start'] = true;
$timeData['shift_start_date'] = $date;
Log::info("Found time_in on {$date}: {$timeData['time_in']}");

// Don't look for time_out here - it should appear on the next day's record
Log::info("Shift starts on {$date}, time_out should appear on next day");
        }

// STRATEGY 2: Check if this day has a time_out (shift ends on this day)
$currentDayTimeOut = $sortedTransactions->where('punch_state', 1)
            ->filter(function($trans) use ($date) {
return Carbon::parse($trans->punch_time)->toDateString() === $date;
            })
            ->first();

if ($currentDayTimeOut) {
$timeOut = Carbon::parse($currentDayTimeOut->punch_time);
Log::info("Found time_out on {$date}: {$timeOut}");

// Look for the most recent time_in before this time_out
$matchingTimeIn = $sortedTransactions->where('punch_state', 0)
                ->filter(function($transaction) use ($timeOut) {
$punchTime = Carbon::parse($transaction->punch_time);
return $punchTime->lt($timeOut);
                })
                ->sortByDesc('punch_time')
                ->first();

if ($matchingTimeIn) {
$timeIn = Carbon::parse($matchingTimeIn->punch_time);
Log::info("Found matching time_in: {$timeIn}");

// Set the time_in and time_out for calculation
$timeData['time_in'] = $timeIn;
$timeData['time_out'] = $timeOut;
$timeData['shift_start_date'] = $timeIn->toDateString();
$timeData['shift_end_date'] = $date;
$timeData['is_shift_end'] = true;

// Only mark as shift_start if time_in is on the same day
if ($timeIn->toDateString() === $date) {
$timeData['is_shift_start'] = true;
                }

Log::info("MATCHED: time_in from {$timeIn->toDateString()} with time_out from {$date}");
            } else {
// No matching time_in found, but show the time_out
$timeData['time_out'] = $timeOut;
$timeData['is_shift_end'] = true;
$timeData['shift_end_date'] = $date;
Log::info("No matching time_in found, showing time_out only");
            }
        }

Log::info("Final result for {$date}:");
Log::info("  Time In: " . ($timeData['time_in'] ? $timeData['time_in']->format('Y-m-d H:i:s') : 'null'));
Log::info("  Time Out: " . ($timeData['time_out'] ? $timeData['time_out']->format('Y-m-d H:i:s') : 'null'));
Log::info("  Shift Start: " . ($timeData['is_shift_start'] ? 'yes' : 'no'));
Log::info("  Shift End: " . ($timeData['is_shift_end'] ? 'yes' : 'no'));
Log::info("  Shift Start Date: " . $timeData['shift_start_date']);
Log::info("  Shift End Date: " . $timeData['shift_end_date']);

return $timeData;
    }

/**
     * ✅ UPDATED: Extract time data based on punch_state
     * punch_state = 0: time_in
     * punch_state = 1: time_out
     * punch_state = 4/5: break_out/break_in for onsite
     * WFH keeps the legacy reverse mapping: 5=Break Out, 4=Break In
     */
protected function extractRegularScheduleTimeData(array &$timeData, $transactions, DTRSchedule $schedule, Carbon $carbonDate, bool $isWFH = false): void
    {
if ($transactions->isEmpty()) {
return;
        }

// Check if transactions have punch_state field
$hasPunchState = $transactions->first() && isset($transactions->first()->punch_state);
        
if (!$hasPunchState) {
// Fallback for WFH or legacy transactions without punch_state
Log::info("Using fallback (first/last) for regular schedule on " . $carbonDate->toDateString());
$this->extractRegularScheduleTimeDataFallback($timeData, $transactions, $schedule, $carbonDate);
return;
        }

// For overnight schedules, include next day transactions
if ($schedule->is_overnight) {
$nextDay = $carbonDate->copy()->addDay();
[$breakOutState, $breakInState] = $this->getBreakPunchStates($isWFH);
            
// Get time_in (punch_state = 0) from current date
$timeInPunch = $transactions->filter(function($trans) use ($carbonDate) {
return Carbon::parse($trans->punch_time)->isSameDay($carbonDate) && $trans->punch_state == 0;
            })->first();
            
if ($timeInPunch) {
$timeData['time_in'] = Carbon::parse($timeInPunch->punch_time);
            }
            
// Get time_out (punch_state = 1) from current date or next day
$timeOutPunch = $transactions->filter(function($trans) use ($carbonDate, $nextDay) {
$punchTime = Carbon::parse($trans->punch_time);
return ($punchTime->isSameDay($carbonDate) || $punchTime->isSameDay($nextDay)) && $trans->punch_state == 1;
            })->sortBy('punch_time')->last();
            
if ($timeOutPunch) {
$lastPunch = Carbon::parse($timeOutPunch->punch_time);
// Only set time_out if it's actually after time_in (if time_in exists)
if (!$timeData['time_in']) {
// No time_in but has time_out - incomplete record
$timeData['time_out'] = $lastPunch;
Log::warning("Overnight: Only time_out found (no time_in) on " . $carbonDate->toDateString());
                } elseif ($lastPunch->gt($timeData['time_in'])) {
$timeData['time_out'] = $lastPunch;
                }
            }
            
// Get break times from punch_state if schedule has breaks
if ($schedule->has_break) {
// Break out
$breakOutPunch = $transactions->filter(function($trans) use ($carbonDate, $nextDay, $breakOutState) {
$punchTime = Carbon::parse($trans->punch_time);
return ($punchTime->isSameDay($carbonDate) || $punchTime->isSameDay($nextDay)) && $trans->punch_state == $breakOutState;
                })->first();
                
if ($breakOutPunch) {
$timeData['break_out'] = Carbon::parse($breakOutPunch->punch_time);
                }
                
// Break in
$breakInPunch = $transactions->filter(function($trans) use ($carbonDate, $nextDay, $breakInState) {
$punchTime = Carbon::parse($trans->punch_time);
return ($punchTime->isSameDay($carbonDate) || $punchTime->isSameDay($nextDay)) && $trans->punch_state == $breakInState;
                })->first();
                
if ($breakInPunch) {
$timeData['break_in'] = Carbon::parse($breakInPunch->punch_time);
                }
                
Log::info("Break times extracted from punch_state for overnight schedule on " . $carbonDate->toDateString());
            }
        } else {
// Non-overnight schedule: extract from punch_state
// Time in (punch_state = 0)
$timeInPunch = $transactions->where('punch_state', 0)->first();
if ($timeInPunch) {
$timeData['time_in'] = Carbon::parse($timeInPunch->punch_time);
            }
            
// Time out (punch_state = 1)
$timeOutPunch = $transactions->where('punch_state', 1)->sortBy('punch_time')->last();
if ($timeOutPunch) {
$lastPunch = Carbon::parse($timeOutPunch->punch_time);
                
// Check if there's time_in
if (!$timeData['time_in']) {
// No time_in but has time_out - incomplete record, but still save the time_out
$timeData['time_out'] = $lastPunch;
Log::warning("Only time_out found (no time_in) on " . $carbonDate->toDateString());
                } elseif ($lastPunch->gt($timeData['time_in'])) {
// Normal case: time_out after time_in
$timeData['time_out'] = $lastPunch;
                }
            }
            
// Get break times from punch_state if schedule has breaks
if ($schedule->has_break) {
[$breakOutState, $breakInState] = $this->getBreakPunchStates($isWFH);

// Break out
$breakOutPunch = $transactions->where('punch_state', $breakOutState)->first();
if ($breakOutPunch) {
$timeData['break_out'] = Carbon::parse($breakOutPunch->punch_time);
                }
                
// Break in
$breakInPunch = $transactions->where('punch_state', $breakInState)->first();
if ($breakInPunch) {
$timeData['break_in'] = Carbon::parse($breakInPunch->punch_time);
                }
                
Log::info("Break times extracted from punch_state for day schedule on " . $carbonDate->toDateString());
            }
        }
    }

/**
     * ✅ FALLBACK: For WFH or legacy transactions without punch_state field
     */
protected function extractRegularScheduleTimeDataFallback(array &$timeData, $transactions, DTRSchedule $schedule, Carbon $carbonDate): void
    {
// For overnight schedules, include next day transactions
if ($schedule->is_overnight) {
$nextDay = $carbonDate->copy()->addDay();
            
// Get first punch from current date for time_in
$currentDateTransactions = $transactions->filter(function($trans) use ($carbonDate) {
return Carbon::parse($trans->punch_time)->isSameDay($carbonDate);
            });
            
if ($currentDateTransactions->isNotEmpty()) {
$timeData['time_in'] = Carbon::parse($currentDateTransactions->first()->punch_time);
            }
            
// Get all transactions from current date and next day for time_out
$allTransactions = $transactions->filter(function($trans) use ($carbonDate, $nextDay) {
$punchTime = Carbon::parse($trans->punch_time);
return $punchTime->isSameDay($carbonDate) || $punchTime->isSameDay($nextDay);
            });
            
if ($allTransactions->isNotEmpty()) {
$lastPunch = Carbon::parse($allTransactions->last()->punch_time);
if (!$timeData['time_in']) {
$timeData['time_out'] = $lastPunch;
Log::warning("Fallback: Only time_out on " . $carbonDate->toDateString());
                } elseif ($lastPunch->gt($timeData['time_in'])) {
$timeData['time_out'] = $lastPunch;
                }
            }
        } else {
// Non-overnight: first and last transaction
if ($transactions->count() >= 2) {
$timeData['time_in'] = Carbon::parse($transactions->first()->punch_time);
$timeData['time_out'] = Carbon::parse($transactions->last()->punch_time);
            } elseif ($transactions->count() == 1) {
$timeData['time_in'] = Carbon::parse($transactions->first()->punch_time);
Log::warning("Fallback: Only one transaction on " . $carbonDate->toDateString());
            }
        }
    }

protected function calculateTimes(array $timeData, Carbon $carbonDate, ?DTRSchedule $schedule, bool $isRestDayWithWork = false, $user = null): array
    {
$result = [
'time_in' => $timeData['time_in']?->format('H:i:s'),
'time_out' => $timeData['time_out']?->format('H:i:s'),
'break_in' => $timeData['break_in']?->format('H:i:s'),
'break_out' => $timeData['break_out']?->format('H:i:s'),
'total_hours_rendered' => '00:00',
'late' => '00:00',
'overtime' => '00:00',
'ut' => '00:00',
'shift_start_date' => $timeData['shift_start_date'] ?? $carbonDate->toDateString(),
'shift_end_date' => $timeData['shift_end_date'] ?? $carbonDate->toDateString(),
'is_shift_start' => $timeData['is_shift_start'] ?? false,
'is_shift_end' => $timeData['is_shift_end'] ?? false,
'is_24h_complete' => false,
        ];

if (!$schedule) {
return $result;
        }

// For 24-hour schedules
if ($schedule->is_24hours) {
// If this is only a shift start day, show only time_in
if ($timeData['is_shift_start'] && !$timeData['is_shift_end']) {
$result['time_in'] = $timeData['time_in']?->format('H:i:s');
$result['time_out'] = null;
$result['total_hours_rendered'] = '00:00';
Log::info("Shift start day - showing time_in only");
return $result;
            }

// If this is only a shift end day, show only time_out and calculate hours
if ($timeData['is_shift_end'] && $timeData['time_in'] && $timeData['time_out']) {
// Calculate total hours rendered
$totalMinutesRendered = $timeData['time_in']->diffInMinutes($timeData['time_out']);

Log::info("Calculating 24H hours: {$timeData['time_in']} to {$timeData['time_out']} = {$totalMinutesRendered} minutes");

// Handle shifts that span multiple days
if ($totalMinutesRendered < 0) {
$totalMinutesRendered += 24 * 60;
Log::info("Adjusted for multi-day: {$totalMinutesRendered} minutes");
                }

$result['total_hours_rendered'] = $this->formatMinutesToTime($totalMinutesRendered);

// Calculate undertime/overtime
$expected24Hours = 24 * 60;
if ($totalMinutesRendered < $expected24Hours) {
$undertimeMinutes = $expected24Hours - $totalMinutesRendered;
$result['ut'] = $this->formatMinutesToTime($undertimeMinutes);
Log::info("Undertime calculated: {$undertimeMinutes} minutes");
                } elseif ($totalMinutesRendered >= $expected24Hours) {
$result['is_24h_complete'] = true;
if ($totalMinutesRendered > $expected24Hours) {
$overtimeMinutes = $totalMinutesRendered - $expected24Hours;
$result['overtime'] = $this->formatMinutesToTime($overtimeMinutes);
Log::info("Overtime calculated: {$overtimeMinutes} minutes");
                    }
                }

// Show only time_out on shift end day
$result['time_in'] = null;
$result['time_out'] = $timeData['time_out']?->format('H:i:s');
Log::info("Shift end day - showing time_out only, complete: " . ($result['is_24h_complete'] ? 'yes' : 'no'));
            }

return $result;
        }

// For non-24hour schedules, return early if no time_in
if (!$timeData['time_in']) {
return $result;
        }

// Calculate total hours rendered for non-24hour schedules
        // ✅ Calculate ONLY from time_in to time_out (breaks NOT used)
        $totalMinutesRendered = 0;

        if ($timeData['time_in'] && $timeData['time_out']) {
            $totalMinutesRendered = $timeData['time_in']->diffInMinutes($timeData['time_out']);
            Log::info("Calculated hours: {$totalMinutesRendered} min (breaks not used in calculation)");
        }

        $result['total_hours_rendered'] = $this->formatMinutesToTime($totalMinutesRendered);

// If it's a rest day with work, all hours are overtime
if ($isRestDayWithWork) {
$result['overtime'] = $result['total_hours_rendered'];
$result['late'] = '00:00';
$result['ut'] = '00:00';
return $result;
        }

// Calculate late, overtime, and undertime based on schedule type
if ($schedule->is_flexi) {
$this->calculateFlexiTimes($result, $timeData, $carbonDate, $schedule, $user);
        } elseif ($schedule->is_overnight) {
$this->calculateOvernightTimes($result, $timeData, $carbonDate, $schedule);
        } else {
$this->calculateRegularTimes($result, $timeData, $carbonDate, $schedule);
        }

return $result;
    }

    /**
     * ✅ UPDATED VERSION - calculateFlexiTimes
     * 
     * IMPORTANT: Breaks are NOT used in calculations - only time_in to time_out
     * 
     * NEW ADDITIONS:
     * - Handle 12:30 PM specifically (treat as 1:00 PM for calculation)
     * - All time calculations ignore seconds for proper tallying
     * 
     * RULES FOR REGULAR EMPLOYEES (non-COS):
     * 1. Time in 7:00 AM - 9:00 AM: On time, expected end = time_in + 8 hours
     * 2. Time in 9:01 AM or later: Tardy, late = (time_in - 9:00 AM), expected end = time_in + 8 hours
     * 3. Time in 11:00 AM or 1:00 PM: Half-day, 4 hours tardiness, expected = 4 hours of work
     * 4. Time in 12:30 PM: Treat as 1:00 PM, 4 hours tardiness, expected = 4 hours of work
     * 5. Time in BEFORE 7:00 AM: Early arrival, NOT LATE (0 minutes), expected end based on 7:00 AM start
     * 6. Max time out = 6:00 PM (work beyond capped at 6 PM for tardy employees)
     * 7. Undertime if actual time_out < expected time_out
     * 8. Overtime if actual time_out > expected time_out (max up to 6:00 PM for tardy)
     * 
     * RULES FOR COS EMPLOYEES (appointment = 'cos'):
     * 1. Time in 7:30 AM - 9:00 AM: On time, expected end = time_in + 8 hours, minimum 4:30 PM
     * 2. Time in 9:01 AM or later: Tardy, late = (time_in - 9:00 AM), expected end = time_in + 8 hours, minimum 4:30 PM
     * 3. Time in 11:00 AM or 1:00 PM: Half-day, 4 hours tardiness, expected = 4 hours of work
     * 4. Time in 12:30 PM: Treat as 1:00 PM, 4 hours tardiness, expected = 4 hours of work
     * 5. Time in BEFORE 7:30 AM: Early arrival, NOT LATE (0 minutes), expected end based on 7:30 AM start
     * 6. Max time out = 6:00 PM (work beyond capped at 6 PM for tardy employees)
     * 7. Undertime if actual time_out < expected time_out
     * 8. Overtime if actual time_out > expected time_out (max up to 6:00 PM for tardy)
     */
protected function calculateFlexiTimes(array &$result, array $timeData, Carbon $carbonDate, DTRSchedule $schedule, $user = null): void
    {
$firstTimeIn = $timeData['time_in'];
$lastTimeOut = $timeData['time_out'];
        
if (!$firstTimeIn || !$lastTimeOut) {
return;
        }

// Check if user is COS appointment
$isCOS = false;
if ($user && isset($user->appointment) && !is_null($user->appointment) && trim($user->appointment) !== '') {
$appointment = trim(strtolower($user->appointment));
$isCOS = ($appointment === 'cos');
Log::info("User {$user->emp_code} appointment: '{$user->appointment}', trimmed: '{$appointment}', isCOS: " . ($isCOS ? 'yes' : 'no'));
        } else {
Log::info("User " . ($user ? $user->emp_code : 'unknown') . " appointment is NULL or empty, treating as regular employee (isCOS: no)");
        }

// Flexi cutoff times - adjusted based on COS status
$earliestTimeIn = $isCOS 
            ? $carbonDate->copy()->setTime(7, 30, 0)  // 7:30 AM for COS
            : $carbonDate->copy()->setTime(7, 0, 0);   // 7:00 AM for regular
$flexiCutoff = $carbonDate->copy()->setTime(9, 0, 0);     // 9:00 AM
$tardyCutoff = $carbonDate->copy()->setTime(9, 1, 0);     // 9:01 AM
$halfDayCutoff1 = $carbonDate->copy()->setTime(11, 0, 0); // 11:00 AM
$halfDayCutoff2 = $carbonDate->copy()->setTime(13, 0, 0); // 1:00 PM (also used for 12:30 calculation)
$maxTimeOut = $carbonDate->copy()->setTime(18, 0, 0);     // 6:00 PM max
$minTimeOutCOS = $carbonDate->copy()->setTime(16, 30, 0); // 4:30 PM minimum for COS

Log::info("COS Status: " . ($isCOS ? 'YES' : 'NO') . ", Earliest time in: " . $earliestTimeIn->format('H:i'));

// Calculate actual work minutes - Subtract 60 mins for lunch if shift spans 12 PM - 1 PM
$actualWorkMinutes = $firstTimeIn->diffInMinutes($lastTimeOut);

// If shift spans from before 12 PM to after 1 PM, deduct 60 minutes
$lunchStart = $carbonDate->copy()->setTime(12, 0, 0);
$lunchEnd = $carbonDate->copy()->setTime(13, 0, 0);
if ($firstTimeIn->lt($lunchStart) && $lastTimeOut->gt($lunchEnd)) {
    $actualWorkMinutes -= 60;
    Log::info("Lunch break deducted: 60 minutes");
}

$result['total_hours_rendered'] = $this->formatMinutesToTime($actualWorkMinutes);

// Initialize
$lateMinutes = 0;
$expectedEndTime = null;
$undertimeMinutes = 0;
$overtimeMinutes = 0;
$isTardy = false; // Track if employee is tardy (affects 6:00 PM cap)

// ✅ NEW: Handle 12:30 PM specifically - treat as 1:00 PM for calculation
if ($firstTimeIn->hour == 12 && $firstTimeIn->minute == 30) {
Log::info("Time in at 12:30 PM detected - treating as 1:00 PM (half-day)");
// Override the time_in for calculation purposes to 1:00 PM
$firstTimeIn = $carbonDate->copy()->setTime(13, 0, 0);
        }

// RULE: If employee reports at 11:00 AM or 1:00 PM, 4 hours tardiness (half-day)
        if ($firstTimeIn->format('H:i') === '11:00' || $firstTimeIn->format('H:i') === '13:00') {
            $lateMinutes = 4 * 60; // 4 hours tardiness
            $isTardy = true; // Half-day is considered tardy
            
            // Expected end time: time_in + 4 hours
            $expectedEndTime = $firstTimeIn->copy()->addHours(4);
            
            // Cap at 6:00 PM max for half-day employees
            if ($expectedEndTime->gt($maxTimeOut)) {
                $expectedEndTime = $maxTimeOut->copy();
                Log::info("Half-day: Expected end time capped at 6:00 PM");
            }
            
            Log::info("Half-day flexi: Time in at {$firstTimeIn->format('H:i')}, 4 hours tardiness, expected end: {$expectedEndTime->format('H:i')}");
        }
        // RULE: Time in beyond 9:01 AM is tardy
        elseif ($firstTimeIn->gte($tardyCutoff)) {
            $lateMinutes = $firstTimeIn->diffInMinutes($flexiCutoff);
            $isTardy = true; // Flag to track tardy status
            
            // Expected end time: time_in + 8 hours + 1 hour lunch = 9 hours
            $expectedEndTime = $firstTimeIn->copy()->addHours(9);
            
            // For COS, enforce minimum 4:30 PM
            if ($isCOS && $expectedEndTime->lt($minTimeOutCOS)) {
                $expectedEndTime = $minTimeOutCOS->copy();
                Log::info("COS: Expected end time adjusted to minimum 4:30 PM");
            }
            
            // Cap at 6:00 PM max ONLY for tardy employees
            if ($expectedEndTime->gt($maxTimeOut)) {
                $expectedEndTime = $maxTimeOut->copy();
                Log::info("Tardy employee: Expected end time capped at 6:00 PM");
            }
            
            Log::info("Tardy: Time in at {$firstTimeIn->format('H:i')}, late by {$lateMinutes} min, expected end: {$expectedEndTime->format('H:i')}");
        }
        // RULE: Time in between earliest - 9:00 AM is on time
        elseif ($firstTimeIn->between($earliestTimeIn, $flexiCutoff)) {
            $lateMinutes = 0;
            $isTardy = false; // On-time employee
            
            // Expected end time: time_in + 8 hours + 1 hour lunch = 9 hours
            $expectedEndTime = $firstTimeIn->copy()->addHours(9);
            
            // For COS, enforce minimum 4:30 PM
            if ($isCOS && $expectedEndTime->lt($minTimeOutCOS)) {
                $expectedEndTime = $minTimeOutCOS->copy();
                Log::info("COS: Expected end time adjusted to minimum 4:30 PM");
            }
            
            // NO 6:00 PM cap for on-time employees - they can earn unlimited overtime
            
            Log::info("On time: Time in at {$firstTimeIn->format('H:i')}, expected end: {$expectedEndTime->format('H:i')} (no OT cap)");
        }
        // ✅ FIXED: Time in before earliest allowed time (early arrival - not late!)
        else {
            // Early arrival is never late
            $lateMinutes = 0;
            $isTardy = false; // Early arrival is not tardy
            
            // For calculation purposes, use the earliest allowed time as reference
            $calculationStartTime = $earliestTimeIn->copy();
            
            // Expected end time: earliest_time + 8 hours + 1 hour lunch = 9 hours
            $expectedEndTime = $calculationStartTime->copy()->addHours(9);
            
            // For COS, enforce minimum 4:30 PM
            if ($isCOS && $expectedEndTime->lt($minTimeOutCOS)) {
                $expectedEndTime = $minTimeOutCOS->copy();
                Log::info("COS: Expected end time adjusted to minimum 4:30 PM");
            }
            
            // NO 6:00 PM cap for early arrivals - they can earn unlimited overtime
            
            Log::info("Early arrival: Time in at {$firstTimeIn->format('H:i')} (before {$earliestTimeIn->format('H:i')}), NOT LATE, expected end: {$expectedEndTime->format('H:i')} (no OT cap)");
        }

// Calculate undertime/overtime based on ACTUAL time out vs EXPECTED time out
if ($expectedEndTime) {
if ($lastTimeOut->lt($expectedEndTime)) {
$undertimeMinutes = $expectedEndTime->diffInMinutes($lastTimeOut);
Log::info("UNDERTIME: Expected end {$expectedEndTime->format('H:i')}, actual {$lastTimeOut->format('H:i')}, UT = {$undertimeMinutes} min");
            } elseif ($lastTimeOut->gt($expectedEndTime)) {
// For tardy employees, overtime is already capped at 6:00 PM in expected end time
// For on-time/early employees, calculate full overtime without cap
if ($isTardy) {
// Tardy employee - expected end is already capped at 6:00 PM max
$overtimeMinutes = $lastTimeOut->diffInMinutes($expectedEndTime);
// But don't give overtime credit for work beyond 6:00 PM
if ($lastTimeOut->gt($maxTimeOut)) {
$overtimeMinutes = $maxTimeOut->diffInMinutes($expectedEndTime);
Log::info("OVERTIME (TARDY - CAPPED): Expected end {$expectedEndTime->format('H:i')}, capped at 6:00 PM, OT = {$overtimeMinutes} min");
                    } else {
Log::info("OVERTIME (TARDY): Expected end {$expectedEndTime->format('H:i')}, actual {$lastTimeOut->format('H:i')}, OT = {$overtimeMinutes} min");
                    }
                } else {
// On-time or early employee - NO cap on overtime
$overtimeMinutes = $lastTimeOut->diffInMinutes($expectedEndTime);
Log::info("OVERTIME (ON-TIME/EARLY - NO CAP): Expected end {$expectedEndTime->format('H:i')}, actual {$lastTimeOut->format('H:i')}, OT = {$overtimeMinutes} min");
                }
            } else {
Log::info("ON TIME: Actual matches expected end time exactly");
            }
        }

$result['late'] = $this->formatMinutesToTime($lateMinutes);
$result['ut'] = $this->formatMinutesToTime($undertimeMinutes);
$result['overtime'] = $this->formatMinutesToTime($overtimeMinutes);
    }

protected function calculateOvernightTimes(array &$result, array $timeData, Carbon $carbonDate, DTRSchedule $schedule): void
    {
$defaultStartTime = $timeData['defaultStartTime'];
$defaultEndTime = $timeData['defaultEndTime']; // Already adjusted for next day

$actualStartTime = $timeData['time_in'];
$actualEndTime = $timeData['time_out'];

// Late calculation
$lateMinutes = 0;
if ($actualStartTime && $actualStartTime->gt($defaultStartTime)) {
$lateMinutes = $actualStartTime->diffInMinutes($defaultStartTime);
        }

$result['late'] = $this->formatMinutesToTime($lateMinutes);

// Undertime and overtime calculation
$undertimeMinutes = 0;
$overtimeMinutes = 0;

if ($actualEndTime && $defaultEndTime) {
if ($actualEndTime->lt($defaultEndTime)) {
$undertimeMinutes = $defaultEndTime->diffInMinutes($actualEndTime);
            } elseif ($actualEndTime->gt($defaultEndTime)) {
$overtimeMinutes = $actualEndTime->diffInMinutes($defaultEndTime);
            }
        } elseif (!$actualEndTime && $actualStartTime) {
// No time out recorded - major undertime
$undertimeMinutes = 8 * 60; // 8 hours penalty
        }

$result['ut'] = $this->formatMinutesToTime($undertimeMinutes);
$result['overtime'] = $this->formatMinutesToTime($overtimeMinutes);
    }

protected function calculateRegularTimes(array &$result, array $timeData, Carbon $carbonDate, DTRSchedule $schedule): void
    {
$defaultStartTime = $timeData['defaultStartTime'];
$defaultEndTime = $timeData['defaultEndTime'];

$actualStartTime = $timeData['time_in'];
$actualEndTime = $timeData['time_out'];

// Late calculation
$lateMinutes = 0;
if ($actualStartTime && $actualStartTime->gt($defaultStartTime)) {
$lateMinutes = $actualStartTime->diffInMinutes($defaultStartTime);
        }

$result['late'] = $this->formatMinutesToTime($lateMinutes);

// Undertime and overtime calculation
$undertimeMinutes = 0;
$overtimeMinutes = 0;

if ($actualEndTime && $defaultEndTime) {
if ($actualEndTime->lt($defaultEndTime)) {
$undertimeMinutes = $defaultEndTime->diffInMinutes($actualEndTime);
            } elseif ($actualEndTime->gt($defaultEndTime)) {
$overtimeMinutes = $actualEndTime->diffInMinutes($defaultEndTime);
            }
        }
        // FIXED: Removed penalty for missing time_out - incomplete records should have 00:00

$result['ut'] = $this->formatMinutesToTime($undertimeMinutes);
$result['overtime'] = $this->formatMinutesToTime($overtimeMinutes);
    }

/**
     * ✅ UPDATED: Determine remarks with regional holiday consideration
     * Checks both national holidays (region_id = null) and regional holidays (region_id = user's region)
     */
protected function determineRemarks(array $timeData, string $dayOfWeek, $approvedLeaves, string $date, bool $isFlexi = false, ?DTRSchedule $schedule = null, array $pendingApplications = [], array $calculatedTimes = [], $user = null): string
    {
// For 24-hour schedules
if ($schedule && $schedule->is_24hours) {
if ($timeData['is_shift_start'] && !$timeData['is_shift_end']) {
return 'Shift Started';
            }
if (!$timeData['is_shift_start'] && $timeData['is_shift_end'] && $timeData['time_out']) {
return 'Shift Ended';
            }
if ($timeData['is_shift_start'] && $timeData['is_shift_end']) {
return 'Present';
            }
if ($timeData['time_out'] && !$timeData['time_in']) {
return 'Shift Ended';
            }
if (!$timeData['time_in'] && !$timeData['time_out']) {
return 'Absent';
            }
if ($timeData['time_in'] && !$timeData['time_out']) {
return 'Incomplete';
            }
        }

// Check for rest days first (dynamic)
if ($schedule && $this->isRestDay($schedule, Carbon::parse($date))) {
// If they have time entries on rest day, mark as Overtime (Rest Day)
if ($timeData['time_in'] || $timeData['time_out']) {
return 'Overtime (Rest Day)';
            }
return 'Rest Day';
        }

// ✅ UPDATED: Check for holidays with regional consideration
$userRegionId = $user ? $this->getUserRegionId($user) : null;
        
$holiday = Holiday::whereDate('holiday_date', $date)
            ->where(function($query) use ($userRegionId) {
// Include national holidays (region_id is null)
$query->whereNull('region_id');
                
// If user has a region, also include regional holidays
if ($userRegionId) {
$query->orWhere('region_id', $userRegionId);
                }
            })
            ->first();
            
if ($holiday) {
// Display the holiday description for all types
return $holiday->description;
        }

// Check for approved leaves
if ($approvedLeaves->isNotEmpty()) {
return 'Leave';
        }

// Check for pending applications - this takes priority over 'Absent'
if (!empty($pendingApplications) && $pendingApplications['has_any_pending']) {
// If no time entries and has pending application, return 'Pending Application'
if (!$timeData['time_in'] && !$timeData['time_out']) {
return 'Pending Application';
            }
        }

// Determine based on time entries
if (!$timeData['time_in'] && !$timeData['time_out']) {
return 'Absent';
        }

// Check for incomplete records
if ($this->hasIncompleteRecord($timeData, $schedule, $date)) {
return 'Incomplete';
        }

// Check for late and undertime conditions
$late = $this->isLate($timeData, $date, $schedule);
$undertime = $this->hasUndertime($calculatedTimes);

// Check if there's overtime (if calculatedTimes provided)
$hasOvertime = false;
if (!empty($calculatedTimes) && isset($calculatedTimes['overtime'])) {
$overtimeParts = explode(':', $calculatedTimes['overtime']);
$overtimeMinutes = ((int)$overtimeParts[0] * 60) + (int)$overtimeParts[1];
$hasOvertime = $overtimeMinutes > 0;
        }

if ($late && $undertime) {
return 'Late/Undertime';
        } elseif ($late && $hasOvertime) {
return 'Late/Overtime';
        } elseif ($undertime && $hasOvertime) {
return 'Undertime/Overtime';
        } elseif ($late) {
return 'Late';
        } elseif ($undertime) {
return 'Undertime';
        } elseif ($hasOvertime) {
return 'Overtime';
        }

return 'Present';
    }

/**
     * ✅ UPDATED: Check for incomplete records - only check time_in and time_out
     * - time_in without time_out → Incomplete
     * - time_out without time_in → Incomplete
     * - Break punches are NOT required for completeness
     */
    protected function hasIncompleteRecord(array $timeData, ?DTRSchedule $schedule, string $date = null): bool
    {
        $hasTimeIn = !is_null($timeData['time_in']);
        $hasTimeOut = !is_null($timeData['time_out']);

        // For all schedules: only require time_in and time_out
        // Incomplete if only one is present (either time_in without time_out OR time_out without time_in)
        return ($hasTimeIn && !$hasTimeOut) || (!$hasTimeIn && $hasTimeOut);
    }

protected function isLate(array $timeData, string $date, ?DTRSchedule $schedule): bool
    {
if (!$schedule || !$timeData['time_in']) {
return false;
        }

// 24-hour schedules don't have "late" concept
if ($schedule->is_24hours) {
return false;
        }

if ($schedule->is_flexi) {
$flexiCutoff = Carbon::parse($date)->setTime(9, 0, 0);
return $timeData['time_in']->gt($flexiCutoff);
        } else {
$defaultStartTime = Carbon::parse($date)->setTimeFromTimeString($schedule->default_start_time);
return $timeData['time_in']->gt($defaultStartTime);
        }
    }

protected function hasUndertime(array $calculatedTimes): bool
    {
if (empty($calculatedTimes) || !isset($calculatedTimes['ut'])) {
return false;
        }

$utParts = explode(':', $calculatedTimes['ut']);
$undertimeMinutes = ((int)$utParts[0] * 60) + (int)$utParts[1];

return $undertimeMinutes > 0;
    }

protected function formatMinutesToTime(int $minutes): string
    {
$hours = floor($minutes / 60);
$remainingMinutes = $minutes % 60;

return sprintf('%02d:%02d', $hours, $remainingMinutes);
    }

protected function saveDtrRecord(User $user, string $date, array $data): void
    {
try {
$record = EmployeesDtr::updateOrCreate(
                ['user_id' => $user->id, 'date' => $date],
                array_merge(['emp_code' => $user->emp_code], $data)
            );

Log::info("DTR record saved/updated for user {$user->emp_code} on {$date}. Record ID: {$record->id}");
        } catch (\Exception $e) {
Log::error("Error saving DTR record for user {$user->emp_code} on {$date}: " . $e->getMessage());
        }
    }

protected function logJobStart(): void
    {
echo "AutoSaveDtrRecordsMonthly job started\n";
Log::info("AutoSaveDtrRecordsMonthly job started");
echo "Processing period: {$this->startDate->toDateString()} to {$this->endDate->toDateString()}\n";
Log::info("Processing period: {$this->startDate->toDateString()} to {$this->endDate->toDateString()}");
    }

protected function logJobSuccess(): void
    {
echo "AutoSaveDtrRecordsMonthly job completed successfully\n";
Log::info("AutoSaveDtrRecordsMonthly job completed successfully");
    }

protected function logJobError(\Exception $e): void
    {
echo "AutoSaveDtrRecordsMonthly job failed: " . $e->getMessage() . "\n";
Log::error("AutoSaveDtrRecordsMonthly job failed: " . $e->getMessage());
Log::error($e->getTraceAsString());
    }

protected function logTransactionCount(string $empCode, string $date, int $count): void
    {
echo "Total transactions found for user {$empCode} on {$date}: {$count}\n";
Log::info("Total transactions found for user {$empCode} on {$date}: {$count}");
    }

protected function logCalculatedData(string $empCode, string $date, array $data): void
    {
echo "Calculated data for user {$empCode} on {$date}: " . json_encode($data) . "\n";
Log::info("Calculated data for user {$empCode} on {$date}: " . json_encode($data));
    }
}
