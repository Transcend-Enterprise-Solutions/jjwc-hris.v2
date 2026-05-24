<div x-data="{ open: false }" class="w-full">
    <style>
        .scrollbar-thin1::-webkit-scrollbar {
            width: 5px;
        }

        .scrollbar-thin1::-webkit-scrollbar-thumb {
            background-color: #1a1a1a4b;
            /* cursor: grab; */
            border-radius: 0 50px 50px 0;
        }

        .scrollbar-thin1::-webkit-scrollbar-track {
            background-color: #ffffff23;
            border-radius: 0 50px 50px 0;
        }

        @media (max-width: 1024px) {
            .custom-d {
                display: block;
            }
        }

        @media (max-width: 768px) {
            .m-scrollable {
                width: 100%;
                overflow-x: scroll;
            }
        }

        @media (min-width:1024px) {
            .custom-p {
                padding-bottom: 14px !important;
            }
        }

        @-webkit-keyframes spinner-border {
            to {
                transform: rotate(360deg);
            }
        }

        @keyframes spinner-border {
            to {
                transform: rotate(360deg);
            }
        }

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

    @if (!$applyForLeave)
        {{-- Leave Application Table --}}
        <div class="w-full flex justify-center">
            <div class="flex justify-center w-full">
                <div class="w-full bg-white rounded-2xl p-3 sm:p-6 shadow dark:bg-gray-800 overflow-x-visible">
                    <div class="pb-4 pt-4 sm:pt-1 flex items-center justify-between relative">
                        <!-- Title -->
                        <div class="w-full text-center">
                            <h1 class="text-lg font-bold text-slate-800 dark:text-white">Leave Application</h1>
                        </div>

                        <!-- Three Dots Button -->
                        {{-- <div class="relative">
                            <!-- Button to toggle dropdown -->
                            <button wire:click="toggleDropdown"
                                class="p-2 rounded-lg hover:bg-gray-200 dark:hover:bg-slate-600 focus:outline-none">
                                <i class="bi bi-three-dots-vertical text-slate-800 dark:text-white"></i>
                            </button>

                            <!-- Dropdown Menu (Hidden by default) -->
                            <div wire:click.away="closeDropdown"
                                class="absolute right-0 mt-2 w-64 rounded-md shadow-lg bg-white dark:bg-slate-700 ring-1 ring-black ring-opacity-5 z-50 {{ $showDropdown ? 'block' : 'hidden' }}">
                                <div class="p-2">
                                    <!-- Request Button -->
                                    <button wire:click="requestForm"
                                        class="block w-full whitespace-nowrap px-4 py-2 text-xs text-slate-800 dark:text-white hover:bg-gray-100 dark:hover:bg-slate-600 rounded-md
                           transition-all">
                                        @if ($requestSent)
                                            Request Sent <i class="bi bi-check2-circle text-green-500"></i>
                                        @else
                                            Request Mandatory Leave Form
                                        @endif
                                    </button>

                                    <!-- Export Button (Disabled until approved) -->
                                    <button wire:click="exportMandatoryLeaveForm" transition-all
                                        {{ !$requestApproved ? 'cursor-not-allowed opacity-50' : '' }}"
                                        @if (!$requestApproved) disabled title="Not yet approved. Please wait for it" @endif>
                                        Export Mandatory Leave Form
                                    </button>
                                </div>
                            </div>
                        </div> --}}
                    </div>

                    <div class="flex flex-col justify-between items-center sm:flex-row">
                        <div class="flex flex-col md:flex-row items-center w-56">
                            <!-- Apply for Leave Button -->
                            <div class="flex items-center mb-4 md:mb-0 w-full">
                                <button wire:click="openLeaveForm"
                                    class="text-sm mt-4 sm:mt-1 px-2 py-1.5 bg-green-500 text-white rounded-md hover:bg-green-600 focus:outline-none dark:bg-green-700 w-full dark:hover:bg-green-600 dark:text-gray-300 dark:hover:text-white">
                                    Apply For Leave
                                </button>
                            </div>
                            {{-- <div class="flex items-center mb-4 md:mb-0 w-full">
                                <button wire:click="openLeaveForm"
                                    class="text-sm mt-4 sm:mt-1 px-2 py-1.5 bg-green-500 text-white rounded-md hover:bg-green-600 focus:outline-none dark:bg-green-700 w-full dark:hover:bg-green-600 dark:text-gray-300 dark:hover:text-white {{ !$hasAllApprovers ? 'opacity-50 cursor-not-allowed' : '' }}"
                                    @if (!$hasAllApprovers) title="Approvers must be set first" @endif>
                                    @if (!$hasAllApprovers)
                                        <i class="bi bi-lock-fill mr-1"></i>
                                    @endif
                                    Apply For Leave
                                </button>
                            </div> --}}
                        </div>

                        <!-- Year Selection and Export Button -->
                        <div class="flex justify-center items-center gap-4 w-64">
                            <!-- Year Selection -->
                            <div class="flex flex-col">
                                <label class="block text-sm font-medium text-gray-700 dark:text-slate-400 mb-1">
                                    Select Year:
                                </label>
                                <select wire:model.live="selectedYear"
                                    class="px-2 py-1.5 block w-full shadow-sm sm:text-sm border border-gray-400 hover:bg-gray-300 rounded-md dark:hover:bg-slate-600 dark:border-slate-600 dark:text-gray-300 dark:bg-gray-800">
                                    @for ($year = now()->year; $year >= 2020; $year--)
                                        <option value="{{ $year }}">{{ $year }}</option>
                                    @endfor
                                </select>
                            </div>

                            <!-- Export Button -->
                            <div class="flex flex-col">
                                <label class="block text-sm font-medium text-gray-700 dark:text-slate-400 mb-1">
                                    Generate Leave Card
                                </label>
                                <button wire:click="exportExcel"
                                    class="inline-flex items-center justify-center px-4 py-1.5 text-sm font-medium tracking-wide text-neutral-800 dark:text-neutral-200 transition-colors duration-200 rounded-lg border border-gray-400 hover:bg-gray-300 dark:hover:bg-slate-600 dark:border-slate-600 focus:outline-none {{ $isDisabled ? 'opacity-50 cursor-not-allowed' : 'opacity-100 cursor-pointer' }}"
                                    type="button" title="Export Leave Card" {{ $isDisabled ? 'disabled' : '' }}>
                                    <img class="flex dark:hidden" src="/images/export-excel.png" width="22"
                                        alt="exportExcel" wire:target="exportExcel" wire:loading.remove>
                                    <img class="hidden dark:block" src="/images/export-excel-dark.png" width="22"
                                        alt="exportExcel" wire:target="exportExcel" wire:loading.remove>
                                    <div wire:loading wire:target="exportExcel">
                                        <div class="spinner-border small text-primary" role="status"></div>
                                    </div>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div x-data="{ activeTab: @entangle('activeTab') }" class="flex flex-col">
                        <!-- Tabs for Status -->
                        <div class="flex gap-2 overflow-x-auto -mb-2 mt-2">
                            <button @click="$wire.setActiveTab('pending')"
                                :class="{ 'font-bold dark:text-gray-300 dark:bg-gray-700 bg-gray-200 rounded-t-lg': activeTab === 'pending', 'text-slate-700 font-medium dark:text-slate-300 dark:hover:text-white hover:text-black': activeTab !== 'pending' }"
                                class="h-min px-4 pt-2 pb-4 text-sm" role="tab">Pending</button>
                            <button @click="$wire.setActiveTab('approved')"
                                :class="{ 'font-bold dark:text-gray-300 dark:bg-gray-700 bg-gray-200 rounded-t-lg': activeTab === 'approved', 'text-slate-700 font-medium dark:text-slate-300 dark:hover:text-white hover:text-black': activeTab !== 'approved' }"
                                class="h-min px-4 pt-2 pb-4 text-sm" role="tab">Approved</button>
                            <button @click="$wire.setActiveTab('disapproved')"
                                :class="{ 'font-bold dark:text-gray-300 dark:bg-gray-700 bg-gray-200 rounded-t-lg': activeTab === 'disapproved', 'text-slate-700 font-medium dark:text-slate-300 dark:hover:text-white hover:text-black': activeTab !== 'disapproved' }"
                                class="h-min px-4 pt-2 pb-4 text-sm" role="tab">Disapproved</button>
                        </div>

                        <!-- Table for Leave Applications -->
                        <div class="overflow-x-auto text-sm">
                            <div class="overflow-hidden border dark:border-gray-700 rounded-t-lg">
                                <div class="overflow-x-auto">
                                    <table class="w-full min-w-full">
                                        <thead class="bg-gray-200 dark:bg-gray-700 rounded-xl">
                                            <tr class="whitespace-nowrap">
                                                <th scope="col" class="px-4 py-2 text-center">Date of Filing</th>
                                                <th scope="col" class="px-4 py-2 text-center">Type of Leave</th>
                                                {{-- <th scope="col" class="px-4 py-2 text-center">Leave Mode</th> --}}
                                                <th scope="col" class="px-4 py-2 text-center">Details of Leave</th>
                                                @if ($activeTab === 'pending')
                                                    <th scope="col" class="px-4 py-2 text-center">
                                                        Requested
                                                    </th>
                                                    <th scope="col" class="px-4 py-2 text-center">
                                                        Requested Date/s
                                                    </th>
                                                @elseif ($activeTab === 'disapproved')
                                                    <th scope="col" class="px-4 py-2 text-center">
                                                        Disapproved
                                                    </th>
                                                    <th scope="col" class="px-4 py-2 text-center">
                                                        Disapproved Date/s
                                                    </th>
                                                @else
                                                    <th scope="col" class="px-4 py-2 text-center">
                                                        Approved
                                                    </th>
                                                    <th scope="col" class="px-4 py-2 text-center">
                                                        Approved Date/s
                                                    </th>
                                                @endif
                                                <th scope="col" class="px-4 py-2 text-center">Approvers</th>
                                                <th
                                                    class="px-5 py-3 text-gray-100 text-sm font-medium text-center sticky top-0 right-0 z-10 bg-gray-600 dark:bg-gray-600 uppercase">
                                                    Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if ($leaveApplications->count() > 0)
                                                @foreach ($leaveApplications as $leaveApplication)
                                                    @php
                                                        // Get the approval record for this application
                                                        $approval = \App\Models\LeaveApprovals::with([
                                                            'firstApproverUser',
                                                            'secondApproverUser',
                                                            'thirdApproverUser',
                                                        ])
                                                            ->where('application_id', $leaveApplication->id)
                                                            ->first();
                                                    @endphp
                                                    <tr class="whitespace-nowrap">
                                                        <td class="px-4 py-2 text-center">
                                                            {{ \Carbon\Carbon::parse($leaveApplication->date_of_filing)->format('m/d/Y') }}
                                                        </td>
                                                        <td class="px-4 py-2 text-center">
                                                            {{ $leaveApplication->type_of_leave }}
                                                        </td>
                                                        <td class="px-4 py-2 text-center">
                                                            {{ $leaveApplication->details_of_leave ?? 'N/A' }}
                                                        </td>
                                                        <td class="px-4 py-2 text-center">
                                                            {{ $leaveApplication->number_of_days }}
                                                            day{{ $leaveApplication->number_of_days > 1 ? 's' : '' }}
                                                        </td>
                                                        <td class="px-4 py-2 text-center">
                                                            @php
                                                                $dates = $leaveApplication->approved_dates ?? $leaveApplication->list_of_dates;
                                                            @endphp

                                                            @if (Str::contains($dates, ' - '))
                                                                {{-- Date range: just display as-is or format each part --}}
                                                                @php
                                                                    [$start, $end] = explode(' - ', $dates, 2);
                                                                @endphp
                                                                {{ \Carbon\Carbon::parse(trim($start))->format('m/d/Y') }} - {{ \Carbon\Carbon::parse(trim($end))->format('m/d/Y') }}
                                                            @else
                                                                <div class="flex flex-col">
                                                                    @foreach (explode(',', $dates) as $date)
                                                                        <span>{{ \Carbon\Carbon::parse(trim($date))->format('m/d/Y') }}</span>
                                                                    @endforeach
                                                                </div>
                                                            @endif
                                                        </td>

                                                        <!-- Approvers Column -->
                                                        <td class="px-4 py-2 text-center">
                                                            <div class="flex justify-center items-center space-x-2">
                                                                <!-- First Approver -->
                                                                <div class="relative group">
                                                                    @if ($approval && $approval->firstApproverUser)
                                                                        @if ($approval->firstApproverUser->profile_photo_path)
                                                                            <img src="{{ route('profile-photo.file', ['filename' => basename($approval->firstApproverUser->profile_photo_path)]) }}"
                                                                                alt="{{ $approval->firstApproverUser->name }}"
                                                                                class="w-8 h-8 rounded-full object-cover border border-gray-300 dark:border-gray-600"
                                                                                title="{{ $approval->firstApproverUser->name }}">
                                                                        @else
                                                                            <div class="w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center text-white text-xs font-medium"
                                                                                title="{{ $approval->firstApproverUser->name }}">
                                                                                {{ strtoupper(substr($approval->firstApproverUser->name, 0, 1)) }}
                                                                            </div>
                                                                        @endif
                                                                    @else
                                                                        <div class="w-8 h-8 rounded-full bg-gray-300 flex items-center justify-center text-gray-600 text-xs"
                                                                            title="Not assigned">
                                                                            <i class="bi bi-person-x"></i>
                                                                        </div>
                                                                    @endif
                                                                </div>

                                                                <!-- Second Approver -->
                                                                <div class="relative group">
                                                                    @if ($approval && $approval->secondApproverUser)
                                                                        @if ($approval->secondApproverUser->profile_photo_path)
                                                                            <img src="{{ route('profile-photo.file', ['filename' => basename($approval->secondApproverUser->profile_photo_path)]) }}"
                                                                                alt="{{ $approval->secondApproverUser->name }}"
                                                                                class="w-8 h-8 rounded-full object-cover border border-gray-300 dark:border-gray-600"
                                                                                title="{{ $approval->secondApproverUser->name }}">
                                                                        @else
                                                                            <div class="w-8 h-8 rounded-full bg-violet-500 flex items-center justify-center text-white text-xs font-medium"
                                                                                title="{{ $approval->secondApproverUser->name }}">
                                                                                {{ strtoupper(substr($approval->secondApproverUser->name, 0, 1)) }}
                                                                            </div>
                                                                        @endif
                                                                    @else
                                                                        <div class="w-8 h-8 rounded-full bg-gray-300 flex items-center justify-center text-gray-600 text-xs"
                                                                            title="Not assigned">
                                                                            <i class="bi bi-person-x"></i>
                                                                        </div>
                                                                    @endif
                                                                </div>

                                                                <!-- Third Approver -->
                                                                <div class="relative group">
                                                                    @if ($approval && $approval->thirdApproverUser)
                                                                        @if ($approval->thirdApproverUser->profile_photo_path)
                                                                            <img src="{{ route('profile-photo.file', ['filename' => basename($approval->thirdApproverUser->profile_photo_path)]) }}"
                                                                                alt="{{ $approval->thirdApproverUser->name }}"
                                                                                class="w-8 h-8 rounded-full object-cover border border-gray-300 dark:border-gray-600"
                                                                                title="{{ $approval->thirdApproverUser->name }}">
                                                                        @else
                                                                            <div class="w-8 h-8 rounded-full bg-green-500 flex items-center justify-center text-white text-xs font-medium"
                                                                                title="{{ $approval->thirdApproverUser->name }}">
                                                                                {{ strtoupper(substr($approval->thirdApproverUser->name, 0, 1)) }}
                                                                            </div>
                                                                        @endif
                                                                    @else
                                                                        <div class="w-8 h-8 rounded-full bg-gray-300 flex items-center justify-center text-gray-600 text-xs"
                                                                            title="Not assigned">
                                                                            <i class="bi bi-person-x"></i>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </td>

                                                        <td
                                                            class="px-5 py-4 text-sm font-medium text-center whitespace-nowrap sticky right-0 z-10 bg-white dark:bg-gray-800">
                                                            <div
                                                                class="relative flex items-center justify-center space-x-2">
                                                                <!-- Export PDF Button with Loading -->
                                                                <button type="button"
                                                                    wire:click.prevent="exportPDF({{ $leaveApplication->id }})"
                                                                    wire:loading.attr="disabled"
                                                                    class="inline-flex items-center justify-center px-3 py-1 text-sm font-medium tracking-wide text-red-500 hover:text-red-600 focus:outline-none transition-colors {{ $exportingPDF && $currentPDFId == $leaveApplication->id ? 'opacity-50 cursor-not-allowed' : '' }}"
                                                                    title="Export in PDF"
                                                                    @if ($exportingPDF && $currentPDFId == $leaveApplication->id) disabled @endif>
                                                                    <span wire:loading.remove
                                                                        wire:target="exportPDF({{ $leaveApplication->id }})">
                                                                        <i class="bi bi-file-earmark-arrow-down"></i>
                                                                    </span>
                                                                    <span wire:loading
                                                                        wire:target="exportPDF({{ $leaveApplication->id }})">
                                                                        <div class="spinner-border small text-red-500"
                                                                            role="status"></div>
                                                                    </span>
                                                                </button>

                                                                <!-- Show PDF Button with Loading -->
                                                                <button
                                                                    wire:click="applicationProgress({{ $leaveApplication->id }})"
                                                                    wire:loading.attr="disabled"
                                                                    class="inline-flex items-center justify-center px-3 py-1 text-sm font-medium tracking-wide text-blue-500 hover:text-blue-600 transition-colors {{ $showingProgress && $currentPDFId == $leaveApplication->id ? 'opacity-50 cursor-not-allowed' : '' }}"
                                                                    title="View Application Progress"
                                                                    @if ($showingProgress && $currentPDFId == $leaveApplication->id) disabled @endif>
                                                                    <span wire:loading.remove
                                                                        wire:target="applicationProgress({{ $leaveApplication->id }})">
                                                                        <i class="bi bi-eye"></i>
                                                                    </span>
                                                                    <span wire:loading
                                                                        wire:target="applicationProgress({{ $leaveApplication->id }})">
                                                                        <div class="spinner-border small text-blue-500"
                                                                            role="status"></div>
                                                                    </span>
                                                                </button>

                                                                <!-- Cancel Button (Only for pending applications) -->
                                                                @if ($leaveApplication->status === 'Pending')
                                                                    <button
                                                                        wire:click="openCancelModal({{ $leaveApplication->id }})"
                                                                        class="inline-flex items-center justify-center px-3 py-1 text-sm font-medium tracking-wide text-orange-500 hover:text-orange-600 transition-colors"
                                                                        title="Cancel Application">
                                                                        <i class="bi bi-x-circle"></i>
                                                                    </button>
                                                                @endif
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="7" class="px-4 py-2 text-center">
                                                        No {{ $activeTab }} leave request.
                                                    </td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        {{-- Pagination --}}
                        <div
                            class="p-5 border-t rounded-b-lg border-gray-200 dark:border-slate-600 text-neutral-500 dark:text-neutral-200 bg-gray-200 dark:bg-gray-700">
                            {{ $leaveApplications->links() }}
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <!-- Updated Application Progress Modal -->
        <x-modal id="applicationProgressModal" maxWidth="2xl" centered wire:model="showingProgress">
            <div class="p-0 bg-white dark:bg-gray-800 rounded-lg">
                <!-- Header -->
                <div class="bg-gradient-to-r from-blue-600 to-purple-600 p-4 rounded-t-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-lg font-bold text-white">Application Progress</h2>
                        </div>
                    </div>
                </div>

                <!-- Progress Content -->
                <div class="p-4 max-h-96 overflow-y-auto">
                    @if ($currentPDFId)
                        @php
                            $application = $this->getApplicationDetails($currentPDFId);
                            $latestApproval = $this->getLatestApproval($application);
                            $progress = $this->getApprovalProgress($application);
                        @endphp

                        <!-- Application Info -->
                        <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                <div>
                                    <span class="font-semibold text-gray-600 dark:text-gray-300">Type:</span>
                                    <span
                                        class="text-gray-800 dark:text-white ml-2">{{ $application->type_of_leave }}</span>
                                </div>
                                <div>
                                    <span class="font-semibold text-gray-600 dark:text-gray-300">Status:</span>
                                    <span
                                        class="ml-2 capitalize 
                                @if ($application->status === 'Approved') text-green-600
                                @elseif($application->status === 'Disapproved') text-red-600
                                @else text-yellow-600 @endif font-medium">
                                        {{ $application->status }}
                                    </span>
                                </div>
                                <div>
                                    <span class="font-semibold text-gray-600 dark:text-gray-300">Days:</span>
                                    <span
                                        class="text-gray-800 dark:text-white ml-2">{{ $application->number_of_days }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Progress Steps -->
                        <div class="space-y-6">
                            <!-- Step 1: First Approver -->
                            <div class="flex items-start space-x-4">
                                <div class="flex flex-col items-center">
                                    <div
                                        class="w-12 h-12 rounded-full flex items-center justify-center 
                                @if ($progress['first_approved']) bg-green-100 border-2 border-green-500 text-green-600
                                @elseif($latestApproval && $latestApproval->first_approver)
                                    bg-blue-100 border-2 border-blue-500 text-blue-600
                                @else
                                    bg-gray-100 border-2 border-gray-300 text-gray-400 @endif">
                                        @if ($progress['first_approved'])
                                            <i class="bi bi-check-lg text-lg"></i>
                                        @else
                                            <i class="bi bi-1-circle-fill text-lg"></i>
                                        @endif
                                    </div>
                                    <div
                                        class="h-16 w-0.5 mt-2 
                                @if ($progress['first_approved']) bg-green-500
                                @elseif($latestApproval && $latestApproval->first_approver) bg-blue-500
                                @else bg-gray-300 @endif">
                                    </div>
                                </div>

                                <div class="flex-1">
                                    <div class="flex items-center justify-between mb-2">
                                        <h3 class="font-semibold text-gray-800 dark:text-white">First Approval</h3>
                                        @if ($progress['first_approved'])
                                            <span
                                                class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full font-medium">
                                                Approved
                                            </span>
                                        @elseif($latestApproval && $latestApproval->first_approver)
                                            <span
                                                class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full font-medium">
                                                Under Review
                                            </span>
                                        @else
                                            <span
                                                class="px-2 py-1 bg-gray-100 text-gray-600 text-xs rounded-full font-medium">
                                                Pending
                                            </span>
                                        @endif
                                    </div>

                                    @if ($latestApproval && $latestApproval->firstApproverUser)
                                        <div
                                            class="flex items-center space-x-3 p-3 bg-white dark:bg-gray-700 rounded-lg border 
                                    @if ($progress['first_approved']) border-green-200 dark:border-green-800
                                    @elseif($latestApproval->first_approver) border-blue-200 dark:border-blue-800 @endif">
                                            @if ($latestApproval->firstApproverUser->profile_photo_path)
                                                <img src="{{ route('profile-photo.file', ['filename' => basename($latestApproval->firstApproverUser->profile_photo_path)]) }}"
                                                    alt="{{ $latestApproval->firstApproverUser->name }}"
                                                    class="w-10 h-10 rounded-full object-cover border-2 
                                                @if ($progress['first_approved']) border-green-200
                                                @else border-blue-200 @endif">
                                            @else
                                                <div
                                                    class="w-10 h-10 rounded-full 
                                            @if ($progress['first_approved']) bg-green-500
                                            @else bg-blue-500 @endif flex items-center justify-center text-white font-bold text-sm">
                                                    {{ strtoupper(substr($latestApproval->firstApproverUser->name, 0, 1)) }}
                                                </div>
                                            @endif
                                            <div>
                                                <p class="font-medium text-gray-800 dark:text-white">
                                                    {{ $latestApproval->firstApproverUser->name }}
                                                </p>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                                    @if ($progress['first_approved'])
                                                        Approved - Stage {{ $progress['current_stage'] }}
                                                    @else
                                                        First Approver - Assigned
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                    @else
                                        <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg border border-dashed">
                                            <p class="text-gray-500 dark:text-gray-400 text-sm">Waiting for approver
                                                assignment</p>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Step 2: Second Approver -->
                            <div class="flex items-start space-x-4">
                                <div class="flex flex-col items-center">
                                    <div
                                        class="w-12 h-12 rounded-full flex items-center justify-center @if ($progress['second_approved']) bg-green-100 border-2 border-green-500 text-green-600 @elseif($progress['first_approved'] && $latestApproval && $latestApproval->second_approver) bg-blue-100 border-2 border-blue-500 text-blue-600 @else bg-gray-100 border-2 border-gray-300 text-gray-400 @endif">
                                        @if ($progress['second_approved'])
                                            <i class="bi bi-check-lg text-lg"></i>
                                        @else
                                            <i class="bi bi-2-circle-fill text-lg"></i>
                                        @endif
                                    </div>
                                    <div
                                        class="h-16 w-0.5 mt-2 @if ($progress['second_approved']) bg-green-500 @elseif($progress['first_approved'] && $latestApproval && $latestApproval->second_approver) bg-blue-500 @else bg-gray-300 @endif">
                                    </div>
                                </div>

                                <div class="flex-1">
                                    <div class="flex items-center justify-between mb-2">
                                        <h3 class="font-semibold text-gray-800 dark:text-white">Second Approval</h3>
                                        @if ($progress['second_approved'])
                                            <span
                                                class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full font-medium">
                                                Approved
                                            </span>
                                        @elseif($progress['first_approved'] && $latestApproval && $latestApproval->second_approver)
                                            <span
                                                class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full font-medium">
                                                Under Review
                                            </span>
                                        @else
                                            <span
                                                class="px-2 py-1 bg-gray-100 text-gray-600 text-xs rounded-full font-medium">
                                                {{ $progress['first_approved'] ? 'Ready' : 'Waiting' }}
                                            </span>
                                        @endif
                                    </div>

                                    @if ($latestApproval && $latestApproval->secondApproverUser)
                                        <div
                                            class="flex items-center space-x-3 p-3 bg-white dark:bg-gray-700 rounded-lg border
                                    @if ($progress['second_approved']) border-green-200 dark:border-green-800
                                    @elseif($progress['first_approved']) border-blue-200 dark:border-blue-800 @endif">
                                            @if ($latestApproval->secondApproverUser->profile_photo_path)
                                                <img src="{{ route('profile-photo.file', ['filename' => basename($latestApproval->secondApproverUser->profile_photo_path)]) }}"
                                                    alt="{{ $latestApproval->secondApproverUser->name }}"
                                                    class="w-10 h-10 rounded-full object-cover border-2 
                                                @if ($progress['second_approved']) border-green-200
                                                @else border-blue-200 @endif">
                                            @else
                                                <div
                                                    class="w-10 h-10 rounded-full 
                                            @if ($progress['second_approved']) bg-green-500
                                            @else bg-blue-500 @endif flex items-center justify-center text-white font-bold text-sm">
                                                    {{ strtoupper(substr($latestApproval->secondApproverUser->name, 0, 1)) }}
                                                </div>
                                            @endif
                                            <div>
                                                <p class="font-medium text-gray-800 dark:text-white">
                                                    {{ $latestApproval->secondApproverUser->name }}
                                                </p>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                                    @if ($progress['second_approved'])
                                                        Approved - Stage {{ $progress['current_stage'] }}
                                                    @else
                                                        Second Approver
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                    @else
                                        <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg border border-dashed">
                                            <p class="text-gray-500 dark:text-gray-400 text-sm">
                                                @if ($progress['first_approved'])
                                                    Ready for second approval
                                                @else
                                                    Waiting for first approval
                                                @endif
                                            </p>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Step 3: Third Approver -->
                            <div class="flex items-start space-x-4">
                                <div class="flex flex-col items-center">
                                    <div
                                        class="w-12 h-12 rounded-full flex items-center justify-center 
                                @if ($progress['third_approved']) bg-green-100 border-2 border-green-500 text-green-600
                                @elseif($progress['second_approved'] && $latestApproval && $latestApproval->third_approver)
                                    bg-blue-100 border-2 border-blue-500 text-blue-600
                                @else
                                    bg-gray-100 border-2 border-gray-300 text-gray-400 @endif">
                                        @if ($progress['third_approved'])
                                            <i class="bi bi-check-lg text-lg"></i>
                                        @else
                                            <i class="bi bi-3-circle-fill text-lg"></i>
                                        @endif
                                    </div>
                                </div>

                                <div class="flex-1">
                                    <div class="flex items-center justify-between mb-2">
                                        <h3 class="font-semibold text-gray-800 dark:text-white">Final Approval</h3>
                                        @if ($progress['third_approved'])
                                            <span
                                                class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full font-medium">
                                                Approved
                                            </span>
                                        @elseif($progress['second_approved'] && $latestApproval && $latestApproval->third_approver)
                                            <span
                                                class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full font-medium">
                                                Under Review
                                            </span>
                                        @else
                                            <span
                                                class="px-2 py-1 bg-gray-100 text-gray-600 text-xs rounded-full font-medium">
                                                {{ $progress['second_approved'] ? 'Ready' : 'Waiting' }}
                                            </span>
                                        @endif
                                    </div>

                                    @if ($latestApproval && $latestApproval->thirdApproverUser)
                                        <div
                                            class="flex items-center space-x-3 p-3 bg-white dark:bg-gray-700 rounded-lg border
                                    @if ($progress['third_approved']) border-green-200 dark:border-green-800 @elseif($progress['second_approved']) border-blue-200 dark:border-blue-800 @endif">
                                            @if ($latestApproval->thirdApproverUser->profile_photo_path)
                                                <img src="{{ route('profile-photo.file', ['filename' => basename($latestApproval->thirdApproverUser->profile_photo_path)]) }}"
                                                    alt="{{ $latestApproval->thirdApproverUser->name }}"
                                                    class="w-10 h-10 rounded-full object-cover border-2 
                                                @if ($progress['third_approved']) border-green-200
                                                @else border-blue-200 @endif">
                                            @else
                                                <div
                                                    class="w-10 h-10 rounded-full 
                                            @if ($progress['third_approved']) bg-green-500
                                            @else bg-blue-500 @endif flex items-center justify-center text-white font-bold text-sm">
                                                    {{ strtoupper(substr($latestApproval->thirdApproverUser->name, 0, 1)) }}
                                                </div>
                                            @endif
                                            <div>
                                                <p class="font-medium text-gray-800 dark:text-white">
                                                    {{ $latestApproval->thirdApproverUser->name }}
                                                </p>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                                    @if ($progress['third_approved'])
                                                        Final Approval Complete
                                                    @else
                                                        Final Approver
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                    @else
                                        <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg border border-dashed">
                                            <p class="text-gray-500 dark:text-gray-400 text-sm">
                                                @if ($progress['second_approved'])
                                                    Ready for final approval
                                                @else
                                                    Waiting for previous approvals
                                                @endif
                                            </p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div
                                class="w-16 h-16 mx-auto mb-4 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center">
                                <i class="bi bi-file-earmark-text text-2xl text-gray-400"></i>
                            </div>
                            <p class="text-gray-500 dark:text-gray-400">No application selected</p>
                        </div>
                    @endif
                </div>

                <!-- Footer -->
                <div class="bg-gray-50 dark:bg-gray-700 px-6 py-4 rounded-b-lg">
                    <div class="flex justify-end">
                        <button wire:click="$set('showingProgress', false)"
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors text-sm">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </x-modal>

        <!-- Cancel Confirmation Modal -->
        <x-modal id="cancelConfirmModal" maxWidth="md" centered wire:model="showCancelModal">
            <div class="p-6">
                <div class="text-center">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                        <i class="bi bi-exclamation-triangle text-red-600 text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
                        Cancel Leave Application
                    </h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                        Are you sure you want to cancel this leave application? Your leave credits will be restored.
                    </p>
                </div>

                <div class="flex justify-center space-x-3 mt-6">
                    <button wire:click="$set('showCancelModal', false)"
                        class="px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-white rounded-lg hover:bg-gray-400 dark:hover:bg-gray-500 transition">
                        No, Keep It
                    </button>
                    <button wire:click="confirmCancelApplication"
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition"
                        wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="confirmCancelApplication">Yes, Cancel Application</span>
                        <span wire:loading wire:target="confirmCancelApplication">
                            <i class="bi bi-arrow-repeat animate-spin"></i> Cancelling...
                        </span>
                    </button>
                </div>
            </div>
        </x-modal>

        <x-modal id="approverWarningModal" maxWidth="md" centered wire:model="showApproverWarning">
            <div class="p-6 bg-white dark:bg-gray-800 rounded-lg">
                <!-- Icon and Header -->
                <div class="text-center">
                    <div
                        class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-yellow-100 dark:bg-yellow-900 mb-4">
                        <i class="bi bi-exclamation-triangle text-yellow-600 dark:text-yellow-400 text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">
                        Leave Approvers Not Set
                    </h3>
                    <div
                        class="mt-4 p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
                        <p class="text-sm text-gray-700 dark:text-gray-300 mb-3">
                            The leave approval system requires all three approvers to be configured before you can
                            submit leave applications.
                        </p>
                        <p class="text-sm text-gray-700 dark:text-gray-300 font-semibold">
                            <i class="bi bi-info-circle text-blue-500 mr-1"></i>
                            Please advise the administrator to set up the approvers in the Leave Settings.
                        </p>
                    </div>
                </div>

                <!-- Missing Approvers Info -->
                <div class="mt-4 space-y-2">
                    @php
                        $approvers = \App\Models\LeaveApprover::where('is_active', true)
                            ->orderBy('approver_level')
                            ->get();

                        $firstApprover = $approvers->where('approver_level', 'first')->first();
                        $secondApprover = $approvers->where('approver_level', 'second')->first();
                        $thirdApprover = $approvers->where('approver_level', 'third')->first();
                    @endphp

                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        <p class="font-medium mb-2">Approver Status:</p>
                        <ul class="space-y-1 ml-4">
                            <li class="flex items-center">
                                @if ($firstApprover && $firstApprover->user_id)
                                    <i class="bi bi-check-circle-fill text-green-500 mr-2"></i>
                                    <span>First Approver: Set</span>
                                @else
                                    <i class="bi bi-x-circle-fill text-red-500 mr-2"></i>
                                    <span class="text-red-600 dark:text-red-400">First Approver: Not Set</span>
                                @endif
                            </li>
                            <li class="flex items-center">
                                @if ($secondApprover && $secondApprover->user_id)
                                    <i class="bi bi-check-circle-fill text-green-500 mr-2"></i>
                                    <span>Second Approver: Set</span>
                                @else
                                    <i class="bi bi-x-circle-fill text-red-500 mr-2"></i>
                                    <span class="text-red-600 dark:text-red-400">Second Approver: Not Set</span>
                                @endif
                            </li>
                            <li class="flex items-center">
                                @if ($thirdApprover && $thirdApprover->user_id)
                                    <i class="bi bi-check-circle-fill text-green-500 mr-2"></i>
                                    <span>Third Approver: Set</span>
                                @else
                                    <i class="bi bi-x-circle-fill text-red-500 mr-2"></i>
                                    <span class="text-red-600 dark:text-red-400">Third Approver: Not Set</span>
                                @endif
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Action Button -->
                <div class="flex justify-center mt-6">
                    <button wire:click="closeApproverWarning"
                        class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors text-sm font-medium">
                        <i class="bi bi-check-lg mr-1"></i>
                        I Understand
                    </button>
                </div>
            </div>
        </x-modal>
        {{-- Leave Application Form --}}
    @else
        <div class="p-4 w-full bg-white rounded-2xl sm:p-6 shadow dark:bg-gray-800 overflow-x-visible">
            <div
                class="bg-slate-800 rounded-t-lg dark:bg-gray-200 p-4 text-gray-50 dark:text-slate-900 font-bold flex justify-between items-center">
                <span>Basic Information</span>
                <div class="relative group">
                    <i class="bi bi-info-circle cursor-pointer"></i>
                    <div
                        class="absolute right-0 w-64 bg-gray-700 text-white text-sm p-3 rounded border border-slate-50 shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-opacity duration-300 z-50">
                        <strong>📌 Instructions:</strong><br><br>
                        <strong>Basic Information:</strong> This section is automatically filled with your
                        details.<br>
                        <strong>- If you don't have a salary yet, then advice or request to your HR or Admin to
                            give you an appointment.</strong><br><br>

                        <strong>Details of Application:</strong><br>
                        - In <strong>Part A</strong>, select one leave type.<br>
                        - In <strong>Part B</strong>, choose one option that applies to your leave. (If
                        applicable)<br>
                        - In <strong>Part C</strong>, select your leave dates. If you're sure, click "Add" to
                        confirm, or close it to make changes. The total days will be calculated
                        automatically. And if it's range then simply select the range of your leave.<br>
                        - In <strong>Part D</strong>, you must select one required option.<br><br>
                        <strong>Upload File:</strong> This step is optional. You may attach supporting documents
                        if needed.
                    </div>
                </div>
            </div>

            <div class="border p-4">
                <form>
                    <div class="gap-4">
                        <div class="gap-2 columns-1 w-full">
                            <label for="surname"
                                class="block text-sm font-medium text-gray-700 dark:text-slate-100">Fullname</label>
                            <input type="text" id="surname" wire:model='name' disabled
                                class="mt-1 p-2 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:text-gray-300 dark:bg-gray-700">
                        </div>

                        <div class="gap-2 lg:columns-2 sm:columns-1 mt-2">
                            <label for="office_or_department"
                                class="block text-sm font-medium text-gray-700 dark:text-slate-100">Office/Division</label>
                            <input type="text" id="office_or_department" wire:model="office_or_department"
                                disabled
                                class="mt-1 p-2 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:text-gray-300 dark:bg-gray-700">
                            @error('office_or_department')
                                <span class="text-red-500 text-sm">This field is
                                    required!</span>
                            @enderror

                            <label for="date_of_filing"
                                class="block text-sm font-medium text-gray-700 dark:text-slate-100">Date of
                                Filing</label>
                            <input type="date" id="date_of_filing" wire:model="date_of_filing" disabled
                                class="mt-1 p-2 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:text-gray-300 dark:bg-gray-700">
                        </div>

                        <div class="gap-2 lg:columns-2 sm:columns-1 mt-2">
                            <label for="position"
                                class="block text-sm font-medium text-gray-700 dark:text-slate-100">Position</label>
                            <input type="text" id="position" wire:model="position" disabled
                                class="mt-1 p-2 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:text-gray-300 dark:bg-gray-700">
                            @error('position')
                                <span class="text-red-500 text-sm">This field is required!</span>
                            @enderror
                            <div class="mb-4">
                                <!-- Label and error message row -->
                                <div class="flex items-center justify-start">
                                    <label for="salary"
                                        class="block text-sm font-medium text-gray-700 dark:text-slate-100">Salary</label>
                                    @if($salary === '₱0.00')
                                        <p class="text-xs text-yellow-600 dark:text-yellow-400 ml-2 italic">
                                            (No salary record found.)
                                        </p>
                                    @endif
                                </div>

                                <!-- Input field -->
                                <div class="mt-1 relative flex items-center">
                                    <span style="font-family: 'Arial', sans-serif; font-weight: bold;"
                                        class="absolute left-3">&#8369;</span>
                                    <input type="number" id="salary" wire:model="salary" disabled
                                        class="p-2 pl-8 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:text-gray-300 dark:bg-gray-700">
                                </div>
                            </div>
                        </div>

                    </div>
                </form>
            </div>

            {{-- Form fields --}}
            <div class="bg-gray-800 dark:bg-gray-200 p-4 text-gray-50 dark:text-slate-900 font-bold">
                Details of Application
            </div>

            <div class="border p-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- A. --}}
                <fieldset class="border border-gray-300 p-4 rounded-lg overflow-y-auto w-full h-104 mb-4 md:mb-0">
                    <legend class="text-gray-700 dark:text-slate-100">A. Type of Leave to be availed of
                    </legend>
                    @error('type_of_leave')
                        <span class="text-red-500 text-sm">Please choose one!</span>
                    @enderror

                    <div class="gap-2 columns-1">
                        <input type="radio" value="Vacation Leave" wire:model.live="type_of_leave">
                        <label class="text-md text-gray-700 dark:text-slate-100">Vacation Leave</label>
                    </div>
                    <div class="gap-2 columns-1">
                        <input type="radio" value="Mandatory/Forced Leave" wire:model.live="type_of_leave">
                        <label class="text-md text-gray-700 dark:text-slate-100">Mandatory/Forced Leave</label>
                    </div>
                    <div class="gap-2 columns-1">
                        <input type="radio" value="Sick Leave" wire:model.live="type_of_leave">
                        <label class="text-md text-gray-700 dark:text-slate-100">Sick Leave</label>
                    </div>
                    <div class="gap-2 columns-1">
                        <input type="radio" value="Maternity Leave" wire:model.live="type_of_leave">
                        <label class="text-md text-gray-700 dark:text-slate-100">Maternity Leave</label>
                    </div>
                    <div class="gap-2 columns-1">
                        <input type="radio" value="Paternity Leave" wire:model.live="type_of_leave">
                        <label class="text-md text-gray-700 dark:text-slate-100">Paternity Leave</label>
                    </div>
                    <div class="gap-2 columns-1">
                        <input type="radio" value="Special Privilege Leave" wire:model.live="type_of_leave">
                        <label class="text-md text-gray-700 dark:text-slate-100">Special Privilege
                            Leave</label>
                    </div>
                    <div class="gap-2 columns-1">
                        <input type="radio" value="Solo Parent Leave" wire:model.live="type_of_leave">
                        <label class="text-md text-gray-700 dark:text-slate-100">Solo Parent Leave</label>
                    </div>
                    <div class="gap-2 columns-1">
                        <input type="radio" value="Study Leave" wire:model.live="type_of_leave">
                        <label class="text-md text-gray-700 dark:text-slate-100">Study Leave</label>
                    </div>
                    <div class="gap-2 columns-1">
                        <input type="radio" value="10-Day VAWC Leave" wire:model.live="type_of_leave">
                        <label class="text-md text-gray-700 dark:text-slate-100">10-Day VAWC Leave</label>
                    </div>
                    <div class="gap-2 columns-1">
                        <input type="radio" value="Rehabilitation Privilege" wire:model.live="type_of_leave">
                        <label class="text-md text-gray-700 dark:text-slate-100">Rehabilitation
                            Privilege</label>
                    </div>
                    <div class="gap-2 columns-1">
                        <input type="radio" value="Special Leave Benefits for Women"
                            wire:model.live="type_of_leave">
                        <label class="text-md text-gray-700 dark:text-slate-100">Special Leave Benefits for
                            Women</label>
                    </div>
                    <div class="gap-2 columns-1">
                        <input type="radio" value="Special Emergency (Calamity) Leave"
                            wire:model.live="type_of_leave">
                        <label class="text-md text-gray-700 dark:text-slate-100">Special Emergency (Calamity)
                            Leave</label>
                    </div>
                    <div class="gap-2 columns-1">
                        <input type="radio" value="Adoption Leave" wire:model.live="type_of_leave">
                        <label class="text-md text-gray-700 dark:text-slate-100">Adoption Leave</label>
                    </div>

                    <div class="gap-2 columns-1">
                        <input type="radio" value="CTO Leave" wire:model.live="type_of_leave">
                        <label class="text-md text-gray-700 dark:text-slate-100">CTO Leave</label>
                    </div>

                    <div class="gap-2 columns-1">
                        <input type="radio" value="Others" wire:model.live="type_of_leave">
                        <label class="text-md text-gray-700 dark:text-slate-100">Others (Please
                            specify):</label>

                        @if ($type_of_leave === 'Others')
                            <input type="text" id="other_leave" wire:model="other_leave"
                                placeholder="Please specify"
                                class="mt-2 p-2 block shadow-sm text-sm sm:text-sm border-gray-300 rounded-md dark:text-gray-300 dark:bg-gray-700 w-full">
                        @endif
                    </div>
                </fieldset>

                {{-- B. --}}
                <fieldset class="border border-gray-300 p-4 rounded-lg w-full h-104 mb-4 md:mb-0 overflow-y-auto">
                    <legend class="text-gray-700 dark:text-slate-100">B. Details of Leave</legend>

                    @error('details_of_leave')
                        <span class="text-red-500 text-sm">Please choose one!</span>
                    @enderror

                    @if ($type_of_leave === 'Others')
                        <div
                            class="w-full p-3 bg-slate-100 rounded-lg shadow-sm dark:bg-gray-700 max-h-60 overflow-y-auto">
                            <h6 class="mb-3 text-sm font-medium text-gray-900 dark:text-white italic bg-red-400 pl-1">
                                Other purpose:
                            </h6>
                            <div class="gap-2 columns-1">
                                <input type="radio" class="ml-1" value="Monetization of Leave Credits"
                                    wire:model="details_of_leave">
                                <label class="text-md text-gray-700 dark:text-slate-100">Monetization of Leave
                                    Credits</label>
                            </div>
                            <div class="gap-2 columns-1 mt-4">
                                <input type="radio" class="ml-1" value="Terminal Leave"
                                    wire:model="details_of_leave">
                                <label class="text-md text-gray-700 dark:text-slate-100">Terminal Leave</label>
                            </div>
                        </div>
                    @endif

                    @if (
                        $type_of_leave === 'Vacation Leave' ||
                            $type_of_leave === 'Special Privilege Leave' ||
                            $type_of_leave === 'Mandatory/Forced Leave' ||
                            $type_of_leave === 'Solo Parent Leave' ||
                            $type_of_leave === 'Study Leave' ||
                            $type_of_leave === 'Maternity Leave' ||
                            $type_of_leave === 'Paternity Leave' ||
                            $type_of_leave === '10-Day VAWC Leave' ||
                            $type_of_leave === 'Rehabilitation Privilege' ||
                            $type_of_leave === 'Special Emergency (Calamity) Leave' ||
                            $type_of_leave === 'Adoption Leave' ||
                            $type_of_leave === 'CTO Leave')
                        <div
                            class="w-full p-3 bg-slate-100 rounded-lg shadow-sm dark:bg-gray-700 max-h-60 overflow-y-auto mt-4">
                            <h6 class="mb-3 text-sm font-medium text-gray-900 dark:text-white italic bg-red-400 pl-1">
                                In case of Vacation/Special Privilege Leave:</h6>
                            <div class="grid grid-cols-1 gap-4">
                                <div class="gap-2 columns-1">
                                    <input type="radio" class="ml-1" value="Within the Philippines"
                                        wire:model.live="details_of_leave">
                                    <label class="text-md text-gray-700 dark:text-slate-100">Within the
                                        Philippines</label>
                                    @if ($details_of_leave === 'Within the Philippines')
                                        <input type="text" id="within_the_ph" wire:model="philippines"
                                            placeholder="Please specify"
                                            class="mt-2 p-2 block shadow-sm text-sm sm:text-sm border-gray-300 rounded-md dark:text-gray-300 dark:bg-gray-700 w-full">
                                    @endif
                                </div>
                                <div class="gap-2 columns-1">
                                    <input type="radio" class="ml-1" value="Abroad"
                                        wire:model.live="details_of_leave">
                                    <label class="text-md text-gray-700 dark:text-slate-100">Abroad
                                        (Specify)</label>
                                    @if ($details_of_leave === 'Abroad')
                                        <input type="text" id="abroad_value" wire:model="abroad"
                                            placeholder="Please specify"
                                            class="mt-2 p-2 block shadow-sm text-sm sm:text-sm border-gray-300 rounded-md dark:text-gray-300 dark:bg-gray-700 w-full">
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                    @if ($type_of_leave === 'Sick Leave')
                        <div
                            class="w-full p-3 bg-slate-100 rounded-lg shadow-sm dark:bg-gray-700 max-h-60 overflow-y-auto mt-4">
                            <h6 class="mb-3 text-sm font-medium text-gray-900 dark:text-white italic bg-red-400 pl-1">
                                In
                                case of Sick Leave:</h6>
                            <div class="grid grid-cols-1 gap-4">
                                <div class="gap-2 columns-1">
                                    <input type="radio" class="ml-1" value="In Hospital"
                                        wire:model.live="details_of_leave">
                                    <label class="text-md text-gray-700 dark:text-slate-100">In Hospital
                                        (Special Illness)</label>
                                    @if ($details_of_leave === 'In Hospital')
                                        <input type="text" id="in_hospital" wire:model="inHospital"
                                            placeholder="Please specify"
                                            class="mt-2 p-2 block shadow-sm text-sm sm:text-sm border-gray-300 rounded-md dark:text-gray-300 dark:bg-gray-700 w-full">
                                    @endif
                                </div>
                                <div class="gap-2 columns-1">
                                    <input type="radio" class="ml-1" value="Out Patient"
                                        wire:model.live="details_of_leave">
                                    <label class="text-md text-gray-700 dark:text-slate-100">Out Patient
                                        (Special Illness)</label>
                                    @if ($details_of_leave === 'Out Patient')
                                        <input type="text" id="out_patient" wire:model="outPatient"
                                            placeholder="Please specify"
                                            class="mt-2 p-2 block shadow-sm text-sm sm:text-sm border-gray-300 rounded-md dark:text-gray-300 dark:bg-gray-700 w-full">
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                    @if ($type_of_leave === 'Special Leave Benefits for Women')
                        <div
                            class="w-full p-3 bg-slate-100 rounded-lg shadow-sm dark:bg-gray-700 max-h-60 overflow-y-auto mt-4">
                            <h6 class="mb-3 text-sm font-medium text-gray-900 dark:text-white italic bg-red-400 pl-1">
                                In case of Special Leave Benefits for Women:</h6>
                            <div class="gap-2 columns-1">
                                <input type="radio" class="ml-1" value="Women Special Illness"
                                    wire:model.live="details_of_leave">
                                <label class="text-md text-gray-700 dark:text-slate-100">(Special
                                    Illness)</label>
                                @if ($details_of_leave === 'Women Special Illness')
                                    <input type="text" id="women_leave" wire:model="specialIllnessForWomen"
                                        placeholder="Please specify"
                                        class="mt-2 p-2 block shadow-sm text-sm sm:text-sm border-gray-300 rounded-md dark:text-gray-300 dark:bg-gray-700 w-full">
                                @endif
                            </div>
                        </div>
                    @endif

                    @if ($type_of_leave === 'Study Leave')
                        <div
                            class="w-full p-3 bg-slate-100 rounded-lg shadow-sm dark:bg-gray-700 max-h-60 overflow-y-auto mt-4">
                            <h6 class="mb-3 text-sm font-medium text-gray-900 dark:text-white italic bg-red-400 pl-1">
                                In case of Study Leave:</h6>
                            <div class="gap-2 columns-1">
                                <input type="radio" class="ml-1" value="Completion of Masters Degree"
                                    wire:model="details_of_leave">
                                <label class="text-md text-gray-700 dark:text-slate-100">Completion of Master's
                                    Degree</label>
                            </div>
                            <div class="gap-2 columns-1 mt-4">
                                <input type="radio" class="ml-1" value="BAR/Board Examination Review"
                                    wire:model="details_of_leave">
                                <label class="text-md text-gray-700 dark:text-slate-100">BAR/Board Examination
                                    Review</label>
                            </div>
                        </div>
                    @endif
                </fieldset>

                {{-- C. Number of Working Days Applied for --}}
                {{-- <fieldset class="border border-gray-300 p-4 rounded-lg overflow-hidden w-full h-full mb-4 md:mb-0">
                    <legend class="text-gray-700 dark:text-slate-100">C. Number of Working Days Applied for</legend>

                    <div class="w-full p-3 bg-slate-100 rounded-lg shadow-sm dark:bg-gray-700">
                        <div class="gap-2 columns-1">
                            <label class="text-sm text-gray-700 dark:text-slate-100">Days</label>
                            <input type="number" id="number_of_days" wire:model="number_of_days" disabled
                                class="mt-1 p-2 block shadow-sm text-sm sm:text-sm border-gray-300 rounded-md dark:text-gray-300 dark:bg-gray-700 w-full">
                            @error('number_of_days')
                                <span class="text-red-500 text-sm">This field is required!</span>
                            @enderror
                        </div>

                        @if (in_array($type_of_leave, [
                                'Vacation Leave',
                                'Mandatory/Forced Leave',
                                'Sick Leave',
                                'Paternity Leave',
                                'Special Privilege Leave',
                                'Solo Parent Leave',
                                '10-Day VAWC Leave',
                                'Special Emergency (Calamity) Leave',
                                'Adoption Leave',
                                'CTO Leave',
                                'Others',
                            ]))
                            <div class="mb-4 mt-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-slate-100">List of
                                    Dates</label>
                                <div class="mt-1 flex">
                                    <input type="date" wire:model="new_date"
                                        class="block w-full rounded-l-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm sm:text-sm">
                                    <button wire:click="addDate"
                                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-r-md text-white bg-green-600 hover:bg-green-700 focus:outline-none">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4v16m8-8H4" />
                                        </svg>
                                    </button>
                                </div>
                                @error('list_of_dates')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                        @endif

                        @if (in_array($type_of_leave, [
                                'Maternity Leave',
                                'Study Leave',
                                'Rehabilitation Privilege',
                                'Special Leave Benefits for Women',
                            ]))
                            <fieldset
                                class="border border-red-400 p-4 rounded-lg overflow-hidden w-full h-full mb-4 md:mb-0 mt-2">
                                <div class="gap-2 columns-1">
                                    <h6
                                        class="mt-2 mb-2 text-sm font-medium text-gray-900 dark:text-white italic bg-red-400 pl-1">
                                        In case of Study Leave, Maternity Leave, Special Leave Benefits for Women, and
                                        Rehabilitation Leave:</h6>

                                    <div class="gap-2 columns-1 mt-2">
                                        <label
                                            class="block text-sm font-medium text-gray-700 dark:text-slate-100">Start
                                            date</label>
                                        <input type="date" id="start_date" wire:model.live="start_date"
                                            class="mt-1 p-2 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:text-gray-300 dark:bg-gray-700">
                                    </div>

                                    <div class="gap-2 columns-1 mt-2">
                                        <label
                                            class="block text-sm font-medium text-gray-700 dark:text-slate-100 mt-2">End
                                            date</label>
                                        <input type="date" id="end_date" wire:model.live="end_date"
                                            class="mt-1 p-2 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:text-gray-300 dark:bg-gray-700">
                                    </div>
                                </div>
                            </fieldset>
                        @endif

                        <div class="gap-2 columns-1 mt-2">
                            <ul>
                                @foreach ($list_of_dates as $index => $date)
                                    <li class="dark:text-slate-50 text-slate-900 flex items-center">
                                        <i class="bi bi-check-lg pr-4 text-green-600"></i>{{ $date }}
                                        <button wire:click="removeDate({{ $index }})"
                                            class="ml-4 text-red-600">
                                            <i class="bi bi-x"></i>
                                        </button>
                                    </li>
                                @endforeach
                            </ul>

                            @error('new_date')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </fieldset> --}}
                {{-- C. Number of Working Days Applied for --}}
                <fieldset class="border border-gray-300 p-4 rounded-lg overflow-hidden w-full h-full mb-4 md:mb-0">
                    <legend class="text-gray-700 dark:text-slate-100">C. Number of Working Days Applied for</legend>

                    <div class="w-full p-3 bg-slate-100 rounded-lg shadow-sm dark:bg-gray-700">
                        <div class="gap-2 columns-1">
                            <label class="text-sm text-gray-700 dark:text-slate-100">Days</label>
                            <input type="number" id="number_of_days" wire:model="number_of_days" disabled
                                class="mt-1 p-2 block shadow-sm text-sm sm:text-sm border-gray-300 rounded-md dark:text-gray-300 dark:bg-gray-700 w-full">
                            @error('number_of_days')
                                <span class="text-red-500 text-sm">This field is required!</span>
                            @enderror
                        </div>

                        {{-- Date Mode Toggle --}}
                        @if ($type_of_leave)
                            <div class="mt-3 mb-3 flex rounded-md overflow-hidden border border-gray-300 dark:border-gray-600 w-full">
                                <button type="button"
                                    wire:click="setDateMode('single')"
                                    class="flex-1 py-2 text-sm font-medium transition-colors duration-150
                                        {{ $dateMode === 'single'
                                            ? 'bg-blue-600 text-white'
                                            : 'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600' }}">
                                    Single / Multiple Dates
                                </button>
                                <button type="button"
                                    wire:click="setDateMode('range')"
                                    class="flex-1 py-2 text-sm font-medium transition-colors duration-150
                                        {{ $dateMode === 'range'
                                            ? 'bg-blue-600 text-white'
                                            : 'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600' }}">
                                    Date Range
                                </button>
                            </div>
                        @endif

                        {{-- Single / Multiple Date Picker --}}
                        @if ($type_of_leave && $dateMode === 'single')
                            <div class="mb-4 mt-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-slate-100">Select Date</label>
                                <div class="mt-1 flex">
                                    <input type="date" wire:model="new_date"
                                        class="block w-full rounded-l-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm sm:text-sm">
                                    <button wire:click="addDate"
                                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-r-md text-white bg-green-600 hover:bg-green-700 focus:outline-none">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                        </svg>
                                    </button>
                                </div>
                                @error('list_of_dates')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                                @error('new_date')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                        @endif

                        {{-- Date Range Picker --}}
                        @if ($type_of_leave && $dateMode === 'range')
                            <div class="mt-3">
                                <div class="gap-2 columns-1 mt-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-100">Start Date</label>
                                    <input type="date" id="start_date" wire:model.live="start_date"
                                        class="mt-1 p-2 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:text-gray-300 dark:bg-gray-700">
                                    @error('start_date')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="gap-2 columns-1 mt-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-100 mt-2">End Date</label>
                                    <input type="date" id="end_date" wire:model.live="end_date"
                                        class="mt-1 p-2 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:text-gray-300 dark:bg-gray-700">
                                    @error('end_date')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        @endif

                        {{-- Selected Dates List --}}
                        @if (!empty($list_of_dates))
                            <div class="gap-2 columns-1 mt-3">
                                <ul>
                                    @foreach ($list_of_dates as $index => $date)
                                        <li class="dark:text-slate-50 text-slate-900 flex items-center">
                                            <i class="bi bi-check-lg pr-4 text-green-600"></i>{{ $date }}
                                            @if ($dateMode === 'single')
                                                <button wire:click="removeDate({{ $index }})" class="ml-4 text-red-600">
                                                    <i class="bi bi-x"></i>
                                                </button>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                </fieldset>

                {{-- D. --}}
                <fieldset class="border border-gray-300 p-4 rounded-lg overflow-hidden w-full h-full mb-4 md:mb-0">
                    <legend class="text-gray-700 dark:text-slate-100">D. Commutation</legend>
                    <div class="gap-2 columns-1">
                        <input type="radio" value="Requested" wire:model.live="commutation">
                        <label class="text-md text-gray-700 dark:text-slate-100">Requested</label>
                    </div>
                    <div class="gap-2 columns-1 mt-4">
                        <input type="radio" value="Not Requested" wire:model.live="commutation">
                        <label class="text-md text-gray-700 dark:text-slate-100">Not Requested</label>
                    </div>
                    @error('commutation')
                        <span class="text-red-500 text-sm">Please choose one!</span>
                    @enderror
                </fieldset>

                <!-- File upload section -->
                <div class="flex flex-col items-center justify-center w-full col-span-1 md:col-span-2 mt-4 md:mt-0">
                    <label for="dropzone-file"
                        class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:hover:bg-gray-800 dark:bg-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500">
                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                            <i class="bi bi-cloud-arrow-up" style="font-size: 2rem;"></i>
                            <p class="mb-2 text-sm text-gray-500 dark:text-gray-400"><span class="font-semibold">Click
                                    to upload</span></p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">JPEG, JPG, PNG or PDF</p>
                        </div>
                        <input id="dropzone-file" type="file" wire:model="files" multiple class="hidden" />
                    </label>

                    <!-- Display selected files -->
                    @if ($files)
                        <div class="mt-4">
                            <ul class="list-disc list-inside">
                                @foreach ($files as $index => $file)
                                    <li class="flex items-center text-sm text-gray-700 dark:text-gray-300">
                                        {{ $file->getClientOriginalName() }}
                                        <button type="button" wire:click="removeFile({{ $index }})"
                                            class="ml-2 text-red-500">
                                            &times;
                                        </button>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    @error('files')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                    @error('files.*')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>

            </div>

            <div class="bg-gray-800 dark:bg-gray-200 p-2 text-white flex justify-center rounded-b-lg border">
                <button wire:click="submitLeaveApplication" role="status"
                    class="btn bg-emerald-200 dark:bg-emerald-500 hover:bg-emerald-600 text-gray-800 dark:text-white whitespace-nowrap mx-2">
                    Submit
                </button>
                <button wire:click="closeLeaveForm" class="mr-2 bg-gray-500 text-white px-4 py-2 rounded mx-2">
                    Close
                </button>
            </div>
        </div>
    @endif
</div>