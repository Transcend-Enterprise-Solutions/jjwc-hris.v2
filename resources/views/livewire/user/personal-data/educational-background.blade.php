<div class="w-full overflow-hidden relative"
x-data="{
    showAddEdit: true,
}" x-cloak>

    <div class="flex justify-between flex-wrap bg-gray-100 dark:bg-gray-700 px-6 py-4">
        <h2 class="text-xl font-bold dark:text-white">Educational Background</h2>
        <div class="flex gap-2">
            @if(!$addEducBackground && !$editEducBackground)
                <button wire:click="toggleAddEducBackground" @click="showAddEdit = false"
                        x-show="showAddEdit === true"
                        class="inline-flex items-center px-4 py-1 bg-green-600 hover:bg-green-700 text-white text-xs font-medium rounded-lg shadow-sm transition-colors duration-200 focus:outline-none">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Add
                </button>

                @if($educBackground && $educBackground->isNotEmpty())
                    <button wire:click="toggleEditEducBackground" @click="showAddEdit = false"
                            x-show="showAddEdit === true"
                            class="inline-flex items-center px-4 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-lg shadow-sm transition-colors duration-200 focus:outline-none">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit All
                    </button>
                @endif
            @endif

            @if($editEducBackground && !$addEducBackground)
                <button wire:click="saveEducationBackground"
                        class="text-sm px-4 py-1 bg-green-600 hover:bg-green-700 text-white rounded-md focus:outline-none">
                    Save
                </button>
                <button wire:click="cancelEduc" @click="showAddEdit = true"
                        class="inline-flex items-center px-3 py-1 bg-gray-600 hover:bg-gray-700 text-white text-xs font-medium rounded-md shadow-sm transition-colors duration-200">
                    Cancel
                </button>
            @endif
        </div>
    </div>

    {{-- Form Content --}}
    <div class="p-6 space-y-6">
        @if(!$editEducBackground)
            @if($educBackground && $educBackground->isNotEmpty())
                @foreach($educBackground as $index => $educ)
                    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm overflow-hidden">
                        {{-- Education Level Header --}}
                        <div class="bg-gray-50 dark:bg-gray-800 px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                            <div class="flex justify-between items-center">
                                <h3 class="text-md font-semibold text-gray-900 dark:text-white uppercase">
                                    {{ $educ->level ?: 'Education Level' }}
                                </h3>
                                <button wire:click="toggleDelete({{ $educ->id }})"
                                        class="inline-flex items-center p-2 text-red-500 hover:text-red-700 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-md transition-colors duration-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        {{-- Education Details --}}
                        <div class="p-4">
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                                <div class="form-group">
                                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Name of School
                                    </label>
                                    <div class="px-3 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-md text-gray-900 dark:text-gray-100">
                                        <span class="text-sm">{{ $educ->name_of_school ?: '--' }}</span>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Basic Education/Degree/Course
                                    </label>
                                    <div class="px-3 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-md text-gray-900 dark:text-gray-100">
                                        <span class="text-sm">{{ $educ->basic_educ_degree_course ?: '--' }}</span>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        From
                                    </label>
                                    <div class="px-3 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-md text-gray-900 dark:text-gray-100">
                                        <div class="text-sm space-y-1">
                                            <div>{{ $educ->from ? \Carbon\Carbon::parse($educ->from)->format('M d, Y') : '--' }}</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        To
                                    </label>
                                    <div class="px-3 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-md text-gray-900 dark:text-gray-100">
                                        <div class="text-sm space-y-1">
                                            <div>
                                                @if($educ->toPresent)
                                                    {{ $educ->toPresent }}
                                                @elseif($educ->to)
                                                    {{ \Carbon\Carbon::parse($educ->to)->format('M d, Y') }}
                                                @else
                                                    --
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Highest Level/Units Earned
                                    </label>
                                    <div class="px-3 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-md text-gray-900 dark:text-gray-100">
                                        <span class="text-sm">{{ $educ->highest_level_unit_earned ?: '--' }}</span>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Year Graduated
                                    </label>
                                    <div class="px-3 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-md text-gray-900 dark:text-gray-100">
                                        <span class="text-sm">{{ $educ->year_graduated ?: '--' }}</span>
                                    </div>
                                </div>

                                <div class="form-group col-span-full">
                                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Scholarship/Academic Honors Received
                                    </label>
                                    <div class="px-3 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-md text-gray-900 dark:text-gray-100">
                                        <span class="text-sm">{{ $educ->award ?: '--' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                {{-- Empty State --}}
                <div class="text-center py-12 bg-gray-50 dark:bg-gray-800 rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-600">
                    <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                    <p class="text-xl font-medium text-gray-500 dark:text-gray-400 mb-2">No Educational Background Added</p>
                    <p class="text-gray-400 dark:text-gray-500 mb-4">Start building your educational profile</p>
                    <button wire:click="toggleAddEducBackground"
                            class="inline-flex items-center px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md shadow-sm transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Add First Education
                    </button>
                </div>
            @endif
        @else
            {{-- Edit/Add Form --}}
            <div class="space-y-6">
                {{-- Existing Education Records (Edit Mode) --}}
                @if(!$addEducBackground && $education)
                    @foreach($education as $index => $educ)
                        <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm overflow-hidden">
                            {{-- Form Header --}}
                            <div class="bg-blue-50 dark:bg-blue-900/20 px-4 py-3">
                                <h3 class="text-md font-semibold text-blue-800 dark:text-blue-200">
                                    Edit {{ $educ['level'] ?: 'Education' }}
                                </h3>
                            </div>

                            <div class="p-6">
                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                    {{-- Left Column --}}
                                    <div class="space-y-4">
                                        {{-- Education Level --}}
                                        <div class="form-group">
                                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                Education Level <span class="text-red-500">*</span>
                                            </label>
                                            <select wire:model="education.{{ $index }}.level_code" 
                                                    class="text-sm w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-gray-100">
                                                <option value="">Select Education Level</option>
                                                <option value="1">Elementary</option>
                                                <option value="2">Secondary</option>
                                                <option value="3">Vocational/Trade Course</option>
                                                <option value="4">College</option>
                                                <option value="5">Graduate Studies</option>
                                            </select>
                                            @error('education.' . $index . '.level_code') 
                                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span> 
                                            @enderror
                                        </div>

                                        {{-- Name of School --}}
                                        <div class="form-group">
                                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                Name of School <span class="text-red-500">*</span>
                                            </label>
                                            <input type="text" wire:model="education.{{ $index }}.name_of_school"
                                                   class="text-sm w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-gray-100"
                                                   placeholder="Enter school name">
                                            @error('education.' . $index . '.name_of_school') 
                                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span> 
                                            @enderror
                                        </div>

                                        {{-- Basic Education/Degree/Course --}}
                                        <div class="form-group">
                                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                Basic Education/Degree/Course
                                            </label>
                                            <input type="text" wire:model="education.{{ $index }}.basic_educ_degree_course"
                                                   class="text-sm w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-gray-100"
                                                   placeholder="Enter degree/course">
                                        </div>
                                    </div>

                                    {{-- Right Column --}}
                                    <div class="space-y-4">
                                        {{-- Highest Level/Units Earned --}}
                                        <div class="form-group">
                                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                Highest Level/Units Earned
                                            </label>
                                            <input type="text" wire:model="education.{{ $index }}.highest_level_unit_earned"
                                                   class="text-sm w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-gray-100"
                                                   placeholder="Enter highest level/units">
                                        </div>

                                        {{-- Year Graduated --}}
                                        <div class="form-group">
                                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                Year Graduated
                                            </label>
                                            <input type="number" wire:model="education.{{ $index }}.year_graduated"
                                                   class="text-sm w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-gray-100"
                                                   placeholder="Enter graduation year"
                                                   min="1900" max="{{ date('Y') + 10 }}"
                                                   @if($education[$index]['toPresent'] ?? false) disabled @endif>
                                            @error('education.' . $index . '.year_graduated') 
                                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span> 
                                            @enderror
                                        </div>

                                        {{-- Scholarship/Academic Honors --}}
                                        <div class="form-group">
                                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                Scholarship/Academic Honors Received
                                            </label>
                                            <input type="text" wire:model="education.{{ $index }}.award" rows="3"
                                                      class="text-sm w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-gray-100"
                                                      placeholder="Enter honors/awards received">
                                        </div>
                                    </div>

                                    {{-- Period of Attendance --}}
                                    <div class="space-y-4 col-span-full">
                                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300">
                                            Period of Attendance
                                        </label>
                                        <div class="form-group">
                                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                                                <div>
                                                    <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">From <span class="text-red-500">*</span></label>
                                                    <input type="date" wire:model="education.{{ $index }}.from"
                                                           class="text-sm w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-gray-100">
                                                    @error('education.' . $index . '.from') 
                                                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span> 
                                                    @enderror
                                                </div>
                                                <div>
                                                    <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">To</label>
                                                    <div class="space-y-2">
                                                        <input type="date" wire:model="education.{{ $index }}.toPresent"
                                                               class="{{ $education[$index]['toPresent'] ? 'hidden' : '' }} text-sm w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-gray-100">
                                                        <label class="flex items-center">
                                                            <input type="checkbox" wire:model.live="education.{{ $index }}.toPresent" value="Present"
                                                                   class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                                            <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">Currently studying</span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif

                {{-- New Education Records --}}
                @if($addEducBackground && $newEducation)
                    @foreach($newEducation as $index => $educ)
                        <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm overflow-hidden">
                            {{-- Form Header --}}
                            <div class="bg-green-50 dark:bg-green-900/20 px-4 py-3">
                                <div class="flex justify-between items-center">
                                    <h3 class="text-md font-semibold text-green-800 dark:text-green-200">
                                        Add New Education
                                    </h3>
                                    <div class="flex justify-end space-x-4">
                                        <button wire:click="saveEducationBackground"
                                            class="text-sm px-4 py-1 bg-green-600 hover:bg-green-700 text-white rounded-md focus:outline-none">
                                            Save
                                        </button>
                                        <button wire:click="cancelEduc" @click="showAddEdit = true"
                                                class="text-sm px-4 py-1 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-50 dark:hover:bg-gray-800 focus:outline-none">
                                            Cancel
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="p-6">
                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                                    {{-- Left Column --}}
                                    <div class="space-y-4">
                                        {{-- Education Level --}}
                                        <div class="form-group">
                                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                Education Level <span class="text-red-500">*</span>
                                            </label>
                                            <select wire:model="newEducation.{{ $index }}.level_code" 
                                                    class="text-sm w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-gray-100">
                                                <option value="">Select Education Level</option>
                                                <option value="1">Elementary</option>
                                                <option value="2">Secondary</option>
                                                <option value="3">Vocational/Trade Course</option>
                                                <option value="4">College</option>
                                                <option value="5">Graduate Studies</option>
                                            </select>
                                            @error('newEducation.' . $index . '.level_code') 
                                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span> 
                                            @enderror
                                        </div>

                                        {{-- Graduate Study Type (for Graduate Studies only) --}}
                                        @if(($newEducation[$index]['level_code'] ?? '') == '5')
                                            <div class="form-group">
                                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                    Graduate Study Type <span class="text-red-500">*</span>
                                                </label>
                                                <select wire:model="newEducation.{{ $index }}.graduateStudy" 
                                                        class="text-sm w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-gray-100">
                                                    <option value="">Select Graduate Study Type</option>
                                                    <option value="m">Master's Degree</option>
                                                    <option value="d">Doctoral Degree</option>
                                                </select>
                                                @error('newEducation.' . $index . '.graduateStudy') 
                                                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span> 
                                                @enderror
                                            </div>
                                        @endif

                                        {{-- Name of School --}}
                                        <div class="form-group">
                                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                Name of School <span class="text-red-500">*</span>
                                            </label>
                                            <input type="text" wire:model="newEducation.{{ $index }}.name_of_school"
                                                   class="text-sm w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-gray-100"
                                                   placeholder="Enter school name">
                                            @error('newEducation.' . $index . '.name_of_school') 
                                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span> 
                                            @enderror
                                        </div>

                                        {{-- Basic Education/Degree/Course --}}
                                        <div class="form-group">
                                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                Basic Education/Degree/Course
                                            </label>
                                            <input type="text" wire:model="newEducation.{{ $index }}.basic_educ_degree_course"
                                                   class="text-sm w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-gray-100"
                                                   placeholder="Enter degree/course">
                                        </div>
                                    </div>

                                    {{-- Right Column --}}
                                    <div class="space-y-4">
                                        {{-- Highest Level/Units Earned --}}
                                        <div class="form-group">
                                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                Highest Level/Units Earned
                                            </label>
                                            <input type="text" wire:model="newEducation.{{ $index }}.highest_level_unit_earned"
                                                   class="text-sm w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-gray-100"
                                                   placeholder="Enter highest level/units">
                                        </div>

                                        {{-- Year Graduated --}}
                                        <div class="form-group">
                                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                Year Graduated
                                            </label>
                                            <input type="number" wire:model="newEducation.{{ $index }}.year_graduated"
                                                   class="text-sm w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-gray-100"
                                                   placeholder="Enter graduation year"
                                                   min="1900" max="{{ date('Y') + 10 }}"
                                                   @if($newEducation[$index]['toPresent'] ?? false) disabled @endif>
                                            @error('newEducation.' . $index . '.year_graduated') 
                                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span> 
                                            @enderror
                                        </div>

                                        {{-- Scholarship/Academic Honors --}}
                                        <div class="form-group">
                                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                Scholarship/Academic Honors Received
                                            </label>
                                            <input type="text" wire:model="newEducation.{{ $index }}.award"
                                                   class="text-sm w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-gray-100"
                                                   placeholder="Enter honors/awards received">
                                        </div>
                                    </div>

                                    {{-- Period of Attendance --}}
                                    <div class="space-y-4 col-span-full">
                                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300">
                                            Period of Attendance
                                        </label>
                                        <div class="form-group">
                                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                                                <div>
                                                    <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">From <span class="text-red-500">*</span></label>
                                                    <input type="date" wire:model="newEducation.{{ $index }}.from"
                                                           class="text-sm w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-gray-100">
                                                    @error('newEducation.' . $index . '.from') 
                                                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span> 
                                                    @enderror
                                                </div>

                                                <div>
                                                    <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">To</label>
                                                    <div class="space-y-2">
                                                        <input type="date" wire:model="newEducation.{{ $index }}.to"
                                                               class="{{ $newEducation[$index]['toPresent'] ? 'hidden' : '' }} w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-gray-100"
                                                               @if($newEducation[$index]['toPresent'] ?? false) disabled @endif>
                                                        <label class="flex items-center">
                                                            <input type="checkbox" wire:model.live="newEducation.{{ $index }}.toPresent" value="Present"
                                                                   class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                                            <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">Currently studying</span>
                                                        </label>
                                                    </div>
                                                    @error('newEducation.' . $index . '.to') 
                                                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span> 
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
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

    {{-- Custom Styles --}}
    <style>
        .form-group {
            @apply transition-all duration-200;
        }
        
        .form-group:hover {
            @apply transform scale-[1.01];
        }
        
        .custom-d {
            display: block;
        }
    </style>
</div>