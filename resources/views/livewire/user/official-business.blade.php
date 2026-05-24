<div class="w-full text-sm" 
x-data="{ selectedTab: @entangle('selectedTab') }"
x-cloak>

    <style>
        .scrollbar-thin1::-webkit-scrollbar {
                       width: 5px;
                   }

       .scrollbar-thin1::-webkit-scrollbar-thumb {
           background-color: #c5c5c54b;
       }

       .scrollbar-thin1::-webkit-scrollbar-track {
           background-color: #ffffff23;
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

       .dot-anim{
            background: rgb(168, 168, 255);
            width: 18px;
            height: 18px;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            animation: dot 2s ease-in-out infinite;
       }

       @keyframes dot{
            0%{
                transform: translate(-50%, -50%) scale(0.8);
            }50%{
                transform: translate(-50%, -50%) scale(1.2);
            }100%{
                transform: translate(-50%, -50%) scale(0.8);
            }
       }
   </style>

    <div class="flex justify-center w-full">
        <div class="w-full bg-white rounded-2xl p3 sm:p-8 shadow dark:bg-gray-800 overflow-x-visible p-3">
            <div class="pb-4 mb-3">
                <h1 class="text-lg font-bold text-center text-slate-800 dark:text-white">
                    My Official Business
                </h1>
            </div>

            
            @if($pdfContent)
                <div class="flex justify-end w-full">
                    <button class="text-gray-100 bg-green-500 
                        hover:bg-green-500 focus:outline-none rounded-md py-1 px-4"
                        wire:click="closeOb">
                        Back
                    </button>
                </div>
                <div class="mt-2" style="overflow: hidden;">
                    <iframe id="pdfIframe" src="data:application/pdf;base64,{{ $pdfContent }}"
                        style="width: 100%; max-height: 80vh; min-height: 500px;" frameborder="0"></iframe>
                </div>
            @else
                @if($ongoingObs)
                    <div class="w-full flex flex-col justify-center items-center mb-6 bg-gray-100 dark:bg-gray-900 border border-gray-300 dark:border-slate-900 shadow-xl relative" x-data="{ showDialog: false }">
                        <style>
                            .obs{
                                height: 250px;
                                width: 66%;
                            }

                            .obs2{
                                height: 250px;
                                width: 34%;
                            }

                            @media (max-width: 768px){
                                .obs,
                                .obs2{
                                    width: 100%;
                                }
                            }
                        </style>

                        <div class="flex justify-left items-center w-full overflow-hidden flex-wrap">
                            <div class="flex bg-gray-100 dark:bg-gray-900 relative obs">
                                <div class="w-full p-4">
                                    <div class="flex w-full">
                                        <p class="font-bold"><span class="{{ $obStatus == 'ONGOING' ? 'text-green-500' : 'text-orange-500' }}">{{ $obStatus }}</span> Official Business: {{ $ongoingObs->company }}</p>
                                    </div>
                                    <div class="flex w-full">
                                        <div class="flex items-center">
                                            <p class="mr-2">Current Location: </p>
                                            <div class="relative flex items-center justify-center mr-3" style="height: 18px; width: 18px;">
                                                <div class="bg-blue-500 rounded-full border border-white z-10" style="height: 12px; width: 12px;"></div>
                                                <div class="bg-blue-500 rounded-full dot-anim opacity-40"></div>
                                            </div>
                                        </div>
                                        <div class="flex">
                                            <p class="">OB Location: </p><img src="{{ asset('/images/red-dot.png') }}" alt="map icon" style="width: 25px; height: 25px; margin-bottom:-3px;" />
                                        </div>
                                    </div>
                                    <div>
                                        <p class="">Company: <span class="text-gray-700 dark:text-gray-100">{{ $ongoingObs->company }}</span></p>
                                        <p class="">Address: <span class="text-gray-700 dark:text-gray-100">{{ $ongoingObs->address }}</span></p>
                                        <p class="">Date: 
                                            @if($ongoingObs->dates->count() > 0)
                                                @foreach($ongoingObs->dates as $index => $dateRecord)
                                                    <span class="text-gray-700 dark:text-gray-100 p-2 rounded">{{ \Carbon\Carbon::parse($dateRecord->date)->format('m/d/Y') }}@if(!$loop->last), @endif</span>
                                                @endforeach
                                            @else
                                                N/A
                                            @endif
                                        </p>
                                        <p class="">Duration: <span class="text-gray-700 dark:text-gray-100">{{ $ongoingObs->duration ? ucwords(str_replace('_', ' ', $ongoingObs->duration)) : 'N/A' }}</span></p>
                                        <p class="">Time of Departure: 
                                            <span class="text-gray-700 dark:text-gray-100">
                                                {{ \Carbon\Carbon::parse($ongoingObs->time_start)->format('h:i A') }}
                                            </span>
                                        </p>
                                        <p class="">Estimated Time of Return 
                                            <span class="text-gray-700 dark:text-gray-100">
                                                {{ \Carbon\Carbon::parse($ongoingObs->time_end)->format('h:i A') }}
                                            </span>
                                        </p>                                    
                                        <p class="">Purpose: <span class="text-gray-700 dark:text-gray-100">{{ $ongoingObs->purpose }}</span></p>
                                    </div>

                                </div>

                                {{-- Map ----------------------------------------------- --}}
                                <div wire:ignore style="height: 250px; width: 100%;">
                                    <div id="map2" style="height: 250px; width: 100%; margin: 0;"></div>
                                </div>

                            </div>

                            <button class="absolute top-2 right-2 text-gray-100 bg-green-500 
                                hover:bg-green-500 focus:outline-none rounded-md py-1 px-4 z-10"
                                wire:click="showOb({{ $ongoingObs->id }})">
                                View
                            </button>

                            <div class="block p-6 shadow bg-gray-200 dark:bg-slate-800 relative obs2 h-full">
                                <h5 class="text-lg font-bold tracking-tight text-gray-900 dark:text-white text-center">OB ATTENDANCE</h5>
                                <div class="grid grid-cols-1 gap-2 p-4">
                                    <div class="flex justify-center">
                                        <button wire:click="confirmPunch({{ $ongoingObs->id }}, 'timeIn', 'Time In')"
                                            {{ $hasObTimeIn ? 'disabled' : '' }}
                                            class="relative inline-flex items-center justify-center p-0.5 mb-2 overflow-hidden text-sm 
                                            font-medium text-gray-900 rounded-lg group bg-gradient-to-br from-purple-600 to-blue-500 
                                            group-hover:from-purple-600 group-hover:to-blue-500 hover:text-white
                                            dark:text-white focus:ring-4 focus:outline-none focus:ring-blue-300 
                                            dark:focus:ring-blue-800 w-48 lg:w-64 disabled:opacity-50 disabled:cursor-not-allowed">
                                            <span
                                                class="relative px-2 py-2.5 bg-white dark:bg-gray-900 rounded-md 
                                                group-hover:bg-opacity-0 w-48 lg:w-64 transition-all duration-75 ease-in 
                                                group-disabled:bg-opacity-0 group-disabled:text-white nowrap">
                                                Time In{{ $hasObTimeIn ? (': ' . \Carbon\Carbon::parse($hasObTimeIn)->format('h:i A')) : '' }}
                                            </span>
                                        </button>
                                    </div>
                                    <div class="flex justify-center">
                                        <button wire:click="confirmPunch({{ $ongoingObs->id }}, 'timeOut', 'Time Out')"
                                            {{ $hasObTimeIn && !$hasObTimeOut ? '' : 'disabled' }}
                                            class="relative inline-flex items-center justify-center p-0.5 mb-2 overflow-hidden text-sm font-medium text-gray-900 rounded-lg group bg-gradient-to-br from-purple-600 to-blue-500 group-hover:from-purple-600 group-hover:to-blue-500 hover:text-white dark:text-white focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 w-48 lg:w-64 disabled:opacity-50 disabled:cursor-not-allowed">
                                            <span
                                                class="relative px-2 py-2.5 bg-white dark:bg-gray-900 rounded-md group-hover:bg-opacity-0 w-48 lg:w-64 transition-all duration-75 ease-in group-disabled:bg-opacity-0 group-disabled:text-white">
                                                Time Out{{ $hasObTimeOut ? (': ' . \Carbon\Carbon::parse($hasObTimeOut)->format('h:i A')) : '' }}
                                            </span>
                                        </button>
                                    </div>

                                    @if($isWithinRadius)
                                        <div class="flex justify-center">
                                            <p class="text-blue-500 underline" @click="showDialog = true">OB Details</p>
                                        </div>
                                    @endif
                                </div>

                                @if(!$isWithinRadius && $isTodayIsOb)
                                    <div
                                        class="absolute inset-0 flex justify-center items-center bg-gray-200 dark:bg-slate-700 bg-opacity-90 dark:bg-opacity-90">
                                        <div class="text-center">
                                            <i class="bi bi-person-lock" style="font-size: 3rem;"></i>
                                            <p class="font-bold mb-4">You have not arrived at <br>
                                                the OB location.</p>
                                            <p class="text-white bg-blue-500 p-2 rounded-md cursor-pointer hover:bg-blue-600" @click="showDialog = true">View OB Details</p>
                                        </div>
                                    </div>
                                @elseif(!$isTodayIsOb)
                                    <div
                                        class="absolute inset-0 flex justify-center items-center bg-gray-200 dark:bg-slate-700 bg-opacity-90 dark:bg-opacity-90">
                                        <div class="text-center">
                                            <i class="bi bi-person-lock" style="font-size: 3rem;"></i>
                                            <p class="font-bold mb-4">Attendance will be available on <br>
                                                <span class="text-gray-800 dark:text-white">{{ \Carbon\Carbon::parse($ongoingObs->date)->format('F d, Y') }}</span>
                                            </p>
                                            {{-- <p class="text-white bg-blue-500 p-2 rounded-md cursor-pointer hover:bg-blue-600" @click="showDialog = true">View OB Details</p> --}}
                                        </div>
                                    </div>
                                @endif

                                {{-- <div 
                                    x-show="showDialog" 
                                    x-transition:enter="transition ease-out duration-300 transform"
                                    x-transition:enter-start="translate-y-full opacity-0"
                                    x-transition:enter-end="translate-y-0 opacity-100"
                                    x-transition:leave="transition ease-in duration-200 transform"
                                    x-transition:leave-start="translate-y-0 opacity-100"
                                    x-transition:leave-end="translate-y-full opacity-0"
                                    x-cloak 
                                    class="absolute inset-0 bg-gray-200 dark:bg-slate-700 overflow-hidden">
                                    <div class="p-6 scrollbar-thin1" style="height: 100%; overflow-y:scroll">
                                        <div>
                                            <button @click="showDialog = false" class="float-right focus:outline-none">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                        <h5 class="text-xl font-bold mb-4 text-center text-gray-700 dark:text-gray-50">OB Details</h5>
                                        <p class="">Company: <span class="text-gray-700 dark:text-gray-100">{{ $ongoingObs->company }}</span></p>
                                        <p class="">Address: <span class="text-gray-700 dark:text-gray-100">{{ $ongoingObs->address }}</span></p>
                                        <p class="">Date: <span class="text-gray-700 dark:text-gray-100">{{ $ongoingObs->date }}</span></p>
                                        <p class="">Stary Time: <span class="text-gray-700 dark:text-gray-100">{{ $ongoingObs->time_start }}</span></p>
                                        <p class="">End Time: <span class="text-gray-700 dark:text-gray-100">{{ $ongoingObs->time_end }}</span></p>
                                        <p class="">Purpose: <span class="text-gray-700 dark:text-gray-100">{{ $ongoingObs->purpose }}</span></p>
                                        <div class="w-full flex justify-center mt-6">
                                            <button 
                                                class="text-white bg-blue-500 p-2 rounded-md cursor-pointer hover:bg-blue-600"
                                                @click="showDialog = false">
                                                Attendance
                                            </button>
                                        </div>
                                    </div>
                                </div> --}}

                            </div>
                        </div>
                    </div>
                @endif

                <div class="mb-6 flex flex-col sm:flex-row items-end justify-between">
                    <div class="w-full sm:w-1/3 sm:mr-4" x-show="selectedTab === 'requests'">
                        <label for="search4" class="block text-sm font-medium text-gray-700 dark:text-slate-400 mb-1">Search</label>
                        <input type="text" id="search4" wire:model.live="search4"
                            class="px-2 py-1.5 block w-full shadow-sm sm:text-sm border border-gray-400 hover:bg-gray-300 rounded-md
                                dark:hover:bg-slate-600 dark:border-slate-600
                                dark:text-gray-300 dark:bg-gray-800"
                            placeholder="Enter reference number or company">
                    </div>
                    <div class="w-full sm:w-1/3 sm:mr-4" x-show="selectedTab === 'approved'">
                        <label for="search5" class="block text-sm font-medium text-gray-700 dark:text-slate-400 mb-1">Search</label>
                        <input type="text" id="search5" wire:model.live="search5"
                            class="px-2 py-1.5 block w-full shadow-sm sm:text-sm border border-gray-400 hover:bg-gray-300 rounded-md
                                dark:hover:bg-slate-600 dark:border-slate-600
                                dark:text-gray-300 dark:bg-gray-800"
                            placeholder="Enter reference number or company">
                    </div>
                    <div class="w-full sm:w-1/3 sm:mr-4" x-show="selectedTab === 'disapproved'">
                        <label for="search6" class="block text-sm font-medium text-gray-700 dark:text-slate-400 mb-1">Search</label>
                        <input type="text" id="search6" wire:model.live="search6"
                            class="px-2 py-1.5 block w-full shadow-sm sm:text-sm border border-gray-400 hover:bg-gray-300 rounded-md
                                dark:hover:bg-slate-600 dark:border-slate-600
                                dark:text-gray-300 dark:bg-gray-800"
                            placeholder="Enter reference number or company">
                    </div>

                    <div class="w-full sm:w-2/3 flex flex-col sm:flex-row sm:justify-end sm:space-x-4">
                        <div class="w-full sm:w-auto">
                            <button wire:click="toggleAddOB" 
                                class="text-sm mt-4 sm:mt-1 px-2 py-1.5 bg-green-500 text-white rounded-md 
                                hover:bg-green-600 focus:outline-none dark:bg-gray-700 w-full
                                dark:hover:bg-green-600 dark:text-gray-300 dark:hover:text-white">
                                Apply OB
                            </button>
                        </div>
                    </div>

                </div>

                <div class="overflow-hidden text-sm pb-3">
                    <div class="flex gap-2 overflow-x-auto -mb-2" class="relative">
                        <button @click="selectedTab = 'approved'"
                            :class="{ 'font-bold dark:text-gray-300 dark:bg-gray-700 bg-gray-200 rounded-t-lg': selectedTab === 'approved', 'text-slate-700 font-medium dark:text-slate-300 dark:hover:text-white hover:text-black': selectedTab !== 'approved' }"
                            class="h-min px-4 pt-2 pb-4 text-sm text-nowrap">
                            Approved OB
                        </button>
                        <button @click="selectedTab = 'requests'"
                            :class="{ 'font-bold dark:text-gray-300 dark:bg-gray-700 bg-gray-200 rounded-t-lg': selectedTab === 'requests', 'text-slate-700 font-medium dark:text-slate-300 dark:hover:text-white hover:text-black': selectedTab !== 'requests' }"
                            class="h-min px-4 pt-2 pb-4 text-sm text-nowrap">
                            OB Requests
                        </button>
                        <button @click="selectedTab = 'disapproved'"
                            :class="{ 'font-bold dark:text-gray-300 dark:bg-gray-700 bg-gray-200 rounded-t-lg': selectedTab === 'disapproved', 'text-slate-700 font-medium dark:text-slate-300 dark:hover:text-white hover:text-black': selectedTab !== 'disapproved' }"
                            class="h-min px-4 pt-2 pb-4 text-sm text-nowrap">
                            Disapproved OB
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <div class="overflow-hidden border dark:border-gray-700 rounded-lg">
                            <div x-show="selectedTab === 'requests'">
                                <div class="overflow-x-auto">
                                    <table class="w-full min-w-full">
                                        <thead class="bg-gray-200 dark:bg-gray-700 rounded-xl">
                                            <tr class="whitespace-nowrap">
                                                <th scope="col" class="px-5 py-3 text-left text-sm font-medium uppercase">
                                                    Approval Status
                                                </th>
                                                <th scope="col" class="px-5 py-3 text-left text-sm font-medium uppercase">
                                                    Reference No.
                                                </th>
                                                <th scope="col" class="px-5 py-3 text-center text-sm font-medium uppercase">
                                                    Company
                                                </th>
                                                <th scope="col" class="px-5 py-3 text-center text-sm font-medium uppercase">
                                                    Address
                                                </th>
                                                <th scope="col" class="px-5 py-3 text-center text-sm font-medium uppercase">
                                                    Geolocation
                                                </th>
                                                <th scope="col" class="px-5 py-3 text-center text-sm font-medium uppercase">
                                                    Purpose
                                                </th>
                                                <th scope="col" class="px-5 py-3 text-center text-sm font-medium uppercase">
                                                    Date
                                                </th>
                                                <th scope="col" class="px-5 py-3 text-center text-sm font-medium uppercase">
                                                    Duration
                                                </th>
                                                <th scope="col" class="px-5 py-3 text-center text-sm font-medium uppercase">
                                                    Time
                                                </th>
                                                <th scope="col" class="px-5 py-3 text-center text-sm font-medium uppercase">
                                                    Attendance
                                                </th>
                                                <th scope="col" class="px-5 py-3 text-center text-sm font-medium uppercase">
                                                    Attachment
                                                </th>
                                                <th class="px-5 py-3 text-gray-100 text-sm font-medium text-center uppercase sticky right-0 z-10 bg-gray-600 dark:bg-gray-600">
                                                    Action
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-neutral-200 dark:divide-gray-400">
                                            @foreach ($obRequests as $obs)
                                                <tr class="text-neutral-800 dark:text-neutral-200">
                                                    <td class="px-5 py-4 text-left text-sm font-medium whitespace-nowrap relative" style="overflow-y: visible">
                                                        <div>
                                                            <span class="opacity-60">Supervisor: </span>
                                                                {{ $obs->supervisor }} <br/>
                                                            <span class="opacity-60">Status: </span>
                                                                <span class="{{ isset($obs->date_sup_approved) && $obs->date_sup_approved ? 'text-green-500' :
                                                                (isset($obs->date_sup_disapproved) && $obs->date_sup_disapproved ? 'text-red-500' : 'text-orange-500') }}">
                                                            
                                                                    {{ isset($obs->date_sup_approved) && $obs->date_sup_approved ? 'Approved' :
                                                                    (isset($obs->date_sup_disapproved) && $obs->date_sup_disapproved ? 'Disapproved' : 'Pending') }}
                                                                </span><br/>
                                                            @if($obs->date_sup_approved)
                                                                <span class="opacity-60">Date Approved: </span>{{ \Carbon\Carbon::parse($obs->date_sup_approved)->format('F d, Y') }} <br/>
                                                            @endif
                                                            @if($obs->date_sup_disapproved)
                                                                <span class="opacity-60">Date Disapproved: </span>{{ \Carbon\Carbon::parse($obs->date_sup_disapproved)->format('F d, Y') }} <br/>
                                                            @endif
                                                        </div>
                                                        <hr class="my-1 opacity-60">
                                                        <div>
                                                            <span class="opacity-60">HR: </span><span class="{{ $obs->hr ? '' : 'text-orange-500' }}">{{ $obs->hr ?: 'Pending' }}</span> <br/>
                                                            @if($obs->hr)
                                                                <span class="opacity-60">Status: </span>
                                                                    <span class="{{ isset($obs->date_sup_approved) && $obs->date_sup_approved ? 'text-green-500' :
                                                                    (isset($obs->date_sup_disapproved) && $obs->date_sup_disapproved ? 'text-red-500' : 'text-orange-500') }}">
                                                                
                                                                        {{ isset($obs->date_sup_approved) && $obs->date_sup_approved ? 'Approved' :
                                                                        (isset($obs->date_sup_disapproved) && $obs->date_sup_disapproved ? 'Disapproved' : 'Pending') }}
                                                                    </span><br/>
                                                                @if($obs->date_sup_approved)
                                                                    <span class="opacity-60">Date Approved: </span>{{ \Carbon\Carbon::parse($obs->date_sup_approved)->format('F d, Y') }} <br/>
                                                                @endif
                                                                @if($obs->date_sup_disapproved)
                                                                    <span class="opacity-60">Date Disapproved: </span>{{ \Carbon\Carbon::parse($obs->date_sup_disapproved)->format('F d, Y') }} <br/>
                                                                @endif
                                                            @endif
                                                        </div>
                                                    </td>
                                                    <td class="px-5 py-4 text-left text-sm font-medium whitespace-nowrap">
                                                        {{ $obs->reference_number }}
                                                    </td>
                                                    <td class="px-5 py-4 text-center text-sm font-medium whitespace-nowrap">
                                                        {{ $obs->company }}
                                                    </td>
                                                    <td class="px-5 py-4 text-center text-sm font-medium whitespace-nowrap">
                                                        {{ $obs->address }}
                                                    </td>
                                                    <td class="px-5 py-4 text-center text-sm font-medium whitespace-nowrap">
                                                        Lat: {{ $obs->lat }} <br>
                                                        Lng: {{ $obs->lng }}
                                                    </td>
                                                    <td class="px-5 py-4 text-center text-sm font-medium whitespace-nowrap">
                                                        <div 
                                                            class="truncate max-w-xs"
                                                            style="max-width: 20ch; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"
                                                        >
                                                            {{ $obs->purpose }}
                                                        </div>
                                                    </td>
                                                    <td class="px-5 py-4 text-center text-sm font-medium whitespace-nowrap">
                                                        @if($obs->dates->count() > 0)
                                                            @foreach($obs->dates as $index => $dateRecord)
                                                                <span class="text-gray-700 dark:text-gray-100 p-2 rounded bg-gray-100 dark:bg-gray-900 ">{{ \Carbon\Carbon::parse($dateRecord->date)->format('m/d/Y') }}</span><br><br>
                                                            @endforeach
                                                        @else
                                                            N/A
                                                        @endif
                                                    </td>
                                                    <td class="px-5 py-4 text-center text-sm font-medium whitespace-nowrap">
                                                        @if($obs->duration == 'half_day')
                                                            Half day / less than 8 hours 
                                                        @else
                                                            {{ $obs->duration ? ucwords(str_replace('_', ' ', $obs->duration)) : 'N/A' }}
                                                        @endif
                                                    </td>
                                                    <td class="px-5 py-4 text-left text-sm font-medium whitespace-nowrap">
                                                        @if($obs->duration == 'half_day')
                                                            <span class="opacity-70">Time of Departure:</span> {{ \Carbon\Carbon::parse($obs->time_start)->format('h:i A') }} <br>
                                                            <span class="opacity-70">Estimated Time of Return :</span> {{ \Carbon\Carbon::parse($obs->time_end)->format('h:i A') }}
                                                        @else
                                                            --
                                                        @endif
                                                    </td>
                                                    <td class="px-5 py-4 text-left text-sm font-medium whitespace-nowrap">
                                                        <span class="opacity-70">Time In:</span> {{ $obs->time_in ? \Carbon\Carbon::parse($obs->time_in)->format('h:i A') : '' }} <br>
                                                        <span class="opacity-70">Time Out:</span> {{ $obs->time_out ? \Carbon\Carbon::parse($obs->time_out)->format('h:i A') : '' }}
                                                    </td>
                                                    <td class="px-5 py-4 text-center text-sm font-medium whitespace-nowrap">
                                                        @if($obs->attachment)
                                                            <button 
                                                                wire:click="downloadAttachment({{ $obs->id }})"
                                                                class="text-blue-600 hover:text-blue-900"
                                                                title="Download Attachment"
                                                            >
                                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                                </svg>
                                                            </button>
                                                        @else
                                                            <span class="opacity-50">No attachment</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-sm font-medium text-center whitespace-nowrap sticky right-0 z-10 bg-white dark:bg-gray-800 border-b-2 border-neutral-300 dark:border-gray-500">
                                                        <div class="relative w-full h-full flex items-center justify-center">

                                                            @if(!$obs->date_sup_approved && !$obs->date_sup_disapproved)
                                                                <button wire:click="toggleEditOB({{ $obs->id }})" 
                                                                    class="peer inline-flex items-center justify-center px-4 py-2 -m-5 
                                                                    -mr-2 text-sm font-medium tracking-wide text-blue-500 hover:text-blue-600 
                                                                    focus:outline-none" title="Edit">
                                                                    <i class="fas fa-pencil-alt ml-3"></i>
                                                                </button>
                                                                <button wire:click="toggleDeleteOB({{ $obs->id }})" 
                                                                    class=" text-red-600 hover:text-red-900 dark:text-red-600 
                                                                    dark:hover:text-red-900 relative z-10" title="Delete">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            @endif
                                                            
                                                            <button wire:click="showOb({{ $obs->id }})" 
                                                                class="peer inline-flex items-center justify-center px-4 py-2 -m-5 
                                                                -mr-2 text-sm font-medium tracking-wide text-blue-500 hover:text-blue-600 
                                                                focus:outline-none" title="View">
                                                                <i class="fas fa-eye ml-3"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    @if ($obRequests->isEmpty())
                                        <div class="p-4 text-center text-gray-500 dark:text-gray-300">
                                            No records!
                                        </div> 
                                    @endif
                                </div>
                                <div class="p-5 text-neutral-500 dark:text-neutral-200 bg-gray-200 dark:bg-gray-700">
                                    {{ $obRequests->links() }}
                                </div>
                            </div>
                            <div x-show="selectedTab === 'disapproved'">
                                <div class="overflow-x-auto">
                                    <table class="w-full min-w-full">
                                        <thead class="bg-gray-200 dark:bg-gray-700 rounded-xl">
                                            <tr class="whitespace-nowrap">
                                                <th scope="col" class="px-5 py-3 text-left text-sm font-medium uppercase">
                                                    Approval Status
                                                </th>
                                                <th scope="col" class="px-5 py-3 text-left text-sm font-medium uppercase">
                                                    Reference No.
                                                </th>
                                                <th scope="col" class="px-5 py-3 text-center text-sm font-medium uppercase">
                                                    Company
                                                </th>
                                                <th scope="col" class="px-5 py-3 text-center text-sm font-medium uppercase">
                                                    Address
                                                </th>
                                                <th scope="col" class="px-5 py-3 text-center text-sm font-medium uppercase">
                                                    Geolocation
                                                </th>
                                                <th scope="col" class="px-5 py-3 text-center text-sm font-medium uppercase">
                                                    Purpose
                                                </th>
                                                <th scope="col" class="px-5 py-3 text-center text-sm font-medium uppercase">
                                                    Date
                                                </th>
                                                <th scope="col" class="px-5 py-3 text-center text-sm font-medium uppercase">
                                                    Duration
                                                </th>
                                                <th scope="col" class="px-5 py-3 text-center text-sm font-medium uppercase">
                                                    Time
                                                </th>
                                                <th scope="col" class="px-5 py-3 text-center text-sm font-medium uppercase">
                                                    Attendance
                                                </th>
                                                <th class="px-5 py-3 text-gray-100 text-sm font-medium text-center uppercase sticky right-0 z-10 bg-gray-600 dark:bg-gray-600">
                                                    Action
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-neutral-200 dark:divide-gray-400">
                                            @foreach ($disapprovedObs as $obs)
                                                <tr class="text-neutral-800 dark:text-neutral-200">
                                                    <td class="px-5 py-4 text-left text-sm font-medium whitespace-nowrap relative" style="overflow-y: visible">
                                                        <div>
                                                            <span class="opacity-60">Supervisor: </span>
                                                                {{ $obs->supervisor }} <br/>
                                                            <span class="opacity-60">Status: </span>
                                                                <span class="{{ isset($obs->date_sup_approved) && $obs->date_sup_approved ? 'text-green-500' :
                                                                (isset($obs->date_sup_disapproved) && $obs->date_sup_disapproved ? 'text-red-500' : 'text-orange-500') }}">
                                                            
                                                                    {{ isset($obs->date_sup_approved) && $obs->date_sup_approved ? 'Approved' :
                                                                    (isset($obs->date_sup_disapproved) && $obs->date_sup_disapproved ? 'Disapproved' : 'Pending') }}
                                                                </span><br/>
                                                            @if($obs->date_sup_approved)
                                                                <span class="opacity-60">Date Approved: </span>{{ \Carbon\Carbon::parse($obs->date_sup_approved)->format('F d, Y') }} <br/>
                                                            @endif
                                                            @if($obs->date_sup_disapproved)
                                                                <span class="opacity-60">Date Disapproved: </span>{{ \Carbon\Carbon::parse($obs->date_sup_disapproved)->format('F d, Y') }} <br/>
                                                            @endif
                                                        </div>
                                                        <hr class="my-1 opacity-60">
                                                        <div>
                                                            <span class="opacity-60">HR: </span><span class="{{ $obs->hr ? '' : 'text-orange-500' }}">{{ $obs->hr ?: 'Pending' }}</span> <br/>
                                                            @if($obs->hr)
                                                                <span class="opacity-60">Status: </span>
                                                                    <span class="{{ $obs->date_approved ? 'text-green-500' : ($obs->date_disapproved ? 'text-red-500' : 'text-orange-500') }}">
                                                                        {{ $obs->date_approved ? 'Approved' : ($obs->date_disapproved ? 'Disapproved' : 'Pending') }}
                                                                    </span><br/>
                                                                @if($obs->date_approved)
                                                                    <span class="opacity-60">Date Approved: </span>{{ \Carbon\Carbon::parse($obs->date_approved)->format('F d, Y') }} <br/>
                                                                @endif
                                                                @if($obs->date_disapproved)
                                                                    <span class="opacity-60">Date Disapproved: </span>{{ \Carbon\Carbon::parse($obs->date_disapproved)->format('F d, Y') }} <br/>
                                                                @endif
                                                            @endif
                                                        </div>
                                                    </td>
                                                    <td class="px-5 py-4 text-left text-sm font-medium whitespace-nowrap">
                                                        {{ $obs->reference_number }}
                                                    </td>
                                                    <td class="px-5 py-4 text-center text-sm font-medium whitespace-nowrap">
                                                        {{ $obs->company }}
                                                    </td>
                                                    <td class="px-5 py-4 text-center text-sm font-medium whitespace-nowrap">
                                                        {{ $obs->address }}
                                                    </td>
                                                    <td class="px-5 py-4 text-center text-sm font-medium whitespace-nowrap">
                                                        Lat: {{ $obs->lat }} <br>
                                                        Lng: {{ $obs->lng }}
                                                    </td>
                                                    <td class="px-5 py-4 text-center text-sm font-medium whitespace-nowrap">
                                                        <div 
                                                            class="truncate max-w-xs"
                                                            style="max-width: 20ch; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"
                                                        >
                                                            {{ $obs->purpose }}
                                                        </div>
                                                    </td>
                                                    <td class="px-5 py-4 text-center text-sm font-medium whitespace-nowrap">
                                                        @if($obs->dates->count() > 0)
                                                            @foreach($obs->dates as $index => $dateRecord)
                                                                <span class="text-gray-700 dark:text-gray-100 p-2 rounded bg-gray-100 dark:bg-gray-900 ">{{ \Carbon\Carbon::parse($dateRecord->date)->format('m/d/Y') }}</span><br><br>
                                                            @endforeach
                                                        @else
                                                            N/A
                                                        @endif
                                                    </td>
                                                    <td class="px-5 py-4 text-center text-sm font-medium whitespace-nowrap">
                                                        @if($obs->duration == 'half_day')
                                                            Half day / less than 8 hours 
                                                        @else
                                                            {{ $obs->duration ? ucwords(str_replace('_', ' ', $obs->duration)) : 'N/A' }}
                                                        @endif
                                                    </td>
                                                    <td class="px-5 py-4 text-left text-sm font-medium whitespace-nowrap">
                                                        @if($obs->duration == 'half_day')
                                                            <span class="opacity-70">Time of Departure:</span> {{ \Carbon\Carbon::parse($obs->time_start)->format('h:i A') }} <br>
                                                            <span class="opacity-70">Estimated Time of Return :</span> {{ \Carbon\Carbon::parse($obs->time_end)->format('h:i A') }}
                                                        @else
                                                            --
                                                        @endif
                                                    </td>
                                                    <td class="px-5 py-4 text-left text-sm font-medium whitespace-nowrap">
                                                        <span class="opacity-70">Time In:</span> {{ $obs->time_in ? \Carbon\Carbon::parse($obs->time_in)->format('h:i A') : '' }} <br>
                                                        <span class="opacity-70">Time Out:</span> {{ $obs->time_out ? \Carbon\Carbon::parse($obs->time_out)->format('h:i A') : '' }}
                                                    </td>
                                                    <td class="px-5 py-4 text-sm font-medium text-center whitespace-nowrap sticky right-0 z-10 bg-white dark:bg-gray-800">
                                                        <div class="relative">
                                                            <button wire:click="showOb({{ $obs->id }})" 
                                                                class="peer inline-flex items-center justify-center px-4 py-2 -m-5 
                                                                -mr-2 text-sm font-medium tracking-wide text-blue-500 hover:text-blue-600 
                                                                focus:outline-none" title="View">
                                                                <i class="fas fa-eye ml-3"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    @if ($disapprovedObs->isEmpty())
                                        <div class="p-4 text-center text-gray-500 dark:text-gray-300">
                                            No records!
                                        </div> 
                                    @endif
                                </div>
                                <div class="p-5 text-neutral-500 dark:text-neutral-200 bg-gray-200 dark:bg-gray-700">
                                    {{ $disapprovedObs->links() }}
                                </div>
                            </div>
                            <div x-show="selectedTab === 'approved'">
                                <div class="overflow-x-auto">
                                    <table class="w-full min-w-full">
                                        <thead class="bg-gray-200 dark:bg-gray-700 rounded-xl">
                                            <tr class="whitespace-nowrap">
                                                <th scope="col" class="px-5 py-3 text-left text-sm font-medium uppercase">
                                                    Approved By
                                                </th>
                                                <th scope="col" class="px-5 py-3 text-left text-sm font-medium uppercase">
                                                    Reference No.
                                                </th>
                                                <th scope="col" class="px-5 py-3 text-center text-sm font-medium uppercase">
                                                    Company
                                                </th>
                                                <th scope="col" class="px-5 py-3 text-center text-sm font-medium uppercase">
                                                    Address
                                                </th>
                                                <th scope="col" class="px-5 py-3 text-center text-sm font-medium uppercase">
                                                    Geolocation
                                                </th>
                                                <th scope="col" class="px-5 py-3 text-center text-sm font-medium uppercase">
                                                    Purpose
                                                </th>
                                                <th scope="col" class="px-5 py-3 text-center text-sm font-medium uppercase">
                                                    Date
                                                </th>
                                                <th scope="col" class="px-5 py-3 text-center text-sm font-medium uppercase">
                                                    Duration
                                                </th>
                                                <th scope="col" class="px-5 py-3 text-center text-sm font-medium uppercase">
                                                    Time
                                                </th>
                                                <th scope="col" class="px-5 py-3 text-center text-sm font-medium uppercase">
                                                    Attendance
                                                </th>
                                                <th class="px-5 py-3 text-gray-100 text-sm font-medium text-center uppercase sticky right-0 z-10 bg-gray-600 dark:bg-gray-600">
                                                    Action
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-neutral-200 dark:divide-gray-400">
                                            @foreach ($approvedObs as $obs)
                                                <tr class="text-neutral-800 dark:text-neutral-200">
                                                    <td class="px-5 py-4 text-left text-sm font-medium whitespace-nowrap relative" style="overflow-y: visible">
                                                        <div>
                                                            <span class="opacity-60">Supervisor: </span>
                                                                {{ $obs->supervisor }} <br/>
                                                            <span class="opacity-60">Status: </span>
                                                                <span class="{{ isset($obs->date_sup_approved) && $obs->date_sup_approved ? 'text-green-500' :
                                                                (isset($obs->date_sup_disapproved) && $obs->date_sup_disapproved ? 'text-red-500' : 'text-orange-500') }}">
                                                            
                                                                    {{ isset($obs->date_sup_approved) && $obs->date_sup_approved ? 'Approved' :
                                                                    (isset($obs->date_sup_disapproved) && $obs->date_sup_disapproved ? 'Disapproved' : 'Pending') }}
                                                                </span><br/>
                                                            @if($obs->date_sup_approved)
                                                                <span class="opacity-60">Date Approved: </span>{{ \Carbon\Carbon::parse($obs->date_sup_approved)->format('F d, Y') }} <br/>
                                                            @endif
                                                            @if($obs->date_sup_disapproved)
                                                                <span class="opacity-60">Date Disapproved: </span>{{ \Carbon\Carbon::parse($obs->date_sup_disapproved)->format('F d, Y') }} <br/>
                                                            @endif
                                                        </div>
                                                        <hr class="my-1 opacity-60">
                                                        <div>
                                                            <span class="opacity-60">HR: </span><span class="{{ $obs->hr ? '' : 'text-orange-500' }}">{{ $obs->hr ?: 'Pending' }}</span> <br/>
                                                            @if($obs->hr)
                                                                <span class="opacity-60">Status: </span>
                                                                    <span class="{{ $obs->date_approved ? 'text-green-500' : ($obs->date_disapproved ? 'text-red-500' : 'text-orange-500') }}">
                                                                        {{ $obs->date_approved ? 'Approved' : ($obs->date_disapproved ? 'Disapproved' : 'Pending') }}
                                                                    </span><br/>
                                                                @if($obs->date_approved)
                                                                    <span class="opacity-60">Date Approved: </span>{{ \Carbon\Carbon::parse($obs->date_approved)->format('F d, Y') }} <br/>
                                                                @endif
                                                                @if($obs->date_disapproved)
                                                                    <span class="opacity-60">Date Disapproved: </span>{{ \Carbon\Carbon::parse($obs->date_disapproved)->format('F d, Y') }} <br/>
                                                                @endif
                                                            @endif
                                                        </div>
                                                    </td>
                                                    <td class="px-5 py-4 text-left text-sm font-medium whitespace-nowrap">
                                                        {{ $obs->reference_number }}
                                                    </td>
                                                    <td class="px-5 py-4 text-center text-sm font-medium whitespace-nowrap">
                                                        {{ $obs->company }}
                                                    </td>
                                                    <td class="px-5 py-4 text-center text-sm font-medium whitespace-nowrap">
                                                        {{ $obs->address }}
                                                    </td>
                                                    <td class="px-5 py-4 text-center text-sm font-medium whitespace-nowrap">
                                                        Lat: {{ $obs->lat }} <br>
                                                        Lng: {{ $obs->lng }}
                                                    </td>
                                                    <td class="px-5 py-4 text-center text-sm font-medium whitespace-nowrap">
                                                        <div 
                                                            class="truncate max-w-xs"
                                                            style="max-width: 20ch; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"
                                                        >
                                                            {{ $obs->purpose }}
                                                        </div>
                                                    </td>
                                                    <td class="px-5 py-4 text-center text-sm font-medium whitespace-nowrap">
                                                        @if($obs->dates->count() > 0)
                                                            @foreach($obs->dates as $index => $dateRecord)
                                                                <span class="text-gray-700 dark:text-gray-100 p-2 rounded bg-gray-100 dark:bg-gray-900 mr-2">{{ \Carbon\Carbon::parse($dateRecord->date)->format('m/d/Y') }}</span>
                                                            @endforeach
                                                        @else
                                                            N/A
                                                        @endif
                                                    </td>
                                                    <td class="px-5 py-4 text-center text-sm font-medium whitespace-nowrap">
                                                        @if($obs->dates->count() > 0)
                                                            @foreach($obs->dates as $index => $dateRecord)
                                                                <span class="text-gray-700 dark:text-gray-100 p-2 rounded bg-gray-100 dark:bg-gray-900 ">{{ \Carbon\Carbon::parse($dateRecord->date)->format('m/d/Y') }}</span><br><br>
                                                            @endforeach
                                                        @else
                                                            N/A
                                                        @endif
                                                    </td>
                                                    <td class="px-5 py-4 text-left text-sm font-medium whitespace-nowrap">
                                                        @if($obs->duration == 'half_day')
                                                            <span class="opacity-70">Time of Departure:</span> {{ \Carbon\Carbon::parse($obs->time_start)->format('h:i A') }} <br>
                                                            <span class="opacity-70">Estimated Time of Return :</span> {{ \Carbon\Carbon::parse($obs->time_end)->format('h:i A') }}
                                                        @else
                                                            --
                                                        @endif
                                                    </td>
                                                    <td class="px-5 py-4 text-left text-sm font-medium whitespace-nowrap">
                                                        <span class="opacity-70">Time In:</span> {{ $obs->time_in ? \Carbon\Carbon::parse($obs->time_in)->format('h:i A') : '' }} <br>
                                                        <span class="opacity-70">Time Out:</span> {{ $obs->time_out ? \Carbon\Carbon::parse($obs->time_out)->format('h:i A') : '' }}
                                                    </td>
                                                    <td class="px-5 py-4 text-sm font-medium text-center whitespace-nowrap sticky right-0 z-10 bg-white dark:bg-gray-800">
                                                        <div class="relative">
                                                            <button wire:click="showOb({{ $obs->id }})" 
                                                                class="peer inline-flex items-center justify-center px-4 py-2 -m-5 
                                                                -mr-2 text-sm font-medium tracking-wide text-blue-500 hover:text-blue-600 
                                                                focus:outline-none" title="View">
                                                                <i class="fas fa-eye ml-3"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    @if ($approvedObs->isEmpty())
                                        <div class="p-4 text-center text-gray-500 dark:text-gray-300">
                                            No records!
                                        </div> 
                                    @endif
                                </div>
                                <div class="p-5 text-neutral-500 dark:text-neutral-200 bg-gray-200 dark:bg-gray-700">
                                    {{ $approvedObs->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>


    {{-- Add and Edit OB Modal --}}
    <x-modal id="obModal" maxWidth="2xl" wire:model="editOB">
        <div class="p-4">
            <div class="rounded-lg mb-4 p-4 dark:text-gray-50 text-slate-900 font-bold text-lg">
                {{ $addOB ? 'Apply' : 'Edit' }} Official Business
                <button @click="show = false" class="float-right focus:outline-none" wire:click='resetVariables'>
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label for="company" class="block text-sm font-medium text-gray-700 dark:text-slate-400">Destination (Company) <span class="text-red-500">*</span></label>
                    <input type="text" id="company" wire:model.live='company' class="mt-1 p-2 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:text-gray-300 dark:bg-gray-700">
                    @error('company') 
                        <span class="text-red-500 text-sm">The company is required!</span> 
                    @enderror
                </div>
                <div class="col-span-2">
                    <label for="address" class="block text-sm font-medium text-gray-700 dark:text-slate-400">Destination (Address) <span class="text-red-500">*</span></label>
                    <input type="text" id="address" wire:model.live='address' class="mt-1 p-2 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:text-gray-300 dark:bg-gray-700">
                    @error('address') 
                        <span class="text-red-500 text-sm">The address is required!</span> 
                    @enderror
                </div>
                <div class="col-span-2">
                    <label for="dates" class="block text-sm font-medium text-gray-700 dark:text-slate-400">Dates of OB <span class="text-red-500">*</span></label>
                    
                    <!-- Date input and Add button -->
                    <div class="flex gap-2 mt-1">
                        <input 
                            type="date" 
                            id="dateInput" 
                            wire:model="dateInput" 
                            class="flex-1 p-2 shadow-sm sm:text-sm border-gray-300 rounded-md dark:text-gray-300 dark:bg-gray-700"
                        >
                        <button 
                            type="button"
                            wire:click="addDate"
                            class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded"
                        >
                            Add
                        </button>
                    </div>
                    
                    @error('dates') 
                        <span class="text-red-500 text-sm">{{ $message }}</span> 
                    @enderror
                    @error('dateInput') 
                        <span class="text-red-500 text-sm">{{ $message }}</span> 
                    @enderror
                    
                    <!-- Display selected dates -->
                    @if(!empty($dates))
                        <div class="mt-3 space-y-2">
                            <p class="text-sm font-medium text-gray-700 dark:text-slate-400">Selected Dates:</p>
                            <div class="flex flex-wrap gap-2">
                                @foreach($dates as $index => $date)
                                    <div class="flex items-center bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 px-3 py-1 rounded-full">
                                        <span class="text-sm">{{ \Carbon\Carbon::parse($date)->format('M d, Y') }}</span>
                                        <button 
                                            type="button"
                                            wire:click="removeDate({{ $index }})"
                                            class="ml-2 text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 focus:outline-none"
                                        >
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-400">Duration <span class="text-red-500">*</span></label>
                    <div class="mt-2 space-y-2">
                        <div class="flex items-center">
                            <input 
                                type="radio" 
                                id="whole_day" 
                                wire:model.live="duration" 
                                value="whole_day" 
                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 dark:bg-gray-700 dark:border-gray-600"
                            >
                            <label for="whole_day" class="ml-2 block text-sm text-gray-700 dark:text-slate-400">Whole day</label>
                        </div>
                        
                        <div class="flex items-center">
                            <input 
                                type="radio" 
                                id="half_day" 
                                wire:model.live="duration" 
                                value="half_day" 
                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 dark:bg-gray-700 dark:border-gray-600"
                            >
                            <label for="half_day" class="ml-2 block text-sm text-gray-700 dark:text-slate-400">Half day / less than 8 hours</label>
                        </div>
                        
                        <div class="flex items-center">
                            <input 
                                type="radio" 
                                id="am" 
                                wire:model.live="duration" 
                                value="am" 
                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 dark:bg-gray-700 dark:border-gray-600"
                            >
                            <label for="am" class="ml-2 block text-sm text-gray-700 dark:text-slate-400">AM</label>
                        </div>
                        
                        <div class="flex items-center">
                            <input 
                                type="radio" 
                                id="pm" 
                                wire:model.live="duration" 
                                value="pm" 
                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 dark:bg-gray-700 dark:border-gray-600"
                            >
                            <label for="pm" class="ml-2 block text-sm text-gray-700 dark:text-slate-400">PM</label>
                        </div>
                    </div>
                    
                    <!-- Conditional time inputs for half day selection -->
                    <div x-show="$wire.duration === 'half_day'" class="grid grid-cols-2 gap-4 mt-3">
                        <div>
                            <label for="startTime" class="block text-sm font-medium text-gray-700 dark:text-slate-400">Time of Departure <span class="text-red-500">*</span></label>
                            <input 
                                type="time" 
                                id="startTime" 
                                wire:model.live="startTime" 
                                class="mt-1 p-2 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:text-gray-300 dark:bg-gray-700"
                            >
                        </div>
                        <div>
                            <label for="endTime" class="block text-sm font-medium text-gray-700 dark:text-slate-400">Estimated Time of Return <span class="text-red-500">*</span></label>
                            <input 
                                type="time" 
                                id="endTime" 
                                wire:model.live="endTime" 
                                class="mt-1 p-2 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:text-gray-300 dark:bg-gray-700"
                            >
                        </div>
                    </div>
                    
                    @error('duration')
                        <span class="text-red-500 text-sm">Please select a time option!</span>
                    @enderror
                    @error('startTime')
                        <span class="text-red-500 text-sm">Start time is required for half day selection!</span>
                    @enderror
                    @error('endTime')
                        <span class="text-red-500 text-sm">End time is required for half day selection!</span>
                    @enderror
                </div>

                <div class="col-span-2">
                    <label for="purpose" class="block text-sm font-medium text-gray-700 dark:text-slate-400">Purpose <span class="text-red-500">*</span></label>
                    <textarea type="text" id="purpose" cols="30" rows="4" wire:model.live='purpose' class="mt-1 p-2 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:text-gray-300 dark:bg-gray-700"></textarea>
                    @error('purpose') 
                        <span class="text-red-500 text-sm">The purpose is required!</span> 
                    @enderror
                </div>

                <div class="col-span-2">
                    <label for="attachment" class="block text-sm font-medium text-gray-700 dark:text-slate-400">
                        Attachment <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="file" 
                        accept=".pdf" 
                        id="attachment" 
                        wire:model="attachment" 
                        class="mt-1 p-2 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:text-gray-300 dark:bg-gray-700"
                    >
                    @error('attachment') 
                        <span class="text-red-500 text-sm">{{ $message }}</span> 
                    @enderror
                    
                    @if($existingAttachment)
                        <div class="mt-2">
                            <span class="text-sm text-gray-600 dark:text-gray-400">
                                Current file: 
                                <button 
                                    type="button" 
                                    wire:click="downloadAttachment({{ $editId }})"
                                    class="text-blue-600 hover:text-blue-800 underline"
                                >
                                    {{ basename($existingAttachment) }}
                                </button>
                            </span>
                        </div>
                    @endif
                </div>


            </div>

            
            <div class="mt-4  mb-1">
                <label for="purpose" class="block text-sm font-medium text-gray-700 dark:text-slate-400">Geolocation <span class="text-red-500">*</span></label>
            </div>
            <div class="flex-col justify-center w-full bg-gray-200 dark:bg-slate-700 border border-gray-300 mb-2" style="border-radius: 8px;">
                <div style="border-radius: 8px 8px 0 0;">
                    <input id="locationSearch" type="text" 
                           placeholder="Search location..." 
                           class="px-2 py-1.5 block w-full shadow-sm sm:text-sm border border-gray-400 hover:bg-gray-300
                                dark:hover:bg-slate-600 dark:border-slate-600
                                dark:text-gray-300 dark:bg-gray-800" style="border-radius: 8px 8px 0 0;"/>
                </div>
                <div wire:ignore class="w-full">
                    <div id="map" style="height: 250px; width: 100%; margin: 0;"></div>
                </div>

                <div class="text-sm flex mt-2 px-4">
                    <div class="w-1/2 mb-2">
                        Lat: <span class="text-gray-800 dark:text-gray-50">{{ $newLatitude ?? '...' }}</span> <br>
                        Lng: <span class="text-gray-800 dark:text-gray-50">{{ $newLongitude ?? '...' }}</span>
                    </div>
                </div>
                @error('newLatitude') 
                    <span class="text-red-500 text-sm">The geolocation is required!</span> 
                @enderror
            </div>

             {{-- Save and Cancel buttons --}}
             <div class="mt-6 flex justify-end col-span-2">
                <button class="mr-2 bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded" wire:click="saveOB">
                    <div wire:loading wire:target="saveOB" style="margin-right: 5px">
                        <div class="spinner-border small text-primary" role="status">
                        </div>
                    </div>
                    Save
                </button>
                <p @click="show = false" class="bg-gray-400 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded cursor-pointer" wire:click='resetVariables'>
                    Cancel
                </p>
            </div>
          
        </div>
    </x-modal>

    {{-- View OB Modal --}}
    <x-modal id="obModal" maxWidth="2xl" wire:model="viewOB">
        <div class="p-4">
            <div class="rounded-lg mb-4 p-4 dark:text-gray-50 text-slate-900 font-bold text-lg">
                Official Business: {{ $company}}
                <button @click="show = false" class="float-right focus:outline-none" wire:click='resetVariables'>
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label for="company" class="block text-sm font-medium text-gray-700 dark:text-slate-400">Company</label>
                    <input type="text" id="company" wire:model='company' class="mt-1 p-2 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:text-gray-300 dark:bg-gray-700" readonly>
                </div>
                <div class="col-span-2">
                    <label for="address" class="block text-sm font-medium text-gray-700 dark:text-slate-400">Address</label>
                    <input type="text" id="address" wire:model='address' class="mt-1 p-2 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:text-gray-300 dark:bg-gray-700" readonly>
                </div>
                <div class="col-span-2">
                    <label for="date" class="block text-sm font-medium text-gray-700 dark:text-slate-400">Date</label>
                    <input type="date" id="date" wire:model='date' class="mt-1 p-2 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:text-gray-300 dark:bg-gray-700" readonly>
                </div>
                <div class="col-span-2 sm:col-span-1">
                    <label for="startTime" class="block text-sm font-medium text-gray-700 dark:text-slate-400">Start Time</label>
                    <input type="time" id="startTime" wire:model='startTime' class="mt-1 p-2 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:text-gray-300 dark:bg-gray-700" readonly>
                </div>
                <div class="col-span-2 sm:col-span-1">
                    <label for="endTime" class="block text-sm font-medium text-gray-700 dark:text-slate-400">End Time</label>
                    <input type="time" id="endTime" wire:model='endTime' class="mt-1 p-2 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:text-gray-300 dark:bg-gray-700" readonly>
                </div>
                <div class="col-span-2">
                    <label for="purpose" class="block text-sm font-medium text-gray-700 dark:text-slate-400">Purpose</label>
                    <textarea type="text" id="purpose" cols="30" rows="4" wire:model='purpose' class="mt-1 p-2 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:text-gray-300 dark:bg-gray-700" readonly></textarea>
                </div>
                <div class="col-span-2 sm:col-span-1">
                    <label for="timeIn" class="block text-sm font-medium text-gray-700 dark:text-slate-400">Time In</label>
                    <input type="time" id="timeIn" wire:model='timeIn' class="mt-1 p-2 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:text-gray-300 dark:bg-gray-700" readonly>
                </div>
                <div class="col-span-2 sm:col-span-1">
                    <label for="timeOut" class="block text-sm font-medium text-gray-700 dark:text-slate-400">Time Out</label>
                    <input type="time" id="timeOut" wire:model='timeOut' class="mt-1 p-2 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:text-gray-300 dark:bg-gray-700" readonly>
                </div>

                @if($supDisapprovedDate == 'N/A')
                    <div class="col-span-2 sm:col-span-1">
                        <label for="approvedBySup" class="block text-sm font-medium text-gray-700 dark:text-slate-400">Approved By (Supervisor)</label>
                        <input type="text" id="approvedBySup" wire:model='approvedBySup' class="mt-1 p-2 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:text-gray-300 dark:bg-gray-700" readonly>
                    </div>
                    <div class="col-span-2 sm:col-span-1">
                        <label for="supApprovedDate" class="block text-sm font-medium text-gray-700 dark:text-slate-400">Approved Date (Supervisor)</label>
                        <input type="text" id="supApprovedDate" wire:model='supApprovedDate' class="mt-1 p-2 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:text-gray-300 dark:bg-gray-700" readonly>
                    </div>
                @endif

                @if($supDisapprovedDate != 'N/A')
                    <div class="col-span-2 sm:col-span-1">
                        <label for="disapprovedBySup" class="block text-sm font-medium text-gray-700 dark:text-slate-400">Dispproved By (Supervisor)</label>
                        <input type="text" id="disapprovedBySup" wire:model='disapprovedBySup' class="mt-1 p-2 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:text-gray-300 dark:bg-gray-700" readonly>
                    </div>
                    <div class="col-span-2 sm:col-span-1">
                        <label for="supDisapprovedDate" class="block text-sm font-medium text-gray-700 dark:text-slate-400">Disapproved Date (Supervisor)</label>
                        <input type="text" id="supDisapprovedDate" wire:model='supDisapprovedDate' class="mt-1 p-2 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:text-gray-300 dark:bg-gray-700" readonly>
                    </div>
                @endif

                @if($disapprovedDate == 'N/A')
                    <div class="col-span-2 sm:col-span-1">
                        <label for="approvedBy" class="block text-sm font-medium text-gray-700 dark:text-slate-400">Approved By (HR)</label>
                        <input type="text" id="approvedBy" wire:model='approvedBy' class="mt-1 p-2 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:text-gray-300 dark:bg-gray-700" readonly>
                    </div>
                    <div class="col-span-2 sm:col-span-1">
                        <label for="approvedDate" class="block text-sm font-medium text-gray-700 dark:text-slate-400">Approved Date (HR)</label>
                        <input type="text" id="approvedDate" wire:model='approvedDate' class="mt-1 p-2 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:text-gray-300 dark:bg-gray-700" readonly>
                    </div>
                @endif

                @if($disapprovedDate != 'N/A')
                    <div class="col-span-2 sm:col-span-1">
                        <label for="disapprovedBy" class="block text-sm font-medium text-gray-700 dark:text-slate-400">Disapproved By (HR)</label>
                        <input type="text" id="disapprovedBy" wire:model='disapprovedBy' class="mt-1 p-2 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:text-gray-300 dark:bg-gray-700" readonly>
                    </div>
                    <div class="col-span-2 sm:col-span-1">
                        <label for="disapprovedDate" class="block text-sm font-medium text-gray-700 dark:text-slate-400">Disapproved Date (HR)</label>
                        <input type="text" id="disapprovedDate" wire:model='disapprovedDate' class="mt-1 p-2 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:text-gray-300 dark:bg-gray-700" readonly>
                    </div>
                @endif

            </div>
   
            <div class="mt-4  mb-1">
                <label for="purpose" class="block text-sm font-medium text-gray-700 dark:text-slate-400">Geolocation</label>
            </div>
            <div class="flex-col justify-center w-full bg-gray-200 dark:bg-slate-700 border border-gray-300 mb-2" style="border-radius: 8px;">
                <div wire:ignore class="w-full">
                    <div id="map3" style="height: 250px; width: 100%; margin: 0;"></div>
                </div>

                <div class="text-sm flex mt-2 px-4">
                    <div class="w-1/2 mb-2">
                        Lat: <span class="text-gray-800 dark:text-gray-50">{{ $registeredLatitude ?? '...' }}</span> <br>
                        Lng: <span class="text-gray-800 dark:text-gray-50">{{ $registeredLongitude ?? '...' }}</span>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex justify-end col-span-2">
                <p @click="show = false" class="bg-gray-400 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded cursor-pointer" wire:click='resetVariables'>
                    Close
                </p>
            </div>
          
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
                Are you sure you want to delete this?
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

    {{-- Confirmation Modal --}}
    <x-modal id="punchConfirmation" maxWidth="md" centered wire:model="showConfirmation">
        <div class="p-4">
            <div class="flex items-center justify-between pb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-200">
                    Punch Confirmation
                </h3>
                <button @click="show = false"
                    class="text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 focus:outline-none">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <div class="space-y-6">
                <p class="text-gray-700 dark:text-gray-300">
                    Are you sure you want to punch {{ $verifyType }}?
                </p>

                <!-- Action Buttons -->
                <div class="mt-6 flex justify-end space-x-4">
                    <button wire:click="recordObAttendance"
                        class="px-4 py-2 rounded-md bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-gray-800">
                        Yes
                    </button>
                    <button @click="show = false"
                        class="px-4 py-2 rounded-md bg-gray-700 hover:bg-gray-800 text-white text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 dark:focus:ring-offset-gray-800">
                        No
                    </button>
                </div>
            </div>
        </div>
    </x-modal>

</div>


<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBvGOC4HUPjiDuOE2yr7CwbnC4j6vsa274&libraries=places"></script>
<script>
    let map, map2, map3, marker, marker3, currentMarker, destinationMarker, searchBox, radiusCircle;

    function initMap() {
        const defaultLocation = { lat: 14.5995, lng: 120.9842 };
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
        marker = new google.maps.Marker({
            position: defaultLocation,
            map: map,
            draggable: true,
            title: 'Your OB Location',
            animation: google.maps.Animation.DROP
        });

        google.maps.event.addListener(marker, 'dragend', function(event) {
            const lat = event.latLng.lat();
            const lng = event.latLng.lng();
            updateLivewireLocation(lat, lng);
        });

        const input = document.getElementById("locationSearch");
        searchBox = new google.maps.places.SearchBox(input);

        map.addListener("bounds_changed", () => {
            searchBox.setBounds(map.getBounds());
        });

        searchBox.addListener("places_changed", () => {
            const places = searchBox.getPlaces();

            if (places.length === 0) return;
            const place = places[0];
            if (!place.geometry || !place.geometry.location) return;

            const location = place.geometry.location;
            const lat = location.lat();
            const lng = location.lng();
            map.setCenter(location);
            marker.setPosition(location);
            updateLivewireLocation(lat, lng);
        });
    }

    function updateLivewireLocation(lat, lng) {
        @this.set('newLatitude', lat);
        @this.set('newLongitude', lng);
    }


    const createAnimatedMarkerIcon = () => {
        const svg = `
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="-20 -20 40 40">
                <circle cx="0" cy="0" r="8" fill="#4286f4c7">
                    <animate
                        attributeName="r"
                        values="8;16;8"
                        dur="2s"
                        repeatCount="indefinite"
                        begin="0s"
                    />
                    <animate
                        attributeName="fill-opacity"
                        values="0.3;0.1;0.3"
                        dur="2s"
                        repeatCount="indefinite"
                        begin="0s"
                    />
                </circle>
                <!-- Main circle -->
                <circle cx="0" cy="0" r="8" fill="#4285F4" fill-opacity="0.7" stroke="white" stroke-width="1" />
            </svg>
        `;
    
        return {
            url: 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent(svg),
            scaledSize: new google.maps.Size(40, 40),
            anchor: new google.maps.Point(20, 20),
            origin: new google.maps.Point(0, 0)
        };
    };

    function initMap2() {
        const destination = { lat: {{ $ongoingObs ? $ongoingObs->lat : 0.00 }}, lng: {{ $ongoingObs ? $ongoingObs->lng : 0.00 }} };
        const lat = @this.latitude;
        const lng = @this.longitude;
        const currentLocation = { lat: parseFloat(lat), lng: parseFloat(lng) };

        if (!map2) {
            map2 = new google.maps.Map(document.getElementById("map2"), {
                mapTypeControl: false,
                streetViewControl: false,
                fullscreenControl: true,
                zoomControl: true,
                styles: [
                    {
                        featureType: "poi",
                        elementType: "labels",
                        stylers: [{ visibility: "off" }],
                    },
                ],
            });
        }

        if (currentMarker) {
            currentMarker.setPosition(currentLocation);
        } else {
            currentMarker = new google.maps.Marker({
                position: currentLocation,
                map: map2,
                title: 'Your Location',
                icon: createAnimatedMarkerIcon()
            });
        }

        destinationMarker = new google.maps.Marker({
            position: destination,
            map: map2,
            title: 'OB Location',
            icon: 'http://maps.google.com/mapfiles/ms/icons/red-dot.png',
        });

        if (radiusCircle) {
            radiusCircle.setMap(null);
        }

        radiusCircle = new google.maps.Circle({
            strokeColor: "#FF0000",
            strokeOpacity: 0.5,
            strokeWeight: 1,
            fillColor: "#FF0000",
            fillOpacity: 0.1,
            map: map2,
            center: destination,
            radius: 300
        });

        const bounds = new google.maps.LatLngBounds();
    
        bounds.extend(currentLocation);
        bounds.extend(destination);
        
        map2.fitBounds(bounds);
        bounds.union(radiusCircle.getBounds());
        
        const padding = {
            top: 50,
            right: 50,
            bottom: 50,
            left: 50
        };
        map2.fitBounds(bounds, padding);
    }

    function initMap3() {
        let lat, lng;
        if (@this.registeredLatitude && @this.registeredLongitude) {
            lat = parseFloat(@this.registeredLatitude);
            lng = parseFloat(@this.registeredLongitude);
        } else {
            lat = 14.5995;
            lng = 120.9842;
        }
        const obLocation = { lat, lng };

        if (!map3) {
            map3 = new google.maps.Map(document.getElementById("map3"), {
                zoom: 15,
                center: obLocation,
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

        map3.setCenter(obLocation);
        if (!marker3) {
            marker3 = new google.maps.Marker({
                position: obLocation,
                map: map3,
                draggable: false,
                title: 'Your OB Location',
                animation: google.maps.Animation.DROP
            });
        } else {
            marker3.setPosition(obLocation);
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        initMap();
        initMap2();
        initMap3();
    });

    setInterval(initMap2 , 5000);
    setInterval(initMap3 , 5000);


    function resizeIframe() {
        const iframe = document.getElementById('pdfIframe');
        const pdfDocument = iframe.contentDocument || iframe.contentWindow.document;

        if (pdfDocument) {
            iframe.style.height = pdfDocument.body.scrollHeight + 'px';
        }
    }

    document.getElementById('pdfIframe').onload = resizeIframe;
    window.onresize = resizeIframe;
</script>