<div class="w-full"
x-data="{
    selectedTab: @entangle('selectedTab'),
    selectedSubTab: @entangle('selectedSubTab'),
    adminSubTab: @entangle('adminSubTab'),
    sgTab: @entangle('sgTab'),
}"
x-cloak>

    <style>
        .spinner-border {
            display: inline-block;
            width: 1rem;
            height: 1rem;
            vertical-align: text-bottom;
            border: 2px solid currentColor;
            border-right-color: transparent;
            border-radius: 50%;
            -webkit-animation: spinner-border .75s linear infinite;
            animation: spinner-border .75s linear infinite;
            color: rgb(0, 255, 42);
        }
    </style>

    <div class="flex justify-center w-full">
        <div class="w-full bg-white rounded-2xl p-3 sm:p-6 shadow dark:bg-gray-800 overflow-x-visible">
            <div class="pb-4 mb-3 pt-4 sm:pt-0">
                <h1 class="text-lg font-bold text-center text-slate-800 dark:text-white">Organization Management</h1>
            </div>

            <div class="mb-6 flex flex-col sm:flex-row items-end justify-between">

                <div class="w-full sm:w-1/3 sm:mr-4" x-show="selectedTab === 'org' && selectedSubTab === 'headcount'">
                    <label for="search2" class="block text-sm font-medium text-gray-700 dark:text-slate-400 mb-1">Search</label>
                    <input type="text" id="search2" wire:model.live="search2"
                        class="px-2 py-1.5 block w-full shadow-sm text-xs border border-gray-400 hover:bg-gray-300 rounded-md
                            dark:hover:bg-slate-600 dark:border-slate-600
                            dark:text-gray-300 dark:bg-gray-800"
                        placeholder="Search name/id/position/office/divisions">
                </div>

                <div class="w-full sm:w-1/3 sm:mr-4" x-show="selectedTab === 'role'">
                    <label for="search" class="block text-sm font-medium text-gray-700 dark:text-slate-400 mb-1">Search</label>
                    <input type="text" id="search" wire:model.live="search"
                        class="px-2 py-1.5 block w-full shadow-sm text-xs border border-gray-400 hover:bg-gray-300 rounded-md
                            dark:hover:bg-slate-600 dark:border-slate-600
                            dark:text-gray-300 dark:bg-gray-800"
                        placeholder="Enter employee name or ID">
                </div>

                <div class="w-full sm:w-1/3 sm:mr-4" x-show="selectedTab === 'pos'">
                    <label for="search3" class="block text-sm font-medium text-gray-700 dark:text-slate-400 mb-1">Search</label>
                    <input type="text" id="search3" wire:model.live="search3"
                        class="px-2 py-1.5 block w-full shadow-sm text-xs border border-gray-400 hover:bg-gray-300 rounded-md
                            dark:hover:bg-slate-600 dark:border-slate-600
                            dark:text-gray-300 dark:bg-gray-800"
                        placeholder="Enter employee name or ID">
                </div>

                <div class="w-full sm:w-1/3 sm:mr-4" x-show="selectedTab === 'settings' || selectedSubTab === 'positions'">
                    <label for="search4" class="block text-sm font-medium text-gray-700 dark:text-slate-400 mb-1">Search</label>
                    <input type="text" id="search4" wire:model.live="search4"
                        class="px-2 py-1.5 block w-full shadow-sm text-xs border border-gray-400 hover:bg-gray-300 rounded-md
                            dark:hover:bg-slate-600 dark:border-slate-600
                            dark:text-gray-300 dark:bg-gray-800"
                        placeholder="Enter office/division">
                </div>

                <div class="w-full sm:w-2/3 flex flex-col sm:flex-row sm:justify-end sm:space-x-4" x-show="selectedTab === 'org'">

                    <div class="w-full sm:w-auto relative" x-data="{ open: false }" @click.outside="open = false">
                        <button @click="open = !open"
                            class="mt-4 sm:mt-1 inline-flex items-center dark:hover:bg-slate-600 dark:border-slate-600
                            justify-center px-2 py-1.5 text-sm font-medium tracking-wide
                            text-neutral-800 dark:text-neutral-200 transition-colors duration-200
                            rounded-lg border border-gray-400 hover:bg-gray-300 focus:outline-none"
                            type="button">
                            Filter Status
                            <i class="bi bi-chevron-down w-5 h-5 ml-2"></i>
                        </button>

                        <div x-show="open"
                            class="absolute top-12 z-20 p-3 border border-gray-400 bg-white rounded-lg
                            shadow-2xl dark:bg-gray-700 max-h-60 overflow-y-auto scrollbar-thin1" style="width: 130px">
                            <h6 class="mb-3 text-sm font-medium text-gray-900 dark:text-white">Select Status</h6>
                            <ul class="space-y-2 text-sm">
                                <li class="flex items-center" wire:click='toggleAllStats'>
                                    <label for="allCol" class="px-2 text-gray-900 bg-slate-200 rounded-sm">
                                    {{ $allStat ? 'Unselect' : 'Select' }} All
                                    </label>
                                </li>
                                <li class="flex items-center">
                                    <input id="allCol" type="checkbox" wire:model.live="status.active"
                                        class="h-4 w-4">
                                    <label for="allCol" class="ml-2 text-gray-900 dark:text-gray-300">Active</label>
                                </li>
                                <li class="flex items-center">
                                    <input id="allCol" type="checkbox" wire:model.live="status.inactive"
                                        class="h-4 w-4">
                                    <label for="allCol" class="ml-2 text-gray-900 dark:text-gray-300">Inactive</label>
                                </li>
                                <li class="flex items-center">
                                    <input id="allCol" type="checkbox" wire:model.live="status.resigned"
                                        class="h-4 w-4">
                                    <label for="allCol" class="ml-2 text-gray-900 dark:text-gray-300">Resigned</label>
                                </li>
                                <li class="flex items-center">
                                    <input id="allCol" type="checkbox" wire:model.live="status.retired"
                                        class="h-4 w-4">
                                    <label for="allCol" class="ml-2 text-gray-900 dark:text-gray-300">Retired</label>
                                </li>
                            </ul>
                        </div>
                    </div>

                </div>

            
                <div class="w-full sm:w-2/3 flex flex-col sm:flex-row sm:justify-end sm:space-x-4" x-show="selectedTab === 'pos'">

                        <!-- Export to Excel -->
                        {{-- <div class="relative inline-block text-left">
                            <button wire:click="exportExcel"
                                class="peer mt-4 sm:mt-1 inline-flex items-center dark:hover:bg-slate-600 dark:border-slate-600
                                justify-center px-4 py-1.5 text-sm font-medium tracking-wide
                                text-neutral-800 dark:text-neutral-200 transition-colors duration-200
                                rounded-lg border border-gray-400 hover:bg-gray-300 focus:outline-none"
                                type="button"  aria-describedby="excelExport">
                                <img class="flex dark:hidden" src="/images/export-excel.png" width="22" alt="">
                                <img class="hidden dark:block" src="/images/export-excel-dark.png" width="22" alt="">
                            </button>
                            <div id="excelExport" class="absolute -top-5 left-1/2 -translate-x-1/2 z-10 whitespace-nowrap rounded bg-gray-600 px-2 py-1 text-center text-sm text-white opacity-0 transition-all ease-out peer-hover:opacity-100 peer-focus:opacity-100 dark:text-black" role="tooltip">Export Roles</div>
                        </div> --}}
                </div>
            </div>

            <!-- Table -->
            <div class="w-full">
                <div class="flex flex-col">
                    <div class="flex gap-2 overflow-x-auto -mb-2">
                        <button @click="selectedTab = 'org'"
                                :class="{ 'font-bold dark:text-gray-300 dark:bg-gray-700 bg-gray-200 rounded-t-lg': selectedTab === 'org', 'text-slate-700 font-medium dark:text-slate-300 dark:hover:text-white hover:text-black': selectedTab !== 'org' }"
                                class="h-min px-4 pt-2 pb-4 text-sm text-nowrap">
                            Organization
                        </button>
                        <button @click="selectedTab = 'role'"
                                :class="{ 'font-bold dark:text-gray-300 dark:bg-gray-700 bg-gray-200 rounded-t-lg': selectedTab === 'role', 'text-slate-700 font-medium dark:text-slate-300 dark:hover:text-white hover:text-black': selectedTab !== 'role' }"
                                class="h-min px-4 pt-2 pb-4 text-sm text-nowrap">
                            Admin Role
                        </button>
                        <button @click="selectedTab = 'settings'"
                                :class="{ 'font-bold dark:text-gray-300 dark:bg-gray-700 bg-gray-200 rounded-t-lg': selectedTab === 'settings', 'text-slate-700 font-medium dark:text-slate-300 dark:hover:text-white hover:text-black': selectedTab !== 'settings' }"
                                class="h-min px-4 pt-2 pb-4 text-sm text-nowrap">
                            HR Settings
                        </button>
                        <button @click="selectedTab = 'sgstep'"
                                :class="{ 'font-bold dark:text-gray-300 dark:bg-gray-700 bg-gray-200 rounded-t-lg': selectedTab === 'sgstep', 'text-slate-700 font-medium dark:text-slate-300 dark:hover:text-white hover:text-black': selectedTab !== 'sgstep' }"
                                class="h-min px-4 pt-2 pb-4 text-sm text-nowrap">
                            SG/STEP
                        </button>
                        <button @click="selectedTab = 'dlforms'"
                                :class="{ 'font-bold dark:text-gray-300 dark:bg-gray-700 bg-gray-200 rounded-t-lg': selectedTab === 'dlforms', 'text-slate-700 font-medium dark:text-slate-300 dark:hover:text-white hover:text-black': selectedTab !== 'dlforms' }"
                                class="h-min px-4 pt-2 pb-4 text-sm text-nowrap">
                            Downloadable Forms
                        </button>
                    </div>
                    <div class="-my-2 overflow-x-auto">
                        <div class="inline-block w-full py-2 align-middle">
                            <div>
                                <div class="overflow-hidden border dark:border-gray-700 rounded-lg">
                                    <div x-show="selectedTab === 'org'">
                                        <div class="overflow-x-hidden">
                                            <div class="flex gap-2 overflow-x-auto dark:bg-gray-700 bg-gray-200 rounded-t-lg">
                                                <button @click="selectedSubTab = 'headcount'"
                                                        :class="{ 'font-bold text-blue-600 dark:text-blue-400 border-b-2 border-blue-600 dark:border-blue-400': selectedSubTab === 'headcount', 'text-slate-700 font-medium dark:text-slate-300 dark:hover:text-white hover:text-black': selectedSubTab !== 'headcount' }"
                                                        class="h-min px-4 pt-2 pb-2 text-xs no-wrap">
                                                    Headcount
                                                </button>
                                                <button @click="selectedSubTab = 'positions'"
                                                        :class="{ 'font-bold text-blue-600 dark:text-blue-400 border-b-2 border-blue-600 dark:border-blue-400': selectedSubTab === 'positions', 'text-slate-700 font-medium dark:text-slate-300 dark:hover:text-white hover:text-black': selectedSubTab !== 'positions' }"
                                                        class="h-min px-4 pt-2 pb-2 text-xs no-wrap">
                                                    Position Distribution   
                                                </button>
                                            </div>
                                        </div>

                                        <div class="p-5 text-neutral-500 dark:text-neutral-200 bg-gray-200 dark:bg-gray-700" x-show="selectedSubTab === 'headcount'">
                                            @forelse ($organizations as $division => $divisionData)
                                                <div class="block lg:flex w-full bg-white dark:bg-gray-600 mb-5 overflow-y-hidden" style="max-height: 650px">
                                                    <div class="pb-4 flex flex-col gap-2 sm:w-1/5 bg-gray-50 dark:bg-gray-800 relative">
                                                        <h2 class="text-wrap text-sm pb-2 h-min px-4 pt-2 font-bold dark:text-gray-300">
                                                            <i class="bi bi-building mr-2 text-emerald-500 dark:text-emerald-300"></i>
                                                            {{ $division }}
                                                        </h2>

                                                        <div class="flex justify-center items-center w-full">
                                                            <button wire:click="exportEmployees('{{ $division }}')"
                                                                class="peer inline-flex items-center justify-center px-2
                                                                text-sm font-medium tracking-wide text-green-500 hover:text-green-600 focus:outline-none"
                                                                title="Export List">
                                                                <img class="flex dark:hidden" src="/images/icons8-xls-export-dark.png" width="18" alt="" wire:loading.remove wire:target="exportEmployees('{{ $division }}')">
                                                                <img class="hidden dark:block" src="/images/icons8-xls-export-light.png" width="18" alt="" wire:loading.remove wire:target="exportEmployees('{{ $division }}')">
                                                                <div wire:loading wire:target="exportEmployees('{{ $division }}')" style="margin-left: 5px">
                                                                    <div class="spinner-border small text-primary" role="status">
                                                                    </div>
                                                                </div>
                                                            </button>
                                                        </div>

                                                        <div class="flex flex-col justify-center items-center" style="height: 120px">
                                                            <p class="text-5xl font-bold">{{ count($divisionData['users']) }}</p>
                                                            <p class="text-xs">Number of Employees</p>
                                                        </div>
                                                        
                                                        <!-- Summary Section -->
                                                        <div class="px-4 space-y-1">
                                                            @if($divisionData['totals']['Plantilla'] > 0)
                                                                <div class="flex justify-between items-center text-xs">
                                                                    <span class="font-medium">Plantilla:</span>
                                                                    <span class="font-bold">{{ $divisionData['totals']['Plantilla'] }}</span>
                                                                </div>
                                                            @endif
                                                            
                                                            @if($divisionData['totals']['COS'] > 0)
                                                                <div class="flex justify-between items-center text-xs">
                                                                    <span class="font-medium">COS:</span>
                                                                    <span class="font-bold">{{ $divisionData['totals']['COS'] }}</span>
                                                                </div>
                                                            @endif
                                                        </div>
                                            
                                                    </div>
                                                    <div class="pb-2 px-2 sm:w-4/5 flex flex-col">
                                                        <div class="overflow-x-auto overflow-y-hidden">
                                                            <table class="w-full">
                                                                <thead class="bg-white dark:bg-gray-600 text-gray-300 dark:text-gray-400">
                                                                    <tr class="whitespace-nowrap border-b border-gray-100 dark:border-gray-500">
                                                                        <th width="30%" scope="col" class="px-2 py-3 text-xs font-medium text-left uppercase sticky top-0 bg-white dark:bg-gray-600">
                                                                            Name
                                                                        </th>
                                                                        <th width="15%" scope="col" class="px-2 py-3 text-xs font-medium text-center uppercase sticky top-0 bg-white dark:bg-gray-600">
                                                                            Emp Number
                                                                        </th>
                                                                        <th width="30%" scope="col" class="px-2 py-3 text-xs font-medium text-center uppercase sticky top-0 bg-white dark:bg-gray-600">
                                                                            Position
                                                                        </th>
                                                                        <th width="20%" scope="col" class="px-2 py-3 text-xs font-medium text-center uppercase sticky top-0 bg-white dark:bg-gray-600">
                                                                            Appointment
                                                                        </th>
                                                                        <th width="5%" scope="col" class="px-2 py-3 text-xs font-medium text-center uppercase sticky top-0 bg-white dark:bg-gray-600">
                                                                            Status
                                                                        </th>
                                                                    </tr>
                                                                </thead>
                                                            </table>
                                                        </div>
                                                        <div class="overflow-y-auto flex-grow scrollbar-thin1" style="max-height: calc(450px - 3rem);">
                                                            <table class="w-full">
                                                                <tbody>
                                                                    @foreach(['Plantilla', 'COS'] as $appointmentType)
                                                                        @if(count($divisionData['by_appointment'][$appointmentType]) > 0)
                                                                            <!-- Appointment Type Header Row -->
                                                                            <tr class="bg-gray-100 dark:bg-gray-700">
                                                                                <td colspan="5" class="px-2 py-2 text-left text-xs font-bold 
                                                                                    @if($appointmentType == 'Plantilla') text-blue-600 dark:text-blue-400 
                                                                                    @elseif($appointmentType == 'COS') text-green-600 dark:text-green-400 
                                                                                    @else text-purple-600 dark:text-purple-400 @endif">
                                                                                    {{ $appointmentType }} ({{ count($divisionData['by_appointment'][$appointmentType]) }} Employees)
                                                                                </td>
                                                                            </tr>
                                                                            
                                                                            <!-- Employee Rows for this Appointment Type -->
                                                                            @foreach ($divisionData['by_appointment'][$appointmentType] as $user)
                                                                                <tr class="text-neutral-800 dark:text-neutral-200 border-b border-gray-100 dark:border-gray-500">
                                                                                    <td width="30%" class="px-2 py-2 text-left text-xs text-nowrap">
                                                                                        {{ $user->name ?? 'N/A' }}
                                                                                    </td>
                                                                                    <td width="15%" class="px-2 py-2 text-center text-xs text-nowrap">
                                                                                        {{ $user->emp_code }}
                                                                                    </td>
                                                                                    <td width="30%" class="px-2 py-2 text-center text-xs text-nowrap">
                                                                                        {{ $user->position ?? 'N/A' }}
                                                                                    </td>
                                                                                    <td width="20%" class="px-2 py-2 text-center text-xs text-nowrap uppercase">
                                                                                        @if($user->appointment != "cos" && $user->appointment != "ct")
                                                                                            @php
                                                                                                $appointment = explode(',', $user->appointment);
                                                                                            @endphp
                                                                                            @if($appointment[0] == 'pa')
                                                                                            Presidential Appointee
                                                                                            @else
                                                                                                Plantilla
                                                                                            @endif
                                                                                        @else
                                                                                            @if($user->appointment == "ct")
                                                                                                Co-Terminus
                                                                                            @else
                                                                                                {{ $user->appointment }}
                                                                                            @endif
                                                                                        @endif
                                                                                    </td>
                                                                                    <td width="5%" class="px-2 py-2 text-center text-xs text-nowrap">
                                                                                        <span title="
                                                                                            {{ $user->active_status == 0 ? 'Status: Inactive' : '' }}
                                                                                            {{ $user->active_status == 1 ? 'Status: Active' : '' }}
                                                                                            {{ $user->active_status == 2 ? 'Status: Resigned' : '' }}
                                                                                            {{ $user->active_status == 3 ? 'Status: Retired' : '' }}"
                                                                                            class="inline-block px-3 py-1 text-xs font-semibold
                                                                                            {{ $user->active_status == 0 ? 'text-red-400' : '' }}
                                                                                            {{ $user->active_status == 1 ? 'text-green-400' : '' }}
                                                                                            {{ $user->active_status == 2 ? 'text-yellow-400' : '' }}
                                                                                            {{ $user->active_status == 3 ? 'text-purple-400' : '' }}">
                                                                                            ⦿
                                                                                        </span>
                                                                                    </td>
                                                                                </tr>
                                                                            @endforeach
                                                                            
                                                                            <!-- Empty row for spacing between appointment types -->
                                                                            @if(!$loop->last)
                                                                                <tr>
                                                                                    <td colspan="5" class="px-2 py-1"></td>
                                                                                </tr>
                                                                            @endif
                                                                        @endif
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            @empty
                                                <div class="p-4 text-center text-gray-500 dark:text-gray-300">
                                                    No records found!
                                                </div>
                                            @endforelse
                                        </div>

                                        <div class="p-5 text-neutral-500 dark:text-neutral-200 bg-gray-200 dark:bg-gray-700" x-show="selectedSubTab === 'positions'">
                                            @foreach ($officeDivisions as $officeDivision)

                                                <!-- Office/Division Header -->
                                                <div class="flex justify-between items-center w-full py-1.5 bg-gray-50 dark:bg-gray-800 px-4">
                                                    <div class="flex items-end">
                                                        <i class="bi bi-building mr-2 text-emerald-500 dark:text-emerald-300"></i>
                                                        <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-300">{{ $officeDivision->office_division }}</h3>
                                                    </div>
                                                </div>

                                                <div class="w-full p-4 flex flex-col mb-6 bg-white dark:bg-gray-600">
                                                    <div class="w-full">
                                                        <div class="flex justify-left items-center w-full">
                                                            <h3 class="text-xs font-semibold text-gray-300 dark:text-gray-500">POSITIONS</h3>
                                                            <button wire:click="exportEmployeesPerUnit(null, {{ $officeDivision->id }})"
                                                                class="peer inline-flex items-center justify-center px-2
                                                                text-sm font-medium tracking-wide text-green-500 hover:text-green-600 focus:outline-none"
                                                                title="Export List">
                                                                <img class="flex dark:hidden" src="/images/icons8-xls-export-dark.png" width="18" alt="" wire:loading.remove wire:target="exportEmployeesPerUnit(null, {{ $officeDivision->id }})">
                                                                <img class="hidden dark:block" src="/images/icons8-xls-export-light.png" width="18" alt="" wire:loading.remove wire:target="exportEmployeesPerUnit(null, {{ $officeDivision->id }})">
                                                                <div wire:loading wire:target="exportEmployeesPerUnit(null, {{ $officeDivision->id }})" style="margin-left: 5px">
                                                                    <div class="spinner-border small text-primary" role="status">
                                                                    </div>
                                                                </div>
                                                            </button>
                                                        </div>
                                                        @if($officeDivision->positions->isNotEmpty())
                                                            <div class="pb-2 px-2 w-full flex flex-col">
                                                                <div class="overflow-x-auto overflow-y-hidden">
                                                                    <table class="w-full">
                                                                        <thead class="bg-white dark:bg-gray-600 text-gray-300 dark:text-gray-400">
                                                                            <tr class="whitespace-nowrap border-b border-gray-100 dark:border-gray-500">
                                                                                <th width="40%" scope="col" class="px-2 py-3 text-xs font-medium text-left uppercase sticky top-0 bg-white dark:bg-gray-600">
                                                                                    Position
                                                                                </th>
                                                                                <th width="40%" scope="col" class="px-2 py-3 text-xs font-medium text-left uppercase sticky top-0 bg-white dark:bg-gray-600">
                                                                                    Employee
                                                                                </th>
                                                                                <th width="20%" scope="col" class="px-2 py-3 text-xs font-medium text-center uppercase sticky top-0 bg-white dark:bg-gray-600">
                                                                                    Status
                                                                                </th>
                                                                            </tr>
                                                                        </thead>
                                                                    </table>
                                                                </div>
                                                                <div class="overflow-y-auto flex-grow scrollbar-thin1" style="max-height: calc(300px - 3rem);">
                                                                    <table class="w-full">
                                                                        <tbody>
                                                                            @foreach ($officeDivision->positions as $position)
                                                                                @php
                                                                                    $user1 = App\Models\User::where('position_id', $position->id)
                                                                                    ->where('office_division_id', $officeDivision->id)
                                                                                    ->whereNull('unit_id')
                                                                                    ->select('users.name', 'users.active_status')
                                                                                    ->first();
                                                                                @endphp
                                                                                <tr class="text-neutral-800 dark:text-neutral-200 border-b border-gray-100 dark:border-gray-500 {{ $user1 ? '!text-teal-500' : '' }}">
                                                                                    <td width="40%" class="px-2 py-2 text-left text-xs text-nowrap {{ $user1 ? '!font-bold' : '' }}">
                                                                                        {{ $position->position }}
                                                                                    </td>
                                                                                    <td width="40%" class="px-2 py-2 text-left text-xs text-nowrap">
                                                                                        <div class="flex gap-3 items-center">
                                                                                            @if ($user1 && $user1->profile_photo_path)
                                                                                                <img src="{{ route('profile-photo.file', ['filename' => basename($user1->profile_photo_path)]) }}"
                                                                                                    alt="{{ $user1 ? $user1->name : '' }}"
                                                                                                    style="width: 30px; height: 30px;"
                                                                                                    class="rounded-full object-cover border border-gray-500">
                                                                                            @elseif($user1 && $user1->name)
                                                                                                <div class="rounded-full bg-gray-500 border border-gray-500 dark:bg-gray-600 flex items-center justify-center text-white text-xs font-medium" style="width: 30px; height: 30px;">
                                                                                                    {{ strtoupper(substr(($user1 ? $user1->name : ''), 0, 1)) }}{{ strtoupper(substr(explode(' ', ($user1 ? $user1->name : ''))[1] ?? '', 0, 1)) }}
                                                                                                </div>
                                                                                            @endif
                                                                                            <span>{{ $user1 ? $user1->name : '' }}</span>
                                                                                        </div>  
                                                                                    </td>
                                                                                    <td width="20%" class="px-2 py-2 text-center text-xs text-nowrap">
                                                                                        <span title="
                                                                                            {{ $user1 && $user1->active_status == 0 ? 'Status: Inactive' : '' }}
                                                                                            {{ $user1 && $user1->active_status == 1 ? 'Status: Active' : '' }}
                                                                                            {{ $user1 && $user1->active_status == 2 ? 'Status: Resigned' : '' }}
                                                                                            {{ $user1 && $user1->active_status == 3 ? 'Status: Retired' : '' }}"
                                                                                            class="inline-block px-3 py-1 text-xs font-semibold
                                                                                            {{ $user1 && $user1->active_status == 0 ? 'text-red-400' : '' }}
                                                                                            {{ $user1 && $user1->active_status == 1 ? 'text-green-400' : '' }}
                                                                                            {{ $user1 && $user1->active_status == 2 ? 'text-yellow-400' : '' }}
                                                                                            {{ $user1 && $user1->active_status == 3 ? 'text-purple-400' : '' }}">
                                                                                            {{ $user1 ? '⦿' : '' }}
                                                                                        </span>
                                                                                    </td>
                                                                                </tr>
                                                                            @endforeach
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="w-full pt-4">
                                                        <h3 class="text-xs font-semibold text-gray-300 dark:text-gray-500">UNITS</h3>
                                                        <!-- Units under the Office/Division -->
                                                        @if($officeDivision->officeDivisionUnits->isNotEmpty())
                                                            <div class="flex-col overflow-x-auto pb-4">
                                                                @foreach ($officeDivision->officeDivisionUnits as $unit)
                                                                    <!-- Unit Header -->
                                                                    <div class="block px-4 border-l boder-gray-300 dark:border-gray-500 w-auto">
                                                                        <div class="flex justify-left items-center w-full py-1" style="min-width: 100px">
                                                                            <img class="flex dark:hidden" src="/images/unit-dark.png" width="15" alt="">
                                                                            <img class="hidden dark:block" src="/images/unit-light.png" width="15" alt="">
                                                                            <h4 class="ml-2 text-sm text-gray-500 dark:text-gray-300">{{ $unit->unit }}</h4>
                                                                            <button wire:click="exportEmployeesPerUnit({{ $unit->id }}, {{ $officeDivision->id }})"
                                                                                class="peer inline-flex items-center justify-center px-2
                                                                                text-sm font-medium tracking-wide text-green-500 hover:text-green-600 focus:outline-none"
                                                                                title="Export List">
                                                                                <img class="flex dark:hidden" src="/images/icons8-xls-export-dark.png" width="18" alt="" wire:loading.remove wire:target="exportEmployeesPerUnit({{ $unit->id }}, {{ $officeDivision->id }})">
                                                                                <img class="hidden dark:block" src="/images/icons8-xls-export-light.png" width="18" alt="" wire:loading.remove wire:target="exportEmployeesPerUnit({{ $unit->id }}, {{ $officeDivision->id }})">
                                                                                <div wire:loading wire:target="exportEmployeesPerUnit({{ $unit->id }}, {{ $officeDivision->id }})" style="margin-left: 5px">
                                                                                    <div class="spinner-border small text-primary" role="status">
                                                                                    </div>
                                                                                </div>
                                                                            </button>
                                                                        </div>
                                                                        <div class="flex justify-between items-center w-full">
                                                                            <h3 class="text-xs font-semibold text-gray-300 dark:text-gray-500">POSITIONS</h3>
                                                                        </div>
                                                                        <!-- Positions under the Unit -->
                                                                        @if($unit->positions->isNotEmpty())
                                                                            <div class="pb-2 pr-2 w-full flex flex-col" style="min-width: 100px">
                                                                                <div class="overflow-x-auto overflow-y-hidden">
                                                                                    <table class="w-full">
                                                                                        <thead class="bg-white dark:bg-gray-600 text-gray-300 dark:text-gray-400">
                                                                                            <tr class="whitespace-nowrap border-b border-gray-100 dark:border-gray-500" style="font-size: 10px">
                                                                                                <th width="40%" scope="col" class="px-2 py-3 font-medium text-left uppercase sticky top-0 bg-white dark:bg-gray-600">
                                                                                                    Position
                                                                                                </th>
                                                                                                <th width="40%" scope="col" class="px-2 py-3 font-medium text-left uppercase sticky top-0 bg-white dark:bg-gray-600">
                                                                                                    Employee
                                                                                                </th>
                                                                                                <th width="20%" scope="col" class="px-2 py-3 font-medium text-center uppercase sticky top-0 bg-white dark:bg-gray-600">
                                                                                                    Status
                                                                                                </th>
                                                                                            </tr>
                                                                                        </thead>
                                                                                    </table>
                                                                                </div>
                                                                                <div class="overflow-y-auto flex-grow scrollbar-thin1" style="max-height: calc(300px - 3rem);">
                                                                                    <table class="w-full">
                                                                                        <tbody>
                                                                                            @foreach ($unit->positions as $position)
                                                                                                @php
                                                                                                    $user = App\Models\User::where('users.office_division_id', $officeDivision->id)
                                                                                                                ->where('users.unit_id', $unit->id)
                                                                                                                ->where('users.position_id', $position->id)
                                                                                                                ->join('positions', 'positions.id', 'users.position_id')
                                                                                                                ->join('office_divisions', 'office_divisions.id', 'users.office_division_id')
                                                                                                                ->join('office_division_units', 'office_division_units.id', 'users.unit_id')
                                                                                                                ->select('users.name', 'users.active_status')
                                                                                                                ->first();
                                                                                                @endphp
                                                                                                <tr class="text-neutral-800 dark:text-neutral-200 border-b border-gray-100 dark:border-gray-500 {{ $user ? '!text-teal-500' : '' }}">
                                                                                                    <td width="40%" class="px-2 py-2 text-left text-xs text-nowrap {{ $user ? '!font-bold' : '' }}">
                                                                                                        {{ $position->position }}
                                                                                                    </td>
                                                                                                    <td width="40%" class="px-2 py-2 text-left text-xs text-nowrap">
                                                                                                        <div class="flex gap-3 items-center">
                                                                                                            @if ($user && $user->profile_photo_path)
                                                                                                                <img src="{{ route('profile-photo.file', ['filename' => basename($user->profile_photo_path)]) }}"
                                                                                                                    alt="{{ $user ? $user->name : '' }}"
                                                                                                                    style="width: 30px; height: 30px;"
                                                                                                                    class="rounded-full object-cover border border-gray-500">
                                                                                                            @elseif($user && $user->name)
                                                                                                                <div class="rounded-full bg-gray-500 border border-gray-500 dark:bg-gray-600 flex items-center justify-center text-white text-xs font-medium" style="width: 30px; height: 30px;">
                                                                                                                    {{ strtoupper(substr(($user ? $user->name : ''), 0, 1)) }}{{ strtoupper(substr(explode(' ', ($user ? $user->name : ''))[1] ?? '', 0, 1)) }}
                                                                                                                </div>
                                                                                                            @endif
                                                                                                            <span>{{ $user ? $user->name : '' }}</span>
                                                                                                        </div> 
                                                                                                    </td>
                                                                                                    <td width="20%" class="px-2 py-2 text-center text-xs text-nowrap">
                                                                                                        <span class="text-center" title="
                                                                                                            {{ $user && $user->active_status == 0 ? 'Status: Inactive' : '' }}
                                                                                                            {{ $user && $user->active_status == 1 ? 'Status: Active' : '' }}
                                                                                                            {{ $user && $user->active_status == 2 ? 'Status: Resigned' : '' }}
                                                                                                            {{ $user && $user->active_status == 3 ? 'Status: Retired' : '' }}"
                                                                                                            class="inline-block px-3 py-1 text-xs font-semibold
                                                                                                            {{ $user && $user->active_status == 0 ? 'text-red-400' : '' }}
                                                                                                            {{ $user && $user->active_status == 1 ? 'text-green-400' : '' }}
                                                                                                            {{ $user && $user->active_status == 2 ? 'text-yellow-400' : '' }}
                                                                                                            {{ $user && $user->active_status == 3 ? 'text-purple-400' : '' }}">
                                                                                                            {{ $user ? '⦿' : '' }}
                                                                                                        </span>
                                                                                                    </td>
                                                                                                </tr>
                                                                                            @endforeach
                                                                                        </tbody>
                                                                                    </table>
                                                                                </div>
                                                                            </div>
                                                                        @endif

                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>

                                            @endforeach
                                        </div>
                                    </div>
                                    <div x-show="selectedTab === 'role'">
                                        <div class="overflow-x-auto">
                                            <div class="flex gap-2 overflow-x-auto dark:bg-gray-700 bg-gray-200">
                                                <button @click="adminSubTab = 'admin'"
                                                        :class="{ 'font-medium text-blue-500 dark:bg-gray-700 bg-gray-200 rounded-t-lg': adminSubTab === 'admin', 'text-slate-700 font-medium dark:text-slate-300 dark:hover:text-white hover:text-black': adminSubTab !== 'admin' }"
                                                        class="h-min px-4 py-2 text-xs text-nowrap">
                                                    System Admin
                                                </button>
                                                <button @click="adminSubTab = 'access-settings'"
                                                        :class="{ 'font-medium text-blue-500 dark:bg-gray-700 bg-gray-200 rounded-t-lg': adminSubTab === 'access-settings', 'text-slate-700 font-medium dark:text-slate-300 dark:hover:text-white hover:text-black': adminSubTab !== 'access-settings' }"
                                                        class="h-min px-4 py-2 text-xs text-nowrap">
                                                    Access Settings
                                                </button>
                                            </div>

                                            <div class="overflow-hidden" x-show="adminSubTab == 'admin'">
                                                <div class="p-4 bg-white dark:bg-gray-800">
                                                    <div class="flex justify-between items-center">
                                                        <h1 class="text-lg font-bold text-left text-slate-800 dark:text-white">System Admin & Account Role</h1>

                                                        <div class="flex flex-col sm:flex-row sm:justify-end sm:space-x-4">
                                                            <div class="w-full sm:w-auto">
                                                                <button wire:click="toggleAddRole"
                                                                    class="mt-4 sm:mt-1 px-2 py-1.5 bg-green-500 text-white rounded-md text-sm
                                                                    hover:bg-green-600 focus:outline-none dark:bg-gray-700 w-full
                                                                    dark:hover:bg-green-600 dark:text-gray-300 dark:hover:text-white">
                                                                    <i class="bi bi-plus-lg mr-2"></i> Add Admin
                                                                </button>
                                                            </div>

                                                            <!-- Export to Excel -->
                                                            <div class="relative inline-block text-left">
                                                                <button wire:click="exportRoles"
                                                                    class="peer mt-4 sm:mt-1 inline-flex items-center dark:hover:bg-slate-600 dark:border-slate-600
                                                                    justify-center px-4 py-1.5 text-sm font-medium tracking-wide
                                                                    text-neutral-800 dark:text-neutral-200 transition-colors duration-200
                                                                    rounded-lg border border-gray-400 hover:bg-gray-300 focus:outline-none"
                                                                    type="button" title="Export Roles">
                                                                    <img class="flex dark:hidden" src="/images/export-excel.png" width="22" alt="">
                                                                    <img class="hidden dark:block" src="/images/export-excel-dark.png" width="22" alt="">
                                                                </button>
                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="overflow-x-auto">
                                                    <table class="w-full min-w-full">
                                                        <thead class="bg-gray-200 dark:bg-gray-700 rounded-xl">
                                                            <tr class="whitespace-nowrap">
                                                                <th scope="col" class="px-5 py-3 text-xs font-medium text-left uppercase">
                                                                    Admin Role
                                                                </th>
                                                                <th scope="col" class="px-5 py-3 text-xs font-medium text-left uppercase">
                                                                    Name
                                                                </th>
                                                                <th scope="col" class="px-5 py-3 text-xs font-medium text-center uppercase">
                                                                    Employee Number
                                                                </th>
                                                                <th scope="col" class="px-5 py-3 text-xs font-medium text-center uppercase">
                                                                    Office/Division
                                                                </th>
                                                                <th scope="col" class="px-5 py-3 text-xs font-medium text-center uppercase">
                                                                    Unit
                                                                </th>
                                                                <th scope="col" class="px-5 py-3 text-xs font-medium text-center uppercase">
                                                                    Position
                                                                </th>
                                                                <th class="px-5 py-3 text-gray-100 text-xs font-medium text-center uppercase sticky right-0 z-10 bg-gray-600 dark:bg-gray-600">
                                                                    Action
                                                                </th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="divide-y divide-neutral-200 dark:divide-gray-700">
                                                            @foreach ($admins as $admin)
                                                                <tr class="text-neutral-800 dark:text-neutral-200">
                                                                    <td class="px-5 py-4 text-left text-xs font-medium whitespace-nowrap">
                                                                        {{ $admin->role_name }}
                                                                    </td>
                                                                    <td class="px-5 py-4 text-left text-xs font-medium whitespace-nowrap">
                                                                        <div class="flex gap-3 items-center">
                                                                            @if ($admin->profile_photo_path)
                                                                                <img src="{{ route('profile-photo.file', ['filename' => basename($admin->profile_photo_path)]) }}"
                                                                                    alt="{{ $admin?->name ?? 'No User Assigned' }}"
                                                                                    style="width: 30px; height: 30px;"
                                                                                    class="rounded-full object-cover border border-gray-500">
                                                                            @else
                                                                                <div class="rounded-full bg-gray-500 border border-gray-500 dark:bg-gray-600 flex items-center justify-center text-white text-xs font-medium" style="width: 30px; height: 30px;">
                                                                                    {{ strtoupper(substr(($admin?->name ?? 'No User Assigned'), 0, 1)) }}{{ strtoupper(substr(explode(' ', ($admin?->name ?? 'No User Assigned'))[1] ?? '', 0, 1)) }}
                                                                                </div>
                                                                            @endif
                                                                            <span>{{ $admin->name }}</span>
                                                                        </div>
                                                                    </td>
                                                                    <td class="px-5 py-4 text-center text-xs font-medium whitespace-nowrap">
                                                                        @php
                                                                            $empCode = explode('-', $admin->emp_code);
                                                                        @endphp
                                                                        @if($admin->appointment == 'cos')
                                                                            {{ $empCode[1] ? 'D-' . substr($empCode[1], 1) : '' }}
                                                                        @else
                                                                            {{ $empCode[1] }}
                                                                        @endif
                                                                    </td>
                                                                    <td class="px-5 py-4 text-center text-xs font-medium whitespace-nowrap">
                                                                        {{ $admin->office_division }}
                                                                    </td>
                                                                    <td class="px-5 py-4 text-center text-xs font-medium whitespace-nowrap">
                                                                        {{ $admin->unit ?: '-' }}
                                                                    </td>
                                                                    <td class="px-5 py-4 text-center text-xs font-medium whitespace-nowrap">
                                                                        {{ $admin->position }}
                                                                    </td>
                                                                    <td class="px-5 py-4 text-sm font-medium text-center whitespace-nowrap sticky right-0 z-10 bg-white dark:bg-gray-800">
                                                                        <div class="relative">
                                                                            <button wire:click="toggleEditRole({{ $admin->id }})"
                                                                                class="peer inline-flex items-center justify-center px-4 py-2 -m-5
                                                                                -mr-2 text-sm font-medium tracking-wide text-blue-500 hover:text-blue-600
                                                                                focus:outline-none" title="Edit">
                                                                                <i class="fas fa-pencil-alt"></i>
                                                                            </button>
                                                                            <button wire:click="toggleDelete({{ $admin->id }}, 'role')"
                                                                                class=" text-red-600 hover:text-red-900 dark:text-red-600
                                                                                dark:hover:text-red-900" title="Delete">
                                                                                <i class="fas fa-trash"></i>
                                                                            </button>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>

                                                @if ($admins->isEmpty())
                                                    <div class="p-4 text-center text-gray-500 dark:text-gray-300">
                                                        No records!
                                                    </div>
                                                @endif
                                                <div class="p-5 text-neutral-500 dark:text-neutral-200 bg-gray-200 dark:bg-gray-700">
                                                    {{ $admins->links() }}
                                                </div>
                                            </div>

                                            <div class="overflow-hidden" x-show="adminSubTab == 'access-settings'">
                                                <div class="p-4 bg-white dark:bg-gray-800">
                                                    <div class="flex justify-between items-center">
                                                        <h1 class="text-lg font-bold text-left text-slate-800 dark:text-white">Admin Role Access Settings</h1>
                                                        <div class="flex flex-col sm:flex-row sm:justify-end sm:space-x-4">
                                                            <div class="w-full sm:w-auto">
                                                                <button wire:click="toggleAddRoleAccess"
                                                                    class="mt-4 sm:mt-1 px-2 py-1.5 bg-green-500 text-white rounded-md text-sm hover:bg-green-600 focus:outline-none dark:bg-gray-700 w-full dark:hover:bg-green-600 dark:text-gray-300 dark:hover:text-white">
                                                                    <i class="bi bi-plus-lg mr-2"></i> Add Role Access
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="overflow-x-auto">
                                                    <table class="w-full min-w-full">
                                                        <thead class="bg-gray-200 dark:bg-gray-700 rounded-xl">
                                                            <tr class="whitespace-nowrap">
                                                                <th scope="col" class="px-5 py-3 text-xs font-medium text-left uppercase">Role Name</th>
                                                                <th scope="col" class="px-5 py-3 text-xs font-medium text-center uppercase">Role Code</th>
                                                                <th scope="col" class="px-5 py-3 text-xs font-medium text-left uppercase">Accessible Modules</th>
                                                                <th class="px-5 py-3 text-gray-100 text-xs font-medium text-center uppercase sticky right-0 z-10 bg-gray-600 dark:bg-gray-600">Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="divide-y divide-neutral-200 dark:divide-gray-700">
                                                            @foreach ($roleAccesses as $roleAccess)
                                                                <tr class="text-neutral-800 dark:text-neutral-200">
                                                                    <td class="px-5 py-4 text-left text-xs font-medium whitespace-nowrap">
                                                                        {{ $roleAccess->role_name }}
                                                                    </td>
                                                                    <td class="px-5 py-4 text-center text-xs font-medium whitespace-nowrap">
                                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                                                            {{ $roleAccess->role_code }}
                                                                        </span>
                                                                    </td>
                                                                    <td class="px-5 py-4 text-left text-xs font-medium max-w-md">
                                                                        <div class="flex flex-wrap gap-1">
                                                                            @php $modules = $this->getModulesForRoleAccess($roleAccess); @endphp
                                                                            @if($modules->count() > 0)
                                                                                @foreach($modules as $module)
                                                                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                                                        {{ $module->module_name }}
                                                                                    </span>
                                                                                @endforeach
                                                                            @else
                                                                                <span class="text-gray-500 dark:text-gray-400 text-xs">No modules assigned</span>
                                                                            @endif
                                                                        </div>
                                                                    </td>
                                                                    <td class="px-5 py-4 text-sm font-medium text-center whitespace-nowrap sticky right-0 z-10 bg-white dark:bg-gray-800">
                                                                        <div class="relative">
                                                                            <button wire:click="toggleEditRoleAccess({{ $roleAccess->id }})"
                                                                                class="peer inline-flex items-center justify-center px-4 py-2 -m-5 -mr-2 text-sm font-medium tracking-wide text-blue-500 hover:text-blue-600 focus:outline-none" title="Edit">
                                                                                <i class="fas fa-pencil-alt"></i>
                                                                            </button>

                                                                            @if(!in_array($roleAccess->role_code, ['sa','pr', 'vp', 'avp', 'dm', 'hr', 'sv', 'pa']))
                                                                                <button wire:click="toggleDelete({{ $roleAccess->id }}, 'role access')"
                                                                                    class="text-red-600 hover:text-red-900 dark:text-red-600 dark:hover:text-red-900" title="Delete">
                                                                                    <i class="fas fa-trash"></i>
                                                                                </button>
                                                                            @endif
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>

                                                @if ($roleAccesses && $roleAccesses->isEmpty())
                                                    <div class="p-4 text-center text-gray-500 dark:text-gray-300">
                                                        No role access configurations found!
                                                    </div>
                                                @endif

                                                <div class="p-5 text-neutral-500 dark:text-neutral-200 bg-gray-200 dark:bg-gray-700">
                                                    {{ $roleAccesses ? $roleAccesses->links() : ''}}
                                                </div>
                                            </div>                   
                                            
                                        </div>
                                    </div>
                                    <div x-show="selectedTab === 'settings'">
                                        <div class="p-5 text-neutral-500 dark:text-neutral-200 bg-gray-200 dark:bg-gray-700">
                                            <table class="w-full min-w-full">
                                                <thead class="bg-gray-200 dark:bg-gray-700 rounded-xl" style="height: 20px">
                                                    <tr class="whitespace-nowrap">
                                                        <td>
                                                            <div class="flex flex-col md:flex-col lg:flex-row lg:justify-between items-center w-full mb-2">
                                                                <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase">OFFICE/DIVISION <span class="mx-2">|</span> <span>Units</span> <span class="mx-2">|</span> <span>Positions</span></h3>
                                                                <div>
                                                                    <button wire:click="toggleAddSettings('office/division')"
                                                                        class="peer inline-flex items-center justify-center px-4 py-2
                                                                        text-sm font-medium tracking-wide text-blue-500 hover:text-blue-600
                                                                        focus:outline-none" title="Add">
                                                                        <i title="Add" class="fas fa-plus text-green-500"></i>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </thead>
                                            </table>


                                            @foreach ($officeDivisions as $officeDivision)

                                                <!-- Office/Division Header -->
                                                <div class="flex justify-between items-center w-full py-1.5 bg-gray-50 dark:bg-gray-800 px-4">
                                                    <div class="flex items-end">
                                                        <i class="bi bi-building mr-2 text-emerald-500 dark:text-emerald-300"></i>
                                                        <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-300">{{ $officeDivision->office_division }}</h3>
                                                    </div>
                                                    <div class="relative px-2">
                                                        <button wire:click="toggleEditSettings({{ $officeDivision->id }}, 'office/division')"
                                                            class="peer inline-flex items-center justify-center py-2 lg:mr-2
                                                            text-xs font-medium tracking-wide text-blue-500 hover:text-blue-600
                                                            focus:outline-none" title="Edit">
                                                            <i class="fas fa-pencil-alt"></i>
                                                        </button>
                                                        <button wire:click="toggleDeleteSettings({{ $officeDivision->id }}, 'office/division')"
                                                            class="text-red-600 text-xs hover:text-red-900 dark:text-red-600
                                                            dark:hover:text-red-900" title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>

                                                <div class="w-full p-4 flex flex-col mb-6 bg-white dark:bg-gray-600">
                                                    <div class="w-full">
                                                        <div class="flex justify-left items-center w-full">
                                                            <h3 class="text-xs font-semibold text-gray-300 dark:text-gray-500">POSITIONS</h3>
                                                            <div class="relative">
                                                                @if($officeDivision->positions->isNotEmpty())
                                                                    <button wire:click="toggleEditPos({{ $officeDivision->id }}, 'position')"
                                                                        class="peer inline-flex items-center justify-center ml-4 mb-3
                                                                        text-xs font-medium tracking-wide text-blue-500 hover:text-blue-600
                                                                        focus:outline-none" title="Edit Positions">
                                                                        <i class="fas fa-pencil-alt" style="font-size: 10px"></i>
                                                                    </button>
                                                                @else
                                                                    <button wire:click="toggleAddPos({{ $officeDivision->id }}, 'position')"
                                                                        class="text-red-600 text-xs hover:text-red-900 dark:text-red-600 ml-4 mb-1
                                                                        dark:hover:text-red-900" title="Add Position">
                                                                        <i title="Add Position" class="fas fa-plus text-green-500"></i>
                                                                    </button>
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <!-- Positions directly under the office/division -->
                                                        @if($officeDivision->positions->isNotEmpty())
                                                            <ul class="ml-4 list-disc">
                                                                @foreach ($officeDivision->positions as $position)
                                                                    <li class="text-sm text-gray-500 dark:text-gray-400">{{ $position->position }}</li>
                                                                @endforeach
                                                            </ul>
                                                        @endif
                                                    </div>
                                                    <div class="w-full pt-4">
                                                        <h3 class="text-xs font-semibold text-gray-300 dark:text-gray-500">UNITS</h3>
                                                        <!-- Units under the office/division -->
                                                        @if($officeDivision->officeDivisionUnits->isNotEmpty())
                                                            <div class="flex overflow-x-auto pb-4">
                                                                @foreach ($officeDivision->officeDivisionUnits as $unit)
                                                                    <!-- Unit Header -->
                                                                    <div class="block px-4 border-l boder-gray-300 dark:border-gray-500">
                                                                        <div class="flex justify-left items-center w-full py-1">
                                                                            <img class="flex dark:hidden" src="/images/unit-dark.png" width="15" alt="">
                                                                            <img class="hidden dark:block" src="/images/unit-light.png" width="15" alt="">
                                                                            <h4 class="ml-2 text-sm text-gray-500 dark:text-gray-300">{{ $unit->unit }}</h4>
                                                                        </div>

                                                                        <div class="ml-6 flex justify-between items-center w-full">
                                                                            <h3 class="text-xs font-semibold text-gray-300 dark:text-gray-500">POSITIONS</h3>
                                                                            <div class="relative mr-4 mb-2">
                                                                                @if($unit->positions->isNotEmpty())
                                                                                    <button wire:click="toggleEditUnitPos({{ $officeDivision->id }}, {{ $unit->id }} ,'unit-position')"
                                                                                        class="peer inline-flex items-center justify-center
                                                                                        text-xs font-medium tracking-wide text-blue-500 hover:text-blue-600
                                                                                        focus:outline-none" title="Edit Positions">
                                                                                        <i class="fas fa-pencil-alt" style="font-size: 10px"></i>
                                                                                    </button>
                                                                                @else
                                                                                    <button wire:click="toggleAddUnitPos({{ $officeDivision->id }}, {{ $unit->id }} ,'unit-position')"
                                                                                        class="text-red-600 text-xs hover:text-red-900 dark:text-red-600
                                                                                        dark:hover:text-red-900" title="Add Position">
                                                                                        <i title="Add Position" class="fas fa-plus text-green-500"></i>
                                                                                    </button>
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                        <!-- Positions under the Unit -->
                                                                        @if($unit->positions->isNotEmpty())
                                                                            <ul class="ml-12 list-disc">
                                                                                @foreach ($unit->positions as $position)
                                                                                    <li class="text-sm text-gray-500 dark:text-gray-400">{{ $position->position }}</li>
                                                                                @endforeach
                                                                            </ul>
                                                                        @endif

                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>

                                            @endforeach
                                        </div>
                                    </div>
                                    <div x-show="selectedTab === 'sgstep'">
                                        <div class="overflow-x-auto">
                                            <table class="w-full min-w-full">
                                                <thead class="bg-gray-200 dark:bg-gray-700 rounded-xl" style="height: 20px">
                                                    <tr class="whitespace-nowrap">
                                                        <td><h3 class="p-4 text-sm font-semibold text-black dark:text-gray-200 uppercase">Salary Grade & Step</h3></td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <div class="flex gap-2 overflow-x-auto">
                                                                <button @click="sgTab = 'plantilla'"
                                                                        :class="{ 'font-bold dark:text-gray-300 border-b-2 border-blue-500': sgTab === 'plantilla', 'text-slate-700 font-medium dark:text-slate-300 dark:hover:text-white hover:text-black': sgTab !== 'plantilla' }"
                                                                        class="h-min px-4 py-2 text-sm text-nowrap">
                                                                    Plantilla
                                                                </button>
                                                                <button @click="sgTab = 'cos'"
                                                                        :class="{ 'font-bold dark:text-gray-300 border-b-2 border-blue-500': sgTab === 'cos', 'text-slate-700 font-medium dark:text-slate-300 dark:hover:text-white hover:text-black': sgTab !== 'cos' }"
                                                                        class="h-min px-4 py-2 text-sm text-nowrap">
                                                                    COS
                                                                </button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </thead>
                                            </table>


                                            <div class="p-4">
                                                <div class="grid grid-cols-12 gap-4" x-show="sgTab === 'plantilla'">
                                                    <div class="col-span-full sm:col-span-12  bg-gray-200 dark:bg-gray-700 rounded-lg shadow block">
                                                        <div class="flex flex-col items-center w-full pb-2 my-4">
                                                            <div class="flex justify-center sm:justify-between items-center mb-4 w-full px-4">
                                                                <h3 class="p-4 text-lg font-semibold text-black dark:text-gray-200 uppercase">Plantilla</h3>
                                                                <div class="flex items-center">
                                                                    <input type="file" id="salaryGrade" wire:model.live="file" style="display: none;" accept=".xlsx, .xls">
                                                                    @if($file)
                                                                        <p class="text-xs text-gray-600 dark:text-gray-100 mr-2">File selected: {{ $file->getClientOriginalName() }}</p>
                                                                    @endif
                                                                    @error('file') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                                                                    <button type="button" class="text-xs bg-green-500 hover:bg-green-700 text-white py-1 px-2 rounded" title="Import Salary Grade"
                                                                        onclick="document.getElementById('salaryGrade').click()">
                                                                        <i class="bi bi-upload" wire:target='file' wire:loading.remove></i>
                                                                        <div wire:loading wire:target="file">
                                                                            <div class="spinner-border small text-primary" role="status">
                                                                            </div>
                                                                        </div>
                                                                    </button>
                                                                    <button type="button" class="text-xs bg-green-500 hover:bg-green-700 text-white py-1 px-2 rounded ml-2" title="Export Salary Grade" wire:click='exportSalaryGrade'>
                                                                        <i class="bi bi-download" wire:target='exportSalaryGrade' wire:loading.remove></i>
                                                                        <div wire:loading wire:target="exportSalaryGrade">
                                                                            <div class="spinner-border small text-primary" role="status">
                                                                            </div>
                                                                        </div>
                                                                    </button>
                                                                </div>
                                                            </div>

                                                            <div class="w-full overflow-hidden border dark:border-gray-700">
                                                                <div class="overflow-x-auto">
                                                                    <table class="min-w-full bg-white dark:bg-gray-500">
                                                                        <thead class="bg-gray-100 dark:bg-slate-800 rounded-xl">
                                                                            <tr class="text-gra7-900 dark:text-gray-100 uppercase leading-normal" style="font-size: 11px">
                                                                                <th class="py-2 px-2 text-left">SG</th>
                                                                                <th class="py-2 px-2 text-left">Step 1</th>
                                                                                <th class="py-2 px-2 text-left">Step 2</th>
                                                                                <th class="py-2 px-2 text-left">Step 3</th>
                                                                                <th class="py-2 px-2 text-left">Step 4</th>
                                                                                <th class="py-2 px-2 text-left">Step 5</th>
                                                                                <th class="py-2 px-2 text-left">Step 6</th>
                                                                                <th class="py-2 px-2 text-left">Step 7</th>
                                                                                <th class="py-2 px-2 text-left">Step 8</th>
                                                                                <th class="py-2 px-2 text-center sticky right-0 z-10 bg-gray-100 dark:bg-slate-800">Actions</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody class="divide-y divide-neutral-200 dark:divide-gray-400 dark:bg-gray-700" style="font-size: 11px" wire:target='file' wire:loading.remove>
                                                                            @foreach ($salaryGrades as $salaryGrade)
                                                                                <tr class="border-b border-gray-200 hover:bg-gray-100 !hover:text-gray-800">
                                                                                    <td class="py-2 px-2 text-left whitespace-nowrap text-gray-800 dark:text-gray-300">
                                                                                        {{ $salaryGrade->salary_grade }}
                                                                                    </td>
                                                                                    @for ($i = 1; $i <= 8; $i++)
                                                                                        <td class="py-2 px-2 text-left">
                                                                                            {{ number_format($salaryGrade->{"step$i"}, 2) }}
                                                                                        </td>
                                                                                    @endfor
                                                                                    <td class="py-2 px-2 text-center sticky right-0 z-10 bg-white dark:bg-gray-700">
                                                                                        <div class="relative">
                                                                                            <button wire:click="editSG({{ $salaryGrade->id }})"
                                                                                                class="peer inline-flex items-center justify-center px-4 py-2 -m-5
                                                                                                -mr-2 text-xs font-medium tracking-wide text-blue-500 hover:text-blue-600
                                                                                                focus:outline-none" title="Edit">
                                                                                                <i class="fas fa-pencil-alt"></i>
                                                                                            </button>
                                                                                            <button wire:click="toggleDeleteSG({{ $salaryGrade->id }}, 'Salary Grade')"
                                                                                                class="text-xs text-red-600 hover:text-red-900 dark:text-red-600
                                                                                                dark:hover:text-red-900" title="Delete">
                                                                                                <i class="fas fa-trash"></i>
                                                                                            </button>
                                                                                        </div>
                                                                                    </td>
                                                                                </tr>
                                                                            @endforeach
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                                <div class="text-xs flex justify-center items-center mt-4" wire:target='file' wire:loading.remove>
                                                                    <button wire:click="openSGModal" class="bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-2 rounded">
                                                                        Add Salary Grade
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="grid grid-cols-12 gap-4" x-show="sgTab === 'cos'">
                                                        @livewire('admin.cos-salary-grade-settings')
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div x-show="selectedTab === 'dlforms'">
                                        <div class="overflow-x-auto">
                                            <livewire:admin.downloadable-forms />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- Add and Edit Role Modal --}}
    <x-modal id="roleModal" maxWidth="2xl" wire:model="editRole" centered>
        <div class="p-4">
            <div class="bg-slate-800 rounded-lg mb-4 dark:bg-gray-200 p-4 text-gray-50 dark:text-slate-900 font-bold">
                {{ $addRole ? 'Add' : 'Edit' }} Admin Role
                <button @click="show = false" class="float-right focus:outline-none" wire:click='resetVariables'>
                    <i class="fas fa-times"></i>
                </button>
            </div>
            {{-- Form fields --}}
            <form wire:submit.prevent='saveRole'>
                <div class="grid grid-cols-2 gap-4">

                    <div class="col-span-full sm:col-span-2">
                        <label for="userId" class="block text-sm font-medium text-gray-700 dark:text-slate-400">Employee Name <span class="text-red-500">*</span></label>
                        <select id="userId" wire:model='userId' class="mt-1 p-2 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:text-gray-300 dark:bg-gray-700"
                            {{ $addRole ? '' : 'disabled' }}>
                            <option value="{{ $userId }}">{{ $name ? $name : 'Select an employee' }}</option>
                            @foreach ($roleEmployees as $employee)
                                <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                            @endforeach
                        </select>
                        @error('userId')
                            <span class="text-red-500 text-sm">Please select an employee!</span>
                        @enderror
                    </div>

                    <div class="col-span-full sm:col-span-1">
                        <label for="user_role" class="block text-sm font-medium text-gray-700 dark:text-slate-400">Admin Role <span class="text-red-500">*</span></label>
                        <select id="userId" wire:model='user_role' class="mt-1 p-2 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:text-gray-300 dark:bg-gray-700">
                            <option value="">Select Role</option>
                            @foreach ($adminRoles as $role)
                                <option value="{{ $role->role_code }}">{{ $role->role_name }}</option>
                            @endforeach
                        </select>
                        @error('user_role')
                            <span class="text-red-500 text-sm">The account role is required!</span>
                        @enderror
                    </div>

                    {{-- <div class="col-span-full sm:col-span-1">
                        <label for="admin_email" class="block text-sm font-medium text-gray-700 dark:text-slate-400">Admin Email <span class="text-red-500">*</span></label>
                        <input type="text" id="admin_email" wire:model='admin_email' class="mt-1 p-2 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:text-gray-300 dark:bg-gray-700">
                        @error('admin_email')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div> --}}

                    <div class="col-span-full sm:col-span-1">
                        <label for="office_division" class="block text-sm font-medium text-gray-700 dark:text-slate-400">Office/Division</label>
                        <select id="office_division" wire:model.live='divId' class="mt-1 p-2 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:text-gray-300 dark:bg-gray-700">
                            <option class="text-gray-300" value="{{ $divId }}">{{ $office_division ? $office_division : 'Select office/division' }}</option>
                            @foreach($officeDivisions as $office)
                                <option value="{{ $office->id }}">{{ $office->office_division }}</option>
                            @endforeach
                        </select>
                        @error('divId')
                            <span class="text-red-500 text-sm">Please select office/division!</span>
                        @enderror
                    </div>

                    <div class="col-span-full sm:col-span-1">
                        <label for="unit" class="block text-sm font-medium text-gray-700 dark:text-slate-400">Unit</label>
                        <select id="unit" wire:model.live='unit' class="mt-1 p-2 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:text-gray-300 dark:bg-gray-700">
                            @if($divsUnits)
                                <option class="text-gray-300" value="{{ $unit }}">{{ $unitName ?: 'Select Unit' }}</option>
                                @foreach($divsUnits as $u)
                                    <option value="{{ $u->id }}">{{ $u->unit }}</option>
                                @endforeach
                            @else
                                <option class="text-gray-300" value="">Select Unit</option>
                            @endif
                        </select>
                    </div>

                    {{-- @if($addRole)
                        <div class="col-span-full sm:col-span-1">
                            <label for="password" class="block text-sm font-medium text-gray-700 dark:text-slate-400">Password <span class="text-red-500">*</span></label>
                            <input type="password" id="password" wire:model='password' class="mt-1 p-2 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:text-gray-300 dark:bg-gray-700">
                            @error('password')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-span-full sm:col-span-1">
                            <label for="cpassword" class="block text-sm font-medium text-gray-700 dark:text-slate-400">Confirm Password <span class="text-red-500">*</span></label>
                            <input type="password" id="cpassword" wire:model='cpassword' class="mt-1 p-2 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:text-gray-300 dark:bg-gray-700">
                            @error('cpassword')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                    @endif --}}

                    <div class="mt-4 flex justify-end col-span-2">
                        <button class="mr-2 bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                            <div wire:loading wire:target="saveRole" class="spinner-border small text-primary" role="status">
                            </div>
                            Save
                        </button>
                        <p @click="show = false" class="bg-gray-400 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded cursor-pointer" wire:click='resetVariables'>
                            Cancel
                        </p>
                    </div>
                </div>
            </form>
        </div>
    </x-modal>

    {{-- Add and Edit office/division or Position Modal --}}
    <x-modal id="posModal" maxWidth="2xl" wire:model="settings">
        <div class="p-4">
            <div class="bg-slate-800 rounded-lg mb-4 dark:bg-gray-200 p-4 text-gray-50 dark:text-slate-900 font-bold uppercase">
                {{ $add ? 'Add' : 'Edit' }} {{ $data }}
                <button @click="show = false" class="float-right focus:outline-none" wire:click='resetVariables'>
                    <i class="fas fa-times"></i>
                </button>
            </div>
            {{-- Form fields --}}
            <form wire:submit.prevent='saveSettings'>
                <div class="grid grid-cols-2 gap-4">

                    @if($add)
                            @if($data === "office/division")
                                <div class="col-span-2 relative">
                                    <label for="settings_data" class="block text-sm font-medium text-gray-700 dark:text-slate-400 uppercase">{{ $data }}</label>
                                    <input type="text" id="settings_data" wire:model='settings_data' class="mt-1 p-2 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:text-gray-300 dark:bg-gray-700">
                                    @error('settings_data')
                                        <span class="text-red-500 text-sm">This field is required!</span>
                                    @enderror
                                </div>
                                @foreach ($units as $index => $setting)
                                    <div class="col-span-1 relative">
                                        <label for="unit_{{ $index }}" class="block text-sm font-medium text-gray-700 dark:text-slate-400 uppercase">Unit</label>
                                        <input type="text" id="unit_{{ $index }}" wire:model='units.{{ $index }}.value' class="mt-1 p-2 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:text-gray-300 dark:bg-gray-700">

                                        <button type="button" wire:click="removeUnit({{ $index }})" class="absolute right-2 top-8 text-red-500 hover:text-red-700">
                                            <i class="fas fa-times"></i>
                                        </button>
                                        @error('units.' . $index . '.value')
                                            <span class="text-red-500 text-sm">This field is required!</span>
                                        @enderror
                                    </div>
                                @endforeach
                                <div class="col-span-2">
                                    <button type="button" wire:click="addNewUnit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                        Add Another Unit
                                    </button>
                                </div>
                            @endif
                            @if($data === "position")
                                @foreach ($settingsData as $index => $setting)
                                    <div class="col-span-2 grid grid-cols-2 gap-4 relative">
                                        <div>
                                            <label for="settings_data_{{ $index }}" class="block text-sm font-medium text-gray-700 dark:text-slate-400 uppercase">Position Name</label>
                                            <input type="text" id="settings_data_{{ $index }}" wire:model='settingsData.{{ $index }}.value' class="mt-1 p-2 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:text-gray-300 dark:bg-gray-700">
                                            @error('settingsData.' . $index . '.value')
                                                <span class="text-red-500 text-sm">This field is required!</span>
                                            @enderror
                                        </div>
                                        <div>
                                            <label for="level_{{ $index }}" class="block text-sm font-medium text-gray-700 dark:text-slate-400 uppercase">Level</label>
                                            <input type="number" id="level_{{ $index }}" wire:model='settingsData.{{ $index }}.level' min="1" class="mt-1 p-2 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:text-gray-300 dark:bg-gray-700">
                                            @error('settingsData.' . $index . '.level')
                                                <span class="text-red-500 text-sm">Level is required!</span>
                                            @enderror
                                        </div>
                                        <button type="button" wire:click="removeSetting({{ $index }})" class="absolute right-0 top-8 text-red-500 hover:text-red-700">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                @endforeach
                                <div class="col-span-2">
                                    <button type="button" wire:click="addNewSetting" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                        Add Another {{ $data }}
                                    </button>
                                </div>
                            @endif
                            @if($data === "unit-position")
                                @foreach ($settingsData as $index => $setting)
                                    <div class="col-span-2 grid grid-cols-2 gap-4 relative">
                                        <div>
                                            <label for="settings_data_{{ $index }}" class="block text-sm font-medium text-gray-700 dark:text-slate-400 uppercase">Position Name</label>
                                            <input type="text" id="settings_data_{{ $index }}" wire:model='settingsData.{{ $index }}.value' class="mt-1 p-2 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:text-gray-300 dark:bg-gray-700">
                                            @error('settingsData.' . $index . '.value')
                                                <span class="text-red-500 text-sm">This field is required!</span>
                                            @enderror
                                        </div>
                                        <div>
                                            <label for="level_{{ $index }}" class="block text-sm font-medium text-gray-700 dark:text-slate-400 uppercase">Level</label>
                                            <input type="number" id="level_{{ $index }}" wire:model='settingsData.{{ $index }}.level' min="1" class="mt-1 p-2 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:text-gray-300 dark:bg-gray-700">
                                            @error('settingsData.' . $index . '.level')
                                                <span class="text-red-500 text-sm">Level is required!</span>
                                            @enderror
                                        </div>
                                        <button type="button" wire:click="removeSetting({{ $index }})" class="absolute right-0 top-8 text-red-500 hover:text-red-700">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                @endforeach
                                <div class="col-span-2">
                                    <button type="button" wire:click="addNewSetting" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                        Add Another {{ $data }}
                                    </button>
                                </div>
                            @endif
                    @else
                        @if($data === "office/division")
                            <div class="col-span-2 relative">
                                <label for="settings_data" class="block text-sm font-medium text-gray-700 dark:text-slate-400 uppercase">{{ $data }}</label>
                                <input type="text" id="settings_data" wire:model='settings_data' class="mt-1 p-2 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:text-gray-300 dark:bg-gray-700">
                                @error('settings_data')
                                    <span class="text-red-500 text-sm">This field is required!</span>
                                @enderror
                            </div>
                            @foreach ($units as $index => $setting)
                                <div class="col-span-1 relative">
                                    <label for="unit_{{ $index }}" class="block text-sm font-medium text-gray-700 dark:text-slate-400 uppercase">Unit</label>
                                    <input type="text" id="unit_{{ $index }}" wire:model='units.{{ $index }}.value' class="mt-1 p-2 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:text-gray-300 dark:bg-gray-700">

                                    <button type="button" wire:click="removeUnit({{ $index }})" class="absolute right-2 top-8 text-red-500 hover:text-red-700">
                                        <i class="fas fa-times"></i>
                                    </button>
                                    @error('units.' . $index . '.value')
                                        <span class="text-red-500 text-sm">This field is required!</span>
                                    @enderror
                                </div>
                            @endforeach
                            <div class="col-span-2">
                                <button type="button" wire:click="addNewUnit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                    Add Another Unit
                                </button>
                            </div>
                        @endif
                        @if($data === "position")
                                @foreach ($settingsData as $index => $setting)
                                    <div class="col-span-2 grid grid-cols-2 gap-4 relative">
                                        <div>
                                            <label for="settings_data_{{ $index }}" class="block text-sm font-medium text-gray-700 dark:text-slate-400 uppercase">Position Name</label>
                                            <input type="text" id="settings_data_{{ $index }}" wire:model='settingsData.{{ $index }}.value' class="mt-1 p-2 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:text-gray-300 dark:bg-gray-700">
                                            @error('settingsData.' . $index . '.value')
                                                <span class="text-red-500 text-sm">This field is required!</span>
                                            @enderror
                                        </div>
                                        <div>
                                            <label for="level_{{ $index }}" class="block text-sm font-medium text-gray-700 dark:text-slate-400 uppercase">Level</label>
                                            <input type="number" id="level_{{ $index }}" wire:model='settingsData.{{ $index }}.level' min="1" class="mt-1 p-2 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:text-gray-300 dark:bg-gray-700">
                                            @error('settingsData.' . $index . '.level')
                                                <span class="text-red-500 text-sm">Level is required!</span>
                                            @enderror
                                        </div>
                                        <button type="button" wire:click="removeSetting({{ $index }})" class="absolute right-0 top-8 text-red-500 hover:text-red-700">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                @endforeach
                                <div class="col-span-2">
                                    <button type="button" wire:click="addNewSetting" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                        Add Another {{ $data }}
                                    </button>
                                </div>
                            @endif
                            @if($data === "unit-position")
                                @foreach ($settingsData as $index => $setting)
                                    <div class="col-span-2 grid grid-cols-2 gap-4 relative">
                                        <div>
                                            <label for="settings_data_{{ $index }}" class="block text-sm font-medium text-gray-700 dark:text-slate-400 uppercase">Position Name</label>
                                            <input type="text" id="settings_data_{{ $index }}" wire:model='settingsData.{{ $index }}.value' class="mt-1 p-2 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:text-gray-300 dark:bg-gray-700">
                                            @error('settingsData.' . $index . '.value')
                                                <span class="text-red-500 text-sm">This field is required!</span>
                                            @enderror
                                        </div>
                                        <div>
                                            <label for="level_{{ $index }}" class="block text-sm font-medium text-gray-700 dark:text-slate-400 uppercase">Level</label>
                                            <input type="number" id="level_{{ $index }}" wire:model='settingsData.{{ $index }}.level' min="1" class="mt-1 p-2 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:text-gray-300 dark:bg-gray-700">
                                            @error('settingsData.' . $index . '.level')
                                                <span class="text-red-500 text-sm">Level is required!</span>
                                            @enderror
                                        </div>
                                        <button type="button" wire:click="removeSetting({{ $index }})" class="absolute right-0 top-8 text-red-500 hover:text-red-700">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                @endforeach
                                <div class="col-span-2">
                                    <button type="button" wire:click="addNewSetting" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                        Add Another {{ $data }}
                                    </button>
                                </div>
                            @endif
                    @endif

                    <div class="mt-4 flex justify-end col-span-2">
                        <button type="submit" class="mr-2 bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                            <div wire:loading wire:target="saveSettings" class="spinner-border small text-primary" role="status">
                            </div>
                            Save
                        </button>
                        <button type="button" @click="show = false" class="bg-gray-400 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded cursor-pointer" wire:click='resetVariables'>
                            Cancel
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </x-modal>

    {{-- Delete Modal --}}
    <x-modal id="deleteModal" maxWidth="md" wire:model="deleteId" centered>
        <div class="p-4">
            <div class="mb-4 text-slate-900 dark:text-gray-100 font-bold">
                Confirm Deletion
                <button @click="show = false" class="float-right focus:outline-none">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300">
                Are you sure you want to delete this {{ $deleteMessage }}?
            </label>
            <form wire:submit.prevent='deleteData'>
                <div class="mt-4 flex justify-end col-span-1 sm:col-span-1">
                    <button class="mr-2 bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                        <div wire:loading wire:target="deleteData" style="margin-bottom: 5px;">
                            <div class="spinner-border small text-primary" role="status">
                            </div>
                        </div>
                        Delete
                    </button>
                    <p @click="show = false" class="bg-gray-400 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded cursor-pointer">
                        Cancel
                    </p>
                </div>
            </form>

        </div>
    </x-modal>

    {{-- Add Salary Grade Modal --}}
    <x-modal id="addSalaryGradeModal" maxWidth="2xl" wire:model="showSGModal">
        <div class="p-4">
            <div class="bg-slate-800 rounded-lg mb-4 dark:bg-gray-200 p-4 text-gray-50 dark:text-slate-900 font-bold uppercase">
                {{ $isEditing ? 'Edit' : 'Add' }} Salary Grade
                <button @click="show = false" class="float-right focus:outline-none">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            {{-- Form fields --}}
            <form wire:submit.prevent='saveSalaryGrade'>
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label for="salary_grade" class="block text-sm font-medium text-gray-700 dark:text-slate-400 uppercase">Salary Grade</label>
                        <input type="number" id="salary_grade" wire:model='salaryGradeData.salary_grade' {{ $isEditing ? 'readonly' : '' }}
                        class="mt-1 p-2 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:text-gray-300 dark:bg-gray-700" required>
                        @error('salaryGradeData.salary_grade') <span class="text-red-500 text-sm">This field is required!</span> @enderror
                    </div>
                    @for ($i = 1; $i <= 8; $i++)
                        <div class="col-span-full sm:col-span-1">
                            <label for="step{{ $i }}" class="block text-sm font-medium text-gray-700 dark:text-slate-400 uppercase">Step {{ $i }}</label>
                            <input type="number" step="0.01" id="step{{ $i }}" wire:model='salaryGradeData.step{{ $i }}' class="mt-1 p-2 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:text-gray-300 dark:bg-gray-700">
                            @error('salaryGradeData.step'.$i) <span class="text-red-500 text-sm">This field is required!</span> @enderror
                        </div>
                    @endfor
                    <div class="mt-4 flex justify-end col-span-2">
                        <button type="submit" class="mr-2 bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                            <div wire:loading wire:target="saveSalaryGrade" class="spinner-border small text-primary" role="status">
                            </div>
                            Save
                        </button>
                        <p @click="show = false" class="bg-gray-400 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded cursor-pointer">
                            Cancel
                        </p>
                    </div>
                </div>
            </form>
        </div>
    </x-modal>

    <!-- Add/Edit Role Access Modal -->
    <x-modal id="roleAccessModal" maxWidth="2xl" wire:model="showRoleAccessModal">
        <div class="p-4">
            <div class="bg-slate-800 rounded-lg mb-4 dark:bg-gray-200 p-4 text-gray-50 dark:text-slate-900 font-bold">
                {{ $editingRoleAccess ? 'Edit Role Access' : 'Add New Role Access' }}

                <button @click="show = false" class="float-right focus:outline-none" wire:click='resetVariables'>
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form wire:submit.prevent="saveRoleAccess">
                <div class="grid grid-cols-2 gap-4">
                    <!-- Role Name -->
                    <div class="col-span-full">
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-400">Role Name <span class="text-red-500">*</span></label>
                        <input type="text" 
                                wire:model="roleName"
                                class="mt-1 p-2 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:text-gray-300 dark:bg-gray-700"
                                placeholder="e.g., HR Manager"
                                required>
                        @error('roleName') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <!-- Role Code -->
                    @if(!$editingRoleAccess)
                        <div class="col-span-full">
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-400">Role Code <span class="text-red-500">*</span></label>
                            <input type="text" 
                                    wire:model="roleCode"
                                    class="mt-1 p-2 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:text-gray-300 dark:bg-gray-700"
                                    placeholder="e.g., hr, sv, pa, ..."
                                    required>
                            @error('roleCode') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    @endif

                    <!-- Module Access -->
                   <div class="col-span-full">
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-400">Accessible Modules</label>
                        <div class="border border-gray-300 dark:border-gray-600 rounded-lg p-4 mt-1">
                            <!-- Select All Checkbox -->
                            <div class="mb-4 pb-3 border-b border-gray-200 dark:border-gray-600">
                                <label class="flex items-center space-x-2 p-2 hover:bg-gray-100 dark:hover:bg-gray-600 rounded cursor-pointer font-medium">
                                    <input type="checkbox" 
                                            wire:model.live="selectAll"
                                            class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <span class="text-sm text-gray-700 dark:text-gray-300">Select All Modules</span>
                                </label>
                            </div>

                            <div class="space-y-4">
                                <!-- Top-level System Modules (without parent) -->
                                @if($topLevelModules->count() > 0)
                                    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-3">
                                        <div class="mb-2">
                                            <span class="text-sm font-semibold text-blue-800 dark:text-blue-200">Top Level Modules</span>
                                        </div>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                            @foreach($topLevelModules as $module)
                                                <label class="flex items-center space-x-2 p-2 hover:bg-blue-100 dark:hover:bg-blue-800/30 rounded cursor-pointer">
                                                    <input type="checkbox" 
                                                            wire:model.live="roleAccessModules" 
                                                            value="{{ $module->id }}"
                                                            class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                                    <span class="text-sm text-gray-700 dark:text-gray-300">{{ $module->module_name }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                <!-- Parent Modules with Child System Modules -->
                                @foreach($parentModules as $parentModule)
                                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3">
                                        <!-- Parent Module Header with Checkbox -->
                                        <div class="mb-3">
                                            <label class="flex items-center space-x-2 p-2 hover:bg-gray-100 dark:hover:bg-gray-600 rounded cursor-pointer">
                                                <input type="checkbox" 
                                                        {{ $this->isParentModuleChecked($parentModule->id) ? 'checked' : '' }}
                                                        wire:click="toggleParentModule({{ $parentModule->id }})"
                                                        class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 
                                                            {{ $this->isParentModuleIndeterminate($parentModule->id) ? 'indeterminate' : '' }}">
                                                <span class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                                                    {{ $parentModule->module_name }}
                                                </span>
                                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                                    ({{ $parentModule->systemModules->count() }} modules)
                                                </span>
                                            </label>
                                        </div>

                                        <!-- Child System Modules -->
                                        @if($parentModule->systemModules->count() > 0)
                                            <div class="ml-6 grid grid-cols-1 md:grid-cols-2 gap-2">
                                                @foreach($parentModule->systemModules as $module)
                                                    <label class="flex items-center space-x-2 p-2 hover:bg-gray-100 dark:hover:bg-gray-600 rounded cursor-pointer">
                                                        <input type="checkbox" 
                                                                wire:model.live="roleAccessModules" 
                                                                value="{{ $module->id }}"
                                                                class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ $module->module_name }}</span>
                                                    </label>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="ml-6 text-sm text-gray-500 dark:text-gray-400 italic">
                                                No modules under this parent
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @error('roleAccessModules') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <!-- Buttons -->
                    <div class="mt-4 flex justify-end col-span-2 gap-2">
                        <button type="submit" 
                                class="px-4 text-sm py-2 bg-green-600 hover:bg-green-700 text-white rounded transition-colors">
                            {{ $editingRoleAccess ? 'Update' : 'Save' }} Role Access
                        </button>
                        <p @click="show = false" class="bg-gray-400 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded cursor-pointer" wire:click='resetVariables'>
                            Cancel
                        </p>
                    </div>
                </div>
            </form>
        </div>
    </x-modal>

    <style>
    input[type="checkbox"].indeterminate {
        opacity: 0.5;
    }
    </style>
</div>


<script>
    document.addEventListener('livewire:load', function () {
        Livewire.hook('message.processed', (message, component) => {
            document.querySelectorAll('input[type="checkbox"].indeterminate').forEach(function(checkbox) {
                checkbox.indeterminate = true;
            });
        });
    });
</script>
