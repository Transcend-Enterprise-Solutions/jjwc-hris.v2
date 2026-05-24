<div class="w-full flex flex-col justify-center">
    <div class="w-full bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-md">
        <h1 class="text-lg font-bold text-center text-black dark:text-white mb-6">Overtime Approval</h1>

        <!-- Flash Messages -->
        @if (session()->has('message'))
            <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-md dark:bg-green-900 dark:border-green-600 dark:text-green-300">
                {{ session('message') }}
            </div>
        @endif

        @if (session()->has('error'))
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-md dark:bg-red-900 dark:border-red-600 dark:text-red-300">
                {{ session('error') }}
            </div>
        @endif

        <!-- Overtime Type Selection Modal -->
        @if ($showOvertimeTypeModal)
            <div class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-white dark:bg-gray-800 rounded-lg p-6 w-96">
                    <h2 class="text-lg font-bold mb-4 text-black dark:text-white">Select Overtime Type</h2>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-400 mb-2">
                            Overtime Type
                        </label>
                        <select wire:model="selectedOvertimeType"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white dark:border-gray-600">
                            @foreach($availableOvertimeTypes as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <button wire:click="closeOvertimeTypeModal"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 dark:bg-gray-600 dark:text-white dark:hover:bg-gray-500">
                            Cancel
                        </button>
                        <button wire:click="approveWithOvertimeType"
                            class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 dark:bg-green-700 dark:hover:bg-green-800">
                            Approve
                        </button>
                    </div>
                </div>
            </div>
        @endif

        <!-- Export Button -->
        <div class="mb-4 flex justify-end">
            <button wire:click="exportOvertimeToExcel"
                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Export to Excel
            </button>
        </div>

        <!-- Filters -->
        <div class="mb-6 flex flex-col sm:flex-row items-end justify-between space-y-4 sm:space-y-0 gap-4">
            <!-- Search Input -->
            <div class="w-full sm:w-1/5">
                <label for="search" class="block text-sm font-medium text-gray-700 dark:text-slate-400 mb-1">Search</label>
                <input type="text" id="search" wire:model.live="searchTerm"
                    class="px-2 py-1.5 block w-full shadow-sm sm:text-sm border border-gray-400 hover:bg-gray-300 rounded-md dark:hover:bg-slate-600 dark:border-slate-600 dark:text-gray-300 dark:bg-gray-800"
                    placeholder="Enter employee name or ID">
            </div>

            <!-- Month Filter -->
            <div class="w-full sm:w-1/6">
                <label for="monthFilter" class="block text-sm font-medium text-gray-700 dark:text-slate-400 mb-1">Month</label>
                <select id="monthFilter" wire:model.live="selectedMonth"
                    class="px-2 py-1.5 block w-full shadow-sm sm:text-sm border border-gray-400 hover:bg-gray-300 rounded-md dark:hover:bg-slate-600 dark:border-slate-600 dark:text-gray-300 dark:bg-gray-800">
                    <option value="1">January</option>
                    <option value="2">February</option>
                    <option value="3">March</option>
                    <option value="4">April</option>
                    <option value="5">May</option>
                    <option value="6">June</option>
                    <option value="7">July</option>
                    <option value="8">August</option>
                    <option value="9">September</option>
                    <option value="10">October</option>
                    <option value="11">November</option>
                    <option value="12">December</option>
                </select>
            </div>

            <!-- Year Filter -->
            <div class="w-full sm:w-1/6">
                <label for="yearFilter" class="block text-sm font-medium text-gray-700 dark:text-slate-400 mb-1">Year</label>
                <select id="yearFilter" wire:model.live="selectedYear"
                    class="px-2 py-1.5 block w-full shadow-sm sm:text-sm border border-gray-400 hover:bg-gray-300 rounded-md dark:hover:bg-slate-600 dark:border-slate-600 dark:text-gray-300 dark:bg-gray-800">
                    @for ($year = now()->year; $year >= now()->year - 5; $year--)
                        <option value="{{ $year }}">{{ $year }}</option>
                    @endfor
                </select>
            </div>

            <!-- Status Filter -->
            <div class="w-full sm:w-1/5">
                <label for="statusFilter" class="block text-sm font-medium text-gray-700 dark:text-slate-400 mb-1">Status</label>
                <select id="statusFilter" wire:model.live="approvalStatusFilter"
                    class="px-2 py-1.5 block w-full shadow-sm sm:text-sm border border-gray-400 hover:bg-gray-300 rounded-md dark:hover:bg-slate-600 dark:border-slate-600 dark:text-gray-300 dark:bg-gray-800">
                    <option value="all">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="approved">Approved</option>
                    <option value="rejected">Rejected</option>
                </select>
            </div>

            <!-- Office Division Filter -->
            <div class="w-full sm:w-1/5">
                <label for="officeDivision" class="block text-sm font-medium text-gray-700 dark:text-slate-400 mb-1">Office Division</label>
                <select id="officeDivision" wire:model.live="selectedDivision"
                    class="px-2 py-1.5 block w-full shadow-sm sm:text-sm border border-gray-400 hover:bg-gray-300 rounded-md dark:hover:bg-slate-600 dark:border-slate-600 dark:text-gray-300 dark:bg-gray-800">
                    <option value="">All Divisions</option>
                    @foreach($officeDivisions as $division)
                        <option value="{{ $division->id }}">{{ $division->office_division }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto text-sm">
            <table class="min-w-full bg-white dark:bg-gray-800 overflow-hidden">
                <thead class="bg-gray-200 dark:bg-gray-700 rounded-xl">
                    <tr class="whitespace-nowrap">
                        <th class="px-4 py-2 text-left">Employee</th>
                        <th class="px-4 py-2 text-center">Employee ID</th>
                        <th class="px-4 py-2 text-center">Date</th>
                        <th class="px-4 py-2 text-center">Day</th>
                        <th class="px-4 py-2 text-center">Location</th>
                        <th class="px-4 py-2 text-center">Time In</th>
                        <th class="px-4 py-2 text-center">Break Out</th>
                        <th class="px-4 py-2 text-center">Break In</th>
                        <th class="px-4 py-2 text-center">Time Out</th>
                        <th class="px-4 py-2 text-center">Late</th>
                        <th class="px-4 py-2 text-center">Undertime</th>
                        <th class="px-4 py-2 text-center">Overtime</th>
                        <th class="px-4 py-2 text-center">OT Type</th>
                        <th class="px-4 py-2 text-center">Hours Rendered</th>
                        <th class="px-4 py-2 text-center">Status</th>
                        <th class="px-4 py-2 text-center">Updated By</th>
                        <th class="px-4 py-2 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($overtimeRecords as $record)
                        <tr class="whitespace-nowrap border-b border-gray-200 dark:border-gray-700">
                            <td class="px-4 py-2 text-left">
                                <div class="flex gap-3 items-center">
                                    @if ($record->profile_photo_path)
                                        <img src="{{ route('profile-photo.file', ['filename' => basename($record->profile_photo_path)]) }}"
                                            alt="{{ $record->user_name ?? 'No User Assigned' }}"
                                            width="32" height="32"
                                            class="w-10 h-10 rounded-full object-cover border border-gray-500">
                                    @else
                                        <div class="w-10 h-10 rounded-full bg-gray-500 border border-gray-500 dark:bg-gray-600 flex items-center justify-center text-white text-xs font-medium">
                                            {{ strtoupper(substr(($record->user_name ?? 'No User Assigned'), 0, 1)) }}{{ strtoupper(substr(explode(' ', ($record->user_name ?? 'No User Assigned'))[1] ?? '', 0, 1)) }}
                                        </div>
                                    @endif
                                    <span>{{ $record->user_name }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-2 text-center">{{ $record->emp_code }}</td>
                            <td class="px-4 py-2 text-center">{{ Carbon\Carbon::parse($record->date)->format('M d, Y') }}</td>
                            <td class="px-4 py-2 text-center">{{ $record->day_of_week }}</td>
                            <td class="px-4 py-2 text-center">{{ $record->location ?? 'N/A' }}</td>
                            
                            <!-- TIME IN - CHANGED TO 12-HOUR FORMAT -->
                            <td class="px-4 py-2 text-center">
                                @if($record->up_time_in)
                                    {{ is_string($record->up_time_in) ? \Carbon\Carbon::createFromFormat('H:i:s', $record->up_time_in)->format('h:i A') : $record->up_time_in->format('h:i A') }}
                                @elseif($record->time_in)
                                    {{ is_string($record->time_in) ? \Carbon\Carbon::createFromFormat('H:i:s', $record->time_in)->format('h:i A') : $record->time_in->format('h:i A') }}
                                @else
                                    --:--
                                @endif
                            </td>
                            
                            <!-- BREAK OUT - CHANGED TO 12-HOUR FORMAT -->
                            <td class="px-4 py-2 text-center">
                                @if($record->up_break_out)
                                    {{ is_string($record->up_break_out) ? \Carbon\Carbon::createFromFormat('H:i:s', $record->up_break_out)->format('h:i A') : $record->up_break_out->format('h:i A') }}
                                @elseif($record->break_out)
                                    {{ is_string($record->break_out) ? \Carbon\Carbon::createFromFormat('H:i:s', $record->break_out)->format('h:i A') : $record->break_out->format('h:i A') }}
                                @else
                                    --:--
                                @endif
                            </td>
                            
                            <!-- BREAK IN - CHANGED TO 12-HOUR FORMAT -->
                            <td class="px-4 py-2 text-center">
                                @if($record->up_break_in)
                                    {{ is_string($record->up_break_in) ? \Carbon\Carbon::createFromFormat('H:i:s', $record->up_break_in)->format('h:i A') : $record->up_break_in->format('h:i A') }}
                                @elseif($record->break_in)
                                    {{ is_string($record->break_in) ? \Carbon\Carbon::createFromFormat('H:i:s', $record->break_in)->format('h:i A') : $record->break_in->format('h:i A') }}
                                @else
                                    --:--
                                @endif
                            </td>
                            
                            <!-- TIME OUT - CHANGED TO 12-HOUR FORMAT -->
                            <td class="px-4 py-2 text-center">
                                @if($record->up_time_out)
                                    {{ is_string($record->up_time_out) ? \Carbon\Carbon::createFromFormat('H:i:s', $record->up_time_out)->format('h:i A') : $record->up_time_out->format('h:i A') }}
                                @elseif($record->time_out)
                                    {{ is_string($record->time_out) ? \Carbon\Carbon::createFromFormat('H:i:s', $record->time_out)->format('h:i A') : $record->time_out->format('h:i A') }}
                                @else
                                    --:--
                                @endif
                            </td>
                            
                            <td class="px-4 py-2 text-center">
                                <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                    {{ $record->up_late ?? $record->late ?? '00:00' }}
                                </span>
                            </td>
                            <td class="px-4 py-2 text-center">
                                <span class="px-2 py-1 text-xs rounded-full bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200">
                                    {{ $record->up_ut ?? $record->ut ?? '00:00' }}
                                </span>
                            </td>
                            <td class="px-4 py-2 text-center">
                                <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                    {{ $record->up_ot ?? $record->overtime ?? '00:00' }}
                                </span>
                            </td>
                            <td class="px-4 py-2 text-center">
                                @if($record->ot_approval_status === 'approved')
                                    @if($record->ot_type === 'night_differential')
                                        <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                            Night Differential
                                        </span>
                                    @else
                                        <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                                            Regular
                                        </span>
                                    @endif
                                @else
                                    <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                                        Not Set
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-2 text-center">{{ $record->up_total_hours_rendered ?? $record->total_hours_rendered ?? '00:00' }}</td>
                            <td class="px-4 py-2 text-center">
                                @if($record->ot_approval_status === 'approved')
                                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                        Approved
                                    </span>
                                @elseif($record->ot_approval_status === 'rejected')
                                    <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                        Rejected
                                    </span>
                                @else
                                    <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                        Pending
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-2 text-center">{{ $record->updated_by ?? '-' }}</td>
                            <td class="px-4 py-2 text-center">
                                <div class="flex flex-col gap-1 items-center">
                                    @if($record->ot_approval_status !== 'approved')
                                        <button wire:click="openOvertimeTypeModal({{ $record->id }})"
                                                class="px-2 py-1 text-xs font-medium text-white bg-green-600 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 w-16">
                                            Approve
                                        </button>
                                    @endif

                                    @if($record->ot_approval_status !== 'rejected')
                                        <button wire:click="rejectOvertime({{ $record->id }})"
                                                onclick="return confirm('Are you sure you want to reject this overtime?')"
                                                class="px-2 py-1 text-xs font-medium text-white bg-red-600 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 w-16">
                                            Reject
                                        </button>
                                    @endif

                                    @if($record->ot_approval_status !== 'pending' && $record->ot_approval_status !== null)
                                        <button wire:click="resetToPending({{ $record->id }})"
                                                onclick="return confirm('Are you sure you want to reset this overtime to pending?')"
                                                class="px-2 py-1 text-xs font-medium text-white bg-yellow-600 rounded-md hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 w-16">
                                            Pending
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="17" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                No overtime records found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $overtimeRecords->links() }}
        </div>
    </div>
</div>