<div class="w-full"
x-data="{
    selectedTab: 'C1',
}" x-cloak>

    <div class="w-full flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-3 flex items-center">
            <i class="fas fa-user mr-2 text-blue-600"></i>
            Personal Data Sheet
        </h3>
        <button wire:click="exportPDS"
            class="flex items-center dark:hover:bg-slate-600 dark:border-slate-600
            justify-center px-4 py-1.5 text-sm font-medium tracking-wide 
            text-neutral-800 dark:text-neutral-200 transition-colors duration-200 
            rounded-lg border border-gray-400 hover:bg-gray-300 focus:outline-none"
            type="button" title="Export PDS">
            <img class="flex dark:hidden" src="/images/export-excel.png" width="18" alt="">
            <img class="hidden dark:block" src="/images/export-excel-dark.png" width="18"
                alt="">
            <div wire:loading wire:target="exportPDS" style="margin-left: 5px">
                <div class="spinner-border small text-primary" role="status">
                </div>
            </div>
        </button>
    </div>

    <div class="w-full flex gap-2 overflow-x-auto -mb-2" class="relative">
        <button @click="selectedTab = 'C1'"
            :class="{ 'font-bold text-gray-100 dark:text-gray-700 bg-gray-400 dark:bg-slate-300 rounded-t-lg': selectedTab === 'C1', 'text-slate-500 font-medium dark:text-slate-300 dark:hover:text-white hover:text-black': selectedTab !== 'C1' }"
            class="h-min px-4 pt-2 pb-4 text-sm no-wrap">
            C1
        </button>
        <button @click="selectedTab = 'C2'"
            :class="{ 'font-bold text-gray-100 dark:text-gray-700 bg-gray-400 dark:bg-slate-300 rounded-t-lg': selectedTab === 'C2', 'text-slate-500 font-medium dark:text-slate-300 dark:hover:text-white hover:text-black': selectedTab !== 'C2' }"
            class="h-min px-4 pt-2 pb-4 text-sm no-wrap">
            C2
        </button>
        <button @click="selectedTab = 'C3'"
            :class="{ 'font-bold text-gray-100 dark:text-gray-700 bg-gray-400 dark:bg-slate-300 rounded-t-lg': selectedTab === 'C3', 'text-slate-500 font-medium dark:text-slate-300 dark:hover:text-white hover:text-black': selectedTab !== 'C3' }"
            class="h-min px-4 pt-2 pb-4 text-sm">
            C3
        </button>
        <button @click="selectedTab = 'C4'"
            :class="{ 'font-bold text-gray-100 dark:text-gray-700 bg-gray-400 dark:bg-slate-300 rounded-t-lg': selectedTab === 'C4', 'text-slate-500 font-medium dark:text-slate-300 dark:hover:text-white hover:text-black': selectedTab !== 'C4' }"
            class="h-min px-4 pt-2 pb-4 text-sm">
            C4
        </button>
    </div>

    <div x-show="selectedTab === 'C1'" class="relative z-10">
        {{-- Employee's Data --}}
        <div
            class="bg-gray-400 dark:bg-slate-300 p-2 text-gray-50 dark:text-slate-900 font-bold rounded-t-lg">
            I. PERSONAL INFORMATION
        </div>
        <div>

            <div class="custom-d flex w-full">

                <div class="w-full sm:w-2/4 block">
                    <div class="flex w-full sm:w-auto">
                        <p
                            class="border border-gray-200 dark:border-slate-600 p-1 w-3/6 bg-gray-50 dark:bg-slate-700">
                            Surname</p>
                        <p
                            class="border border-gray-200 dark:border-slate-600 w-full p-1 dark:text-gray-200">
                            {{ $selectedUser->userData->surname ?: 'N/A' }}</p>
                    </div>

                    <div class="flex w-full sm:w-auto">
                        <p
                            class="border border-gray-200 dark:border-slate-600 p-1 w-3/6 bg-gray-50 dark:bg-slate-700">
                            Firstname</p>
                        <p
                            class="border border-gray-200 dark:border-slate-600 w-full p-1 dark:text-gray-200">
                            {{ $selectedUser->userData->first_name ?: 'N/A' }}</p>
                    </div>
                </div>

                <div class="w-full sm:w-2/4 block">
                    <div class="flex w-full sm:w-auto">
                        <p
                            class="border border-gray-200 dark:border-slate-600 p-1 w-3/6 dark:bg-slate-700 bg-gray-50">
                            Middlename</p>
                        <p
                            class="border border-gray-200 dark:border-slate-600 w-full p-1 dark:text-gray-200">
                            {{ $selectedUser->userData->middle_name ?: 'N/A' }}</p>
                    </div>

                    <div class="flex w-full sm:w-auto">
                        <p
                            class="border border-gray-200 dark:border-slate-600 p-1 w-3/6 bg-gray-50 dark:bg-slate-700">
                            Name Extension</p>
                        <p
                            class="border border-gray-200 dark:border-slate-600 w-full p-1 dark:text-gray-200">
                            {{ $selectedUser->userData->name_extension ?: 'N/A' }}</p>
                    </div>
                </div>

            </div>

            <div class="custom-d flex w-full">

                <div class="w-full sm:w-2/4 block">
                    <div class="flex w-full sm:w-auto">
                        <p
                            class="border border-gray-200 dark:border-slate-600 p-1 w-3/6 bg-gray-50 dark:bg-slate-700">
                            Date of Birth</p>
                        <p
                            class="w-full border border-gray-200 dark:border-slate-600 p-1 dark:text-gray-200">
                            {{ $selectedUser->userData->date_of_birth ? \Carbon\Carbon::parse($selectedUser->userData->date_of_birth)->format('F d, Y') : 'N/A' }}
                        </p>
                    </div>
                    <div class="flex w-full sm:w-auto">
                        <p
                            class="border border-gray-200 dark:border-slate-600 p-1 w-3/6 bg-gray-50 dark:bg-slate-700">
                            Place of Birth</p>
                        <p
                            class="w-full border border-gray-200 dark:border-slate-600 p-1 dark:text-gray-200">
                            {{ $selectedUser->userData->place_of_birth ?: 'N/A' }}</p>
                    </div>
                    <div class="flex w-full sm:w-auto">
                        <p
                            class="border border-gray-200 dark:border-slate-600 p-1 w-3/6 bg-gray-50 dark:bg-slate-700">
                            Sex</p>
                        <p
                            class="w-full border border-gray-200 dark:border-slate-600 p-1 dark:text-gray-200">
                            {{ $selectedUser->userData->sex ?: 'N/A' }}</p>
                    </div>
                    <div class="flex w-full sm:w-auto">
                        <p
                            class="border border-gray-200 dark:border-slate-600 p-1 w-3/6 bg-gray-50 dark:bg-slate-700">
                            Civil Status</p>
                        <p
                            class="w-full border border-gray-200 dark:border-slate-600 p-1 dark:text-gray-200">
                            {{ $selectedUser->userData->civil_status ?: 'N/A' }}</p>
                    </div>
                    <div class="flex w-full sm:w-auto">
                        <p
                            class="border border-gray-200 dark:border-slate-600 p-1 w-3/6 bg-gray-50 dark:bg-slate-700">
                            Citizenship</p>
                        <p
                            class="w-full border border-gray-200 dark:border-slate-600 p-1 dark:text-gray-200">
                            {{ $selectedUser->userData->citizenship ?: 'N/A' }}  <span class="text-xs opacity-80">{{ $selectedUser->userData->dual_citizenship_type ? '| ' . $selectedUser->userData->dual_citizenship_type : '' }} {{ $selectedUser->userData->dual_citizenship_country ? '| ' . $selectedUser->userData->dual_citizenship_country : '' }}</span></p>
                    </div>
                    <div class="flex w-full sm:w-auto">
                        <p
                            class="border border-gray-200 dark:border-slate-600 p-1 w-3/6 bg-gray-50 dark:bg-slate-700">
                            Height</p>
                        <p
                            class="w-full border border-gray-200 dark:border-slate-600 p-1 dark:text-gray-200">
                            {{ $selectedUser->userData->height ?: 'N/A' }}m</p>
                    </div>
                    <div class="flex w-full sm:w-auto">
                        <p
                            class="border border-gray-200 dark:border-slate-600 p-1 w-3/6 bg-gray-50 dark:bg-slate-700">
                            Weight</p>
                        <p
                            class="w-full border border-gray-200 dark:border-slate-600 p-1 dark:text-gray-200">
                            {{ $selectedUser->userData->weight ?: 'N/A' }}kg</p>
                    </div>
                    <div class="flex w-full sm:w-auto">
                        <p
                            class="border border-gray-200 dark:border-slate-600 p-1 w-3/6 bg-gray-50 dark:bg-slate-700">
                            Bloodtype</p>
                        <p
                            class="w-full border border-gray-200 dark:border-slate-600 p-1 dark:text-gray-200">
                            {{ $selectedUser->userData->blood_type ?: 'N/A' }}</p>
                    </div>
                </div>

                <div class="w-full sm:w-2/4 block">
                    <div class="flex w-full sm:w-auto">
                        <p
                            class="border border-gray-200 dark:border-slate-600 px-1 w-3/6 bg-gray-50 dark:bg-slate-700  py-2.5">
                            Permanent Address</p>
                        <p
                            class="custom-p w-full border border-gray-200 dark:border-slate-600 px-1 py-2.5 dark:text-gray-200">
                            {{ $selectedUser->userData->p_house_street }} <br>
                            {{ $selectedUser->userData->permanent_selectedBarangay }}
                            {{ $selectedUser->userData->permanent_selectedCity }} <br>
                            {{ $selectedUser->userData->permanent_selectedProvince }}, Philippines <br>
                            {{ $selectedUser->userData->permanent_selectedZipcode }}
                        </p>
                    </div>
                    <div class="flex w-full sm:w-auto">
                        <p
                            class="border border-gray-200 dark:border-slate-600 px-1 w-3/6 bg-gray-50 dark:bg-slate-700  py-2.5">
                            Residential Address</p>
                        <p
                            class="w-full border border-gray-200 dark:border-slate-600 px-1 py-2.5 dark:text-gray-200">
                            {{ $selectedUser->userData->r_house_street }} <br>
                            {{ $selectedUser->userData->residential_selectedBarangay }}
                            {{ $selectedUser->userData->residential_selectedCity }} <br>
                            {{ $selectedUser->userData->residential_selectedProvince }}, Philippines <br>
                            {{ $selectedUser->userData->residential_selectedZipcode }}
                        </p>
                    </div>
                    <div class="flex w-full sm:w-auto">
                        <p
                            class="border border-gray-200 dark:border-slate-600 p-1 w-3/6 bg-gray-50 dark:bg-slate-700">
                            Tel No.</p>
                        <p
                            class="w-full border border-gray-200 dark:border-slate-600 p-1 dark:text-gray-200">
                            {{ $selectedUser->userData->tel_number ?: 'N/A' }}</p>
                    </div>
                </div>

            </div>

            <div class="custom-d flex w-full">

                <div class="w-full sm:w-2/4 block">
                    <div class="flex w-full sm:w-auto">
                        <p
                            class="border border-gray-200 dark:border-slate-600 p-1 w-3/6 bg-gray-50 dark:bg-slate-700">
                            Mobile No.</p>
                        <p
                            class="w-full border border-gray-200 dark:border-slate-600 p-1 dark:text-gray-200">
                            {{ $selectedUser->userData->mobile_number ?: 'N/A' }}</p>
                    </div>
                </div>

                <div class="w-full sm:w-2/4 block">
                    <div class="flex w-full sm:w-auto">
                        <p
                            class="border border-gray-200 dark:border-slate-600 p-1 w-3/6 bg-gray-50 dark:bg-slate-700">
                            Email</p>
                        <p
                            class="w-full border border-gray-200 dark:border-slate-600 p-1 dark:text-gray-200">
                            {{ $selectedUser->userData->email ?: 'N/A' }}</p>
                    </div>
                </div>
            </div>

            <div class="custom-d flex w-full">

                <div class="w-full sm:w-2/4 block">
                    <div class="flex w-full sm:w-auto">
                        <p
                            class="border border-gray-200 dark:border-slate-600 p-1 w-3/6 bg-gray-50 dark:bg-slate-700">
                            UMID ID No.</p>
                        <p
                            class="w-full border border-gray-200 dark:border-slate-600 p-1 dark:text-gray-200">
                            {{ $selectedUser->userData->umid ?: 'N/A' }}</p>
                    </div>
                    <div class="flex w-full sm:w-auto">
                        <p
                            class="border border-gray-200 dark:border-slate-600 p-1 w-3/6 bg-gray-50 dark:bg-slate-700">
                            Pag-Ibig ID No.</p>
                        <p
                            class="w-full border border-gray-200 dark:border-slate-600 p-1 dark:text-gray-200">
                            {{ $selectedUser->userData->pagibig ?: 'N/A' }}</p>
                    </div>
                    <div class="flex w-full sm:w-auto">
                        <p
                            class="border border-gray-200 dark:border-slate-600 p-1 w-3/6 bg-gray-50 dark:bg-slate-700">
                            PhilHealth ID No.</p>
                        <p
                            class="w-full border border-gray-200 dark:border-slate-600 p-1 dark:text-gray-200">
                            {{ $selectedUser->userData->philhealth ?: 'N/A' }}</p>
                    </div>
                </div>

                <div class="w-full sm:w-2/4 block">
                    <div class="flex w-full sm:w-auto">
                        <p
                            class="border border-gray-200 dark:border-slate-600 p-1 w-3/6 bg-gray-50 dark:bg-slate-700">
                            PhilSys No.</p>
                        <p
                            class="w-full border border-gray-200 dark:border-slate-600 p-1 dark:text-gray-200">
                            {{ $selectedUser->userData->philsys ?: 'N/A' }}</p>
                    </div>
                    <div class="flex w-full sm:w-auto">
                        <p
                            class="border border-gray-200 dark:border-slate-600 p-1 w-3/6 bg-gray-50 dark:bg-slate-700">
                            TIN No.</p>
                        <p
                            class="w-full border border-gray-200 dark:border-slate-600 p-1 dark:text-gray-200">
                            {{ $selectedUser->userData->tin ?: 'N/A' }}</p>
                    </div>
                    <div class="flex w-full sm:w-auto">
                        <p
                            class="border border-gray-200 dark:border-slate-600 p-1 w-3/6 bg-gray-50 dark:bg-slate-700">
                            AE No.</p>
                        <p
                            class="w-full border border-gray-200 dark:border-slate-600 p-1 dark:text-gray-200">
                            {{ $selectedUser->userData->agency_employee_no ?: 'N/A' }}</p>
                    </div>
                </div>

            </div>

        </div>

        {{-- Family Background --}}
        <div class="bg-gray-400 dark:bg-slate-300 p-2 text-gray-50 dark:text-slate-900 font-bold">II.
            FAMILY BACKGROUND
        </div>
        <div>
            {{-- Spouse --}}
            <div
                class="flex w-full sm:w-auto bg-gray-50 dark:bg-slate-700 border border-gray-200 dark:border-slate-600">
                <p class="p-1 w-full font-bold dark:text-gray-200">Spouse</p>
            </div>

            @if ($userSpouse)
                <div class="custom-d flex w-full">
                    <div class="w-full sm:w-2/4 block">
                        <div class="flex w-full sm:w-auto">
                            <p
                                class="border border-gray-200 dark:border-slate-600 p-1 w-3/6 bg-gray-50 dark:bg-slate-700">
                                Surname</p>
                            <p
                                class="w-full border border-gray-200 dark:border-slate-600 p-1 dark:text-gray-200">
                                {{ $userSpouse->surname ?: 'N/A' }}</p>
                        </div>

                        <div class="flex w-full sm:w-auto">
                            <p
                                class="border border-gray-200 dark:border-slate-600 p-1 w-3/6 bg-gray-50 dark:bg-slate-700">
                                Firstname</p>
                            <p
                                class="w-full border border-gray-200 dark:border-slate-600 p-1 dark:text-gray-200">
                                {{ $userSpouse->first_name ?: 'N/A' }}</p>
                        </div>
                    </div>

                    <div class="w-full sm:w-2/4 block">
                        <div class="flex w-full sm:w-auto">
                            <p
                                class="border border-gray-200 dark:border-slate-600 p-1 w-3/6 bg-gray-50 dark:bg-slate-700">
                                Middlename</p>
                            <p
                                class="w-full border border-gray-200 dark:border-slate-600 p-1 dark:text-gray-200">
                                {{ $userSpouse->middle_name ?: 'N/A' }}</p>
                        </div>

                        <div class="flex w-full sm:w-auto">
                            <p
                                class="border border-gray-200 dark:border-slate-600 p-1 w-3/6 bg-gray-50 dark:bg-slate-700">
                                Name Extension</p>
                            <p
                                class="w-full border border-gray-200 dark:border-slate-600 p-1 dark:text-gray-200">
                                {{ $userSpouse->name_extension ?: 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                <div class="custom-d flex w-full">
                    <div class="w-full sm:w-2/4 block">
                        <div class="flex w-full sm:w-auto">
                            <p
                                class="border border-gray-200 dark:border-slate-600 p-1 w-3/6 bg-gray-50 dark:bg-slate-700">
                                Date of Birth</p>
                            <p
                                class="w-full border border-gray-200 dark:border-slate-600 p-1 dark:text-gray-200">
                                {{ $userSpouse->birth_date ? \Carbon\Carbon::parse($userSpouse->birth_date)->format('m/d/Y') : 'N/A' }}
                            </p>
                        </div>

                        <div class="flex w-full sm:w-auto">
                            <p
                                class="border border-gray-200 dark:border-slate-600 p-1 w-3/6 bg-gray-50 dark:bg-slate-700">
                                Occupation</p>
                            <p
                                class="w-full border border-gray-200 dark:border-slate-600 p-1 dark:text-gray-200">
                                {{ $userSpouse->occupation ?: 'N/A' }}</p>
                        </div>
                    </div>

                    <div class="w-full sm:w-2/4 block">
                        <div class="flex w-full sm:w-auto">
                            <p
                                class="border border-gray-200 dark:border-slate-600 p-1 w-3/6 bg-gray-50 dark:bg-slate-700">
                                Employer</p>
                            <p
                                class="w-full border border-gray-200 dark:border-slate-600 p-1 dark:text-gray-200">
                                {{ $userSpouse->employer ?: 'N/A' }}</p>
                        </div>

                        <div class="flex w-full sm:w-auto">
                            <p
                                class="border border-gray-200 dark:border-slate-600 p-1 w-3/6 bg-gray-50 dark:bg-slate-700">
                                Tel. No.</p>
                            <p
                                class="w-full border border-gray-200 dark:border-slate-600 p-1 dark:text-gray-200">
                                {{ $userSpouse->tel_number ?: 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                <div class="custom-d flex w-full">
                    <div class="w-full sm:w-4/4 block">
                        <div class="flex w-full sm:w-auto">
                            <p
                                class="border border-gray-200 dark:border-slate-600 p-1 w-3/6 sm:w-1/5 bg-gray-50 dark:bg-slate-700">
                                Business Address</p>
                            <p
                                class="w-full border border-gray-200 dark:border-slate-600 p-1 dark:text-gray-200">
                                {{ $userSpouse->business_address ?: 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Father --}}
            <div
                class="flex w-full sm:w-auto bg-gray-50 dark:bg-slate-700 border border-gray-200 dark:border-slate-600">
                <p class="p-1 w-full font-bold dark:text-gray-200">Father</p>
            </div>

            @if ($userFather)
                <div class="custom-d flex w-full">

                    <div class="w-full sm:w-2/4 block">
                        <div class="flex w-full sm:w-auto">
                            <p
                                class="border border-gray-200 dark:border-slate-600 p-1 w-3/6 bg-gray-50 dark:bg-slate-700">
                                Surname</p>
                            <p
                                class="w-full border border-gray-200 dark:border-slate-600 p-1 dark:text-gray-200">
                                {{ $userFather->surname ?: 'N/A' }}</p>
                        </div>

                        <div class="flex w-full sm:w-auto">
                            <p
                                class="border border-gray-200 dark:border-slate-600 p-1 w-3/6 bg-gray-50 dark:bg-slate-700">
                                Firstname</p>
                            <p
                                class="w-full border border-gray-200 dark:border-slate-600 p-1 dark:text-gray-200">
                                {{ $userFather->first_name ?: 'N/A' }}</p>
                        </div>
                    </div>

                    <div class="w-full sm:w-2/4 block">
                        <div class="flex w-full sm:w-auto">
                            <p
                                class="border border-gray-200 dark:border-slate-600 p-1 w-3/6 bg-gray-50 dark:bg-slate-700">
                                Middlename</p>
                            <p
                                class="w-full border border-gray-200 dark:border-slate-600 p-1 dark:text-gray-200">
                                {{ $userFather->middle_name ?: 'N/A' }}</p>
                        </div>

                        <div class="flex w-full sm:w-auto">
                            <p
                                class="border border-gray-200 dark:border-slate-600 p-1 w-3/6 bg-gray-50 dark:bg-slate-700">
                                Name Extension</p>
                            <p
                                class="w-full border border-gray-200 dark:border-slate-600 p-1 dark:text-gray-200">
                                {{ $userFather->name_extension ?: 'N/A' }}</p>
                        </div>
                    </div>

                </div>
            @endif

            {{-- Mother's Maiden Name --}}
            <div
                class="flex w-full sm:w-auto bg-gray-50 dark:bg-slate-700 border border-gray-200 dark:border-slate-600">
                <p class="p-1 w-full font-bold dark:text-gray-200">Mother's Maiden Name</p>
            </div>

            @if ($userMother)
                <div class="custom-d flex w-full">

                    <div class="w-full sm:w-2/4 block">
                        <div class="flex w-full sm:w-auto">
                            <p
                                class="border border-gray-200 dark:border-slate-600 p-1 w-3/6 bg-gray-50 dark:bg-slate-700">
                                Surname</p>
                            <p
                                class="w-full border border-gray-200 dark:border-slate-600 p-1 dark:text-gray-200">
                                {{ $userMother->surname ?: 'N/A' }}</p>
                        </div>

                        <div class="flex w-full sm:w-auto">
                            <p
                                class="border border-gray-200 dark:border-slate-600 p-1 w-3/6 bg-gray-50 dark:bg-slate-700">
                                Firstname</p>
                            <p
                                class="w-full border border-gray-200 dark:border-slate-600 p-1 dark:text-gray-200">
                                {{ $userMother->first_name ?: 'N/A' }}</p>
                        </div>
                    </div>

                    <div class="w-full sm:w-2/4 block">
                        <div class="flex w-full sm:w-auto">
                            <p
                                class="border border-gray-200 dark:border-slate-600 p-1 w-3/6 bg-gray-50 dark:bg-slate-700">
                                Middlename</p>
                            <p
                                class="w-full border border-gray-200 dark:border-slate-600 p-1 dark:text-gray-200">
                                {{ $userMother->middle_name ?: 'N/A' }}</p>
                        </div>

                        <div class="flex w-full sm:w-auto">
                            <p
                                class="border border-gray-200 dark:border-slate-600 p-1 w-3/6 bg-gray-50 dark:bg-slate-700">
                                Name Extension</p>
                            <p
                                class="w-full border border-gray-200 dark:border-slate-600 p-1 dark:text-gray-200">
                                {{ $userMother->name_extension ?: 'N/A' }}</p>
                        </div>
                    </div>

                </div>
            @endif

            {{-- Children --}}
            <div
                class="flex w-full sm:w-auto bg-gray-50 dark:bg-slate-700 border border-gray-200 dark:border-slate-600">
                <p class="p-1 w-full font-bold dark:text-gray-200">Children</p>
            </div>

            @if ($userChildren)
                @foreach ($userChildren as $child)
                    <div class="custom-d flex w-full">

                        <div class="w-full sm:w-2/4 block">
                            <div class="flex w-full sm:w-auto">
                                <p
                                    class="border border-gray-200 dark:border-slate-600 p-1 w-3/6 bg-gray-50 dark:bg-slate-700">
                                    Fullname</p>
                                <p
                                    class="w-full border border-gray-200 dark:border-slate-600 p-1 dark:text-gray-200">
                                    {{ $child->childs_name }}</p>
                            </div>
                        </div>

                        <div class="w-full sm:w-2/4 block">
                            <div class="flex w-full sm:w-auto">
                                <p
                                    class="border border-gray-200 dark:border-slate-600 p-1 w-3/6 bg-gray-50 dark:bg-slate-700">
                                    Date of Birth</p>
                                <p
                                    class="w-full border border-gray-200 dark:border-slate-600 p-1 dark:text-gray-200">
                                    {{ $child->childs_birth_date ? \Carbon\Carbon::parse($child->childs_birth_date)->format('m/d/Y') : 'N/A' }}
                                </p>
                            </div>
                        </div>

                    </div>
                @endforeach
            @endif

        </div>

        {{-- Educational Background --}}
        <div
            class="bg-gray-400 dark:bg-slate-300 p-2 text-gray-50 dark:text-slate-900 font-bold {{ $educBackground && $educBackground->isNotEmpty() ? '' : 'border-b-2 border-gray-200 dark:border-slate-600' }}">
            III. EDUCATIONAL BACKGROUND
        </div>
        <div>
            @foreach ($educBackground as $educ)
                <div class="flex w-full sm:w-auto">
                    <p
                        class="border border-gray-200 dark:border-slate-600 p-1 w-1/7 bg-gray-200 font-bold dark:bg-slate-700 dark:text-gray-200">
                        Level</p>
                    <p
                        class="w-full border border-gray-200 dark:border-slate-600 p-1 font-bold uppercase dark:text-gray-200">
                        {{ $educ->level ?: 'N/A' }}
                    </p>
                </div>
                <div class="custom-d flex w-full overflow-x-auto">

                    <div class="w-full sm:w-2/4 block">
                        <div class="flex w-full sm:w-auto">
                            <p
                                class="border border-gray-200 dark:border-slate-600 p-1 w-3/6 bg-gray-50 dark:bg-slate-700">
                                Name of School</p>
                            <p
                                class="w-full border border-gray-200 dark:border-slate-600 p-1 dark:text-gray-200">
                                {{ $educ->name_of_school ?: 'N/A' }}</p>
                        </div>
                        <div class="flex w-full sm:w-auto">
                            <p
                                class="border border-gray-200 dark:border-slate-600 p-1 w-3/6 bg-gray-50 dark:bg-slate-700">
                                Period of Attendance</p>
                            <p
                                class="w-full border border-gray-200 dark:border-slate-600 p-1 dark:text-gray-200">
                                From: {{ $educ->from ? \Carbon\Carbon::parse($educ->from)->format('m/d/Y') : 'N/A' }} <br>
                                To: {{ $educ->to ? \Carbon\Carbon::parse($educ->to)->format('m/d/Y') : 'Present' }}
                            </p>
                        </div>
                        <div class="flex w-full sm:w-auto">
                            <p
                                class="border border-gray-200 dark:border-slate-600 p-1 w-3/6 bg-gray-50 dark:bg-slate-700">
                                Scholarship/Academic Honors Received</p>
                            <p
                                class="w-full border border-gray-200 dark:border-slate-600 p-1 dark:text-gray-200">
                                {{ $educ->award ?: 'N/A' }}</p>
                        </div>
                    </div>

                    <div class="w-full sm:w-2/4 block">
                        <div class="flex w-full sm:w-auto">
                            <p
                                class="border border-gray-200 dark:border-slate-600 p-1 w-3/6 bg-gray-50 dark:bg-slate-700">
                                Basic Education/<br>Degree/Course</p>
                            <p
                                class="w-full border border-gray-200 dark:border-slate-600 p-1 dark:text-gray-200">
                                {{ $educ->basic_educ_degree_course ?: 'N/A' }}</p>
                        </div>
                        <div class="flex w-full sm:w-auto">
                            <p
                                class="border border-gray-200 dark:border-slate-600 p-1 w-3/6 bg-gray-50 dark:bg-slate-700">
                                Highest Level/<br>Units Earned</p>
                            <p
                                class="w-full border border-gray-200 dark:border-slate-600 p-1 dark:text-gray-200">
                                {{ $educ->highest_level_unit_earned ?: 'N/A' }}</p>
                        </div>
                        <div class="flex w-full sm:w-auto">
                            <p
                                class="border border-gray-200 dark:border-slate-600 p-1 w-3/6 bg-gray-50 dark:bg-slate-700">
                                Year Graduated</p>
                            <p
                                class="w-full border border-gray-200 dark:border-slate-600 p-1 dark:text-gray-200">
                                {{ $educ->year_graduated ?: 'N/A' }}</p>
                        </div>
                    </div>

                </div>
            @endforeach
        </div>
    </div>

    <div x-show="selectedTab === 'C2'" class="relative z-10">
        {{-- Civil Service Eligibility --}}
        <div
            class="rounded-t-lg bg-gray-400 dark:bg-slate-300 p-2 text-gray-50 dark:text-slate-900 font-bold {{ $eligibility && $eligibility->isNotEmpty() ? '' : 'border-b-2 border-gray-200 dark:border-slate-600' }}">
            IV. CIVIL SERVICE ELIGIBILITY
        </div>

        @if ($eligibility && $eligibility->isNotEmpty())
            <div class="m-scrollable overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-100 dark:bg-slate-700">
                            <th
                                class="p-1 font-medium text-left uppercase border-2 border-gray-200 dark:border-slate-600">
                                Eligibility</th>
                            <th
                                class="p-1 font-medium text-left uppercase border-2 border-gray-200 dark:border-slate-600">
                                Rating</th>
                            <th
                                class="p-1 font-medium text-left uppercase border-2 border-gray-200 dark:border-slate-600">
                                Date of Examination/Confernment</th>
                            <th
                                class="p-1 font-medium text-left uppercase border-2 border-gray-200 dark:border-slate-600">
                                Place of Examination/Confernment</th>
                            <th
                                class="p-1 font-medium text-left uppercase border-2 border-gray-200 dark:border-slate-600">
                                License Number</th>
                            <th width="20%"
                                class="p-1 font-medium text-left uppercase border-2 border-gray-200 dark:border-slate-600">
                                Date of Validity</th>
                        </tr>
                    </thead>
                    <tbody class="">
                        @foreach ($eligibility as $elig)
                            <tr class="dark:text-gray-200">
                                <td
                                    class="p-1 border border-gray-200 dark:border-slate-600 text-left">
                                    {{ $elig->eligibility ?: 'N/A' }}</td>
                                <td
                                    class="p-1 border border-gray-200 dark:border-slate-600 text-left">
                                    {{ $elig->rating ?: 'N/A' }}</td>
                                <td
                                    class="p-1 border border-gray-200 dark:border-slate-600 text-left">
                                    {{ $elig->date ? \Carbon\Carbon::parse($elig->date)->format('m/d/Y') : 'N/A' }}</td>
                                <td
                                    class="p-1 border border-gray-200 dark:border-slate-600 text-left">
                                    {{ $elig->place_of_exam ?: 'N/A' }}</td>
                                <td
                                    class="p-1 border border-gray-200 dark:border-slate-600 text-left">
                                    {{ $elig->license ?: 'N/A' }}</td>
                                <td
                                    class="p-1 border border-gray-200 dark:border-slate-600 text-left">
                                    {{ $elig->date_of_validity ? \Carbon\Carbon::parse($elig->date_of_validity)->format('m/d/Y') : 'N/A' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        {{-- Work Experience --}}
        <div
            class="bg-gray-400 dark:bg-slate-300 p-2 text-gray-50 dark:text-slate-900 font-bold {{ $workExperience && $workExperience->isNotEmpty() ? '' : 'border-b-2 border-gray-200 dark:border-slate-600' }}">
            V. WORK EXPERIENCE
        </div>

        @if ($workExperience && $workExperience->isNotEmpty())
            <div class="m-scrollable overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-100 dark:bg-slate-700">
                            <th class="p-1 border-2 border-gray-200 dark:border-slate-600 font-medium text-left uppercase w-1/5"
                                width="20%">
                                <div class="block w-full">
                                    <div class=" flex justify-center w-full">
                                        INCLUSIVE DATES
                                    </div>
                                    <div class="flex w-full">
                                        <div
                                            class="flex justify-center border border-gray-200 dark:border-slate-600 p-1 w-2/4">
                                            From
                                        </div>
                                        <div
                                            class="flex justify-center border border-gray-200 dark:border-slate-600 p-1 w-2/4">
                                            To
                                        </div>
                                    </div>
                                </div>
                            </th>
                            <th
                                class="p-1 border-2 border-gray-200 dark:border-slate-600 font-medium text-left uppercase">
                                Position Title</th>
                            <th
                                class="p-1 border-2 border-gray-200 dark:border-slate-600 font-medium text-left uppercase">
                                Department/Agency/Office/Company</th>
                            {{-- <th
                                class="p-1 border-2 border-gray-200 dark:border-slate-600 font-medium text-left uppercase">
                                Monthly Salary</th>
                            <th
                                class="p-1 border-2 border-gray-200 dark:border-slate-600 font-medium text-left uppercase">
                                SALARY/JOB/PAY GRADE & STEP</th> --}}
                            <th
                                class="p-1 border-2 border-gray-200 dark:border-slate-600 font-medium text-left uppercase">
                                Status of Appointment</th>
                            <th
                                class="p-1 border-2 border-gray-200 dark:border-slate-600 font-medium text-left uppercase">
                                GOV'T SERVICE</th>
                        </tr>
                    </thead>
                    <tbody class="">
                        @foreach ($workExperience as $exp)
                            <tr class="text-neutral-800 dark:text-neutral-200">
                                <td
                                    class="p-1 border-2 border-gray-200 dark:border-slate-600 dark:text-gray-200 text-left w-1/5">
                                    <div class="flex w-full">
                                        <div
                                            class="flex justify-center border-r border-r-gray-300 p-1 w-2/4">
                                            {{ $exp->start_date ? \Carbon\Carbon::parse($exp->start_date)->format('m/d/Y') : 'N/A' }}
                                        </div>
                                        <div
                                            class="flex justify-center border-l border-l-gray-300 p-1 w-2/4">
                                            {{ $exp->end_date ? \Carbon\Carbon::parse($exp->end_date)->format('m/d/Y') : 'Present' }}
                                        </div>
                                    </div>
                                </td>
                                <td
                                    class="p-1 border-2 border-gray-200 dark:border-slate-600 dark:text-gray-200 text-left">
                                    {{ $exp->position ?: 'N/A' }}</td>
                                <td
                                    class="p-1 border-2 border-gray-200 dark:border-slate-600 dark:text-gray-200 text-left">
                                    {{ $exp->department ?: 'N/A' }}</td>
                                {{-- <td
                                    class="p-1 border-2 border-gray-200 dark:border-slate-600 dark:text-gray-200 text-left">
                                    {{ '₱ ' . number_format($exp->monthly_salary, 2) }}</td>
                                <td
                                    class="p-1 border-2 border-gray-200 dark:border-slate-600 dark:text-gray-200 text-left">
                                    {{ $exp->sg_step ?: 'N/A' }}</td> --}}
                                <td
                                    class="p-1 border-2 border-gray-200 dark:border-slate-600 dark:text-gray-200 text-left">
                                    {{ $exp->status_of_appointment ?: 'N/A' }}</td>
                                <td
                                    class="p-1 border-2 border-gray-200 dark:border-slate-600 dark:text-gray-200 text-left">
                                    {{ $exp->gov_service ? 'Yes' : 'No' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <div x-show="selectedTab === 'C3'" class="relative z-10">
        {{-- Voluntary Work --}}
        <div
            class="rounded-t-lg bg-gray-400 dark:bg-slate-300 p-2 text-gray-50 dark:text-slate-900 font-bold {{ $voluntaryWorks && $voluntaryWorks->isNotEmpty() ? '' : 'border-b-2 border-gray-200 dark:border-slate-600' }}">
            VI. VOLUNTARY WORK
        </div>

        @if ($voluntaryWorks && $voluntaryWorks->isNotEmpty())
            <div class="m-scrollable overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-100 dark:bg-slate-700">
                            <th class="p-1 border-2 border-gray-200 dark:border-slate-600 font-medium text-left uppercase"
                                width="20%">Name of Organization</th>
                            <th
                                class="p-1 border-2 border-gray-200 dark:border-slate-600 font-medium text-left uppercase">
                                Address of Organization</th>
                            <th
                                class="p-1 border-2 border-gray-200 dark:border-slate-600 font-medium text-left uppercase w-1/5">
                                <div class="block w-full">
                                    <div class=" flex justify-center w-full">
                                        INCLUSIVE DATES
                                    </div>
                                    <div class="flex w-full">
                                        <div
                                            class="flex justify-center border border-gray-200 dark:border-slate-600 p-1 w-2/4">
                                            From
                                        </div>
                                        <div
                                            class="flex justify-center border border-gray-200 dark:border-slate-600 p-1 w-2/4">
                                            To
                                        </div>
                                    </div>
                                </div>
                            </th>
                            <th
                                class="p-1 border-2 border-gray-200 dark:border-slate-600 font-medium text-left uppercase">
                                Number of Hours</th>
                            <th class="p-1 border-2 border-gray-200 dark:border-slate-600 font-medium text-left uppercase"
                                width="20%">Position/Nature of Work</th>
                        </tr>
                    </thead>
                    <tbody class="">
                        @foreach ($voluntaryWorks as $voluntary)
                            <tr>
                                <td
                                    class="p-1 border-2 border-gray-200 dark:border-slate-600 dark:text-gray-200 text-left">
                                    {{ $voluntary->org_name ?: 'N/A' }}</td>
                                <td
                                    class="p-1 border-2 border-gray-200 dark:border-slate-600 dark:text-gray-200 text-left">
                                    {{ $voluntary->org_address ?: 'N/A' }}</td>
                                <td
                                    class="p-1 border-2 border-gray-200 dark:border-slate-600 dark:text-gray-200 text-left w-1/5">
                                    <div class="flex w-full">
                                        <div
                                            class="flex justify-center border-r border-r-gray-300 p-1 w-2/4">
                                            {{ $voluntary->start_date ? \Carbon\Carbon::parse($voluntary->start_date)->format('m/d/Y') : 'N/A' }}
                                        </div>
                                        <div
                                            class="flex justify-center border-l border-l-gray-300 p-1 w-2/4">
                                            {{ $voluntary->end_date ? \Carbon\Carbon::parse($voluntary->end_datee)->format('m/d/Y') : 'Present' }}
                                        </div>
                                    </div>
                                </td>
                                <td
                                    class="p-1 border-2 border-gray-200 dark:border-slate-600 dark:text-gray-200 text-sm text-left">
                                    {{ $voluntary->no_of_hours ?: 'N/A' }}</td>
                                <td
                                    class="p-1 border-2 border-gray-200 dark:border-slate-600 dark:text-gray-200 text-sm text-left">
                                    {{ $voluntary->position_nature ?: 'N/A' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        {{-- Learning and Development --}}
        <div
            class="bg-gray-400 dark:bg-slate-300 p-2 text-gray-50 dark:text-slate-900 font-bold {{ $lds && $lds->isNotEmpty() ? '' : 'border-b-2 border-gray-200 dark:border-slate-600' }}">
            VII. LEARNING AND DEVELOPMENT
        </div>

        @if ($lds && $lds->isNotEmpty())
            <div class="m-scrollable overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-100 dark:bg-slate-700">
                            <th class="p-1 border-2 border-gray-200 dark:border-slate-600 dark:bg-slate-700 font-medium text-left uppercase"
                                width="20%">Title of Training</th>
                            <th
                                class="p-1 border-2 border-gray-200 dark:border-slate-600 dark:bg-slate-700 font-medium text-left uppercase w-1/5">
                                <div class="block w-full">
                                    <div class=" flex justify-center w-full">
                                        INCLUSIVE DATES
                                    </div>
                                    <div class="flex w-full">
                                        <div
                                            class="flex justify-center border border-gray-200 dark:border-slate-600 p-1 w-2/4">
                                            From
                                        </div>
                                        <div
                                            class="flex justify-center border border-gray-200 dark:border-slate-600 p-1 w-2/4">
                                            To
                                        </div>
                                    </div>
                                </div>
                            </th>
                            <th
                                class="p-1 border-2 border-gray-200 dark:border-slate-600 dark:bg-slate-700 font-medium text-left uppercase">
                                Number of Hours</th>
                            <th
                                class="p-1 border-2 border-gray-200 dark:border-slate-600 dark:bg-slate-700 font-medium text-left uppercase">
                                Type of LD</th>
                            <th class="p-1 border-2 border-gray-200 dark:border-slate-600 dark:bg-slate-700 font-medium text-left uppercase"
                                width="20%">
                                Conducted/Sponsored By
                            </th>
                            <th class="p-1 border-2 border-gray-200 dark:border-slate-600 dark:bg-slate-700 font-medium text-left uppercase"
                                width="20%">
                                Certificate
                            </th>
                        </tr>
                    </thead>
                    <tbody class="">
                        @foreach ($lds as $ld)
                            <tr class="text-neutral-800 dark:text-neutral-200">
                                <td
                                    class="p-1 border-2 border-gray-200 dark:border-slate-600 dark:text-gray-200 text-left">
                                    {{ $ld->title ?: 'N/A' }}</td>
                                <td
                                    class="p-1 border-2 border-gray-200 dark:border-slate-600 dark:text-gray-200 text-left w-1/5">
                                    <div class="flex w-full">
                                        <div
                                            class="flex justify-center border-r border-r-gray-300 p-1 w-2/4">
                                            {{ $ld->start_date ? \Carbon\Carbon::parse($ld->start_date)->format('m/d/Y') : 'N/A' }}
                                        </div>
                                        <div
                                            class="flex justify-center border-l border-l-gray-300 p-1 w-2/4">
                                            {{ $ld->end_date ? \Carbon\Carbon::parse($ld->end_date)->format('m/d/Y') : 'Present' }}
                                        </div>
                                    </div>
                                </td>
                                <td
                                    class="p-1 border-2 border-gray-200 dark:border-slate-600 dark:text-gray-200 text-left">
                                    {{ $ld->no_of_hours ?: 'N/A' }}</td>
                                <td
                                    class="p-1 border-2 border-gray-200 dark:border-slate-600 dark:text-gray-200 text-left">
                                    {{ $ld->type_of_ld ?: 'N/A' }}
                                </td>
                                <td
                                    class="p-1 border-2 border-gray-200 dark:border-slate-600 dark:text-gray-200 text-left">
                                    {{ $ld->conducted_by ?: 'N/A' }}
                                </td>
                                <td class="p-1 border-2 border-gray-200 dark:border-slate-600 dark:text-gray-200 text-left">
                                    @php
                                        $fileName = $ld->certificate ? basename($ld->certificate) : 'N/A';
                                        $truncatedFileName = strlen($fileName) > 15 ? substr($fileName, 0, 15) . '...' : $fileName;
                                    @endphp
                                    <span class="{{ $ld->certificate ? 'text-blue-500 cursor-pointer' : '' }}" @if($ld->certificate)wire:click='downloadCertificate({{ $ld->id }})'@endif>{{ $truncatedFileName }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        {{-- Other Information --}}
        <div class="bg-gray-400 dark:bg-slate-300 p-2 text-gray-50 dark:text-slate-900 font-bold">VIII.
            OTHER INFORMATION</div>

        <div class="m-scrollable overflow-x-auto">

            {{-- SKILLS --}}
            <div
                class="flex w-full sm:w-auto border-2 border-gray-200 dark:border-slate-600 bg-gray-100 dark:bg-slate-700">
                <p class="p-1 w-full font-bold">SKILLS</p>
            </div>

            <div
                class="custom-d flex w-full border-r-2 border-l-2 border-gray-200 dark:border-slate-600">
                <div class="flex w-full sm:w-auto dark:text-gray-200">
                    @foreach ($skills as $skill)
                        <p class="p-1"> • {{ $skill->skill }} </p>
                    @endforeach
                </div>
            </div>

            {{-- Hobbies --}}
            <div
                class="flex w-full sm:w-auto border-2 border-gray-200 dark:border-slate-600 bg-gray-100 dark:bg-slate-700">
                <p class="p-1 w-full font-bold">HOBBIES</p>
            </div>

            <div
                class="custom-d flex w-full border-r-2 border-l-2 border-gray-200 dark:border-slate-600">
                <div class="flex w-full sm:w-auto dark:text-gray-200">
                    @foreach ($hobbies as $hobby)
                        <p class="p-1"> • {{ $hobby->hobby }} </p>
                    @endforeach
                </div>
            </div>

            {{-- NON-ACADEMIC DISTINCTIONS / RECOGNITION --}}
            <div
                class="flex w-full sm:w-auto border-2 border-gray-200 dark:border-slate-600 bg-gray-100 dark:bg-slate-700">
                <p class="p-1 w-full font-bold">NON-ACADEMIC DISTINCTIONS / RECOGNITION</p>
            </div>

            @if ($non_acads_distinctions && $non_acads_distinctions->isNotEmpty())
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-100 dark:bg-slate-700">
                            <th class="p-1 border-r-2 border-l-2 border-gray-200 dark:border-slate-600 font-medium text-left uppercase"
                                width="20%">Award</th>
                            <th
                                class="p-1 border-r-2 border-l-2 border-gray-200 dark:border-slate-600 font-medium text-left uppercase">
                                Association/ Organization Name</th>
                            <th class="p-1 border-r-2 border-l-2 border-gray-200 dark:border-slate-600 font-medium text-left uppercase"
                                width="20%">Date Received</th>
                        </tr>
                    </thead>
                    <tbody class="">
                        @foreach ($non_acads_distinctions as $non_acads_distinction)
                            <tr class="dark:text-gray-200">
                                <td
                                    class="p-1 border-r-2 border-l-2 border-t-2 border-gray-200 dark:border-slate-600 text-left">
                                    {{ $non_acads_distinction->award ?: 'N/A' }}</td>
                                <td
                                    class="p-1 border-r-2 border-l-2 border-t-2 border-gray-200 dark:border-slate-600 text-left">
                                    {{ $non_acads_distinction->ass_org_name ?: 'N/A' }}</td>
                                <td
                                    class="p-1 border-r-2 border-l-2 border-t-2 border-gray-200 dark:border-slate-600 text-left">
                                    {{ $non_acads_distinction->date_received ? \Carbon\Carbon::parse($non_acads_distinction->date_received)->format('m/d/Y') : 'N/A' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif

            {{-- MEMBERSHIP IN ASSOCIATION/ORGANIZATION --}}
            <div
                class="flex w-full sm:w-auto border-2 border-gray-200 dark:border-slate-600 bg-gray-100 dark:bg-slate-700">
                <p class="p-1 w-full font-bold">MEMBERSHIP IN ASSOCIATION/ORGANIZATION</p>
            </div>

            @if ($assOrgMemberships && $assOrgMemberships->isNotEmpty())
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-100 dark:bg-slate-700">
                            <th
                                class="p-1 border-r-2 border-l-2 border-gray-200 dark:border-slate-600 font-medium text-left uppercase">
                                Association/Organization Name</th>
                            <th class="p-1 border-r-2 border-l-2 border-gray-200 dark:border-slate-600 font-medium text-left uppercase"
                                width="20%">Position</th>
                        </tr>
                    </thead>
                    <tbody class="">
                        @foreach ($assOrgMemberships as $assOrgMembership)
                            <tr class="dark:text-gray-200">
                                <td
                                    class="p-1 border-r-2 border-l-2 border-t-2 border-gray-200 dark:border-slate-600 text-left">
                                    {{ $assOrgMembership->ass_org_name ?: 'N/A' }}</td>
                                <td
                                    class="p-1 border-r-2 border-l-2 border-t-2 border-gray-200 dark:border-slate-600 text-left">
                                    {{ $assOrgMembership->position ?: 'N/A' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

    <div x-show="selectedTab === 'C4'" class="relative z-10">
        <div class="m-scrollable overflow-x-auto">
            <div
                class="bg-gray-400 dark:bg-slate-300 p-2 text-gray-50 dark:text-slate-900 font-bold rounded-t-lg">
            </div>

            {{-- 34 --}}
            <div
                class="flex flex-col w-full border-2 border-gray-200 dark:border-slate-600 bg-gray-100 dark:bg-slate-700">
                <div class="w-full block sm:flex">
                    <div
                        class="w-full sm:w-4/6 flex items-start p-2 text-gray-800 dark:text-gray-100 bg-slate-100 dark:bg-slate-900 text-xs">
                        <p>34.</p>
                        <p class="ml-2 mb-4">
                            Are you related by consanguinity or affinity to the appointing or
                            recommending authority, or to the <br>
                            chief of bureau or office or to the person who has immediate supervision
                            over you in the Office, <br>
                            Bureau or Department where you will be apppointed,
                        </p>
                    </div>
                    <div
                        class="w-full sm:w-2/6 flex flex-col justify-end items-start px-4 bg-white dark:bg-slate-700 relative">
                    </div>
                </div>
                <div class="w-full block sm:flex">
                    <div
                        class="w-full sm:w-4/6 flex items-start p-2 text-gray-800 dark:text-gray-100 bg-slate-100 dark:bg-slate-900 text-xs">
                        <p class="ml-6">
                            a. within the third degree?
                        </p>
                    </div>
                    <div
                        class="w-full sm:w-2/6 flex flex-col justify-center p-2 items-start px-4 bg-white dark:bg-slate-700 relative">
                        <div class="flex items-center">
                            <input id="yes" type="radio" value="1"
                                wire:model='q34aAnswer' name="answer34a"
                                style="pointer-events: none">
                            <label for="yes" class="ml-2">Yes</label>
                            <input id="yes" class="ml-10" value="0" type="radio"
                                wire:model='q34aAnswer' name="answer34a"
                                style="pointer-events: none">
                            <label for="yes" class="ml-2">No</label>
                        </div>
                    </div>
                </div>
                <div class="w-full block sm:flex">
                    <div
                        class="w-full sm:w-4/6 flex items-start p-2 text-gray-800 dark:text-gray-100 bg-slate-100 dark:bg-slate-900 text-xs">
                        <p class="ml-6">
                            b. within the fourth degree (for Local Government Unit - Career Employees)?
                        </p>
                    </div>
                    <div
                        class="w-full sm:w-2/6 flex flex-col justify-end p-2 items-start px-4 bg-white dark:bg-slate-700 relative">
                        <div class="flex items-center">
                            <input id="yes" type="radio" value="1"
                                wire:model.live='q34bAnswer' name="answer34b"
                                style="pointer-events: none">
                            <label for="yes" class="ml-2">Yes</label>
                            <input id="yes" class="ml-10" value="0" type="radio"
                                wire:model.live='q34bAnswer' name="answer34b"
                                style="pointer-events: none">
                            <label for="yes" class="ml-2">No</label>
                        </div>
                        <div
                            class="w-full block items-center text-gray-800 dark:text-gray-100 text-xs mt-2">
                            <p class="text-gray-400">If YES, give details:</p>
                            <div class="w-full border-b border-black dark:border-white mt-2 mb-2">
                                {{ $q34bDetails }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 35 --}}
            <div
                class="flex flex-col w-full border-2 border-gray-200 dark:border-slate-600 bg-gray-100 dark:bg-slate-700">
                <div class="w-full block sm:flex">
                    <div
                        class="w-full sm:w-4/6 flex items-start p-2 text-gray-800 dark:text-gray-100 bg-slate-100 dark:bg-slate-900 text-xs">
                        <p>35.</p>
                        <p class="ml-2">
                            a. Have you ever been found guilty of any administrative offense?
                        </p>
                    </div>
                    <div
                        class="w-full sm:w-2/6 flex flex-col justify-end p-2 items-start px-4 bg-white dark:bg-slate-700 relative">
                        <div class="flex items-center">
                            <input id="yes" type="radio" value="1"
                                wire:model.live='q35aAnswer' name="answer35a"
                                style="pointer-events: none">
                            <label for="yes" class="ml-2">Yes</label>
                            <input id="yes" class="ml-10" value="0" type="radio"
                                wire:model.live='q35aAnswer' name="answer35a"
                                style="pointer-events: none">
                            <label for="yes" class="ml-2">No</label>
                        </div>
                        <div
                            class="w-full block items-center text-gray-800 dark:text-gray-100 text-xs mt-2">
                            <p class="text-gray-400">If YES, give details:</p>
                            <div class="w-full border-b border-black dark:border-white mt-2 mb-2">
                                {{ $q35aDetails }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="w-full block sm:flex">
                    <div
                        class="w-full sm:w-4/6 flex items-start p-2 text-gray-800 dark:text-gray-100 bg-slate-100 dark:bg-slate-900 text-xs">
                        <p class="ml-6">
                            b. Have you been criminally charged before any court?
                        </p>
                    </div>
                    <div
                        class="w-full sm:w-2/6 flex flex-col justify-end p-2 items-start px-4 bg-white dark:bg-slate-700 relative">
                        <div class="flex items-center">
                            <input id="yes" type="radio" value="1"
                                wire:model.live='q35bAnswer' name="answer35b"
                                style="pointer-events: none">
                            <label for="yes" class="ml-2">Yes</label>
                            <input id="yes" class="ml-10" value="0" type="radio"
                                wire:model.live='q35bAnswer' name="answer35b"
                                style="pointer-events: none">
                            <label for="yes" class="ml-2">No</label>
                        </div>
                        <div
                            class="w-full block items-center text-gray-800 dark:text-gray-100 text-xs mt-2">
                            <p class="text-gray-400">If YES, give details:</p>
                            <div class="flex w-full  mt-2">
                                <p class="w-2/5 text-right text-gray-400">Date Filed:</p>
                                <div class="w-3/5 border-b border-black dark:border-white ml-2 mb-2">
                                    {{ $q35bDate_filed }}
                                </div>
                            </div>
                            <div class="flex w-full">
                                <p class="w-2/5 text-right text-gray-400">Status of Case/s:</p>
                                <div class="w-3/5 border-b border-black dark:border-white ml-2 mb-2">
                                    {{ $q35bStatus }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 36 --}}
            <div
                class="flex flex-col w-full border-2 border-gray-200 dark:border-slate-600 bg-gray-100 dark:bg-slate-700">
                <div class="w-full block sm:flex">
                    <div
                        class="w-full sm:w-4/6 flex items-start p-2 text-gray-800 dark:text-gray-100 bg-slate-100 dark:bg-slate-900 text-xs">
                        <p>36.</p>
                        <p class="ml-2">
                            Have you ever been convicted of any crime or violation of any law, decree,
                            ordinance or regulation by any court or tribunal?
                        </p>
                    </div>
                    <div
                        class="w-full sm:w-2/6 flex flex-col justify-end p-2 items-start px-4 bg-white dark:bg-slate-700 relative">
                        <div class="flex items-center">
                            <input id="yes" type="radio" value="1"
                                wire:model.live='q36aAnswer' name="answer36a"
                                style="pointer-events: none">
                            <label for="yes" class="ml-2">Yes</label>
                            <input id="yes" class="ml-10" value="0" type="radio"
                                wire:model.live='q36aAnswer' name="answer36a"
                                style="pointer-events: none">
                            <label for="yes" class="ml-2">No</label>
                        </div>
                        <div
                            class="w-full block items-center text-gray-800 dark:text-gray-100 text-xs mt-2">
                            <p class="text-gray-400">If YES, give details:</p>
                            <div class="w-full border-b border-black dark:border-white mt-2 mb-2">
                                {{ $q36aDetails }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 37 --}}
            <div
                class="flex flex-col w-full border-2 border-gray-200 dark:border-slate-600 bg-gray-100 dark:bg-slate-700">
                <div class="w-full block sm:flex">
                    <div
                        class="w-full sm:w-4/6 flex items-start p-2 text-gray-800 dark:text-gray-100 bg-slate-100 dark:bg-slate-900 text-xs">
                        <p>37.</p>
                        <p class="ml-2">
                            Have you ever been separated from the service in any of the following modes:
                            resignation, retirement, dropped from the rolls, dismissal,
                            termination, end of term, finished contract or phased out (abolition) in the
                            public or private sector?
                        </p>
                    </div>
                    <div
                        class="w-full sm:w-2/6 flex flex-col justify-end p-2 items-start px-4 bg-white dark:bg-slate-700 relative">
                        <div class="flex items-center">
                            <input id="yes" type="radio" value="1"
                                wire:model.live='q37aAnswer' name="answer37a"
                                style="pointer-events: none">
                            <label for="yes" class="ml-2">Yes</label>
                            <input id="yes" class="ml-10" value="0" type="radio"
                                wire:model.live='q37aAnswer' name="answer37a"
                                style="pointer-events: none">
                            <label for="yes" class="ml-2">No</label>
                        </div>
                        <div
                            class="w-full block items-center text-gray-800 dark:text-gray-100 text-xs mt-2">
                            <p class="text-gray-400">If YES, give details:</p>
                            <div class="w-full border-b border-black dark:border-white mt-2 mb-2">
                                {{ $q37aDetails }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 38 --}}
            <div
                class="flex flex-col w-full border-2 border-gray-200 dark:border-slate-600 bg-gray-100 dark:bg-slate-700">
                <div class="w-full block sm:flex">
                    <div
                        class="w-full sm:w-4/6 flex items-start p-2 text-gray-800 dark:text-gray-100 bg-slate-100 dark:bg-slate-900 text-xs">
                        <p>38.</p>
                        <p class="ml-2">
                            a. Have you ever been a candidate in a national or local election held
                            within the last year (except Barangay election)?
                        </p>
                    </div>
                    <div
                        class="w-full sm:w-2/6 flex flex-col justify-end p-2 items-start px-4 bg-white dark:bg-slate-700 relative">
                        <div class="flex items-center">
                            <input id="yes" type="radio" value="1"
                                wire:model.live='q38aAnswer' name="answer38a"
                                style="pointer-events: none">
                            <label for="yes" class="ml-2">Yes</label>
                            <input id="yes" class="ml-10" value="0" type="radio"
                                wire:model.live='q38aAnswer' name="answer38a"
                                style="pointer-events: none">
                            <label for="yes" class="ml-2">No</label>
                        </div>
                        <div
                            class="w-full block items-center text-gray-800 dark:text-gray-100 text-xs mt-2">
                            <p class="text-gray-400">If YES, give details:</p>
                            <div class="w-full border-b border-black dark:border-white mt-2 mb-2">
                                {{ $q38aDetails }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="w-full block sm:flex">
                    <div
                        class="w-full sm:w-4/6 flex items-start p-2 text-gray-800 dark:text-gray-100 bg-slate-100 dark:bg-slate-900 text-xs">
                        <p class="ml-6">
                            b. Have you resigned from the government service during the three (3)-month
                            period before
                            the last election to promote/actively campaign for a national or local
                            candidate?
                        </p>
                    </div>
                    <div
                        class="w-full sm:w-2/6 flex flex-col justify-end p-2 items-start px-4 bg-white dark:bg-slate-700 relative">
                        <div class="flex items-center">
                            <input id="yes" type="radio" value="1"
                                wire:model.live='q38bAnswer' name="answer38b"
                                style="pointer-events: none">
                            <label for="yes" class="ml-2">Yes</label>
                            <input id="yes" class="ml-10" value="0" type="radio"
                                wire:model.live='q38bAnswer' name="answer38b"
                                style="pointer-events: none">
                            <label for="yes" class="ml-2">No</label>
                        </div>
                        <div
                            class="w-full block items-center text-gray-800 dark:text-gray-100 text-xs mt-2">
                            <p class="text-gray-400">If YES, give details:</p>
                            <div class="w-full border-b border-black dark:border-white mt-2 mb-2">
                                {{ $q38bDetails }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 39 --}}
            <div
                class="flex flex-col w-full border-2 border-gray-200 dark:border-slate-600 bg-gray-100 dark:bg-slate-700">
                <div class="w-full block sm:flex">
                    <div
                        class="w-full sm:w-4/6 flex items-start p-2 text-gray-800 dark:text-gray-100 bg-slate-100 dark:bg-slate-900 text-xs">
                        <p>39.</p>
                        <p class="ml-2">
                            Have you acquired the status of an immigrant or permanent resident of
                            another country?
                        </p>
                    </div>
                    <div
                        class="w-full sm:w-2/6 flex flex-col justify-end p-2 items-start px-4 bg-white dark:bg-slate-700 relative">
                        <div class="flex items-center">
                            <input id="yes" type="radio" value="1"
                                wire:model.live='q39aAnswer' name="answer39a"
                                style="pointer-events: none">
                            <label for="yes" class="ml-2">Yes</label>
                            <input id="yes" class="ml-10" value="0" type="radio"
                                wire:model.live='q39aAnswer' name="answer39a"
                                style="pointer-events: none">
                            <label for="yes" class="ml-2">No</label>
                        </div>
                        <div
                            class="w-full block items-center text-gray-800 dark:text-gray-100 text-xs mt-2">
                            <p class="text-gray-400">If YES, give details (country):</p>
                            <div class="w-full border-b border-black dark:border-white mt-2 mb-2">
                                {{ $q39aDetails }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 40 --}}
            <div
                class="flex flex-col w-full border-2 border-gray-200 dark:border-slate-600 bg-gray-100 dark:bg-slate-700">
                <div class="w-full block sm:flex">
                    <div
                        class="w-full sm:w-4/6 flex items-start p-2 text-gray-800 dark:text-gray-100 bg-slate-100 dark:bg-slate-900 text-xs">
                        <p>40.</p>
                        <p class="ml-2 mb-4">
                            Pursuant to: (a) Indigenous People's Act (RA 8371); (b) Magna Carta for
                            Disabled Persons (RA 7277, as amended); and (c)
                            Solo Parents Welfare Act of 2000 (RA 8972), please answer the following
                            items:
                        </p>
                    </div>
                    <div
                        class="w-full sm:w-2/6 flex flex-col justify-end items-start px-4 bg-white dark:bg-slate-700">
                    </div>
                </div>
                <div class="w-full block sm:flex">
                    <div
                        class="w-full sm:w-4/6 flex items-start p-2 text-gray-800 dark:text-gray-100 bg-slate-100 dark:bg-slate-900 text-xs">
                        <p class="ml-6">
                            a. Are you a member of any indigenous group?
                        </p>
                    </div>
                    <div
                        class="w-full sm:w-2/6 flex flex-col justify-end p-2 items-start px-4 bg-white dark:bg-slate-700 relative">
                        <div class="flex items-center">
                            <input id="yes" type="radio" value="1"
                                wire:model='q40aAnswer' name="answer40a"
                                style="pointer-events: none">
                            <label for="yes" class="ml-2">Yes</label>
                            <input id="yes" class="ml-10" value="0" type="radio"
                                wire:model='q40aAnswer' name="answer40a"
                                style="pointer-events: none">
                            <label for="yes" class="ml-2">No</label>
                        </div>
                        <div
                            class="w-full block items-center text-gray-800 dark:text-gray-100 text-xs mt-2">
                            <p class="text-gray-400">If YES, please specify:</p>
                            <div class="w-full border-b border-black dark:border-white mt-2 mb-2">
                                {{ $q40aDetails }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="w-full block sm:flex">
                    <div
                        class="w-full sm:w-4/6 flex items-start p-2 text-gray-800 dark:text-gray-100 bg-slate-100 dark:bg-slate-900 text-xs">
                        <p class="ml-6">
                            b. Are you a person with disability?
                        </p>
                    </div>
                    <div
                        class="w-full sm:w-2/6 flex flex-col justify-end p-2 items-start px-4 bg-white dark:bg-slate-700 relative">
                        <div class="flex items-center">
                            <input id="yes" type="radio" value="1"
                                wire:model.live='q40bAnswer' name="answer40b"
                                style="pointer-events: none">
                            <label for="yes" class="ml-2">Yes</label>
                            <input id="yes" class="ml-10" value="0" type="radio"
                                wire:model.live='q40bAnswer' name="answer40b"
                                style="pointer-events: none">
                            <label for="yes" class="ml-2">No</label>
                        </div>
                        <div
                            class="w-full block items-center text-gray-800 dark:text-gray-100 text-xs mt-2">
                            <p class="text-gray-400">If YES, please specify ID No:</p>
                            <div class="w-full border-b border-black dark:border-white mt-2 mb-2">
                                {{ $q40bDetails }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="w-full block sm:flex">
                    <div
                        class="w-full sm:w-4/6 flex items-start p-2 text-gray-800 dark:text-gray-100 bg-slate-100 dark:bg-slate-900 text-xs">
                        <p class="ml-6">
                            c. Are you a solo parent?
                        </p>
                    </div>
                    <div
                        class="w-full sm:w-2/6 flex flex-col justify-end p-2 items-start px-4 bg-white dark:bg-slate-700 relative">
                        <div class="flex items-center">
                            <input id="yes" type="radio" value="1"
                                wire:model.live='q40cAnswer' name="answer40c"
                                style="pointer-events: none">
                            <label for="yes" class="ml-2">Yes</label>
                            <input id="yes" class="ml-10" value="0" type="radio"
                                wire:model.live='q40cAnswer' name="answer40c"
                                style="pointer-events: none">
                            <label for="yes" class="ml-2">No</label>
                        </div>
                        <div
                            class="w-full block items-center text-gray-800 dark:text-gray-100 text-xs mt-2">
                            <p class="text-gray-400">If YES, please specify ID No:</p>
                            <div class="w-full border-b border-black dark:border-white mt-2 mb-2">
                                {{ $q40cDetails }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Character References --}}
            <div
                class="flex w-full sm:w-auto border-2 border-gray-200 dark:border-slate-600 bg-gray-100 dark:bg-slate-700">
                <p class="p-1 w-full font-bold">CHARACTER REFERENCES</p>
            </div>

            @if ($references && $references->isNotEmpty())
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-100 dark:bg-slate-700">
                            <th class="p-1 border-r-2 border-l-2 border-gray-200 dark:border-slate-600 font-medium text-left uppercase"
                                width="20%">Fullname</th>
                            <th
                                class="p-1 border-r-2 border-l-2 border-gray-200 dark:border-slate-600 font-medium text-left uppercase">
                                Address</th>
                            <th
                                class="p-1 border-r-2 border-l-2 border-gray-200 dark:border-slate-600 font-medium text-left uppercase">
                                Tel Number</th>
                            <th class="p-1 border-r-2 border-l-2 border-gray-200 dark:border-slate-600 font-medium text-left uppercase"
                                width="20%">Mobile Number</th>
                        </tr>
                    </thead>
                    <tbody class="">
                        @foreach ($references as $reference)
                            <tr class="dark:text-gray-200">
                                <td
                                    class="p-1 border-r-2 border-l-2 border-t-2 border-gray-200 dark:border-slate-600 text-left">
                                    {{ $reference->firstname }}
                                    {{ $reference->middle_initial ? $reference->middle_initial . '.' : '' }}
                                    {{ $reference->surname }}</td>
                                <td
                                    class="p-1 border-r-2 border-l-2 border-t-2 border-gray-200 dark:border-slate-600 text-left">
                                    {{ $reference->address ?: 'N/A' }}</td>
                                <td
                                    class="p-1 border-r-2 border-l-2 border-t-2 border-gray-200 dark:border-slate-600 text-left">
                                    {{ $reference->tel_number ?: 'N/A' }}</td>
                                <td
                                    class="p-1 border-r-2 border-l-2 border-t-2 border-gray-200 dark:border-slate-600 text-left">
                                    {{ $reference->mobile_number ?: 'N/A' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif

            {{-- Gov ID --}}
            <div class="flex flex-col w-full border-2 border-gray-200 dark:border-slate-600">
                <div class="w-full block sm:flex">
                    <div
                        class="w-full bg-gray-100 dark:bg-slate-700 border-2 border-gray-200 dark:border-slate-600 flex flex-col justify-end relative">
                        <div
                            class="w-full border-b-2 border-gray-200 dark:border-slate-600 p-2 bg-gray-100 dark:bg-slate-700">
                            <p class="w-full">Government Issued ID (i.e.Passport, GSIS, SSS, PRC,
                                Driver's License, etc.) </p>
                            <p class="w-full text-right">PLEASE INDICATE ID Number</p>
                        </div>
                        <div class="flex w-full border-b-2 border-gray-200 dark:border-slate-600 px-2 bg-gray-50 dark:bg-gray-800 items-center"
                            style="height: 50px">
                            <p class="w-2/3">Government Issued ID:</p>
                            @if ($editGovId)
                                <input type="text" value="{{ $govId }}"
                                    wire:model='govId'
                                    class="text-sm bg-gray-100 text-gray-800 w-full" autofocus
                                    style="height: 35px">
                            @elseif($govId)
                                <p class="w-2/3 text-gray-800 dark:text-gray-100 text-right">
                                    {{ $govId ?: 'N/A' }}</p>
                            @endif
                        </div>
                        <div class="flex w-full border-b-2 border-gray-200 dark:border-slate-600 px-2 bg-gray-50 dark:bg-gray-800 items-center"
                            style="height: 50px">
                            <p class="w-2/3">ID/License/Passport No.:</p>
                            @if ($editGovId)
                                <input type="text" value="{{ $idNumber }}"
                                    wire:model='idNumber'
                                    class="text-sm bg-gray-100 text-gray-800 w-full" autofocus
                                    style="height: 35px">
                            @elseif($idNumber)
                                <p class="w-2/3 text-gray-800 dark:text-gray-100 text-right">
                                    {{ $idNumber ?: 'N/A' }}</p>
                            @endif
                        </div>
                        <div class="flex w-full px-2 bg-gray-50 dark:bg-gray-800 items-center"
                            style="height: 50px">
                            <p class="w-2/3">Date/Place of Issuance:</p>
                            @if ($editGovId)
                                <input type="text" wire:model='dateIssued'
                                    class="text-sm bg-gray-100 text-gray-800 w-full" autofocus
                                    style="height: 35px">
                            @elseif($dateIssued)
                                <p class="w-2/3 text-gray-800 dark:text-gray-100 text-right">
                                    {{ $dateIssued ?: 'N/A' }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- Footer --}}
    <div class="bg-gray-400 dark:bg-slate-700 p-2 text-white flex justify-center rounded-b-lg">
    </div>
</div>
