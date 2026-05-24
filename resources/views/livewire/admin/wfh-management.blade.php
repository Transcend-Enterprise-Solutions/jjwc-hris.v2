<div class="w-full flex flex-col justify-center"
x-data="{ 
    selectedTab: '{{ request()->query('tab', 'employees') }}',
}" 
x-cloak>

    <div id="wfh-details"></div>

    <style>
        #map {
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            transition: all 0.3s ease;
        }
        
        #map:hover {
            box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
        }

        .scrollbar-thin1::-webkit-scrollbar {
                        width: 5px;
                    }

        .scrollbar-thin1::-webkit-scrollbar-thumb {
            background-color: #1a1a1a4b;
            border-radius: 0 50px 50px 0;
        }

        .scrollbar-thin1::-webkit-scrollbar-track {
            background-color: #ffffff23;
            border-radius: 0 50px 50px 0;
        }

        @media (max-width: 1024px){
            .custom-d{
                display: block;
            }
        }

        @media (max-width: 768px){
            .m-scrollable{
                width: 100%;
                overflow-x: scroll;
            }
        }

        @media (min-width:1024px){
            .custom-p{
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

        .spinner-border-2 {
            display: inline-block;
            width: 1rem;
            height: 1rem;
            vertical-align: text-bottom;
            border: 2px solid currentColor;
            border-right-color: transparent;
            border-radius: 50%;
            -webkit-animation: spinner-border .75s linear infinite;
            animation: spinner-border .75s linear infinite;
            color: rgb(255, 255, 255);
        }
    </style>

    <div class="flex justify-center w-full">
        <div class="w-full bg-white rounded-2xl p-3 sm:p-6 shadow dark:bg-gray-800 overflow-x-visible">

            <div class="pb-4 mb-3 pt-4 sm:pt-0">
                <h1 class="text-lg font-bold text-center text-slate-800 dark:text-white">Work From Home Managememnt</h1>
            </div>

            <div class="flex-col justify-center w-full bg-gray-200 dark:bg-slate-700 border border-gray-300 dark:border-gray-800 mb-2" style="border-radius: 8px;">
                {{-- <div wire:ignore class="w-full">
                    <div id="map" style="height: 250px; width: 100%; border-radius: 8px 8px 0 0; margin: 0;"></div>
                </div> --}}

                <div class="text-sm grid grid-cols-2 mt-2 px-4 mb-2">
                    <div class="col-span-2 sm:col-span-1">
                        Employee: <span class="text-gray-800 dark:text-gray-50">{{ $employeeName ?? '...' }}</span><br>
                        Address: <span class="text-gray-800 dark:text-gray-50">{{ $address ?? '...' }}</span>
                    </div>
                    <div class="col-span-2 sm:col-span-1">
                        <span class="{{ $approveOnly ? 'hidden' : '' }}">
                            Approved By: <span class="text-gray-800 dark:text-gray-50">{{ $approvedBy ?? '...' }}</span><br>
                            Date Approved: <span class="text-gray-800 dark:text-gray-50">{{ $approvedDate ?? '...' }}</span>
                        </span>
                        <span class="{{ $approveOnly ? '' : 'hidden' }}">
                            Disapproved By: <span class="text-gray-800 dark:text-gray-50">{{ $disapprovedBy ?? '...' }}</span><br>
                            Date Disapproved: <span class="text-gray-800 dark:text-gray-50">{{ $disapprovedDate ?? '...' }}</span>
                        </span>
                    </div>
                </div>

                @if($thisWFHLocId)
                    <div class="relative mt-1 flex gap-4 w-full mb-4 justify-center items-center">
                        <button wire:click="toogleConfirmModal({{ $thisWFHLocId }}, 'approve')" 
                            class="px-3 py-1 text-white rounded-md 
                            text-sm bg-green-500 hover:bg-green-600  
                            focus:outline-none" title="Approve">
                            Approve
                        </button>
                        <button wire:click="toogleConfirmModal({{ $thisWFHLocId }}, 'disapprove')" 
                            class="px-3 py-1 text-white rounded-md 
                            text-sm bg-red-500 hover:bg-red-600  
                            focus:outline-none" title="Disapprove">
                            Disapprove
                        </button>
                        <button wire:click="resetVariables" 
                            class="px-3 py-1 text-white rounded-md 
                            text-sm bg-gray-500 hover:bg-gray-600  
                            focus:outline-none" title="Cancel">
                            Cancel
                        </button>
                    </div>
                @endif

            </div>

            <div class="w-full sm:w-1/3 sm:mr-4 mb-4" x-show="selectedTab === 'employees'">
                <label for="search" class="block text-sm font-medium text-gray-700 dark:text-slate-400 mb-1">Search</label>
                <input type="text" id="search" wire:model.live="search"
                    class="px-2 py-1.5 block w-full shadow-sm sm:text-sm border border-gray-400 hover:bg-gray-300 rounded-md
                        dark:hover:bg-slate-600 dark:border-slate-600
                        dark:text-gray-300 dark:bg-gray-800"
                    placeholder="Enter employee name or ID">
            </div>

            <div class="w-full sm:w-1/3 sm:mr-4 mb-4" x-show="selectedTab === 'requests'">
                <label for="search2" class="block text-sm font-medium text-gray-700 dark:text-slate-400 mb-1">Search</label>
                <input type="text" id="search2" wire:model.live="search2"
                    class="px-2 py-1.5 block w-full shadow-sm sm:text-sm border border-gray-400 hover:bg-gray-300 rounded-md
                        dark:hover:bg-slate-600 dark:border-slate-600
                        dark:text-gray-300 dark:bg-gray-800"
                    placeholder="Enter employee name or ID">
            </div>

            <div class="w-full sm:w-1/3 sm:mr-4 mb-4" x-show="selectedTab === 'history'">
                <label for="search3" class="block text-sm font-medium text-gray-700 dark:text-slate-400 mb-1">Search</label>
                <input type="text" id="search3" wire:model.live="search3"
                    class="px-2 py-1.5 block w-full shadow-sm sm:text-sm border border-gray-400 hover:bg-gray-300 rounded-md
                        dark:hover:bg-slate-600 dark:border-slate-600
                        dark:text-gray-300 dark:bg-gray-800"
                    placeholder="Enter employee name or ID">
            </div>

             <!-- Table -->
             <div class="flex flex-col">
                <div class="flex gap-2 overflow-x-auto -mb-2">
                    <button @click="selectedTab = 'employees'" 
                            :class="{ 'font-bold dark:text-gray-300 dark:bg-gray-700 bg-gray-200 rounded-t-lg': selectedTab === 'employees', 'text-slate-700 font-medium dark:text-slate-300 dark:hover:text-white hover:text-black': selectedTab !== 'employees' }" 
                            class="h-min px-4 pt-2 pb-4 text-sm no-wrap">
                        Employees
                    </button>
                    <button @click="selectedTab = 'requests'" 
                            :class="{ 'font-bold dark:text-gray-300 dark:bg-gray-700 bg-gray-200 rounded-t-lg': selectedTab === 'requests', 'text-slate-700 font-medium dark:text-slate-300 dark:hover:text-white hover:text-black': selectedTab !== 'requests' }" 
                            class="h-min px-4 pt-2 pb-4 text-sm">
                        Location Request
                    </button>
                    <button @click="selectedTab = 'history'" 
                            :class="{ 'font-bold dark:text-gray-300 dark:bg-gray-700 bg-gray-200 rounded-t-lg': selectedTab === 'history', 'text-slate-700 font-medium dark:text-slate-300 dark:hover:text-white hover:text-black': selectedTab !== 'history' }" 
                            class="h-min px-4 pt-2 pb-4 text-sm">
                        Request History
                    </button>
                </div>
                <div class="-my-2 overflow-x-auto">
                    <div class="inline-block w-full py-2 align-middle">
                        <div class="overflow-hidden border dark:border-gray-700 rounded-lg">
                            <div x-show="selectedTab === 'employees'">
                                <div class="overflow-x-auto">
                                    <table class="w-full min-w-full">
                                        <thead class="bg-gray-200 dark:bg-gray-700 rounded-xl">
                                            <tr class="whitespace-nowrap">
                                                <th scope="col" class="px-5 py-3 text-left text-sm font-medium uppercase">
                                                    Name
                                                </th>
                                                <th scope="col" class="px-5 py-3 text-center text-sm font-medium uppercase">
                                                    Employee No.
                                                </th>
                                                <th scope="col" class="px-5 py-3 text-center text-sm font-medium uppercase">
                                                    Address
                                                </th>
                                                <th scope="col" class="px-5 py-3 text-center text-sm font-medium uppercase">
                                                    Geolocation
                                                </th>
                                                <th class="px-5 py-3 text-gray-100 text-sm font-medium text-center uppercase sticky right-0 z-10 bg-gray-600 dark:bg-gray-600">
                                                    Action
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-neutral-200 dark:divide-gray-400">
                                                @foreach($employees as $employee)
                                                    <tr class="text-neutral-800 dark:text-neutral-200">
                                                        <td class="px-5 py-4 text-left text-sm font-medium whitespace-nowrap">
                                                            {{ $employee->surname }}, {{ $employee->first_name }} {{ $employee->middle_name ?? '' }} {{ $employee->name_extension ?? '' }}
                                                        </td>
                                                        <td class="px-5 py-4 text-center text-sm font-medium whitespace-nowrap">
                                                            @if($employee->appointment === 'plantilla')
                                                                {{ $employee->emp_code }}
                                                            @else
                                                                {{ $employee->emp_code ? 'D-' . substr($employee->emp_code, 1) : '' }}
                                                            @endif
                                                        </td>
                                                        <td class="px-5 py-4 text-center text-sm font-medium whitespace-nowrap">
                                                            {{ $employee->address }}
                                                        </td>
                                                        <td class="px-5 py-4 text-center text-sm font-medium whitespace-nowrap">
                                                            Lat: {{ $employee->curr_lat ?? 'None' }} <br>
                                                            Lng: {{ $employee->curr_lng ?? 'None' }}
                                                        </td>
                                                        <td class="px-5 py-4 text-sm font-medium text-center whitespace-nowrap sticky right-0 z-10 bg-white dark:bg-gray-800">
                                                            <a href="#wfh-details">
                                                                <div class="relative">
                                                                    <button wire:click="viewPreviousEmployeeLocation({{ $employee->id }})" 
                                                                        class="peer inline-flex items-center justify-center px-4 py-2 -m-5 
                                                                        -mr-2 text-sm font-medium tracking-wide focus:outline-none text-blue-500" title="View">
                                                                        <i class="bi bi-eye-fill"></i>
                                                                    </button>
                                                                </div>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                        </tbody>
                                    </table>
                                    @if ($employees->isEmpty())
                                        <div class="p-4 text-center text-gray-500 dark:text-gray-300">
                                            No records!
                                        </div> 
                                    @endif
                                </div>
                                <div class="p-5 text-neutral-500 dark:text-neutral-200 bg-gray-200 dark:bg-gray-700">
                                    {{ $employees->links() }}
                                </div>
                             </div>
                            <div x-show="selectedTab === 'requests'">
                                <div class="overflow-x-auto">
                                    <table class="w-full min-w-full">
                                        <thead class="bg-gray-200 dark:bg-gray-700 rounded-xl">
                                            <tr class="whitespace-nowrap">
                                                <th scope="col" class="px-5 py-3 text-left text-sm font-medium uppercase">
                                                    Name
                                                </th>
                                                <th scope="col" class="px-5 py-3 text-center text-sm font-medium uppercase">
                                                    Address
                                                </th>
                                                <th scope="col" class="px-5 py-3 text-center text-sm font-medium uppercase">
                                                    Geolocation
                                                </th>
                                                <th scope="col" class="px-5 py-3 text-center text-sm font-medium uppercase">
                                                    Request Attachment
                                                </th>
                                                <th scope="col" class="px-5 py-3 text-center text-sm font-medium uppercase">
                                                    Status
                                                </th>
                                                <th class="px-5 py-3 text-gray-100 text-sm font-medium text-center uppercase sticky right-0 z-10 bg-gray-600 dark:bg-gray-600">
                                                    Action
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-neutral-200 dark:divide-gray-400">
                                                @foreach($locRequesters as $employee)
                                                    <tr class="text-neutral-800 dark:text-neutral-200">
                                                        <td class="px-5 py-4 text-left text-sm font-medium whitespace-nowrap">
                                                            {{ $employee->surname }}, {{ $employee->first_name }} {{ $employee->middle_name ?? '' }} {{ $employee->name_extension ?? '' }}<br>
                                                            @if($employee->appointment === 'plantilla')
                                                                {{ $employee->emp_code }}
                                                            @else
                                                                {{ $employee->emp_code ? 'D-' . substr($employee->emp_code, 1) : '' }}
                                                            @endif
                                                        </td>
                                                        <td class="px-5 py-4 text-center text-sm font-medium text-nowrap">
                                                            {{ $employee->address ?? 'None' }}
                                                        </td>
                                                        <td class="px-5 py-4 text-center text-sm font-medium whitespace-nowrap">
                                                            Lat: {{ $employee->curr_lat ?? 'None' }} <br>
                                                            Lng: {{ $employee->curr_lng ?? 'None' }}
                                                        </td>
                                                        <td class="px-5 py-4 text-center text-sm font-medium whitespace-nowrap {{ $employee->attachment ? '' : 'opacity-30' }}">
                                                            {{ $employee->attachment ?? 'None' }}
                                                        </td>
                                                        <td class="px-5 py-4 text-center text-sm font-medium whitespace-nowrap">
                                                            @if($employee->status)
                                                            <span
                                                                class="text-xs text-white bg-green-500 rounded-lg py-1.5 px-4">Approved</span>
                                                            @else
                                                            <span
                                                                class="text-xs text-white bg-orange-500 rounded-lg py-1.5 px-4">Pending</span>
                                                            @endif
                                                        </td>
                                                        <td class="px-5 py-4 text-sm font-medium text-center whitespace-nowrap sticky right-0 z-10 bg-white dark:bg-gray-800">
                                                            @if($employee->status)
                                                                <a href="#wfh-details">
                                                                    <div class="relative">
                                                                        @php
                                                                            $thisName = trim($employee->surname . ', ' . $employee->first_name . ' ' . 
                                                                                ($employee->middle_name ? $employee->middle_name . ' ' : '') . 
                                                                                ($employee->name_extension ?? ''));
                                                                        @endphp
                                                                        <button wire:click="viewPreviousEmployeeLocation({{ $employee->id }})" 
                                                                            class="peer inline-flex items-center justify-center px-4 py-2 -m-5 
                                                                            -mr-2 text-sm font-medium tracking-wide text-blue-500 hover:text-blue-600
                                                                            focus:outline-none" title="View">
                                                                            <i class="bi bi-eye-fill"></i>
                                                                        </button>
                                                                    </div>
                                                                </a>
                                                            @else
                                                                <div class="relative">
                                                                    <a href="#wfh-details">
                                                                        <button wire:click="viewPreviousEmployeeLocation({{ $employee->id }})" 
                                                                            class="peer inline-flex items-center justify-center px-4 py-2 -m-5 
                                                                            -mr-2 text-sm font-medium tracking-wide text-blue-500 hover:text-blue-600
                                                                            focus:outline-none" title="View">
                                                                            <i class="bi bi-eye-fill"></i>
                                                                        </button>
                                                                    </a>
                                                                </div>
                                                                <div class="relative">
                                                                    <button wire:click="toogleConfirmModal({{ $employee->user_id }}, 'approve')" 
                                                                        class="peer inline-flex items-center justify-center px-4 py-2 -m-5 
                                                                        -mr-2 text-sm font-medium tracking-wide text-green-500 hover:text-green-600  
                                                                        focus:outline-none" title="Approve">
                                                                        <i class="bi bi-check-square"></i>
                                                                    </button>
                                                                </div>
                                                                <div class="relative">
                                                                    <button wire:click="toogleConfirmModal({{ $employee->user_id }}, 'disapprove')" 
                                                                        class="peer inline-flex items-center justify-center px-4 py-2 -m-5 
                                                                        -mr-2 text-sm font-medium tracking-wide text-red-500 hover:text-red-600  
                                                                        focus:outline-none" title="Disapprove">
                                                                        <i class="bi bi-x-square"></i>
                                                                    </button>
                                                                </div>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                        </tbody>
                                    </table>
                                    @if ($locRequesters->isEmpty())
                                        <div class="p-4 text-center text-gray-500 dark:text-gray-300">
                                            No requests!
                                        </div> 
                                    @endif
                                </div>
                                <div class="p-5 text-neutral-500 dark:text-neutral-200 bg-gray-200 dark:bg-gray-700">
                                    {{ $locRequesters->links() }}
                                </div>
                             </div>
                             <div x-show="selectedTab === 'history'">
                                <div class="overflow-x-auto">
                                    <table class="w-full min-w-full">
                                        <thead class="bg-gray-200 dark:bg-gray-700 rounded-xl">
                                            <tr class="whitespace-nowrap">
                                                <th scope="col" class="px-5 py-3 text-left text-sm font-medium uppercase">
                                                    Date
                                                </th>
                                                <th scope="col" class="px-5 py-3 text-center text-sm font-medium uppercase">
                                                    Status
                                                </th>
                                                <th scope="col" class="px-5 py-3 text-left text-sm font-medium uppercase">
                                                    Name
                                                </th>
                                                <th scope="col" class="px-5 py-3 text-center text-sm font-medium uppercase">
                                                    Address
                                                </th>
                                                <th scope="col" class="px-5 py-3 text-center text-sm font-medium uppercase">
                                                    Geoocation
                                                </th>
                                                <th scope="col" class="px-5 py-3 text-center text-sm font-medium uppercase">
                                                    Request Attachment
                                                </th>
                                                <th class="px-5 py-3 text-gray-100 text-sm font-medium text-center uppercase sticky right-0 z-10 bg-gray-600 dark:bg-gray-600">
                                                    Action
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-neutral-200 dark:divide-gray-400">
                                                @foreach($history as $employee)
                                                    <tr class="text-neutral-800 dark:text-neutral-200">
                                                        <td class="px-5 py-4 text-left text-sm font-medium text-nowrap">
                                                            {{ \Carbon\Carbon::parse($employee->date_approved)->format('F d, Y') }}
                                                        </td>
                                                        <td class="px-5 py-4 text-center text-sm font-medium text-nowrap">
                                                            @if($employee->status == 1)
                                                                <span
                                                                    class="text-xs text-white bg-green-500 rounded-lg py-1.5 px-4">Approved</span>
                                                            @elseif($employee->status == 2)
                                                                <span
                                                                class="text-xs text-white bg-red-500 rounded-lg py-1.5 px-4">Disapproved</span>
                                                            @endif
                                                        </td>
                                                        <td class="px-5 py-4 text-left text-sm font-medium whitespace-nowrap">
                                                            {{ $employee->surname }}, {{ $employee->first_name }} {{ $employee->middle_name ?? '' }} {{ $employee->name_extension ?? '' }}<br>
                                                            @if($employee->appointment === 'plantilla')
                                                                {{ $employee->emp_code }}
                                                            @else
                                                                {{ $employee->emp_code ? 'D-' . substr($employee->emp_code, 1) : '' }}
                                                            @endif
                                                        </td>
                                                        <td class="px-5 py-4 text-center text-sm font-medium text-nowrap">
                                                            {{ $employee->address ?? 'None' }}
                                                        </td>
                                                        <td class="px-5 py-4 text-center text-sm font-medium whitespace-nowrap">
                                                            Lat: {{ $employee->curr_lat ?? 'None' }} <br>
                                                            Lng: {{ $employee->curr_lng ?? 'None' }}
                                                        </td>
                                                        <td class="px-5 py-4 text-center text-sm font-medium whitespace-nowrap {{ $employee->attachment ? '' : 'opacity-30' }}">
                                                            {{ $employee->attachment ?? 'None' }}
                                                        </td>
                                                        <td class="px-5 py-4 text-sm font-medium text-center whitespace-nowrap sticky right-0 z-10 bg-white dark:bg-gray-800">
                                                            <a href="#wfh-details">
                                                                <div class="relative">
                                                                    @php
                                                                        $thisName = trim($employee->surname . ', ' . $employee->first_name . ' ' . 
                                                                            ($employee->middle_name ? $employee->middle_name . ' ' : '') . 
                                                                            ($employee->name_extension ?? ''));
                                                                    @endphp
                                                                    <button wire:click="viewPreviousEmployeeLocation({{ $employee->id }})" 
                                                                        class="peer inline-flex items-center justify-center px-4 py-2 -m-5 
                                                                        -mr-2 text-sm font-medium tracking-wide text-blue-500 hover:text-blue-600
                                                                        focus:outline-none" title="View">
                                                                        <i class="bi bi-eye-fill"></i>
                                                                    </button>
                                                                </div>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                        </tbody>
                                    </table>
                                    @if ($history->isEmpty())
                                        <div class="p-4 text-center text-gray-500 dark:text-gray-300">
                                            No records!
                                        </div> 
                                    @endif
                                </div>
                                <div class="p-5 text-neutral-500 dark:text-neutral-200 bg-gray-200 dark:bg-gray-700">
                                    {{ $history->links() }}
                                </div>
                             </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

   {{-- Confirm Modal --}}
   <x-modal id="confirmModal" maxWidth="md" wire:model="confirmId" centered>
        <div class="p-4">
            <div class="mb-4 text-slate-900 dark:text-gray-100 font-bold">
                {{ $confirmMessage == 'approve' ? 'Confirm approval' : 'Confirm disapproval' }}
                <button @click="show = false" class="float-right focus:outline-none">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300">
                Are you sure you want to {{ $confirmMessage == 'approve' ? 'approve' : 'disapprove' }} this request?
            </label>

            <div class="mt-4 flex justify-end col-span-1 sm:col-span-1">
                @if($confirmMessage == 'approve')
                    <button wire:click='approveEmployeeLocation' class="mr-2 bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                        <div wire:loading wire:target="approveEmployeeLocation" style="margin-bottom: 5px;">
                            <div class="spinner-border small text-primary" role="status">
                            </div>
                        </div>
                        Approve
                    </button>
                @else
                    <button wire:click='disapproveEmployeeLocation' class="mr-2 bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                        <div wire:loading wire:target="disapproveEmployeeLocation" style="margin-bottom: 5px;">
                            <div class="spinner-border small text-primary" role="status">
                            </div>
                        </div>
                        Disapprove
                    </button>
                @endif
                <p @click="show = false" class="bg-gray-400 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded cursor-pointer">
                    Cancel
                </p>
            </div>

        </div>
    </x-modal>


</div>

{{-- <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBLp1y5i3ftfv5O_BN0_YSMd0VrXUht-Bs"></script>
<script>
    let map;
    let marker;
    
    // Initialize map first
    function initMap() {
        // Default to a central location if no coordinates yet
        const defaultLocation = { lat: 14.5995, lng: 120.9842 }; // Manila coordinates
        
        map = new google.maps.Map(document.getElementById("map"), {
            zoom: 15,
            center: defaultLocation,
            mapTypeControl: false,
            streetViewControl: false,
            fullscreenControl: true,
            zoomControl: true,
            styles: [
                {
                    featureType: "poi",
                    elementType: "labels",
                    stylers: [{ visibility: "off" }]
                }
            ]
        });
    }
    
    // Function to update map with new coordinates
    function updateMap() {
        const lat = @this.registeredLatitude;
        const lng = @this.registeredLongitude;
        
        if (lat && lng) {
            if (!map) {
                initMap();
            }

            const newLocation = { lat: parseFloat(lat), lng: parseFloat(lng) };
            
            // Update map center
            map.setCenter(newLocation);
        
            // Update or create marker
            if (marker) {
                marker.setPosition(newLocation);
            } else {
                marker = new google.maps.Marker({
                    position: newLocation,
                    map: map,
                    title: 'Your Location',
                    animation: google.maps.Animation.DROP
                });
            }
        }
    }
    
    // Initialize map when page loads
    document.addEventListener('DOMContentLoaded', initMap); 

    // Listen for location updates from Livewire
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('location-updated', () => {
            updateMap();
        });
    });

    // Remove marker when switching tabs
    Alpine.effect(() => {
        if (selectedTab !== 'employees') {
            if (marker) {
                marker.setMap(null);
                marker = null;
            }
        }
    });
</script> --}}
