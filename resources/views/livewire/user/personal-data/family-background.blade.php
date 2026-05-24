<div class="w-full overflow-hidden relative">
    <!-- Header -->
    <div class="flex justify-between flex-wrap bg-gray-100 dark:bg-gray-700 px-6 py-4">
        <h2 class="text-xl font-bold dark:text-white">Family Background</h2>
    </div>

    <!-- Form Content -->
    <div class="p-6 space-y-8">
        
        <!-- Spouse Section -->
        <div class="space-y-4">
            <div class="flex justify-between items-center">
                <div class="flex items-center gap-6">
                    <h3 class="text-md font-semibold text-gray-900 dark:text-white flex-1">
                        Spouse Information
                    </h3>
                    <button wire:click="toggleEditFamily('spouse')"
                            class="inline-flex items-center px-4 py-1 text-blue-500 text-xs font-medium rounded-lg shadow-sm transition-colors duration-200 focus:outline-none">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit
                    </button>
                </div>
                

                @if($editingSpouse)
                    <div class="flex gap-2">
                        <button wire:click="saveFamily('spouse')"
                                class="inline-flex items-center px-3 py-1 bg-green-600 hover:bg-green-700 text-white text-xs font-medium rounded-md shadow-sm transition-colors duration-200">
                            Save
                        </button>
                        <button wire:click="cancelEdit('spouse')"
                                class="inline-flex items-center px-3 py-1 bg-gray-600 hover:bg-gray-700 text-white text-xs font-medium rounded-md shadow-sm transition-colors duration-200">
                            Cancel
                        </button>
                    </div>
                @endif
            </div>
            
            @if($userSpouse || $editingSpouse)
                <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg p-4 shadow-sm {{ $editingSpouse ? '!border-blue-500' : '' }}">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Spouse Name Fields -->
                        <div class="space-y-4 text-sm">
                            <div class="form-group">
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">Surname</label>
                                @if(!$editingSpouse)
                                    <div class="px-3 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-md text-gray-900 dark:text-gray-100">
                                        <span class="text-sm">{{ $userSpouse->surname ?: '--' }}</span>
                                    </div>
                                @else
                                    <input wire:model="spouse_surname" type="text"
                                           class="text-sm w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                    @error('spouse_surname') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                @endif
                            </div>
                    
                            <div class="form-group">
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">First Name</label>
                                @if(!$editingSpouse)
                                    <div class="px-3 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-md text-gray-900 dark:text-gray-100">
                                        <span class="text-sm">{{ $userSpouse->first_name ?: '--' }}</span>
                                    </div>
                                @else
                                    <input wire:model="spouse_first_name" type="text"
                                           class="text-sm w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                    @error('spouse_first_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                @endif
                            </div>
                    
                            <div class="form-group">
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">Date of Birth</label>
                                @if(!$editingSpouse)
                                    <div class="px-3 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-md text-gray-900 dark:text-gray-100">
                                        <span class="text-sm">
                                            @if($userSpouse->birth_date)
                                                {{ \Carbon\Carbon::parse($userSpouse->birth_date)->format('F d, Y') }}
                                            @else
                                                --
                                            @endif
                                        </span>
                                    </div>
                                @else
                                    <input wire:model="spouse_birth_date" type="date"
                                           class="text-sm w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                    @error('spouse_birth_date') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                @endif
                            </div>
                        </div>
                    
                        <div class="space-y-4">
                            <div class="form-group">
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">Middle Name</label>
                                @if(!$editingSpouse)
                                    <div class="px-3 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-md text-gray-900 dark:text-gray-100">
                                        <span class="text-sm">{{ $userSpouse->middle_name ?: '--' }}</span>
                                    </div>
                                @else
                                    <input wire:model="spouse_middle_name" type="text"
                                           class="text-sm w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                    @error('spouse_middle_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                @endif
                            </div>
                    
                            <div class="form-group">
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">Name Extension</label>
                                @if(!$editingSpouse)
                                    <div class="px-3 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-md text-gray-900 dark:text-gray-100">
                                        <span class="text-sm">{{ $userSpouse->name_extension ?: '--' }}</span>
                                    </div>
                                @else
                                    <input wire:model="spouse_name_extension" type="text"
                                           class="text-sm w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                    @error('spouse_name_extension') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                @endif
                            </div>
                    
                            <div class="form-group">
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">Occupation</label>
                                @if(!$editingSpouse)
                                    <div class="px-3 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-md text-gray-900 dark:text-gray-100">
                                        <span class="text-sm">{{ $userSpouse->occupation ?: '--' }}</span>
                                    </div>
                                @else
                                    <input wire:model="spouse_occupation" type="text"
                                           class="text-sm w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                    @error('spouse_occupation') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                        <div class="form-group">
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">Employer</label>
                            @if(!$editingSpouse)
                                <div class="px-3 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-md text-gray-900 dark:text-gray-100">
                                    <span class="text-sm">{{ $userSpouse->employer ?: '--' }}</span>
                                </div>
                            @else
                                <input wire:model="spouse_employer" type="text"
                                       class="text-sm w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                @error('spouse_employer') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            @endif
                        </div>
                    
                        <div class="form-group">
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">Tel. No.</label>
                            @if(!$editingSpouse)
                                <div class="px-3 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-md text-gray-900 dark:text-gray-100">
                                    <span class="text-sm">{{ $userSpouse->tel_number ?: '--' }}</span>
                                </div>
                            @else
                                <input wire:model="spouse_tel_number" type="tel"
                                       class="text-sm w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                @error('spouse_tel_number') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            @endif
                        </div>
                    </div>
                    
                    <div class="form-group mt-4">
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">Business Address</label>
                        @if(!$editingSpouse)
                            <div class="px-3 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-md text-gray-900 dark:text-gray-100">
                                <span class="text-sm">{{ $userSpouse->business_address ?: '--' }}</span>
                            </div>
                        @else
                            <textarea wire:model="spouse_business_address" rows="2"
                                      class="text-sm w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"></textarea>
                            @error('spouse_business_address') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        @endif
                    </div>
                </div>
            @elseif(!$userSpouse && !$editingSpouse)
                <div class="text-center py-8 bg-gray-50 dark:bg-gray-800 rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-600">
                    <svg wire:click="toggleEditFamily('spouse')" class="cursor-pointer w-12 h-12 mx-auto text-gray-400 hover:text-green-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <p class="text-gray-500 dark:text-gray-400">No spouse information added</p>
                </div>
            @endif
        </div>

        <!-- Father Section -->
        <div class="space-y-4">
            <div class="flex justify-between items-center">
                <div class="flex items-center gap-6">
                    <h3 class="text-md font-semibold text-gray-900 dark:text-white flex-1">
                        Father Information
                    </h3>
                    <button wire:click="toggleEditFamily('father')"
                            class="inline-flex items-center px-4 py-1 text-blue-500 text-xs font-medium rounded-lg shadow-sm transition-colors duration-200 focus:outline-none">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit
                    </button>
                </div>
                
                @if($editingFather)
                    <div class="flex gap-2">
                        <button wire:click="saveFamily('father')"
                                class="inline-flex items-center px-3 py-1 bg-green-600 hover:bg-green-700 text-white text-xs font-medium rounded-md shadow-sm transition-colors duration-200">
                            Save
                        </button>
                        <button wire:click="cancelEdit('father')"
                                class="inline-flex items-center px-3 py-1 bg-gray-600 hover:bg-gray-700 text-white text-xs font-medium rounded-md shadow-sm transition-colors duration-200">
                            Cancel
                        </button>
                    </div>
                @endif
            </div>
            
            @if($userFather || $editingFather)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg p-4 shadow-sm  {{ $editingFather ? '!border-blue-500' : '' }}">
                    <div class="form-group">
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">Surname</label>
                        @if(!$editingFather)
                            <div class="px-3 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-md text-gray-900 dark:text-gray-100">
                                <span class="text-sm">{{ $userFather->surname ?: '--' }}</span>
                            </div>
                        @else
                            <input wire:model="father_surname" type="text" 
                                   class="text-sm w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                            @error('father_surname') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        @endif
                    </div>
                    
                    <div class="form-group">
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">First Name</label>
                        @if(!$editingFather)
                            <div class="px-3 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-md text-gray-900 dark:text-gray-100">
                                <span class="text-sm">{{ $userFather->first_name ?: '--' }}</span>
                            </div>
                        @else
                            <input wire:model="father_first_name" type="text" 
                                   class="text-sm w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                            @error('father_first_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        @endif
                    </div>
                    
                    <div class="form-group">
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">Middle Name</label>
                        @if(!$editingFather)
                            <div class="px-3 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-md text-gray-900 dark:text-gray-100">
                                <span class="text-sm">{{ $userFather->middle_name ?: '--' }}</span>
                            </div>
                        @else
                            <input wire:model="father_middle_name" type="text" 
                                   class="text-sm w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                            @error('father_middle_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        @endif
                    </div>
                    
                    <div class="form-group">
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">Name Extension</label>
                        @if(!$editingFather)
                            <div class="px-3 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-md text-gray-900 dark:text-gray-100">
                                <span class="text-sm">{{ $userFather->name_extension ?: '--' }}</span>
                            </div>
                        @else
                            <input wire:model="father_name_extension" type="text" 
                                   class="text-sm w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                            @error('father_name_extension') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        @endif
                    </div>
                </div>
            @elseif(!$userFather && !$editingFather)
                <div class="text-center py-8 bg-gray-50 dark:bg-gray-800 rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-600">
                    <svg wire:click="toggleEditFamily('father')" class="cursor-pointer w-12 h-12 mx-auto text-gray-400 hover:text-green-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <p class="text-gray-500 dark:text-gray-400">No father information added</p>
                </div>
            @endif
        </div>

        <!-- Mother Section -->
        <div class="space-y-4">
            <div class="flex justify-between items-center">
                <div class="flex items-center gap-6">
                    <h3 class="text-md font-semibold text-gray-900 dark:text-white flex-1">
                        Mother's Maiden Name
                    </h3>
                    <button wire:click="toggleEditFamily('mother')"
                            class="inline-flex items-center px-4 py-1 text-blue-500 text-xs font-medium rounded-lg shadow-sm transition-colors duration-200 focus:outline-none">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit
                    </button>
                </div>
                
                @if($editingMother)
                    <div class="flex gap-2">
                        <button wire:click="saveFamily('mother')"
                                class="inline-flex items-center px-3 py-1 bg-green-600 hover:bg-green-700 text-white text-xs font-medium rounded-md shadow-sm transition-colors duration-200">
                            Save
                        </button>
                        <button wire:click="cancelEdit('mother')"
                                class="inline-flex items-center px-3 py-1 bg-gray-600 hover:bg-gray-700 text-white text-xs font-medium rounded-md shadow-sm transition-colors duration-200">
                            Cancel
                        </button>
                    </div>
                @endif
            </div>
            
            @if($userMother || $editingMother)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg p-4 shadow-sm  {{ $editingMother ? '!border-blue-500' : '' }}">
                    <div class="form-group">
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">Surname</label>
                        @if(!$editingMother)
                            <div class="px-3 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-md text-gray-900 dark:text-gray-100">
                                <span class="text-sm">{{ $userMother->surname ?: '--' }}</span>
                            </div>
                        @else
                            <input wire:model="mother_surname" type="text" 
                                   class="text-sm w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                            @error('mother_surname') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        @endif
                    </div>
                    
                    <div class="form-group">
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">First Name</label>
                        @if(!$editingMother)
                            <div class="px-3 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-md text-gray-900 dark:text-gray-100">
                                <span class="text-sm">{{ $userMother->first_name ?: '--' }}</span>
                            </div>
                        @else
                            <input wire:model="mother_first_name" type="text" 
                                   class="text-sm w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                            @error('mother_first_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        @endif
                    </div>
                    
                    <div class="form-group">
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">Middle Name</label>
                        @if(!$editingMother)
                            <div class="px-3 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-md text-gray-900 dark:text-gray-100">
                                <span class="text-sm">{{ $userMother->middle_name ?: '--' }}</span>
                            </div>
                        @else
                            <input wire:model="mother_middle_name" type="text" 
                                   class="text-sm w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                            @error('mother_middle_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        @endif
                    </div>
                    
                    <div class="form-group">
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">Name Extension</label>
                        @if(!$editingMother)
                            <div class="px-3 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-md text-gray-900 dark:text-gray-100">
                                <span class="text-sm">{{ $userMother->name_extension ?: '--' }}</span>
                            </div>
                        @else
                            <input wire:model="mother_name_extension" type="text" 
                                   class="text-sm w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                            @error('mother_name_extension') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        @endif
                    </div>
                </div>
            @elseif(!$userMother && !$editingMother)
                <div class="text-center py-8 bg-gray-50 dark:bg-gray-800 rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-600">
                    <svg wire:click="toggleEditFamily('mother')" class="cursor-pointer w-12 h-12 mx-auto text-gray-400 hover:text-green-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <p class="text-gray-500 dark:text-gray-400">No mother information added</p>
                </div>
            @endif
        </div>

        <!-- Children Section -->
        <div class="space-y-4">
            <div class="flex justify-between items-center">
                <div class="flex items-center gap-6">
                    <h3 class="text-md font-semibold text-gray-900 dark:text-white flex-1">
                        Children Information
                    </h3>
                    <button wire:click="toggleEditFamily('children')"
                            class="inline-flex items-center px-4 py-1 text-blue-500 text-xs font-medium rounded-lg shadow-sm transition-colors duration-200 focus:outline-none">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit
                    </button>
                </div>
                
                 @if($editingChildren)
                    <div class="flex gap-2">
                        <button wire:click="saveFamily('children')"
                                class="inline-flex items-center px-3 py-1 bg-green-600 hover:bg-green-700 text-white text-xs font-medium rounded-md shadow-sm transition-colors duration-200">
                            Save
                        </button>
                        <button wire:click="cancelEdit('children')"
                                class="inline-flex items-center px-3 py-1 bg-gray-600 hover:bg-gray-700 text-white text-xs font-medium rounded-md shadow-sm transition-colors duration-200">
                            Cancel
                        </button>
                    </div>
                @endif
            </div>
            
            @if(!$editingChildren)
                {{-- Display Mode --}}
                @if($userChildren && $userChildren->isNotEmpty())
                    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg p-4 shadow-sm space-y-4  {{ $editingChildren ? '!border-blue-500' : '' }}">
                        @foreach($userChildren as $child)
                            <div class="">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="form-group">
                                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">Full Name</label>
                                        <div class="px-3 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-md text-gray-900 dark:text-gray-100">
                                            <span class="text-sm">{{ $child->childs_name ?: '--' }}</span>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">Date of Birth</label>
                                        <div class="px-3 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-md text-gray-900 dark:text-gray-100">
                                            <span class="text-sm">
                                                @if($child->childs_birth_date)
                                                    {{ \Carbon\Carbon::parse($child->childs_birth_date)->format('F d, Y') }}
                                                @else
                                                    --
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 bg-gray-50 dark:bg-gray-800 rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-600">
                        <svg wire:click="toggleEditFamily('children')" class="cursor-pointer w-12 h-12 mx-auto text-gray-400 hover:text-green-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        <p class="text-gray-500 dark:text-gray-400">No children added</p>
                        <p class="text-sm text-gray-400 dark:text-gray-500 mt-2">Click the plus icon to add children</p>
                    </div>
                @endif
            @else
                {{-- Edit Mode --}}
                <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg p-4 shadow-sm space-y-4  {{ $editingChildren ? '!border-blue-500' : '' }}">
                    @if(count($children) > 0)
                        @foreach($children as $index => $child)
                            <div class="">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="form-group">
                                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">Full Name</label>
                                        <input wire:model="children.{{ $index }}.childs_name" type="text" 
                                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                            placeholder="Enter child's full name">
                                        @error('children.' . $index . '.childs_name') 
                                            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> 
                                        @enderror
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">Date of Birth</label>
                                        <div class="flex space-x-2">
                                            <input wire:model="children.{{ $index }}.childs_birth_date" type="date" 
                                                class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                            @if(count($children) > 1)
                                                <button wire:click="removeChild({{ $index }})" type="button"
                                                        class="px-3 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md transition-colors flex items-center justify-center"
                                                        title="Remove child">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            @endif
                                        </div>
                                        @error('children.' . $index . '.childs_birth_date') 
                                            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> 
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                    
                    {{-- Add Child Button --}}
                    <div class="text-center">
                        <button wire:click="addChild" type="button" 
                                class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Add Child
                        </button>
                    </div>
                </div>
            @endif
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
        
        .custom-d {
            display: block;
        }
    </style>
</div>