<div class="w-full" x-data="{ selectedTab: @entangle('selectedTab').live }">
    <div class="w-full flex justify-center">
        <div class="w-full bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-md">
            <h1 class="text-lg font-bold text-center text-black dark:text-white mb-6">WFH Request Approval</h1>

            @if (session()->has('message'))
                <div class="mb-4 px-4 py-2 bg-green-100 text-green-800 rounded">
                    {{ session('message') }}
                </div>
            @endif

            @if (session()->has('error'))
                <div class="mb-4 px-4 py-2 bg-red-100 text-red-800 rounded">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Tabs -->
            <div class="w-full mt-6">
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
                        <div x-show="selectedTab === '{{ $status }}'" id="tabpanel{{ ucfirst($status) }}" role="tabpanel" aria-labelledby="tab{{ ucfirst($status) }}">
                            <div class="overflow-x-auto">
                                <table class="min-w-full bg-white dark:bg-gray-800 overflow-hidden">
                                    <thead class="bg-gray-200 dark:bg-gray-700 rounded-xl">
                                        <tr class="whitespace-nowrap">
                                            <th class="px-4 py-2 text-left">Employee</th>
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
                                                <td class="px-4 py-2 text-left">
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
                                                        <div class="flex justify-center space-x-2">
                                                            <button wire:click="approveRequest({{ $request->id }})" 
                                                                    wire:confirm="Are you sure you want to approve this request?" 
                                                                    class="px-3 py-1 bg-green-500 text-white rounded hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500">
                                                                Approve
                                                            </button>
                                                            <button @click="$refs.rejectModal{{ $request->id }}.showModal()" 
                                                                    class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-500">
                                                                Reject
                                                            </button>
                                                        </div>

                                                        <!-- Reject Modal -->
                                                        <dialog id="rejectModal{{ $request->id }}" 
                                                                x-ref="rejectModal{{ $request->id }}" 
                                                                class="p-6 rounded-lg shadow-xl bg-white dark:bg-gray-800 w-full max-w-md backdrop:bg-black backdrop:bg-opacity-50">
                                                            <div class="flex flex-col">
                                                                <div class="flex items-center justify-between mb-4">
                                                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Reject WFH Request</h3>
                                                                    <button @click="$refs.rejectModal{{ $request->id }}.close()" 
                                                                            class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                                                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                                        </svg>
                                                                    </button>
                                                                </div>
                                                                
                                                                <div class="mb-4">
                                                                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                                                                        <strong>Employee:</strong> {{ $request->user?->name ?? 'N/A' }}
                                                                    </p>
                                                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                                                        <strong>WFH Date:</strong> {{ \Carbon\Carbon::parse($request->wfhDay)->format('Y-m-d (D)') }}
                                                                    </p>
                                                                </div>

                                                                <label class="mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                                                    Reason for rejection (optional)
                                                                </label>
                                                                <textarea wire:model="reason" 
                                                                          class="p-2 border rounded mb-4 text-gray-700 dark:text-gray-300 dark:bg-gray-700 dark:border-gray-600 focus:ring-2 focus:ring-red-500 focus:border-red-500" 
                                                                          rows="3"
                                                                          placeholder="Enter reason for rejection..."></textarea>
                                                                
                                                                <div class="flex justify-end space-x-2">
                                                                    <button @click="$refs.rejectModal{{ $request->id }}.close()" 
                                                                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                                                                        Cancel
                                                                    </button>
                                                                    <button wire:click="rejectRequest({{ $request->id }})" 
                                                                            @click="$refs.rejectModal{{ $request->id }}.close()" 
                                                                            class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-500">
                                                                        Confirm Rejection
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </dialog>
                                                    @elseif($request->status === 'approved')
                                                        <span class="text-green-500">Approved on {{ $request->approved_at->format('Y-m-d H:i') }}</span>
                                                    @elseif($request->status === 'rejected')
                                                        <span class="text-red-500">Rejected on {{ $request->rejected_at->format('Y-m-d H:i') }}</span>
                                                        @if($request->rejection_reason)
                                                            <p class="text-sm italic mt-1">Reason: {{ $request->rejection_reason }}</p>
                                                        @endif
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
</div>