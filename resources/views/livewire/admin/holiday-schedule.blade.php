<div x-data="{
    isModalOpen: @entangle('isModalOpen'),
    isEditMode: @entangle('isEditMode'),
    confirmingHolidayDeletion: @entangle('confirmingHolidayDeletion')
}" x-cloak>
    <div class="w-full bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-md text-sm">
        <h1 class="text-lg font-bold text-center text-black dark:text-white mb-6">Holidays</h1>

        <!-- Modal -->
        <div x-show="isModalOpen" class="fixed inset-0 z-50 overflow-y-auto bg-black bg-opacity-40 flex items-center justify-center">
            <div @click.away="isModalOpen = false" x-show="isModalOpen" 
                x-transition:enter="transition ease-out duration-300" 
                x-transition:enter-start="opacity-0 translate-y-4" 
                x-transition:enter-end="opacity-100 translate-y-0" 
                x-transition:leave="transition ease-in duration-200" 
                x-transition:leave-start="opacity-100 translate-y-0" 
                x-transition:leave-end="opacity-0 translate-y-4" 
                class="relative bg-white dark:bg-gray-800 p-6 mx-4 sm:mx-auto w-full max-w-4xl rounded-2xl shadow-lg">
                
                <div class="flex items-center justify-between pb-4 border-b dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-200">
                        <span x-text="isEditMode ? 'Edit Holiday' : 'Add Holiday'"></span>
                    </h3>
                    <button wire:click="closeModal" class="text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form wire:submit.prevent="saveHoliday" class="space-y-4 mt-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Holiday Name <span class="text-red-500">*</span>
                            </label>
                            <input id="description" 
                                   type="text" 
                                   wire:model="description" 
                                   placeholder="Enter holiday name" 
                                   class="w-full p-2 border rounded text-gray-700 dark:text-gray-300 dark:bg-gray-700 dark:border-gray-600 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('description') 
                                <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> 
                            @enderror
                        </div>

                        <div>
                            <label for="holiday_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Date <span class="text-red-500">*</span>
                            </label>
                            <input id="holiday_date" 
                                   type="date" 
                                   wire:model="holiday_date" 
                                   class="w-full p-2 border rounded text-gray-700 dark:text-gray-300 dark:bg-gray-700 dark:border-gray-600 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('holiday_date') 
                                <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> 
                            @enderror
                        </div>

                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Type <span class="text-red-500">*</span>
                            </label>
                            <select id="type" 
                                    wire:model="type" 
                                    class="w-full p-2 border rounded text-gray-700 dark:text-gray-300 dark:bg-gray-700 dark:border-gray-600 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Select Type</option>
                                <option value="Regular">Regular</option>
                                <option value="Special">Special</option>
                            </select>
                            @error('type') 
                                <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> 
                            @enderror
                        </div>

                        <div>
                            <label for="region_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Region <span class="text-gray-500 text-xs">(Optional - Leave blank for nationwide)</span>
                            </label>
                            <select id="region_id" 
                                    wire:model="region_id" 
                                    class="w-full p-2 border rounded text-gray-700 dark:text-gray-300 dark:bg-gray-700 dark:border-gray-600 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Nationwide</option>
                                @foreach($regions as $region)
                                    <option value="{{ $region->id }}">{{ $region->region_description }}</option>
                                @endforeach
                            </select>
                            @error('region_id') 
                                <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> 
                            @enderror
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end space-x-2 pt-4 border-t dark:border-gray-700">
                        <button type="button" 
                                wire:click="closeModal" 
                                class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-blue-600 dark:hover:bg-blue-700">
                            <span x-text="isEditMode ? 'Update' : 'Save'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Filters and Add Button Section -->
        <div class="mb-6 space-y-4">
            <!-- Add Holiday Button -->
            <div class="flex justify-start">
                <button wire:click="openModal" 
                        class="inline-flex items-center px-4 py-2 bg-green-500 text-white text-sm font-medium rounded-md hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:bg-green-600 dark:hover:bg-green-700 shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Add Holiday
                </button>
            </div>

            <!-- Filters Row -->
            <div class="flex flex-col sm:flex-row gap-3 items-stretch sm:items-center">
                <div class="flex items-center gap-2">
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300 whitespace-nowrap">Filters:</label>
                </div>
                
                <!-- Region Filter -->
                <div class="flex-1 sm:flex-initial sm:min-w-[200px]">
                    <select wire:model.live="filterRegion" 
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md text-gray-700 dark:text-gray-300 dark:bg-gray-700 dark:border-gray-600 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Regions</option>
                        <option value="nationwide">Nationwide Only</option>
                        @foreach($regions as $region)
                            <option value="{{ $region->id }}">{{ $region->region_description }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Type Filter -->
                <div class="flex-1 sm:flex-initial sm:min-w-[180px]">
                    <select wire:model.live="filterType" 
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md text-gray-700 dark:text-gray-300 dark:bg-gray-700 dark:border-gray-600 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Types</option>
                        <option value="Regular">Regular</option>
                        <option value="Special">Special</option>
                    </select>
                </div>

                <!-- Reset Filters Button -->
                @if($filterRegion || $filterType)
                    <div class="flex-shrink-0">
                        <button wire:click="resetFilters" 
                                class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 text-sm font-medium bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            Reset
                        </button>
                    </div>
                @endif
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
            <table class="min-w-full bg-white dark:bg-gray-800">
                <thead class="bg-gray-200 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                            Holiday Name
                        </th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                            Date
                        </th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                            Type
                        </th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                            Region
                        </th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($holidays as $holiday)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150">
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-200">
                                {{ $holiday->description }}
                            </td>
                            <td class="px-4 py-3 text-sm text-center text-gray-700 dark:text-gray-300">
                                {{ $holiday->holiday_date->format('M d, Y (D)') }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $holiday->type === 'Regular' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' }}">
                                    {{ $holiday->type }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-center">
                                @if($holiday->region)
                                    <span class="text-gray-700 dark:text-gray-300">{{ $holiday->region->region_description }}</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200">
                                        Nationwide
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center space-x-3">
                                    <button wire:click="edit({{ $holiday->id }})" 
                                            class="text-indigo-600 hover:text-indigo-900 dark:text-blue-400 dark:hover:text-blue-300 transition-colors" 
                                            title="Edit">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>
                                    <button wire:click="confirmDelete({{ $holiday->id }})" 
                                            class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 transition-colors" 
                                            title="Delete">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center">
                                <div class="flex flex-col items-center justify-center text-gray-500 dark:text-gray-400">
                                    <svg class="w-12 h-12 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                    </svg>
                                    <p class="text-sm font-medium">No holidays found</p>
                                    <p class="text-xs mt-1">Try adjusting your filters or add a new holiday</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $holidays->links() }}
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div x-show="confirmingHolidayDeletion" 
         class="fixed inset-0 z-50 overflow-y-auto bg-black bg-opacity-40 flex items-center justify-center">
        <div @click.away="confirmingHolidayDeletion = false" 
             x-show="confirmingHolidayDeletion" 
             x-transition:enter="transition ease-out duration-300" 
             x-transition:enter-start="opacity-0 translate-y-4" 
             x-transition:enter-end="opacity-100 translate-y-0" 
             x-transition:leave="transition ease-in duration-200" 
             x-transition:leave-start="opacity-100 translate-y-0" 
             x-transition:leave-end="opacity-0 translate-y-4" 
             class="relative bg-white dark:bg-gray-800 p-6 mx-4 sm:mx-auto w-full max-w-lg rounded-2xl shadow-lg">
            
            <div class="flex items-center justify-between pb-4 border-b dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-200">Confirm Deletion</h3>
                <button wire:click="closeConfirmationModal" 
                        class="text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <div class="mt-4">
                <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 dark:bg-red-900 rounded-full">
                    <svg class="w-6 h-6 text-red-600 dark:text-red-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <p class="mt-4 text-center text-gray-800 dark:text-gray-300">
                    Are you sure you want to delete this holiday? This action cannot be undone.
                </p>
            </div>

            <div class="mt-6 flex justify-end space-x-2">
                <button wire:click="closeConfirmationModal" 
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300 dark:bg-gray-600 dark:text-gray-300 dark:hover:bg-gray-500">
                    Cancel
                </button>
                <button wire:click="deleteConfirmed" 
                        class="px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-500 dark:bg-red-600 dark:hover:bg-red-700">
                    Delete Holiday
                </button>
            </div>
        </div>
    </div>
</div>