<div class="w-full">

    {{-- Main Display --}}
    <div class="flex justify-center w-full">
        <div class="overflow-x-auto w-full bg-white rounded-2xl p-3 shadow dark:bg-gray-800">

            @if(!$showWorkExpSheet)

                <div class="pt-4 pb-4">
                    <h1 class="text-lg font-bold text-center text-black dark:text-white">Work Experience Sheet</h1>
                </div>

                <div class="mb-6 flex flex-col sm:flex-row items-end justify-between">
                    {{-- Search Input --}}
                    <div class="w-full sm:w-1/3 sm:mr-4">
                        <label for="search"
                            class="block text-sm font-medium text-gray-700 dark:text-slate-400 mb-1">Search</label>
                        <input type="text" id="search" wire:model.live="search"
                            class="px-2 py-1.5 block w-full shadow-sm sm:text-sm border border-gray-400 hover:bg-gray-300 rounded-md
                                dark:hover:bg-slate-600 dark:border-slate-600
                                dark:text-gray-300 dark:bg-gray-800"
                            placeholder="Enter employee name or ID">
                    </div>
                </div>

                <!-- Table -->
                <div class="flex flex-col">
                    <div class="-my-2 overflow-x-auto">
                        <div class="inline-block w-full py-2 align-middle">
                            <div class="overflow-hidden border dark:border-gray-700 rounded-lg">
                                <div class="overflow-x-auto">
                                    <table class="w-full min-w-full">
                                        <thead class="bg-gray-200 dark:bg-gray-700 rounded-xl">
                                            <tr class="whitespace-nowrap">
                                                <th scope="col"
                                                    class="px-5 py-3 text-sm font-medium text-left uppercase">
                                                    Employee
                                                </th>
                                                <th scope="col"
                                                    class="px-5 py-3 text-sm font-medium text-center uppercase">
                                                    Employee No.
                                                </th>
                                                <th scope="col"
                                                    class="px-5 py-3 text-sm font-medium text-center uppercase">
                                                    Work Experience
                                                </th>
                                                <th
                                                    class="px-5 py-3 text-gray-100 text-sm font-medium text-center sticky right-0 z-10 bg-gray-600 dark:bg-gray-600">
                                                    Action
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-neutral-200 dark:divide-gray-400">
                                            @foreach ($users as $user)
                                                <tr class="text-sm whitespace-nowrap">
                                                    <td class="px-4 py-2 text-left">
                                                        <div class="flex gap-3 items-center">
                                                            @if ($user->profile_photo_path)
                                                                <img src="{{ route('profile-photo.file', ['filename' => basename($user->profile_photo_path)]) }}"
                                                                    alt="{{ $user?->name ?? 'No User Assigned' }}"
                                                                    width="32" height="32"
                                                                    class="w-10 h-10 rounded-full object-cover border border-gray-500">
                                                            @else
                                                                <div class="w-10 h-10 rounded-full bg-gray-500 border border-gray-500 dark:bg-gray-600 flex items-center justify-center text-white text-xs font-medium">
                                                                    {{ strtoupper(substr(($user?->name ?? 'No User Assigned'), 0, 1)) }}{{ strtoupper(substr(explode(' ', ($user?->name ?? 'No User Assigned'))[1] ?? '', 0, 1)) }}
                                                                </div>
                                                            @endif
                                                            <span>{{ $user->name }}</span>
                                                        </div>
                                                    </td>
                                                    <td class="px-4 py-2 text-center">
                                                        {{ $user->emp_code }}
                                                    </td>
                                                    <td class="px-4 py-2 text-center">
                                                        {{ $user->formatted_exp }}
                                                    </td>
            
                                                    <td
                                                        class="px-5 py-4 text-sm font-medium text-center whitespace-nowrap sticky right-0 z-10 bg-white dark:bg-gray-800">
                                                        <button wire:click="showPDF({{ $user->id }})"
                                                            class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium tracking-wide text-blue-500 hover:text-blue-600 focus:outline-none">
                                                            <i class="fas fa-eye" title="Show Details"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    @if ($users->isEmpty())
                                        <div class="p-4 text-center text-gray-500 dark:text-gray-300">
                                            No records!
                                        </div> 
                                    @endif
                                </div>
                                <div class="p-5 text-neutral-500 dark:text-neutral-200 bg-gray-200 dark:bg-gray-700">
                                    {{ $users->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            @else

                <div class="mb-8 flex flex-col sm:flex-row items-center justify-between relative">
                    <p class="text-md">Employee: <span class="text-gray-800 dark:text-gray-50">{{ $employeeName }}</span></p>
                    <button wire:click="closeWorkExpSheet"
                        class="text-black dark:text-white whitespace-nowrap mx-2">
                        <i class="bi bi-x-circle" title="Close"></i>
                    </button>
                </div>

                <div class="mt-2" style="overflow: hidden;">
                    <iframe id="pdfIframe" src="data:application/pdf;base64,{{ $pdfContent }}"
                        style="width: 100%; max-height: 80vh; min-height: 500px;" frameborder="0"></iframe>
                </div>

            @endif

        </div>
    </div>

    
</div>

<script>
    function resizeIframe() {
        const iframe = document.getElementById('pdfIframe');
        const pdfDocument = iframe.contentDocument || iframe.contentWindow.document;

        if (pdfDocument) {
            // Set the iframe height based on the content
            iframe.style.height = pdfDocument.body.scrollHeight + 'px';
        }
    }

    // Adjust iframe size when the PDF is loaded
    document.getElementById('pdfIframe').onload = resizeIframe;

    // Optional: Adjust iframe size when the window is resized
    window.onresize = resizeIframe;
</script>
