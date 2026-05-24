<div class="w-full">
    
    <div class="w-full flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 flex items-center">
            <i class="bi bi-archive mr-2 text-blue-600"></i>
            Documents
        </h3>
    </div>
 
    <div class="my-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full min-w-full">
                    <thead class="bg-gray-200 dark:bg-gray-700 rounded-xl">
                        <tr class="whitespace-nowrap">
                            <th scope="col" class="px-5 py-3 text-xs font-medium text-left uppercase">
                                Document
                            </th>
                            <th scope="col" class="px-5 py-3 text-xs font-medium text-left uppercase">
                                File
                            </th>
                            <th class="px-5 py-3 text-xs font-medium text-center uppercase sticky right-0 z-10">
                                Action
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700">
                        @foreach($documents as $document)
                            <tr class="text-neutral-800 dark:text-neutral-200">
                                <td class="px-5 py-4 text-left text-xs font-medium whitespace-nowrap">
                                    {{ $document->document_type }}
                                </td>
                                <td class="px-5 py-4 text-left text-xs font-medium whitespace-nowrap">
                                    <p wire:click="downloadDocument({{ $document->id }})" class="text-blue-500 hover:underline cursor-pointer text-xs">
                                        {{ $document->file_name }} <i class="ml-2 text-sm bi bi-download"></i>
                                    </p>
                                </td>
                                <td class="px-5 py-4 text-sm font-medium text-center whitespace-nowrap sticky right-0 z-10 bg-white dark:bg-gray-800">
                                    <div class="flex items-center justify-center space-x-2">
                                        <button wire:click="viewDocument({{ $document->id }})"
                                                class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-200" 
                                                title="View">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            @if ($documents->isEmpty())
                <div class="p-4 text-center text-gray-500 dark:text-gray-300">
                    No documents found!
                </div>
            @endif
        </div>
    </div>

    @if($pdfContent)
        <div class="w-full">
            <div class="w-full flex flex-col sm:flex-row items-center justify-between">
                <p class="text-md font-semibold mt-4 text-gray-700 dark:text-gray-100"><i class="bi bi-file-earmark-pdf"></i> {{ $documentName }}</p>
                <p class="text-xs cursor-pointer hover:scale-110" wire:click="set('pdfContent', null)">Close</p>
            </div>
            <iframe class="mt-2" id="pdfIframe" src="data:application/pdf;base64,{{ $pdfContent }}"
                style="width: 100%; max-height: 80vh; min-height: 500px;" frameborder="0"></iframe>
        </div>
    @endif
</div>

<script>
    function resizeIframe() {
        const iframe = document.getElementById('pdfIframe');
        const pdfDocument = iframe.contentDocument || iframe.contentWindow.document;

        if (pdfDocument) {
            iframe.style.height = pdfDocument.body.scrollHeight + 'px';
        }
    }

    document.getElementById('pdfIframe').onload = resizeIframe;
    window.onresize = resizeIframe;
</script>
