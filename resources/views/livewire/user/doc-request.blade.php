<div class="w-full" x-data="{ selectedTab: 'pending' }">
    <div class="w-full flex justify-center">
        <div class="w-full bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-md">
            <h1 class="text-lg font-bold text-center text-black dark:text-white mb-6">Request a Document</h1>

            <!-- Document Type Selection -->
            <select wire:model.live="documentType" class="w-full p-2 border rounded text-gray-700 dark:text-gray-300 dark:bg-gray-700 mb-4">
                <option value="" selected>Select document type</option>
                @foreach ($availableDocumentTypes as $key => $label)
                    <option value="{{ $key }}">{{ $label }}</option>
                @endforeach
            </select>

            <!-- Others Specification Input (conditionally shown) -->
            @if($documentType === 'others')
                <div class="mb-4">
                    <label for="otherDocumentType" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Please specify the document type:
                    </label>
                    <input 
                        type="text" 
                        id="otherDocumentType"
                        wire:model="otherDocumentType" 
                        placeholder="Enter document type..."
                        class="w-full p-2 border rounded text-gray-700 dark:text-gray-300 dark:bg-gray-700 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        maxlength="255"
                    >
                    @error('otherDocumentType')
                        <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                    @enderror
                </div>
            @endif

            <!-- Submit Button -->
            <button wire:click="requestDocument" class="w-full px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                Submit Request
            </button>

            <!-- Tabs -->
            <div class="w-full mt-6">
                <div class="flex gap-2 overflow-x-auto border-b border-slate-300 dark:border-slate-700" role="tablist" wire:poll.10s='updateNotificationCounts'>
                    <button @click="selectedTab = 'pending'" :class="selectedTab === 'pending' ? 'font-bold text-violet-700 mt-2 border-b-2 border-violet-700 dark:border-blue-600 dark:text-blue-600' : 'text-slate-700 font-medium mt-2 dark:text-slate-300 dark:hover:border-b-slate-300 dark:hover:text-white hover:border-b-2 hover:border-b-slate-800 hover:text-black'" class="h-min px-4 py-2 text-sm" role="tab">Pending</button>
                    <div class="flex space-x-1 border-b border-gray-200 dark:border-gray-700">
                        <button @click="selectedTab = 'preparing'; $wire.markNotificationsAsRead('approved')"
                            class="group relative px-4 py-2 text-sm font-medium mt-2 transition duration-150 ease-in-out"
                            :class="selectedTab === 'preparing' ? 'text-violet-700 border-b-2 border-violet-700 dark:text-blue-500 dark:border-blue-500' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200'"
                            role="tab">
                            Preparing
                            @if($preparingCount > 0)
                                <span class="absolute -top-1 -right-1 flex items-center justify-center w-4 h-4 text-[10px] font-bold text-white bg-red-500 rounded-full">{{ $preparingCount }}</span>
                            @endif
                        </button>

                        <button @click="selectedTab = 'completed'; $wire.markNotificationsAsRead('completed')"
                            class="group mt-2 relative px-4 py-2 text-sm font-medium transition duration-150 ease-in-out"
                            :class="selectedTab === 'completed' ? 'text-violet-700 border-b-2 border-violet-700 dark:text-blue-500 dark:border-blue-500' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200'"
                            role="tab">
                            Completed
                            @if($completedCount > 0)
                                <span class="absolute -top-1 -right-1 flex items-center justify-center w-4 h-4 text-[10px] font-bold text-white bg-red-500 rounded-full">{{ $completedCount }}</span>
                            @endif
                        </button>

                        <button @click="selectedTab = 'rejected'; $wire.markNotificationsAsRead('rejected')"
                            class="group relative px-4 py-2 mt-2 text-sm font-medium transition duration-150 ease-in-out"
                            :class="selectedTab === 'rejected' ? 'text-violet-700 border-b-2 border-violet-700 dark:text-blue-500 dark:border-blue-500' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200'"
                            role="tab">
                            Rejected
                            @if($rejectedCount > 0)
                                <span class="absolute -top-1 -right-1 flex items-center justify-center w-4 h-4 text-[10px] font-bold text-white bg-red-500 rounded-full">{{ $rejectedCount }}</span>
                            @endif
                        </button>

                    </div>

                </div>

                <!-- Tab Content -->
                <div class="px-2 py-4 text-slate-700 dark:text-slate-300 text-sm">
                    @foreach (['pending', 'preparing', 'completed', 'rejected'] as $status)
                        <div x-show="selectedTab === '{{ $status }}'" id="tabpanel{{ ucfirst($status) }}" role="tabpanel" aria-labelledby="tab{{ ucfirst($status) }}">
                            <div class="overflow-x-auto">
                                <table class="min-w-full bg-white dark:bg-gray-800 overflow-hidden">
                                    <thead class="bg-gray-200 dark:bg-gray-700 rounded-xl">
                                        <tr class="whitespace-nowrap">
                                            <th class="px-4 py-2 text-center">Document Type</th>
                                            <th class="px-4 py-2 text-center">Date Requested</th>
                                            <th class="px-4 py-2 text-center">Date Completed</th>
                                            <th class="px-4 py-2 text-center">My Document</th>
                                            @if($status === 'completed')
                                                <th class="px-4 py-2 text-center">My Rating</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($requests->where('status', $status) as $request)
                                            <tr class="border-b dark:border-gray-600 whitespace-nowrap">
                                                <td class="px-4 py-2 text-center">{{ $request->document_type }}</td>
                                                <td class="px-4 py-2 text-center">{{ $request->date_requested->format('Y-m-d') }}</td>
                                                <td class="px-4 py-2 text-center">{{ $request->date_completed ? $request->date_completed->format('Y-m-d') : 'N/A' }}</td>
                                                <td class="px-4 py-2 text-center">
                                                    @if ($request->file_path)
                                                        <button wire:click="downloadDocument({{ $request->id }})" class="text-blue-500 hover:underline">
                                                            {{ Str::limit($request->filename, 15, '...') }}
                                                        </button>
                                                    @else
                                                        No Document
                                                    @endif
                                                </td>
                                                @if($status === 'completed')
                                                    <td class="px-4 py-2 text-center">
                                                        @if($request->rating)
                                                        <div class="flex justify-center items-center">
                                                            @for ($i = 1; $i <= 5; $i++)
                                                                <div class="relative w-5 h-5 flex-shrink-0">
                                                                    <!-- Empty Star -->
                                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 text-gray-300">
                                                                        <path d="M12 2a1.5 1.5 0 01.71.19l2.45 1.14 1.63 2.78a1.5 1.5 0 00.78.59l3.07.45a1.5 1.5 0 01.83 2.56l-2.22 2.17.52 3.05a1.5 1.5 0 01-2.17 1.57l-2.73-1.43-2.73 1.43a1.5 1.5 0 01-2.17-1.57l.52-3.05-2.22-2.17a1.5 1.5 0 01.83-2.56l3.07-.45a1.5 1.5 0 00.78-.59L11.84 3.33 14.29 2.19A1.5 1.5 0 0112 2z"/>
                                                                    </svg>
                                                                    <!-- Filled Star -->
                                                                    @php
                                                                        $ratingValue = $request->rating->overall;
                                                                        $starPercentage = ($ratingValue - $i + 1) * 100;
                                                                        $starPercentageRounded = round(max(0, min(100, $starPercentage)));
                                                                    @endphp
                                                                    <div class="absolute top-0 left-0 h-full overflow-hidden" style="width: {{ $starPercentageRounded }}%">
                                                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 text-yellow-500">
                                                                            <path d="M12 2a1.5 1.5 0 01.71.19l2.45 1.14 1.63 2.78a1.5 1.5 0 00.78.59l3.07.45a1.5 1.5 0 01.83 2.56l-2.22 2.17.52 3.05a1.5 1.5 0 01-2.17 1.57l-2.73-1.43-2.73 1.43a1.5 1.5 0 01-2.17-1.57l.52-3.05-2.22-2.17a1.5 1.5 0 01.83-2.56l3.07-.45a1.5 1.5 0 00.78-.59L11.84 3.33 14.29 2.19A1.5 1.5 0 0112 2z"/>
                                                                        </svg>
                                                                    </div>
                                                                </div>
                                                            @endfor
                                                            <span class="ml-2">{{ number_format($request->rating->overall, 1) }}</span>
                                                        </div>

                                                        @else
                                                            Not yet rated
                                                        @endif
                                                    </td>
                                                @endif
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

    <!-- Rating Modal -->
    <div x-data="{ showModal: @entangle('showRatingModal') }"
    x-show="showModal"
    x-transition:enter="ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-50 overflow-y-auto bg-gray-500 bg-opacity-75 rounded-lg"
    aria-labelledby="modal-title"
    role="dialog" aria-modal="true"
    x-cloak
    @click.away="showModal = false" style="z-index: 9999;">

        <div class="flex items-center justify-center min-h-screen p-4">
            <div @click.away="showModal = false"
                x-show="showModal"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="inline-block bg-white rounded-lg shadow-xl transform transition-all sm:max-w-lg sm:w-full sm:mx-4"
                style="max-height: 90vh; overflow-y: auto;">

                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4" id="modal-title">
                        To download your documents please rate your experience
                    </h3>
                    <div class="space-y-6">
                        @foreach(['responsiveness', 'reliability', 'access_facilities', 'communication', 'cost', 'integrity', 'assurance', 'outcome'] as $criterion)
                        <div class="flex items-center space-x-4 mb-6">
                            <div class="flex-1">
                                <label for="{{ $criterion }}" class="block text-sm font-medium text-gray-700">
                                    {{ ucfirst(str_replace('_', ' & ', $criterion)) }}
                                </label>
                                <p class="text-xs text-gray-500 mt-1">{{ $descriptions[$criterion] }}</p>
                            </div>
                            <div x-data="{ currentVal: @entangle('ratings.' . $criterion) }" class="flex items-center gap-1">
                                @for($i = 1; $i <= 5; $i++)
                                <label class="cursor-pointer transition-transform duration-150 hover:scale-110">
                                    <input type="radio" class="sr-only" name="{{ $criterion }}" value="{{ $i }}" wire:model.live="ratings.{{ $criterion }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6" :class="currentVal >= {{ $i }} ? 'text-yellow-500' : 'text-gray-300'">
                                        <path d="M12 2a1.5 1.5 0 01.71.19l2.45 1.14 1.63 2.78a1.5 1.5 0 00.78.59l3.07.45a1.5 1.5 0 01.83 2.56l-2.22 2.17.52 3.05a1.5 1.5 0 01-2.17 1.57l-2.73-1.43-2.73 1.43a1.5 1.5 0 01-2.17-1.57l.52-3.05-2.22-2.17a1.5 1.5 0 01.83-2.56l3.07-.45a1.5 1.5 0 00.78-.59L11.84 3.33 14.29 2.19A1.5 1.5 0 0112 2z"/>
                                    </svg>
                                </label>
                                @endfor
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                <div x-show="$wire.errors.has('ratings')" class="mb-4 text-red-600">
                    <p class="text-center">{{ $errors->first('ratings.*') }}</p>
                </div>

                <div class="bg-gray-50 px-4 py-3 text-right sm:px-6 border-t border-gray-200">

                    <button @click="showModal = false" type="button" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md shadow-sm hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Cancel
                    </button>
                    <button @click="$wire.submitRating()" type="button" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-blue-500 border border-transparent rounded-md shadow-sm hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 ml-3">
                        Submit
                    </button>

                </div>
            </div>
        </div>
    </div>

</div>