<div class="w-full flex flex-col justify-center">

    <div class="w-full bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-md">
        <h1 class="text-lg font-bold text-center text-black dark:text-white mb-6">Admin Daily Time Record</h1>

        <!-- Search and Date Range Picker (Unchanged) -->
        <div class="mb-6 flex flex-col sm:flex-row items-end justify-between space-y-4 sm:space-y-0">
            <!-- Search Input -->
            <div class="w-full sm:w-1/3 sm:mr-4">
                <label for="search" class="block text-sm font-medium text-gray-700 dark:text-slate-400 mb-1">Search</label>
                <input type="text" id="search" wire:model.live="searchTerm"
                    class="px-2 py-1.5 block w-full shadow-sm sm:text-sm border border-gray-400 hover:bg-gray-300 rounded-md
                        dark:hover:bg-slate-600 dark:border-slate-600
                        dark:text-gray-300 dark:bg-gray-800"
                    placeholder="Enter employee name or ID">
            </div>

            <!-- Office Division Dropdown -->
            <div class="w-full sm:w-1/3 sm:mr-4">
                <label for="officeDivision" class="block text-sm font-medium text-gray-700 dark:text-slate-400 mb-1">Office Division</label>
                <select id="officeDivision" wire:model.live="selectedDivision"
                    class="px-2 py-1.5 block w-full shadow-sm sm:text-sm border border-gray-400 hover:bg-gray-300 rounded-md
                        dark:hover:bg-slate-600 dark:border-slate-600
                        dark:text-gray-300 dark:bg-gray-800">
                    <option value="">All Divisions</option>
                    @foreach($officeDivisions as $division)
                        <option value="{{ $division->id }}">{{ $division->office_division }}</option>
                    @endforeach
                </select>
            </div>
            {{-- Appointment --}}
            <div class="w-full sm:w-1/3 sm:mr-4">
                <label for="appointmentFilter" class="block text-sm font-medium text-gray-700 dark:text-slate-400 mb-1">Appointment</label>
                <select id="appointmentFilter" wire:model.live="selectedAppointment"
                    class="px-2 py-1.5 block w-full shadow-sm sm:text-sm border border-gray-400 hover:bg-gray-300 rounded-md
                        dark:hover:bg-slate-600 dark:border-slate-600
                        dark:text-gray-300 dark:bg-gray-800">
                    <option value="">All Appointments</option>
                    <option value="plantilla">Plantilla</option>
                    <option value="cos">COS</option>
                </select>
            </div>

            <!-- Date Range Picker -->
            <div class="w-full sm:w-2/3 flex flex-col sm:flex-row sm:justify-end sm:space-x-4">
                <div class="w-full sm:w-auto">
                    <label for="startDate" class="block text-sm font-medium text-gray-700 dark:text-slate-400 mb-1">Start Date</label>
                    <input type="date" id="startDate" wire:model.live="startDate"
                        class="px-2 py-1.5 block w-full shadow-sm sm:text-sm border border-gray-400 hover:bg-gray-300 rounded-md
                            dark:hover:bg-slate-600 dark:border-slate-600
                            dark:text-gray-300 dark:bg-gray-800">
                </div>

                <div class="w-full sm:w-auto mt-4 sm:mt-0">
                    <label for="endDate" class="block text-sm font-medium text-gray-700 dark:text-slate-400 mb-1">End Date</label>
                    <input type="date" id="endDate" wire:model.live="endDate"
                        class="px-2 py-1.5 block w-full shadow-sm sm:text-sm border border-gray-400 hover:bg-gray-300 rounded-md
                            dark:hover:bg-slate-600 dark:border-slate-600
                            dark:text-gray-300 dark:bg-gray-800">
                </div>
            </div>

            <div class="ml-4" x-data="{ showModal: false, signatoryName: '' }" x-cloak>
                <!-- Trigger Button -->
                <button @click="showModal = true" class="inline-flex items-center dark:hover:bg-slate-600 dark:border-slate-600
                                    justify-center px-2 py-1.5 text-sm font-medium tracking-wide
                                    text-neutral-800 dark:text-neutral-200 transition-colors duration-200
                                    rounded-lg border border-gray-400 hover:bg-gray-300 focus:outline-none">
                    <img src="{{ asset('images/icons8-export-pdf-60.png') }}" alt="Export to PDF" class="w-10" wire:loading.remove wire:target="exportToPdf">
                    <div wire:loading wire:target="exportToPdf">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true" class="size-6 fill-red-600 motion-safe:animate-spin dark:fill-red-600">
                            <path d="M12,1A11,11,0,1,0,23,12,11,11,0,0,0,12,1Zm0,19a8,8,0,1,1,8-8A8,8,0,0,1,12,20Z" opacity=".25" />
                            <path d="M10.14,1.16a11,11,0,0,0-9,8.92A1.59,1.59,0,0,0,2.46,12,1.52,1.52,0,0,0,4.11,10.7a8,8,0,0,1,6.66-6.61A1.42,1.42,0,0,0,12,2.69h0A1.57,1.57,0,0,0,10.14,1.16Z" />
                        </svg>
                    </div>
                </button>

                <!-- Modal -->
                <div x-show="showModal" class="fixed inset-0 z-50 overflow-y-auto bg-black bg-opacity-40 flex items-center justify-center">
                    <div @click.away="showModal = false"
                         x-show="showModal"
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 translate-y-4"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-200"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 translate-y-4"
                         class="bg-white dark:bg-gray-800 rounded-lg p-4 sm:p-6 w-11/12 sm:w-1/2 lg:w-1/3 mx-4">
                        <div class="flex flex-col justify-mt-5">
                            <h2 class="text-lg font-semibold mb-5 text-left text-gray-900 dark:text-gray-100">Confirm Action</h2>
                            <div class="flex justify-end">
                            <button @click="showModal = false" class="bg-gray-300 text-gray-800 px-4 py-2 rounded-md mb-2 sm:mb-0 sm:mr-2 dark:bg-gray-600 dark:text-gray-200">Cancel</button>
                            <button @click="showModal = false; $wire.exportToPdf(signatoryName)" class="bg-blue-500 text-white px-4 py-2 rounded-md">Generate PDF</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Signatory Modal --}}
        @if($showSignatoryModal)
            <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                    <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75 dark:bg-gray-900 dark:bg-opacity-75" aria-hidden="true"></div>
                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                    <div class="inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6 dark:bg-gray-800">
                        <div class="sm:flex sm:items-start">
                            <div class="w-full mt-3 text-center sm:mt-0 sm:text-left">
                                <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100" id="modal-title">
                                    Edit Division Signatory
                                </h3>
                                <div class="mt-4">
                                    <label for="signName" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Signatory Name</label>
                                    <input type="text" wire:model="signName" id="signName"
                                        class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:focus:ring-indigo-300 dark:focus:border-indigo-300 dark:text-gray-100">
                                </div>
                                <div class="mt-4">
                                    <label for="signPos" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Position</label>
                                    <input type="text" wire:model="signPos" id="signPos"
                                        class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:focus:ring-indigo-300 dark:focus:border-indigo-300 dark:text-gray-100">
                                </div>
                            </div>
                        </div>
                        <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                            <button wire:click="saveSignatory" type="button"
                                    class="inline-flex justify-center w-full px-4 py-2 text-base font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm dark:focus:ring-indigo-300">
                                Save
                            </button>
                            <button wire:click="$set('showSignatoryModal', false)" type="button"
                                    class="inline-flex justify-center w-full px-4 py-2 mt-3 text-base font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600 dark:hover:bg-gray-600 dark:focus:ring-indigo-300">
                                Cancel
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Table -->
        <div class="overflow-x-auto text-sm">
            <table class="min-w-full bg-white dark:bg-gray-800 overflow-hidden">
                <thead class="bg-gray-200 dark:bg-gray-700 rounded-xl">
                    <tr class="whitespace-nowrap">
                        <th class="px-4 py-2 text-left">Employee</th>
                        <th class="px-4 py-2 text-center">Employee ID</th>
                        <th class="px-4 py-2 text-center">Date</th>
                        <th class="px-4 py-2 text-center">Day</th>
                        <th class="px-4 py-2 text-center">Location</th>
                        <th class="px-4 py-2 text-center">Time In</th>
                        <th class="px-4 py-2 text-center">Break Out</th>
                        <th class="px-4 py-2 text-center">Break In</th>
                        <th class="px-4 py-2 text-center">Time Out</th>
                        <th class="px-4 py-2 text-center">Late</th>
                        <th class="px-4 py-2 text-center">Undertime</th>
                        <th class="px-4 py-2 text-center">Overtime</th>
                        <th class="px-4 py-2 text-center">Hours Rendered</th>
                        <th class="px-4 py-2 text-center">Updated By</th>
                        <th class="px-4 py-2 text-center">Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($dtrs as $dtr)
                        <tr class="whitespace-nowrap">
                            <td class="px-4 py-2 text-left">
                                <div class="flex gap-3 items-center">
                                    @if ($dtr->profile_photo_path)
                                        <img src="{{ route('profile-photo.file', ['filename' => basename($dtr->profile_photo_path)]) }}"
                                            alt="{{ $dtr?->user_name ?? 'No User Assigned' }}"
                                            width="32" height="32"
                                            class="w-10 h-10 rounded-full object-cover border border-gray-500">
                                    @else
                                        <div class="w-10 h-10 rounded-full bg-gray-500 border border-gray-500 dark:bg-gray-600 flex items-center justify-center text-white text-xs font-medium">
                                            {{ strtoupper(substr(($dtr?->user_name ?? 'No User Assigned'), 0, 1)) }}{{ strtoupper(substr(explode(' ', ($dtr?->user_name ?? 'No User Assigned'))[1] ?? '', 0, 1)) }}
                                        </div>
                                    @endif
                                    <span>{{ $dtr->user_name }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-2 text-center">{{ $dtr->emp_code }}</td>
                            <td class="px-4 py-2 text-center">{{ $dtr->date }}</td>
                            <td class="px-4 py-2 text-center">{{ $dtr->day_of_week }}</td>
                            <td class="px-4 py-2 text-center">
                                {{ $dtr->location === 'Onsite' ? '' : $dtr->location }}
                            </td>

                            <!-- Use updated values if available -->
                            <td class="px-4 py-2 text-center">{{ $dtr->up_time_in ?? $dtr->time_in ?? '--:--' }}</td>
                            <td class="px-4 py-2 text-center">{{ $dtr->up_break_out ?? $dtr->break_out ?? '--:--' }}</td>
                            <td class="px-4 py-2 text-center">{{ $dtr->up_break_in ?? $dtr->break_in ?? '--:--' }}</td>
                            <td class="px-4 py-2 text-center">{{ $dtr->up_time_out ?? $dtr->time_out ?? '--:--' }}</td>
                            <td class="px-4 py-2 text-center">{{ $dtr->up_late ?? $dtr->late ?? '00:00' }}</td>
                            <td class="px-4 py-2 text-center">{{ $dtr->up_ut ?? $dtr->ut ?? '00:00' }}</td>
                            <td class="px-4 py-2 text-center">{{ $dtr->up_ot ?? $dtr->overtime ?? '00:00' }}</td>

                            <td class="px-4 py-2 text-center">{{ $dtr->up_total_hours_rendered ?? $dtr->total_hours_rendered ?? '00:00' }}</td>

                            <!-- Updated By -->
                            <td class="px-4 py-2 text-center">
                                {{ $dtr->updated_by ?? '-' }}
                            </td>

                            <!-- Remarks -->
                            <td class="px-4 py-2 text-center">
                                @php
                                    $effectiveRemarks = $dtr->up_remarks ?: $dtr->effective_remarks;
                                    $late = $dtr->up_late ?? $dtr->late;
                                    $ut = $dtr->up_ut ?? $dtr->ut;

                                    // Check if late or undertime values are not zero/empty
                                    $hasLate = !in_array($late, ['00:00', '00:00:00', '0:00', null, '']);
                                    $hasUndertime = !in_array($ut, ['00:00', '00:00:00', '0:00', null, '']);
                                @endphp

                                @switch(strtolower($effectiveRemarks))
                                    @case('absent')
                                        <span class="w-fit inline-flex overflow-hidden rounded-2xl border border-red-600 bg-white text-xs font-medium text-red-600 dark:border-red-600 dark:bg-slate-900 dark:text-red-600">
                                            <span class="px-2 py-1 bg-red-600/10 dark:bg-red-600/10">{{ ucfirst($effectiveRemarks) }}</span>
                                        </span>
                                        @break

                                    @case('leave')
                                        <span class="w-fit inline-flex overflow-hidden rounded-2xl border border-sky-600 bg-white text-xs font-medium text-sky-600 dark:border-sky-600 dark:bg-slate-900 dark:text-sky-600">
                                            <span class="px-2 py-1 bg-sky-600/10 dark:bg-sky-600/10">{{ ucfirst($effectiveRemarks) }}</span>
                                        </span>
                                        @break

                                    @case('holiday')
                                        <span class="w-fit inline-flex overflow-hidden rounded-2xl border border-blue-600 bg-white text-xs font-medium text-blue-600 dark:border-blue-600 dark:bg-slate-900 dark:text-blue-600">
                                            <span class="px-2 py-1 bg-blue-600/10 dark:bg-blue-600/10">{{ ucfirst($effectiveRemarks) }}</span>
                                        </span>
                                        @break

                                    @case('present')
                                        <span class="w-fit inline-flex overflow-hidden rounded-2xl border border-green-600 bg-white text-xs font-medium text-green-600 dark:border-green-600 dark:bg-slate-900 dark:text-green-600">
                                            <span class="px-2 py-1 bg-green-600/10 dark:bg-green-600/10">Present</span>
                                        </span>
                                        @break

                                    @case('late')
                                        <span class="w-fit inline-flex overflow-hidden rounded-2xl border border-yellow-600 bg-white text-xs font-medium text-yellow-600 dark:border-yellow-600 dark:bg-slate-900 dark:text-yellow-600">
                                            <span class="px-2 py-1 bg-yellow-600/10 dark:bg-yellow-600/10">Late</span>
                                        </span>
                                        @break

                                    @case('undertime')
                                        <span class="w-fit inline-flex overflow-hidden rounded-2xl border border-orange-600 bg-white text-xs font-medium text-orange-600 dark:border-orange-600 dark:bg-slate-900 dark:text-orange-600">
                                            <span class="px-2 py-1 bg-orange-600/10 dark:bg-orange-600/10">Undertime</span>
                                        </span>
                                        @break

                                    @case('late/undertime')
                                        <span class="w-fit inline-flex overflow-hidden rounded-2xl border border-red-500 bg-white text-xs font-medium text-red-500 dark:border-red-500 dark:bg-slate-900 dark:text-red-500">
                                            <span class="px-2 py-1 bg-red-500/10 dark:bg-red-500/10">Late/Undertime</span>
                                        </span>
                                        @break

                                    @case('incomplete')
                                        <span class="w-fit inline-flex overflow-hidden rounded-2xl border border-purple-600 bg-white text-xs font-medium text-purple-600 dark:border-purple-600 dark:bg-slate-900 dark:text-purple-600">
                                            <span class="px-2 py-1 bg-purple-600/10 dark:bg-purple-600/10">Incomplete</span>
                                        </span>
                                        @break

                                    @case('saturday')
                                    @case('sunday')
                                        <span class="w-fit inline-flex overflow-hidden rounded-2xl border border-gray-600 bg-white text-xs font-medium text-gray-600 dark:border-gray-600 dark:bg-slate-900 dark:text-gray-600">
                                            <span class="px-2 py-1 bg-gray-600/10 dark:bg-gray-600/10">{{ ucfirst($effectiveRemarks) }}</span>
                                        </span>
                                        @break

                                    @default
                                        <span class="w-fit inline-flex overflow-hidden rounded-2xl border border-slate-300 bg-white text-xs font-medium text-slate-700 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300">
                                            <span class="px-2 py-1 bg-slate-100/10 dark:bg-slate-800/10">{{ ucfirst($effectiveRemarks) }}</span>
                                        </span>
                                @endswitch

                                <button wire:click="openEditModal({{ $dtr->id }})" class="ml-2 text-indigo-600 hover:text-indigo-900">
                                    <i class="bi bi-pencil-square"></i> Edit
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="15" class="px-4 py-2 text-center">No records found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Edit Modal --}}
        @if($showEditModal)
            <div class="fixed inset-0 z-50 overflow-y-auto bg-black bg-opacity-40 flex items-center justify-center" style="z-index: 9999">
                <div class="relative bg-white dark:bg-gray-800 p-6 mx-4 md:mx-auto max-w-lg w-full md:max-w-lg rounded-2xl">
                    <!-- Modal header -->
                    <div class="flex items-center justify-between pb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-200">
                            Edit DTR
                        </h3>
                        <button wire:click="closeEditModal"
                            class="text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 focus:outline-none">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                                </path>
                            </svg>
                        </button>
                    </div>

                    <!-- Modal body -->
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Time In</label>
                                <input type="time" wire:model="editData.time_in"
                                    class="w-full p-2 border rounded text-gray-700 dark:text-gray-300 dark:bg-gray-700" />
                                @error('editData.time_in') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Break Out</label>
                                <input type="time" wire:model="editData.break_out"
                                    class="w-full p-2 border rounded text-gray-700 dark:text-gray-300 dark:bg-gray-700" />
                                @error('editData.break_out') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Break In</label>
                                <input type="time" wire:model="editData.break_in"
                                    class="w-full p-2 border rounded text-gray-700 dark:text-gray-300 dark:bg-gray-700" />
                                @error('editData.break_in') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Time Out</label>
                                <input type="time" wire:model="editData.time_out"
                                    class="w-full p-2 border rounded text-gray-700 dark:text-gray-300 dark:bg-gray-700" />
                                @error('editData.time_out') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Late</label>
                                <input type="text" wire:model="editData.late"
                                    class="w-full p-2 border rounded text-gray-700 dark:text-gray-300 dark:bg-gray-700" />
                                @error('editData.late') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Undertime</label>
                                <input type="text" wire:model="editData.ut"
                                    class="w-full p-2 border rounded text-gray-700 dark:text-gray-300 dark:bg-gray-700" />
                                @error('editData.ut') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Overtime</label>
                                <input type="text" wire:model="editData.overtime"
                                    class="w-full p-2 border rounded text-gray-700 dark:text-gray-300 dark:bg-gray-700" />
                                @error('editData.overtime') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Hours Rendered</label>
                                <input type="text" wire:model="editData.total_hours_rendered"
                                    class="w-full p-2 border rounded text-gray-700 dark:text-gray-300 dark:bg-gray-700" />
                                @error('editData.total_hours_rendered') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Remarks</label>
                                <input type="text" wire:model="editData.effective_remarks"
                                    class="w-full p-2 border rounded text-gray-700 dark:text-gray-300 dark:bg-gray-700" />
                                @error('editData.effective_remarks') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- Modal footer -->
                        <div class="mt-6 flex justify-end space-x-2">
                            <button wire:click="closeEditModal"
                                class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400 dark:bg-gray-600 dark:hover:bg-gray-500 dark:text-gray-200">
                                Cancel
                            </button>
                            <button wire:click="saveEdit"
                                class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 dark:bg-indigo-700 dark:hover:bg-indigo-600">
                                Save
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Pagination -->
        <div class="mt-4">
            {{ $dtrs->links() }}
        </div>
    </div>

    <div class="w-full mt-8 bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-md">
        <div class="mb-4">
            <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100 text-center">DTR Signatories</h3>
            <div class="mt-4 overflow-x-auto text-sm">
                <table class="min-w-full bg-white dark:bg-gray-800 rounded-lg shadow">
                    <thead class="bg-gray-200 dark:bg-gray-700 rounded-xl">
                        <tr class="whitespace-nowrap">
                            <th class="px-4 py-2 text-center">Office Division</th>
                            <th class="px-4 py-2 text-center">Unit</th>
                            <th class="px-4 py-2 text-center">Signatory Name</th>
                            <th class="px-4 py-2 text-center">Signatory Position</th>
                            <th class="px-4 py-2 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($officeDivisions as $division)
                            <!-- Division Signatory Row -->
                            <tr class="whitespace-nowrap border-b border-gray-200 dark:border-gray-700">
                                <td class="px-4 py-2 text-center font-medium">{{ $division->office_division }}</td>
                                <td class="px-4 py-2 text-center text-gray-500 dark:text-gray-400">-</td>
                                <td class="px-4 py-2 text-center">{{ $division->sign_name ?? 'N/A' }}</td>
                                <td class="px-4 py-2 text-center">{{ $division->sign_pos ?? 'N/A' }}</td>
                                <td class="px-4 py-2 text-center">
                                    <button wire:click="openSignatoryModal({{ $division->id }})"
                                            class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-indigo-300">
                                        Edit
                                    </button>
                                </td>
                            </tr>

                            <!-- Unit Signatory Rows -->
                            @foreach($division->units as $unit)
                                <tr class="whitespace-nowrap border-b border-gray-200 dark:border-gray-700">
                                    <td class="px-4 py-2 text-center text-gray-500 dark:text-gray-400">
                                        {{ $division->office_division }}
                                    </td>
                                    <td class="px-4 py-2 text-center font-medium">{{ $unit->unit }}</td>
                                    <td class="px-4 py-2 text-center">{{ $unit->sign_name ?? 'N/A' }}</td>
                                    <td class="px-4 py-2 text-center">{{ $unit->sign_pos ?? 'N/A' }}</td>
                                    <td class="px-4 py-2 text-center">
                                        <button wire:click="openUnitSignatoryModal({{ $unit->id }})"
                                                class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-indigo-300">
                                            Edit
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Unit Signatory Modal -->
    @if($showUnitSignatoryModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75 dark:bg-gray-900 dark:bg-opacity-75" aria-hidden="true"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6 dark:bg-gray-800">
                    <div class="sm:flex sm:items-start">
                        <div class="w-full mt-3 text-center sm:mt-0 sm:text-left">
                            <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100" id="modal-title">
                                Edit Unit Signatory
                            </h3>
                            <div class="mt-4">
                                <label for="unitSignName" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Signatory Name</label>
                                <input type="text" wire:model="unitSignName" id="unitSignName"
                                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:focus:ring-indigo-300 dark:focus:border-indigo-300 dark:text-gray-100">
                            </div>
                            <div class="mt-4">
                                <label for="unitSignPos" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Position</label>
                                <input type="text" wire:model="unitSignPos" id="unitSignPos"
                                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:focus:ring-indigo-300 dark:focus:border-indigo-300 dark:text-gray-100">
                            </div>
                        </div>
                    </div>
                    <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                        <button wire:click="saveUnitSignatory" type="button"
                                class="inline-flex justify-center w-full px-4 py-2 text-base font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm dark:focus:ring-indigo-300">
                            Save
                        </button>
                        <button wire:click="$set('showUnitSignatoryModal', false)" type="button"
                                class="inline-flex justify-center w-full px-4 py-2 mt-3 text-base font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600 dark:hover:bg-gray-600 dark:focus:ring-indigo-300">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
