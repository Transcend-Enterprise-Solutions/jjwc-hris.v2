<div class="w-full" x-data="{
    isModalOpen: @entangle('isModalOpen'),
    selectedTab: @entangle('selectedTab').live,
    selectedDates: [],
    dateInputValue: '',
    dateError: '',
    
    openModal() {
        this.isModalOpen = true;
        this.selectedDates = [];
        this.dateInputValue = '';
        this.dateError = '';
        $wire.call('openModal');
    },
    
    formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', { 
            weekday: 'short', 
            year: 'numeric', 
            month: 'short', 
            day: 'numeric' 
        });
    },
    
    addDate() {
        if (!this.dateInputValue) {
            this.dateError = 'Please select a date first.';
            return;
        }
        
        const selectedDate = new Date(this.dateInputValue);
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        
        if (selectedDate < today) {
            this.dateError = 'Cannot select past dates. Please choose today or a future date.';
            return;
        }
        
        if (this.selectedDates.includes(this.dateInputValue)) {
            this.dateError = 'This date has already been added.';
            return;
        }
        
        this.selectedDates.push(this.dateInputValue);
        this.selectedDates.sort();
        this.dateError = '';
        this.dateInputValue = '';
        
        // Update Livewire
        $wire.set('selectedDates', this.selectedDates);
    },
    
    removeDate(index) {
        this.selectedDates.splice(index, 1);
        this.dateError = '';
        $wire.set('selectedDates', this.selectedDates);
    },
    
    validateBeforeSubmit() {
        if (this.selectedDates.length === 0) {
            this.dateError = 'Please add at least one WFH date.';
            return false;
        }
        return true;
    }
}">
    <div class="w-full flex justify-center">
        <div class="w-full bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-md">
            <h1 class="text-lg font-bold text-center text-black dark:text-white mb-6">Work From Home Requests</h1>

            <!-- Request Button -->
            <div class="flex justify-start mb-6">
                <button @click="openModal()"
                    class="inline-flex items-center px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-blue-600 dark:hover:bg-blue-700">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Add WFH Request
                </button>
            </div>

            <!-- Tabs -->
            <div class="w-full">
                <div class="flex gap-2 overflow-x-auto border-b border-slate-300 dark:border-slate-700" role="tablist">
                    <button @click="selectedTab = 'pending'; $wire.setSelectedTab('pending')"
                        :class="selectedTab === 'pending' ? 'font-bold text-violet-700 mt-2 border-b-2 border-violet-700 dark:border-blue-600 dark:text-blue-600' : 'text-slate-700 font-medium mt-2 dark:text-slate-300 dark:hover:border-b-slate-300 dark:hover:text-white hover:border-b-2 hover:border-b-slate-800 hover:text-black'"
                        class="h-min px-4 py-2 text-sm" role="tab">
                        Pending
                    </button>

                    <button @click="selectedTab = 'approved'; $wire.setSelectedTab('approved')"
                        class="group px-4 py-2 text-sm font-medium mt-2 transition duration-150 ease-in-out"
                        :class="selectedTab === 'approved' ? 'text-violet-700 border-b-2 border-violet-700 dark:text-blue-500 dark:border-blue-500' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200'"
                        role="tab">
                        Approved
                    </button>

                    <button @click="selectedTab = 'rejected'; $wire.setSelectedTab('rejected')"
                        class="group px-4 py-2 mt-2 text-sm font-medium transition duration-150 ease-in-out"
                        :class="selectedTab === 'rejected' ? 'text-violet-700 border-b-2 border-violet-700 dark:text-blue-500 dark:border-blue-500' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200'"
                        role="tab">
                        Rejected
                    </button>
                </div>

                <!-- Tab Content -->
                <div class="px-2 py-4 text-slate-700 dark:text-slate-300 text-sm">
                    @foreach (['pending', 'approved', 'rejected'] as $status)
                        <div x-show="selectedTab === '{{ $status }}'" id="tabpanel{{ ucfirst($status) }}" role="tabpanel">
                            <div class="overflow-x-auto">
                                <table class="min-w-full bg-white dark:bg-gray-800 overflow-hidden">
                                    <thead class="bg-gray-200 dark:bg-gray-700 rounded-xl">
                                        <tr class="whitespace-nowrap">
                                            <th class="px-4 py-2 text-center">Date Requested</th>
                                            <th class="px-4 py-2 text-center">WFH Date</th>
                                            <th class="px-4 py-2 text-center">Purpose</th>
                                            <th class="px-4 py-2 text-center">Attachment</th>
                                            <th class="px-4 py-2 text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($requests->where('status', $status) as $request)
                                            <tr class="border-b dark:border-gray-600 whitespace-nowrap">
                                                <td class="px-4 py-2 text-center">{{ $request->created_at->format('Y-m-d H:i') }}</td>
                                                <td class="px-4 py-2 text-center">{{ \Carbon\Carbon::parse($request->wfhDay)->format('Y-m-d (D)') }}</td>
                                                <td class="px-4 py-2 text-center text-sm max-w-xs truncate" title="{{ $request->wfh_reason }}">
                                                    {{ $request->wfh_reason }}
                                                </td>
                                                <td class="px-4 py-2 text-center">
                                                    @if($request->attachment)
                                                        <a href="{{ route('wfh.download', $request->id) }}" 
                                                           class="inline-flex items-center px-3 py-1 text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                            </svg>
                                                            Download
                                                        </a>
                                                    @else
                                                        <span class="text-gray-400 text-xs">No file</span>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-2 text-center">
                                                    @if($request->status === 'pending')
                                                        <button wire:click="cancelRequest({{ $request->id }})" 
                                                                wire:confirm="Are you sure you want to cancel this request?" 
                                                                class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                                            Cancel
                                                        </button>
                                                    @elseif($request->status === 'approved')
                                                        <span class="text-green-500">Approved on {{ $request->approved_at->format('Y-m-d H:i') }}</span>
                                                    @elseif($request->status === 'rejected')
                                                        <span class="text-red-500">Rejected on {{ $request->rejected_at->format('Y-m-d H:i') }}</span>
                                                        @if($request->rejection_reason)
                                                            <p class="text-sm italic mt-1">Reason: {{ $request->rejection_reason }}</p>
                                                        @endif
                                                    @else
                                                        <span class="text-gray-400">No actions available</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @if ($requests->where('status', $status)->isEmpty())
                                <div class="p-4 text-center text-gray-500 dark:text-gray-300">
                                    No {{ ucfirst($status) }} requests available.
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- WFH Request Modal -->
    <div x-show="isModalOpen" x-cloak
        class="fixed inset-0 z-50 overflow-y-auto bg-black bg-opacity-40 flex items-center justify-center"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        style="display: none;">

        <div @click.away="isModalOpen = false"
            class="relative bg-white dark:bg-gray-800 p-6 mx-4 sm:mx-auto w-full max-w-md rounded-2xl shadow-lg"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-4">

            <!-- Modal header -->
            <div class="flex items-center justify-between pb-4 border-b dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-200">
                    Request Work From Home
                </h3>
                <button @click="isModalOpen = false"
                    class="text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Modal body -->
            <form @submit.prevent="if(validateBeforeSubmit()) { $wire.call('requestWfh') }" class="mt-4 space-y-4">
                <!-- Multiple Date Selection -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        WFH Date(s) <span class="text-red-500">*</span>
                    </label>
                    <div class="mt-1">
                        <!-- Date input for adding dates -->
                        <div class="flex gap-2 mb-2">
                            <input type="date" 
                                   x-model="dateInputValue"
                                   :min="new Date().toISOString().split('T')[0]"
                                   @keypress.enter.prevent="addDate()"
                                   class="flex-1 p-2 border rounded text-gray-700 dark:text-gray-300 dark:bg-gray-700 dark:border-gray-600 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <button type="button"
                                    @click="addDate()"
                                    class="px-3 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500">
                                Add Date
                            </button>
                        </div>
                        
                        <!-- Selected dates display -->
                        <div class="space-y-2 mb-2 max-h-40 overflow-y-auto">
                            <template x-for="(date, index) in selectedDates" :key="index">
                                <div class="flex items-center justify-between p-3 bg-blue-50 dark:bg-gray-700 rounded-lg">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        <span class="text-gray-700 dark:text-gray-300" x-text="formatDate(date)"></span>
                                    </div>
                                    <button type="button" 
                                            @click="removeDate(index)"
                                            class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                            </template>
                            
                            <div x-show="selectedDates.length === 0" class="text-center text-gray-400 dark:text-gray-500 text-sm py-4">
                                No dates selected. Add dates using the field above.
                            </div>
                        </div>
                        
                        <!-- Hidden input to store selected dates -->
                        <input type="hidden" 
                               name="selectedDates"
                               :value="JSON.stringify(selectedDates)">
                        
                        <!-- Instructions -->
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            Select dates and click "Add Date" to add multiple WFH dates. Minimum 1 date required.
                        </p>
                        
                        <!-- Error display area -->
                        <div x-show="dateError" x-text="dateError" class="text-red-500 text-sm mt-1"></div>
                    </div>
                    @error('selectedDates')
                        <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="wfh_reason" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Purpose <span class="text-red-500">*</span>
                    </label>
                    <textarea id="wfh_reason" 
                              wire:model="wfh_reason" 
                              name="wfh_reason"
                              rows="4" 
                              required
                              class="w-full p-2 mt-1 border rounded text-gray-700 dark:text-gray-300 dark:bg-gray-700 dark:border-gray-600 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                              placeholder="Please provide a reason for your WFH request (minimum 5 characters)"></textarea>
                    @error('wfh_reason')
                        <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Attachment Field -->
                <div>
                    <label for="attachment" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Attachment (Optional)
                    </label>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Max 10MB - PDF, JPG, PNG, DOC, DOCX</p>
                    <input type="file" 
                           id="attachment" 
                           wire:model="attachment" 
                           name="attachment"
                           accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"
                           class="w-full p-2 mt-1 border rounded text-sm text-gray-700 dark:text-gray-300 dark:bg-gray-700 dark:border-gray-600 focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                                  file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold 
                                  file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 
                                  dark:file:bg-gray-600 dark:file:text-gray-200 dark:hover:file:bg-gray-500">
                    
                    @error('attachment')
                        <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                    @enderror
                    
                    <!-- Loading indicator -->
                    <div wire:loading wire:target="attachment" class="text-sm text-blue-600 dark:text-blue-400 mt-2 flex items-center">
                        <svg class="animate-spin h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Uploading file...
                    </div>
                    
                    <!-- File preview -->
                    @if ($attachment)
                        <div class="mt-2 p-2 bg-blue-50 dark:bg-gray-700 rounded flex items-center justify-between">
                            <div class="flex items-center text-sm text-gray-700 dark:text-gray-300">
                                <svg class="w-5 h-5 mr-2 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <span class="truncate max-w-xs">{{ $attachment->getClientOriginalName() }}</span>
                            </div>
                            <button type="button" 
                                    wire:click="$set('attachment', null)"
                                    class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    @endif
                </div>

                <div class="flex justify-end space-x-2 pt-4">
                    <button type="button" 
                            @click="isModalOpen = false" 
                            class="px-4 py-2 bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                        Cancel
                    </button>
                    <button type="submit" 
                            wire:loading.attr="disabled"
                            class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed dark:bg-blue-600 dark:hover:bg-blue-700">
                        <span wire:loading.remove wire:target="requestWfh">Submit Request</span>
                        <span wire:loading wire:target="requestWfh" class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Submitting...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>