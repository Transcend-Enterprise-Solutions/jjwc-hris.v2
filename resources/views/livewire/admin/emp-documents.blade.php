<div class="w-full" x-data="{
    showDeleteModal: false,
    documentToDeleteId: null,
    notificationMessage: '',
    notificationType: '',
    showNotification: false,
}" x-init="
    $wire.on('show-delete-modal', () => {
        showDeleteModal = true;
    });
    $wire.on('notify', event => {
        notificationMessage = event.message;
        notificationType = event.type;
        showNotification = true;
        setTimeout(() => showNotification = false, 3000);
    });
">
    <div class="w-full">
        <div class="flex justify-center w-full">
            <div class="w-full bg-white dark:bg-gray-800 rounded-2xl px-6 shadow">
                <div class="pt-4 pb-4">
                    <h1 class="text-lg font-bold text-center text-black dark:text-white">Employee Documents</h1>
                </div>
                <div class="w-full">
                    <div @keydown.right.prevent="$focus.wrap().next()" @keydown.left.prevent="$focus.wrap().previous()" class="flex gap-2 overflow-x-auto border-b border-slate-300 dark:border-slate-700" role="tablist" aria-label="tab options">
                        @foreach ($tabs as $key => $label)
                            <button wire:click="$set('selectedTab', '{{ $key }}')" :aria-selected="$wire.selectedTab === '{{ $key }}'" :tabindex="$wire.selectedTab === '{{ $key }}' ? '0' : '-1'" :class="$wire.selectedTab === '{{ $key }}' ? 'font-bold text-violet-700 border-b-2 whitespace-nowrap border-violet-700 dark:border-blue-600 dark:text-blue-600' : 'text-slate-700 font-medium whitespace-nowrap dark:text-slate-300 dark:hover:border-b-slate-300 dark:hover:text-white hover:border-b-2 hover:border-b-slate-800 hover:text-black'" class="h-min px-4 py-2 text-sm whitespace-nowrap" type="button" role="tab" aria-controls="tabpanel{{ ucfirst($key) }}">{{ $label }}</button>
                        @endforeach
                    </div>
                    <div class="px-2 py-4 text-slate-700 dark:text-slate-300">

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">Employee</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">Upload Date</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">Version</th>
                                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200">
                                    @php
                                        $documentGroups = $documents->groupBy(function($document) {
                                            return $document->user_id . '-' . $document->type;
                                        });
                                    @endphp
                                    @foreach ($documentGroups as $group)
                                        @foreach ($group->sortBy('created_at') as $index => $document)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-left text-sm font-medium text-gray-900 dark:text-gray-300">
                                                    <div class="flex gap-3 items-center">
                                                        @if ($document->user->profile_photo_path)
                                                            <img src="{{ route('profile-photo.file', ['filename' => basename($document->user->profile_photo_path)]) }}"
                                                                alt="{{ $document->user?->name ?? 'No User Assigned' }}"
                                                                width="32" height="32"
                                                                class="w-10 h-10 rounded-full object-cover border border-gray-500">
                                                        @else
                                                            <div class="w-10 h-10 rounded-full bg-gray-500 border border-gray-500 dark:bg-gray-600 flex items-center justify-center text-white text-xs font-medium">
                                                                {{ strtoupper(substr(($document->user?->name ?? 'No User Assigned'), 0, 1)) }}{{ strtoupper(substr(explode(' ', ($document->user?->name ?? 'No User Assigned'))[1] ?? '', 0, 1)) }}
                                                            </div>
                                                        @endif
                                                        <span>{{ $document->user?->name ?? 'No User Assigned' }}</span>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-left text-sm text-gray-500 dark:text-gray-200">{{ $document->created_at->format('Y-m-d H:i:s') }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-left text-sm text-gray-500 dark:text-gray-200">
                                                    v{{ $index + 1 }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500 dark:text-gray-200">
                                                    <a wire:click="downloadDocument({{ $document->id }})" download class="text-blue-500 hover:text-blue-700" title="Download">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                    <button wire:click="confirmDelete({{ $document->id }})" class="text-red-500 hover:text-red-700" title="Delete" :disabled="$wire.isDeleting">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @if ($documents->isEmpty())
                            <div class="p-4 text-center text-gray-500 dark:text-gray-300">
                                No documents available.
                            </div>
                        @else
                            <div class="mt-4">
                                {{ $documents->links() }}
                            </div>
                        @endif

                        <!-- Employees without uploads section -->
                        <div class="mt-8">
                            <h3 class="text-xs font-semibold mb-4">Employees without {{ $tabs[$selectedTab] }} :</h3>
                            <ul class="flex flex-wrap gap-2 text-xs list-none pl-0">
                                @foreach ($employeesWithoutUpload[$selectedTab] ?? [] as $employee)
                                    <li class="inline-block bg-gray-200 dark:bg-gray-700 rounded px-2 py-1">{{ $employee->name }}</li>
                                @endforeach
                            </ul>
                            @if (empty($employeesWithoutUpload[$selectedTab]))
                                <p class="text-xs text-gray-500 dark:text-gray-300">All employees have uploaded this document.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- delete Confirmation Modal -->
    <div x-show="showDeleteModal" x-cloak
        class="fixed inset-0 z-50 overflow-y-auto bg-black bg-opacity-40 flex items-center justify-center">
        <div x-show="showDeleteModal" x-cloak
        class="fixed inset-0 z-50 overflow-y-auto bg-black bg-opacity-40 flex items-center justify-center">
        <div @click.away="showDeleteModal = false"
            x-show="showDeleteModal"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-4"
            class="bg-white dark:bg-gray-800 p-6 mx-auto max-w-lg rounded-2xl">
            <div class="flex items-center justify-between pb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-200">Confirm Deletion</h3>
                <button @click="showDeleteModal = false" class="text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <p class="mb-4 text-gray-800 dark:text-gray-300">Are you sure you want to delete this document?</p>
            <div class="flex justify-end space-x-4">
                <button @click="showDeleteModal = false" class="px-4 py-2 bg-gray-300 dark:bg-gray-600 rounded">Cancel</button>
                <button wire:click="deleteRequest" @click="showDeleteModal = false" class="px-4 py-2 bg-red-500 text-white rounded">Delete</button>
            </div>
        </div>
    </div>
</div>

<!-- Notification -->
<div x-show="showNotification" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-90" x-transition:enter-end="opacity-100 transform scale-100" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 transform scale-100" x-transition:leave-end="opacity-0 transform scale-90" class="fixed bottom-4 right-4 max-w-sm bg-white border border-gray-200 dark:bg-gray-800 dark:border-gray-700 rounded-lg shadow-md z-50 p-4">
    <div :class="{'bg-red-100 text-red-700': notificationType === 'error', 'bg-green-100 text-green-700': notificationType === 'success'}" class="flex items-start space-x-2">
        <svg x-show="notificationType === 'success'" class="w-6 h-6 text-green-500 dark:text-green-300" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" d="M16.707 6.707a1 1 0 01-1.414 0L9 1.414 4.707 5.707a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0l8 8a1 1 0 010 1.414z" clip-rule="evenodd"></path>
            <path fill-rule="evenodd" d="M9 3a1 1 0 011-1h8a1 1 0 110 2h-8a1 1 0 01-1-1zM4 12a1 1 0 00-2 0v8a1 1 0 102 0v-8z" clip-rule="evenodd"></path>
        </svg>
        <svg x-show="notificationType === 'error'" class="w-6 h-6 text-red-500 dark:text-red-300" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" d="M8.257 3.099c.362-.728 1.363-.728 1.724 0l5.5 11a1 1 0 01-.895 1.451h-11a1 1 0 01-.895-1.451l5.5-11zM11 14a1 1 0 100 2 1 1 0 000-2zm0 4a1 1 0 100 2 1 1 0 000-2z" clip-rule="evenodd"></path>
        </svg>
        <div>
            <p class="font-bold" x-text="notificationMessage"></p>
        </div>
    </div>
</div>
