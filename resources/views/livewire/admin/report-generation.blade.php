<div class="w-full" x-data="{ selectedTab: 'hc' }" x-cloak>

    <div class="flex justify-center w-full">
        <div class="w-full bg-white rounded-2xl p-3 sm:p-6 shadow dark:bg-gray-800 overflow-x-visible">
            <div class="pb-4 mb-3 pt-4 sm:pt-0">
                <h1 class="text-lg font-bold text-center text-slate-800 dark:text-white">Report Generation</h1>
            </div>

            <!-- Table -->
            <div class="w-full">
                <div class="flex gap-2 overflow-x-auto -mb-2">
                    <button @click="selectedTab = 'hc'"
                        :class="{ 'font-bold dark:text-gray-300 dark:bg-gray-700 bg-gray-200 rounded-t-lg': selectedTab === 'hc', 'text-slate-700 font-medium dark:text-slate-300 dark:hover:text-white hover:text-black': selectedTab !== 'hc' }"
                        class="h-min px-4 pt-2 pb-4 text-sm whitespace-nowrap">
                        Employee
                    </button>
                    <button @click="selectedTab = 'la'"
                        :class="{ 'font-bold dark:text-gray-300 dark:bg-gray-700 bg-gray-200 rounded-t-lg': selectedTab === 'la', 'text-slate-700 font-medium dark:text-slate-300 dark:hover:text-white hover:text-black': selectedTab !== 'la' }"
                        class="h-min px-4 pt-2 pb-4 text-sm whitespace-nowrap">
                        Leave Availment
                    </button>
                    <button @click="selectedTab = 'alc'"
                        :class="{ 'font-bold dark:text-gray-300 dark:bg-gray-700 bg-gray-200 rounded-t-lg': selectedTab === 'alc', 'text-slate-700 font-medium dark:text-slate-300 dark:hover:text-white hover:text-black': selectedTab !== 'alc' }"
                        class="h-min px-4 pt-2 pb-4 text-sm whitespace-nowrap">
                        Accumulated Leave Credits
                    </button>
                </div>
                <div class="flex flex-col">
                    <div class="-my-2 overflow-x-auto">
                        <div class="inline-block w-full py-2 align-middle">

                            <div x-show="selectedTab === 'hc'">
                                @livewire('admin.reports.head-count')
                            </div>

                            <div x-show="selectedTab === 'la'">
                                @livewire('admin.reports.leave-availment')
                            </div>

                            <div x-show="selectedTab === 'alc'">
                                @livewire('admin.reports.accumulated-leave-credits')
                            </div>


                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>
