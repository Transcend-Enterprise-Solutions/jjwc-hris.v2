<div class="w-full">
    <div class="flex justify-center w-full">
        <div class=" w-full bg-white dark:bg-gray-800 rounded-2xl px-6 shadow ">
            <div class="pt-4 pb-4">
                <h1 class="text-lg font-bold text-center text-black dark:text-white">My Documents</h1>
            </div>
            <!-- File Upload Area -->
            <div
                x-data="{
                    dragOver: false,
                    file: null,
                    handleDrop(event) {
                        event.preventDefault();
                        this.dragOver = false;
                        this.file = event.dataTransfer.files[0];
                        if (this.file.type !== 'application/pdf') {
                            alert('Only PDF files are allowed.');
                            this.file = null;
                            $wire.fileSelected = false;
                            return;
                        }
                        this.handleFile(this.file);
                    },
                    handleFile(file) {
                        const reader = new FileReader();
                        reader.onload = (e) => {
                            $wire.call('handleDroppedFile', e.target.result);
                        };
                        reader.readAsDataURL(file);
                    },
                    clearFile() {
                        this.file = null;
                        $wire.fileSelected = false;
                        $wire.call('clearDroppedFile');
                    },
                    selectFile(event) {
                        this.file = event.target.files[0];
                        if (this.file) {
                            if (this.file.type !== 'application/pdf') {
                                alert('Only PDF files are allowed.');
                                this.file = null;
                                $wire.fileSelected = false;
                                return;
                            }
                            $wire.fileSelected = true;
                            this.handleFile(this.file);
                        }
                    }
                }"
                x-on:dragover.prevent="dragOver = true"
                x-on:dragleave.prevent="dragOver = false"
                x-on:drop.prevent="handleDrop($event)"
                class="mt-4 w-full flex flex-col items-center gap-2"
            >
                <span class="text-sm text-slate-700 dark:text-slate-300">Upload Document</span>
                <div
                    class="w-full flex flex-col items-center justify-center gap-2 rounded-2xl border border-dashed border-slate-300 p-8 text-slate-700 dark:border-slate-700 dark:text-slate-300"
                    :class="{ 'bg-slate-100 dark:bg-slate-700': dragOver }"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true" fill="currentColor" class="w-12 h-12 opacity-75">
                        <path fill-rule="evenodd" d="M10.5 3.75a6 6 0 0 0-5.98 6.496A5.25 5.25 0 0 0 6.75 20.25H18a4.5 4.5 0 0 0 2.206-8.423 3.75 3.75 0 0 0-4.133-4.303A6.001 6.001 0 0 0 10.5 3.75Zm2.03 5.47a.75.75 0 0 0-1.06 0l-3 3a.75.75 0 1 0 1.06 1.06l1.72-1.72v4.94a.75.75 0 0 0 1.5 0v-4.94l1.72 1.72a.75.75 0 1 0 1.06-1.06l-3-3Z" clip-rule="evenodd"/>
                    </svg>
                    <div class="group">
                        <label for="fileInput" class="cursor-pointer font-medium text-violet-700 group-focus-within:underline dark:text-blue-600">
                            <input id="fileInput" type="file" wire:model="file" accept=".pdf" class="sr-only" aria-describedby="validFileFormats" x-on:change="selectFile($event)" />
                            Browse
                        </label>
                        or drag and drop here
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true" class="size-7 fill-indigo-700 motion-safe:animate-spin dark:fill-indigo-600"wire:loading wire:target="file">
                        <path d="M12,1A11,11,0,1,0,23,12,11,11,0,0,0,12,1Zm0,19a8,8,0,1,1,8-8A8,8,0,0,1,12,20Z" opacity=".25" />
                        <path d="M10.14,1.16a11,11,0,0,0-9,8.92A1.59,1.59,0,0,0,2.46,12,1.52,1.52,0,0,0,4.11,10.7a8,8,0,0,1,6.66-6.61A1.42,1.42,0,0,0,12,2.69h0A1.57,1.57,0,0,0,10.14,1.16Z" />
                    </svg>
                    <small id="validFileFormats">PDF files only - Max 5MB</small>
                </div>

                <!-- Remove File Button -->
                <div x-show="$wire.fileSelected" class="mt-2 flex items-center">
                    <p class="mr-2 text-sm text-slate-700 dark:text-slate-300" wire:loading.remove wire:target="file"> <span x-text="file?.name"></span></p>
                    <button @click="clearFile" class="text-red-500 hover:text-red-700" title="Remove File" wire:loading.remove wire:target="file">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>

            @if ($error)
                <div class="mt-4 text-red-500">{{ $error }}</div>
            @endif

            <!-- Document Type Selection -->
            <select wire:model="documentType" class="mt-4 w-full p-2 border rounded text-gray-700 dark:text-gray-300 dark:bg-gray-700">
                <option value="" disabled selected>Select document type</option>
                @foreach ($availableDocumentTypes as $key => $label)
                    <option value="{{ $key }}">{{ $label }}</option>
                @endforeach
            </select>

            <!-- Upload Button -->
            <button wire:click="uploadDocument" class="mt-4 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 w-full" wire:loading.attr="disabled">
                <div wire:loading wire:target = "uploadDocument">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true" class="size-4 fill-slate-700 motion-safe:animate-spin dark:fill-slate-300">
                        <path d="M12,1A11,11,0,1,0,23,12,11,11,0,0,0,12,1Zm0,19a8,8,0,1,1,8-8A8,8,0,0,1,12,20Z" opacity=".25" />
                        <path d="M10.14,1.16a11,11,0,0,0-9,8.92A1.59,1.59,0,0,0,2.46,12,1.52,1.52,0,0,0,4.11,10.7a8,8,0,0,1,6.66-6.61A1.42,1.42,0,0,0,12,2.69h0A1.57,1.57,0,0,0,10.14,1.16Z" />
                    </svg>
                </div>
                <span wire:loading.remove wire:target="uploadDocument">Upload Document</span>
                <span wire:loading wire:target="uploadDocument">Uploading Document...</span>

            </button>

            <!-- Existing Documents -->
            @if ($documents->count() > 0)
                <div class="mt-8 pb-8">
                    <h2 class="text-lg font-semibold mb-4">Uploaded Documents</h2>
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white dark:bg-gray-800">
                            <thead>
                                <tr>
                                    <th class="py-2 px-4 border-b whitespace-nowrap">Document Type</th>
                                    <th class="py-2 px-4 border-b whitespace-nowrap">Version</th>
                                    <th class="py-2 px-4 border-b whitespace-nowrap">Uploaded At</th>
                                    <th class="py-2 px-4 border-b whitespace-nowrap">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($documents as $document)
                                    <tr>
                                        <td class="py-2 px-4 border-b text-center align-middle whitespace-nowrap">{{ $availableDocumentTypes[$document->document_type] ?? $document->document_type }}</td>
                                        <td class="py-2 px-4 border-b text-center align-middle whitespace-nowrap">{{ $document->version }}</td>
                                        <td class="py-2 px-4 border-b text-center align-middle whitespace-nowrap">{{ $document->created_at->setTimezone('Asia/Manila')->format('M d, Y H:i') }}</td>
                                        <td class="py-2 px-4 border-b flex justify-center space-x-2 whitespace-nowrap">
                                            <button wire:click="downloadDocument({{ $document->id }})" class="text-blue-500 hover:text-blue-700" title="Download">
                                                <i class="fas fa-download"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
            <div class="mt-4">
                <h2 class="text-lg font-semibold text-black dark:text-white">Lacking Requirements:</h2>
                <ul class="flex flex-wrap gap-2 mt-2 text-sm text-red-600 dark:text-red-400 pb-5 list-none pl-0">
                    @foreach ($availableDocumentTypes as $key => $label)
                        @if (!$documents->contains('document_type', $key))
                            <li class="inline-block bg-red-200  text-red-600  rounded px-2 py-1">
                                {{ $label }}
                            </li>
                        @endif
                    @endforeach
                </ul>
            </div>
            <!-- Confirmation Modal -->
            <div x-data="{ open: @entangle('confirmDeleteModal') }" x-show="open" class="relative z-50 w-auto h-auto" @keydown.escape.window="open = false">
                <template x-teleport="body">
                    <div x-show="open" class="fixed top-0 left-0 z-[99] flex items-center justify-center w-full h-full">
                        <div class="fixed inset-0 bg-gray-600 bg-opacity-75"></div>
                        <div class="bg-white rounded-lg shadow-lg w-1/3 mx-auto p-4 relative z-10">
                            <h2 class="text-lg font-semibold mb-4">Confirm Deletion</h2>
                            <p>Are you sure you want to delete this document?</p>
                            <div class="mt-4 flex justify-end">
                                <button @click="open = false" class="mr-2 px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Cancel</button>
                                <button wire:click="deleteDocument" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">Delete</button>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>

<script>
    Livewire.on('documentUploaded', () => {
        // Reset file input
        document.getElementById('fileInput').value = '';

        // Reset Alpine.js file data
        if (typeof Alpine !== 'undefined') {
            Alpine.raw(document.querySelector('[x-data]')).__x.$data.file = null;
        }
    });
</script>
