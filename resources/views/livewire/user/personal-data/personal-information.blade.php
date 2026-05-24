<div class="w-full overflow-hidden relative">
    <!-- Header -->
    <div class="flex justify-between flex-wrap bg-gray-100 dark:bg-gray-700 px-6 py-4">
        <h2 class="text-xl font-bold dark:text-white">Personal Information</h2>

        <!-- Action Buttons -->
        <div class="flex space-x-2">
            <!-- Edit Button (shown when not editing) -->
            @if(!$editing)
                <button wire:click='toggleEditPersonalInfo'
                        class="inline-flex items-center px-4 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-lg shadow-sm transition-colors duration-200 focus:outline-none">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Edit
                </button>
            @endif

            <!-- Cancel and Save Buttons (shown when editing) -->
            @if($editing)
                <div class="flex space-x-2">
                    <button wire:click='cancelEdit'
                            class="inline-flex items-center px-4 py-1 bg-gray-500 hover:bg-gray-600 text-white text-xs font-medium rounded-lg shadow-sm transition-colors duration-200 focus:outline-none">
                        Cancel
                    </button>
                    
                    <button wire:click="savePersonalInfo" 
                            class="inline-flex items-center px-4 py-1 bg-green-600 hover:bg-green-700 text-white text-xs font-medium rounded-lg shadow-sm transition-colors duration-200 focus:outline-none">
                        Save
                    </button>
                </div>
            @endif
        </div>
    </div>

    <!-- Form Content -->
    <div class="p-6 space-y-8">
        
        <!-- Basic Information Section -->
        <div class="space-y-4">
            <h3 class="text-md font-semibold text-gray-900 dark:text-white">
                Basic Information
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg p-4 shadow-sm">
                <!-- Name Fields -->
                <div class="space-y-4">
                    <!-- Surname -->
                    <div class="form-group">
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">Surname</label>
                        @if(!$editing)
                            <div class="px-3 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-md text-gray-900 dark:text-gray-100">
                                <span class="text-sm">{{ $userData->surname ?: '--' }}</span>
                            </div>
                        @else
                            <input wire:model="surname" type="text" 
                                   class="text-sm w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                            @error('surname') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        @endif
                    </div>

                    <!-- First Name -->
                    <div class="form-group">
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">First Name</label>
                        @if(!$editing)
                            <div class="px-3 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-md text-gray-900 dark:text-gray-100">
                                <span class="text-sm">{{ $userData->first_name ?: '--' }}</span>
                            </div>
                        @else
                            <input wire:model="first_name" type="text" 
                                   class="text-sm w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                            @error('first_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        @endif
                    </div>
                </div>

                <div class="space-y-4">
                    <!-- Middle Name -->
                    <div class="form-group">
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">Middle Name</label>
                        @if(!$editing)
                            <div class="px-3 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-md text-gray-900 dark:text-gray-100">
                                <span class="text-sm">{{ $userData->middle_name ?: '--' }}</span>
                            </div>
                        @else
                            <input wire:model="middle_name" type="text" 
                                   class="text-sm w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                            @error('middle_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        @endif
                    </div>

                    <!-- Name Extension -->
                    <div class="form-group">
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">Name Extension</label>
                        @if(!$editing)
                            <div class="px-3 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-md text-gray-900 dark:text-gray-100">
                                <span class="text-sm">{{ $userData->name_extension ?: '--' }}</span>
                            </div>
                        @else
                            <input wire:model="name_extension" type="text" 
                                   class="text-sm w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                            @error('name_extension') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Personal Details Section -->
        <div class="space-y-4">
            <h3 class="text-md font-semibold text-gray-900 dark:text-white">
                Personal Details
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg p-4 shadow-sm">
                <!-- Date of Birth -->
                <div class="form-group">
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">Date of Birth</label>
                    @if(!$editing)
                        <div class="px-3 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-md text-gray-900 dark:text-gray-100">
                            <span class="text-sm">
                                @if($userData->date_of_birth)
                                    {{ \Carbon\Carbon::parse($userData->date_of_birth)->format('F d, Y') }}
                                @else
                                    --
                                @endif
                            </span>
                        </div>
                    @else
                        <input wire:model="date_of_birth" type="date" 
                               class="text-sm w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                        @error('date_of_birth') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    @endif
                </div>

                <!-- Place of Birth -->
                <div class="form-group">
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">Place of Birth</label>
                    @if(!$editing)
                        <div class="px-3 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-md text-gray-900 dark:text-gray-100">
                            <span class="text-sm">{{ $userData->place_of_birth ?: '--' }}</span>
                        </div>
                    @else
                        <input wire:model="place_of_birth" type="text" 
                               class="text-sm w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                        @error('place_of_birth') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    @endif
                </div>

                <!-- Sex -->
                <div class="form-group">
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">Sex</label>
                    @if(!$editing)
                        <div class="px-3 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-md text-gray-900 dark:text-gray-100">
                            <span class="text-sm">{{ $userData->sex ?: '--' }}</span>
                        </div>
                    @else
                        <select wire:model="sex" 
                                class="text-sm w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                            <option value="">Select Sex</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                        @error('sex') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    @endif
                </div>

                <!-- Civil Status -->
                <div class="form-group">
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">Civil Status</label>
                    @if(!$editing)
                        <div class="px-3 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-md text-gray-900 dark:text-gray-100">
                            <span class="text-sm">{{ $userData->civil_status ?: '--' }}</span>
                        </div>
                    @else
                        <select wire:model="civil_status" 
                                class="text-sm w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                            <option value="">Select Status</option>
                            <option value="Single">Single</option>
                            <option value="Married">Married</option>
                            <option value="Widowed">Widowed</option>
                            <option value="Separated">Separated</option>
                            <option value="Other">Other</option>
                        </select>
                        @error('civil_status') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    @endif
                </div>

                <!-- Height -->
                <div class="form-group">
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">Height (m)</label>
                    @if(!$editing)
                        <div class="px-3 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-md text-gray-900 dark:text-gray-100">
                            <span class="text-sm">{{ $userData->height ? $userData->height . 'm' : '--' }}</span>
                        </div>
                    @else
                        <input wire:model="height" type="number" step="0.01" 
                               class="text-sm w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                        @error('height') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    @endif
                </div>

                <!-- Weight -->
                <div class="form-group">
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">Weight (kg)</label>
                    @if(!$editing)
                        <div class="px-3 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-md text-gray-900 dark:text-gray-100">
                            <span class="text-sm">{{ $userData->weight ? $userData->weight . 'kg' : '--' }}</span>
                        </div>
                    @else
                        <input wire:model="weight" type="number" 
                               class="text-sm w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                        @error('weight') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    @endif
                </div>

                <!-- Blood Type -->
                <div class="form-group">
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">Blood Type</label>
                    @if(!$editing)
                        <div class="px-3 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-md text-gray-900 dark:text-gray-100">
                            <span class="text-sm">{{ $userData->blood_type ?: '--' }}</span>
                        </div>
                    @else
                        <select wire:model="blood_type" 
                                class="text-sm w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                            <option value="">Select Blood Type</option>
                            <option value="A+">A+</option>
                            <option value="A-">A-</option>
                            <option value="B+">B+</option>
                            <option value="B-">B-</option>
                            <option value="AB+">AB+</option>
                            <option value="AB-">AB-</option>
                            <option value="O+">O+</option>
                            <option value="O-">O-</option>
                        </select>
                        @error('blood_type') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    @endif
                </div>

                <!-- Citizenship -->
                <div class="form-group">
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">Citizenship</label>
                    @if(!$editing)
                        <div class="px-3 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-md text-gray-900 dark:text-gray-100">
                            <span class="text-sm">
                                {{ $userData->citizenship ?: '--' }}
                                @if($userData->dual_citizenship_type)
                                    <span class="text-xs opacity-80">
                                        | {{ $userData->dual_citizenship_type }}
                                    </span>
                                @endif
                                @if($userData->dual_citizenship_country)
                                    <span class="text-xs opacity-80">
                                        | {{ $userData->dual_citizenship_country }}
                                    </span>
                                @endif
                            </span>
                        </div>
                    @else
                        <input wire:model="citizenship" type="text" 
                               class="text-sm w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                        @error('citizenship') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    @endif
                </div>

                <!-- Ethnicity -->
                <div class="form-group">
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">Ethnicity</label>
                    @if(!$editing)
                        <div class="px-3 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-md text-gray-900 dark:text-gray-100">
                            <span class="text-sm">{{ $userData->ethnicity ?: '--' }}</span>
                        </div>
                    @else
                        <select wire:model="ethnicity" 
                                class="text-sm w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                            <option value="">Select Ethnicity</option>
                            @foreach ($ethnicities as $ethnicity)
                                <option value="{{ $ethnicity->name }}">{{ $ethnicity->name }}</option>
                            @endforeach
                        </select>
                        @error('ethnicity') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    @endif
                </div>

                <!-- Solo Parent -->
                <div class="form-group">
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Is Solo Parent
                    </label>

                    @if(!$editing)
                        <div class="px-3 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-md text-gray-900 dark:text-gray-100">
                            <span class="text-sm">
                                {{ $userData->is_solo_parent ? 'Yes' : 'No' }}
                            </span>
                        </div>
                    @else
                        <div class="flex items-center gap-6">
                            <label class="flex items-center gap-2">
                                <input 
                                    wire:model="is_solo_parent" 
                                    type="radio" 
                                    value="1"
                                    class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-600"
                                >
                                <span class="text-sm text-gray-700 dark:text-gray-300">Yes</span>
                            </label>

                            <label class="flex items-center gap-2">
                                <input 
                                    wire:model="is_solo_parent" 
                                    type="radio" 
                                    value="0"
                                    class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-600"
                                >
                                <span class="text-sm text-gray-700 dark:text-gray-300">No</span>
                            </label>
                        </div>

                        @error('is_solo_parent')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    @endif
                </div>

                <!-- PWD -->
                <div class="form-group">
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">
                        PWD
                    </label>

                    @if(!$editing)
                        <div class="px-3 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-md text-gray-900 dark:text-gray-100">
                            <span class="text-sm">
                                {{ $userData->pwd ? 'Yes' : 'No' }}
                            </span>
                        </div>
                    @else
                        <div class="flex items-center gap-6">
                            <label class="flex items-center gap-2">
                                <input 
                                    wire:model="pwd" 
                                    type="radio" 
                                    value="1"
                                    class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-600"
                                >
                                <span class="text-sm text-gray-700 dark:text-gray-300">Yes</span>
                            </label>

                            <label class="flex items-center gap-2">
                                <input 
                                    wire:model="pwd" 
                                    type="radio" 
                                    value="0"
                                    class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-600"
                                >
                                <span class="text-sm text-gray-700 dark:text-gray-300">No</span>
                            </label>
                        </div>

                        @error('pwd')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    @endif
                </div>

                <!-- Citizenship -->
                <div class="form-group">
                    <!-- Citizenship Radio Buttons -->
                    <div class="w-full">
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2"><i class="bi bi-person"></i> Citizenship</label>
                        <div class="mt-2">
                            <label class="inline-flex items-center">
                                <input type="radio" name="citizenship" value="Filipino"
                                    wire:model.live="citizenship"
                                    class="text-indigo-600 border-gray-300 focus:ring-indigo-500" @if(!$editing) readonly disabled @endif>
                                <span class="ml-2">Filipino</span>
                            </label>
                            <label class="inline-flex items-center ml-6">
                                <input type="radio" name="citizenship" value="Dual Citizenship"
                                    wire:model.live="citizenship"
                                    class="text-indigo-600 border-gray-300 focus:ring-indigo-500" @if(!$editing) readonly disabled @endif>
                                <span class="ml-2">Dual Citizenship</span>
                            </label>
                        </div>
                        @error('citizenship')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Dual Citizenship Additional Fields -->
                    @if ($citizenship === 'Dual Citizenship')
                        <!-- Dual Citizenship Type Radio Buttons -->
                        <div class="w-full mt-4">
                            <label class="text-sm text-gray-700">Dual Citizenship Type <span
                                    class="text-red-600">*</span></label>
                            <div class="mt-2">
                                <label class="inline-flex items-center">
                                    <input type="radio" name="dual_citizenship_type" value="By Birth"
                                        wire:model="dual_citizenship_type"
                                        class="text-indigo-600 border-gray-300 focus:ring-indigo-500"  @if(!$editing) readonly disabled @endif>
                                    <span class="ml-2">By Birth</span>
                                </label>
                                <label class="inline-flex items-center ml-6">
                                    <input type="radio" name="dual_citizenship_type"
                                        value="By Naturalization" wire:model="dual_citizenship_type"
                                        class="text-indigo-600 border-gray-300 focus:ring-indigo-500"  @if(!$editing) readonly disabled @endif>
                                    <span class="ml-2">By Naturalization</span>
                                </label>
                            </div>
                            @error('dual_citizenship_type')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Country Select Field -->
                        <div class="w-full mt-4">
                            <label class="text-sm text-gray-700">Country <span
                                    class="text-red-600">*</span></label>
                            <select wire:model="dual_citizenship_country"
                                class="text-sm w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"  @if(!$editing) readonly disabled @endif>
                                <option value="">Select Country</option>
                                @foreach ($countries as $country)
                                    <option value="{{ $country->name }}">{{ $country->name }}</option>
                                @endforeach
                            </select>
                            @error('dual_citizenship_country')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                    @endif
                </div>  

            </div>
        </div>

        <!-- Address Section -->
        <div class="space-y-4">
            <h3 class="text-md font-semibold text-gray-900 dark:text-white">
                Address Information
            </h3>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg p-4 shadow-sm">
                <!-- Permanent Address -->
                <div class="form-group">
                    <div class="flex items-center mb-4">
                        <svg class="w-4 h-4 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Permanent Address</label>
                    </div>
                    
                    @if(!$editing)
                        <div class="px-3 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-md text-gray-900 dark:text-gray-100 min-h-[120px]">
                            <div class="text-sm space-y-1">
                                @php
                                    $p_address_line_1 = explode(',', $userData->p_house_street);
                                @endphp
                                <div><strong>House/Block/Lot:</strong> 
                                    {{ 
                                        (isset($p_address_line_1[0]) ? $p_address_line_1[0] : '') . ' ' .
                                        (isset($p_address_line_1[1]) ? $p_address_line_1[1] : '') . ' ' .  
                                        (isset($p_address_line_1[2]) ? $p_address_line_1[2] : '') 
                                    }}
                                </div>
                                <div><strong>Barangay:</strong> {{ $userData->permanent_selectedBarangay ?: '--' }}</div>
                                <div><strong>City:</strong> {{ $userData->permanent_selectedCity ?: '--' }}</div>
                                <div><strong>Province:</strong> {{ $userData->permanent_selectedProvince ?: '--' }}</div>
                                <div><strong>Zip Code:</strong> {{ $userData->permanent_selectedZipcode ?: '--' }}</div>
                            </div>
                        </div>
                    @else
                        <div class="space-y-3">
                            <!-- Province -->
                            <div>
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Province <span class="text-red-500">*</span></label>
                                <select wire:model.live="p_province" class="text-sm w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                    <option value="">Select Province</option>
                                    @if($pprovinces)
                                        @foreach($pprovinces as $province)
                                            <option value="{{ $province->province_description }}">{{ $province->province_description }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                @error('p_province') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            
                            <!-- City -->
                            <div>
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">City <span class="text-red-500">*</span></label>
                                <select wire:model.live="p_city" class="text-sm w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                    <option value="">Select City</option>
                                    @if($pcities)
                                        @foreach($pcities as $city)
                                            <option value="{{ $city->city_municipality_description }}">{{ $city->city_municipality_description }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                @error('p_city') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            
                            <!-- Barangay -->
                            <div>
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Barangay <span class="text-red-500">*</span></label>
                                <select wire:model="p_barangay" class="text-sm w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                    <option value="">Select Barangay</option>
                                    @if($pbarangays)
                                        @foreach($pbarangays as $barangay)
                                            <option value="{{ $barangay->barangay_description }}">{{ $barangay->barangay_description }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                @error('p_barangay') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            
                            <!-- House/Block/Lot No. -->
                            <div>
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">House/Block/Lot No.</label>
                                <input wire:model="p_house_number" type="text" 
                                       class="text-sm w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                @error('p_house_number') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            
                            <!-- Street -->
                            <div>
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Street</label>
                                <input wire:model="p_street" type="text" 
                                       class="text-sm w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                @error('p_street') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            
                            <!-- Subdivision/Village -->
                            <div>
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Subdivision/Village</label>
                                <input wire:model="p_subdivision" type="text" 
                                       class="text-sm w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                @error('p_subdivision') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            
                            <!-- Zip Code -->
                            <div>
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Zip Code <span class="text-red-500">*</span></label>
                                <input wire:model="p_zipcode" type="number" 
                                       class="text-sm w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                @error('p_zipcode') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Residential Address -->
                <div class="form-group">
                    <div class="flex items-center mb-4">
                        <svg class="w-4 h-4 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Residential Address</label>
                    </div>
                    
                    @if(!$editing)
                        <div class="px-3 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-md text-gray-900 dark:text-gray-100 min-h-[120px]">
                            <div class="text-sm space-y-1">
                                @php
                                    $r_address_line_1 = explode(',', $userData->r_house_street);
                                @endphp
                                <div><strong>House/Block/Lot:</strong> 
                                    {{ 
                                        (isset($r_address_line_1[0]) ? $r_address_line_1[0] : '') . ' ' .
                                        (isset($r_address_line_1[1]) ? $r_address_line_1[1] : '') . ' ' .  
                                        (isset($r_address_line_1[2]) ? $r_address_line_1[2] : '') 
                                    }}
                                </div>
                                <div><strong>Barangay:</strong> {{ $userData->residential_selectedBarangay ?: '--' }}</div>
                                <div><strong>City:</strong> {{ $userData->residential_selectedCity ?: '--' }}</div>
                                <div><strong>Province:</strong> {{ $userData->residential_selectedProvince ?: '--' }}</div>
                                <div><strong>Zip Code:</strong> {{ $userData->residential_selectedZipcode ?: '--' }}</div>
                            </div>
                        </div>
                    @else
                        <div class="space-y-3">
                            <!-- Same as Permanent Address Checkbox -->
                            <div class="flex items-center space-x-2 mb-3">
                                <input wire:model.live="same_as_permanent" type="checkbox" 
                                       class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Same as permanent address</label>
                            </div>
                            
                            <div class="{{ $same_as_permanent ? 'hidden' : '' }} space-y-3">
                                <!-- Province -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Province <span class="text-red-500">*</span></label>
                                    <select wire:model.live="r_province" class="text-sm w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                        <option value="">Select Province</option>
                                        @if($pprovinces)
                                            @foreach($pprovinces as $province)
                                                <option value="{{ $province->province_description }}">{{ $province->province_description }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @error('r_province') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                
                                <!-- City -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">City <span class="text-red-500">*</span></label>
                                    <select wire:model.live="r_city" class="text-sm w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                        <option value="">Select City</option>
                                        @if($rcities)
                                            @foreach($rcities as $city)
                                                <option value="{{ $city->city_municipality_description }}">{{ $city->city_municipality_description }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @error('r_city') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                
                                <!-- Barangay -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Barangay <span class="text-red-500">*</span></label>
                                    <select wire:model="r_barangay" class="text-sm w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                        <option value="">Select Barangay</option>
                                        @if($rbarangays)
                                            @foreach($rbarangays as $barangay)
                                                <option value="{{ $barangay->barangay_description }}">{{ $barangay->barangay_description }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @error('r_barangay') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                
                                <!-- House/Block/Lot No. -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">House/Block/Lot No.</label>
                                    <input wire:model="r_house_number" type="text" 
                                           class="text-sm w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                    @error('r_house_number') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                
                                <!-- Street -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Street</label>
                                    <input wire:model="r_street" type="text" 
                                           class="text-sm w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                    @error('r_street') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                
                                <!-- Subdivision/Village -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Subdivision/Village</label>
                                    <input wire:model="r_subdivision" type="text" 
                                           class="text-sm w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                    @error('r_subdivision') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                
                                <!-- Zip Code -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Zip Code <span class="text-red-500">*</span></label>
                                    <input wire:model="r_zipcode" type="number" 
                                           class="text-sm w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                    @error('r_zipcode') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Contact Information -->
        <div class="space-y-4">
            <h3 class="text-md font-semibold text-gray-900 dark:text-white">
                Contact Information
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg p-4 shadow-sm">
                <!-- Mobile Number -->
                <div class="form-group">
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">Mobile No.</label>
                    @if(!$editing)
                        <div class="px-3 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-md text-gray-900 dark:text-gray-100">
                            <span class="text-sm">{{ $userData->mobile_number ?: '--' }}</span>
                        </div>
                    @else
                        <input wire:model="mobile_number" type="tel" 
                               class="text-sm w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                        @error('mobile_number') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    @endif
                </div>

                <!-- Tel Number -->
                <div class="form-group">
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">Tel No.</label>
                    @if(!$editing)
                        <div class="px-3 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-md text-gray-900 dark:text-gray-100">
                            <span class="text-sm">{{ $userData->tel_number ?: '--' }}</span>
                        </div>
                    @else
                        <input wire:model="tel_number" type="tel" 
                               class="text-sm w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                        @error('tel_number') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    @endif
                </div>

                <!-- Email -->
                <div class="form-group">
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">Email</label>
                    @if(!$editing)
                        <div class="px-3 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-md text-gray-900 dark:text-gray-100">
                            <span class="text-sm">{{ $userData->email ?: '--' }}</span>
                        </div>
                    @else
                        <input wire:model="email" type="email" 
                               class="text-sm w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                        @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    @endif
                </div>
            </div>
        </div>

        <!-- Government IDs Section -->
        <div class="space-y-4">
            <h3 class="text-md font-semibold text-gray-900 dark:text-white">
                Government ID Numbers
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg p-4 shadow-sm">
                <!-- UMID ID -->
                <div class="form-group">
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">UMID ID No.</label>
                    @if(!$editing)
                        <div class="px-3 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-md text-gray-900 dark:text-gray-100">
                            <span class="text-sm">{{ $userData->umid ?: '--' }}</span>
                        </div>
                    @else
                        <input wire:model="umid" type="text" 
                               class="text-sm w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                        @error('umid') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    @endif
                </div>

                <!-- Pag-Ibig ID -->
                <div class="form-group">
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">Pag-Ibig ID No.</label>
                    @if(!$editing)
                        <div class="px-3 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-md text-gray-900 dark:text-gray-100">
                            <span class="text-sm">{{ $userData->pagibig ?: '--' }}</span>
                        </div>
                    @else
                        <input wire:model="pagibig" type="text" 
                               class="text-sm w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                        @error('pagibig') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    @endif
                </div>

                <!-- PhilHealth ID -->
                <div class="form-group">
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">PhilHealth ID No.</label>
                    @if(!$editing)
                        <div class="px-3 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-md text-gray-900 dark:text-gray-100">
                            <span class="text-sm">{{ $userData->philhealth ?: '--' }}</span>
                        </div>
                    @else
                        <input wire:model="philhealth" type="text" 
                               class="text-sm w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                        @error('philhealth') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    @endif
                </div>

                <!-- PhilSys Number -->
                <div class="form-group">
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">PhilSys No.</label>
                    @if(!$editing)
                        <div class="px-3 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-md text-gray-900 dark:text-gray-100">
                            <span class="text-sm">{{ $userData->philsys ?: '--' }}</span>
                        </div>
                    @else
                        <input wire:model="philsys" type="text" 
                               class="text-sm w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                        @error('philsys') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    @endif
                </div>

                <!-- TIN Number -->
                <div class="form-group">
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">TIN No.</label>
                    @if(!$editing)
                        <div class="px-3 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-md text-gray-900 dark:text-gray-100">
                            <span class="text-sm">{{ $userData->tin ?: '--' }}</span>
                        </div>
                    @else
                        <input wire:model="tin" type="text" 
                               class="text-sm w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                        @error('tin') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    @endif
                </div>

                <!-- Agency Employee Number -->
                <div class="form-group">
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">Agency Employee No.</label>
                    @if(!$editing)
                        <div class="px-3 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-md text-gray-900 dark:text-gray-100">
                            <span class="text-sm">{{ $userData->agency_employee_no ?: '--' }}</span>
                        </div>
                    @else
                        <input wire:model="agency_employee_no" type="text" 
                               class="text-sm w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                        @error('agency_employee_no') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    @endif
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
        
        .custom-d {
            display: block;
        }
    </style>
</div>