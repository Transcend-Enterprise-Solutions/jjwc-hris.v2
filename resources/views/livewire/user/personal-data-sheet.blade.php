<div class="w-full" x-data="{
    tab: @entangle('tab').live,
}" x-cloak>

    <div class="flex justify-center w-full">
        <div class="overflow-x-auto w-full bg-white rounded-2xl p-3 sm:p-6 shadow dark:bg-gray-800">
            <div class="flex justify-center w-full">
                <div class="overflow-x-auto w-full bg-white rounded-2xl p-4 dark:bg-gray-800 relative">
                    <div class="layout-container">
                        <div class="{{ $consentStatus ? 'hidden' : 'w-[100%] h-[100%] absolute top-0 left-0 bg-black/20 backdrop-blur-sm z-10 flex justify-center items-center' }}">
                            <div class="flex flex-col justify-between items-center bg-white dark:bg-gray-700 rounded-lg p-6 w-full max-w-md text-center">
                                <h2 class="text-xl font-bold mb-4 text-gray-800 dark:text-gray-200">Data Privacy Consent</h2>
                                <p class="mb-6 text-gray-600 dark:text-gray-300">To access your Personal Data Sheet, please provide your consent to process your personal data in accordance with our data privacy policy.</p>
                                <a href="/data-privacy-policy" target="_blank" class="text-blue-500 hover:text-blue-600 text-sm mb-4 underline">Data Privacy Policy</a>
                                <button wire:click="giveConsent" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors duration-200">I Consent</button>
                            </div>
                        </div>

                        {{-- Tabs and DP -------- --}}
                        <div class="sidebar-section">
                            <div class="flex justify-center items-center" style="width: 150px; height: 150px;">
                                @if ($user->profile_photo_path)
                                    <img src="{{ route('profile-photo.file', ['filename' => basename($user->profile_photo_path)]) }}"
                                        alt="{{ $user->name }}"
                                        style="width: 150px; height: 150px;"
                                        class="rounded-full object-cover border border-gray-300 dark:border-gray-600 shadow-lg">
                                @else
                                    <div class="rounded-full bg-gray-500 border border-gray-300 dark:border-gray-600
                                        dark:bg-gray-600 flex items-center justify-center 
                                        text-white text-2xl font-medium shadow-lg"
                                        style="width: 150px; height: 150px;">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}{{ strtoupper(substr(explode(' ', $user->name)[1] ?? '', 0, 1)) }}
                                    </div>
                                @endif
                            </div>

                            <h1 class="text-xl font-bold text-center text-slate-800 dark:text-white">PERSONAL DATA</h1>

                            <div class="flex justify-center flex-col gap-2 overflow-x-auto">
                                <button @click="tab = 'employee-details'"
                                        :class="{ 'font-bold dark:text-gray-300 dark:bg-gray-700 bg-gray-200 rounded-lg': tab === 'employee-details', 'text-slate-700 font-medium dark:text-slate-300 dark:hover:text-white hover:text-black': tab !== 'employee-details' }"
                                        class="text-left h-min px-4 py-1 text-sm text-nowrap">
                                    Employee Information
                                </button>
                                <button @click="tab = 'personal-details'"
                                        :class="{ 'font-bold dark:text-gray-300 dark:bg-gray-700 bg-gray-200 rounded-lg': tab === 'personal-details', 'text-slate-700 font-medium dark:text-slate-300 dark:hover:text-white hover:text-black': tab !== 'personal-details' }"
                                        class="text-left h-min px-4 py-1 text-sm text-nowrap">
                                    Personal Information
                                </button>
                                <button @click="tab = 'family-background'"
                                        :class="{ 'font-bold dark:text-gray-300 dark:bg-gray-700 bg-gray-200 rounded-lg': tab === 'family-background', 'text-slate-700 font-medium dark:text-slate-300 dark:hover:text-white hover:text-black': tab !== 'family-background' }"
                                        class="text-left h-min px-4 py-1 text-sm text-nowrap">
                                    Family Background
                                </button>
                                <button @click="tab = 'educational-background'"
                                        :class="{ 'font-bold dark:text-gray-300 dark:bg-gray-700 bg-gray-200 rounded-lg': tab === 'educational-background', 'text-slate-700 font-medium dark:text-slate-300 dark:hover:text-white hover:text-black': tab !== 'educational-background' }"
                                        class="text-left h-min px-4 py-1 text-sm text-nowrap">
                                    Educational Background
                                </button>
                                <button @click="tab = 'eligibility'"
                                        :class="{ 'font-bold dark:text-gray-300 dark:bg-gray-700 bg-gray-200 rounded-lg': tab === 'eligibility', 'text-slate-700 font-medium dark:text-slate-300 dark:hover:text-white hover:text-black': tab !== 'eligibility' }"
                                        class="text-left h-min px-4 py-1 text-sm text-nowrap">
                                    Civil Service Eligibility
                                </button>
                                <button @click="tab = 'work-experience'"
                                        :class="{ 'font-bold dark:text-gray-300 dark:bg-gray-700 bg-gray-200 rounded-lg': tab === 'work-experience', 'text-slate-700 font-medium dark:text-slate-300 dark:hover:text-white hover:text-black': tab !== 'work-experience' }"
                                        class="text-left h-min px-4 py-1 text-sm text-nowrap">
                                    Work Experience
                                </button>
                                <button @click="tab = 'voluntary-work'"
                                        :class="{ 'font-bold dark:text-gray-300 dark:bg-gray-700 bg-gray-200 rounded-lg': tab === 'voluntary-work', 'text-slate-700 font-medium dark:text-slate-300 dark:hover:text-white hover:text-black': tab !== 'voluntary-work' }"
                                        class="text-left h-min px-4 py-1 text-sm text-nowrap">
                                    Voluntary Work
                                </button>
                                <button @click="tab = 'learning-and-development'"
                                        :class="{ 'font-bold dark:text-gray-300 dark:bg-gray-700 bg-gray-200 rounded-lg': tab === 'learning-and-development', 'text-slate-700 font-medium dark:text-slate-300 dark:hover:text-white hover:text-black': tab !== 'learning-and-development' }"
                                        class="text-left h-min px-4 py-1 text-sm text-nowrap">
                                    Learning & Development
                                </button>
                                <button @click="tab = 'background-and-interests'"
                                        :class="{ 'font-bold dark:text-gray-300 dark:bg-gray-700 bg-gray-200 rounded-lg': tab === 'background-and-interests', 'text-slate-700 font-medium dark:text-slate-300 dark:hover:text-white hover:text-black': tab !== 'background-and-interests' }"
                                        class="text-left h-min px-4 py-1 text-sm text-nowrap">
                                    Background & Interests
                                </button>
                                <button @click="tab = 'other-information'"
                                        :class="{ 'font-bold dark:text-gray-300 dark:bg-gray-700 bg-gray-200 rounded-lg': tab === 'other-information', 'text-slate-700 font-medium dark:text-slate-300 dark:hover:text-white hover:text-black': tab !== 'other-information' }"
                                        class="text-left h-min px-4 py-1 text-sm text-nowrap">
                                    Other Information
                                </button>
                                <button @click="tab = 'character-reference'"
                                        :class="{ 'font-bold dark:text-gray-300 dark:bg-gray-700 bg-gray-200 rounded-lg': tab === 'character-reference', 'text-slate-700 font-medium dark:text-slate-300 dark:hover:text-white hover:text-black': tab !== 'character-reference' }"
                                        class="text-left h-min px-4 py-1 text-sm text-nowrap">
                                    Character References
                                </button>
                                <button @click="tab = 'government-id'"
                                        :class="{ 'font-bold dark:text-gray-300 dark:bg-gray-700 bg-gray-200 rounded-lg': tab === 'government-id', 'text-slate-700 font-medium dark:text-slate-300 dark:hover:text-white hover:text-black': tab !== 'government-id' }"
                                        class="text-left h-min px-4 py-1 text-sm text-nowrap">
                                    Govt. Issued ID
                                </button>
                                <button @click="tab = 'e-signature'"
                                        :class="{ 'font-bold dark:text-gray-300 dark:bg-gray-700 bg-gray-200 rounded-lg': tab === 'e-signature', 'text-slate-700 font-medium dark:text-slate-300 dark:hover:text-white hover:text-black': tab !== 'e-signature' }"
                                        class="text-left h-min px-4 py-1 text-sm text-nowrap">
                                    E-Signature
                                </button>

                                <div class="flex w-full">
                                    <button wire:click="exportPDS"
                                        class="w-full peer mt-4 sm:mt-1 inline-flex items-center dark:hover:bg-slate-600 dark:border-slate-600
                                        justify-center px-4 py-1.5 text-sm font-medium tracking-wide 
                                        text-neutral-800 dark:text-neutral-200 transition-colors duration-200 
                                        rounded-lg border border-gray-400 hover:bg-gray-300 focus:outline-none"
                                        type="button" title="Export voluntary-work">
                                        <img class="flex dark:hidden" src="/images/export-excel.png" width="22" alt="">
                                        <img class="hidden dark:block" src="/images/export-excel-dark.png" width="22" alt="">
                                        <span class="ml-2">Export PDS</span>
                                        <div wire:loading wire:target="exportPDS" style="margin-left: 5px">
                                            <div class="spinner-border small text-primary" role="status">
                                            </div>
                                        </div>
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- Tab Content -------- --}}
                        <div class="content-section tab-content overflow-hidden">

                            {{-- employee details Section --------------------------------- --}}
                            <div class="w-full overflow-hidden text-sm" x-show="tab === 'employee-details'">
                                @livewire('user.personal-data.employee-information')
                            </div>

                            {{-- personal details Section --------------------------------- --}}
                            <div class="w-full overflow-hidden text-sm" x-show="tab === 'personal-details'">
                                @livewire('user.personal-data.personal-information')
                            </div>
                            
                            {{-- family background Section -------------------------------- --}}
                            <div class="w-full overflow-hidden text-sm" x-show="tab === 'family-background'">
                                @livewire('user.personal-data.family-background')
                            </div>
                            
                            {{-- educational-background Section --------------------------- --}}
                            <div class="w-full overflow-hidden text-sm" x-show="tab === 'educational-background'">
                                @livewire('user.personal-data.educational-background')
                            </div>
                            
                            {{-- eligibility Section -------------------------------------- --}}
                            <div class="w-full overflow-hidden text-sm" x-show="tab === 'eligibility'">
                                @livewire('user.personal-data.civil-service-eligibility')
                            </div>
                            
                            {{-- work-experience Section ---------------------------------- --}}
                            <div class="w-full overflow-hidden text-sm" x-show="tab === 'work-experience'">
                                @livewire('user.personal-data.work-experience')
                            </div>
                            
                            {{-- voluntary work Section ----------------------------------- --}}
                            <div class="w-full overflow-hidden text-sm" x-show="tab === 'voluntary-work'">
                                @livewire('user.personal-data.voluntary-work')       
                            </div>
                            
                            {{-- learning and development Section ------------------------- --}}
                            <div class="w-full overflow-hidden text-sm" x-show="tab === 'learning-and-development'">
                                @livewire('user.personal-data.learning-and-development')       
                            </div>
                            
                            {{-- background and interest Section -------------------------------- --}}
                            <div class="w-full overflow-hidden text-sm" x-show="tab === 'background-and-interests'">
                                @livewire('user.personal-data.background-and-interests')       
                            </div>
                            
                            {{-- other information Section -------------------------------- --}}
                            <div class="w-full overflow-hidden text-sm" x-show="tab === 'other-information'">
                                @livewire('user.personal-data.other-information')       
                            </div>
                            
                            {{-- character reference Section ------------------------------ --}}
                            <div class="w-full overflow-hidden text-sm" x-show="tab === 'character-reference'">
                                @livewire('user.personal-data.character-reference')       
                            </div>
                            
                            {{-- government issued ids Section ---------------------------- --}}
                            <div class="w-full overflow-hidden text-sm" x-show="tab === 'government-id'">
                                @livewire('user.personal-data.government-id')       
                            </div>
                            
                            {{-- e-signature Section -------------------------------------- --}}
                            <div class="w-full overflow-hidden text-sm" x-show="tab === 'e-signature'">
                                @livewire('user.personal-data.e-signature')       
                            </div>

                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .form-group {
            @apply transition-all duration-200;
        }
        
        .form-group:hover {
            @apply transform scale-[1.01];
        }
        
        input:focus, select:focus, textarea:focus {
            @apply shadow-lg;
        }
    </style>
</div>
