<div class="w-full" x-data="{
    showDeleteModal: false,
    showApproveModal: false,
    showRejectModal: false,
    showSpinnerModal: false,
    deleteRequestId: null,
    approveRequestId: null,
    rejectRequestId: null,
    selectedTab: 'pending',
    open: false,
    handleFileUpload(requestId) {
        this.showSpinnerModal = true;
        $wire.uploadDocument(requestId).then(() => {
            this.showSpinnerModal = false;
        });
    }
}" x-cloak>
    <div class="w-full">
        <div class="flex justify-center w-full">
            <div class="w-full bg-white dark:bg-gray-800 rounded-2xl px-6 shadow">
                <div class="pt-4 pb-4">
                    <h1 class="text-lg font-bold text-center text-black dark:text-white">Document Requests</h1>
                </div>
                <!-- Document Type Filter -->
                <div class="mb-4">
                    <label for="documentTypeFilter" class="block text-sm font-medium text-gray-700 dark:text-gray-300 pb-2">Filter by Document Type</label>
                    <div class="relative inline-block w-full mb-5">
                        <button @click="open = !open" class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md py-2 px-3 text-left cursor-default focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            {{ count($selectedDocumentTypes) ? implode(', ', $selectedDocumentTypes) : 'Select Document Types' }}
                        </button>
                        <div x-show="open" @click.away="open = false" class="absolute right-0 z-10 mt-1 w-full bg-white dark:bg-gray-700 shadow-lg max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm">
                            <label class="flex items-center px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600">
                                <input type="checkbox" wire:model.live="selectAll" class="form-checkbox h-5 w-5 text-indigo-600">
                                <span class="ml-2 text-gray-900 dark:text-gray-200">Select All</span>
                            </label>
                            @foreach($documentTypes as $type)
                                <label class="flex items-center px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600">
                                    <input type="checkbox" wire:model.live="selectedDocumentTypes" value="{{ $type }}" class="form-checkbox h-5 w-5 text-indigo-600">
                                    <span class="ml-2 text-gray-900 dark:text-gray-200">{{ $type }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>


                <!-- Tabs -->
                <div class="w-full" wire:poll.10s='loadRequests'>
                    <div class="flex gap-2 overflow-x-auto border-b border-slate-300 dark:border-slate-700" role="tablist">
                        <button @click="selectedTab = 'pending'" :class="selectedTab === 'pending' ? 'font-bold text-violet-700 border-b-2 border-violet-700 dark:border-blue-600 dark:text-blue-600 mt-2' : 'mt-2 text-slate-700 font-medium dark:text-slate-300 dark:hover:border-b-slate-300 dark:hover:text-white hover:border-b-2 hover:border-b-slate-800 hover:text-black'" class="h-min px-4 py-2 text-sm relative" role="tab">
                            Pending
                            @if($pendingCount > 0)
                                <span class="absolute -top-1 -right-1 flex items-center justify-center w-4 h-4 text-[10px] font-bold text-white bg-red-500 rounded-full">{{ $pendingCount }}</span>
                            @endif
                            </button>
                        <button @click="selectedTab = 'preparing'" :class="selectedTab === 'preparing' ? 'font-bold text-violet-700 border-b-2 border-violet-700 dark:border-blue-600 dark:text-blue-600 mt-2' : 'mt-2 text-slate-700 font-medium dark:text-slate-300 dark:hover:border-b-slate-300 dark:hover:text-white hover:border-b-2 hover:border-b-slate-800 hover:text-black'" class="h-min px-4 py-2 text-sm relative" role="tab">
                            Preparing
                            @if($preparingCount > 0)
                                <span class="absolute -top-1 -right-1 flex items-center justify-center w-4 h-4 text-[10px] font-bold text-white bg-red-500 rounded-full">{{ $preparingCount }}</span>
                            @endif
                            </button>

                        <button @click="selectedTab = 'completed'" :class="selectedTab === 'completed' ? 'font-bold text-violet-700 border-b-2 border-violet-700 dark:border-blue-600 dark:text-blue-600 mt-2' : 'mt-2 text-slate-700 font-medium dark:text-slate-300 dark:hover:border-b-slate-300 dark:hover:text-white hover:border-b-2 hover:border-b-slate-800 hover:text-black'" class="h-min px-4 py-2 text-sm" role="tab">Completed</button>
                        <button @click="selectedTab = 'rejected'" :class="selectedTab === 'rejected' ? 'font-bold text-violet-700 border-b-2 border-violet-700 dark:border-blue-600 dark:text-blue-600 mt-2' : 'mt-2 text-slate-700 font-medium dark:text-slate-300 dark:hover:border-b-slate-300 dark:hover:text-white hover:border-b-2 hover:border-b-slate-800 hover:text-black'" class="h-min px-4 py-2 text-sm" role="tab">Rejected</button>
                    </div>
                    <!-- Tab Content -->
                    <div class="px-2 py-4 text-slate-700 dark:text-slate-300">
                        @foreach (['pending', 'preparing', 'completed', 'rejected'] as $status)
                            <div x-show="selectedTab === '{{ $status }}'" id="tabpanel{{ ucfirst($status) }}" role="tabpanel" aria-labelledby="tab{{ ucfirst($status) }}">
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50 dark:bg-gray-700">
                                            <tr>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider whitespace-nowrap">Employee</th>
                                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider whitespace-nowrap">Document Type</th>
                                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider whitespace-nowrap">Date Requested</th>
                                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider whitespace-nowrap">Date Completed</th>
                                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider whitespace-nowrap">Document</th>
                                                @if($status === 'completed')
                                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider whitespace-nowrap">Rating</th>
                                                @endif
                                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider whitespace-nowrap">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200">
                                            @foreach ($requests->where('status', $status) as $request)
                                                <tr>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-left text-gray-900 dark:text-gray-300">
                                                        <div class="flex gap-3 items-center">
                                                            @if ($request->user->profile_photo_path)
                                                                <img src="{{ route('profile-photo.file', ['filename' => basename($request->user->profile_photo_path)]) }}"
                                                                    alt="{{ $request->user?->name ?? 'No User Assigned' }}"
                                                                    width="32" height="32"
                                                                    class="w-10 h-10 rounded-full object-cover border border-gray-500">
                                                            @else
                                                                <div class="w-10 h-10 rounded-full bg-gray-500 border border-gray-500 dark:bg-gray-600 flex items-center justify-center text-white text-xs font-medium">
                                                                    {{ strtoupper(substr(($request->user?->name ?? 'No User Assigned'), 0, 1)) }}{{ strtoupper(substr(explode(' ', ($request->user?->name ?? 'No User Assigned'))[1] ?? '', 0, 1)) }}
                                                                </div>
                                                            @endif
                                                            <span>{{ $request->user?->name ?? 'No User Assigned' }}</span>
                                                        </div>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500 dark:text-gray-200">{{ $request->document_type }}</td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500 dark:text-gray-200">{{ $request->date_requested->format('Y-m-d') }}</td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500 dark:text-gray-200">{{ $request->date_completed ? $request->date_completed->format('Y-m-d') : 'N/A' }}</td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500 dark:text-gray-200">
                                                        @if ($status === 'preparing')
                                                            <div class="flex items-center space-x-2">
                                                                <input type="file" id="file-input-{{ $request->id }}" wire:model="uploadedFile.{{ $request->id }}" class="mt-2 mb-2">
                                                                <button @click="handleFileUpload({{ $request->id }})" class="text-green-500 hover:text-green-700">
                                                                    <i x-show="!showSpinnerModal" class="fas fa-upload"></i>
                                                                    {{-- <svg x-show="showSpinnerModal" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true" class="size-5 fill-green-600 motion-safe:animate-spin dark:fill-green-600">
                                                                        <path d="M12,1A11,11,0,1,0,23,12,11,11,0,0,0,12,1Zm0,19a8,8,0,1,1,8-8A8,8,0,0,1,12,20Z" opacity=".25" />
                                                                        <path d="M10.14,1.16a11,11,0,0,0-9,8.92A1.59,1.59,0,0,0,2.46,12,1.52,1.52,0,0,0,4.11,10.7a8,8,0,0,1,6.66-6.61A1.42,1.42,0,0,0,12,2.69h0A1.57,1.57,0,0,0,10.14,1.16Z" />
                                                                    </svg> --}}
                                                                </button>
                                                            </div>
                                                        @elseif ($request->file_path)
                                                            <button wire:click="downloadDocument({{ $request->id }})" class="text-blue-500 hover:underline">
                                                                {{ Str::limit($request->filename, 15, '...') }}
                                                            </button>
                                                        @else
                                                            No Document
                                                        @endif
                                                    </td>
                                                    @if($status === 'completed')
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500 dark:text-gray-200">
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
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-200">
                                                        <div class="flex justify-center space-x-2">
                                                            @if ($status === 'pending')
                                                                <button @click="showApproveModal = true; approveRequestId = {{ $request->id }}" class="text-blue-500 hover:text-blue-700 " title="Approve Request">
                                                                    <i class="fas fa-check"></i>
                                                                </button>
                                                                <button @click="showRejectModal = true; rejectRequestId = {{ $request->id }}" class="text-red-500 hover:text-red-700" title="Reject Request">
                                                                    <i class="fas fa-times"></i>
                                                                </button>
                                                            @endif
                                                            <button @click="showDeleteModal = true; deleteRequestId = {{ $request->id }}" class="text-gray-500 hover:text-gray-700">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </div>
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
    </div>

    <!-- Delete Confirmation Modal -->
    <div x-show="showDeleteModal" class="fixed inset-0 z-50 overflow-y-auto bg-black bg-opacity-40 flex items-center justify-center">
        <div @click.away="showDeleteModal = false" x-show="showDeleteModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-4" class="relative bg-white dark:bg-gray-800 p-6 mx-auto max-w-lg rounded-2xl">
            <div class="flex items-center justify-between pb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-200">Confirm Deletion</h3>
                <button @click="showDeleteModal = false" class="text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <p class="mb-4 text-gray-800 dark:text-gray-300">Are you sure you want to delete this document request?</p>
            <div class="flex justify-end space-x-4">
                <button @click="showDeleteModal = false" class="px-4 py-2 bg-gray-300 dark:bg-gray-600 rounded">Cancel</button>
                <button @click="$wire.deleteRequest(deleteRequestId); showDeleteModal = false" class="px-4 py-2 bg-red-500 text-white rounded">Delete</button>
            </div>
        </div>
    </div>

    <!-- Approve Confirmation Modal -->
    <div x-show="showApproveModal" class="fixed inset-0 z-50 overflow-y-auto bg-black bg-opacity-40 flex items-center justify-center">
        <div @click.away="showApproveModal = false" x-show="showApproveModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-4" class="relative bg-white dark:bg-gray-800 p-6 mx-auto max-w-lg rounded-2xl">
            <div class="flex items-center justify-between pb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-200">Confirm Approval</h3>
                <button @click="showApproveModal = false" class="text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <p class="mb-4 text-gray-800 dark:text-gray-300">Are you sure you want to approve this document request?</p>
            <div class="flex justify-end space-x-4">
                <button @click="showApproveModal = false" class="px-4 py-2 bg-gray-300 dark:bg-gray-600 rounded">Cancel</button>
                <button @click="$wire.approveRequest(approveRequestId); showApproveModal = false" class="px-4 py-2 bg-green-500 text-white rounded">Approve</button>
            </div>
        </div>
    </div>

    <!-- Reject Confirmation Modal -->
    <div x-show="showRejectModal" class="fixed inset-0 z-50 overflow-y-auto bg-black bg-opacity-40 flex items-center justify-center">
        <div @click.away="showRejectModal = false" x-show="showRejectModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-4" class="relative bg-white dark:bg-gray-800 p-6 mx-auto max-w-lg rounded-2xl">
            <div class="flex items-center justify-between pb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-200">Confirm Rejection</h3>
                <button @click="showRejectModal = false" class="text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <p class="mb-4 text-gray-800 dark:text-gray-300">Are you sure you want to reject this document request?</p>
            <div class="flex justify-end space-x-4">
                <button @click="showRejectModal = false" class="px-4 py-2 bg-gray-300 dark:bg-gray-600 rounded">Cancel</button>
                <button @click="$wire.rejectRequest(rejectRequestId); showRejectModal = false" class="px-4 py-2 bg-red-500 text-white rounded">Reject</button>
            </div>
        </div>
    </div>
    <!-- Spinner Modal -->
    <div x-show="showSpinnerModal" class="fixed inset-0 z-50 overflow-y-auto bg-black bg-opacity-40 flex items-center justify-center">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-xl">
            <div class="flex flex-col items-center">
                <div class="animate-spin rounded-full h-16 w-16 border-t-2 border-b-2 border-blue-500"></div>
                <p class="mt-4 text-gray-700 dark:text-gray-300">Uploading file...</p>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('livewire:load', function () {
        Livewire.on('upload-complete', function () {
            Alpine.store('showSpinnerModal', false);
        });
    });
</script>
