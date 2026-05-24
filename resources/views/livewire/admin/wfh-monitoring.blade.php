@once
    @push('styles')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tT0L0rGfF8IuF9G+Gx3Y=" crossorigin="">
        <style>
            [data-wfh-tab-button][aria-selected="true"] {
                background: #fff;
                color: #1d4ed8;
                box-shadow: 0 1px 2px rgb(15 23 42 / 0.08);
                outline: 1px solid rgb(219 234 254);
            }

            .dark [data-wfh-tab-button][aria-selected="true"] {
                background: rgb(59 130 246 / 0.15);
                color: #bfdbfe;
                outline-color: rgb(59 130 246 / 0.2);
            }
        </style>
    @endpush
    @push('scripts')
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    @endpush
@endonce

<script>
    window.__legacyWfhMonitoringAdmin = (wire) => ({
    wire,
    livePeer: null,
    liveToken: null,
    liveStatus: 'Idle',
    liveSessionId: null,
    liveEmployeeName: null,
    snapshotSessionId: null,
    snapshotEmployeeName: null,
    snapshotToken: null,
    snapshotStatus: 'Select an employee to start live snapshots.',
    snapshotUrl: null,
    snapshotCapturedAt: null,
    snapshotType: null,
    snapshotPollTimer: null,
    mediaPeer: null,
    mediaToken: null,
    mediaStatus: 'Idle',
    mediaSessionId: null,
    mediaEmployeeName: null,
    tab: 'sessions',
    gpsSelectedSessionId: @js($gpsSelectedSession?->id ?? $selectedMonitoringSession?->id ?? null),
    gpsTrailPoints: @js($gpsLocationTrail),
    gpsMap: null,
    gpsMapLayer: null,
    gpsMapMarkers: [],
    escapeHtml(value) {
        return String(value ?? '').replace(/[&<>"']/g, (char) => ({
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#39;',
        }[char]));
    },
    async selectMonitoringSession(sessionId, tabName = null) {
        if (tabName) {
            this.tab = tabName;
        }

        try {
            await this.wire.selectMonitoringSession(sessionId);
        } catch {
            // Keep the screen controls responsive while Livewire refreshes.
        }
    },
    selectGpsSession(sessionId) {
        this.gpsSelectedSessionId = sessionId;
        this.wire.selectMonitoringSession(sessionId);
        this.tab = 'gps';
        this.$nextTick(() => this.renderGpsMap(true));
    },
    openTab(name) {
        this.tab = name;

        if (name === 'gps') {
            this.$nextTick(() => this.renderGpsMap(true));
        }
    },
    init() {
        this.$watch('tab', (value) => {
            if (value === 'gps') {
                this.$nextTick(() => this.renderGpsMap(true));
            }
        });

        this.$nextTick(() => {
            if (this.tab === 'gps') {
                this.renderGpsMap(true);
            }
        });
    },
    focusGpsPoint(index) {
        if (!this.gpsMap || !Array.isArray(this.gpsTrailPoints) || !this.gpsTrailPoints[index]) {
            return;
        }

        const point = this.gpsTrailPoints[index];
        const marker = this.gpsMapMarkers[index];

        this.gpsMap.setView([point.lat, point.lng], 14, {
            animate: true,
        });

        if (marker && typeof marker.openPopup === 'function') {
            marker.openPopup();
        }
    },
    renderGpsMap(force = false) {
        if (this.tab !== 'gps' || !window.L) {
            return;
        }

        const mapElement = this.$refs.gpsMap;

        if (!mapElement) {
            return;
        }

        if (force && this.gpsMap) {
            this.gpsMap.remove();
            this.gpsMap = null;
            this.gpsMapLayer = null;
            this.gpsMapMarkers = [];
        }

        if (!this.gpsMap) {
            this.gpsMap = L.map(mapElement, {
                scrollWheelZoom: true,
                zoomControl: true,
            });

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors',
                maxZoom: 19,
            }).addTo(this.gpsMap);

            this.gpsMapLayer = L.layerGroup().addTo(this.gpsMap);
        }

        this.$nextTick(() => {
            requestAnimationFrame(() => {
                this.gpsMap.invalidateSize(true);
            });

            this.gpsMapLayer.clearLayers();
            this.gpsMapMarkers = [];

            const points = (this.gpsTrailPoints || [])
                .filter((point) => Number.isFinite(point.lat) && Number.isFinite(point.lng));

            if (!points.length) {
                const fallbackCenter = [14.5995, 120.9842];
                this.gpsMap.setView(fallbackCenter, 12);
                return;
            }

            const latLngs = points.map((point) => [point.lat, point.lng]);
            const polyline = L.polyline(latLngs, {
                color: '#2563eb',
                weight: 4,
                opacity: 0.9,
                lineCap: 'round',
                lineJoin: 'round',
            }).addTo(this.gpsMapLayer);

            points.forEach((point, index) => {
                const isLatest = index === points.length - 1;
                const marker = L.circleMarker([point.lat, point.lng], {
                    radius: isLatest ? 12 : 7,
                    color: isLatest ? '#dc2626' : '#475569',
                    weight: 3,
                    fillColor: isLatest ? '#fca5a5' : '#cbd5e1',
                    fillOpacity: 0.95,
                }).addTo(this.gpsMapLayer);

                const popupContent = `
                    <div style="min-width: 180px; font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, sans-serif;">
                        <div style="font-weight: 700; color: #0f172a;">${this.escapeHtml(point.label || 'Employee location')}</div>
                        <div style="margin-top: 2px; font-size: 12px; color: #64748b;">${this.escapeHtml(point.time || '')}</div>
                        ${point.status ? `<div style="margin-top: 4px; font-size: 12px; color: #2563eb;">${this.escapeHtml(point.status)}</div>` : ''}
                        ${point.accuracy ? `<div style="margin-top: 4px; font-size: 12px; color: #475569;">Accuracy ${this.escapeHtml(point.accuracy)}</div>` : ''}
                    </div>
                `;

                marker.bindPopup(popupContent);
                if (isLatest) {
                    marker.bindTooltip('Current location', {
                        direction: 'top',
                        offset: [0, -8],
                        permanent: true,
                    });
                }
                this.gpsMapMarkers.push(marker);
            });

            if (points.length > 1) {
                const startMarker = L.circleMarker(latLngs[0], {
                    radius: 8,
                    color: '#16a34a',
                    weight: 3,
                    fillColor: '#86efac',
                    fillOpacity: 0.95,
                }).addTo(this.gpsMapLayer);

                startMarker.bindTooltip('Trail start', { direction: 'top', offset: [0, -6] });
                this.gpsMapMarkers.push(startMarker);
            }

            this.gpsMap.fitBounds(polyline.getBounds().pad(0.35), {
                maxZoom: 14,
            });

            setTimeout(() => {
                this.gpsMap.invalidateSize(true);
            }, 150);
        });
    },
    async waitForIceGathering(peer) {
        if (peer.iceGatheringState === 'complete') return;

        await new Promise((resolve) => {
            const timeout = setTimeout(resolve, 3000);
            peer.addEventListener('icegatheringstatechange', () => {
                if (peer.iceGatheringState === 'complete') {
                    clearTimeout(timeout);
                    resolve();
                }
            });
        });
    },
    async startLiveScreen(sessionId, employeeName = null) {
        if (!window.RTCPeerConnection) {
            this.liveStatus = 'WebRTC is not supported by this browser.';
            return;
        }

        this.stopLocalLiveScreen(false);
        this.tab = 'screens';
        await this.$nextTick();
        this.liveSessionId = sessionId;
        this.liveEmployeeName = employeeName;
        this.liveStatus = 'Requesting employee screen stream...';

        let request = null;

        try {
            request = await this.wire.requestLiveScreen(sessionId);
        } catch (error) {
            this.liveStatus = error?.message || 'Unable to request live screen for this session.';
            return;
        }

        if (!request?.token) {
            this.liveStatus = 'Unable to request live screen for this session.';
            return;
        }

        this.liveToken = request.token;
        const peer = new RTCPeerConnection({
            iceServers: [{ urls: 'stun:stun.l.google.com:19302' }],
        });

        peer.addTransceiver('video', { direction: 'recvonly' });
        peer.ontrack = (event) => {
            const video = this.$refs.liveScreenVideo;
            if (video) {
                video.srcObject = event.streams?.[0] || new MediaStream([event.track]);
                video.play();
                this.liveStatus = 'Live screen connected.';
            }
        };
        peer.onconnectionstatechange = () => {
            if (['connected', 'completed'].includes(peer.connectionState)) {
                this.liveStatus = 'Live screen connected.';
                return;
            }

            if (['failed', 'disconnected', 'closed'].includes(peer.connectionState)) {
                this.liveStatus = 'Live screen connection failed.';
                return;
            }

            this.liveStatus = `Live screen ${peer.connectionState}.`;
        };

        const offer = await peer.createOffer();
        await peer.setLocalDescription(offer);
        await this.waitForIceGathering(peer);
        await this.wire.publishLiveOffer(sessionId, this.liveToken, peer.localDescription.toJSON());
        this.livePeer = peer;
        this.liveStatus = 'Waiting for employee browser to answer...';
        this.pollLiveAnswer();
    },
    async pollLiveAnswer() {
        if (!this.liveSessionId || !this.liveToken || !this.livePeer) return;

        if (['connected', 'completed'].includes(this.livePeer.connectionState)) {
            this.liveStatus = 'Live screen connected.';
            return;
        }

        if (['failed', 'disconnected', 'closed'].includes(this.livePeer.connectionState)) {
            this.liveStatus = 'Live screen connection failed.';
            return;
        }

        const signal = await this.wire.getLiveSignal(this.liveSessionId);

        if (signal?.token === this.liveToken && signal?.answer && !this.livePeer.currentRemoteDescription) {
            await this.livePeer.setRemoteDescription(new RTCSessionDescription(signal.answer));
            this.liveStatus = 'Connecting live screen...';
        }

        if (signal?.status === 'stopped') {
            this.stopLocalLiveScreen(false);
            return;
        }

        setTimeout(() => this.pollLiveAnswer(), 1500);
    },
    stopLocalLiveScreen(report = true) {
        if (this.livePeer) {
            this.livePeer.close();
        }

        const video = this.$refs.liveScreenVideo;
        if (video) {
            video.srcObject = null;
        }

        if (report && this.liveSessionId) {
            this.wire.stopLiveScreen(this.liveSessionId);
        }

        this.livePeer = null;
        this.liveToken = null;
        this.liveSessionId = null;
        this.liveEmployeeName = null;
        this.liveStatus = 'Idle';
    },
    async startLiveSnapshots(sessionId, employeeName = null) {
        this.stopLocalLiveScreen(false);
        this.stopLiveSnapshots(false);
        this.tab = 'screens';
        this.snapshotSessionId = sessionId;
        this.snapshotEmployeeName = employeeName;
        this.snapshotStatus = 'Starting live snapshots...';
        this.snapshotUrl = null;
        this.snapshotCapturedAt = null;
        this.snapshotType = null;
        this.selectMonitoringSession(sessionId, 'screens');
        await this.$nextTick();

        let request = null;

        try {
            request = await this.wire.startLiveSnapshots(sessionId);
        } catch (error) {
            this.snapshotStatus = error?.message || 'Unable to start live snapshots.';
            return;
        }

        if (!request?.token) {
            this.snapshotStatus = 'Unable to start live snapshots for this employee.';
            return;
        }

        this.snapshotToken = request.token;
        this.applySnapshot(request.snapshot);
        this.snapshotStatus = 'Live snapshots active.';
        await this.refreshLiveSnapshot();
        this.snapshotPollTimer = setInterval(() => this.refreshLiveSnapshot(), Math.max(3, request.intervalSeconds || 5) * 1000);
    },
    applySnapshot(snapshot) {
        if (!snapshot?.url) {
            return;
        }

        const separator = snapshot.url.includes('?') ? '&' : '?';
        this.snapshotUrl = `${snapshot.url}${separator}v=${Date.now()}`;
        this.snapshotCapturedAt = snapshot.capturedAt || null;
        this.snapshotType = snapshot.captureType || null;
    },
    async refreshLiveSnapshot() {
        if (!this.snapshotSessionId) return;

        try {
            const snapshot = await this.wire.getLatestScreenSnapshot(this.snapshotSessionId);
            this.applySnapshot(snapshot);
            this.snapshotStatus = snapshot?.url
                ? 'Live snapshots active.'
                : 'Waiting for employee WFH Attendance page to upload a frame...';
        } catch {
            this.snapshotStatus = 'Unable to refresh the latest screen frame.';
        }
    },
    stopLiveSnapshots(report = true) {
        if (this.snapshotPollTimer) {
            clearInterval(this.snapshotPollTimer);
        }

        if (report && this.snapshotSessionId) {
            this.wire.stopLiveSnapshots(this.snapshotSessionId);
        }

        this.snapshotPollTimer = null;
        this.snapshotToken = null;
        this.snapshotSessionId = null;
        this.snapshotEmployeeName = null;
        this.snapshotUrl = null;
        this.snapshotCapturedAt = null;
        this.snapshotType = null;
        this.snapshotStatus = 'Live snapshots stopped.';
    },
    async startLiveMedia(sessionId, employeeName = null) {
        if (!window.RTCPeerConnection) {
            this.mediaStatus = 'WebRTC is not supported by this browser.';
            return;
        }

        this.stopLocalLiveMedia(false);
        this.tab = 'screens';
        await this.$nextTick();
        this.mediaSessionId = sessionId;
        this.mediaEmployeeName = employeeName;
        this.mediaStatus = 'Requesting camera and microphone permission...';

        let request = null;

        try {
            request = await this.wire.requestLiveMedia(sessionId);
        } catch (error) {
            this.mediaStatus = error?.message || 'Unable to request camera and microphone for this session.';
            return;
        }

        if (!request?.token) {
            this.mediaStatus = 'Unable to request camera and microphone for this session.';
            return;
        }

        this.mediaToken = request.token;
        const peer = new RTCPeerConnection({
            iceServers: [{ urls: 'stun:stun.l.google.com:19302' }],
        });

        peer.addTransceiver('video', { direction: 'recvonly' });
        peer.addTransceiver('audio', { direction: 'recvonly' });
        peer.ontrack = (event) => {
            const video = this.$refs.liveMediaVideo;
            if (video) {
                video.srcObject = event.streams?.[0] || new MediaStream([event.track]);
                video.play();
                this.mediaStatus = 'Camera and microphone connected.';
            }
        };
        peer.onconnectionstatechange = () => {
            if (['connected', 'completed'].includes(peer.connectionState)) {
                this.mediaStatus = 'Camera and microphone connected.';
                return;
            }

            if (['failed', 'disconnected', 'closed'].includes(peer.connectionState)) {
                this.mediaStatus = 'Camera and microphone connection failed.';
                return;
            }

            this.mediaStatus = `Camera and microphone ${peer.connectionState}.`;
        };

        const offer = await peer.createOffer();
        await peer.setLocalDescription(offer);
        await this.waitForIceGathering(peer);
        await this.wire.publishLiveMediaOffer(sessionId, this.mediaToken, peer.localDescription.toJSON());
        this.mediaPeer = peer;
        this.mediaStatus = 'Waiting for employee approval...';
        this.pollLiveMediaAnswer();
    },
    async pollLiveMediaAnswer() {
        if (!this.mediaSessionId || !this.mediaToken || !this.mediaPeer) return;

        if (['connected', 'completed'].includes(this.mediaPeer.connectionState)) {
            this.mediaStatus = 'Camera and microphone connected.';
            return;
        }

        if (['failed', 'disconnected', 'closed'].includes(this.mediaPeer.connectionState)) {
            this.mediaStatus = 'Camera and microphone connection failed.';
            return;
        }

        const signal = await this.wire.getLiveMediaSignal(this.mediaSessionId);

        if (signal?.token === this.mediaToken && signal?.answer && !this.mediaPeer.currentRemoteDescription) {
            await this.mediaPeer.setRemoteDescription(new RTCSessionDescription(signal.answer));
            this.mediaStatus = 'Connecting camera and microphone...';
        }

        if (signal?.status === 'stopped') {
            this.stopLocalLiveMedia(false);
            return;
        }

        setTimeout(() => this.pollLiveMediaAnswer(), 1500);
    },
    stopLocalLiveMedia(report = true) {
        if (this.mediaPeer) {
            this.mediaPeer.close();
        }

        const video = this.$refs.liveMediaVideo;
        if (video) {
            video.srcObject = null;
        }

        if (report && this.mediaSessionId) {
            this.wire.stopLiveMedia(this.mediaSessionId);
        }

        this.mediaPeer = null;
        this.mediaToken = null;
        this.mediaSessionId = null;
        this.mediaEmployeeName = null;
        this.mediaStatus = 'Idle';
    },
    });
</script>

<div
    class="w-full"
    x-data="(window.wfhMonitoringAdmin || window.__legacyWfhMonitoringAdmin)($wire)"
    x-init="typeof initFromServer === 'function' ? initFromServer($el) : init()"
    data-wfh-monitoring-tabs
    data-active-wfh-tab="sessions"
    data-gps-selected-session-id='@json($gpsSelectedSession?->id ?? $selectedMonitoringSession?->id ?? null)'
>
    <template data-wfh-gps-trail>@json($gpsLocationTrail)</template>
    <div class="flex justify-center w-full">
        <div class="w-full overflow-hidden rounded-2xl bg-white p-3 shadow dark:bg-gray-800 sm:p-6">
            <div class="flex flex-col gap-4 border-b border-slate-200 pb-5 dark:border-slate-700 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-blue-500 dark:text-blue-300">Browser-based WFH monitor</p>
                    <h1 class="mt-2 text-2xl font-bold text-slate-900 dark:text-white">WFH Monitoring</h1>
                    <p class="mt-1 max-w-3xl text-sm text-slate-500 dark:text-slate-400">
                        Simplified into sessions, screens, GPS, and settings so tab changes stay fast and the admin view stays responsive.
                    </p>
                </div>

                <div class="w-full lg:w-96">
                    <label for="monitoringSearch" class="mb-1 block text-sm font-medium text-gray-700 dark:text-slate-400">Search employee</label>
                    <input type="text" id="monitoringSearch" wire:model.live="search"
                        class="block w-full rounded-md border border-gray-400 px-3 py-2 text-sm shadow-sm hover:bg-gray-100 dark:border-slate-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-slate-700"
                        placeholder="Enter employee name or ID">
                </div>
            </div>

            <div class="mt-5 grid gap-3 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-7">
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-700 dark:bg-slate-900">
                    <p class="text-xs font-semibold uppercase text-slate-600 dark:text-slate-300">Total</p>
                    <p class="mt-2 text-3xl font-bold text-slate-800 dark:text-slate-100">{{ $monitoringStats['total'] }}</p>
                </div>
                <div class="rounded-xl border border-emerald-100 bg-emerald-50 p-4 dark:border-emerald-500/20 dark:bg-emerald-500/10">
                    <p class="text-xs font-semibold uppercase text-emerald-700 dark:text-emerald-300">Active</p>
                    <p class="mt-2 text-3xl font-bold text-emerald-700 dark:text-emerald-200">{{ $monitoringStats['active'] }}</p>
                </div>
                <div class="rounded-xl border border-amber-100 bg-amber-50 p-4 dark:border-amber-500/20 dark:bg-amber-500/10">
                    <p class="text-xs font-semibold uppercase text-amber-700 dark:text-amber-300">AFK</p>
                    <p class="mt-2 text-3xl font-bold text-amber-700 dark:text-amber-200">{{ $monitoringStats['afk'] }}</p>
                </div>
                <div class="rounded-xl border border-rose-100 bg-rose-50 p-4 dark:border-rose-500/20 dark:bg-rose-500/10">
                    <p class="text-xs font-semibold uppercase text-rose-700 dark:text-rose-300">Offline</p>
                    <p class="mt-2 text-3xl font-bold text-rose-700 dark:text-rose-200">{{ $monitoringStats['offline'] }}</p>
                </div>
                <div class="rounded-xl border border-sky-100 bg-sky-50 p-4 dark:border-sky-500/20 dark:bg-sky-500/10">
                    <p class="text-xs font-semibold uppercase text-sky-700 dark:text-sky-300">On Break</p>
                    <p class="mt-2 text-3xl font-bold text-sky-700 dark:text-sky-200">{{ $monitoringStats['on_break'] }}</p>
                </div>
                <div class="rounded-xl border border-orange-100 bg-orange-50 p-4 dark:border-orange-500/20 dark:bg-orange-500/10">
                    <p class="text-xs font-semibold uppercase text-orange-700 dark:text-orange-300">Screen Off</p>
                    <p class="mt-2 text-3xl font-bold text-orange-700 dark:text-orange-200">{{ $monitoringStats['screen_off'] }}</p>
                </div>
                <div class="rounded-xl border border-red-100 bg-red-50 p-4 dark:border-red-500/20 dark:bg-red-500/10">
                    <p class="text-xs font-semibold uppercase text-red-700 dark:text-red-300">Geofence</p>
                    <p class="mt-2 text-3xl font-bold text-red-700 dark:text-red-200">{{ $monitoringStats['geofence_alerts'] }}</p>
                </div>
            </div>

            <div class="mt-5 rounded-2xl border border-slate-200 bg-slate-50 p-1.5 dark:border-slate-700 dark:bg-slate-900">
                <div class="flex gap-1.5 overflow-x-auto">
                    @foreach ([
                        'sessions' => 'Sessions',
                        'screens' => 'Screens',
                        'gps' => 'GPS',
                        'settings' => 'Settings',
                    ] as $key => $label)
                        <button
                            type="button"
                            data-wfh-tab-button="{{ $key }}"
                            aria-selected="{{ $key === 'sessions' ? 'true' : 'false' }}"
                            @click="openTab('{{ $key }}')"
                            :class="tab === '{{ $key }}'
                                ? 'bg-white text-blue-700 shadow-sm ring-1 ring-blue-100 dark:bg-blue-500/15 dark:text-blue-200 dark:ring-blue-500/20'
                                : 'text-slate-500 hover:bg-white/80 hover:text-slate-900 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-white'"
                            class="relative whitespace-nowrap rounded-xl px-4 py-2.5 text-sm font-semibold transition"
                        >
                            {{ $label }}
                            <span
                                x-show="tab === '{{ $key }}'"
                                x-cloak
                                class="absolute inset-x-4 bottom-1 mx-auto h-0.5 rounded-full bg-blue-600 dark:bg-blue-300"
                            ></span>
                        </button>
                    @endforeach
                </div>
            </div>

            <div class="mt-5" x-show="tab === 'sessions'" data-wfh-tab-panel="sessions">
                <div class="overflow-hidden rounded-xl border border-slate-200 dark:border-slate-700">
                    <div class="border-b border-slate-200 bg-slate-50 px-4 py-3 dark:border-slate-700 dark:bg-slate-900">
                        <h2 class="text-sm font-bold uppercase text-slate-700 dark:text-slate-200">Today's monitored WFH sessions</h2>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-full">
                            <thead class="bg-gray-200 dark:bg-gray-700">
                                <tr class="whitespace-nowrap">
                                    <th class="px-5 py-3 text-left text-sm font-medium uppercase">Employee</th>
                                    <th class="px-5 py-3 text-center text-sm font-medium uppercase">Status</th>
                                    <th class="px-5 py-3 text-left text-sm font-medium uppercase">Work/Session</th>
                                    <th class="px-5 py-3 text-center text-sm font-medium uppercase">Last Ping</th>
                                    <th class="px-5 py-3 text-center text-sm font-medium uppercase">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-neutral-200 dark:divide-gray-600">
                                @forelse ($monitoringSessions as $session)
                                    @php $state = $this->monitoringStateFor($session); @endphp
                                    <tr class="text-neutral-800 dark:text-neutral-200">
                                        <td class="px-5 py-4 text-left text-sm font-medium whitespace-nowrap">
                                            {{ $this->employeeDisplayName($session->user) }}<br>
                                            <span class="text-xs text-slate-500 dark:text-slate-400">{{ $session->user->emp_code ?? 'No employee no.' }}</span>
                                        </td>
                                        <td class="px-5 py-4 text-center text-sm font-medium whitespace-nowrap">
                                            <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $this->monitoringStateClass($state) }}">{{ $state }}</span>
                                        </td>
                                        <td class="px-5 py-4 text-sm font-medium">
                                            <div>{{ $session->work_status }}</div>
                                            <div class="text-xs text-slate-500 dark:text-slate-400">
                                                Started {{ optional($session->started_at)->format('h:i A') }} - {{ $session->total_monitored_minutes ?? 0 }} mins monitored
                                            </div>
                                        </td>
                                        <td class="px-5 py-4 text-center text-sm font-medium whitespace-nowrap">
                                            {{ optional($session->last_activity_at)->diffForHumans() ?? 'No activity' }}
                                        </td>
                                        <td class="px-5 py-4 text-center text-sm font-medium whitespace-nowrap">
                                            <button
                                                type="button"
                                                @click="startLiveSnapshots({{ $session->id }}, @js($this->employeeDisplayName($session->user)))"
                                                class="rounded-md px-3 py-1.5 text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-500/10"
                                                title="Start shared-hosting live snapshots"
                                            >
                                                <i class="bi bi-display"></i>
                                                <span class="ml-1 text-xs">Live</span>
                                            </button>
                                            <button
                                                type="button"
                                                wire:click="requestScreenSnapshot({{ $session->id }})"
                                                class="rounded-md px-3 py-1.5 text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-500/10"
                                                title="Request screen snapshot"
                                            >
                                                <i class="bi bi-camera-fill"></i>
                                                <span class="ml-1 text-xs">Shot</span>
                                            </button>
                                            <button
                                                type="button"
                                                @click="tab = 'screens'; startLiveMedia({{ $session->id }}, @js($this->employeeDisplayName($session->user)))"
                                                class="rounded-md px-3 py-1.5 text-purple-600 hover:bg-purple-50 dark:hover:bg-purple-500/10"
                                                title="Request camera and microphone"
                                            >
                                                <i class="bi bi-camera-video-fill"></i>
                                                <span class="ml-1 text-xs">Cam/Mic</span>
                                            </button>
                                            <button
                                                type="button"
                                                @click="selectGpsSession({{ $session->id }})"
                                                class="rounded-md px-3 py-1.5 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-500/10"
                                                title="Open GPS location"
                                            >
                                                <i class="bi bi-geo-alt-fill"></i>
                                                <span class="ml-1 text-xs">GPS</span>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="p-5 text-center text-gray-500 dark:text-gray-300">No WFH monitoring sessions for today.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="mt-5 grid gap-5 xl:grid-cols-[22rem_1fr]" x-show="tab === 'screens'" data-wfh-tab-panel="screens" hidden>
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-700 dark:bg-slate-900">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-xs font-semibold uppercase text-slate-500 dark:text-slate-400">Choose employee</p>
                            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Use live snapshots for shared hosting, or request camera and microphone when needed.</p>
                        </div>
                    </div>

                    <div class="mt-3 max-h-[32rem] space-y-2 overflow-y-auto pr-1">
                        @forelse ($monitoringSessions as $session)
                            @php $state = $this->monitoringStateFor($session); @endphp
                            <div class="rounded-xl border border-slate-200 bg-white p-3 transition hover:border-blue-300 hover:bg-blue-50 dark:border-slate-700 dark:bg-gray-800 dark:hover:border-blue-500/50 dark:hover:bg-blue-500/10">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="text-sm font-bold text-slate-900 dark:text-white">{{ $this->employeeDisplayName($session->user) }}</p>
                                        <p class="text-xs text-slate-500 dark:text-slate-400">{{ $session->user->emp_code ?? 'No employee no.' }}</p>
                                    </div>
                                    <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $this->monitoringStateClass($state) }}">{{ $state }}</span>
                                </div>
                                <div class="mt-2 flex items-center justify-between gap-3 text-xs text-slate-500 dark:text-slate-400">
                                    <span>{{ $session->screen_share_active ? 'Screen sharing active' : 'Screen not shared' }}</span>
                                    <span>{{ optional($session->last_activity_at)->diffForHumans() ?? 'No activity' }}</span>
                                </div>
                                <div class="mt-3 flex flex-wrap gap-2">
                                    <button
                                        type="button"
                                        @click="startLiveSnapshots({{ $session->id }}, @js($this->employeeDisplayName($session->user)))"
                                        class="rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300"
                                    >
                                        Live snapshots
                                    </button>
                                    <button
                                        type="button"
                                        wire:click="requestScreenSnapshot({{ $session->id }})"
                                        class="rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-700 dark:bg-amber-500/10 dark:text-amber-300"
                                    >
                                        Snapshot
                                    </button>
                                    <button
                                        type="button"
                                        @click="startLiveMedia({{ $session->id }}, @js($this->employeeDisplayName($session->user)))"
                                        class="rounded-full bg-purple-100 px-2.5 py-1 text-xs font-semibold text-purple-700 dark:bg-purple-500/10 dark:text-purple-300"
                                    >
                                        Request cam/mic
                                    </button>
                                    <button
                                        type="button"
                                        @click="selectGpsSession({{ $session->id }})"
                                        class="rounded-full bg-blue-100 px-2.5 py-1 text-xs font-semibold text-blue-700 dark:bg-blue-500/10 dark:text-blue-300"
                                    >
                                        Focus GPS
                                    </button>
                                </div>
                            </div>
                        @empty
                            <div class="rounded-lg border border-dashed border-slate-300 p-4 text-center text-sm text-slate-500 dark:border-slate-700 dark:text-slate-400">
                                No active WFH sessions to view.
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="rounded-xl border border-slate-200 bg-slate-950 p-4 dark:border-slate-700">
                    <div class="mb-4 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase text-emerald-300">Shared-hosting screen view</p>
                            <p class="mt-1 text-lg font-bold text-white" x-text="snapshotEmployeeName || liveEmployeeName || 'Select an employee to view'"></p>
                            <p class="text-sm text-slate-300" x-text="snapshotStatus || liveStatus"></p>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <button type="button" x-show="snapshotSessionId" @click="refreshLiveSnapshot()" class="rounded-md bg-emerald-500 px-3 py-2 text-xs font-semibold text-slate-950 hover:bg-emerald-400">
                                Refresh frame
                            </button>
                            <button type="button" x-show="snapshotSessionId" @click="stopLiveSnapshots()" class="rounded-md bg-slate-700 px-3 py-2 text-xs font-semibold text-white hover:bg-slate-600">
                                Stop snapshots
                            </button>
                            <button type="button" @click="stopLocalLiveScreen()" class="rounded-md bg-rose-600 px-3 py-2 text-xs font-semibold text-white hover:bg-rose-700">
                                Stop peer video
                            </button>
                            <button type="button" @click="stopLocalLiveMedia()" class="rounded-md bg-slate-700 px-3 py-2 text-xs font-semibold text-white hover:bg-slate-600">
                                Stop cam/mic
                            </button>
                        </div>
                    </div>

                    <div class="relative overflow-hidden rounded-xl border border-slate-800 bg-black">
                        <div class="relative aspect-video max-h-[68vh] w-full bg-black">
                            <img
                                x-show="snapshotUrl"
                                :src="snapshotUrl"
                                alt="Latest employee screen snapshot"
                                class="h-full w-full object-contain"
                            >
                            <video x-show="!snapshotUrl" x-ref="liveScreenVideo" autoplay playsinline controls muted class="h-full w-full bg-black object-contain"></video>
                            <div x-show="!snapshotUrl && !livePeer" class="absolute inset-0 flex items-center justify-center bg-slate-950">
                                <div class="max-w-sm px-6 text-center">
                                    <p class="text-sm font-semibold text-white">No screen frame yet</p>
                                    <p class="mt-2 text-xs leading-5 text-slate-400">Keep the employee WFH Attendance page open in another browser, profile, or incognito session. The first frame appears after that page captures and uploads the screen.</p>
                                </div>
                            </div>
                            <div x-show="snapshotUrl" class="absolute bottom-3 left-3 rounded-md bg-black/70 px-3 py-2 text-xs text-white">
                                <span x-text="snapshotCapturedAt || 'Latest frame'"></span>
                                <span x-show="snapshotType"> · </span>
                                <span x-show="snapshotType" x-text="snapshotType"></span>
                            </div>
                        </div>

                        <div
                            x-show="mediaPeer || mediaToken"
                            x-cloak
                            wire:ignore
                            class="absolute bottom-4 left-4 w-[min(34vw,18rem)] overflow-hidden rounded-2xl border border-white/20 bg-slate-950/95 shadow-2xl"
                        >
                            <div class="flex items-center justify-between gap-2 border-b border-white/10 px-3 py-2 text-[10px] font-bold uppercase tracking-[0.18em] text-slate-200">
                                <span>Camera</span>
                                <span x-text="mediaStatus"></span>
                            </div>
                            <div class="relative bg-black">
                                <video x-ref="liveMediaVideo" autoplay playsinline controls class="aspect-video w-full bg-black object-cover"></video>
                            </div>
                        </div>
                    </div>

                    <p class="mt-3 text-xs text-slate-400">
                        Live snapshots use regular HTTPS uploads, so they work on shared hosting. Peer video is still available in code, but snapshots are the reliable default.
                    </p>

                    <div class="mt-5 rounded-xl border border-slate-800 bg-slate-900/80 p-4">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <p class="text-xs font-semibold uppercase text-slate-400">Screen snapshots</p>
                                <p class="mt-1 text-sm text-slate-300">
                                    {{ $selectedMonitoringSession ? $this->employeeDisplayName($selectedMonitoringSession->user) : 'Select an employee to review snapshots' }}
                                </p>
                            </div>
                            @if ($selectedMonitoringSession)
                                <button
                                    type="button"
                                    wire:click="requestScreenSnapshot({{ $selectedMonitoringSession->id }})"
                                    class="rounded-md bg-amber-500 px-3 py-2 text-xs font-semibold text-slate-950 hover:bg-amber-400"
                                >
                                    Request snapshot
                                </button>
                            @endif
                        </div>

                        <div class="mt-4 grid gap-3 sm:grid-cols-2 2xl:grid-cols-4">
                            @forelse ($selectedMonitoringSession?->screenshots ?? [] as $screenshot)
                                @php $screenshotUrl = '/storage/' . ltrim($screenshot->path, '/'); @endphp
                                <a href="{{ $screenshotUrl }}" target="_blank" class="group overflow-hidden rounded-lg border border-slate-700 bg-black">
                                    <img src="{{ $screenshotUrl }}" alt="WFH screen snapshot" class="aspect-video w-full object-cover opacity-90 transition group-hover:opacity-100">
                                    <div class="border-t border-slate-800 px-3 py-2">
                                        <p class="text-xs font-semibold text-slate-200">{{ optional($screenshot->captured_at)->format('M d, h:i A') }}</p>
                                        <p class="mt-0.5 text-[11px] uppercase tracking-wide text-slate-500">{{ str_replace('_', ' ', $screenshot->capture_type) }}</p>
                                    </div>
                                </a>
                            @empty
                                <div class="col-span-full rounded-lg border border-dashed border-slate-700 p-4 text-center text-sm text-slate-400">
                                    No snapshots captured yet. Request one while the employee screen share is active.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-5 grid gap-5 xl:grid-cols-[20rem_1fr]" x-show="tab === 'gps'" data-wfh-tab-panel="gps" hidden>
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-700 dark:bg-slate-900">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="text-xs font-semibold uppercase text-slate-500 dark:text-slate-400">Employee location</p>
                            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Pick an employee to view where they are and where they were recently.</p>
                        </div>
                        @php $gpsEmployeeCount = $monitoringSessions->count(); @endphp
                        <span class="rounded-full bg-blue-100 px-2.5 py-1 text-[11px] font-bold text-blue-700 dark:bg-blue-500/10 dark:text-blue-300">
                            {{ $gpsEmployeeCount }} {{ \Illuminate\Support\Str::plural('employee', $gpsEmployeeCount) }}
                        </span>
                    </div>

                    <div class="mt-4 max-h-[32rem] space-y-2 overflow-y-auto pr-1">
                        @forelse ($monitoringSessions as $session)
                            @php $state = $this->monitoringStateFor($session); @endphp
                            <button
                                type="button"
                                @click="selectGpsSession({{ $session->id }})"
                                class="w-full rounded-xl border px-3 py-3 text-left transition"
                                :class="gpsSelectedSessionId == {{ $session->id }} ? 'border-blue-300 bg-blue-50 dark:border-blue-500/40 dark:bg-blue-500/10' : 'border-slate-200 bg-white hover:border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:hover:border-slate-600'"
                            >
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="text-sm font-bold text-slate-900 dark:text-white">{{ $this->employeeDisplayName($session->user) }}</p>
                                        <p class="text-xs text-slate-500 dark:text-slate-400">{{ $session->user->emp_code ?? 'No employee no.' }}</p>
                                    </div>
                                    <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $this->monitoringStateClass($state) }}">{{ $state }}</span>
                                </div>
                                <div class="mt-2 text-xs text-slate-500 dark:text-slate-400">
                                    @php $sessionGeofenceLabel = $this->geofenceStatusLabel($session->geofence_status); @endphp
                                    @if ($sessionGeofenceLabel)
                                        <span>{{ $sessionGeofenceLabel }}</span>
                                        <span class="mx-1">•</span>
                                    @endif
                                    <span>{{ optional($session->last_activity_at)->diffForHumans() ?? 'No activity' }}</span>
                                </div>
                                <div class="mt-1 text-[11px] uppercase tracking-wide text-slate-400">
                                    {{ $session->field_location_label ?? 'Employee location' }}
                                </div>
                            </button>
                        @empty
                            <div class="rounded-lg border border-dashed border-slate-300 p-4 text-center text-sm text-slate-500 dark:border-slate-700 dark:text-slate-400">
                                No employees available for GPS review.
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="space-y-5">
                    <div class="rounded-xl border border-slate-200 bg-white p-4 dark:border-slate-700 dark:bg-gray-800">
                        <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                            <div>
                                <h3 class="text-sm font-bold uppercase text-slate-700 dark:text-slate-200">Current location and trail</h3>
                                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                                    {{ $gpsSelectedSession ? $this->employeeDisplayName($gpsSelectedSession->user) : 'Select an employee to review' }}
                                </p>
                            </div>
                        </div>

                        <div class="mt-4 grid gap-4 lg:grid-cols-[1.05fr_0.95fr]">
                            <div class="overflow-hidden rounded-xl border border-slate-200 dark:border-slate-700">
                                <div x-ref="gpsMap" wire:ignore class="h-[30rem] w-full bg-slate-100 dark:bg-slate-800"></div>
                                @if ($gpsSelectedSession && is_numeric($gpsSelectedSession->last_latitude) && is_numeric($gpsSelectedSession->last_longitude))
                                    <div class="flex flex-wrap items-center justify-between gap-2 border-t border-slate-200 px-4 py-3 text-xs text-slate-500 dark:border-slate-700 dark:text-slate-400">
                                        <span class="font-medium text-slate-600 dark:text-slate-300">Trail updates automatically as new pings arrive.</span>
                                    </div>
                                @endif
                            </div>

                            <div class="rounded-xl border border-slate-200 bg-slate-50 dark:border-slate-700 dark:bg-slate-900">
                                <div class="border-b border-slate-200 bg-white px-4 py-3 dark:border-slate-700 dark:bg-slate-800">
                                    <h3 class="text-sm font-bold uppercase text-slate-700 dark:text-slate-200">Recent location history</h3>
                                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Shows the latest places recorded for the selected employee.</p>
                                </div>
                                <div class="max-h-[30rem] overflow-y-auto">
                                    @forelse ($gpsSelectedSession?->locationPings ?? [] as $ping)
                                        <button
                                            type="button"
                                            @click="focusGpsPoint({{ $loop->index }})"
                                            class="block w-full border-b border-slate-200 p-3 text-left text-sm last:border-b-0 hover:bg-blue-50 dark:border-slate-700 dark:hover:bg-blue-500/10"
                                        >
                                            <div class="flex items-center justify-between gap-2">
                                                <span class="font-semibold text-slate-800 dark:text-slate-100">{{ optional($ping->occurred_at)->format('h:i A') }}</span>
                                                <span class="text-[11px] font-semibold uppercase tracking-wide text-blue-600 dark:text-blue-300">View on map</span>
                                            </div>
                                            <div class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                                                {{ $this->displayLocationLabel($ping->latitude, $ping->longitude, $gpsSelectedSession->field_location_label ?? null) ?? ($gpsSelectedSession->field_location_label ?? '') }}
                                                @if ($ping->accuracy)
                                                    · Accuracy {{ number_format($ping->accuracy, 1) }}m
                                                @endif
                                            </div>
                                            <div class="mt-1 text-[11px] uppercase tracking-wide text-slate-400">
                                                Lat {{ number_format($ping->latitude, 6) }}, Lng {{ number_format($ping->longitude, 6) }}
                                            </div>
                                        </button>
                                    @empty
                                        <div class="p-4 text-center text-sm text-slate-500 dark:text-slate-400">
                                            No location pings found for the selected employee.
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-xl border border-slate-200 bg-white dark:border-slate-700 dark:bg-gray-800">
                        <div class="border-b border-slate-200 bg-slate-50 px-4 py-3 dark:border-slate-700 dark:bg-slate-900">
                            <h3 class="text-sm font-bold uppercase text-slate-700 dark:text-slate-200">Current location</h3>
                        </div>
                        <div class="p-4">
                            @if ($gpsSelectedSession)
                                <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $this->employeeDisplayName($gpsSelectedSession->user) }}</p>
                                @php $gpsSummaryLabel = $this->geofenceStatusLabel($gpsSelectedSession->geofence_status); @endphp
                                @if ($gpsSummaryLabel)
                                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ $gpsSummaryLabel }}</p>
                                @endif
                                <p class="mt-2 text-base font-semibold text-slate-800 dark:text-slate-100">
                                    {{ $gpsCurrentLocationLabel ?: ($gpsSelectedSession->field_location_label ?? '') }}
                                </p>
                                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                                    {{ $gpsSelectedSession->last_latitude && $gpsSelectedSession->last_longitude ? 'Location recorded on the map above.' : '' }}
                                </p>
                            @else
                                <p class="text-sm text-slate-500 dark:text-slate-400">Select an employee to view their location summary.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-5" x-show="tab === 'settings'" data-wfh-tab-panel="settings" hidden>
                @include('livewire.admin.partials.wfh-url-rules')
            </div>
        </div>
    </div>
</div>
