<div class="w-full overflow-hidden relative"
x-data="{
    showAddEdit: true,
}" x-cloak>

    <!-- Header -->
    <div class="flex justify-between flex-wrap bg-gray-100 dark:bg-gray-700 px-6 py-4">
        <h2 class="text-xl font-bold dark:text-white">Government Issued ID</h2>

        @if($govId)
            <div class="flex gap-2">
                <button wire:click="toggleEditGovId" @click="showAddEdit = false"
                        x-show="showAddEdit === true"
                        class="inline-flex items-center px-4 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-lg shadow-sm transition-colors duration-200 focus:outline-none">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Edit
                </button>
            </div>
        @endif

        @if($editGovId)
            <div class="flex gap-2">
                <button wire:click="saveGovId" @click="showAddEdit = true"
                        class="inline-flex items-center px-3 py-1 bg-green-600 hover:bg-green-700 text-white text-xs font-medium rounded-md shadow-sm transition-colors duration-200">
                    Save
                </button>
                <button wire:click="toggleEditGovId" @click="showAddEdit = true"
                        class="inline-flex items-center px-3 py-1 bg-gray-600 hover:bg-gray-700 text-white text-xs font-medium rounded-md shadow-sm transition-colors duration-200">
                    Cancel
                </button>
            </div>
        @endif
    </div>


    <div class="p-6">
        @if($govId || $idNumber || $dateIssued || $editGovId)
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg p-4 shadow-sm {{ $editGovId ? '!border-blue-500' : '' }}">
                <div class="w-full flex justify-end">
                    <button wire:click="toggleDelete"
                        class="text-red-500 hover:text-red-700 text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                </div>

                <p class="text-xs text-gray-500 dark:text-gray-400 mb-4 italic">
                    (i.e. Passport, GSIS, SSS, PRC, Driver's License, etc.) - Please indicate ID Number
                </p>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="form-group col-span-full">
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Government Issued ID
                        </label>
                        @if(!$editGovId)
                            <div class="px-3 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-md text-gray-900 dark:text-gray-100">
                                <span class="text-sm">{{ $govId ?: '--' }}</span>
                            </div>
                        @else
                            <input wire:model="govId" type="text"
                                class="text-sm w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                placeholder="e.g., SSS, GSIS, Driver's License">
                            @error('govId') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        @endif
                    </div>

                    <div class="form-group">
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">
                            ID/License/Passport No.
                        </label>
                        @if(!$editGovId)
                            <div class="px-3 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-md text-gray-900 dark:text-gray-100">
                                <span class="text-sm">{{ $idNumber ?: '--' }}</span>
                            </div>
                        @else
                            <input wire:model="idNumber" type="text"
                                class="text-sm w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                placeholder="Enter ID number">
                            @error('idNumber') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        @endif
                    </div>

                    <div class="form-group">
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Date/Place of Issuance
                        </label>
                        @if(!$editGovId)
                            <div class="px-3 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-md text-gray-900 dark:text-gray-100">
                                <span class="text-sm">{{ $dateIssued ?: '--' }}</span>
                            </div>
                        @else
                            <input wire:model="dateIssued" type="text"
                                class="text-sm w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                placeholder="e.g., Jan 15, 2020 / Manila">
                            @error('dateIssued') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        @endif
                    </div>
                </div>
            </div>
        @elseif(!$govId && !$idNumber && !$dateIssued && !$editGovId)
            <div class="text-center py-8 bg-gray-50 dark:bg-gray-800 rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-600">
                <svg wire:click="toggleEditGovId" class="cursor-pointer w-12 h-12 mx-auto text-gray-400 hover:text-green-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <p class="text-gray-500 dark:text-gray-400">No government ID information added</p>
            </div>
        @endif
    </div>

    {{-- Delete Modal --}}
    <x-modal id="deleteModal" maxWidth="md" wire:model="delete" centered>
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
                    <p @click="show = false"
                        class="bg-gray-400 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded cursor-pointer">
                        Cancel
                    </p>
                </div>
            </form>
        </div>
    </x-modal>


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
