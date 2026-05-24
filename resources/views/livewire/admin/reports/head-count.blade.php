<div class="flex flex-col col-span-full sm:col-span-full bg-white dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-700">

    <div class="px-5 pt-5">
        <header class="flex justify-between items-start mb-2">
            <h2 class="text-lg font-semibold text-slate-800 dark:text-slate-100">Employee Reports</h2>
        </header>
    </div>

    <div class="p-4">
        <div class="grid grid-cols-12 gap-4">

            <!-- Total Employees Card -->
            <div class="col-span-full sm:col-span-6 bg-blue-100 dark:bg-blue-800 p-4 rounded-lg shadow flex justify-between">
                <div>
                    <h3 class="text-sm font-semibold text-blue-800 dark:text-gray-200">Total Employees</h3>
                    <p class="text-3xl font-bold text-blue-600 dark:text-gray-200">{{ $totalEmployees }}</p>
                </div>

                <!-- Export to Excel -->
                <div class="w-1/5 sm:w-auto flex justify-center items-center h-full">
                    <button wire:loading.remove wire:target="exportTotalEmployee" wire:click="exportTotalEmployee"
                        class="inline-flex items-center focus:outline-none"
                        type="button" title="Export to Excel">
                        <img class="flex dark:hidden" src="/images/export-excel.png" width="22" alt="">
                        <img class="hidden dark:block" src="/images/export-excel-dark.png" width="22" alt="">
                    </button>
                    <div style="margin-right: 5px">
                        <svg wire:loading wire:target="exportTotalEmployee" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true" class="size-5 fill-green-600 motion-safe:animate-spin dark:fill-green-600">
                            <path d="M12,1A11,11,0,1,0,23,12,11,11,0,0,0,12,1Zm0,19a8,8,0,1,1,8-8A8,8,0,0,1,12,20Z" opacity=".25" />
                            <path d="M10.14,1.16a11,11,0,0,0-9,8.92A1.59,1.59,0,0,0,2.46,12,1.52,1.52,0,0,0,4.11,10.7a8,8,0,0,1,6.66-6.61A1.42,1.42,0,0,0,12,2.69h0A1.57,1.57,0,0,0,10.14,1.16Z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- New Employees This Month Card -->
            <div class="col-span-full sm:col-span-6 bg-yellow-100 dark:bg-yellow-800 p-4 rounded-lg shadow block">
                <div class="block sm:flex justify-between items-center">
                    <div class="flex justify-left items-center">
                        <h3 class="text-sm font-semibold text-yellow-800 dark:text-gray-100">
                            @if($month)
                                Employees for the month of {{ $month ? \Carbon\Carbon::parse($month)->format('F') : '' }} {{ $month ? \Carbon\Carbon::parse($month)->format('Y') : '' }}
                            @else
                                New Employees This Month
                            @endif
                        </h3>
                    </div>
                    <div class="w-full sm:w-auto flex items-center relative">
                        <input type="month" id="month" wire:model.live='month' value="{{ $month }}"
                        class="px-2 py-1.5 block w-32 sm:text-sm border border-teal-800 rounded-md
                            text-teal-800 dark:bg-gray-200 cursor-pointer">

                        <!-- Export to Excel -->
                        <div class="flex justify-center items-center w-1/5 h-full ml-2" style="width: 32px; height: 32px;">
                            <button wire:loading.remove wire:target="exportTotalEmployeeThisMonth" wire:click="exportTotalEmployeeThisMonth"
                                class="inline-flex items-center focus:outline-none"
                                type="button" title="Export to Excel" wire:loading.remove>
                                <img class="flex dark:hidden" src="/images/export-excel.png" alt="">
                                <img class="hidden dark:block" src="/images/export-excel-dark.png" alt="">
                            </button>
                            <div style="margin-right: 5px">
                                <svg wire:loading wire:target="exportTotalEmployeeThisMonth" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true" class="size-5 fill-green-600 motion-safe:animate-spin dark:fill-green-600">
                                    <path d="M12,1A11,11,0,1,0,23,12,11,11,0,0,0,12,1Zm0,19a8,8,0,1,1,8-8A8,8,0,0,1,12,20Z" opacity=".25" />
                                    <path d="M10.14,1.16a11,11,0,0,0-9,8.92A1.59,1.59,0,0,0,2.46,12,1.52,1.52,0,0,0,4.11,10.7a8,8,0,0,1,6.66-6.61A1.42,1.42,0,0,0,12,2.69h0A1.57,1.57,0,0,0,10.14,1.16Z" />
                                </svg>

                            </div>
                        </div>
                    </div>
                </div>
                <div>
                    <p class="text-3xl font-bold text-yellow-700 dark:text-yellow-100">{{ $newEmployeesThisMonth }}</p>
                </div>
            </div>

            <!-- Work Group Distribution Card -->
            <div class="col-span-full sm:col-span-6 bg-purple-100 dark:bg-purple-800 p-4 rounded-lg shadow">
                <div class="text-sm font-semibold text-purple-800 dark:text-gray-100 mb-4 overflow-hidden">Work Group Distribution
                    <hr class="border-t border-purple-200 dark:border-purple-600">
                    <label for="allColActive" class="text-xs text-gray-900 dark:text-gray-300">Select Status: </label>
                    <div class="overflow-hidden">
                        <ul class="flex flex-wrap  text-xs mt-2 overflow-wrap wrap">
                            <li class="flex flex-col sm:flex-row items-center justify-center sm:mb-2">
                                <input id="allColActive" type="checkbox" wire:model.live="status.active" class="sm:mr-2 h-4 w-4">
                                <label for="allColActive" class="sm:mr-2 text-gray-900 dark:text-gray-300">Active</label>
                            </li>
                            <li class="flex flex-col sm:flex-row items-center justify-center sm:mb-2">
                                <input id="allColInactive" type="checkbox" wire:model.live="status.inactive" class="sm:mr-2 h-4 w-4">
                                <label for="allColInactive" class="sm:mr-2 text-gray-900 dark:text-gray-300">Inactive</label>
                            </li>
                            <li class="flex flex-col sm:flex-row items-center justify-center sm:mb-2">
                                <input id="allColResigned" type="checkbox" wire:model.live="status.resigned" class="sm:mr-2 h-4 w-4">
                                <label for="allColResigned" class="sm:mr-2 text-gray-900 dark:text-gray-300">Resigned</label>
                            </li>
                            <li class="flex flex-col sm:flex-row items-center justify-center sm:mb-2">
                                <input id="allColRetired" type="checkbox" wire:model.live="status.retired" class="sm:mr-2 h-4 w-4">
                                <label for="allColRetired" class="sm:mr-2 text-gray-900 dark:text-gray-300">Retired</label>
                            </li>
                        </ul>                    
                    </div>
                </div>
                <ul class="mt-2">
                    @foreach($officeDivisionCounts as $officeDiv)
                        <li class="text-sm text-purple-600 dark:text-purple-300">
                            <div class="flex bg-purple-50 dark:bg-purple-900 px-4 py-2 rounded-sm justify-between mb-2">
                                <div>
                                    <p class="text-xs text-purple-800 dark:text-purple-200">
                                        {{ $officeDiv->office_division }}: <span class="ml-2 text-sm font-bold">{{ $officeDiv->count }}</span>
                                    </p>
                                </div>

                                <!-- Export to Excel -->
                                <div class="w-1/5 sm:w-auto flex justify-center items-center h-full">
                                    <button wire:loading.remove wire:target="exportTotalEmployeeInOfficeDivision('{{ $officeDiv->office_division }}')" wire:click="exportTotalEmployeeInOfficeDivision('{{ $officeDiv->office_division }}')"
                                        class="inline-flex items-center focus:outline-none"
                                        type="button" title="Export to Excel">
                                        <img class="flex dark:hidden" src="/images/export-excel.png" width="22" alt="">
                                        <img class="hidden dark:block" src="/images/export-excel-dark.png" width="22" alt="">
                                    </button>
                                    <div style="margin-right: 5px">
                                        <svg wire:loading wire:target="exportTotalEmployeeInOfficeDivision('{{ $officeDiv->office_division }}')" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true" class="size-5 fill-green-600 motion-safe:animate-spin dark:fill-green-600">
                                            <path d="M12,1A11,11,0,1,0,23,12,11,11,0,0,0,12,1Zm0,19a8,8,0,1,1,8-8A8,8,0,0,1,12,20Z" opacity=".25" />
                                            <path d="M10.14,1.16a11,11,0,0,0-9,8.92A1.59,1.59,0,0,0,2.46,12,1.52,1.52,0,0,0,4.11,10.7a8,8,0,0,1,6.66-6.61A1.42,1.42,0,0,0,12,2.69h0A1.57,1.57,0,0,0,10.14,1.16Z" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>

            <!-- Daily Attendance -->
            <div class="col-span-full sm:col-span-6 bg-teal-50 dark:bg-teal-800 p-4 rounded-lg shadow flex flex-col items-left">
                <div class="block sm:flex justify-between items-center">
                    <div class="flex justify-left items-center">
                        <img class="flex" src="/images/calendar.png" alt="">
                        <h3 class="text-sm font-semibold text-teal-800 dark:text-gray-100">Daily Attendance</h3>
                    </div>
                    <div class="w-full sm:w-auto flex items-center relative">
                        <input type="date" id="date" wire:model.live='date' value="{{ $date }}"
                        class="px-2 py-1.5 block w-32 sm:text-sm border border-teal-800 rounded-md
                            text-teal-800 dark:bg-gray-200 cursor-pointer">

                         <!-- Export to Excel -->
                        <div class="flex justify-center items-center w-1/5 h-full ml-2" style="width: 32px; height: 32px;">
                            <button wire:loading.remove wire:target="exportTotalEmployeeDaily" wire:click="exportTotalEmployeeDaily"
                                class="inline-flex items-center focus:outline-none"
                                type="button" title="Export to Excel" wire:loading.remove>
                                <img class="flex dark:hidden" src="/images/export-excel.png" alt="">
                                <img class="hidden dark:block" src="/images/export-excel-dark.png" alt="">
                            </button>
                            <div style="margin-right: 5px">
                                <svg wire:loading wire:target="exportTotalEmployeeDaily" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true" class="size-5 fill-green-600 motion-safe:animate-spin dark:fill-green-600">
                                    <path d="M12,1A11,11,0,1,0,23,12,11,11,0,0,0,12,1Zm0,19a8,8,0,1,1,8-8A8,8,0,0,1,12,20Z" opacity=".25" />
                                    <path d="M10.14,1.16a11,11,0,0,0-9,8.92A1.59,1.59,0,0,0,2.46,12,1.52,1.52,0,0,0,4.11,10.7a8,8,0,0,1,6.66-6.61A1.42,1.42,0,0,0,12,2.69h0A1.57,1.57,0,0,0,10.14,1.16Z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-2 sm:mt-0">
                    <h3 class="text-xs text-teal-800 dark:text-gray-100">Total Present/Late {{ $date ? 'for ' . \Carbon\Carbon::parse($date)->format('F d, Y') : '' }}</h3>
                    <p class="text-3xl font-bold text-teal-700 dark:text-teal-100">{{ $dailyAttendance }}</p>
                </div>

            </div>

            <!-- Document Requests Card -->
            <div class="col-span-full sm:col-span-6 bg-green-100 dark:bg-green-800 p-4 rounded-lg shadow block">
                <div class="block sm:flex justify-between items-center">
                    <div class="flex justify-left items-center">
                        <h3 class="text-sm font-semibold text-green-800 dark:text-gray-100">
                            @if($month)
                                Employee Document Requests for {{ $docRequestMonth ? \Carbon\Carbon::parse($docRequestMonth)->format('F Y') : '' }}
                            @else
                                Employee Document Requests This Month
                            @endif
                        </h3>
                    </div>
                    <div class="w-full sm:w-auto flex items-center relative">
                        <input type="month" id="doc_request_month" wire:model.live='docRequestMonth' value="{{ $docRequestMonth }}"
                        class="px-2 py-1.5 block w-32 sm:text-sm border border-green-800 rounded-md
                            text-green-800 dark:bg-gray-200 cursor-pointer">

                        <!-- Export to Excel -->
                        <div class="flex justify-center items-center w-1/5 h-full ml-2" style="width: 32px; height: 32px;">
                            <button wire:loading.remove wire:target="exportDocRequests" wire:click="exportDocRequests"
                                class="inline-flex items-center focus:outline-none"
                                type="button" title="Export to Excel" wire:loading.remove>
                                <img class="flex dark:hidden" src="/images/export-excel.png" width="22" alt="">
                                <img class="hidden dark:block" src="/images/export-excel-dark.png" width="22" alt="">
                            </button>
                            <div style="margin-right: 5px">
                                <svg wire:loading wire:target="exportDocRequests" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true" class="size-5 fill-green-600 motion-safe:animate-spin dark:fill-green-600">
                                    <path d="M12,1A11,11,0,1,0,23,12,11,11,0,0,0,12,1Zm0,19a8,8,0,1,1,8-8A8,8,0,0,1,12,20Z" opacity=".25" />
                                    <path d="M10.14,1.16a11,11,0,0,0-9,8.92A1.59,1.59,0,0,0,2.46,12,1.52,1.52,0,0,0,4.11,10.7a8,8,0,0,1,6.66-6.61A1.42,1.42,0,0,0,12,2.69h0A1.57,1.57,0,0,0,10.14,1.16Z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
                <div>
                    <p class="text-3xl font-bold text-green-700 dark:text-green-100">{{ $docRequestsCount }}</p>
                </div>
            </div>
            
            {{-- Rating  --}}
            <div class="col-span-full sm:col-span-6 bg-blue-100 dark:bg-blue-800 p-4 rounded-lg shadow block">
                <div class="block sm:flex justify-between items-center">
                    <div class="flex justify-left items-center">
                        <h3 class="text-sm font-semibold text-blue-800 dark:text-gray-100">
                            @if($ratingsMonth)
                                Average Overall Ratings for the month of {{ \Carbon\Carbon::parse($ratingsMonth)->format('F') }} {{ \Carbon\Carbon::parse($ratingsMonth)->format('Y') }}
                            @else
                                Average Overall Ratings This Month
                            @endif
                        </h3>
                    </div>
                    <div class="w-full sm:w-auto flex items-center relative">
                        <input type="month" id="month" wire:model.live='ratingsMonth'
                            class="px-2 py-1.5 block w-32 sm:text-sm border border-teal-800 rounded-md text-teal-800 dark:bg-gray-200 cursor-pointer">

                        <!-- Export to Excel -->
                        <div class="flex justify-center items-center w-1/5 h-full ml-2" style="width: 32px; height: 32px;">
                            <button wire:loading.remove wire:target="exportAverageOverallRatings" wire:click="exportAverageOverallRatings"
                                class="inline-flex items-center focus:outline-none"
                                type="button" title="Export to Excel" wire:loading.remove>
                                <img class="flex dark:hidden" src="/images/export-excel.png" alt="">
                                <img class="hidden dark:block" src="/images/export-excel-dark.png" alt="">
                            </button>
                            <div style="margin-right: 5px">
                                <svg wire:loading wire:target="exportAverageOverallRatings" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true" class="size-5 fill-blue-600 motion-safe:animate-spin dark:fill-blue-600">
                                    <path d="M12,1A11,11,0,1,0,23,12,11,11,0,0,0,12,1Zm0,19a8,8,0,1,1,8-8A8,8,0,0,1,12,20Z" opacity=".25" />
                                    <path d="M10.14,1.16a11,11,0,0,0-9,8.92A1.59,1.59,0,0,0,2.46,12,1.52,1.52,0,0,0,4.11,10.7a8,8,0,0,1,6.66-6.61A1.42,1.42,0,0,0,12,2.69h0A1.57,1.57,0,0,0,10.14,1.16Z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
                <div>
                    <div class="mt-4">
                        <p class="text-xl font-bold text-blue-700 dark:text-blue-100">
                            {{ number_format($averageOverall, 2) }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
