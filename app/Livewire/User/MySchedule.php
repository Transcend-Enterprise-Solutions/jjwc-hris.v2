<?php

namespace App\Livewire\User;

use Livewire\Component;
use App\Models\DTRSchedule;
use App\Models\Holiday;
use App\Models\UserData;
use App\Models\PhilippineRegions;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('My Schedule')]
class MySchedule extends Component
{
    public $currentMonth;
    public $currentYear;

    public function mount()
    {
        $this->currentMonth = now()->month;
        $this->currentYear = now()->year;
    }

    public function goToPreviousMonth()
    {
        $this->currentMonth--;
        if ($this->currentMonth < 1) {
            $this->currentMonth = 12;
            $this->currentYear--;
        }
    }

    public function goToNextMonth()
    {
        $this->currentMonth++;
        if ($this->currentMonth > 12) {
            $this->currentMonth = 1;
            $this->currentYear++;
        }
    }

    /**
     * Get the authenticated user's region ID
     * 
     * @return int|null
     */
    protected function getUserRegion(): ?int
    {
        $userId = Auth::id();
        $userData = UserData::where('user_id', $userId)->first();
        
        if (!$userData || !$userData->permanent_selectedRegion) {
            return null;
        }

        $region = PhilippineRegions::where('region_description', $userData->permanent_selectedRegion)->first();
        
        return $region ? $region->id : null;
    }

    public function render()
    {
        // Define start and end of the month
        $startOfMonth = Carbon::create($this->currentYear, $this->currentMonth, 1)->startOfMonth();
        $endOfMonth = $startOfMonth->copy()->endOfMonth();

        // Fetch schedules within the month range for the authenticated user
        $schedules = DTRSchedule::where('emp_code', Auth::user()->emp_code)
            ->where(function ($query) use ($startOfMonth, $endOfMonth) {
                $query->whereBetween('start_date', [$startOfMonth, $endOfMonth])
                      ->orWhereBetween('end_date', [$startOfMonth, $endOfMonth])
                      ->orWhere(function ($query) use ($startOfMonth, $endOfMonth) {
                          $query->where('start_date', '<=', $endOfMonth)
                                ->where('end_date', '>=', $startOfMonth);
                      });
            })
            ->orderBy('start_date', 'asc')
            ->get();

        // ✅ UPDATED: Fetch holidays based on user's region
        $userRegionId = $this->getUserRegion();
        
        $holidaysQuery = Holiday::whereBetween('holiday_date', [$startOfMonth, $endOfMonth]);
        
        if ($userRegionId) {
            // Get holidays that are either nationwide or specific to user's region
            $holidaysQuery->where(function($q) use ($userRegionId) {
                $q->whereNull('region_id')  // Nationwide holidays
                  ->orWhere('region_id', $userRegionId);  // Regional holidays
            });
        } else {
            // If no region found, only show nationwide holidays
            $holidaysQuery->whereNull('region_id');
        }
        
        $holidays = $holidaysQuery->orderBy('holiday_date', 'asc')->get();

        return view('livewire.user.my-schedule', [
            'schedules' => $schedules,
            'holidays' => $holidays,
            'userRegion' => $userRegionId ? PhilippineRegions::find($userRegionId) : null,
        ]);
    }

    /**
     * Format schedule time display (helper method for view)
     */
    public function formatScheduleTime($schedule)
    {
        if ($schedule->is_24hours) {
            return '24 Hours';
        }

        $timeDisplay = $schedule->default_start_time . ' - ' . $schedule->default_end_time;

        if ($schedule->is_overnight) {
            $timeDisplay .= ' (Next Day)';
        }

        if ($schedule->has_break) {
            $timeDisplay .= ' (1hr break)';
        }

        return $timeDisplay;
    }

    /**
     * Get sorted WFH days (helper method for view)
     */
    public function getSortedWfhDays($wfhDays)
    {
        if (!$wfhDays) return 'None';

        $dayOrder = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        $wfhDaysArray = explode(',', $wfhDays);

        usort($wfhDaysArray, function ($a, $b) use ($dayOrder) {
            return array_search($a, $dayOrder) - array_search($b, $dayOrder);
        });

        return implode(', ', array_map(function($day) {
            return substr($day, 0, 3);
        }, $wfhDaysArray));
    }

    /**
     * Get sorted rest days (helper method for view)
     */
    public function getSortedRestDays($restDays)
    {
        if (!$restDays) return 'None';

        $dayOrder = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        $restDaysArray = explode(',', $restDays);

        usort($restDaysArray, function ($a, $b) use ($dayOrder) {
            return array_search($a, $dayOrder) - array_search($b, $dayOrder);
        });

        return implode(', ', array_map(function($day) {
            return substr($day, 0, 3);
        }, $restDaysArray));
    }
}