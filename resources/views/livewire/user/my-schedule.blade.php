<div class="w-full">
    <div class="flex flex-col sm:flex-row justify-between items-center w-full">
        <div class="w-full bg-white dark:bg-gray-800 rounded-2xl px-4 sm:px-6 shadow-lg">

            <!-- Header -->
            <div class="pt-6 pb-4">
                <h1 class="text-2xl font-bold text-center text-gray-800 dark:text-white">My Schedule</h1>
                <p class="text-center text-sm text-gray-500 dark:text-gray-400 mt-1">View your work schedule and holidays</p>
                @if($userRegion)
                    <p class="text-center text-xs text-gray-400 dark:text-gray-500 mt-1">
                        📍 Showing holidays for: <span class="font-semibold">{{ $userRegion->region_description }}</span>
                    </p>
                @endif
            </div>

            <!-- Navigation -->
            <div class="pb-4 flex flex-col sm:flex-row sm:items-center justify-between border-b border-gray-200 dark:border-gray-700">
                <div class="flex flex-row justify-between items-center w-full gap-4">
                    <button
                        wire:click="goToPreviousMonth"
                        class="flex items-center gap-2 text-gray-700 dark:text-gray-200 px-4 py-2 rounded-lg bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors text-sm sm:text-base font-medium shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                        <span class="hidden sm:inline">Previous</span>
                    </button>

                    <h2 class="text-xl font-bold text-center text-gray-800 dark:text-white flex-grow">
                        {{ Carbon\Carbon::create($currentYear, $currentMonth)->format('F Y') }}
                    </h2>

                    <button
                        wire:click="goToNextMonth"
                        class="flex items-center gap-2 text-gray-700 dark:text-gray-200 px-4 py-2 rounded-lg bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors text-sm sm:text-base font-medium shadow-sm">
                        <span class="hidden sm:inline">Next</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Legend -->
            <div class="py-4 flex flex-wrap gap-3 justify-center text-xs sm:text-sm border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center gap-2">
                    <span class="w-4 h-4 rounded bg-green-500"></span>
                    <span class="text-gray-700 dark:text-gray-300">Office</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-4 h-4 rounded bg-blue-500"></span>
                    <span class="text-gray-700 dark:text-gray-300">WFH</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-4 h-4 rounded bg-red-500"></span>
                    <span class="text-gray-700 dark:text-gray-300">Holiday</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-4 h-4 rounded bg-yellow-500"></span>
                    <span class="text-gray-700 dark:text-gray-300">No Schedule</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-4 h-4 rounded bg-gray-300 dark:bg-gray-600"></span>
                    <span class="text-gray-700 dark:text-gray-300">Weekend</span>
                </div>
            </div>

            <!-- Calendar -->
            <div class="py-6 overflow-x-auto" x-data x-auto-animate>
                <div class="grid grid-cols-7 gap-2 text-center min-w-[700px]">

                    <!-- Days of the week header -->
                    @foreach(['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'] as $day)
                        <div class="font-bold text-sm text-gray-600 dark:text-gray-400 pb-2">
                            <span class="hidden sm:inline">{{ $day }}</span>
                            <span class="sm:hidden">{{ substr($day, 0, 3) }}</span>
                        </div>
                    @endforeach

                    <!-- Calendar dates -->
                    @php
                        $firstDayOfMonth = Carbon\Carbon::create($currentYear, $currentMonth, 1);
                        $lastDayOfMonth = $firstDayOfMonth->copy()->endOfMonth();
                        $startOfCalendar = $firstDayOfMonth->copy()->startOfWeek(Carbon\Carbon::SUNDAY);
                        $endOfCalendar = $lastDayOfMonth->copy()->endOfWeek(Carbon\Carbon::SATURDAY);
                        $today = Carbon\Carbon::today();
                    @endphp

                    @for ($day = $startOfCalendar; $day <= $endOfCalendar; $day->addDay())
                        @php
                            $isCurrentMonth = $day->month == $currentMonth;
                            $isToday = $day->isSameDay($today);
                            $isWeekend = $day->isWeekend();
                        @endphp

                        <div class="relative border rounded-lg p-2 min-h-[90px] transition-all hover:shadow-md
                            {{ $isCurrentMonth ? 'bg-white dark:bg-gray-700 border-gray-200 dark:border-gray-600' : 'bg-gray-50 dark:bg-gray-800 border-gray-100 dark:border-gray-700' }}
                            {{ $isToday ? 'ring-2 ring-blue-500 dark:ring-blue-400' : '' }}">

                            <!-- Date number -->
                            <div class="flex justify-between items-start mb-1">
                                <span class="text-sm font-semibold
                                    {{ $isCurrentMonth ? 'text-gray-800 dark:text-gray-200' : 'text-gray-400 dark:text-gray-500' }}
                                    {{ $isToday ? 'text-blue-600 dark:text-blue-400' : '' }}">
                                    {{ $day->format('d') }}
                                </span>
                                @if($isToday)
                                    <span class="text-xs bg-blue-500 text-white px-2 py-0.5 rounded-full">Today</span>
                                @endif
                            </div>

                            <!-- Schedule content -->
                            <div class="space-y-1">
                                @php
                                    $scheduleFound = false;
                                    $currentSchedule = null;
                                    $dayName = $day->format('l');
                                    $isRestDay = false;
                                @endphp

                                @foreach($schedules as $schedule)
                                    @if($day->between($schedule->start_date, $schedule->end_date) && $isCurrentMonth)
                                        @php
                                            $currentSchedule = $schedule;
                                            $wfhDays = !empty($schedule->wfh_days) ? explode(',', $schedule->wfh_days) : [];
                                            $restDays = !empty($schedule->rest_days) ? explode(',', $schedule->rest_days) : [];
                                            $isRestDay = in_array($dayName, $restDays);
                                            $isWFH = in_array($dayName, $wfhDays);
                                            $scheduleFound = true;
                                        @endphp

                                        @if($isRestDay)
                                            <div class="bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-md px-2 py-1 text-xs font-medium">
                                                Rest Day
                                            </div>
                                        @elseif($isWFH)
                                            <div class="bg-blue-500 text-white rounded-md px-2 py-1 text-xs font-medium flex items-center justify-center gap-1">
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                                                </svg>
                                                WFH
                                            </div>
                                        @else
                                            <div class="bg-green-500 text-white rounded-md px-2 py-1 text-xs font-medium flex items-center justify-center gap-1">
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 110 2h-3a1 1 0 01-1-1v-2a1 1 0 00-1-1H9a1 1 0 00-1 1v2a1 1 0 01-1 1H4a1 1 0 110-2V4zm3 1h2v2H7V5zm2 4H7v2h2V9zm2-4h2v2h-2V5zm2 4h-2v2h2V9z" clip-rule="evenodd"/>
                                                </svg>
                                                Office
                                            </div>
                                        @endif

                                        @if($schedule->is_24hours || $schedule->is_overnight || $schedule->is_flexi)
                                            <div class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                                                @if($schedule->is_24hours)
                                                    <span class="bg-purple-100 dark:bg-purple-900 text-purple-700 dark:text-purple-300 px-1.5 py-0.5 rounded">24H</span>
                                                @elseif($schedule->is_overnight)
                                                    <span class="bg-indigo-100 dark:bg-indigo-900 text-indigo-700 dark:text-indigo-300 px-1.5 py-0.5 rounded">Night</span>
                                                @elseif($schedule->is_flexi)
                                                    <span class="bg-cyan-100 dark:bg-cyan-900 text-cyan-700 dark:text-cyan-300 px-1.5 py-0.5 rounded">Flexi</span>
                                                @endif
                                            </div>
                                        @endif

                                        @break
                                    @endif
                                @endforeach

                                @if(!$scheduleFound && $isCurrentMonth && !$isWeekend)
                                    <div class="bg-yellow-500 text-white rounded-md px-2 py-1 text-xs font-medium">
                                        No Schedule
                                    </div>
                                @elseif(!$scheduleFound && $isWeekend && !$isRestDay)
                                    <div class="text-gray-400 dark:text-gray-500 text-xs font-medium">
                                        Weekend
                                    </div>
                                @endif

                                <!-- ✅ UPDATED: Holidays with scope indicator -->
                                @foreach($holidays as $holiday)
                                    @if($holiday->holiday_date->isSameDay($day))
                                        <div class="bg-red-500 text-white rounded-md px-2 py-1 text-xs font-medium mt-1 flex items-center gap-1">
                                            <svg class="w-3 h-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"/>
                                            </svg>
                                            <span class="truncate">{{ $holiday->description }}</span>
                                        </div>
                                        <div class="flex items-center justify-between text-xs mt-1">
                                            <span class="text-red-600 dark:text-red-400 font-medium">
                                                {{ ucfirst($holiday->type) }}
                                            </span>
                                            @if($holiday->region_id)
                                                <span class="bg-orange-100 dark:bg-orange-900 text-orange-700 dark:text-orange-300 px-1.5 py-0.5 rounded text-xs">
                                                    Regional
                                                </span>
                                            @else
                                                <span class="bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300 px-1.5 py-0.5 rounded text-xs">
                                                    Nationwide
                                                </span>
                                            @endif
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endfor
                </div>
            </div>

            <!-- Schedule Summary (if available) -->
            @if($schedules->isNotEmpty())
                <div class="pb-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-3">Schedule Details</h3>
                    <div class="space-y-3">
                        @foreach($schedules as $schedule)
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                                    <div>
                                        <span class="text-gray-600 dark:text-gray-400">Period:</span>
                                        <span class="font-medium text-gray-800 dark:text-white ml-2">
                                            {{ $schedule->start_date->format('M d, Y') }} - {{ $schedule->end_date->format('M d, Y') }}
                                        </span>
                                    </div>
                                    <div>
                                        <span class="text-gray-600 dark:text-gray-400">Work Hours:</span>
                                        <span class="font-medium text-gray-800 dark:text-white ml-2">
                                            {{ $this->formatScheduleTime($schedule) }}
                                        </span>
                                    </div>
                                    <div>
                                        <span class="text-gray-600 dark:text-gray-400">WFH Days:</span>
                                        <span class="font-medium text-gray-800 dark:text-white ml-2">
                                            {{ $this->getSortedWfhDays($schedule->wfh_days) }}
                                        </span>
                                    </div>
                                    <div>
                                        <span class="text-gray-600 dark:text-gray-400">Rest Days:</span>
                                        <span class="font-medium text-gray-800 dark:text-white ml-2">
                                            {{ $this->getSortedRestDays($schedule->rest_days) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>