<div class="w-full overflow-hidden relative">
    <!-- Header -->
    <div class="flex justify-between flex-wrap bg-gray-100 dark:bg-gray-700 px-6 py-4">
        <h2 class="text-xl font-bold dark:text-white">E-Signature</h2>
    </div>

    <div class="p-6">
        @if($eSignature && $eSignature->file_path)
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg p-4 shadow-sm">
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-4 italic">
                    Upload your digital signature for document authorization
                </p>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Signature Preview -->
                    <div class="form-group">
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Current E-Signature
                        </label>
                        <div class="px-3 py-2 bg-gray-50 border border-gray-200 dark:border-gray-600 rounded-md">
                            <img src="{{ route('signature.file', basename($eSignature->file_path)) }}"
                                alt="E-Signature" 
                                class="rounded-md w-40 h-40 object-contain mx-auto" />
                        </div>
                    </div>

                    <!-- Upload New Signature -->
                    <div class="form-group">
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Update Signature
                        </label>
                        <div class="space-y-3">
                            <form wire:submit.prevent="uploadSignature">
                                <input type="file" id="e_signature" wire:model="e_signature"
                                    accept="image/*" class="hidden" required>

                                @if ($temporaryUrl)
                                    <div class="px-3 py-2 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-md">
                                        <span class="text-sm text-blue-600 dark:text-blue-400 font-semibold">
                                            Click "Upload" to save the signature
                                        </span>
                                    </div>
                                @endif

                                <div class="flex gap-2 mt-2">
                                    <label for="e_signature"
                                        class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-xs font-medium rounded-lg shadow-sm transition-colors duration-200 focus:outline-none cursor-pointer">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                                        </svg>
                                        <span wire:loading wire:target="e_signature">Loading...</span>
                                        <span wire:loading.remove wire:target="e_signature">Select Image</span>
                                    </label>

                                    <button type="submit"
                                        class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-lg shadow-sm transition-colors duration-200 focus:outline-none">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                        </svg>
                                        Upload
                                    </button>
                                </div>

                                @error('e_signature')
                                    <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                @enderror
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg p-4 shadow-sm">
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-4 italic">
                    Upload your digital signature for document authorization
                </p>

                <div class="text-center py-8 bg-gray-50 dark:bg-gray-800 rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-600">
                    @if ($temporaryUrl)
                        <div class="mb-4">
                            <span class="text-sm text-blue-600 dark:text-blue-400 font-semibold">
                                Click "Upload" to save the signature
                            </span>
                        </div>
                    @else
                        <svg class="w-12 h-12 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                        </svg>
                        <p class="text-gray-500 dark:text-gray-400 mb-4">No signature uploaded</p>
                    @endif

                    <form wire:submit.prevent="uploadSignature" class="inline-block">
                        <input type="file" id="e_signature" wire:model="e_signature"
                            accept="image/*" class="hidden" required>

                        <div class="flex gap-2 justify-center">
                            <label for="e_signature"
                                class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-lg shadow-sm transition-colors duration-200 focus:outline-none cursor-pointer">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                                </svg>
                                <span wire:loading wire:target="e_signature">Loading...</span>
                                <span wire:loading.remove wire:target="e_signature">Select Image</span>
                            </label>

                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg shadow-sm transition-colors duration-200 focus:outline-none">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                </svg>
                                Upload
                            </button>
                        </div>

                        @error('e_signature')
                            <span class="text-red-500 text-xs mt-2 block">{{ $message }}</span>
                        @enderror
                    </form>
                </div>
            </div>
        @endif
    </div>
</div>