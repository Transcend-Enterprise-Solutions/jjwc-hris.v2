<div
    class="flex flex-col col-span-full sm:col-span-full bg-white dark:bg-slate-800 rounded-lg 
    border border-slate-200 dark:border-slate-700">
    <div class="px-5 pt-5">
        <header class="flex justify-between items-start mb-2">
            <h2 class="text-lg font-semibold text-slate-800 dark:text-slate-100 mb-2">Leave Availment Report</h2>
        </header>
    </div>

    <div class="p-4">
        <div class="grid grid-cols-12 gap-4">
            <!-- Total Leave by Status -->
            <div class="col-span-full sm:col-span-6 bg-blue-100 dark:bg-blue-800 p-4 rounded-lg shadow overflow-auto">
                <div class="text-sm font-semibold text-blue-800 dark:text-gray-100 mb-4">Total Leave
                    <hr class="border-t border-blue-200 dark:border-blue-600">
                    <label class="text-xs text-gray-900 dark:text-gray-300">Select Status: </label>
                    <ul class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-4 text-xs mt-2">
                        <li class="flex items-center">
                            <input type="checkbox" value="Pending" wire:model.live="statuses" class="h-4 w-4">
                            <label class="ml-2 text-gray-900 dark:text-gray-300">Pending</label>
                        </li>
                        <li class="flex items-center">
                            <input type="checkbox" value="Approved" wire:model.live="statuses" class="h-4 w-4">
                            <label class="ml-2 text-gray-900 dark:text-gray-300">Approved</label>
                        </li>
                        <li class="flex items-center">
                            <input type="checkbox" value="Approved by HR" wire:model.live="statuses" class="h-4 w-4">
                            <label class="ml-2 text-gray-900 dark:text-gray-300">Approved by HR</label>
                        </li>
                        <li class="flex items-center">
                            <input type="checkbox" value="Approved by Supervisor" wire:model.live="statuses"
                                class="h-4 w-4">
                            <label class="ml-2 text-gray-900 dark:text-gray-300">Approved by Supervisor</label>
                        </li>
                        <li class="flex items-center">
                            <input type="checkbox" value="Disapproved" wire:model.live="statuses" class="h-4 w-4">
                            <label class="ml-2 mr-2 text-gray-900 dark:text-gray-300">Disapproved</label>
                        </li>
                    </ul>
                </div>
                <ul class="mt-2">
                    <li class="text-sm text-blue-600 dark:text-blue-300">
                        <div class="flex bg-blue-50 dark:bg-blue-900 px-4 py-2 rounded-sm justify-between mb-2">
                            <div>
                                <p class="text-xs text-blue-800 dark:text-blue-200">
                                    <span class="ml-2 text-sm font-bold dark:text-white">{{ $leaveCount ?? 0 }}</span>
                                </p>
                            </div>

                            <!-- Export to Excel -->
                            <div class="w-1/5 sm:w-auto flex justify-center items-center h-full">
                                <button wire:loading.remove wire:target="exportToExcel" wire:click="exportToExcel"
                                    class="inline-flex items-center focus:outline-none" type="button"
                                    title="Export to Excel">
                                    <img class="flex dark:hidden" src="/images/export-excel.png" width="22"
                                        alt="">
                                    <img class="hidden dark:block" src="/images/export-excel-dark.png" width="22"
                                        alt="">
                                </button>
                                <div style="margin-right: 5px">
                                    <svg wire:loading wire:target="exportToExcel" xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 24 24" aria-hidden="true"
                                        class="size-5 fill-green-600 motion-safe:animate-spin dark:fill-green-600">
                                        <path
                                            d="M12,1A11,11,0,1,0,23,12,11,11,0,0,0,12,1Zm0,19a8,8,0,1,1,8-8A8,8,0,0,1,12,20Z"
                                            opacity=".25" />
                                        <path
                                            d="M10.14,1.16a11,11,0,0,0-9,8.92A1.59,1.59,0,0,0,2.46,12,1.52,1.52,0,0,0,4.11,10.7a8,8,0,0,1,6.66-6.61A1.42,1.42,0,0,0,12,2.69h0A1.57,1.57,0,0,0,10.14,1.16Z" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>

            <!-- Total Leave Card from Vacation Leave -->
            <div
                class="col-span-full sm:col-span-6 bg-yellow-100 dark:bg-yellow-800 p-4 rounded-lg shadow overflow-auto">
                <div class="text-sm font-semibold text-yellow-800 dark:text-gray-100 mb-4">Total Vacation Leave

                    <hr class="border-t border-yellow-200 dark:border-yellow-600">
                    <label class="text-xs text-gray-900 dark:text-gray-300">Select Status: </label>
                    <ul class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-4 text-xs mt-2">
                        <li class="flex items-center">
                            <input type="checkbox" value="Pending" wire:model.live="statusesForVL" class="h-4 w-4">
                            <label class="sm:ml-2 text-gray-900 dark:text-gray-300">Pending</label>
                        </li>
                        <li class="flex items-center">
                            <input type="checkbox" value="Approved" wire:model.live="statusesForVL" class="h-4 w-4">
                            <label class="sm:ml-2 text-gray-900 dark:text-gray-300">Approved</label>
                        </li>
                        <li class="flex items-center">
                            <input type="checkbox" value="Approved by HR" wire:model.live="statusesForVL"
                                class="h-4 w-4">
                            <label class="sm:ml-2 text-gray-900 dark:text-gray-300">Approved by HR</label>
                        </li>
                        <li class="flex items-center">
                            <input type="checkbox" value="Approved by Supervisor" wire:model.live="statusesForVL"
                                class="h-4 w-4">
                            <label class="sm:ml-2 text-gray-900 dark:text-gray-300">Approved by Supervisor</label>
                        </li>
                        <li class="flex items-center">
                            <input type="checkbox" value="Disapproved" wire:model.live="statusesForVL" class="h-4 w-4">
                            <label class="sm:ml-2 mr-2 text-gray-900 dark:text-gray-300">Disapproved</label>
                        </li>
                    </ul>
                </div>
                <ul class="mt-2">
                    <li class="text-sm text-yellow-600 dark:text-yellow-300">
                        <div class="flex bg-yellow-50 dark:bg-yellow-900 px-4 py-2 rounded-sm justify-between mb-2">
                            <div>
                                <p class="text-xs text-yellow-800 dark:text-yellow-200">
                                    <span
                                        class="ml-2 text-sm font-bold dark:text-white">{{ $vacationLeaveCount ?? 0 }}</span>
                                </p>
                            </div>

                            <!-- Export to Excel for Vacation Leave -->
                            <div class="w-1/5 sm:w-auto flex justify-center items-center h-full">
                                <button wire:loading.remove wire:click="exportVacationLeaveToExcel"
                                    class="inline-flex items-center focus:outline-none" type="button"
                                    title="Export to Excel">
                                    <img class="flex dark:hidden" src="/images/export-excel.png" width="22"
                                        alt="">
                                    <img class="hidden dark:block" src="/images/export-excel-dark.png" width="22"
                                        alt="">
                                </button>
                                <div style="margin-right: 5px">
                                    <svg wire:loading wire:target="exportVacationLeaveToExcel"
                                        xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true"
                                        class="size-5 fill-green-600 motion-safe:animate-spin dark:fill-green-600">
                                        <path
                                            d="M12,1A11,11,0,1,0,23,12,11,11,0,0,0,12,1Zm0,19a8,8,0,1,1,8-8A8,8,0,0,1,12,20Z"
                                            opacity=".25" />
                                        <path
                                            d="M10.14,1.16a11,11,0,0,0-9,8.92A1.59,1.59,0,0,0,2.46,12,1.52,1.52,0,0,0,4.11,10.7a8,8,0,0,1,6.66-6.61A1.42,1.42,0,0,0,12,2.69h0A1.57,1.57,0,0,0,10.14,1.16Z" />
                                    </svg>
                                </div>
                            </div>

                        </div>
                    </li>
                </ul>
            </div>

            <!-- Total Leave Card from Sick Leave -->
            <div
                class="col-span-full sm:col-span-6 bg-purple-100 dark:bg-purple-800 p-4 rounded-lg shadow overflow-auto">
                <div class="text-sm font-semibold text-purple-800 dark:text-gray-100 mb-4">Total Sick Leave

                    <hr class="border-t border-purple-200 dark:border-purple-600">
                    <label class="text-xs text-gray-900 dark:text-gray-300">Select Status: </label>
                    <ul class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-4 text-xs mt-2">
                        <li class="flex items-center">
                            <input type="checkbox" value="Pending" wire:model.live="statusesForSL" class="h-4 w-4">
                            <label class="sm:ml-2 text-gray-900 dark:text-gray-300">Pending</label>
                        </li>
                        <li class="flex items-center">
                            <input type="checkbox" value="Approved" wire:model.live="statusesForSL" class="h-4 w-4">
                            <label class="sm:ml-2 text-gray-900 dark:text-gray-300">Approved</label>
                        </li>
                        <li class="flex items-center">
                            <input type="checkbox" value="Approved by HR" wire:model.live="statusesForSL"
                                class="h-4 w-4">
                            <label class="sm:ml-2 text-gray-900 dark:text-gray-300">Approved by HR</label>
                        </li>
                        <li class="flex items-center">
                            <input type="checkbox" value="Approved by Supervisor" wire:model.live="statusesForSL"
                                class="h-4 w-4">
                            <label class="sm:ml-2 text-gray-900 dark:text-gray-300">Approved by Supervisor</label>
                        </li>
                        <li class="flex items-center">
                            <input type="checkbox" value="Disapproved" wire:model.live="statusesForSL"
                                class="h-4 w-4">
                            <label class="sm:ml-2 mr-2 text-gray-900 dark:text-gray-300">Disapproved</label>
                        </li>
                    </ul>
                </div>
                <ul class="mt-2">

                    <li class="text-sm text-purple-600 dark:text-purple-300">
                        <div class="flex bg-purple-50 dark:bg-purple-900 px-4 py-2 rounded-sm justify-between mb-2">
                            <div>
                                <p class="text-xs text-purple-800 dark:text-purple-200">
                                    <span
                                        class="ml-2 text-sm font-bold dark:text-white">{{ $sickLeaveCount ?? 0 }}</span>
                                </p>
                            </div>

                            <!-- Export to Excel -->
                            <div class="w-1/5 sm:w-auto flex justify-center items-center h-full">
                                <button wire:loading.remove wire:click="exportSickLeaveToExcel"
                                    class="inline-flex items-center focus:outline-none" type="button"
                                    title="Export to Excel">
                                    <img class="flex dark:hidden" src="/images/export-excel.png" width="22"
                                        alt="exportSickLeaveToExcel">
                                    <img class="hidden dark:block" src="/images/export-excel-dark.png" width="22"
                                        alt="exportSickLeaveToExcel">
                                </button>
                                <div style="margin-right: 5px">
                                    <svg wire:loading wire:target="exportSickLeaveToExcel"
                                        xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true"
                                        class="size-5 fill-green-600 motion-safe:animate-spin dark:fill-green-600">
                                        <path
                                            d="M12,1A11,11,0,1,0,23,12,11,11,0,0,0,12,1Zm0,19a8,8,0,1,1,8-8A8,8,0,0,1,12,20Z"
                                            opacity=".25" />
                                        <path
                                            d="M10.14,1.16a11,11,0,0,0-9,8.92A1.59,1.59,0,0,0,2.46,12,1.52,1.52,0,0,0,4.11,10.7a8,8,0,0,1,6.66-6.61A1.42,1.42,0,0,0,12,2.69h0A1.57,1.57,0,0,0,10.14,1.16Z" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </li>

                </ul>
            </div>

            {{-- LeaveAvailment Report --}}
            <div class="col-span-full sm:col-span-6 bg-green-100 dark:bg-green-800 p-4 rounded-lg shadow block">
                <div class="block sm:flex justify-between items-center">
                    <div class="flex justify-left items-center">
                        <h3 class="text-sm font-semibold text-green-800 dark:text-gray-100">
                            Leave Availment for the month of {{ $this->getFormattedMonth() ?? '-- --' }}
                        </h3>
                    </div>
                    <div class="w-full sm:w-auto flex items-center relative">
                        <input type="month" id="month" wire:model.live='month' value="{{ $month }}"
                            class="px-2 py-1.5 block w-32 sm:text-sm border border-teal-800 rounded-md
                            text-teal-800 dark:bg-gray-200 cursor-pointer">

                    </div>
                </div>
                <div class="mt-4">
                    <div class="flex bg-green-50 dark:bg-green-900 px-4 py-2 rounded-sm justify-between mb-2">
                        <div class="flex justify-center items-center">
                            <p class="text-xs text-green-800 dark:text-white">Total Leave
                                Applications:
                                <span class="ml-2 text-sm font-bold dark:text-white">{{ $totalLeaveCount }}</span>
                            </p>
                        </div>

                        <!-- Export to Excel -->
                        <div class="flex justify-center items-center w-1/5 h-full ml-2"
                            style="width: 32px; height: 32px;">
                            <button wire:loading.remove wire:target="leaveAvailmentExport"
                                wire:click="leaveAvailmentExport" class="inline-flex items-center focus:outline-none"
                                type="button" title="Export to Excel">
                                <img class="flex dark:hidden" src="/images/export-excel.png" alt="">
                                <img class="hidden dark:block" src="/images/export-excel-dark.png" alt="">
                            </button>
                            <div style="margin-right: 5px">
                                <svg wire:loading wire:target="leaveAvailmentExport"
                                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true"
                                    class="size-5 fill-green-600 motion-safe:animate-spin dark:fill-green-600">
                                    <path
                                        d="M12,1A11,11,0,1,0,23,12,11,11,0,0,0,12,1Zm0,19a8,8,0,1,1,8-8A8,8,0,0,1,12,20Z"
                                        opacity=".25" />
                                    <path
                                        d="M10.14,1.16a11,11,0,0,0-9,8.92A1.59,1.59,0,0,0,2.46,12,1.52,1.52,0,0,0,4.11,10.7a8,8,0,0,1,6.66-6.61A1.42,1.42,0,0,0,12,2.69h0A1.57,1.57,0,0,0,10.14,1.16Z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
