<div
    class="flex flex-col col-span-full sm:col-span-full bg-white dark:bg-slate-800 rounded-lg 
    border border-slate-200 dark:border-slate-700">
    <div class="px-5 pt-5">
        <header class="flex justify-between items-start mb-2">
            <h2 class="text-lg font-semibold text-slate-800 dark:text-slate-100 mb-2">Accumulated Leave Credits Report
            </h2>
        </header>
    </div>

    <div class="p-4">
        <div class="grid grid-cols-12 gap-4">
            <!-- All Credits -->
            <div
                class="col-span-full sm:col-span-6 bg-blue-100 dark:bg-blue-800 p-4 rounded-lg shadow flex justify-between">
                <div>
                    <h3 class="text-sm font-semibold text-blue-800 dark:text-gray-200">Total Employees have Leave Credits
                    </h3>
                    <p class="text-3xl font-bold text-blue-600 dark:text-gray-200">{{ $totalLeaveCreditsCount }}</p>
                </div>

                <!-- Export to Excel -->
                <div class="w-1/5 sm:w-auto flex justify-center items-center h-full">
                    <button wire:loading.remove wire:target="exportTotalCredits" wire:click="exportTotalCredits"
                        class="inline-flex items-center focus:outline-none" type="button" title="Export to Excel">
                        <img class="flex dark:hidden" src="/images/export-excel.png" width="22" alt="">
                        <img class="hidden dark:block" src="/images/export-excel-dark.png" width="22"
                            alt="">
                    </button>
                    <div style="margin-right: 5px">
                        <svg wire:loading wire:target="exportTotalCredits" xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 24 24" aria-hidden="true"
                            class="size-5 fill-green-600 motion-safe:animate-spin dark:fill-green-600">
                            <path d="M12,1A11,11,0,1,0,23,12,11,11,0,0,0,12,1Zm0,19a8,8,0,1,1,8-8A8,8,0,0,1,12,20Z"
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
