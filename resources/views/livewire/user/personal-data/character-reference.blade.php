<div class="w-full overflow-hidden relative"
x-data="{
    showAddEdit: true,
}" x-cloak>

    <div class="flex justify-between flex-wrap bg-gray-100 dark:bg-gray-700 px-6 py-4">
        <h2 class="text-xl font-bold dark:text-white">Character References</h2>

        @if(!$addReferences && !$editReferences)
            <div class="flex gap-2">
                <button wire:click="toggleAddReferences" 
                        @click="showAddEdit = false"
                        x-show="showAddEdit === true"
                        class="inline-flex items-center px-4 py-1 bg-green-600 hover:bg-green-700 text-white text-xs font-medium rounded-lg shadow-sm transition-colors duration-200 focus:outline-none">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Add
                </button>
                @if($references && $references->isNotEmpty())
                    <button wire:click="toggleEditReferences" @click="showAddEdit = false"
                            x-show="showAddEdit === true"
                            class="inline-flex items-center px-4 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-lg shadow-sm transition-colors duration-200 focus:outline-none">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit All
                    </button>
                @endif
            </div>
        @endif
        
        @if($editReferences && !$addReferences)
            <div class="flex gap-2">
                <button wire:click="saveReferences" 
                        @click="showAddEdit = true"
                        class="inline-flex items-center px-3 py-1 bg-green-600 hover:bg-green-700 text-white text-xs font-medium rounded-md shadow-sm transition-colors duration-200">
                    Save
                </button>
                <button wire:click="cancelEdit" @click="showAddEdit = true"
                        class="inline-flex items-center px-3 py-1 bg-gray-600 hover:bg-gray-700 text-white text-xs font-medium rounded-md shadow-sm transition-colors duration-200">
                    Cancel
                </button>
            </div>
        @endif
    </div>

    <div class="p-6">
        @if(!$editReferences && !$addReferences)
            {{-- Display Mode --}}
            @if($references && $references->isNotEmpty())
                <div class="space-y-4">
                    @foreach($references as $reference)
                        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg p-4 shadow-sm">
                            <div class="w-full flex justify-end">
                                <button wire:click="toggleDelete({{ $reference->id }})"
                                        class="text-red-500 hover:text-red-700 text-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>

                            <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-4 gap-4">
                                <div class="form-group">
                                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">Full Name</label>
                                    <div class="px-3 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-md text-gray-900 dark:text-gray-100">
                                        <span class="text-sm">
                                            {{ $reference->firstname }}
                                            {{ $reference->middle_initial ? $reference->middle_initial . '.' : '' }}
                                            {{ $reference->surname }}
                                        </span>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">Address</label>
                                    <div class="px-3 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-md text-gray-900 dark:text-gray-100">
                                        <span class="text-sm">{{ $reference->address ?: '--' }}</span>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">Telephone Number</label>
                                    <div class="px-3 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-md text-gray-900 dark:text-gray-100">
                                        <span class="text-sm">{{ $reference->tel_number ?: '--' }}</span>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">Mobile Number</label>
                                    <div class="px-3 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-md text-gray-900 dark:text-gray-100">
                                        <span class="text-sm">{{ $reference->mobile_number ?: '--' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 bg-gray-50 dark:bg-gray-800 rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-600">
                    <svg wire:click="toggleAddReferences" class="cursor-pointer w-12 h-12 mx-auto text-gray-400 hover:text-green-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <p class="text-gray-500 dark:text-gray-400">No character references added</p>
                    <p class="text-sm text-gray-400 dark:text-gray-500 mt-2">Click the plus icon to add a reference</p>
                </div>
            @endif
        @else
            {{-- Edit/Add Mode --}}
            <div class="space-y-4">
                {{-- Existing References (Edit Mode) --}}
                @if($editReferences && !$addReferences && count($myReferences) > 0)
                    @foreach($myReferences as $index => $reference)
                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 {{ $index > 0 ? 'mt-4' : '' }}">
                            <div class="flex justify-between items-center mb-4">
                                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">Reference #{{ $index + 1 }}</h4>
                            </div>

                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                                <div class="form-group">
                                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">First Name</label>
                                    <input wire:model="myReferences.{{ $index }}.firstname" type="text"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                        placeholder="Enter first name">
                                    @error('myReferences.' . $index . '.firstname')
                                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">Middle Initial</label>
                                    <input wire:model="myReferences.{{ $index }}.middle_initial" type="text" maxlength="1"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                        placeholder="M">
                                    @error('myReferences.' . $index . '.middle_initial')
                                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">Surname</label>
                                    <input wire:model="myReferences.{{ $index }}.surname" type="text"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                        placeholder="Enter surname">
                                    @error('myReferences.' . $index . '.surname')
                                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">Address</label>
                                    <input wire:model="myReferences.{{ $index }}.address" type="text"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                        placeholder="Enter address">
                                    @error('myReferences.' . $index . '.address')
                                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">Telephone Number</label>
                                    <input wire:model="myReferences.{{ $index }}.tel_number" type="number"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                        placeholder="Enter telephone number">
                                    @error('myReferences.' . $index . '.tel_number')
                                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">Mobile Number</label>
                                    <input wire:model="myReferences.{{ $index }}.mobile_number" type="number"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                        placeholder="Enter mobile number">
                                    @error('myReferences.' . $index . '.mobile_number')
                                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif

                {{-- New Reference (Add Mode) --}}
                @if($addReferences && count($myNewReferences) > 0)
                    <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm overflow-hidden">
                        {{-- Form Header --}}
                        <div class="bg-green-50 dark:bg-green-900/20 px-4 py-3">
                            <div class="flex justify-between items-center">
                                <h3 class="text-md font-semibold text-green-800 dark:text-green-200">
                                    Add New Character Reference
                                </h3>
                                <div class="flex justify-end space-x-4">
                                    <button wire:click="saveReferences" @click="showAddEdit = true"
                                        class="text-xs px-4 py-1 bg-green-600 hover:bg-green-700 text-white rounded-md focus:outline-none">
                                        Save
                                    </button>
                                    <button wire:click="cancelEdit" @click="showAddEdit = true"
                                            class="text-xs px-4 py-1 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-50 dark:hover:bg-gray-800 focus:outline-none">
                                        Cancel
                                    </button>
                                </div>
                            </div>
                        </div>

                        @foreach($myNewReferences as $index => $newReference)
                            <div class="p-6">
                                <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-4">
                                    <div class="form-group">
                                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">First Name</label>
                                        <input wire:model="myNewReferences.{{ $index }}.firstname" type="text"
                                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                            placeholder="Enter first name">
                                        @error('myNewReferences.' . $index . '.firstname')
                                            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">Middle Initial</label>
                                        <input wire:model="myNewReferences.{{ $index }}.middle_initial" type="text" maxlength="1"
                                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                            placeholder="M">
                                        @error('myNewReferences.' . $index . '.middle_initial')
                                            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">Surname</label>
                                        <input wire:model="myNewReferences.{{ $index }}.surname" type="text"
                                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                            placeholder="Enter surname">
                                        @error('myNewReferences.' . $index . '.surname')
                                            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">Address</label>
                                        <input wire:model="myNewReferences.{{ $index }}.address" type="text"
                                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                            placeholder="Enter address">
                                        @error('myNewReferences.' . $index . '.address')
                                            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">Telephone Number</label>
                                        <input wire:model="myNewReferences.{{ $index }}.tel_number" type="number"
                                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                            placeholder="Enter telephone number">
                                        @error('myNewReferences.' . $index . '.tel_number')
                                            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">Mobile Number</label>
                                        <input wire:model="myNewReferences.{{ $index }}.mobile_number" type="number"
                                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                            placeholder="Enter mobile number">
                                        @error('myNewReferences.' . $index . '.mobile_number')
                                            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @endif
    </div>

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