<div class="w-full" x-data="{
    employeeTab: @entangle('employeeTab').live,
    employee: @entangle('employee').live,
}" x-cloak>

    <style>
        [x-cloak] { display: none !important; }
        
        .tab-content > div {
            width: 100% !important;
            padding: 1rem !important;
        }
    </style>

    {{-- Personal Data Sheet --}}
    @if ($personalDataSheetOpen && $selectedUser)
        <div class="flex justify-center w-full">
            <div class="overflow-x-auto w-full bg-white rounded-2xl p-4 shadow dark:bg-gray-800 relative">

                <div class="flex gap-3 justify-right items-center">
                    <button wire:click="closePersonalDataSheet">
                        <i class="fas fa-arrow-left text-gray-500 hover:text-gray-700 dark:text-gray-300 dark:hover:text-white"></i>
                    </button>
                    <h1 class="text-lg font-bold text-center text-slate-800 dark:text-white">{{ $selectedUser ? $selectedUser->name : '' }}</h1>
                </div>

                <div class="flex justify-center items-start mt-4 flex-wrap">
                    {{-- Tabs and DP -------- --}}
                    <div class="w-full sm:w-1/5 flex flex-col gap-4 p-2 justify-start items-start">
                        <div class="flex justify-center items-center" style="width: 150px; height: 150px;">
                            @if ($selectedUser->profile_photo_path)
                                <img src="{{ route('profile-photo.file', ['filename' => basename($selectedUser->profile_photo_path)]) }}"
                                    alt="{{ $selectedUser->name }}"
                                    style="width: 150px; height: 150px;"
                                    class="rounded-full object-cover border border-gray-300 dark:border-gray-600">
                            @else
                                <div class="rounded-full bg-gray-500 border border-gray-300 dark:border-gray-600
                                    dark:bg-gray-600 flex items-center justify-center 
                                    text-white text-2xl font-medium"
                                    style="width: 150px; height: 150px;">
                                    {{ strtoupper(substr($selectedUser->name, 0, 1)) }}{{ strtoupper(substr(explode(' ', $selectedUser->name)[1] ?? '', 0, 1)) }}
                                </div>
                            @endif
                        </div>

                         <div class="flex justify-center flex-col gap-2 overflow-x-auto">
                            <button @click="employeeTab = 'personal-details'"
                                    :class="{ 'font-bold dark:text-gray-300 dark:bg-gray-700 bg-gray-200 rounded-lg': employeeTab === 'personal-details', 'text-slate-700 font-medium dark:text-slate-300 dark:hover:text-white hover:text-black': employeeTab !== 'personal-details' }"
                                    class="text-left h-min px-4 py-1 text-sm text-nowrap">
                                Personal Details
                            </button>
                            <button @click="employeeTab = 'contact-details'"
                                    :class="{ 'font-bold dark:text-gray-300 dark:bg-gray-700 bg-gray-200 rounded-lg': employeeTab === 'contact-details', 'text-slate-700 font-medium dark:text-slate-300 dark:hover:text-white hover:text-black': employeeTab !== 'contact-details' }"
                                    class="text-left h-min px-4 py-1 text-sm text-nowrap">
                                Contact Details
                            </button>
                            <button @click="employeeTab = 'job'"
                                    :class="{ 'font-bold dark:text-gray-300 dark:bg-gray-700 bg-gray-200 rounded-lg': employeeTab === 'job', 'text-slate-700 font-medium dark:text-slate-300 dark:hover:text-white hover:text-black': employeeTab !== 'job' }"
                                    class="text-left h-min px-4 py-1 text-sm text-nowrap">
                                Job
                            </button>
                            <button @click="employeeTab = 'salary'"
                                    :class="{ 'font-bold dark:text-gray-300 dark:bg-gray-700 bg-gray-200 rounded-lg': employeeTab === 'salary', 'text-slate-700 font-medium dark:text-slate-300 dark:hover:text-white hover:text-black': employeeTab !== 'salary' }"
                                    class="text-left h-min px-4 py-1 text-sm text-nowrap">
                                Salary
                            </button>
                            <button @click="employeeTab = 'documents'"
                                    :class="{ 'font-bold dark:text-gray-300 dark:bg-gray-700 bg-gray-200 rounded-lg': employeeTab === 'documents', 'text-slate-700 font-medium dark:text-slate-300 dark:hover:text-white hover:text-black': employeeTab !== 'documents' }"
                                    class="text-left h-min px-4 py-1 text-sm text-nowrap">
                                Documents
                            </button>
                            <button @click="employeeTab = 'pds'"
                                    :class="{ 'font-bold dark:text-gray-300 dark:bg-gray-700 bg-gray-200 rounded-lg': employeeTab === 'pds', 'text-slate-700 font-medium dark:text-slate-300 dark:hover:text-white hover:text-black': employeeTab !== 'pds' }"
                                    class="text-left h-min px-4 py-1 text-sm text-nowrap">
                                Personal Data Sheet
                            </button>
                            <button @click="employeeTab = 'wes'"
                                    :class="{ 'font-bold dark:text-gray-300 dark:bg-gray-700 bg-gray-200 rounded-lg': employeeTab === 'wes', 'text-slate-700 font-medium dark:text-slate-300 dark:hover:text-white hover:text-black': employeeTab !== 'wes' }"
                                    class="text-left h-min px-4 py-1 text-sm text-nowrap">
                                Work Exp. Sheet
                            </button>

                            <button wire:click="confirmToggleActive"
                                class="text-center h-min px-4 py-1 text-sm text-nowrap rounded-lg w-full
                                {{ $selectedUser->active_status == 1
                                    ? 'bg-orange-200 dark:bg-orange-800 text-gray-700 dark:text-white hover:bg-orange-300 dark:hover:bg-orange-900'
                                    : 'bg-green-200 dark:bg-green-800 text-gray-700 dark:text-white hover:bg-green-300 dark:hover:bg-green-900' }}">
                                @if ($selectedUser->active_status == 1)
                                    <i class="bi bi-person-fill-slash text-lg mr-2"></i>
                                    Deactivate
                                @else
                                    <i class="bi bi-person-fill-check text-lg mr-2"></i>
                                    Activate
                                @endif
                            </button>

                            <button wire:click="confirmDelete"
                                class="bg-red-200 dark:bg-red-800 text-gray-700 dark:text-white rounded-lg w-full hover:bg-red-300 dark:hover:bg-red-900 text-center h-min px-4 py-1 text-sm text-nowrap">
                                <i class="bi bi-person-fill-x text-lg mr-2"></i>
                                Delete Permanent
                            </button>
                        </div>
                    </div>

                    {{-- Tab Content -------- --}}
                    <div class="tab-content w-full sm:w-4/5 flex flex-col gap-4 p-4 justify-start items-center bg-gradient-to-r from-gray-50 to-slate-50 dark:from-gray-900/20 dark:to-slate-900/20 rounded-r-lg" style="height: -webkit-fill-available;">

                        {{-- Personal Details Sections --------------- --}}
                        <div class="w-full overflow-hidden text-sm p-4" x-show="employeeTab === 'personal-details'">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-3 flex items-center">
                                <i class="fas fa-user mr-2 text-blue-600"></i>
                                Personal Details
                            </h3>

                            <div class="mt-6 grid grid-cols-2 gap-4">
                                <div class="col-span-full sm:col-span-1 flex items-start justify-start   gap-2 text-xs">
                                    <span class="font-medium text-gray-600 dark:text-gray-400 w-32 flex-shrink-0">Firstname:</span>
                                    <span class="text-gray-900 dark:text-gray-100">{{ $selectedUser->userData->first_name }}</span>
                                </div>
                                <div class="col-span-full sm:col-span-1 flex items-start justify-start   gap-2 text-xs">
                                    <span class="font-medium text-gray-600 dark:text-gray-400 w-32 flex-shrink-0">Middlename:</span>
                                    <span class="text-gray-900 dark:text-gray-100">{{ $selectedUser->userData->middle_name }}</span>
                                </div>
                                <div class="col-span-full sm:col-span-1 flex items-start justify-start   gap-2 text-xs">
                                    <span class="font-medium text-gray-600 dark:text-gray-400 w-32 flex-shrink-0">Lastname:</span>
                                    <span class="text-gray-900 dark:text-gray-100">{{ $selectedUser->userData->surname }}</span>
                                </div>
                                <div class="col-span-full sm:col-span-1 flex items-start justify-start   gap-2 text-xs">
                                    <span class="font-medium text-gray-600 dark:text-gray-400 w-32 flex-shrink-0">Name Extension:</span>
                                    <span class="text-gray-900 dark:text-gray-100">{{ $selectedUser->userData->name_extension }}</span>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4 mt-6">
                                <div class="col-span-full sm:col-span-1 flex items-start justify-start   gap-2 text-xs">
                                    <span class="font-medium text-gray-600 dark:text-gray-400 w-32 flex-shrink-0">Citizenship:</span>
                                    <span class="text-gray-900 dark:text-gray-100">{{ $selectedUser->userData->citizenship }}</span>
                                </div>
                                <div class="col-span-full sm:col-span-1 flex items-start justify-start   gap-2 text-xs">
                                    <span class="font-medium text-gray-600 dark:text-gray-400 w-32 flex-shrink-0">Civil Status:</span>
                                    <span class="text-gray-900 dark:text-gray-100">{{ $selectedUser->userData->civil_status }}</span>
                                </div>
                                <div class="col-span-full sm:col-span-1 flex items-start justify-start   gap-2 text-xs">
                                    <span class="font-medium text-gray-600 dark:text-gray-400 w-32 flex-shrink-0">Sex:</span>
                                    <span class="text-gray-900 dark:text-gray-100">{{ $selectedUser->userData->sex }}</span>
                                </div>
                                <div class="col-span-full sm:col-span-1 flex items-start justify-start   gap-2 text-xs">
                                    <span class="font-medium text-gray-600 dark:text-gray-400 w-32 flex-shrink-0">Blood Type:</span>
                                    <span class="text-gray-900 dark:text-gray-100">{{ $selectedUser->userData->blood_type }}</span>
                                </div>
                                <div class="col-span-full sm:col-span-1 flex items-start justify-start   gap-2 text-xs">
                                    <span class="font-medium text-gray-600 dark:text-gray-400 w-32 flex-shrink-0">Date of Birth:</span>
                                    <span class="text-gray-900 dark:text-gray-100">{{ $selectedUser->userData->date_of_birth ? \Carbon\Carbon::parse($selectedUser->userData->date_of_birth)->format('F d, Y') : 'Not provided' }}</span>
                                </div>
                                <div class="col-span-full sm:col-span-1 flex items-start justify-start   gap-2 text-xs">
                                    <span class="font-medium text-gray-600 dark:text-gray-400 w-32 flex-shrink-0">Place of Birth:</span>
                                    <span class="text-gray-900 dark:text-gray-100">{{ $selectedUser->userData->place_of_birth }}</span>
                                </div>
                                <div class="col-span-full sm:col-span-1 flex items-start justify-start   gap-2 text-xs">
                                    <span class="font-medium text-gray-600 dark:text-gray-400 w-32 flex-shrink-0">Height (m):</span>
                                    <span class="text-gray-900 dark:text-gray-100">{{ $selectedUser->userData->height }}</span>
                                </div>
                                <div class="col-span-full sm:col-span-1 flex items-start justify-start   gap-2 text-xs">
                                    <span class="font-medium text-gray-600 dark:text-gray-400 w-32 flex-shrink-0">Weight (kg):</span>
                                    <span class="text-gray-900 dark:text-gray-100">{{ $selectedUser->userData->weight }}</span>
                                </div>
                                <div class="col-span-full sm:col-span-1 flex items-start justify-start   gap-2 text-xs">
                                    <span class="font-medium text-gray-600 dark:text-gray-400 w-32 flex-shrink-0">PWD:</span>
                                    <span class="text-gray-900 dark:text-gray-100">{{ $selectedUser->userData->pwd ? 'Yes' : 'No' }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- Personal Data Sheet Sections ----------- --}}
                        <div class="w-full overflow-hidden text-sm p-4" x-show="employeeTab === 'pds'">
                            @livewire('admin.employee.pds',['userId' => $selectedUser->id])
                        </div>

                        {{-- Contact Details Sections --------------- --}}
                        <div class="w-full overflow-hidden text-sm p-4" x-show="employeeTab === 'contact-details'">
                            @livewire('admin.employee.contact-details', ['userId' => $selectedUser->id])
                        </div>

                        {{-- Job Sections --------------------------- --}}
                        <div class="w-full overflow-hidden text-sm p-4" x-show="employeeTab === 'job'">
                            @livewire('admin.employee.job', ['userId' => $selectedUser->id])
                        </div>

                        {{-- Salary Sections ------------------------ --}}
                        <div class="w-full overflow-hidden text-sm p-4" x-show="employeeTab === 'salary'">
                            @livewire('admin.employee.salary', ['userId' => $selectedUser->id])
                        </div>

                        {{-- Documents Sections --------------------- --}}
                        <div class="w-full overflow-hidden text-sm p-4" x-show="employeeTab === 'documents'">
                            @livewire('admin.employee.documents', ['userId' => $selectedUser->id])
                        </div>

                        {{-- Work Experience Sheet Sections --------- --}}
                        <div class="w-full overflow-hidden text-sm p-4" x-show="employeeTab === 'wes'">
                            @livewire('admin.employee.work-experience-sheet', ['userId' => $selectedUser->id])
                        </div>

                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="flex justify-center w-full">
            <div class="w-full bg-white rounded-2xl p-3 sm:p-6 shadow dark:bg-gray-800 overflow-x-visible">
                <div class="pb-4 pt-4 sm:pt-1">
                    <h1 class="text-lg font-bold text-center text-slate-800 dark:text-white">Employees Management
                    </h1>
                </div>

                <div class="mb-6 flex flex-col sm:flex-row items-end justify-between gap-2">


                    {{-- Search Input --}}
                    <div class="w-full sm:w-1/3 sm:mr-4">
                        <label for="search"
                            class="block text-sm font-medium text-gray-700 dark:text-slate-400 mb-1">Search</label>
                        <input type="text" id="search" wire:model.live="search"
                            class="px-2 py-1.5 block w-full shadow-sm sm:text-sm border border-gray-400 hover:bg-gray-300 rounded-md
                                dark:hover:bg-slate-600 dark:border-slate-600
                                dark:text-gray-300 dark:bg-gray-800"
                            placeholder="Enter employee name or ID">
                    </div>


                    <div class="w-full sm:w-2/3 flex items-end justify-end gap-2 flex-wrap">

                        {{-- Appointment Filter --}}
                        <div class="w-full sm:w-auto relative" style="min-width: 150px">
                            <label for="appointment"
                                class="block text-xs font-medium text-gray-700 dark:text-slate-400 mb-1">Appointment</label>
                            <select type="text" id="appointment" wire:model.live="appointment"
                                class="px-2 py-1.5 block w-full shadow-sm sm:text-sm border border-gray-400 hover:bg-gray-300 rounded-md
                                    dark:hover:bg-slate-600 dark:border-slate-600
                                    dark:text-gray-300 dark:bg-gray-800">
                                    <option value="">All</option>
                                    <option value="plantilla">Plantilla</option>
                                    <option value="cos">COS</option>
                                    <option value="ojt">OJT</option>
                            </select>
                        </div>

                        
                        {{-- Level Filter --}}
                        <div class="w-full sm:w-auto relative" style="min-width: 150px">
                            <label for="posLevel"
                                class="block text-xs font-medium text-gray-700 dark:text-slate-400 mb-1">Position Level</label>
                            <select type="text" id="posLevel" wire:model.live="posLevel"
                                class="px-2 py-1.5 block w-full shadow-sm sm:text-sm border border-gray-400 hover:bg-gray-300 rounded-md
                                    dark:hover:bg-slate-600 dark:border-slate-600
                                    dark:text-gray-300 dark:bg-gray-800">
                                    <option value="">All</option>
                                    <option value="1">1st Level</option>
                                    <option value="2">2nd Level</option>
                            </select>
                        </div>

                        <!-- Filter Dropdown -->
                        <div x-data="{ open: @entangle('toggleDropdownFilter') }" class="w-full sm:w-auto relative">
                            <button @click="open = !open"
                                class="mt-4 sm:mt-0 inline-flex items-center dark:hover:bg-slate-600 dark:border-slate-600
                                    justify-center px-2 py-1.5 text-sm font-medium tracking-wide
                                    text-neutral-800 dark:text-neutral-200 transition-colors duration-200
                                    rounded-lg border border-gray-400 hover:bg-gray-300 focus:outline-none w-full sm:w-fit"
                                type="button">
                                Group by
                                <i class="bi bi-chevron-down w-5 h-5 ml-2"></i>
                            </button>

                            {{-- @if ($dropdownForFilter) --}}
                            <div x-show="open" @click.away="open = false"
                                class="absolute top-14 sm:top-10 z-20 w-64 p-3 border border-gray-400
                                        bg-white rounded-lg shadow-2xl dark:bg-gray-700
                                        overflow-x-hidden scrollbar-thin1"
                                style="height: fit-content">

                                <!-- Provinces Dropdown -->
                                <div class="relative inline-block text-left w-full">
                                    <button wire:click="toggleDropdownProvince"
                                        class="w-full mr-4 p-2 mb-4 text-left text-sm font-medium tracking-wide text-neutral-800 dark:text-neutral-200 
                                        transition-colors duration-200 rounded-lg border border-gray-400 hover:bg-gray-200 
                                        dark:hover:bg-slate-600 focus:outline-none
                                        {{ $dropdownForProvinceOpen ? 'bg-gray-100 dark:bg-gray-800' : '' }}"
                                        type="button">
                                        @if($selectedProvinces)
                                            @foreach ($selectedProvinces as $item)
                                                {{ $item }}, 
                                            @endforeach
                                        @else
                                            Group by Province
                                        @endif
                                        <i class="bi bi-chevron-down w-5 h-5 ml-2 float-right"></i>
                                    </button>
                                    @if ($dropdownForProvinceOpen)
                                    <div class="w-full absolute z-20">
                                        <div
                                            class="w-full p-3 rounded-lg border border-gray-400 shadow-md bg-gray-100 dark:bg-gray-800
                                             max-h-60 overflow-y-auto scrollbar-thin1">
                                            <h6 class="mb-3 text-sm font-medium text-gray-900 dark:text-white">Province
                                            </h6>
                                            <ul class="space-y-2 text-sm">
                                                <li class="flex items-center">
                                                    <input id="select-all-provinces" type="checkbox"
                                                        wire:model.live="selectAllProvinces"
                                                        class="h-4 w-4 text-neutral-800 dark:text-neutral-200 border-gray-300 dark:border-neutral-500 checked:bg-blue-500 focus:ring-offset-2 focus:ring-2 focus:outline-none">
                                                    <label for="select-all-provinces"
                                                        class="ml-2 text-gray-900 dark:text-gray-300">Select
                                                        All</label>
                                                </li>
                                                @foreach ($provinces as $province)
                                                    <li class="flex items-center">
                                                        <input id="province-{{ $province->province_description }}"
                                                            type="checkbox" wire:model.live="selectedProvinces"
                                                            value="{{ $province->province_description }}"
                                                            class="h-4 w-4 text-neutral-800 dark:text-neutral-200 border-gray-300 dark:border-neutral-500 checked:bg-blue-500 focus:ring-offset-2 focus:ring-2 focus:outline-none">
                                                        <label for="province-{{ $province->province_description }}"
                                                            class="ml-2 text-gray-900 dark:text-gray-300">{{ $province->province_description }}</label>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                        <div class="h-3 w-full"></div>
                                    </div>
                                    @endif
                                </div>

                                <!-- Cities Dropdown -->
                                <div class="relative inline-block text-left w-full">
                                    <button wire:click="toggleDropdownCity"
                                        class="w-full mr-4 p-2 mb-4 text-left text-sm font-medium tracking-wide text-neutral-800 
                                        dark:text-neutral-200 transition-colors duration-200 rounded-lg border border-gray-400 
                                        hover:bg-gray-200 focus:outline-none dark:hover:bg-slate-600
                                        {{ $dropdownForCityOpen ? 'bg-gray-100 dark:bg-gray-800' : '' }}"
                                        type="button">
                                        @if($selectedCities)
                                            @foreach ($selectedCities as $item)
                                                {{ $item }}, 
                                            @endforeach
                                        @else
                                            Group by City
                                        @endif
                                        <i class="bi bi-chevron-down w-5 h-5 ml-2 float-right"></i>
                                    </button>
                                    @if ($dropdownForCityOpen)
                                    <div class="w-full absolute z-20">
                                        <div
                                            class="w-full p-3 rounded-lg border border-gray-400 shadow-md bg-gray-100 dark:bg-gray-800
                                             max-h-60 overflow-y-auto scrollbar-thin1">
                                            <h6 class="mb-3 text-sm font-medium text-gray-900 dark:text-white">City
                                            </h6>
                                            <ul class="space-y-2 text-sm">
                                                <li class="flex items-center">
                                                    <input id="select-all-cities" type="checkbox"
                                                        wire:model.live="selectAllCities"
                                                        class="h-4 w-4 text-neutral-800 dark:text-neutral-200 border-gray-300 dark:border-neutral-500 checked:bg-blue-500 focus:ring-offset-2 focus:ring-2 focus:outline-none">
                                                    <label for="select-all-cities"
                                                        class="ml-2 text-gray-900 dark:text-gray-300">Select
                                                        All</label>
                                                </li>
                                                @foreach ($cities as $city)
                                                    <li class="flex items-center">
                                                        <input id="city-{{ $city->city_municipality_description }}"
                                                            type="checkbox" wire:model.live="selectedCities"
                                                            value="{{ $city->city_municipality_description }}"
                                                            class="h-4 w-4 text-neutral-800 dark:text-neutral-200 border-gray-300 dark:border-neutral-500 checked:bg-blue-500 focus:ring-offset-2 focus:ring-2 focus:outline-none">
                                                        <label for="city-{{ $city->city_municipality_description }}"
                                                            class="ml-2 text-gray-900 dark:text-gray-300">{{ $city->city_municipality_description }}</label>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                        <div class="h-3 w-full"></div>
                                    </div>
                                    @endif
                                </div>

                                <!-- Barangay Dropdown -->
                                <div class="relative inline-block text-left w-full">
                                    <button wire:click="toggleDropdownBarangay"
                                        class="w-full mr-4 p-2 mb-4 text-left text-sm font-medium tracking-wide text-neutral-800 
                                        dark:text-neutral-200 transition-colors duration-200 rounded-lg border 
                                        border-gray-400 hover:bg-gray-200 focus:outline-none dark:hover:bg-slate-600
                                        {{ $dropdownForBarangayOpen ? 'bg-gray-100 dark:bg-gray-800' : '' }}"
                                        type="button">
                                        @if($selectedBarangays)
                                            @foreach ($selectedBarangays as $item)
                                                {{ $item }}, 
                                            @endforeach
                                        @else
                                            Group by Barangay
                                        @endif
                                        <i class="bi bi-chevron-down w-5 h-5 ml-2 float-right"></i>
                                    </button>
                                    @if ($dropdownForBarangayOpen)
                                    <div class="w-full absolute z-20">
                                        <div
                                            class="w-full p-3 rounded-lg border border-gray-400 shadow-md bg-gray-100 dark:bg-gray-800
                                             max-h-60 overflow-y-auto scrollbar-thin1">
                                            <h6 class="mb-3 text-sm font-medium text-gray-900 dark:text-white">Barangay
                                            </h6>
                                            <ul class="space-y-2 text-sm">
                                                <li class="flex items-center">
                                                    <input id="select-all-barangays" type="checkbox"
                                                        wire:model.live="selectAllBarangays"
                                                        class="h-4 w-4 text-neutral-800 dark:text-neutral-200 border-gray-300 dark:border-neutral-500 checked:bg-blue-500 focus:ring-offset-2 focus:ring-2 focus:outline-none">
                                                    <label for="select-all-barangays"
                                                        class="ml-2 text-gray-900 dark:text-gray-300">Select
                                                        All</label>
                                                </li>
                                                @foreach ($barangays as $barangay)
                                                    <li class="flex items-center">
                                                        <input id="barangay-{{ $barangay->barangay_description }}"
                                                            type="checkbox" wire:model.live="selectedBarangays"
                                                            value="{{ $barangay->barangay_description }}"
                                                            class="h-4 w-4 text-neutral-800 dark:text-neutral-200 border-gray-300 dark:border-neutral-500 checked:bg-blue-500 focus:ring-offset-2 focus:ring-2 focus:outline-none">
                                                        <label for="barangay-{{ $barangay->barangay_description }}"
                                                            class="ml-2 text-gray-900 dark:text-gray-300">{{ $barangay->barangay_description }}</label>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                        <div class="h-3 w-full"></div>
                                    </div>
                                    @endif
                                </div>

                                <!-- Civil Status Dropdown -->
                                <div class="relative inline-block text-left w-full">
                                    <button wire:click="toggleDropdownCivilStatus"
                                        class="w-full mr-4 p-2 mb-4 text-left
                                                text-sm font-medium tracking-wide text-neutral-800 dark:text-neutral-200
                                                transition-colors duration-200 rounded-lg border border-gray-400
                                                hover:bg-gray-200 focus:outline-none dark:hover:bg-slate-600
                                                {{ $dropdownForCivilStatusOpen ? 'bg-gray-100 dark:bg-gray-800' : '' }}"
                                        type="button">
                                        @if($selectedCivilStatuses)
                                            @foreach ($selectedCivilStatuses as $item)
                                                {{ $item }}, 
                                            @endforeach
                                        @else
                                            Group by Civil Status
                                        @endif
                                        <i class="bi bi-chevron-down w-5 h-5 ml-2 float-right"></i>
                                    </button>
                                    @if ($dropdownForCivilStatusOpen)
                                    <div class="w-full absolute z-20">
                                        <div
                                            class="w-full p-3 rounded-lg border border-gray-400
                                                shadow-md bg-gray-100 dark:bg-gray-800 max-h-60 overflow-y-auto scrollbar-thin1">
                                            <h6 class="mb-3 text-sm font-medium text-gray-900 dark:text-white">
                                                Civil Status
                                            </h6>
                                            <ul class="space-y-2 text-sm">
                                                <li class="flex items-center">
                                                    <input id="single" type="checkbox"
                                                        wire:model.live="selectedCivilStatuses" value="Single"
                                                        class="h-4 w-4 text-neutral-800 dark:text-neutral-200 border-gray-300 dark:border-neutral-500 checked:bg-blue-500 focus:ring-offset-2 focus:ring-2 focus:outline-none">
                                                    <label for="single"
                                                        class="ml-2 text-gray-900 dark:text-gray-300">Single</label>
                                                </li>
                                                <li class="flex items-center">
                                                    <input id="married" type="checkbox"
                                                        wire:model.live="selectedCivilStatuses" value="Married"
                                                        class="h-4 w-4 text-neutral-800 dark:text-neutral-200 border-gray-300 dark:border-neutral-500 checked:bg-blue-500 focus:ring-offset-2 focus:ring-2 focus:outline-none">
                                                    <label for="married"
                                                        class="ml-2 text-gray-900 dark:text-gray-300">Married</label>
                                                </li>
                                                <li class="flex items-center">
                                                    <input id="widowed" type="checkbox"
                                                        wire:model.live="selectedCivilStatuses" value="Widowed"
                                                        class="h-4 w-4 text-neutral-800 dark:text-neutral-200 border-gray-300 dark:border-neutral-500 checked:bg-blue-500 focus:ring-offset-2 focus:ring-2 focus:outline-none">
                                                    <label for="widowed"
                                                        class="ml-2 text-gray-900 dark:text-gray-300">Widowed</label>
                                                </li>
                                                <li class="flex items-center">
                                                    <input id="separated" type="checkbox"
                                                        wire:model.live="selectedCivilStatuses" value="Separated"
                                                        class="h-4 w-4 text-neutral-800 dark:text-neutral-200 border-gray-300 dark:border-neutral-500 checked:bg-blue-500 focus:ring-offset-2 focus:ring-2 focus:outline-none">
                                                    <label for="separated"
                                                        class="ml-2 text-gray-900 dark:text-gray-300">Separated</label>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="h-3 w-full"></div>
                                    </div>
                                    @endif
                                </div>

                                <!-- Sex Dropdown -->
                                <div class="relative inline-block text-left w-full">
                                    <button wire:click="toggleDropdownSex"
                                        class="w-full mr-4 p-2 mb-4 text-left
                                                text-sm font-medium tracking-wide text-neutral-800 dark:text-neutral-200
                                                transition-colors duration-200 rounded-lg border border-gray-400
                                                hover:bg-gray-200 focus:outline-none dark:hover:bg-slate-600
                                                {{ $dropdownForSexOpen ? 'bg-gray-100 dark:bg-gray-800' : '' }}"
                                        type="button">
                                        @if($sex)
                                           {{ $sex }}
                                        @else
                                            Group by Sex
                                        @endif
                                        <i class="bi bi-chevron-down w-5 h-5 ml-2 float-right"></i>
                                    </button>
                                    @if ($dropdownForSexOpen)
                                    <div class="w-full absolute z-20">
                                        <div
                                            class="w-full p-3 rounded-lg border border-gray-400
                                                    shadow-md bg-gray-100 dark:bg-gray-800 max-h-60 overflow-y-auto scrollbar-thin1">
                                            <h6 class="mb-3 text-sm font-medium text-gray-900 dark:text-white">Sex
                                            </h6>
                                            <ul class="space-y-2 text-sm">
                                                <li class="flex items-center">
                                                    <input id="default" type="radio" wire:model.live="sex"
                                                        value=""
                                                        class="h-4 w-4 text-neutral-800 dark:text-neutral-200 border-gray-300 dark:border-neutral-500 focus:ring-blue-500 focus:text-blue-500 focus:ring-offset-2 focus:ring-2 focus:outline-none">
                                                    <label for="default"
                                                        class="ml-2 text-gray-900 dark:text-gray-300">All</label>
                                                </li>
                                                <li class="flex items-center">
                                                    <input id="male" type="radio" wire:model.live="sex"
                                                        value="Male"
                                                        class="h-4 w-4 text-neutral-800 dark:text-neutral-200 border-gray-300 dark:border-neutral-500 focus:ring-blue-500 focus:text-blue-500 focus:ring-offset-2 focus:ring-2 focus:outline-none">
                                                    <label for="male"
                                                        class="ml-2 text-gray-900 dark:text-gray-300">Male</label>
                                                </li>
                                                <li class="flex items-center">
                                                    <input id="female" type="radio" wire:model.live="sex"
                                                        value="Female"
                                                        class="h-4 w-4 text-neutral-800 dark:text-neutral-200 border-gray-300 dark:border-neutral-500 focus:ring-blue-500 focus:text-blue-500 focus:ring-offset-2 focus:ring-2 focus:outline-none">
                                                    <label for="female"
                                                        class="ml-2 text-gray-900 dark:text-gray-300">Female</label>
                                                </li>
                                                <li class="flex items-center">
                                                    <input id="others" type="radio" wire:model.live="sex"
                                                        value="others"
                                                        class="h-4 w-4 text-neutral-800 dark:text-neutral-200 border-gray-300 dark:border-neutral-500 focus:ring-blue-500 focus:text-blue-500 focus:ring-offset-2 focus:ring-2 focus:outline-none">
                                                    <label for="others"
                                                        class="ml-2 text-gray-900 dark:text-gray-300">Others</label>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="h-3 w-full"></div>
                                    </div>
                                    @endif
                                </div>

                                <!-- Learning and Development Dropdown -->
                                <div class="relative inline-block text-left w-full">
                                    <button wire:click="toggleDropdownLD"
                                        class="w-full mr-4 p-2 mb-4 text-left
                                                text-sm font-medium tracking-wide text-neutral-800 dark:text-neutral-200
                                                transition-colors duration-200 rounded-lg border border-gray-400
                                                hover:bg-gray-200 focus:outline-none dark:hover:bg-slate-600
                                                {{ $dropdownForLDOpen ? 'bg-gray-100 dark:bg-gray-800' : '' }}"
                                        type="button">
                                        @if($selectedLD)
                                            @foreach ($selectedLD as $item)
                                                {{ $item }}, 
                                            @endforeach
                                        @else
                                            Group by L&D
                                        @endif
                                        <i class="bi bi-chevron-down w-5 h-5 ml-2 float-right"></i>
                                    </button>
                                    @if ($dropdownForLDOpen)
                                        <div class="w-full absolute z-20">
                                            <div
                                                class="w-full p-3 bg-gray-100 dark:bg-gray-800 rounded-lg border border-gray-400
                                                    shadow-md max-h-60 overflow-y-auto scrollbar-thin1">
                                                <h6 class="mb-3 text-sm font-medium text-gray-900 dark:text-white">
                                                    Learning and Development
                                                </h6>
                                                <ul class="space-y-2 text-sm">
                                                    <li class="flex items-center">
                                                        <input id="Technical" type="checkbox"
                                                            wire:model.live="selectedLD" value="Technical"
                                                            class="h-4 w-4 text-neutral-800 dark:text-neutral-200 border-gray-300 dark:border-neutral-500 checked:bg-blue-500 focus:ring-offset-2 focus:ring-2 focus:outline-none">
                                                        <label for="Technical"
                                                            class="ml-2 text-gray-900 dark:text-gray-300">Technical</label>
                                                    </li>
                                                    <li class="flex items-center">
                                                        <input id="Supervisory" type="checkbox"
                                                            wire:model.live="selectedLD" value="Supervisory"
                                                            class="h-4 w-4 text-neutral-800 dark:text-neutral-200 border-gray-300 dark:border-neutral-500 checked:bg-blue-500 focus:ring-offset-2 focus:ring-2 focus:outline-none">
                                                        <label for="Supervisory"
                                                            class="ml-2 text-gray-900 dark:text-gray-300">Supervisory</label>
                                                    </li>
                                                    <li class="flex items-center">
                                                        <input id="Leadership" type="checkbox"
                                                            wire:model.live="selectedLD" value="Leadership"
                                                            class="h-4 w-4 text-neutral-800 dark:text-neutral-200 border-gray-300 dark:border-neutral-500 checked:bg-blue-500 focus:ring-offset-2 focus:ring-2 focus:outline-none">
                                                        <label for="Leadership"
                                                            class="ml-2 text-gray-900 dark:text-gray-300">Leadership</label>
                                                    </li>
                                                    <li class="flex items-center">
                                                        <input id="Others" type="checkbox"
                                                            wire:model.live="selectedLD" value="Others"
                                                            class="h-4 w-4 text-neutral-800 dark:text-neutral-200 border-gray-300 dark:border-neutral-500 checked:bg-blue-500 focus:ring-offset-2 focus:ring-2 focus:outline-none">
                                                        <label for="Others"
                                                            class="ml-2 text-gray-900 dark:text-gray-300">Others</label>
                                                    </li>
                                                </ul>
                                            </div>
                                            <div class="h-3 w-full"></div>
                                        </div>
                                    @endif
                                </div>

                                <!-- Eligibility Dropdown -->
                                <div class="relative inline-block text-left w-full">
                                    <button wire:click="toggleDropdownElig"
                                        class="w-full mr-4 p-2 mb-4 text-left
                                                text-sm font-medium tracking-wide text-neutral-800 dark:text-neutral-200
                                                transition-colors duration-200 rounded-lg border border-gray-400
                                                hover:bg-gray-200 focus:outline-none dark:hover:bg-slate-600
                                                {{ $dropdownForEligOpen ? 'bg-gray-100 dark:bg-gray-800' : '' }}"
                                        type="button">
                                        @if($selectedEligibility)
                                            @foreach ($selectedEligibility as $item)
                                                {{ $item }}, 
                                            @endforeach
                                        @else
                                            Group by Eligibility
                                        @endif
                                        <i class="bi bi-chevron-down w-5 h-5 ml-2 float-right"></i>
                                    </button>
                                    @if ($dropdownForEligOpen)
                                        <div class="w-full absolute z-20">
                                            <div
                                                class="w-full p-3 bg-gray-100 dark:bg-gray-800 rounded-lg border border-gray-400
                                                    shadow-md max-h-60 overflow-y-auto scrollbar-thin1">
                                                <h6 class="mb-3 text-sm font-medium text-gray-900 dark:text-white">
                                                    Eligibility
                                                </h6>
                                                <ul class="space-y-2 text-sm">
                                                    @foreach($eligibilities as $eligibility)
                                                        <li class="flex items-center">
                                                            <input id="eligibility-{{ $loop->index }}" type="checkbox"
                                                                wire:model.live="selectedEligibility" value="{{ $eligibility }}"
                                                                class="h-4 w-4 text-neutral-800 dark:text-neutral-200 border-gray-300 dark:border-neutral-500 checked:bg-blue-500 focus:ring-offset-2 focus:ring-2 focus:outline-none">
                                                            <label for="eligibility-{{ $loop->index }}"
                                                                class="ml-2 text-gray-900 dark:text-gray-300">{{ $eligibility }}</label>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                            <div class="h-3 w-full"></div>
                                        </div>
                                    @endif
                                </div>

                                <!-- Education Dropdown -->
                                <div class="relative inline-block text-left w-full">
                                    <button wire:click="toggleDropdownEduc"
                                        class="w-full mr-4 p-2 mb-4 text-left
                                                text-sm font-medium tracking-wide text-neutral-800 dark:text-neutral-200
                                                transition-colors duration-200 rounded-lg border border-gray-400
                                                hover:bg-gray-200 focus:outline-none dark:hover:bg-slate-600
                                                {{ $dropdownForEducOpen ? 'bg-gray-100 dark:bg-gray-800' : '' }}"
                                        type="button">
                                        @if($selectedEduc)
                                            @foreach ($selectedEduc as $item)
                                                @if($item == 'b')
                                                    Bachelors, 
                                                @elseif($item == 'm')
                                                    Masters, 
                                                @elseif($item == 'd')
                                                    Doctors, 
                                                @endif
                                            @endforeach
                                        @else
                                            Group by Education
                                        @endif
                                        <i class="bi bi-chevron-down w-5 h-5 ml-2 float-right"></i>
                                    </button>
                                    @if ($dropdownForEducOpen)
                                        <div class="w-full absolute z-20">
                                            <div
                                                class="w-full p-3 bg-gray-100 dark:bg-gray-800 rounded-lg border border-gray-400
                                                    shadow-md max-h-60 overflow-y-auto scrollbar-thin1">
                                                <h6 class="mb-3 text-sm font-medium text-gray-900 dark:text-white">
                                                    Educational Background
                                                </h6>
                                                <ul class="space-y-2 text-sm">
                                                    <li class="flex items-center">
                                                        <input id="b" type="checkbox"
                                                            wire:model.live="selectedEduc" value="b"
                                                            class="h-4 w-4 text-neutral-800 dark:text-neutral-200 border-gray-300 dark:border-neutral-500 checked:bg-blue-500 focus:ring-offset-2 focus:ring-2 focus:outline-none">
                                                        <label for="b"
                                                            class="ml-2 text-gray-900 dark:text-gray-300">Bachelor's Degree</label>
                                                    </li>
                                                    <li class="flex items-center">
                                                        <input id="m" type="checkbox"
                                                            wire:model.live="selectedEduc" value="m"
                                                            class="h-4 w-4 text-neutral-800 dark:text-neutral-200 border-gray-300 dark:border-neutral-500 checked:bg-blue-500 focus:ring-offset-2 focus:ring-2 focus:outline-none">
                                                        <label for="separated"
                                                            class="ml-2 text-gray-900 dark:text-gray-300">Master's Degree</label>
                                                    </li>
                                                    <li class="flex items-center">
                                                        <input id="d" type="checkbox"
                                                            wire:model.live="selectedEduc" value="d"
                                                            class="h-4 w-4 text-neutral-800 dark:text-neutral-200 border-gray-300 dark:border-neutral-500 checked:bg-blue-500 focus:ring-offset-2 focus:ring-2 focus:outline-none">
                                                        <label for="separated"
                                                            class="ml-2 text-gray-900 dark:text-gray-300">Doctorate Degree</label>
                                                    </li>
                                                </ul>
                                            </div>
                                            <div class="h-3 w-full"></div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            {{-- @endif --}}
                        </div>

                        <!-- Sort Dropdown -->
                        <div x-data="{ open: @entangle('dropdownForCategoryOpen') }" class="w-full sm:w-auto relative">
                            <button @click="open = !open"
                                class="mt-4 sm:mt-0 inline-flex items-center dark:hover:bg-slate-600 dark:border-slate-600
                                justify-center px-2 py-1.5 text-sm font-medium tracking-wide
                                text-neutral-800 dark:text-neutral-200 transition-colors duration-200
                                rounded-lg border border-gray-400 hover:bg-gray-300 focus:outline-none w-full sm:w-fit"
                                type="button">
                                Filter Column
                                <i class="bi bi-chevron-down w-5 h-5 ml-2"></i>
                            </button>

                            {{-- @if ($dropdownForCategoryOpen) --}}
                            <div x-show="open" @click.away="open = false"
                                class="absolute top-14 sm:top-10 z-20 w-56 p-3 border border-gray-400 bg-white rounded-lg
                                        shadow-2xl dark:bg-gray-700 max-h-60 overflow-y-auto scrollbar-thin1">
                                <h6 class="mb-3 text-sm font-medium text-gray-900 dark:text-white">Category</h6>
                                <ul class="space-y-2 text-sm">
                                    {{-- <li class="flex items-center">
                                        <input id="name" type="checkbox" wire:model="filters.name" class="h-4 w-4">
                                        <label for="name" class="ml-2 text-gray-900 dark:text-gray-300">Name</label>
                                    </li> --}}
                                    <li class="flex items-center">
                                        <input id="date_of_birth" type="checkbox"
                                            wire:model.live="filters.date_of_birth" class="h-4 w-4">
                                        <label for="date_of_birth" class="ml-2 text-gray-900 dark:text-gray-300">Birth
                                            Date</label>
                                    </li>
                                    <li class="flex items-center">
                                        <input id="place_of_birth" type="checkbox"
                                            wire:model.live="filters.place_of_birth" class="h-4 w-4">
                                        <label for="place_of_birth"
                                            class="ml-2 text-gray-900 dark:text-gray-300">Birth
                                            Place</label>
                                    </li>
                                    <li class="flex items-center">
                                        <input id="sex" type="checkbox" wire:model.live="filters.sex"
                                            class="h-4 w-4">
                                        <label for="sex"
                                            class="ml-2 text-gray-900 dark:text-gray-300">Sex</label>
                                    </li>
                                    <li class="flex items-center">
                                        <input id="citizenship" type="checkbox" wire:model.live="filters.citizenship"
                                            class="h-4 w-4">
                                        <label for="citizenship"
                                            class="ml-2 text-gray-900 dark:text-gray-300">Citizenship</label>
                                    </li>
                                    <li class="flex items-center">
                                        <input id="civil_status" type="checkbox"
                                            wire:model.live="filters.civil_status" class="h-4 w-4">
                                        <label for="civil_status" class="ml-2 text-gray-900 dark:text-gray-300">Civil
                                            Status</label>
                                    </li>
                                    <li class="flex items-center">
                                        <input id="height" type="checkbox" wire:model.live="filters.height"
                                            class="h-4 w-4">
                                        <label for="height"
                                            class="ml-2 text-gray-900 dark:text-gray-300">Height</label>
                                    </li>
                                    <li class="flex items-center">
                                        <input id="weight" type="checkbox" wire:model.live="filters.weight"
                                            class="h-4 w-4">
                                        <label for="weight"
                                            class="ml-2 text-gray-900 dark:text-gray-300">Weight</label>
                                    </li>
                                    <li class="flex items-center">
                                        <input id="blood_type" type="checkbox" wire:model.live="filters.blood_type"
                                            class="h-4 w-4">
                                        <label for="blood_type" class="ml-2 text-gray-900 dark:text-gray-300">Blood
                                            Type</label>
                                    </li>
                                    <li class="flex items-center">
                                        <input id="ethnicity" type="checkbox" wire:model.live="filters.ethnicity"
                                            class="h-4 w-4">
                                        <label for="ethnicity" class="ml-2 text-gray-900 dark:text-gray-300">Ethnicity</label>
                                    </li>
                                    <li class="flex items-center">
                                        <input id="is_solo_parent" type="checkbox" wire:model.live="filters.is_solo_parent"
                                            class="h-4 w-4">
                                        <label for="is_solo_parent" class="ml-2 text-gray-900 dark:text-gray-300">Is Solo Parent</label>
                                    </li>
                                    <li class="flex items-center">
                                        <input id="pwd" type="checkbox" wire:model.live="filters.pwd"
                                            class="h-4 w-4">
                                        <label for="pwd" class="ml-2 text-gray-900 dark:text-gray-300">PWD</label>
                                    </li>
                                    <li class="flex items-center">
                                        <input id="gsis" type="checkbox" wire:model.live="filters.gsis"
                                            class="h-4 w-4">
                                        <label for="gsis" class="ml-2 text-gray-900 dark:text-gray-300">GSIS
                                            ID
                                            No.</label>
                                    </li>
                                    <li class="flex items-center">
                                        <input id="pagibig" type="checkbox" wire:model.live="filters.pagibig"
                                            class="h-4 w-4">
                                        <label for="pagibig" class="ml-2 text-gray-900 dark:text-gray-300">PAGIBIG ID
                                            No.</label>
                                    </li>
                                    <li class="flex items-center">
                                        <input id="philhealth" type="checkbox" wire:model.live="filters.philhealth"
                                            class="h-4 w-4">
                                        <label for="philhealth"
                                            class="ml-2 text-gray-900 dark:text-gray-300">PhilHealth
                                            ID
                                            No.</label>
                                    </li>
                                    <li class="flex items-center">
                                        <input id="sss" type="checkbox" wire:model.live="filters.sss"
                                            class="h-4 w-4">
                                        <label for="sss" class="ml-2 text-gray-900 dark:text-gray-300">SSS
                                            No.</label>
                                    </li>
                                    <li class="flex items-center">
                                        <input id="tin" type="checkbox" wire:model.live="filters.tin"
                                            class="h-4 w-4">
                                        <label for="tin" class="ml-2 text-gray-900 dark:text-gray-300">TIN
                                            No.</label>
                                    </li>
                                    <li class="flex items-center">
                                        <input id="agency_employee_no" type="checkbox"
                                            wire:model.live="filters.agency_employee_no" class="h-4 w-4">
                                        <label for="agency_employee_no"
                                            class="ml-2 text-gray-900 dark:text-gray-300">Agency
                                            Employee No.</label>
                                    </li>
                                    <li class="flex items-center">
                                        <input id="permanent_selectedProvince" type="checkbox"
                                            wire:model.live="filters.permanent_selectedProvince" class="h-4 w-4">
                                        <label for="permanent_selectedProvince"
                                            class="ml-2 text-gray-900 dark:text-gray-300">Permanent Address
                                            (Province)</label>
                                    </li>
                                    <li class="flex items-center">
                                        <input id="permanent_selectedCity" type="checkbox"
                                            wire:model.live="filters.permanent_selectedCity" class="h-4 w-4">
                                        <label for="permanent_selectedCity"
                                            class="ml-2 text-gray-900 dark:text-gray-300">Permanent
                                            Address (City)</label>
                                    </li>
                                    <li class="flex items-center">
                                        <input id="permanent_selectedBarangay" type="checkbox"
                                            wire:model.live="filters.permanent_selectedBarangay" class="h-4 w-4">
                                        <label for="permanent_selectedBarangay"
                                            class="ml-2 text-gray-900 dark:text-gray-300">Permanent Address
                                            (Barangay)</label>
                                    </li>
                                    <li class="flex items-center">
                                        <input id="p_house_street" type="checkbox"
                                            wire:model.live="filters.p_house_street" class="h-4 w-4">
                                        <label for="p_house_street"
                                            class="ml-2 text-gray-900 dark:text-gray-300">Permanent
                                            Address
                                            (Street)</label>
                                    </li>
                                    <li class="flex items-center">
                                        <input id="permanent_selectedZipcode" type="checkbox"
                                            wire:model.live="filters.permanent_selectedZipcode" class="h-4 w-4">
                                        <label for="permanent_selectedZipcode"
                                            class="ml-2 text-gray-900 dark:text-gray-300">Permanent Address
                                            (Zip Code)</label>
                                    </li>
                                    <li class="flex items-center">
                                        <input id="residential_selectedProvince" type="checkbox"
                                            wire:model.live="filters.residential_selectedProvince" class="h-4 w-4">
                                        <label for="residential_selectedProvince"
                                            class="ml-2 text-gray-900 dark:text-gray-300">Residential Address
                                            (Province)</label>
                                    </li>
                                    <li class="flex items-center">
                                        <input id="residential_selectedCity" type="checkbox"
                                            wire:model.live="filters.residential_selectedCity" class="h-4 w-4">
                                        <label for="residential_selectedCity"
                                            class="ml-2 text-gray-900 dark:text-gray-300">Residential
                                            Address (City)</label>
                                    </li>
                                    <li class="flex items-center">
                                        <input id="residential_selectedBarangay" type="checkbox"
                                            wire:model.live="filters.residential_selectedBarangay" class="h-4 w-4">
                                        <label for="residential_selectedBarangay"
                                            class="ml-2 text-gray-900 dark:text-gray-300">Residential Address
                                            (Barangay)</label>
                                    </li>
                                    <li class="flex items-center">
                                        <input id="r_house_street" type="checkbox"
                                            wire:model.live="filters.r_house_street" class="h-4 w-4">
                                        <label for="p_house_street"
                                            class="ml-2 text-gray-900 dark:text-gray-300">Residential
                                            Address
                                            (Street)</label>
                                    </li>
                                    <li class="flex items-center">
                                        <input id="residential_selectedZipcode" type="checkbox"
                                            wire:model.live="filters.residential_selectedZipcode" class="h-4 w-4">
                                        <label for="residential_selectedZipcode"
                                            class="ml-2 text-gray-900 dark:text-gray-300">Residential Address
                                            (Zip Code)</label>
                                    </li>
                                    <!-- Add new filter for active_status -->
                                    <li class="flex items-center">
                                        <input id="active_status" type="checkbox"
                                            wire:model.live="filters.active_status" class="h-4 w-4">
                                        <label for="active_status"
                                            class="ml-2 text-gray-900 dark:text-gray-300">Active Status</label>
                                    </li>
                                    <!-- Add new filter for appointment -->
                                    <li class="flex items-center">
                                        <input id="appointment" type="checkbox"
                                            wire:model.live="filters.appointment" class="h-4 w-4">
                                        <label for="appointment"
                                            class="ml-2 text-gray-900 dark:text-gray-300">Appointment</label>
                                    </li>
                                    <!-- Add new filter for position -->
                                    <li class="flex items-center">
                                        <input id="position" type="checkbox"
                                            wire:model.live="filters.position" class="h-4 w-4">
                                        <label for="position"
                                            class="ml-2 text-gray-900 dark:text-gray-300">Position</label>
                                    </li>
                                    <!-- Add new filter for date_hired -->
                                    <li class="flex items-center">
                                        <input id="date_hired" type="checkbox"
                                            wire:model.live="filters.date_hired" class="h-4 w-4">
                                        <label for="date_hired" class="ml-2 text-gray-900 dark:text-gray-300">Date
                                            Hired</label>
                                    </li>
                                    <!-- Add new filter for years_in_gov_service -->
                                    <li class="flex items-center">
                                        <input id="years_in_gov_service" type="checkbox"
                                            wire:model.live="filters.years_in_gov_service" class="h-4 w-4">
                                        <label for="years_in_gov_service"
                                            class="ml-2 text-gray-900 dark:text-gray-300">Years in Gov Service</label>
                                    </li>
                                    <li class="flex items-center">
                                        <input id="learning_and_development" type="checkbox"
                                            wire:model.live="filters.learning_and_development" class="h-4 w-4">
                                        <label for="learning_and_development"
                                            class="ml-2 text-gray-900 dark:text-gray-300">Learning and Development</label>
                                    </li>
                                    <li class="flex items-center">
                                        <input id="eligibility" type="checkbox"
                                            wire:model.live="filters.eligibility" class="h-4 w-4">
                                        <label for="eligibility"
                                            class="ml-2 text-gray-900 dark:text-gray-300">Eligibility</label>
                                    </li>
                                    <li class="flex items-center">
                                        <input id="educational_background" type="checkbox"
                                            wire:model.live="filters.educational_background" class="h-4 w-4">
                                        <label for="educational_background"
                                            class="ml-2 text-gray-900 dark:text-gray-300">Educational Background</label>
                                    </li>
                                </ul>
                            </div>
                            {{-- @endif --}}
                        </div>

                        <!-- Export to Excel -->
                        <div class="w-full sm:w-auto">
                            <button wire:click="exportUsers"
                                class="mt-4 sm:mt-0 inline-flex items-center dark:hover:bg-slate-600 dark:border-slate-600
                                    justify-center px-4 py-1.5 text-sm font-medium tracking-wide
                                    text-neutral-800 dark:text-neutral-200 transition-colors duration-200
                                    rounded-lg border border-gray-400 hover:bg-gray-300 focus:outline-none"
                                type="button" title="Export to Excel">
                                <img class="flex dark:hidden" src="/images/export-excel.png" width="22"
                                    alt="" wire:target="exportUsers" wire:loading.remove>
                                <img class="hidden dark:block" src="/images/export-excel-dark.png" width="22"
                                    alt="" wire:target="exportUsers" wire:loading.remove>
                                <div wire:loading wire:target="exportUsers">
                                    <div class="spinner-border small text-primary" role="status"></div>
                                </div>
                            </button>
                        </div>

                    </div>

                </div>

                <!-- Table -->
                <div class="flex flex-col">
                    <div class="-my-2 overflow-x-auto">
                        <div class="inline-block w-full py-2 align-middle">
                            <div class="overflow-hidden border dark:border-gray-700 rounded-lg">
                                <div class="overflow-x-auto">
                                    <table class="w-full min-w-full">
                                        <thead class="bg-gray-200 dark:bg-gray-700 rounded-xl">
                                            <tr class="whitespace-nowrap">
                                                <th scope="col"
                                                    class="px-5 py-3 text-sm font-medium text-left uppercase">
                                                    Name
                                                </th>
                                                <th scope="col"
                                                    class="px-5 py-3 text-sm font-medium text-center uppercase">
                                                    Employee Number
                                                </th>
                                                @if ($filters['date_of_birth'])
                                                    <th scope="col"
                                                        class="px-5 py-3 text-sm font-medium uppercase text-center">
                                                        Birth
                                                        Date</th>
                                                @endif
                                                @if ($filters['place_of_birth'])
                                                    <th scope="col"
                                                        class="px-5 py-3 text-sm font-medium uppercase text-center">
                                                        Birth
                                                        Place</th>
                                                @endif
                                                @if ($filters['sex'])
                                                    <th scope="col"
                                                        class="px-5 py-3 text-sm font-medium uppercase text-center">
                                                        Sex
                                                    </th>
                                                @endif
                                                @if ($filters['citizenship'])
                                                    <th scope="col"
                                                        class="px-5 py-3 text-sm font-medium uppercase text-center">
                                                        Citizenship</th>
                                                @endif
                                                @if ($filters['civil_status'])
                                                    <th scope="col"
                                                        class="px-5 py-3 text-sm font-medium uppercase text-center">
                                                        Civil
                                                        Status</th>
                                                @endif
                                                @if ($filters['height'])
                                                    <th scope="col"
                                                        class="px-5 py-3 text-sm font-medium uppercase text-center">
                                                        Height
                                                    </th>
                                                @endif
                                                @if ($filters['weight'])
                                                    <th scope="col"
                                                        class="px-5 py-3 text-sm font-medium uppercase text-center">
                                                        Weight
                                                    </th>
                                                @endif
                                                @if ($filters['blood_type'])
                                                    <th scope="col"
                                                        class="px-5 py-3 text-sm font-medium uppercase text-center">
                                                        Blood
                                                        Type</th>
                                                @endif
                                                @if ($filters['ethnicity'])
                                                    <th scope="col"
                                                        class="px-5 py-3 text-sm font-medium uppercase text-center">
                                                        Ethnicity</th>
                                                @endif
                                                @if ($filters['is_solo_parent'])
                                                    <th scope="col"
                                                        class="px-5 py-3 text-sm font-medium uppercase text-center">
                                                        Is Solo Parent</th>
                                                @endif
                                                @if ($filters['pwd'])
                                                    <th scope="col"
                                                        class="px-5 py-3 text-sm font-medium uppercase text-center">
                                                        PWD</th>
                                                @endif
                                                @if ($filters['gsis'])
                                                    <th scope="col"
                                                        class="px-5 py-3 text-sm font-medium uppercase text-center">
                                                        GSIS
                                                        ID No.</th>
                                                @endif
                                                @if ($filters['pagibig'])
                                                    <th scope="col"
                                                        class="px-5 py-3 text-sm font-medium uppercase text-center">
                                                        PAGIBIG ID No.</th>
                                                @endif
                                                @if ($filters['philhealth'])
                                                    <th scope="col"
                                                        class="px-5 py-3 text-sm font-medium uppercase text-center">
                                                        PhilHealth ID No.</th>
                                                @endif
                                                @if ($filters['sss'])
                                                    <th scope="col"
                                                        class="px-5 py-3 text-sm font-medium uppercase text-center">
                                                        SSS
                                                        No.</th>
                                                @endif
                                                @if ($filters['tin'])
                                                    <th scope="col"
                                                        class="px-5 py-3 text-sm font-medium uppercase text-center">
                                                        TIN
                                                        No.</th>
                                                @endif
                                                @if ($filters['agency_employee_no'])
                                                    <th scope="col"
                                                        class="px-5 py-3 text-sm font-medium uppercase text-center">
                                                        Agency
                                                        Employee No.</th>
                                                @endif
                                                @if ($filters['permanent_selectedProvince'])
                                                    <th scope="col"
                                                        class="px-5 py-3 text-sm font-medium uppercase text-center">
                                                        Permanent Address (Province)</th>
                                                @endif
                                                @if ($filters['permanent_selectedCity'])
                                                    <th scope="col"
                                                        class="px-5 py-3 text-sm font-medium uppercase text-center">
                                                        Permanent Address (City)</th>
                                                @endif
                                                @if ($filters['permanent_selectedBarangay'])
                                                    <th scope="col"
                                                        class="px-5 py-3 text-sm font-medium uppercase text-center">
                                                        Permanent Address (Barangay)</th>
                                                @endif
                                                @if ($filters['p_house_street'])
                                                    <th scope="col"
                                                        class="px-5 py-3 text-sm font-medium uppercase text-center">
                                                        Permanent Address (Street)</th>
                                                @endif
                                                @if ($filters['permanent_selectedZipcode'])
                                                    <th scope="col"
                                                        class="px-5 py-3 text-sm font-medium uppercase text-center">
                                                        Permanent Address (Zip Code)</th>
                                                @endif
                                                @if ($filters['residential_selectedProvince'])
                                                    <th scope="col"
                                                        class="px-5 py-3 text-sm font-medium uppercase text-center">
                                                        Residential Address (Province)</th>
                                                @endif
                                                @if ($filters['residential_selectedCity'])
                                                    <th scope="col"
                                                        class="px-5 py-3 text-sm font-medium uppercase text-center">
                                                        Residential Address (City)</th>
                                                @endif
                                                @if ($filters['residential_selectedBarangay'])
                                                    <th scope="col"
                                                        class="px-5 py-3 text-sm font-medium uppercase text-center">
                                                        Residential Address (Barangay)</th>
                                                @endif
                                                @if ($filters['r_house_street'])
                                                    <th scope="col"
                                                        class="px-5 py-3 text-sm font-medium uppercase text-center">
                                                        Residential Address (Street)</th>
                                                @endif
                                                @if ($filters['residential_selectedZipcode'])
                                                    <th scope="col"
                                                        class="px-5 py-3 text-sm font-medium uppercase text-center">
                                                        Residential Address (Zip Code)</th>
                                                @endif
                                                @if ($filters['active_status'])
                                                    <th scope="col"
                                                        class="px-5 py-3 text-sm font-medium uppercase text-center">
                                                        Active Status</th>
                                                @endif
                                                @if ($filters['position'])
                                                    <th scope="col"
                                                        class="px-5 py-3 text-sm font-medium uppercase text-center">
                                                        Position</th>
                                                @endif
                                                @if ($filters['appointment'])
                                                    <th scope="col"
                                                        class="px-5 py-3 text-sm font-medium uppercase text-center">
                                                        Nature of Appointment</th>
                                                @endif
                                                @if ($filters['date_hired'])
                                                    <th scope="col"
                                                        class="px-5 py-3 text-sm font-medium uppercase text-center">
                                                        Date Hired</th>
                                                @endif
                                                @if ($filters['years_in_gov_service'])
                                                    <th scope="col"
                                                        class="px-5 py-3 text-sm font-medium uppercase text-center">
                                                        Years in Gov Service</th>
                                                @endif
                                                @if ($filters['learning_and_development'])
                                                    <th scope="col"
                                                        class="px-5 py-3 text-sm font-medium uppercase text-center">
                                                        Learning and Development</th>
                                                @endif
                                                @if ($filters['eligibility'])
                                                    <th scope="col"
                                                        class="px-5 py-3 text-sm font-medium uppercase text-center">
                                                        Eligibility</th>
                                                @endif
                                                @if ($filters['educational_background'])
                                                    <th scope="col"
                                                        class="px-5 py-3 text-sm font-medium uppercase text-center">
                                                        Educational Background</th>
                                                @endif
                                                <th
                                                    class="px-5 py-3 text-gray-100 text-sm font-medium text-right sticky right-0 z-10 bg-gray-600 dark:bg-gray-600">
                                                    Action</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-neutral-200 dark:divide-gray-700">
                                            @foreach ($users as $user)
                                                <tr class="text-sm whitespace-nowrap text-gray-600 dark:text-gray-300">
                                                    <td class="px-4 py-2 text-left">
                                                        <div class="flex gap-3 items-center">
                                                            @if ($user->profile_photo_path)
                                                                <img src="{{ route('profile-photo.file', ['filename' => basename($user->profile_photo_path)]) }}"
                                                                    alt="{{ $user->name }}"
                                                                    width="32" height="32"
                                                                    class="w-10 h-10 rounded-full object-cover border border-gray-500">
                                                            @else
                                                                <div class="w-10 h-10 rounded-full bg-gray-500 border border-gray-500 dark:bg-gray-600 flex items-center justify-center text-white text-xs font-medium">
                                                                    {{ strtoupper(substr($user->name, 0, 1)) }}{{ strtoupper(substr(explode(' ', $user->name)[1] ?? '', 0, 1)) }}
                                                                </div>
                                                            @endif
                                                            <span>{{ $user->name }}</span>
                                                        </div>
                                                    </td>
                                                    <td class="px-4 py-2 text-center">
                                                            {{ $user->emp_code }}</td>
                                                    @if ($filters['date_of_birth'])
                                                        <td class="px-4 py-2 text-center">
                                                            {{ $user->date_of_birth }}</td>
                                                    @endif
                                                    @if ($filters['place_of_birth'])
                                                        <td class="px-4 py-2 text-center">
                                                            {{ $user->place_of_birth }}</td>
                                                    @endif
                                                    @if ($filters['sex'])
                                                        <td class="px-4 py-2 text-center">
                                                            {{ $user->sex == 'No' ? 'Prefer Not To Say' : $user->sex }}
                                                        </td>
                                                    @endif
                                                    @if ($filters['citizenship'])
                                                        <td class="px-4 py-2 text-center">
                                                            {{ $user->citizenship }}</td>
                                                    @endif
                                                    @if ($filters['civil_status'])
                                                        <td class="px-4 py-2 text-center">
                                                            {{ $user->civil_status }}</td>
                                                    @endif
                                                    @if ($filters['height'])
                                                        <td class="px-4 py-2 text-center">
                                                            {{ $user->height }}
                                                        </td>
                                                    @endif
                                                    @if ($filters['weight'])
                                                        <td class="px-4 py-2 text-center">
                                                            {{ $user->weight }}
                                                        </td>
                                                    @endif
                                                    @if ($filters['blood_type'])
                                                        <td class="px-4 py-2 text-center">
                                                            {{ $user->blood_type }}</td>
                                                    @endif
                                                    @if ($filters['ethnicity'])
                                                        <td class="px-4 py-2 text-center">
                                                            {{ $user->ethnicity }}</td>
                                                    @endif
                                                    @if ($filters['is_solo_parent'])
                                                        <td class="px-4 py-2 text-center">
                                                            {{ $user->is_solo_parent ? 'Yes' : 'No' }}</td>
                                                    @endif
                                                    @if ($filters['pwd'])
                                                        <td class="px-4 py-2 text-center">
                                                            {{ $user->pwd ? 'Yes' : 'No' }}</td>
                                                    @endif
                                                    @if ($filters['gsis'])
                                                        <td class="px-4 py-2 text-center">
                                                            {{ $user->gsis }}</td>
                                                    @endif
                                                    @if ($filters['pagibig'])
                                                        <td class="px-4 py-2 text-center">
                                                            {{ $user->pagibig }}</td>
                                                    @endif
                                                    @if ($filters['philhealth'])
                                                        <td class="px-4 py-2 text-center">
                                                            {{ $user->philhealth }}</td>
                                                    @endif
                                                    @if ($filters['sss'])
                                                        <td class="px-4 py-2 text-center">
                                                            {{ $user->sss }}</td>
                                                    @endif
                                                    @if ($filters['tin'])
                                                        <td class="px-4 py-2 text-center">
                                                            {{ $user->tin }}</td>
                                                    @endif
                                                    @if ($filters['agency_employee_no'])
                                                        <td class="px-4 py-2 text-center">
                                                            {{ $user->agency_employee_no }}</td>
                                                    @endif
                                                    @if ($filters['permanent_selectedProvince'])
                                                        <td class="px-4 py-2 text-center">
                                                            {{ $user->permanent_selectedProvince }}</td>
                                                    @endif
                                                    @if ($filters['permanent_selectedCity'])
                                                        <td class="px-4 py-2 text-center">
                                                            {{ $user->permanent_selectedCity }}</td>
                                                    @endif
                                                    @if ($filters['permanent_selectedBarangay'])
                                                        <td class="px-4 py-2 text-center">
                                                            {{ $user->permanent_selectedBarangay }}</td>
                                                    @endif
                                                    @if ($filters['p_house_street'])
                                                        <td class="px-4 py-2 text-center">
                                                            {{ $user->p_house_street }}</td>
                                                    @endif
                                                    @if ($filters['permanent_selectedZipcode'])
                                                        <td class="px-4 py-2 text-center">
                                                            {{ $user->permanent_selectedZipcode }}</td>
                                                    @endif
                                                    @if ($filters['residential_selectedProvince'])
                                                        <td class="px-4 py-2 text-center">
                                                            {{ $user->residential_selectedProvince }}</td>
                                                    @endif
                                                    @if ($filters['residential_selectedCity'])
                                                        <td class="px-4 py-2 text-center">
                                                            {{ $user->residential_selectedCity }}</td>
                                                    @endif
                                                    @if ($filters['residential_selectedBarangay'])
                                                        <td class="px-4 py-2 text-center">
                                                            {{ $user->residential_selectedBarangay }}</td>
                                                    @endif
                                                    @if ($filters['r_house_street'])
                                                        <td class="px-4 py-2 text-center">
                                                            {{ $user->r_house_street }}</td>
                                                    @endif
                                                    @if ($filters['residential_selectedZipcode'])
                                                        <td class="px-4 py-2 text-center">
                                                            {{ $user->residential_selectedZipcode }}</td>
                                                    @endif
                                                    @if ($filters['active_status'])
                                                        <td class="px-4 py-2 text-center">
                                                            {{ $user->active_status_label }}</td>
                                                    @endif
                                                    @if ($filters['position'])
                                                        <td class="px-4 py-2 text-center">
                                                            {{ $user->position }}</td>
                                                    @endif
                                                    @if ($filters['appointment'])
                                                        <td class="px-4 py-2 text-center">
                                                            {{ $user->appointment }}</td>
                                                    @endif
                                                    @if ($filters['date_hired'])
                                                        <td class="px-4 py-2 text-center">
                                                            {{ $user->date_hired }}</td>
                                                    @endif
                                                    @if ($filters['years_in_gov_service'])
                                                        <td class="px-4 py-2 text-center">
                                                            {{ $user->years_in_gov_service ?? 'N/A' }}</td>
                                                    @endif
                                                    @if ($filters['learning_and_development'])
                                                        <td class="px-4 py-2 text-center">
                                                            @if(isset($learnDev[$user->id]))
                                                                @foreach ($learnDev[$user->id] as $item)
                                                                    {{ '• ' . $item->type_of_ld }} 
                                                                @endforeach
                                                            @endif
                                                        </td>
                                                    @endif
                                                    @if ($filters['eligibility'])
                                                        <td class="px-4 py-2 text-center">
                                                            @if(isset($eligs[$user->id]))
                                                                @foreach ($eligs[$user->id] as $item)
                                                                    {{ '• ' . $item->eligibility }} 
                                                                @endforeach
                                                            @endif
                                                        </td>
                                                    @endif

                                                    @if ($filters['educational_background'])
                                                        <td class="px-4 py-2 text-center">
                                                            @if(isset($educBg[$user->id]))
                                                                @foreach ($educBg[$user->id] as $ed)
                                                                    @if($ed->is_bachelor)
                                                                        • Bachelor's Degree 
                                                                    @endif
                                                                    @if($ed->is_master)
                                                                        • Master's Degree 
                                                                    @endif
                                                                    @if($ed->is_doctor)
                                                                        • Doctorate Degree 
                                                                    @endif
                                                                @endforeach
                                                            @endif
                                                        </td>
                                                    @endif
                                                    <td
                                                        class="px-5 py-4 text-sm font-medium text-right whitespace-nowrap sticky right-0 z-10 bg-white dark:bg-gray-800">
                                                        <button wire:click="showUser({{ $user->id }})"
                                                            class="inline-flex items-center justify-center px-4 py-2 -m-5 -mr-2 text-sm font-medium tracking-wide text-blue-500 hover:text-blue-600 focus:outline-none">
                                                            <i class="fas fa-eye" title="Show Details"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    @if ($users->isEmpty())
                                        <div class="p-4 text-center text-gray-500 dark:text-gray-300">
                                            No records!
                                        </div> 
                                    @endif
                                </div>
                                <div class="p-5 text-neutral-500 dark:text-neutral-200 bg-gray-200 dark:bg-gray-700">
                                    {{ $users->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    @endif


    {{-- Deactivate / Activate Confirmation Modal --}}
    <x-modal id="toggleActiveModal" maxWidth="md" wire:model="showToggleActiveModal" centered>
        <div class="p-4">
            <div class="mb-4 text-slate-900 dark:text-gray-100 font-bold">
                {{ $selectedUser && $selectedUser->active_status == 1 ? 'Confirm Deactivation' : 'Confirm Activation' }}
                <button @click="show = false" class="float-right focus:outline-none">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300">
                @if ($selectedUser && $selectedUser->active_status == 1)
                    Are you sure you want to <strong>deactivate</strong> <strong>{{ $selectedUser->name }}</strong>? 
                    They will no longer be able to log in.
                @else
                    Are you sure you want to <strong>activate</strong> <strong>{{ $selectedUser?->name }}</strong>?
                @endif
            </label>

            <div class="mt-4 flex justify-end gap-2">
                <button wire:click="toggleActiveStatus"
                    class="{{ $selectedUser && $selectedUser->active_status == 1
                        ? 'bg-orange-500 hover:bg-orange-700'
                        : 'bg-green-500 hover:bg-green-700' }} text-white font-bold py-2 px-4 rounded">
                    <div wire:loading wire:target="toggleActiveStatus">
                        <div class="spinner-border small text-primary" role="status"></div>
                    </div>
                    {{ $selectedUser && $selectedUser->active_status == 1 ? 'Deactivate' : 'Activate' }}
                </button>
                <button @click="show = false"
                    class="bg-gray-400 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
                    Cancel
                </button>
            </div>
        </div>
    </x-modal>

    {{-- Permanent Delete Confirmation Modal --}}
    <x-modal id="deleteEmployeeModal" maxWidth="md" wire:model="showDeleteModal" centered>
        <div class="p-4">
            <div class="mb-4 text-slate-900 dark:text-gray-100 font-bold">
                Confirm Permanent Deletion
                <button @click="show = false" class="float-right focus:outline-none">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="mb-3 p-3 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-700 rounded-lg">
                <p class="text-sm text-red-700 dark:text-red-300 font-medium">
                    <i class="fas fa-exclamation-triangle mr-1"></i>
                    This action is irreversible.
                </p>
            </div>

            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300">
                Are you sure you want to <strong>permanently delete</strong> <strong>{{ $selectedUser?->name }}</strong>?
                All associated records will be removed from the system.
            </label>

            <div class="mt-4 flex justify-end gap-2">
                <button wire:click="deleteEmployeePermanently"
                    class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                    <div wire:loading wire:target="deleteEmployeePermanently">
                        <div class="spinner-border small text-primary" role="status"></div>
                    </div>
                    Delete Permanently
                </button>
                <button @click="show = false"
                    class="bg-gray-400 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
                    Cancel
                </button>
            </div>
        </div>
    </x-modal>

</div>
