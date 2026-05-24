<div class="w-full"
x-data="{ 
    tab: @entangle('tab'),
}"
x-cloak>


    <div class="w-full bg-white rounded-2xl p-3 sm:p-6 shadow dark:bg-gray-800 mb-4">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-lg font-bold text-slate-800 dark:text-white">Official Documents</h1>
        </div>

        <div class="overflow-x-auto">
            <div class="flex gap-2 overflow-x-auto">
                <button @click="tab = 'affidavits'"
                        :class="{ 'font-bold dark:text-gray-300 border-b-2 border-blue-500': tab === 'affidavits', 'text-slate-700 font-medium dark:text-slate-300 dark:hover:text-white hover:text-black': tab !== 'affidavits' }"
                        class="h-min px-4 py-2 text-sm text-nowrap">
                    Affidavits
                </button>
                <button @click="tab = 'notices'"
                        :class="{ 'font-bold dark:text-gray-300 border-b-2 border-blue-500': tab === 'notices', 'text-slate-700 font-medium dark:text-slate-300 dark:hover:text-white hover:text-black': tab !== 'notices' }"
                        class="h-min px-4 py-2 text-sm text-nowrap">
                    Notices
                </button>
                <button @click="tab = 'certificates'"
                        :class="{ 'font-bold dark:text-gray-300 border-b-2 border-blue-500': tab === 'certificates', 'text-slate-700 font-medium dark:text-slate-300 dark:hover:text-white hover:text-black': tab !== 'certificates' }"
                        class="h-min px-4 py-2 text-sm text-nowrap">
                    Certificates
                </button>
            </div>

            <div class="overflow-hidden border dark:border-gray-700 rounded-b-lg" x-show="tab == 'affidavits'">
                @livewire('admin.case-management')
            </div>

            <div class="overflow-hidden border dark:border-gray-700 rounded-b-lg" x-show="tab == 'notices'">
                @livewire('admin.notices-management')
            </div>

            <div class="overflow-hidden border dark:border-gray-700 rounded-b-lg" x-show="tab == 'certificates'">
                @livewire('admin.certificates-management')
            </div>

        </div>

    </div>
</div>
