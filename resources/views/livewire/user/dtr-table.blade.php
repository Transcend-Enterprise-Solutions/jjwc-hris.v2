<div class="w-full flex justify-center">
    <div class="w-full bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-md">
        <h1 class="text-lg font-bold text-center text-black dark:text-white mb-6">My Daily Time Record</h1>

        <!-- Search and Date Range Picker -->
        <div class="mb-6 flex flex-col sm:flex-row items-end justify-between space-y-4 sm:space-y-0">
            <!-- Search -->
            <div class="w-full sm:w-1/3">
                <label for="searchTerm" class="block text-sm font-medium text-gray-700 dark:text-slate-400 mb-1">Search</label>
                <input type="text" id="searchTerm" wire:model.live="searchTerm"
                    placeholder="Search by date, day, or location..."
                    class="px-3 py-2 block w-full shadow-sm sm:text-sm border border-gray-400 hover:bg-gray-300 rounded-md
                        dark:hover:bg-slate-600 dark:border-slate-600
                        dark:text-gray-300 dark:bg-gray-800">
            </div>

            <!-- Date Range Picker -->
            <div class="w-full sm:w-2/3 flex flex-col sm:flex-row sm:justify-end sm:space-x-4">
                <div class="w-full sm:w-auto">
                    <label for="startDate" class="block text-sm font-medium text-gray-700 dark:text-slate-400 mb-1">Start Date</label>
                    <input type="date" id="startDate" wire:model.live="startDate"
                        class="px-2 py-1.5 block w-full shadow-sm sm:text-sm border border-gray-400 hover:bg-gray-300 rounded-md
                            dark:hover:bg-slate-600 dark:border-slate-600
                            dark:text-gray-300 dark:bg-gray-800">
                </div>

                <div class="w-full sm:w-auto mt-4 sm:mt-0">
                    <label for="endDate" class="block text-sm font-medium text-gray-700 dark:text-slate-400 mb-1">End Date</label>
                    <input type="date" id="endDate" wire:model.live="endDate"
                        class="px-2 py-1.5 block w-full shadow-sm sm:text-sm border border-gray-400 hover:bg-gray-300 rounded-md
                            dark:hover:bg-slate-600 dark:border-slate-600
                            dark:text-gray-300 dark:bg-gray-800">
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto text-sm">
            <table class="min-w-full bg-white dark:bg-gray-800 overflow-hidden">
                <thead class="bg-gray-200 dark:bg-gray-700 rounded-xl">
                    <tr class="whitespace-nowrap">
                        <th class="px-4 py-2 text-center">
                            <div class="flex items-center justify-center">
                                <button wire:click="sortBy('date')" class="{{ $sortField === 'date' ? 'text-blue-600' : 'text-gray-400' }}">
                                    <i class="bi bi-arrow-down-up"></i>
                                </button>
                                <span class="ml-2">Date</span>
                            </div>
                        </th>
                        <th class="px-4 py-2 text-center">Day</th>
                        <th class="px-4 py-2 text-center">Location</th>
                        <th class="px-4 py-2 text-center">Time In</th>
                        <th class="px-4 py-2 text-center">Break Out</th>
                        <th class="px-4 py-2 text-center">Break In</th>
                        <th class="px-4 py-2 text-center">Time Out</th>
                        <th class="px-4 py-2 text-center">
                            <div class="flex items-center justify-center">
                                <button wire:click="sortBy('late')" class="{{ $sortField === 'late' ? 'text-blue-600' : 'text-gray-400' }}">
                                    <i class="bi bi-arrow-down-up"></i>
                                </button>
                                <span class="ml-2">Late</span>
                            </div>
                        </th>
                        <th class="px-4 py-2 text-center">
                            <div class="flex items-center justify-center">
                                <button wire:click="sortBy('ut')" class="{{ $sortField === 'ut' ? 'text-blue-600' : 'text-gray-400' }}">
                                    <i class="bi bi-arrow-down-up"></i>
                                </button>
                                <span class="ml-2">Undertime</span>
                            </div>
                        </th>
                        <th class="px-4 py-2 text-center">Overtime</th>
                        <th class="px-4 py-2 text-center">Hours Rendered</th>
                        <th class="px-4 py-2 text-center">Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($dtrs as $dtr)
                        @php
                            // Use updated values if available, otherwise use original values
                            $timeIn = $dtr->up_time_in ?: $dtr->time_in;
                            $timeOut = $dtr->up_time_out ?: $dtr->time_out;
                            $breakIn = $dtr->up_break_in ?: $dtr->break_in;
                            $breakOut = $dtr->up_break_out ?: $dtr->break_out;

                            // Convert times to 12-hour format with AM/PM
                            $formatTime = function($time) {
                                if (!$time || $time === '--:--') return '--:--';
                                try {
                                    if (is_string($time)) {
                                        // Handle H:i:s or H:i format
                                        $carbon = \Carbon\Carbon::createFromFormat('H:i:s', $time);
                                    } else {
                                        $carbon = \Carbon\Carbon::parse($time);
                                    }
                                    return $carbon->format('h:i A');
                                } catch (\Exception $e) {
                                    return $time;
                                }
                            };

                            $displayTimeIn = $formatTime($timeIn);
                            $displayTimeOut = $formatTime($timeOut);
                            $displayBreakIn = $formatTime($breakIn);
                            $displayBreakOut = $formatTime($breakOut);

                            // Handle late with 10-minute rule
                            $lateTime = $dtr->up_late ?: $dtr->late;
                            if ($lateTime && $lateTime !== '00:00') {
                                list($hours, $minutes) = explode(':', $lateTime);
                                $totalMinutes = (intval($hours) * 60) + intval($minutes);
                                $displayLate = $totalMinutes <= 10 ? '00:00' : $lateTime;
                            } else {
                                $displayLate = $lateTime;
                            }

                            $ut = $dtr->up_ut ?: $dtr->ut;

                            // Only show approved overtime
                            $overtimeValue = $dtr->up_ot ?: $dtr->overtime;
                            $displayOvertime = ($dtr->ot_approval_status === 'approved' && $overtimeValue && $overtimeValue !== '00:00')
                                ? $overtimeValue
                                : '00:00';

                            $totalHours = $dtr->up_total_hours_rendered ?: $dtr->total_hours_rendered;

                            // Enhanced remarks logic
                            $remarks = $dtr->up_remarks ?? $dtr->remarks;

                            // Check for special holiday
                            $holiday = App\Models\Holiday::whereDate('holiday_date', $dtr->date)->first();
                            if ($holiday && !in_array($holiday->type, ['Regular', 'Special'])) {
                                $displayRemarks = $holiday->description;
                            } else if (strtolower($remarks) === 'late' && $displayLate === '00:00') {
                                $displayRemarks = 'Present';
                            } else if (str_contains(strtolower($remarks), 'overtime') && $dtr->ot_approval_status !== 'approved') {
                                $displayRemarks = 'Present';
                            } else {
                                $displayRemarks = $remarks;
                            }

                            // Determine badge styling based on remarks
                            $bgColor = 'bg-gray-200';
                            $textColor = 'text-gray-800';

                            switch (strtolower($displayRemarks)) {
                                case 'present':
                                    $bgColor = 'bg-green-400';
                                    $textColor = 'text-green-800';
                                    break;
                                case str_contains(strtolower($displayRemarks), 'holiday'):
                                case str_contains(strtolower($displayRemarks), 'leave'):
                                    $bgColor = 'bg-blue-400';
                                    $textColor = 'text-blue-800';
                                    break;
                                case 'absent':
                                    $bgColor = 'bg-red-400';
                                    $textColor = 'text-red-800';
                                    break;
                                case 'late/undertime':
                                    $bgColor = 'bg-yellow-400';
                                    $textColor = 'text-yellow-800';
                                    break;
                                case 'late':
                                    $bgColor = 'bg-orange-400';
                                    $textColor = 'text-orange-800';
                                    break;
                                case 'undertime':
                                    $bgColor = 'bg-amber-400';
                                    $textColor = 'text-amber-800';
                                    break;
                                case 'rest day':
                                    $bgColor = 'bg-purple-400';
                                    $textColor = 'text-purple-800';
                                    break;
                            }
                        @endphp
                        <tr class="whitespace-nowrap hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-4 py-2 text-center text-gray-900 dark:text-gray-100">{{ $dtr->date }}</td>
                            <td class="px-4 py-2 text-center text-gray-900 dark:text-gray-100">{{ $dtr->day_of_week }}</td>
                            <td class="px-4 py-2 text-center text-gray-900 dark:text-gray-100">{{ $dtr->location }}</td>
                            <td class="px-4 py-2 text-center text-gray-900 dark:text-gray-100">{{ $displayTimeIn }}</td>
                            <td class="px-4 py-2 text-center text-gray-900 dark:text-gray-100">{{ $displayBreakOut }}</td>
                            <td class="px-4 py-2 text-center text-gray-900 dark:text-gray-100">{{ $displayBreakIn }}</td>
                            <td class="px-4 py-2 text-center text-gray-900 dark:text-gray-100">{{ $displayTimeOut }}</td>
                            <td class="px-4 py-2 text-center {{ $displayLate !== '00:00' ? 'text-red-600 font-semibold' : 'text-gray-900 dark:text-gray-100' }}">
                                {{ $displayLate }}
                            </td>
                            <td class="px-4 py-2 text-center {{ $ut !== '00:00' ? 'text-yellow-600 font-semibold' : 'text-gray-900 dark:text-gray-100' }}">
                                {{ $ut }}
                            </td>
                            <td class="px-4 py-2 text-center {{ $displayOvertime !== '00:00' ? 'text-green-600 font-semibold' : 'text-gray-900 dark:text-gray-100' }}">
                                {{ $displayOvertime }}
                            </td>
                            <td class="px-4 py-2 text-center text-gray-900 dark:text-gray-100">{{ $totalHours }}</td>
                            <td class="px-4 py-2 text-center">
                                <span class="px-4 py-1 text-sm font-semibold rounded-full {{ $bgColor }} {{ $textColor }} inline-block">
                                    {{ $displayRemarks }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr class="whitespace-nowrap">
                            <td colspan="12" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                <i class="bi bi-inbox text-4xl mb-2"></i>
                                <p>No records found</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Page Size Selector and Pagination -->
        <div class="mt-4 flex flex-col sm:flex-row justify-between items-center space-y-4 sm:space-y-0">
            <div class="flex items-center space-x-2">
                <label for="pageSize" class="text-sm text-gray-700 dark:text-gray-300">Show:</label>
                <select id="pageSize" wire:model.live="pageSize"
                    class="px-2 py-1 border border-gray-400 rounded-md text-sm
                        dark:bg-gray-700 dark:border-slate-600 dark:text-gray-300">
                    @foreach($pageSizes as $size)
                        <option value="{{ $size }}">{{ $size }}</option>
                    @endforeach
                </select>
                <span class="text-sm text-gray-700 dark:text-gray-300">entries</span>
            </div>

            <div>
                {{ $dtrs->links() }}
            </div>
        </div>

    </div>
</div>