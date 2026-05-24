<div class="w-full">
    
    <div class="w-full flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 flex items-center">
            <i class="bi bi-file-earmark-text mr-2 text-blue-600"></i>
            Work Experience Sheet
        </h3>
    </div>

    @if($pdfContent)
        <div class="mt-6" style="overflow: hidden;">
            <iframe id="pdfIframe2" src="data:application/pdf;base64,{{ $pdfContent }}"
                style="width: 100%; max-height: 80vh; min-height: 500px;" frameborder="0"></iframe>
        </div>
    @endif
</div>

<script>
    function resizeIframe() {
        const iframe = document.getElementById('pdfIframe2');
        const pdfDocument = iframe.contentDocument || iframe.contentWindow.document;

        if (pdfDocument) {
            iframe.style.height = pdfDocument.body.scrollHeight + 'px';
        }
    }

    document.getElementById('pdfIframe2').onload = resizeIframe;

    window.onresize = resizeIframe;
</script>
