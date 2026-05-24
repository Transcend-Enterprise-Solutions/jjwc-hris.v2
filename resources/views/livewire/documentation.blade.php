<div class="w-full">
    <div class="flex justify-center w-full">
        <div class="w-full bg-white rounded-2xl p-4 shadow dark:bg-gray-800">

            {{-- Header --}}
            <div class="pt-2 pb-3 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                <div>
                    <h1 class="text-lg font-bold text-black dark:text-white">{{ $manualTitle }}</h1>
                    <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5" id="viewHint">
                        Use the arrows or keyboard ← → to flip pages
                    </p>
                </div>

                @if ($pdfReady)
                {{-- View Toggle + Actions --}}
                <div class="flex items-center gap-2 flex-wrap">

                    {{-- Toggle Tabs --}}
                    <div class="flex rounded-lg border border-gray-200 dark:border-slate-600 overflow-hidden text-xs font-medium">
                        <button id="btnBook"
                            onclick="switchView('book')"
                            class="px-3 py-1.5 bg-amber-700 text-white transition">
                            <i class="bi bi-book mr-1"></i>Book View
                        </button>
                        <button id="btnPdf"
                            onclick="switchView('pdf')"
                            class="px-3 py-1.5 bg-white dark:bg-slate-700 text-gray-600 dark:text-slate-300 hover:bg-gray-50 dark:hover:bg-slate-600 transition">
                            <i class="bi bi-file-earmark-pdf mr-1"></i>PDF Viewer
                        </button>
                    </div>

                    <span class="text-gray-300 dark:text-slate-600 hidden sm:inline">|</span>

                    {{-- Download --}}
                    <button id="btnDownload" onclick="downloadPdf()"
                        class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg
                               border border-gray-300 dark:border-slate-600
                               text-gray-700 dark:text-slate-300
                               hover:bg-gray-100 dark:hover:bg-slate-700 transition">
                        <i class="bi bi-download"></i> Download
                    </button>

                    {{-- Print --}}
                    <button id="btnPrint" onclick="printPdf()"
                        class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg
                               border border-gray-300 dark:border-slate-600
                               text-gray-700 dark:text-slate-300
                               hover:bg-gray-100 dark:hover:bg-slate-700 transition">
                        <i class="bi bi-printer"></i> Print
                    </button>

                </div>
                @endif
            </div>

            @if ($pdfReady)

            {{-- ═══════════════════════════════════════════ BOOK VIEW ═══════════════════════════════════════════ --}}
            <div id="bookView">

                {{-- Zoom + Page Info toolbar --}}
                <div class="flex items-center justify-between mb-3 px-1" id="toolbar" style="display:none!important">
                    <div class="flex items-center gap-2">
                        <button onclick="zoomOut()"
                            class="px-3 py-1.5 text-xs font-medium rounded-md border border-gray-300 dark:border-slate-600
                                   text-gray-700 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-700 transition">
                            <i class="bi bi-zoom-out"></i> Zoom out
                        </button>
                        <span id="zoomLabel" class="text-xs text-gray-500 dark:text-slate-400 min-w-[40px] text-center">100%</span>
                        <button onclick="zoomIn()"
                            class="px-3 py-1.5 text-xs font-medium rounded-md border border-gray-300 dark:border-slate-600
                                   text-gray-700 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-700 transition">
                            <i class="bi bi-zoom-in"></i> Zoom in
                        </button>
                    </div>
                    <span id="pageInfo" class="text-xs text-gray-500 dark:text-slate-400"></span>
                </div>

                {{-- Book Stage --}}
                <div class="flex items-center gap-2 sm:gap-4 w-full justify-center py-4">

                    {{-- Prev Arrow --}}
                    <button id="prevBtn" onclick="prevSpread()" disabled
                        class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center
                               border border-gray-300 dark:border-slate-600
                               text-gray-500 dark:text-slate-400
                               hover:bg-gray-100 dark:hover:bg-slate-700
                               disabled:opacity-30 disabled:cursor-not-allowed transition">
                        <i class="bi bi-chevron-left"></i>
                    </button>

                    {{-- Book --}}
                    <div id="bookWrap" style="
                        perspective: 2400px;
                        width: 100%;
                        max-width: 1100px;
                        position: relative;
                    ">
                        {{-- Shadow underneath --}}
                        <div style="
                            position:absolute; bottom:-18px; left:6%; right:6%; height:28px;
                            background:radial-gradient(ellipse at 50% 100%, rgba(0,0,0,0.28) 0%, transparent 70%);
                            border-radius:50%; filter:blur(6px); pointer-events:none;
                        "></div>

                        {{-- Spread container --}}
                        <div id="bookInner" style="
                            display: flex;
                            width: 100%;
                            border-radius: 4px;
                            overflow: hidden;
                            box-shadow: 0 20px 60px rgba(0,0,0,0.30), 0 4px 12px rgba(0,0,0,0.15);
                            min-height: 520px;
                            position: relative;
                        ">
                            {{-- Left Page --}}
                            <div id="leftPage" style="
                                flex:1; background:#fdf8f0; position:relative;
                                border-right: 4px solid #c8a97e; overflow:hidden;
                            ">
                                <canvas id="leftCanvas" style="width:100%;height:100%;display:block;"></canvas>
                                <span id="leftNum" style="
                                    position:absolute;bottom:10px;left:14px;
                                    font-size:12px;color:#8b6340;font-style:italic;font-family:Georgia,serif;
                                "></span>
                            </div>

                            {{-- Spine --}}
                            <div style="
                                width:10px; flex-shrink:0;
                                background: linear-gradient(to right,#6b4226,#c8a97e 30%,#e0c49a 50%,#c8a97e 70%,#6b4226);
                                box-shadow: inset -2px 0 4px rgba(0,0,0,0.2), inset 2px 0 4px rgba(0,0,0,0.2);
                            "></div>

                            {{-- Right Page --}}
                            <div id="rightPage" style="
                                flex:1; background:#fdf8f0; position:relative; overflow:hidden;
                            ">
                                <canvas id="rightCanvas" style="width:100%;height:100%;display:block;"></canvas>
                                <span id="rightNum" style="
                                    position:absolute;bottom:10px;right:14px;
                                    font-size:12px;color:#8b6340;font-style:italic;font-family:Georgia,serif;
                                "></span>
                            </div>

                            {{-- Flip page overlay (the animated turning leaf) --}}
                            <div id="flipLeaf" style="
                                position:absolute; top:0; right:0;
                                width:50%; height:100%;
                                transform-style: preserve-3d;
                                transform-origin: left center;
                                transform: rotateY(0deg);
                                pointer-events:none;
                                z-index: 20;
                            ">
                                {{-- Front face (right page content mid-flip) --}}
                                <div id="leafFront" style="
                                    position:absolute; inset:0;
                                    backface-visibility: hidden;
                                    background: #fdf8f0;
                                    overflow:hidden;
                                ">
                                    <canvas id="leafFrontCanvas" style="width:100%;height:100%;display:block;"></canvas>
                                </div>
                                {{-- Back face (incoming left page content) --}}
                                <div id="leafBack" style="
                                    position:absolute; inset:0;
                                    backface-visibility: hidden;
                                    background: #fdf8f0;
                                    transform: rotateY(180deg);
                                    overflow:hidden;
                                ">
                                    <canvas id="leafBackCanvas" style="width:100%;height:100%;display:block;"></canvas>
                                </div>
                            </div>

                            {{-- Loading overlay --}}
                            <div id="loadingOverlay" style="
                                position:absolute; inset:0; display:flex;
                                align-items:center; justify-content:center;
                                background:rgba(253,248,240,0.94); border-radius:4px; z-index:30;
                            ">
                                <div style="text-align:center;">
                                    <div style="
                                        width:36px;height:36px;border:3px solid #c8a97e;
                                        border-top-color:transparent; border-radius:50%;
                                        animation:spin 0.8s linear infinite; margin:0 auto 12px;
                                    "></div>
                                    <p style="font-size:12px;color:#8b6340;font-family:Georgia,serif;">Preparing your manual…</p>
                                </div>
                            </div>

                        </div>{{-- /bookInner --}}
                    </div>{{-- /bookWrap --}}

                    {{-- Next Arrow --}}
                    <button id="nextBtn" onclick="nextSpread()" disabled
                        class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center
                               border border-gray-300 dark:border-slate-600
                               text-gray-500 dark:text-slate-400
                               hover:bg-gray-100 dark:hover:bg-slate-700
                               disabled:opacity-30 disabled:cursor-not-allowed transition">
                        <i class="bi bi-chevron-right"></i>
                    </button>

                </div>

                {{-- Thumbnail Strip --}}
                <div id="thumbStrip" style="
                    display:flex; gap:6px; overflow-x:auto;
                    max-width:1100px; width:100%; margin:0 auto;
                    padding:8px 2px; scrollbar-width:thin;
                "></div>

            </div>{{-- /bookView --}}


            {{-- ═══════════════════════════════════════════ PDF VIEWER ══════════════════════════════════════════ --}}
            <div id="pdfView" style="display:none;">
                <iframe id="pdfFrame"
                    style="width:100%; height:82vh; border:none; border-radius:8px; display:block;"
                    src="">
                </iframe>
            </div>

            @else

            <div class="flex flex-col items-center justify-center py-20 gap-3">
                <i class="bi bi-file-earmark-x text-4xl text-gray-400 dark:text-slate-500"></i>
                <p class="text-sm text-gray-500 dark:text-slate-400">No manual found. Please contact your administrator.</p>
            </div>

            @endif

        </div>
    </div>

    <style>
        @keyframes spin   { to { transform: rotate(360deg); } }
        @keyframes flipFwd {
            0%   { transform: rotateY(0deg); }
            100% { transform: rotateY(-180deg); }
        }
        @keyframes flipBwd {
            0%   { transform: rotateY(-180deg); }
            100% { transform: rotateY(0deg); }
        }
        .flip-forward { animation: flipFwd 0.55s cubic-bezier(0.645,0.045,0.355,1.000) forwards; }
        .flip-backward { animation: flipBwd 0.55s cubic-bezier(0.645,0.045,0.355,1.000) forwards; }

        #thumbStrip::-webkit-scrollbar { height: 4px; }
        #thumbStrip::-webkit-scrollbar-track { background: transparent; }
        #thumbStrip::-webkit-scrollbar-thumb { background: #c8a97e; border-radius: 2px; }

        #leafFront, #leafBack {
            box-shadow: inset -8px 0 24px rgba(0,0,0,0.12);
        }
    </style>
</div>

@if ($pdfReady)
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script>
(function () {
    pdfjsLib.GlobalWorkerOptions.workerSrc =
        'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

    // ── Decode base64 ──────────────────────────────────────────────────────────
    var base64    = @json($pdfBase64);
    var binary    = atob(base64);
    var bytes     = new Uint8Array(binary.length);
    for (var i = 0; i < binary.length; i++) bytes[i] = binary.charCodeAt(i);
    var blobUrl   = URL.createObjectURL(new Blob([bytes], { type: 'application/pdf' }));

    // ── State ──────────────────────────────────────────────────────────────────
    var pdfDoc      = null;
    var totalPages  = 0;
    var curSpread   = 0;
    var pageImages  = [];
    var zoomLevel   = 1.0;
    var RENDER_SCALE = 2.5;   // higher = sharper/bigger render
    var isFlipping  = false;

    // ── Init ───────────────────────────────────────────────────────────────────
    function init() {
        pdfjsLib.getDocument({ data: bytes }).promise.then(function (doc) {
            pdfDoc     = doc;
            totalPages = doc.numPages;
            pageImages = new Array(totalPages).fill(null);

            renderAllPages().then(function () {
                document.getElementById('loadingOverlay').style.display = 'none';
                document.getElementById('toolbar').style.display        = 'flex';
                buildThumbs();
                showSpread(0, null);
            });
        }).catch(function (err) {
            console.error('PDF load error:', err);
            document.getElementById('loadingOverlay').innerHTML =
                '<p style="font-size:13px;color:#dc2626;padding:1.5rem;">Failed to load PDF. Please refresh.</p>';
        });
    }

    // ── Render all pages to offscreen canvases ─────────────────────────────────
    function renderAllPages() {
        var tasks = [];
        for (var i = 1; i <= totalPages; i++) tasks.push(renderPage(i));
        return Promise.all(tasks);
    }

    function renderPage(num) {
        return pdfDoc.getPage(num).then(function (page) {
            var vp     = page.getViewport({ scale: RENDER_SCALE });
            var c      = document.createElement('canvas');
            c.width    = vp.width;
            c.height   = vp.height;
            return page.render({ canvasContext: c.getContext('2d'), viewport: vp })
                .promise.then(function () {
                    pageImages[num - 1] = c.toDataURL('image/jpeg', 0.93);
                    updateThumb(num - 1);
                });
        });
    }

    // ── Show spread (no flip) ──────────────────────────────────────────────────
    function showSpread(spreadIdx, direction) {
        curSpread = spreadIdx;
        var li = spreadIdx * 2;
        var ri = li + 1;

        if (direction !== null) {
            animateFlip(direction, li, ri);
        } else {
            paintCanvas('leftCanvas',  'leftNum',  li);
            paintCanvas('rightCanvas', 'rightNum', ri);
        }

        var totalSpreads = Math.ceil(totalPages / 2);
        document.getElementById('prevBtn').disabled = curSpread === 0;
        document.getElementById('nextBtn').disabled = curSpread >= totalSpreads - 1;

        var pFrom = li + 1;
        var pTo   = Math.min(ri + 1, totalPages);
        document.getElementById('pageInfo').textContent =
            'Pages ' + pFrom + '\u2013' + pTo + ' of ' + totalPages;

        highlightThumb(spreadIdx);
    }

    // ── Real 3-D flip animation ────────────────────────────────────────────────
    function animateFlip(direction, newLeftIdx, newRightIdx) {
        if (isFlipping) return;
        isFlipping = true;

        var leaf     = document.getElementById('flipLeaf');
        var lFront   = document.getElementById('leafFrontCanvas');
        var lBack    = document.getElementById('leafBackCanvas');

        // The leaf always covers the right side when going forward,
        // and the left side when going backward.
        if (direction === 'forward') {
            leaf.style.left  = '50%';
            leaf.style.right = 'auto';
            leaf.style.transformOrigin = 'left center';
            // Front shows current right page (the one that turns away)
            var prevRightIdx = (curSpread) * 2 + 1; // before curSpread was updated
            paintToCanvas(lFront, prevRightIdx);
            // Back shows the incoming right page
            paintToCanvas(lBack, newRightIdx);
        } else {
            leaf.style.left  = '0';
            leaf.style.right = 'auto';
            leaf.style.width = '50%';
            leaf.style.transformOrigin = 'right center';
            // Front shows current left page (turning away)
            var prevLeftIdx = (curSpread) * 2;
            paintToCanvas(lFront, prevLeftIdx);
            // Back shows incoming left page
            paintToCanvas(lBack, newLeftIdx);
        }

        leaf.style.pointerEvents = 'none';

        // Paint the destination spread underneath immediately
        paintCanvas('leftCanvas',  'leftNum',  newLeftIdx);
        paintCanvas('rightCanvas', 'rightNum', newRightIdx);

        // Remove old animation classes
        leaf.classList.remove('flip-forward', 'flip-backward');
        void leaf.offsetWidth; // reflow

        leaf.classList.add(direction === 'forward' ? 'flip-forward' : 'flip-backward');

        leaf.addEventListener('animationend', function handler() {
            leaf.removeEventListener('animationend', handler);
            // Reset leaf to invisible
            if (direction === 'forward') {
                leaf.style.transform = 'rotateY(-180deg)';
            } else {
                leaf.style.transform = 'rotateY(0deg)';
            }
            leaf.classList.remove('flip-forward', 'flip-backward');
            leaf.style.transform = '';
            isFlipping = false;
        });
    }

    function paintToCanvas(canvas, pageIdx) {
        if (pageIdx < totalPages && pageImages[pageIdx]) {
            var img = new Image();
            img.onload = function () {
                canvas.width  = img.width;
                canvas.height = img.height;
                canvas.getContext('2d').drawImage(img, 0, 0);
            };
            img.src = pageImages[pageIdx];
        } else {
            canvas.width  = 10;
            canvas.height = 10;
            canvas.getContext('2d').clearRect(0, 0, 10, 10);
        }
    }

    function paintCanvas(canvasId, numId, pageIdx) {
        var canvas = document.getElementById(canvasId);
        var numEl  = document.getElementById(numId);

        if (pageIdx < totalPages && pageImages[pageIdx]) {
            var img = new Image();
            img.onload = function () {
                canvas.width  = img.width;
                canvas.height = img.height;
                canvas.getContext('2d').drawImage(img, 0, 0);
                applyZoomToCanvas(canvas);
            };
            img.src = pageImages[pageIdx];
            numEl.textContent = pageIdx + 1;
        } else {
            canvas.width  = 10;
            canvas.height = 10;
            canvas.getContext('2d').clearRect(0, 0, 10, 10);
            numEl.textContent = '';
        }
    }

    function applyZoomToCanvas(canvas) {
        canvas.style.transform       = 'scale(' + zoomLevel + ')';
        canvas.style.transformOrigin = 'top center';
    }

    // ── Thumbnails ─────────────────────────────────────────────────────────────
    function buildThumbs() {
        var strip = document.getElementById('thumbStrip');
        strip.innerHTML = '';
        for (var i = 0; i < totalPages; i++) {
            var t       = document.createElement('div');
            t.dataset.idx = i;
            t.style.cssText =
                'width:54px;height:70px;flex-shrink:0;border-radius:4px;cursor:pointer;' +
                'border:2px solid #d1d5db;background:#f3f4f6;overflow:hidden;' +
                'display:flex;align-items:center;justify-content:center;' +
                'font-size:11px;color:#9ca3af;transition:border-color 0.15s,box-shadow 0.15s;';
            t.textContent = i + 1;
            (function (idx) {
                t.onclick = function () {
                    var targetSpread = Math.floor(idx / 2);
                    if (targetSpread === curSpread) return;
                    var dir = targetSpread > curSpread ? 'forward' : 'backward';
                    showSpread(targetSpread, dir);
                };
            })(i);
            strip.appendChild(t);
        }
    }

    function updateThumb(idx) {
        var thumbs = document.getElementById('thumbStrip').children;
        if (!thumbs[idx] || !pageImages[idx]) return;
        var t = thumbs[idx];
        t.style.backgroundImage    = 'url(' + pageImages[idx] + ')';
        t.style.backgroundSize     = 'cover';
        t.style.backgroundPosition = 'top center';
        t.textContent = '';
    }

    function highlightThumb(spreadIdx) {
        var thumbs  = document.getElementById('thumbStrip').children;
        var li      = spreadIdx * 2;
        var ri      = li + 1;
        Array.from(thumbs).forEach(function (t, i) {
            var active = (i === li || i === ri);
            t.style.borderColor = active ? '#c8a97e' : '#d1d5db';
            t.style.boxShadow   = active ? '0 0 0 2px rgba(200,169,126,0.4)' : 'none';
        });
        if (thumbs[li]) {
            thumbs[li].scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
        }
    }

    // ── Public navigation ──────────────────────────────────────────────────────
    window.prevSpread = function () {
        if (isFlipping || curSpread === 0) return;
        showSpread(curSpread - 1, 'backward');
    };

    window.nextSpread = function () {
        if (isFlipping || curSpread >= Math.ceil(totalPages / 2) - 1) return;
        showSpread(curSpread + 1, 'forward');
    };

    // ── Zoom ───────────────────────────────────────────────────────────────────
    window.zoomIn = function () {
        if (zoomLevel >= 2.0) return;
        zoomLevel = +(Math.min(2.0, zoomLevel + 0.1).toFixed(1));
        applyZoom();
    };

    window.zoomOut = function () {
        if (zoomLevel <= 0.5) return;
        zoomLevel = +(Math.max(0.5, zoomLevel - 0.1).toFixed(1));
        applyZoom();
    };

    function applyZoom() {
        ['leftCanvas', 'rightCanvas'].forEach(function (id) {
            applyZoomToCanvas(document.getElementById(id));
        });
        document.getElementById('zoomLabel').textContent = Math.round(zoomLevel * 100) + '%';
    }

    // ── Keyboard ───────────────────────────────────────────────────────────────
    document.addEventListener('keydown', function (e) {
        if (document.getElementById('bookView').style.display === 'none') return;
        if (e.key === 'ArrowRight') window.nextSpread();
        if (e.key === 'ArrowLeft')  window.prevSpread();
    });

    // ── View switcher ──────────────────────────────────────────────────────────
    window.switchView = function (mode) {
        var bookView = document.getElementById('bookView');
        var pdfView  = document.getElementById('pdfView');
        var btnBook  = document.getElementById('btnBook');
        var btnPdf   = document.getElementById('btnPdf');
        var hint     = document.getElementById('viewHint');

        if (mode === 'book') {
            bookView.style.display = 'block';
            pdfView.style.display  = 'none';
            btnBook.className = 'px-3 py-1.5 bg-amber-700 text-white transition';
            btnPdf.className  = 'px-3 py-1.5 bg-white dark:bg-slate-700 text-gray-600 dark:text-slate-300 hover:bg-gray-50 dark:hover:bg-slate-600 transition';
            hint.textContent  = 'Use the arrows or keyboard ← → to flip pages';
        } else {
            bookView.style.display = 'none';
            pdfView.style.display  = 'block';
            btnPdf.className  = 'px-3 py-1.5 bg-amber-700 text-white transition';
            btnBook.className = 'px-3 py-1.5 bg-white dark:bg-slate-700 text-gray-600 dark:text-slate-300 hover:bg-gray-50 dark:hover:bg-slate-600 transition';
            hint.textContent  = 'Viewing PDF — use browser controls to zoom, scroll, and search';
            // Lazy-load the iframe src
            var frame = document.getElementById('pdfFrame');
            if (!frame.src || frame.src === window.location.href) {
                frame.src = blobUrl;
            }
        }
    };

    // ── Download ───────────────────────────────────────────────────────────────
    window.downloadPdf = function () {
        var a = document.createElement('a');
        a.href     = blobUrl;
        a.download = @json($manualTitle) + '.pdf';
        a.click();
    };

    // ── Print ──────────────────────────────────────────────────────────────────
    window.printPdf = function () {
        var win = window.open(blobUrl);
        win.addEventListener('load', function () { win.print(); });
    };

    init();
})();
</script>
@endif