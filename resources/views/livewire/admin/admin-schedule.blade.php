<div class="w-full" x-data="{
    selectedTab: @entangle('selectedTab'),
    isOvernight: @entangle('is_overnight'),
    is24hours: @entangle('is_24hours')
}" x-cloak>

    <div class="w-full bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-md">
        <h1 class="text-lg font-bold text-center text-black dark:text-white mb-6">Employee Schedule</h1>
        <div class="mb-6 flex flex-col sm:flex-row items-end justify-between gap-3">
            <div class="w-full sm:w-1/3">
                <label for="search" class="block text-sm font-medium text-gray-700 dark:text-slate-400 mb-1">Search</label>
                <input type="text" id="search" wire:model.live="search" class="px-2 py-1.5 block w-full shadow-sm sm:text-sm border border-gray-400 hover:bg-gray-300 rounded-md
                        dark:hover:bg-slate-600 dark:border-slate-600
                        dark:text-gray-300 dark:bg-gray-800" placeholder="Enter employee name">
            </div>
            <div class="w-full sm:w-2/3 flex flex-col sm:flex-row sm:justify-end gap-2">
                <!-- Import/Export Buttons -->
                <div class="flex gap-2">
                    <button wire:click="downloadTemplate"
                        class="text-sm px-3 py-1.5 bg-blue-500 text-white rounded-md
                        hover:bg-blue-600 focus:outline-none dark:bg-blue-600
                        dark:hover:bg-blue-700 flex items-center gap-2">
                        <i class="fas fa-download"></i>
                        Template
                    </button>
                    <button wire:click="exportSchedules"
                        class="text-sm px-3 py-1.5 bg-purple-500 text-white rounded-md
                        hover:bg-purple-600 focus:outline-none dark:bg-purple-600
                        dark:hover:bg-purple-700 flex items-center gap-2">
                        <i class="fas fa-file-export"></i>
                        Export
                    </button>
                    <button wire:click="openImportModal"
                        class="text-sm px-3 py-1.5 bg-indigo-500 text-white rounded-md
                        hover:bg-indigo-600 focus:outline-none dark:bg-indigo-600
                        dark:hover:bg-indigo-700 flex items-center gap-2">
                        <i class="fas fa-file-import"></i>
                        Import
                    </button>
                </div>
                <button wire:click="openModal"
                    class="text-sm px-3 py-1.5 bg-green-500 text-white rounded-md
                    hover:bg-green-600 focus:outline-none dark:bg-gray-700
                    dark:hover:bg-green-600 dark:text-gray-300 dark:hover:text-white flex items-center gap-2">
                    <i class="fas fa-plus"></i>
                    Add Schedule
                </button>
            </div>
        </div>

        <!-- Tabs -->
        <div class="w-full mb-4">
            <div class="flex gap-2 overflow-x-auto border-b border-slate-300 dark:border-slate-700" role="tablist">
                <button wire:click="setTab('current')"
                    :class="{'font-bold text-violet-700 border-b-2 border-violet-700': selectedTab === 'current', 'text-slate-700 font-medium dark:text-white': selectedTab !== 'current'}"
                    class="h-min px-4 py-2 text-sm" role="tab">Current</button>
                <button wire:click="setTab('incoming')"
                    :class="{'font-bold text-violet-700 border-b-2 border-violet-700': selectedTab === 'incoming', 'text-slate-700 font-medium dark:text-white': selectedTab !== 'incoming'}"
                    class="h-min px-4 py-2 text-sm" role="tab">Incoming</button>
                <button wire:click="setTab('expired')"
                    :class="{'font-bold text-violet-700 border-b-2 border-violet-700': selectedTab === 'expired', 'text-slate-700 font-medium dark:text-white': selectedTab !== 'expired'}"
                    class="h-min px-4 py-2 text-sm" role="tab">Expired</button>
            </div>
        </div>

        <!-- Table -->
        <div class="mt-4 overflow-x-auto text-sm">
            <table class="min-w-full bg-white dark:bg-gray-800 overflow-hidden">
                <thead class="bg-gray-200 dark:bg-gray-700 rounded-xl">
                    <tr class="whitespace-nowrap">
                        <th class="px-4 py-2 text-left">Employee</th>
                        <th class="px-4 py-2 text-center">Employee ID</th>
                        <th class="px-4 py-2 text-center">WFH Days</th>
                        <th class="px-4 py-2 text-center">Rest Days</th>
                        <th class="px-4 py-2 text-center">Schedule Time</th>
                        <th class="px-4 py-2 text-center">Break</th>
                        <th class="px-4 py-2 text-center">Flexi</th>
                        <th class="px-4 py-2 text-center">Dates</th>
                        <th class="px-4 py-2 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($filteredSchedules as $schedule)
                    <tr class="border-b dark:border-gray-600 whitespace-nowrap">
                        <td class="px-4 py-2 text-center">
                            <div class="flex gap-3 items-center">
                                @if ($schedule->user->profile_photo_path)
                                    <img src="{{ route('profile-photo.file', ['filename' => basename($schedule->user->profile_photo_path)]) }}"
                                        alt="{{ $this->getFormattedName($schedule->user) ?? 'No User Assigned' }}"
                                        width="32" height="32"
                                        class="w-10 h-10 rounded-full object-cover border border-gray-500">
                                @else
                                    <div class="w-10 h-10 rounded-full bg-gray-500 border border-gray-500 dark:bg-gray-600 flex items-center justify-center text-white text-xs font-medium">
                                        {{ strtoupper(substr(($this->getFormattedName($schedule->user) ?? 'No User Assigned'), 0, 1)) }}{{ strtoupper(substr(explode(' ', ($this->getFormattedName($schedule->user) ?? 'No User Assigned'))[1] ?? '', 0, 1)) }}
                                    </div>
                                @endif
                                <span>{{ $this->getFormattedName($schedule->user) ?? 'No User Assigned' }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-2 text-center">{{ $this->getDisplayEmpCode($schedule->emp_code,
                            $schedule->user?->appointment) }}</td>
                        <td class="px-4 py-2 text-center">
                            {{ $this->getSortedWfhDays($schedule->wfh_days) }}
                        </td>
                        <td class="px-4 py-2 text-center">
                            <span class="px-2 py-1 bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 rounded text-xs">
                                {{ $this->getSortedRestDays($schedule->rest_days) }}
                            </span>
                        </td>
                        <td class="px-4 py-2 text-center">
                            <div class="text-sm">
                                @if($schedule->is_24hours)
                                    <span class="px-2 py-1 bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-200 rounded font-semibold">
                                        24 Hours
                                    </span>
                                @else
                                    {{ $schedule->default_start_time }} - {{ $schedule->default_end_time }}
                                    @if($schedule->is_overnight)
                                        <span class="text-xs text-orange-500 block">(Next Day)</span>
                                    @endif
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-2 text-center">
                            @if($schedule->is_24hours)
                                <span class="text-gray-400">N/A</span>
                            @elseif($schedule->has_break)
                                <span class="text-green-500" title="Has 1-hour break">
                                    <i class="fas fa-coffee"></i>
                                </span>
                            @else
                                <span class="text-gray-400">✗</span>
                            @endif
                        </td>
                        <td class="px-4 py-2 text-center">
                            @if($schedule->is_flexi)
                                <span class="text-green-500">✓</span>
                            @else
                                <span class="text-gray-400">✗</span>
                            @endif
                        </td>
                        <td class="px-4 py-2 text-center">{{ $schedule->start_date->format('Y-m-d') }} - {{
                            $schedule->end_date->format('Y-m-d') }}</td>
                        <td class="px-4 py-2 text-center">
                            <button wire:click="edit({{ $schedule->id }})"
                                class="text-indigo-600 hover:text-indigo-900 dark:text-blue-900 dark:hover:text-blue-800"
                                title="Edit">
                                <i class="fas fa-pencil-alt"></i>
                            </button>
                            <button wire:click="confirmDelete({{ $schedule->id }})"
                                class="ml-2 text-red-600 hover:text-red-900 dark:text-red-600 dark:hover:text-red-900"
                                title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $filteredSchedules->links() }}
        </div>
    </div>

    <!-- Import Modal -->
    <x-modal id="importModal" maxWidth="2xl" wire:model="isImportModalOpen">
        <div class="p-6">
            <div class="flex items-center justify-between pb-4 border-b">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-200">
                    <i class="fas fa-file-import text-indigo-500 mr-2"></i>
                    Import Schedules
                </h3>
            </div>

            <div class="mt-4 space-y-4">
                <!-- Instructions -->
                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                    <h4 class="font-semibold text-blue-900 dark:text-blue-200 mb-2">
                        <i class="fas fa-info-circle mr-1"></i>
                        How to Import Schedules
                    </h4>
                    <ol class="list-decimal list-inside space-y-1 text-sm text-blue-800 dark:text-blue-300">
                        <li>Download the Excel template using the "Template" button</li>
                        <li>Fill in the schedule information following the format in the template</li>
                        <li>Save your Excel file</li>
                        <li>Upload the file below to import schedules</li>
                        <li>The system will automatically create new schedules or update existing ones</li>
                    </ol>
                </div>

                <!-- File Upload -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Select Excel File (.xlsx or .xls)
                    </label>
                    <input type="file" wire:model="importFile" accept=".xlsx,.xls"
                        class="block w-full text-sm text-gray-900 dark:text-gray-300
                        file:mr-4 file:py-2 file:px-4
                        file:rounded-md file:border-0
                        file:text-sm file:font-semibold
                        file:bg-indigo-50 file:text-indigo-700
                        hover:file:bg-indigo-100
                        dark:file:bg-indigo-900 dark:file:text-indigo-200
                        dark:hover:file:bg-indigo-800">
                    @error('importFile')
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Loading Indicator -->
                <div wire:loading wire:target="importFile" class="text-sm text-gray-600 dark:text-gray-400">
                    <i class="fas fa-spinner fa-spin mr-2"></i>
                    Processing file...
                </div>

                <!-- Import Errors -->
                @if(!empty($importErrors))
                <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                    <h4 class="font-semibold text-red-900 dark:text-red-200 mb-2">
                        <i class="fas fa-exclamation-triangle mr-1"></i>
                        Import Errors ({{ count($importErrors) }})
                    </h4>
                    <div class="max-h-48 overflow-y-auto">
                        <ul class="list-disc list-inside space-y-1 text-sm text-red-800 dark:text-red-300">
                            @foreach($importErrors as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                @endif

                <!-- Import Summary -->
                @if($importSummary)
                <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
                    <h4 class="font-semibold text-green-900 dark:text-green-200 mb-2">
                        <i class="fas fa-check-circle mr-1"></i>
                        Import Summary
                    </h4>
                    <div class="grid grid-cols-2 gap-2 text-sm">
                        <div class="text-green-800 dark:text-green-300">
                            <span class="font-medium">Total Rows:</span> {{ $importSummary['total'] }}
                        </div>
                        <div class="text-green-800 dark:text-green-300">
                            <span class="font-medium">Created:</span> {{ $importSummary['created'] }}
                        </div>
                        <div class="text-green-800 dark:text-green-300">
                            <span class="font-medium">Updated:</span> {{ $importSummary['updated'] }}
                        </div>
                        <div class="text-red-800 dark:text-red-300">
                            <span class="font-medium">Errors:</span> {{ $importSummary['errors'] }}
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Action Buttons -->
            <div class="mt-6 flex justify-end space-x-3 pt-4 border-t">
                <button type="button" wire:click="closeImportModal"
                    class="px-4 py-2 bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:ring-offset-2 dark:bg-gray-600 dark:text-gray-300 dark:hover:bg-gray-500">
                    Cancel
                </button>
                <button type="button" wire:click="importSchedules"
                    wire:loading.attr="disabled"
                    wire:target="importSchedules"
                    class="px-6 py-2 bg-indigo-500 hover:bg-indigo-600 dark:bg-indigo-600 dark:hover:bg-indigo-700 text-white rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                    <span wire:loading.remove wire:target="importSchedules">
                        <i class="fas fa-upload mr-1"></i>
                        Import Schedules
                    </span>
                    <span wire:loading wire:target="importSchedules">
                        <i class="fas fa-spinner fa-spin mr-1"></i>
                        Importing...
                    </span>
                </button>
            </div>
        </div>
    </x-modal>

    <!-- Schedule Modal using x-modal -->
    <x-modal id="scheduleModal" maxWidth="lg" wire:model="isModalOpen">
        <div class="p-6">
            <div class="flex items-center justify-between pb-4 border-b">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-200">
                    <span x-text="@js($isEditMode) ? 'Edit Schedule' : 'Add Schedule'"></span>
                </h3>
            </div>

            <!-- Form -->
            <form wire:submit.prevent="saveSchedule" class="space-y-4 mt-4">
                @if($isEditMode)
                <div>
                    <label for="emp_code"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Employee</label>
                    <input type="text" id="emp_code_display" readonly
                        class="w-full p-2 border rounded text-gray-700 dark:text-gray-300 dark:bg-gray-700 bg-gray-100"
                        value="{{ $thisEmployeeName }}">
                    @error('emp_code') <span class="text-red-500">{{ 'Employee Field is required!' }}</span> @enderror
                </div>
                @else
                <div>
                    <label for="emp_code"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Employee</label>
                    <select id="emp_code" wire:model="emp_code"
                        class="w-full p-2 border rounded text-gray-700 dark:text-gray-300 dark:bg-gray-700">
                        <option value="" disabled selected>Select an employee</option>
                        @forelse ($employees as $employee)
                            <option value="{{ $employee->emp_code }}">{{ $this->getFormattedName($employee) }}</option>
                        @empty
                            <option disabled>No employees found</option>
                        @endforelse
                    </select>
                    @error('emp_code') <span class="text-red-500">{{ 'Employee Field is required!' }}</span> @enderror
                </div>
                @endif

                <div class="flex space-x-4" x-show="!is24hours">
                    <div class="w-1/2">
                        <label for="default_start_time"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Start Time</label>
                        <input id="default_start_time" type="time" wire:model="default_start_time"
                            class="w-full p-2 border rounded text-gray-700 dark:text-gray-300 dark:bg-gray-700"
                            :disabled="is24hours">
                        @error('default_start_time') <span class="text-red-500">{{ $message }}</span> @enderror
                    </div>

                    <div class="w-1/2">
                        <label for="default_end_time"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">End Time</label>
                        <input id="default_end_time" type="time" wire:model="default_end_time"
                            class="w-full p-2 border rounded text-gray-700 dark:text-gray-300 dark:bg-gray-700"
                            :disabled="is24hours">
                        @error('default_end_time') <span class="text-red-500">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- 24 Hours Notice -->
                <div x-show="is24hours" class="bg-purple-100 dark:bg-purple-900 border border-purple-300 dark:border-purple-600 rounded p-3">
                    <div class="flex items-center">
                        <i class="fas fa-clock text-purple-500 mr-2"></i>
                        <span class="text-sm text-purple-700 dark:text-purple-300">
                            24-hour schedule selected. Time fields are disabled.
                        </span>
                    </div>
                </div>

                <!-- Overnight shift warning -->
                <div x-show="isOvernight && !is24hours" class="bg-orange-100 dark:bg-orange-900 border border-orange-300 dark:border-orange-600 rounded p-3">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-triangle text-orange-500 mr-2"></i>
                        <span class="text-sm text-orange-700 dark:text-orange-300">
                            This appears to be an overnight shift (ends the next day)
                        </span>
                    </div>
                </div>

                <div class="flex space-x-4">
                    <div class="w-1/2">
                        <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Start
                            Date</label>
                        <input id="start_date" type="date" wire:model="start_date"
                            class="w-full p-2 border rounded text-gray-700 dark:text-gray-300 dark:bg-gray-700">
                        @error('start_date') <span class="text-red-500">{{ $message }}</span> @enderror
                    </div>

                    <div class="w-1/2">
                        <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">End
                            Date</label>
                        <input id="end_date" type="date" wire:model="end_date"
                            class="w-full p-2 border rounded text-gray-700 dark:text-gray-300 dark:bg-gray-700">
                        @error('end_date') <span class="text-red-500">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div>
                    @error('date_range') <span class="text-red-500">{{ $message }}</span> @enderror
                </div>

                <!-- Schedule Options -->
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 space-y-3">
                    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">Schedule Options</h4>

                    <div class="flex flex-col space-y-2">
                        <label class="inline-flex items-center">
                            <input type="checkbox" wire:model.live="is_24hours" class="form-checkbox text-purple-500 rounded">
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                <i class="fas fa-clock mr-1"></i>
                                24-Hour Schedule
                            </span>
                        </label>

                        <label class="inline-flex items-center" x-show="!is24hours">
                            <input type="checkbox" wire:model="is_flexi" class="form-checkbox text-blue-500 rounded">
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                <i class="fas fa-clock mr-1"></i>
                                Flexible Schedule
                            </span>
                        </label>

                        <label class="inline-flex items-center" x-show="!is24hours">
                            <input type="checkbox" wire:model="has_break" class="form-checkbox text-green-500 rounded">
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                <i class="fas fa-coffee mr-1"></i>
                                One Hour Break
                            </span>
                        </label>

                        <label class="inline-flex items-center" x-show="!is24hours">
                            <input type="checkbox" wire:model="is_overnight" class="form-checkbox text-orange-500 rounded">
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                <i class="fas fa-moon mr-1"></i>
                                Overnight Shift
                            </span>
                        </label>
                    </div>
                </div>

                <!-- Rest Days Selection -->
                <div>
                    <label for="rest_days" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        <i class="fas fa-bed mr-1"></i>
                        Rest Days <span class="text-red-500">*</span>
                    </label>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                        @foreach (['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $day)
                        <label class="inline-flex items-center p-2 bg-red-50 dark:bg-red-900/20 rounded hover:bg-red-100 dark:hover:bg-red-900/40 cursor-pointer border border-red-200 dark:border-red-800">
                            <input type="checkbox" wire:model="rest_days" value="{{ $day }}"
                                class="form-checkbox text-red-500 rounded mr-2">
                            <span class="text-sm text-gray-700 dark:text-gray-300">{{ $day }}</span>
                        </label>
                        @endforeach
                    </div>
                    @error('rest_days') <span class="text-red-500">{{ $message }}</span> @enderror
                </div>

                <!-- WFH Days Selection -->
                <div>
                    <label for="wfh_days" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        <i class="fas fa-home mr-1"></i>
                        Work From Home Days (Optional)
                    </label>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                        @foreach (['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $day)
                        <label class="inline-flex items-center p-2 bg-blue-50 dark:bg-blue-900/20 rounded hover:bg-blue-100 dark:hover:bg-blue-900/40 cursor-pointer border border-blue-200 dark:border-blue-800">
                            <input type="checkbox" wire:model="wfh_days" value="{{ $day }}"
                                class="form-checkbox text-blue-500 rounded mr-2">
                            <span class="text-sm text-gray-700 dark:text-gray-300">{{ substr($day, 0, 3) }}</span>
                        </label>
                        @endforeach
                    </div>
                    @error('wfh_days') <span class="text-red-500">{{ $message }}</span> @enderror
                </div>

                <!-- Save Button -->
                <div class="mt-6 flex justify-end space-x-3 pt-4 border-t">
                    <button type="button" wire:click="closeModal"
                        class="px-4 py-2 bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:ring-offset-2 dark:bg-gray-600 dark:text-gray-300 dark:hover:bg-gray-500">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-6 py-2 bg-blue-500 hover:bg-blue-600 dark:bg-blue-600 dark:hover:bg-blue-700 text-white rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                        {{ $isEditMode ? 'Update Schedule' : 'Save Schedule' }}
                    </button>
                </div>
            </form>
        </div>
    </x-modal>

    <!-- Delete Confirmation Modal using x-modal -->
    <x-modal id="deleteConfirmationModal" maxWidth="md" wire:model="confirmingScheduleDeletion">
        <div class="p-6">
            <div class="flex items-center justify-between pb-4 border-b">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-200">
                    <i class="fas fa-exclamation-triangle text-red-500 mr-2"></i>
                    Confirm Deletion
                </h3>
            </div>

            <div class="mt-4">
                <p class="text-sm text-gray-700 dark:text-gray-300">
                    Are you sure you want to delete this schedule? This action cannot be undone.
                </p>
            </div>

            <div class="mt-6 flex justify-end space-x-3 pt-4 border-t">
                <button wire:click="closeConfirmationModal"
                    class="px-4 py-2 bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:ring-offset-2 dark:bg-gray-600 dark:text-gray-300 dark:hover:bg-gray-500">
                    Cancel
                </button>
                <button wire:click="deleteConfirmed"
                    class="px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:bg-red-600 dark:hover:bg-red-700">
                    <i class="fas fa-trash mr-1"></i>
                    Delete
                </button>
            </div>
        </div>
    </x-modal>

    <div class="mt-12 w-full">
        @livewire('admin.holiday-schedule')
    </div>
</div>