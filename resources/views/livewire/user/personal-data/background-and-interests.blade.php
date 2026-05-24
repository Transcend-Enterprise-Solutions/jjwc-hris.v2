<div class="w-full overflow-hidden relative">
    <!-- Header -->
    <div class="flex justify-between flex-wrap bg-gray-100 dark:bg-gray-700 px-6 py-4">
        <h2 class="text-xl font-bold dark:text-white">Background and Interests</h2>
    </div>

    <!-- Form Content -->
    <div class="p-6 space-y-8">
        <!-- Skills Section -->
        <div class="space-y-4">
            <div class="flex justify-between items-center">
                <div class="flex items-center gap-6">
                    <h3 class="text-md font-semibold text-gray-900 dark:text-white flex-1">
                        Skills
                    </h3>
                    @if(!$editingSkills && $mySkills->isNotEmpty())
                        <button wire:click="editInfo('skills')"
                                class="inline-flex items-center px-4 py-1 text-blue-500 text-xs font-medium rounded-lg shadow-sm transition-colors duration-200 focus:outline-none">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Edit
                        </button>
                    @endif
                </div>
                
                @if($editingSkills)
                    <div class="flex gap-2">
                        <button wire:click="saveInfo('skills')"
                                class="inline-flex items-center px-3 py-1 bg-green-600 hover:bg-green-700 text-white text-xs font-medium rounded-md shadow-sm transition-colors duration-200">
                            Save
                        </button>
                        <button wire:click="cancelEdit('skills')"
                                class="inline-flex items-center px-3 py-1 bg-gray-600 hover:bg-gray-700 text-white text-xs font-medium rounded-md shadow-sm transition-colors duration-200">
                            Cancel
                        </button>
                    </div>
                @endif
            </div>
            
            @if(!$editingSkills)
                {{-- Display Mode --}}
                @if($mySkills && $mySkills->isNotEmpty())
                    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg p-4 shadow-sm">
                        <div class="flex flex-wrap gap-3">
                            @foreach($mySkills as $skill)
                                <span class="relative inline-flex items-center px-3 py-1 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 text-sm rounded-full">
                                    {{ $skill->skill }}

                                    <button wire:click="toggleDelete({{ $skill->id }}, 'skill')"
                                            class="bg-white dark:bg-gray-900 hover:text-red-700 text-sm text-red-500
                                            absolute -top-2 right-0 rounded-full h-4 w-4 flex items-center justify-center">
                                            <i class="bi bi-x"></i>
                                    </button>
                                </span>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="text-center py-8 bg-gray-50 dark:bg-gray-800 rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-600">
                        <svg wire:click="editInfo('skills', false)" class="cursor-pointer w-12 h-12 mx-auto text-gray-400 hover:text-green-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        <p class="text-gray-500 dark:text-gray-400">No skills added</p>
                        <p class="text-sm text-gray-400 dark:text-gray-500 mt-2">Click the plus icon to add skills</p>
                    </div>
                @endif
            @else
                {{-- Edit Mode --}}
                <div class="bg-white dark:bg-gray-900 border border-blue-500 rounded-lg p-4 shadow-sm space-y-4">
                    @if(count($skills) > 0)
                        @foreach($skills as $index => $skill)
                            <div class="flex space-x-2">
                                <input wire:model="skills.{{ $index }}.skill" type="text" 
                                    class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                    placeholder="Enter skill">
                                @if(count($skills) > 1)
                                    <button wire:click="removeSkill({{ $index }})" type="button"
                                            class="px-3 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md transition-colors flex items-center justify-center"
                                            title="Remove skill">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                @endif
                            </div>
                            @error('skills.' . $index . '.skill') 
                                <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> 
                            @enderror
                        @endforeach
                    @endif
                    
                    {{-- Add Skill Button --}}
                    <div class="text-center">
                        <button wire:click="addSkill" type="button" 
                                class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Add Skill
                        </button>
                    </div>
                </div>
            @endif
        </div>

        <!-- Hobbies Section -->
        <div class="space-y-4">
            <div class="flex justify-between items-center">
                <div class="flex items-center gap-6">
                    <h3 class="text-md font-semibold text-gray-900 dark:text-white flex-1">
                        Hobbies
                    </h3>
                    @if(!$editingHobbies && $myHobbies->isNotEmpty())
                        <button wire:click="editInfo('hobbies')"
                                class="inline-flex items-center px-4 py-1 text-blue-500 text-xs font-medium rounded-lg shadow-sm transition-colors duration-200 focus:outline-none">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Edit
                        </button>
                    @endif
                </div>
                
                @if($editingHobbies)
                    <div class="flex gap-2">
                        <button wire:click="saveInfo('hobbies')"
                                class="inline-flex items-center px-3 py-1 bg-green-600 hover:bg-green-700 text-white text-xs font-medium rounded-md shadow-sm transition-colors duration-200">
                            Save
                        </button>
                        <button wire:click="cancelEdit('hobbies')"
                                class="inline-flex items-center px-3 py-1 bg-gray-600 hover:bg-gray-700 text-white text-xs font-medium rounded-md shadow-sm transition-colors duration-200">
                            Cancel
                        </button>
                    </div>
                @endif
            </div>
            
            @if(!$editingHobbies)
                {{-- Display Mode --}}
                @if($myHobbies && $myHobbies->isNotEmpty())
                    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg p-4 shadow-sm">
                        <div class="flex flex-wrap gap-2">
                            @foreach($myHobbies as $hobby)
                                <span class="relative inline-flex items-center px-3 py-1 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 text-sm rounded-full">
                                    {{ $hobby->hobby }}

                                    <button wire:click="toggleDelete({{ $hobby->id }}, 'hobby')"
                                            class="bg-white dark:bg-gray-900 hover:text-red-700 text-sm text-red-500
                                            absolute -top-2 right-0 rounded-full h-4 w-4 flex items-center justify-center">
                                            <i class="bi bi-x"></i>
                                    </button>
                                </span>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="text-center py-8 bg-gray-50 dark:bg-gray-800 rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-600">
                        <svg wire:click="editInfo('hobbies', false)" class="cursor-pointer w-12 h-12 mx-auto text-gray-400 hover:text-green-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        <p class="text-gray-500 dark:text-gray-400">No hobbies added</p>
                        <p class="text-sm text-gray-400 dark:text-gray-500 mt-2">Click the plus icon to add hobbies</p>
                    </div>
                @endif
            @else
                {{-- Edit Mode --}}
                <div class="bg-white dark:bg-gray-900 border border-blue-500 rounded-lg p-4 shadow-sm space-y-4">
                    @if(count($hobbies) > 0)
                        @foreach($hobbies as $index => $hobby)
                            <div class="flex space-x-2">
                                <input wire:model="hobbies.{{ $index }}.hobby" type="text" 
                                    class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                    placeholder="Enter hobby">
                                @if(count($hobbies) > 1)
                                    <button wire:click="removeHobby({{ $index }})" type="button"
                                            class="px-3 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md transition-colors flex items-center justify-center"
                                            title="Remove hobby">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                @endif
                            </div>
                            @error('hobbies.' . $index . '.hobby') 
                                <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> 
                            @enderror
                        @endforeach
                    @endif
                    
                    {{-- Add Hobby Button --}}
                    <div class="text-center">
                        <button wire:click="addHobby" type="button" 
                                class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Add Hobby
                        </button>
                    </div>
                </div>
            @endif
        </div>

        <!-- Non-Academic Distinctions Section -->
        <div class="space-y-4">
            <div class="flex justify-between items-center">
                <div class="flex items-center gap-6">
                    <h3 class="text-md font-semibold text-gray-900 dark:text-white flex-1">
                        Non-Academic Distinctions / Recognition
                    </h3>
                    @if(!$editingDistinctions && $myDistinctions->isNotEmpty())
                        <button wire:click="editInfo('distinctions')"
                                class="inline-flex items-center px-4 py-1 text-blue-500 text-xs font-medium rounded-lg shadow-sm transition-colors duration-200 focus:outline-none">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Edit
                        </button>
                    @endif
                </div>
                
                @if($editingDistinctions)
                    <div class="flex gap-2">
                        <button wire:click="saveInfo('distinctions')"
                                class="inline-flex items-center px-3 py-1 bg-green-600 hover:bg-green-700 text-white text-xs font-medium rounded-md shadow-sm transition-colors duration-200">
                            Save
                        </button>
                        <button wire:click="cancelEdit('distinctions')"
                                class="inline-flex items-center px-3 py-1 bg-gray-600 hover:bg-gray-700 text-white text-xs font-medium rounded-md shadow-sm transition-colors duration-200">
                            Cancel
                        </button>
                    </div>
                @endif
            </div>
            
            @if(!$editingDistinctions)
                {{-- Display Mode --}}
                @if($myDistinctions && $myDistinctions->isNotEmpty())
                    @foreach($myDistinctions as $distinction)
                        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg p-4 shadow-sm mb-4">
                            <div class="w-full flex justify-end">
                                <button wire:click="toggleDelete({{ $distinction->id }}, 'distinction')"
                                        class="text-red-500 hover:text-red-700 text-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Award</label>
                                    <div class="px-3 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-md">
                                        <span class="text-sm text-gray-900 dark:text-gray-100">{{ $distinction->award ?: '--' }}</span>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Organization Name</label>
                                    <div class="px-3 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-md">
                                        <span class="text-sm text-gray-900 dark:text-gray-100">{{ $distinction->ass_org_name ?: '--' }}</span>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Date Received</label>
                                    <div class="px-3 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-md">
                                        <span class="text-sm text-gray-900 dark:text-gray-100">
                                            @if($distinction->date_received)
                                                {{ \Carbon\Carbon::parse($distinction->date_received)->format('F d, Y') }}
                                            @else
                                                --
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-8 bg-gray-50 dark:bg-gray-800 rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-600">
                        <svg wire:click="editInfo('distinctions', false)" class="cursor-pointer w-12 h-12 mx-auto text-gray-400 hover:text-green-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        <p class="text-gray-500 dark:text-gray-400">No distinctions added</p>
                        <p class="text-sm text-gray-400 dark:text-gray-500 mt-2">Click the plus icon to add distinctions</p>
                    </div>
                @endif
            @else
                {{-- Edit Mode --}}
                <div class="bg-white dark:bg-gray-900 border border-blue-500 rounded-lg p-4 shadow-sm space-y-4">
                    @if(count($distinctions) > 0)
                        @foreach($distinctions as $index => $distinction)
                            <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="form-group col-span-full">
                                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">Award</label>
                                        <input wire:model="distinctions.{{ $index }}.award" type="text" 
                                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                            placeholder="Enter award name">
                                        @error('distinctions.' . $index . '.award') 
                                            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> 
                                        @enderror
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">Organization Name</label>
                                        <input wire:model="distinctions.{{ $index }}.ass_org_name" type="text" 
                                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                            placeholder="Enter organization name">
                                        @error('distinctions.' . $index . '.ass_org_name') 
                                            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> 
                                        @enderror
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">Date Received</label>
                                        <div class="flex space-x-2">
                                            <input wire:model="distinctions.{{ $index }}.date_received" type="date" 
                                                class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                            @if(count($distinctions) > 1)
                                                <button wire:click="removeDistinction({{ $index }})" type="button"
                                                        class="px-3 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md transition-colors flex items-center justify-center"
                                                        title="Remove distinction">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            @endif
                                        </div>
                                        @error('distinctions.' . $index . '.date_received') 
                                            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> 
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                    
                    {{-- Add Distinction Button --}}
                    <div class="text-center">
                        <button wire:click="addDistinction" type="button" 
                                class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Add Distinction
                        </button>
                    </div>
                </div>
            @endif
        </div>

        <!-- Membership Section -->
        <div class="space-y-4">
            <div class="flex justify-between items-center">
                <div class="flex items-center gap-6">
                    <h3 class="text-md font-semibold text-gray-900 dark:text-white flex-1">
                        Membership in Association/Organization
                    </h3>
                    @if(!$editingMemberships && $myMemberships->isNotEmpty())
                        <button wire:click="editInfo('membership')"
                                class="inline-flex items-center px-4 py-1 text-blue-500 text-xs font-medium rounded-lg shadow-sm transition-colors duration-200 focus:outline-none">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Edit
                        </button>
                    @endif
                </div>
                
                @if($editingMemberships)
                    <div class="flex gap-2">
                        <button wire:click="saveInfo('memberships')"
                                class="inline-flex items-center px-3 py-1 bg-green-600 hover:bg-green-700 text-white text-xs font-medium rounded-md shadow-sm transition-colors duration-200">
                            Save
                        </button>
                        <button wire:click="cancelEdit('memberships')"
                                class="inline-flex items-center px-3 py-1 bg-gray-600 hover:bg-gray-700 text-white text-xs font-medium rounded-md shadow-sm transition-colors duration-200">
                            Cancel
                        </button>
                    </div>
                @endif
            </div>
            
            @if(!$editingMemberships)
                {{-- Display Mode --}}
                @if($myMemberships && $myMemberships->isNotEmpty())
                    @foreach($myMemberships as $membership)
                        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg p-4 shadow-sm mb-4">
                            <div class="w-full flex justify-end">
                                <button wire:click="toggleDelete({{ $membership->id }}, 'memberships')"
                                        class="text-red-500 hover:text-red-700 text-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Organization Name</label>
                                    <div class="px-3 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-md">
                                        <span class="text-sm text-gray-900 dark:text-gray-100">{{ $membership->ass_org_name ?: '--' }}</span>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Position</label>
                                    <div class="px-3 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-md">
                                        <span class="text-sm text-gray-900 dark:text-gray-100">{{ $membership->position ?: '--' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-8 bg-gray-50 dark:bg-gray-800 rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-600">
                        <svg wire:click="editInfo('memberships', false)" class="cursor-pointer w-12 h-12 mx-auto text-gray-400 hover:text-green-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        <p class="text-gray-500 dark:text-gray-400">No memberships added</p>
                        <p class="text-sm text-gray-400 dark:text-gray-500 mt-2">Click the plus icon to add memberships</p>
                    </div>
                @endif
            @else
                {{-- Edit Mode --}}
                <div class="bg-white dark:bg-gray-900 border border-blue-500 rounded-lg p-4 shadow-sm space-y-4">
                    @if(count($memberships) > 0)
                        @foreach($memberships as $index => $membership)
                            <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="form-group">
                                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">Organization Name</label>
                                        <input wire:model="memberships.{{ $index }}.ass_org_name" type="text" 
                                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                            placeholder="Enter organization name">
                                        @error('memberships.' . $index . '.ass_org_name') 
                                            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> 
                                        @enderror
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">Position</label>
                                        <div class="flex space-x-2">
                                            <input wire:model="memberships.{{ $index }}.position" type="text" 
                                                class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                                placeholder="Enter position">
                                            @if(count($memberships) > 1)
                                                <button wire:click="removeMembership({{ $index }})" type="button"
                                                        class="px-3 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md transition-colors flex items-center justify-center"
                                                        title="Remove membership">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            @endif
                                        </div>
                                        @error('memberships.' . $index . '.position') 
                                            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> 
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                    
                    {{-- Add Membership Button --}}
                    <div class="text-center">
                        <button wire:click="addMembership" type="button" 
                                class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Add Membership
                        </button>
                    </div>
                </div>
            @endif
        </div>
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
                Are you sure you want to delete this voluntary work record?
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
        
        .custom-d {
            display: block;
        }
    </style>
</div>