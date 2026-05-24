<div class="w-full min-h-screen bg-gray-50 dark:bg-gray-900 transition-colors duration-200">
    <div class="max-w-6xl mx-auto p-6 space-y-8">
        <!-- Header -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="text-center">
                <div class="flex items-center justify-center mb-4">
                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                </div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Download Forms</h1>
                <p class="text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
                    Access and download the forms you need. Click on any form name to download the file instantly.
                </p>
            </div>
        </div>

        <!-- Search Bar -->
        @if(count($forms) > 5)
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <div class="relative max-w-md mx-auto">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <input
                    type="text"
                    wire:model.live="search"
                    placeholder="Search forms..."
                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-200"
                >
            </div>
        </div>
        @endif

        <!-- Forms Grid -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            @if(count($filteredForms) > 0)
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                        <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Available Forms ({{ count($filteredForms) }})
                    </h2>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($filteredForms as $form)
                            <div class="group bg-gray-50 dark:bg-gray-700 rounded-lg p-6 hover:bg-gray-100 dark:hover:bg-gray-600 transition-all duration-200 hover:shadow-md border border-gray-200 dark:border-gray-600">
                                <div class="flex items-start space-x-4">
                                    <div class="flex-shrink-0">
                                        <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center group-hover:bg-blue-200 dark:group-hover:bg-blue-800 transition-colors duration-200">
                                            @php
                                                $extension = strtolower(pathinfo($form->original_name ?? $form->file_path, PATHINFO_EXTENSION));
                                                $iconClass = match($extension) {
                                                    'pdf' => 'text-red-600 dark:text-red-400',
                                                    'doc', 'docx' => 'text-blue-600 dark:text-blue-400',
                                                    'xls', 'xlsx' => 'text-green-600 dark:text-green-400',
                                                    'jpg', 'jpeg', 'png' => 'text-purple-600 dark:text-purple-400',
                                                    default => 'text-gray-600 dark:text-gray-400'
                                                };
                                            @endphp
                                            <svg class="w-6 h-6 {{ $iconClass }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors duration-200">
                                            {{ $form->name }}
                                        </h3>
                                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">
                                            {{ $form->original_name ?? basename($form->file_path) }}
                                        </p>
                                        <button
                                            wire:click="downloadFile({{ $form->id }})"
                                            class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 text-white text-sm font-medium rounded-lg transition-all duration-200 hover:shadow-md focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-700"
                                        >
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            Download
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <!-- Empty State -->
                <div class="text-center py-16">
                    <div class="flex justify-center mb-6">
                        <div class="w-20 h-20 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center">
                            <svg class="w-10 h-10 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                    </div>
                    @if($search)
                        <h3 class="text-xl font-medium text-gray-900 dark:text-white mb-2">No forms found</h3>
                        <p class="text-gray-500 dark:text-gray-400 mb-4">
                            No forms match your search for "{{ $search }}"
                        </p>
                        <button
                            wire:click="$set('search', '')"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200"
                        >
                            Clear Search
                        </button>
                    @else
                        <h3 class="text-xl font-medium text-gray-900 dark:text-white mb-2">No forms available</h3>
                        <p class="text-gray-500 dark:text-gray-400">
                            There are currently no forms available for download.
                        </p>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('livewire:load', function() {
        Livewire.on('swal', (data) => {
            Swal.fire({
                title: data.title,
                text: data.text,
                icon: data.icon,
                confirmButtonText: 'OK'
            });
        });
    });
</script>
@endpush
