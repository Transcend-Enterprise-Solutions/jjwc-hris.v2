<div class="w-full">
    
    <div class="w-full flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 flex items-center">
            <i class="bi bi-briefcase-fill mr-2 text-blue-600"></i>
            Job Details
        </h3>

        <button wire:click="toggleEditPosition({{ $selectedUser->id }})"
                class="px-2 py-1.5 bg-blue-500 text-white rounded-md text-xs
                hover:bg-blue-600 focus:outline-none dark:bg-gray-700
                dark:hover:bg-blue-600 dark:text-gray-300 dark:hover:text-white">
            <i class="bi bi-pencil mr-2"></i> Edit
        </button>
    </div>

    <div class="mt-6 grid grid-cols-1 gap-3">
        <div class="col-span-full flex items-start justify-start   gap-2 text-xs">
            <span class="font-medium text-gray-600 dark:text-gray-400 w-32 flex-shrink-0">Employee Number:</span>
            <span class="text-gray-900 dark:text-gray-100">{{ $selectedUser->emp_code }}</span>
        </div>
        <div class="col-span-full flex items-start justify-start   gap-2 text-xs">
            <span class="font-medium text-gray-600 dark:text-gray-400 w-32 flex-shrink-0">Position:</span>
            <span class="text-gray-900 dark:text-gray-100">{{ $selectedUser->position ? $selectedUser->position->position : '' }}</span>
        </div>
        <div class="col-span-full flex items-start justify-start   gap-2 text-xs">
            <span class="font-medium text-gray-600 dark:text-gray-400 w-32 flex-shrink-0">Office/Division:</span>
            <span class="text-gray-900 dark:text-gray-100">{{ $selectedUser->officeDivision ? $selectedUser->officeDivision->office_division : '' }}</span>
        </div>
        <div class="col-span-full flex items-start justify-start   gap-2 text-xs">
            <span class="font-medium text-gray-600 dark:text-gray-400 w-32 flex-shrink-0">Unit:</span>
            <span class="text-gray-900 dark:text-gray-100">{{ $selectedUser->officeDivisionUnit ? $selectedUser->officeDivisionUnit->unit : '' }}</span>
        </div>
        <div class="col-span-full flex items-start justify-start gap-2 text-xs">
            <span class="font-medium text-gray-600 dark:text-gray-400 w-32 flex-shrink-0">Appointment:</span>
            <span class="text-gray-900 dark:text-gray-100 uppercase">{{ $selectedUser->userData->appointment ?: 'Not provided' }}</span>
        </div>
        <div class="col-span-full flex items-start justify-start gap-2 text-xs">
            <span class="font-medium text-gray-600 dark:text-gray-400 w-32 flex-shrink-0">Date Hired:</span>
            <span class="text-gray-900 dark:text-gray-100">{{ $selectedUser->userData->date_hired ? \Carbon\Carbon::parse($selectedUser->userData->date_hired)->format('F d, Y') : 'Not provided' }}</span>
        </div>      
        <div class="col-span-full flex items-start justify-start gap-2 text-xs">
            <span class="font-medium text-gray-600 dark:text-gray-400 flex-shrink-0">Employment Status:</span>
            <span class="inline-block px-3 py-1 text-xs font-semibold
                {{ $selectedUser->active_status == 0 ? 'text-red-800 bg-red-200' : '' }}
                {{ $selectedUser->active_status == 1 ? 'text-green-800 bg-green-200' : '' }}
                {{ $selectedUser->active_status == 2 ? 'text-yellow-800 bg-yellow-200' : '' }}
                {{ $selectedUser->active_status == 3 ? 'text-purple-800 bg-purple-200' : '' }}
                    rounded-full">
                @if($selectedUser->active_status == 0)
                    Inactive
                @elseif($selectedUser->active_status == 1)
                    Active
                @elseif($selectedUser->active_status == 2)
                    Resigned
                @elseif($selectedUser->active_status == 3)
                    Retired
                @endif
            </span>
        </div>
    </div>

    <div class="w-full mt-8 flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <p class="text-sm font-semibold text-gray-600 dark:text-gray-300 mb-3 flex items-center">
            <i class="fas fa-handshake mr-2"></i>
            Employment Contract Details
        </p>
        <button wire:click="addNewContract({{ $selectedUser->id }})"
                class="px-2 py-1.5 bg-green-500 text-white rounded-md text-xs
                hover:bg-green-600 focus:outline-none dark:bg-gray-700
                dark:hover:bg-green-600 dark:text-gray-300 dark:hover:text-white">
            <i class="bi bi-plus-lg mr-2"></i> Add Contract
        </button>
    </div>  

    
    {{-- Active Contracts Section --}}
    @if($selectedUser->active_contracts && $selectedUser->active_contracts->count() > 0)
        <div class="my-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4 flex items-center">
                <span class="w-3 h-3 bg-green-500 rounded-full mr-2"></span>
                Active Contracts ({{ $selectedUser->active_contracts->count() }})
            </h3>
            
            @foreach($selectedUser->active_contracts as $contract)
                <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 rounded-lg p-4 mb-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex items-start justify-start gap-2 text-xs">
                            <span class="font-medium text-gray-600 dark:text-gray-400 w-32 flex-shrink-0">Contract Number:</span>
                            <span class="text-gray-900 dark:text-gray-100">
                                {{ $contract->contract_number ?: 'N/A' }}
                            </span>
                        </div>
                        
                        <div class="flex items-start justify-start gap-2 text-xs">
                            <span class="font-medium text-gray-600 dark:text-gray-400 w-32 flex-shrink-0">Contract Type:</span>
                            <span class="text-gray-900 dark:text-gray-100">
                                {{ $contract->contract_type ?: 'N/A' }}
                            </span>
                        </div>
                        
                        <div class="flex items-start justify-start gap-2 text-xs">
                            <span class="font-medium text-gray-600 dark:text-gray-400 w-32 flex-shrink-0">Start Date:</span>
                            <span class="text-gray-900 dark:text-gray-100">
                                {{ $contract->start_date ? $contract->start_date->format('M d, Y') : 'N/A' }}
                            </span>
                        </div>
                        
                        <div class="flex items-start justify-start gap-2 text-xs">
                            <span class="font-medium text-gray-600 dark:text-gray-400 w-32 flex-shrink-0">End Date:</span>
                            <span class="text-gray-900 dark:text-gray-100">
                                {{ $contract->end_date ? $contract->end_date->format('M d, Y') : 'Permanent' }}
                            </span>
                        </div>
                        
                        @if($contract->contract_details)
                            <div class="col-span-full flex items-start justify-start gap-2 text-xs">
                                <span class="font-medium text-gray-600 dark:text-gray-400 w-32 flex-shrink-0">Contract File:</span>
                                <button wire:click="downloadContractDocument({{ $contract->id }})" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-200 underline">
                                    {{ basename($contract->contract_details) }}
                                </button>
                            </div>
                        @endif

                        <div class="flex items-start justify-start gap-2 text-xs">
                             <div class="flex items-center justify-center space-x-2">
                                <button wire:click="editContractModal({{ $contract->id }})"
                                        class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-200" 
                                        title="Edit Contract">
                                    <i class="fas fa-pencil-alt"></i>
                                </button>
                                <button wire:click="toggleDelete({{ $contract->id }})"
                                        class="text-sm text-red-600 hover:text-red-900 dark:text-red-600 dark:hover:text-red-900" 
                                        title="Delete Contract">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Expired/Inactive Contracts Section --}}
    @if($selectedUser->expired_contracts && $selectedUser->expired_contracts->count() > 0)
        <div class="mb-6">
            <h3 class="text-md font-semibold text-gray-900 dark:text-gray-100 mb-4 flex items-center">
                <span class="w-3 h-3 bg-red-500 rounded-full mr-2"></span>
                Expired Contracts ({{ $selectedUser->expired_contracts->count() }})
            </h3>
            
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-full">
                        <thead class="bg-gray-200 dark:bg-gray-700 rounded-xl">
                            <tr class="whitespace-nowrap">
                                <th scope="col" class="px-5 py-3 text-xs font-medium text-left uppercase">
                                    Contract Number
                                </th>
                                <th scope="col" class="px-5 py-3 text-xs font-medium text-center uppercase">
                                    Contract Type
                                </th>
                                <th scope="col" class="px-5 py-3 text-xs font-medium text-center uppercase">
                                    Start Date
                                </th>
                                <th scope="col" class="px-5 py-3 text-xs font-medium text-center uppercase">
                                    End Date
                                </th>
                                <th scope="col" class="px-5 py-3 text-xs font-medium text-center uppercase">
                                    Document
                                </th>
                                <th class="px-5 py-3 text-xs font-medium text-center uppercase sticky right-0 z-10">
                                    Action
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-700">
                            @foreach($selectedUser->expired_contracts as $contract)
                                <tr class="text-neutral-800 dark:text-neutral-200">
                                    <td class="px-5 py-4 text-left text-xs font-medium whitespace-nowrap">
                                        <div class="flex items-center">
                                            <span class="w-2 h-2 bg-red-500 rounded-full mr-2"></span>
                                            {{ $contract->contract_number ?: 'N/A' }}
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 text-center text-xs font-medium whitespace-nowrap">
                                        <span class="capitalize">{{ $contract->contract_type ?: 'N/A' }}</span>
                                    </td>
                                    <td class="px-5 py-4 text-center text-xs font-medium whitespace-nowrap">
                                        {{ $contract->start_date ? $contract->start_date->format('M d, Y') : 'N/A' }}
                                    </td>
                                    <td class="px-5 py-4 text-center text-xs font-medium whitespace-nowrap">
                                        {{ $contract->end_date ? $contract->end_date->format('M d, Y') : 'N/A' }}
                                    </td>
                                    <td class="px-5 py-4 text-center text-xs font-medium whitespace-nowrap">
                                        @if($contract->contract_details)
                                            <button wire:click="downloadContractDocument({{ $contract->id }})" 
                                                    class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-200" 
                                                    title="Download Document">
                                                <i class="fas fa-file-download mr-2"></i> {{ basename($contract->contract_details) }}
                                            </button>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 text-sm font-medium text-center whitespace-nowrap sticky right-0 z-10 bg-white dark:bg-gray-800">
                                        <div class="flex items-center justify-center space-x-2">
                                            <button wire:click="editContractModal({{ $contract->id }})"
                                                    class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-200" 
                                                    title="Edit Contract">
                                                <i class="fas fa-pencil-alt"></i>
                                            </button>
                                            <button wire:click="toggleDelete({{ $contract->id }})"
                                                    class="text-sm text-red-600 hover:text-red-900 dark:text-red-600 dark:hover:text-red-900" 
                                                    title="Delete Contract">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                @if ($selectedUser->expired_contracts->isEmpty())
                    <div class="p-4 text-center text-gray-500 dark:text-gray-300">
                        No expired contracts found!
                    </div>
                @endif
            </div>
        </div>
    @endif

    {{-- No Contracts Message --}}
    @if((!$selectedUser->active_contracts || $selectedUser->active_contracts->count() === 0) && 
        (!$selectedUser->expired_contracts || $selectedUser->expired_contracts->count() === 0))
        <div class="text-center py-8">
            <div class="text-gray-400 dark:text-gray-600">
                <svg class="w-12 h-12 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <p class="text-sm font-medium">No contracts found for this employee.</p>
            </div>
        </div>
    @endif
    

    {{-- Add and Edit Contract Modal --}}
    <x-modal id="contractModal" maxWidth="4xl" wire:model="editContract">
        <div class="p-4">
            <div class="mb-4 py-4 dark:text-gray-50 text-slate-900 font-bold text-lg">
                {{ $addContract ? 'Add' : 'Edit' }} Employee Contract
                <button @click="show = false" class="float-right focus:outline-none" wire:click="resetVariables">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            {{-- Form Fields --}}
            <form wire:submit.prevent="saveContract">
                <div class="grid grid-cols-2 gap-4">
                    
                    <div class="col-span-full sm:col-span-1">
                        <label for="contract_number" class="block text-xs font-medium text-gray-700 dark:text-slate-400">Contract Number <span class="text-red-500">*</span></label>
                        <input type="text" id="contract_number" wire:model="contract_number" class="mt-1 p-2 block w-full shadow-sm sm:text-xs border-gray-300 rounded-md dark:text-gray-300 dark:bg-gray-700">
                        @error('contract_number') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-span-full sm:col-span-1">
                        <label for="contract_type" class="block text-xs font-medium text-gray-700 dark:text-slate-400">Contract Type <span class="text-red-500">*</span></label>
                        <select id="contract_type" wire:model="contract_type" class="mt-1 p-2 block w-full shadow-sm sm:text-xs border-gray-300 rounded-md dark:text-gray-300 dark:bg-gray-700">
                            <option value="">Select Contract Type</option>
                            <option value="permanent">Permanent</option>
                            <option value="temporary">Temporary</option>
                            <option value="fixed_term">Fixed Term</option>
                            <option value="part_time">Part Time</option>
                            <option value="internship">Internship</option>
                            <option value="probationary">Probationary</option>
                            <option value="consultant">Consultant</option>
                        </select>
                        @error('contract_type') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-span-full sm:col-span-1">
                        <label for="start_date" class="block text-xs font-medium text-gray-700 dark:text-slate-400">Start Date <span class="text-red-500">*</span></label>
                        <input type="date" id="start_date" wire:model="start_date" class="mt-1 p-2 block w-full shadow-sm sm:text-xs border-gray-300 rounded-md dark:text-gray-300 dark:bg-gray-700">
                        @error('start_date') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-span-full sm:col-span-1">
                        <label for="end_date" class="block text-xs font-medium text-gray-700 dark:text-slate-400">End Date (Optional)</label>
                        <input type="date" id="end_date" wire:model="end_date" class="mt-1 p-2 block w-full shadow-sm sm:text-xs border-gray-300 rounded-md dark:text-gray-300 dark:bg-gray-700">
                        <small class="text-xs text-gray-500 dark:text-gray-400">Leave empty for permanent contracts</small>
                        @error('end_date') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-span-full sm:col-span-1">
                        <label for="status" class="block text-xs font-medium text-gray-700 dark:text-slate-400">Status <span class="text-red-500">*</span></label>
                        <select id="status" wire:model="status" class="mt-1 p-2 block w-full shadow-sm sm:text-xs border-gray-300 rounded-md dark:text-gray-300 dark:bg-gray-700">
                            <option value="">Select Status</option>
                            <option value="active">Active</option>
                            <option value="terminated">Terminated</option>
                            <option value="expired">Expired</option>
                            <option value="suspended">Suspended</option>
                            <option value="pending">Pending</option>
                        </select>
                        @error('status') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-span-full sm:col-span-1">
                        <label for="date_created" class="block text-xs font-medium text-gray-700 dark:text-slate-400">Date Created <span class="text-red-500">*</span></label>
                        <input type="date" id="date_created" wire:model="date_created" class="mt-1 p-2 block w-full shadow-sm sm:text-xs border-gray-300 rounded-md dark:text-gray-300 dark:bg-gray-700">
                        @error('date_created') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-span-full">
                        <label for="contract_details" class="block text-xs font-medium text-gray-700 dark:text-slate-400">Contract Document (Optional)</label>

                        <div 
                            x-data="{ 
                                isDragging: false,
                                handleDrop(event) {
                                    this.isDragging = false;
                                    const files = event.dataTransfer.files;
                                    if (files.length > 0) {
                                        // Get the file input element
                                        const fileInput = document.getElementById('contract_details');
                                        // Create a new FileList-like object
                                        const dt = new DataTransfer();
                                        dt.items.add(files[0]);
                                        fileInput.files = dt.files;
                                        
                                        // Trigger the change event to notify Livewire
                                        fileInput.dispatchEvent(new Event('change', { bubbles: true }));
                                    }
                                }
                            }"
                            x-on:dragover.prevent="isDragging = true"
                            x-on:dragleave.prevent="isDragging = false"
                            x-on:drop.prevent="handleDrop($event)"
                            x-bind:class="isDragging ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-300 dark:border-gray-600'"
                            class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-dashed rounded-md cursor-pointer transition-all duration-200 ease-in-out dark:bg-gray-800"
                        >
                            <div class="space-y-1 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-200" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v24a4 4 0 004 4h24a4 4 0 004-4V20l-12-12z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M28 8v12h12" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                <div class="flex text-xs text-gray-600 dark:text-gray-300">
                                    <label for="contract_details" class="relative cursor-pointer bg-gray-100 rounded-md font-medium text-blue-600 hover:text-blue-500 dark:bg-gray-700 dark:text-blue-300">
                                        <span class="px-4">Upload contract document</span>
                                        <input id="contract_details" name="contract_details" type="file" wire:model="contract_details" class="sr-only" accept=".pdf,.png,.jpg,.jpeg,.doc,.docx">
                                    </label>
                                    <p class="pl-1">or drag and drop</p>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">PDF, DOC, PNG, JPG up to 5MB</p>

                                @if ($contract_details)
                                    <p class="text-xs text-green-600 dark:text-green-400 mt-2">
                                        Selected: {{ $contract_details->getClientOriginalName() }}
                                    </p>
                                @elseif (is_string($oldContractDetails) && $oldContractDetails)
                                    <p class="text-xs text-blue-600 dark:text-blue-400 mt-2">
                                        @if (Storage::disk('public')->exists($oldContractDetails))
                                            Existing file: 
                                            <p wire:click="downloadContractDocument({{ $contractId }})" class="text-blue-500 hover:underline cursor-pointer">
                                                {{ basename($oldContractDetails) }}
                                            </p>
                                        @endif
                                    </p>
                                @endif
                                
                                <div wire:loading wire:target="contract_details" class="text-blue-500 mt-2 text-xs">
                                    Uploading file...
                                </div>

                                @error('contract_details') 
                                    <span class="text-red-500 text-xs">{{ $message }}</span> 
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 flex justify-end col-span-full">
                        <button 
                            class="mr-2 bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded disabled:opacity-50 disabled:cursor-not-allowed"
                            type="submit"
                            wire:loading.attr="disabled"
                            wire:target="saveContract,contract_details"
                        >
                            <div wire:loading wire:target="saveContract" class="spinner-border small text-white" role="status"></div>
                            Save Contract
                        </button>

                        <p @click="show = false" class="bg-gray-400 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded cursor-pointer" wire:click='resetVariables'>
                            Cancel
                        </p>
                    </div>
                </div>
            </form>
        </div>
    </x-modal>

    {{-- Add and Edit Employee Pos Modal --}}
    <x-modal id="empModal" maxWidth="2xl" wire:model="editPosition" centered>
        <div class="p-4">
            <div class="mb-4 py-4 dark:text-gray-50 text-slate-900 font-bold text-lg">
                Edit Job Details
                <button @click="show = false" class="float-right focus:outline-none" wire:click='resetVariables'>
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form wire:submit.prevent='savePosition'>
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-full sm:col-span-1">
                        <label for="employeeId" class="block text-sm font-medium text-gray-700 dark:text-slate-400">Employee Number <span class="text-red-500">*</span></label>
                        <input type="text" id="employeeId" wire:model='employeeId' class="mt-1 p-2 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:text-gray-300 dark:bg-gray-700">
                        @error('employeeId')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-span-full sm:col-span-1">
                        <label for="officeDivisionId" class="block text-sm font-medium text-gray-700 dark:text-slate-400">Office/Division <span class="text-red-500">*</span></label>
                        <select id="officeDivisionId" wire:model.live='officeDivisionId' class="mt-1 p-2 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:text-gray-300 dark:bg-gray-700">
                            <option class="text-gray-300" value="">-- Select --</option>
                            @foreach($officeDivisions as $office)
                                <option value="{{ $office->id }}">{{ $office->office_division }}</option>
                            @endforeach
                        </select>
                        @error('officeDivisionId')
                            <span class="text-red-500 text-sm">Please select office/division!</span>
                        @enderror
                    </div>

                    <div class="col-span-full sm:col-span-1">
                        <label for="unitId" class="block text-sm font-medium text-gray-700 dark:text-slate-400">Unit (Optional)</label>
                        <select id="unitId" wire:model.live='unitId' class="mt-1 p-2 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:text-gray-300 dark:bg-gray-700">
                            @if($divsUnits)
                                <option class="text-gray-300" value="">-- Select --</option>
                                @foreach($divsUnits as $u)
                                    <option value="{{ $u->id }}">{{ $u->unit }}</option>
                                @endforeach
                            @else
                                <option class="text-gray-300" value="">-- Select --</option>
                            @endif
                        </select>
                    </div>

                    <div class="col-span-full sm:col-span-1">
                        <label for="positionId" class="block text-sm font-medium text-gray-700 dark:text-slate-400">Position <span class="text-red-500">*</span></label>
                        <select id="positionId" wire:model='positionId' class="mt-1 p-2 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:text-gray-300 dark:bg-gray-700">
                            <option value="">-- Select --</option>
                            @foreach ($positions as $pos)
                                <option value="{{ $pos->id }}">{{ $pos->position }}</option>
                            @endforeach
                        </select>
                        @error('positionId')
                            <span class="text-red-500 text-sm">Please select position!</span>
                        @enderror
                    </div>

                    <div class="col-span-full sm:col-span-1">
                        <label for="appointment" class="block text-sm font-medium text-gray-700 dark:text-slate-400">Appoinment <span class="text-red-500">*</span></label>
                        <select id="appointment" wire:model='appointment' class="mt-1 p-2 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:text-gray-300 dark:bg-gray-700">
                            <option value="">-- Select --</option>
                            @foreach ($appointments as $appointment)
                                <option value="{{ $appointment->appointment }}">{{ $appointment->appointment }}</option>
                            @endforeach
                        </select>
                        @error('appointment')
                            <span class="text-red-500 text-sm">Please select an appointment!</span>
                        @enderror
                    </div>

                    <div class="col-span-full sm:col-span-1">
                        <label for="employmentStatus" class="block text-sm font-medium text-gray-700 dark:text-slate-400">Employment Status <span class="text-red-500">*</span></label>
                        <select id="employmentStatus" wire:model.live='employmentStatus' class="mt-1 p-2 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:text-gray-300 dark:bg-gray-700">
                            <option value="">-- Select --</option>
                            @foreach ($statuses as $status)
                                <option value="{{ $status->id }}">{{ $status->employment_status }}</option>
                            @endforeach
                        </select>
                        @error('employmentStatus')
                            <span class="text-red-500 text-sm">Please select an employment status!</span>
                        @enderror
                    </div>

                    {{-- @if($employmentStatus == 'Separated' || $employmentStatus == 'Suspended')
                        <div class="col-span-full">
                            <label for="statusRemarks" class="block text-sm font-medium text-gray-700 dark:text-slate-400">Status Remarks <span class="text-red-500">*</span></label>
                            <input type="text" id="statusRemarks" wire:model='statusRemarks' class="mt-1 p-2 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:text-gray-300 dark:bg-gray-700">
                            @error('statusRemarks')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                    @endif --}}


                    <div class="mt-4 flex justify-end col-span-2">
                        <button class="mr-2 bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                            <div wire:loading wire:target="savePosition" class="spinner-border small text-primary" role="status">
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
                Are you sure you want to delete this record?
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

</div>
