<div class="min-w-fit">
    <!-- Sidebar backdrop (mobile only) -->
    <div class="fixed inset-0 bg-slate-900 bg-opacity-30 z-40 lg:hidden lg:z-auto transition-opacity duration-200"
        :class="sidebarOpen ? 'opacity-100' : 'opacity-0 pointer-events-none'" aria-hidden="true" x-cloak></div>

    <div id="sidebar"
        class="flex flex-col absolute z-40 left-0 top-0 lg:static lg:left-auto lg:top-auto lg:translate-x-0 h-screen overflow-y-scroll lg:overflow-y-auto no-scrollbar w-64 lg:w-20 lg:sidebar-expanded:!w-64 2xl:!w-64 shrink-0 bg-white dark:bg-slate-800 p-4 transition-all duration-200 ease-in-out rounded-3-2xl"
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-64'" @click.outside="sidebarOpen = false"
        @keydown.escape.window="sidebarOpen = false" style="z-index: 9998">

        <div class="flex justify-left mb-10 ml-0 lg:sidebar-expanded:ml-0 2xl:ml-0"
            style="width: 200px; height: 32px;">
            <!-- Logo -->
            <a class="flex items-center" href="{{ route('/dashboard') }}">
                <img class="mx-auto" src="/images/logo.png" alt="hris logo"
                    style="width: 60px; height: auto;">
                <span
                    class="text-black dark:text-white ml-2 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">JJWC HRIS</span>
            </a>
        </div>

        <div class="space-y-8">
            <div>
                <ul class="mt-3">

                    @if (Auth::user()->user_role != 'emp')
                        @livewire('admin.admin-sidebar')
                    @else
                        <!-- Home -->
                        <li class="pl-4 pr-3 py-2 rounded-lg mb-0.5 last:mb-0 bg-[linear-gradient(135deg,var(--tw-gradient-stops))]
                        @if (in_array(Request::segment(1), ['home'])) {{ 'bg-gray-200 dark:bg-slate-900' }} @endif"
                            x-data="{ open: {{ in_array(Request::segment(1), ['home']) ? 1 : 0 }} }">
                            <a class="block text-gray-800 dark:text-gray-100 truncate transition
                            @if (Route::is('home')) {{ '!text-blue-500' }} @endif"
                                href="{{ route('home') }}">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <i class="bi bi-house-fill text-slate-400 dark:text-slate-300 mr-3"></i>
                                        <span
                                            class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">
                                            Home
                                        </span>
                                    </div>
                                </div>
                            </a>
                        </li>

                        <!-- My Records -->
                        <li class="pl-4 pr-3 py-2 rounded-lg mb-0.5 last:mb-0 bg-[linear-gradient(135deg,var(--tw-gradient-stops))]
                        @if (in_array(Request::segment(1), ['my-records'])) {{ 'bg-gray-200 dark:bg-slate-900' }} @endif"
                            x-data="{ open: {{ in_array(Request::segment(1), ['my-records']) ? 1 : 0 }} }">
                            <a class="block text-gray-800 dark:text-gray-100 truncate transition" href="#0"
                                @click.prevent="sidebarExpanded ? open = !open : sidebarExpanded = true">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <i class="bi bi-journal-check text-slate-400 dark:text-slate-300 mr-3"></i>
                                        <span
                                            class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">
                                            My Records
                                        </span>
                                    </div>
                                    <div class="flex shrink-0 ml-2">
                                        <svg class="lg:hidden lg:sidebar-expanded:inline w-3 h-3 shrink-0 ml-1 fill-current text-slate-400 transition-transform duration-300"
                                            :class="open ? 'rotate-180' : 'rotate-0'" viewBox="0 0 12 12">
                                            <path d="M5.9 11.4L.5 6l1.4-1.4 4 4 4-4L11.3 6z" />
                                        </svg>
                                    </div>
                                </div>
                            </a>
                            <div class="lg:hidden lg:sidebar-expanded:block 2xl:block">
                                <ul class="pl-9 mt-1 transition-all duration-300 overflow-hidden"
                                    :class="open ? '!block' : 'hidden'">
                                    <li class="mb-1 last:mb-0">
                                        <a class="block text-black dark:text-slate-400 hover:text-blue-500 dark:hover:text-blue-500 transition duration-150 truncate @if (Route::is('/my-records/personal-data-sheet')) {{ '!text-blue-500' }} @endif"
                                            href="{{ route('/my-records/personal-data-sheet') }}" wire:navigate>
                                            <span class="text-sm font-medium transition-opacity duration-300"
                                                :class="sidebarExpanded ? 'opacity-100 lg:inline' : 'opacity-0 lg:hidden'">
                                                Personal Data</span>
                                        </a>
                                    </li>
                                    <li class="mb-1 last:mb-0">
                                        <a class="block text-black dark:text-slate-400 hover:text-blue-500 dark:hover:text-blue-500 transition duration-150 truncate @if (Route::is('/my-records/work-experience-sheet')) {{ '!text-blue-500' }} @endif"
                                            href="{{ route('/my-records/work-experience-sheet') }}">
                                            <span class="text-sm font-medium transition-opacity duration-300"
                                                :class="sidebarExpanded ? 'opacity-100 lg:inline' : 'opacity-0 lg:hidden'">
                                                Work Experience Sheet</span>
                                        </a>
                                    </li>
                                    <li class="mb-1 last:mb-0">
                                        <a class="block text-black dark:text-slate-400 hover:text-blue-500 dark:hover:text-blue-500 transition duration-150 truncate @if (Route::is('/my-records/my-documents')) {{ '!text-blue-500' }} @endif"
                                            href="{{ route('/my-records/my-documents') }}" wire:navigate>
                                            <span class="text-sm font-medium transition-opacity duration-300"
                                                :class="sidebarExpanded ? 'opacity-100 lg:inline' : 'opacity-0 lg:hidden'">
                                                My Documents</span>
                                        </a>
                                    </li>
                                    <li class="mb-1 last:mb-0">
                                        <a class="block text-black dark:text-slate-400 hover:text-blue-500 dark:hover:text-blue-500 transition duration-150 truncate @if (Route::is('/my-records/doc-request')) {{ '!text-blue-500' }} @endif"
                                            href="{{ route('/my-records/doc-request') }}" wire:navigate>
                                            <span class="text-sm font-medium transition-opacity duration-300"
                                                :class="sidebarExpanded ? 'opacity-100 lg:inline' : 'opacity-0 lg:hidden'">
                                                Document Request</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>

                        <!-- DTR -->
                        <li class="pl-4 pr-3 py-2 rounded-lg mb-0.5 last:mb-0 bg-[linear-gradient(135deg,var(--tw-gradient-stops))]
                        @if (in_array(Request::segment(1), ['daily-time-record'])) {{ 'bg-gray-200 dark:bg-slate-900' }} @endif"
                            x-data="{ open: {{ in_array(Request::segment(1), ['daily-time-record']) ? 1 : 0 }} }">
                            <a class="block text-gray-800 dark:text-gray-100 truncate transition" href="#0"
                                @click.prevent="sidebarExpanded ? open = !open : sidebarExpanded = true">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <i class="bi bi-clock text-slate-400 mr-3"></i>
                                        <span
                                            class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">
                                            Daily Time Record
                                        </span>
                                    </div>
                                    <div class="flex shrink-0 ml-2">
                                        <svg class="lg:hidden lg:sidebar-expanded:inline w-3 h-3 shrink-0 ml-1 fill-current text-slate-400 transition-transform duration-300"
                                            :class="open ? 'rotate-180' : 'rotate-0'" viewBox="0 0 12 12">
                                            <path d="M5.9 11.4L.5 6l1.4-1.4 4 4 4-4L11.3 6z" />
                                        </svg>
                                    </div>
                                </div>
                            </a>
                            <div class="lg:hidden lg:sidebar-expanded:block 2xl:block">
                                <ul class="pl-9 mt-1 transition-all duration-300 overflow-hidden"
                                    :class="open ? '!block' : 'hidden'">
                                    <li class="mb-1 last:mb-0">
                                        <a class="block text-black dark:text-slate-400 hover:text-blue-500 transition duration-150 truncate @if (Route::is('/daily-time-record/dtr')) {{ '!text-blue-500' }} @endif"
                                            href="{{ route('/daily-time-record/dtr') }}"wire:navigate>
                                            <span class="text-sm font-medium transition-opacity duration-300"
                                                :class="sidebarExpanded ? 'opacity-100 lg:inline' : 'opacity-0 lg:hidden'">
                                                DTR
                                            </span>
                                        </a>
                                    </li>
                                    <li class="mb-1 last:mb-0">
                                        <a class="block text-black dark:text-slate-400 hover:text-blue-500 transition duration-150 truncate @if (Route::is('/daily-time-record/official-business')) {{ '!text-blue-500' }} @endif"
                                            href="{{ route('/daily-time-record/official-business') }}">
                                            <span class="text-sm font-medium transition-opacity duration-300"
                                                :class="sidebarExpanded ? 'opacity-100 lg:inline' : 'opacity-0 lg:hidden'">
                                                Official Business
                                            </span>
                                        </a>
                                    </li>
                                    <li class="mb-1 last:mb-0">
                                        <a class="block text-black dark:text-slate-400 hover:text-blue-500 transition duration-150 truncate @if (Route::is('/daily-time-record/my-schedule')) {{ '!text-blue-500' }} @endif"
                                            href="{{ route('/daily-time-record/my-schedule') }}"wire:navigate>
                                            <span class="text-sm font-medium transition-opacity duration-300"
                                                :class="sidebarExpanded ? 'opacity-100 lg:inline' : 'opacity-0 lg:hidden'">
                                                My Schedule
                                            </span>
                                        </a>
                                    </li>
                                    <li class="mb-1 last:mb-0">
                                        <a class="block text-black dark:text-slate-400 hover:text-blue-500 transition duration-150 truncate @if (Route::is('/daily-time-record/wfh-sched')) {{ '!text-blue-500' }} @endif"
                                            href="{{ route('/daily-time-record/wfh-sched') }}"wire:navigate>
                                            <span class="text-sm font-medium transition-opacity duration-300"
                                                :class="sidebarExpanded ? 'opacity-100 lg:inline' : 'opacity-0 lg:hidden'">
                                                WFH Request
                                            </span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>

                        <!-- Filing and Approval -->
                        <li class="pl-4 pr-3 py-2 rounded-lg mb-0.5 last:mb-0 bg-[linear-gradient(135deg,var(--tw-gradient-stops))]
                            @if (in_array(Request::segment(1), ['filing-and-approval'])) {{ 'bg-gray-200 dark:bg-slate-900' }} @endif"
                            x-data="{ open: {{ in_array(Request::segment(1), ['filing-and-approval']) ? 1 : 0 }} }">
                            <a class="block text-gray-800 dark:text-gray-100 truncate transition" href="#0"
                                @click.prevent="sidebarExpanded ? open = !open : sidebarExpanded = true">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <i class="bi bi-card-checklist text-slate-400 mr-3"></i>
                                        <span
                                            class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">
                                            Filing and Approval
                                        </span>
                                    </div>
                                    <div class="flex shrink-0 ml-2">
                                        <svg class="lg:hidden lg:sidebar-expanded:inline w-3 h-3 shrink-0 ml-1 fill-current text-slate-400 transition-transform duration-300"
                                            :class="open ? 'rotate-180' : 'rotate-0'" viewBox="0 0 12 12">
                                            <path d="M5.9 11.4L.5 6l1.4-1.4 4 4 4-4L11.3 6z" />
                                        </svg>
                                    </div>
                                </div>
                            </a>
                            <div class="lg:hidden lg:sidebar-expanded:block 2xl:block">
                                <ul class="pl-9 mt-1 transition-all duration-300 overflow-hidden"
                                    :class="open ? '!block' : 'hidden'">
                                    {{-- <li class="mb-1 last:mb-0">
                                        <a class="block text-black dark:text-slate-400 hover:text-blue-500 transition duration-150 truncate @if (Route::is('/filing-and-approval')) {{ '!text-blue-500' }} @endif"
                                            href="#0">
                                            <span class="text-sm font-medium transition-opacity duration-300"
                                                :class="sidebarExpanded ? 'opacity-100 lg:inline' : 'opacity-0 lg:hidden'">
                                                Overtime
                                            </span>
                                        </a>
                                    </li> --}}
                                    <li class="mb-1 last:mb-0">
                                        <a class="block text-black dark:text-slate-400 hover:text-blue-500 transition duration-150 truncate @if (Route::is('/filing-and-approval/leave-application')) {{ '!text-blue-500' }} @endif"
                                            href="{{ route('/filing-and-approval/leave-application') }}"
                                            wire:navigate>
                                            <span class="text-sm font-medium transition-opacity duration-300"
                                                :class="sidebarExpanded ? 'opacity-100 lg:inline' : 'opacity-0 lg:hidden'">
                                                Leave Application
                                            </span>
                                        </a>
                                    </li>
                                    @if (auth()->check() && auth()->user()->is_oic)
                                        <li class="mb-1 last:mb-0">
                                            <a class="block text-black dark:text-slate-400 hover:text-blue-500 transition duration-150 truncate @if (Route::is('/filing-and-approval/admin-leave-request')) {{ '!text-blue-500' }} @endif"
                                                href="{{ route('/filing-and-approval/admin-leave-request') }}"
                                                wire:navigate>
                                                <span class="text-sm font-medium transition-opacity duration-300"
                                                    :class="sidebarExpanded ? 'opacity-100 lg:inline' : 'opacity-0 lg:hidden'">
                                                    Leave Request - OIC
                                                </span>
                                            </a>
                                        </li>
                                    @endif
                                    {{-- <li class="mb-1 last:mb-0">
                                        <a class="block text-black dark:text-slate-400 hover:text-blue-500 transition duration-150 truncate @if (Route::is('/filing-and-approval/leave-credits')) {{ '!text-blue-500' }} @endif"
                                            href="{{ route('/filing-and-approval/leave-credits') }}" wire:navigate>
                                            <span class="text-sm font-medium transition-opacity duration-300"
                                                :class="sidebarExpanded ? 'opacity-100 lg:inline' : 'opacity-0 lg:hidden'">
                                                Leave Credits
                                            </span>
                                        </a>
                                    </li> --}}
                                    <li class="mb-1 last:mb-0">
                                        <a class="block text-black dark:text-slate-400 hover:text-blue-500 transition duration-150 truncate @if (Route::is('/filing-and-approval/leave-monetization')) {{ '!text-blue-500' }} @endif"
                                            href="{{ route('/filing-and-approval/leave-monetization') }}"
                                            wire:navigate>
                                            <span class="text-sm font-medium transition-opacity duration-300"
                                                :class="sidebarExpanded ? 'opacity-100 lg:inline' : 'opacity-0 lg:hidden'">
                                                Leave Monetization
                                            </span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>

                        {{-- Downloadable Forms --}}
                        <li class="pl-4 pr-3 py-2 rounded-lg mb-0.5 last:mb-0 bg-[linear-gradient(135deg,var(--tw-gradient-stops))]
                            @if (in_array(Request::segment(1), ['downloadable'])) {{ 'bg-gray-200 dark:bg-slate-900' }} @endif"
                            x-data="{ open: {{ in_array(Request::segment(1), ['downloadable']) ? 1 : 0 }} }">
                            <a class="block text-gray-800 dark:text-gray-100 truncate transition
                            @if (Route::is('downloadable')) {{ '!text-blue-500' }} @endif"
                                href="{{ route('downloadable') }}" wire:navigate>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <i class="bi bi-journal-code text-slate-400 mr-3"></i>
                                        <span
                                            class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">
                                            Downloadable Forms
                                        </span>
                                    </div>
                                </div>
                            </a>
                        </li>
                    @endif
                </ul>
            </div>
        </div>

        <!-- Expand / collapse button -->
        <div class="pt-3 hidden lg:inline-flex 2xl:hidden justify-end mt-auto">
            <div class="px-3 py-2">
                <button @click="sidebarExpanded = !sidebarExpanded">
                    <span class="sr-only">Expand / collapse sidebar</span>
                    <svg class="w-6 h-6 fill-current sidebar-expanded:rotate-180" viewBox="0 0 24 24">
                        <path class="text-slate-400"
                            d="M19.586 11l-5-5L16 4.586 23.414 12 16 19.414 14.586 18l5-5H7v-2z" />
                        <path class="text-slate-600" d="M3 23H1V1h2z" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
</div>
