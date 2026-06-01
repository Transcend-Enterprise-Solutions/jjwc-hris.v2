<div x-data="{
    open: false,
    showWFHLocHistory: '{{ request()->query('showWFHLocHistory', false) }}',
    viewWFHLocHistory: false,
    lastMonitoringPing: 0,
    screenStream: null,
    screenVideo: null,
    screenshotTimer: null,
    liveSnapshotTimer: null,
    liveSnapshotToken: null,
    liveSnapshotUploading: false,
    liveScreenPeer: null,
    liveScreenToken: null,
    liveScreenAnswering: false,
    liveScreenRequestPending: false,
    liveScreenRequestToken: null,
    liveScreenNeedsShareReportedToken: null,
    liveMediaPeer: null,
    liveMediaToken: null,
    liveMediaStream: null,
    liveMediaMicOn: false,
    liveMediaCameraOn: false,
    monitoringFloatOpen: true,
    monitoringPopout: null,
    monitoringPopoutTimer: null,
    monitoringPopoutBlocked: false,
    monitoringPopoutAttempted: false,
    screenShareActive: @js((bool) $monitoringScreenShareActive),
    screenSurfaceWarning: null,
    screenResumeRequired: false,
    afkPromptOpen: false,
    afkTimer: null,
    clockTick: Date.now(),
    monitoringStartedAt: @js($monitoringSessionStartedAt) ? new Date(@js($monitoringSessionStartedAt)).getTime() : Date.now(),
    onlineSeconds: @js((int) $monitoringOnlineSeconds),
    onlineTickStartedAt: Date.now(),
    metricsWindowStartedAt: Date.now(),
    lastInteractionAt: Date.now(),
    keyCount: 0,
    mouseCount: 0,
    clickCount: 0,
    touchCount: 0,
    activitySyncTimer: null,
    lastActivitySyncAt: 0,
    screenshotIntervalMinutes: @js($screenshotIntervalMinutes),
    locationIntervalMinutes: @js($locationIntervalMinutes),
    afkThresholdMinutes: @js($afkThresholdMinutes),
    rtcIceServers: @js(config('wfh_monitoring.ice_servers')),
    lastLocationReadAt: 0,
    lastKnownPosition: {},
    punchSubmitting: false,
    async readPosition() {
        if (!navigator.geolocation) {
            return {};
        }

        return await new Promise((resolve) => {
            navigator.geolocation.getCurrentPosition(
                (position) => resolve({
                    latitude: position.coords.latitude,
                    longitude: position.coords.longitude,
                    accuracy: position.coords.accuracy,
                }),
                () => resolve({}),
                { enableHighAccuracy: true, timeout: 10000, maximumAge: 30000 }
            );
        });
    },
    async syncMonitoring(force = false, options = {}) {
        const now = Date.now();

        if (!force && now - this.lastMonitoringPing < 10000) {
            return;
        }

        this.lastMonitoringPing = now;
        const screenShareLive = this.isScreenShareLive();
        this.screenShareActive = screenShareLive;
        const position = options.skipLocation ? this.lastKnownPosition : await this.getMonitoringPosition();
        const response = await $wire.recordMonitoringHeartbeat(
            document.title,
            window.location.href,
            document.visibilityState,
            position.latitude ?? null,
            position.longitude ?? null,
            position.accuracy ?? null,
            screenShareLive,
            window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true,
            navigator.platform ?? null,
            navigator.userAgent ?? null,
            this.flushActivityMetrics()
        );

        if (response?.afkThresholdMinutes) {
            this.afkThresholdMinutes = response.afkThresholdMinutes;
        }

        if (response?.screenshotIntervalMinutes && response.screenshotIntervalMinutes !== this.screenshotIntervalMinutes) {
            this.screenshotIntervalMinutes = response.screenshotIntervalMinutes;
            this.scheduleScreenshots();
        }

        if (response?.locationIntervalMinutes) {
            this.locationIntervalMinutes = response.locationIntervalMinutes;
        }

        if (response?.captureScreen) {
            this.captureScreenSnapshot('on_demand');
        }

        this.syncLiveSnapshotCapture(response?.liveSnapshots || null);

        if (response?.sessionStartedAt) {
            this.monitoringStartedAt = new Date(response.sessionStartedAt).getTime();
        }

        if (typeof response?.onlineSeconds === 'number') {
            this.onlineSeconds = response.onlineSeconds;
            this.onlineTickStartedAt = Date.now();
        }

        if (typeof response?.screenShareActive === 'boolean') {
            this.screenShareActive = this.isScreenShareLive();

            if (response.screenShareActive && !this.screenShareActive) {
                this.screenResumeRequired = true;
            }
        }
    },
    async getMonitoringPosition() {
        const now = Date.now();
        const intervalMs = Math.max(1, this.locationIntervalMinutes) * 60 * 1000;

        if (this.lastLocationReadAt && now - this.lastLocationReadAt < intervalMs) {
            return this.lastKnownPosition;
        }

        const position = await this.readPosition();

        if (position.latitude && position.longitude) {
            this.lastKnownPosition = position;
            this.lastLocationReadAt = now;
        }

        return this.lastKnownPosition;
    },
    flushActivityMetrics() {
        const now = Date.now();
        const elapsedSeconds = Math.max(1, Math.round((now - this.metricsWindowStartedAt) / 1000));
        const idleSeconds = Math.max(0, Math.round((now - this.lastInteractionAt) / 1000));
        const activeSeconds = Math.max(0, elapsedSeconds - Math.min(elapsedSeconds, idleSeconds));
        const metrics = {
            activeSeconds,
            idleSeconds: Math.min(elapsedSeconds, idleSeconds),
            keystrokes: this.keyCount,
            mouseMoves: this.mouseCount,
            clicks: this.clickCount,
            touches: this.touchCount,
        };

        this.metricsWindowStartedAt = now;
        this.keyCount = 0;
        this.mouseCount = 0;
        this.clickCount = 0;
        this.touchCount = 0;

        return metrics;
    },
    markActivity(type = 'mouse') {
        this.lastInteractionAt = Date.now();
        this.afkPromptOpen = false;

        if (type === 'key') this.keyCount++;
        if (type === 'mouse') this.mouseCount++;
        if (type === 'click') this.clickCount++;
        if (type === 'touch') this.touchCount++;

        this.resetAfkTimer();
        this.queueActivitySync();
    },
    queueActivitySync() {
        const now = Date.now();

        if (now - this.lastActivitySyncAt < 5000) {
            return;
        }

        if (this.activitySyncTimer) {
            clearTimeout(this.activitySyncTimer);
        }

        this.activitySyncTimer = setTimeout(() => {
            this.activitySyncTimer = null;
            this.lastActivitySyncAt = Date.now();
            this.syncMonitoring();
        }, 1200);
    },
    resetAfkTimer() {
        if (this.afkTimer) {
            clearTimeout(this.afkTimer);
        }

        this.afkTimer = setTimeout(() => {
            this.afkPromptOpen = true;
            $wire.recordMonitoringSignal('afk_prompt_shown', 'AFK prompt displayed to employee');
        }, Math.max(1, this.afkThresholdMinutes) * 60 * 1000);
    },
    respondToAfk(response) {
        this.afkPromptOpen = false;
        this.lastInteractionAt = Date.now();
        this.resetAfkTimer();
        $wire.respondToAfkPrompt(response);
    },
    monitoringRuntime() {
        window.jjwcWfhMonitorState = window.jjwcWfhMonitorState || {};

        return window.jjwcWfhMonitorState;
    },
    hasLiveScreenTrackFor(stream) {
        return !!stream?.getVideoTracks?.().some((track) => track.readyState === 'live');
    },
    runtimeScreenStream() {
        const runtime = this.monitoringRuntime();

        if (!this.hasLiveScreenTrackFor(runtime.screenStream)) {
            runtime.screenStream = null;
            runtime.screenShareActive = false;
            return null;
        }

        return runtime.screenStream;
    },
    hasLiveScreenTrack() {
        return this.hasLiveScreenTrackFor(this.screenStream);
    },
    isScreenShareLive() {
        const runtimeStream = this.runtimeScreenStream();

        if (!this.hasLiveScreenTrack() && runtimeStream) {
            this.screenStream = runtimeStream;
            this.attachScreenVideo();
        }

        return this.hasLiveScreenTrack();
    },
    attachScreenVideo() {
        if (!this.screenStream) {
            return;
        }

        this.screenVideo = document.createElement('video');
        this.screenVideo.muted = true;
        this.screenVideo.srcObject = this.screenStream;
        this.screenVideo.play().catch(() => {});
    },
    restoreScreenShareRuntime() {
        const runtime = this.monitoringRuntime();

        if (!runtime.screenStream) {
            return false;
        }

        this.screenStream = runtime.screenStream;

        if (!this.hasLiveScreenTrack()) {
            runtime.screenStream = null;
            this.screenStream = null;
            runtime.screenShareActive = false;
            return false;
        }

        this.screenShareActive = true;
        this.screenResumeRequired = false;
        this.attachScreenVideo();
        this.monitoringRuntime().screenShareActive = true;
        this.scheduleScreenshots();

        return true;
    },
    async startScreenShare() {
        if (!navigator.mediaDevices || !navigator.mediaDevices.getDisplayMedia) {
            this.screenSurfaceWarning = 'Screen sharing is not supported on this browser or device. For WFH Time In with monitored screen capture, use Chrome or Edge on a desktop/laptop. On mobile, the HRIS page remains responsive for viewing attendance and prompts.';
            $wire.recordMonitoringSignal('screen_share_unavailable', 'Screen sharing is not supported by this browser');
            return false;
        }

        if (this.isScreenShareLive()) {
            this.screenShareActive = true;
            this.screenResumeRequired = false;
            return true;
        }

        if (this.screenStream) {
            if (this.hasLiveScreenTrack()) {
                this.screenShareActive = true;
                this.screenResumeRequired = false;
                this.monitoringRuntime().screenStream = this.screenStream;
                this.monitoringRuntime().screenShareActive = true;
                this.updateMonitoringPopout();
                this.syncMonitoring(true, { skipLocation: true });
                return true;
            }

            this.screenStream.getTracks().forEach((track) => track.stop());
            this.screenStream = null;
            this.screenVideo = null;
            this.screenShareActive = false;
        }

        try {
            this.screenSurfaceWarning = null;
            this.screenStream = await navigator.mediaDevices.getDisplayMedia({
                video: {
                    displaySurface: 'monitor',
                    logicalSurface: true,
                    cursor: 'always',
                },
                audio: false,
                preferCurrentTab: false,
                selfBrowserSurface: 'exclude',
                monitorTypeSurfaces: 'include',
                surfaceSwitching: 'exclude',
            });

            const videoTrack = this.screenStream.getVideoTracks()[0];
            const displaySurface = videoTrack?.getSettings?.().displaySurface ?? null;

            if (displaySurface !== 'monitor') {
                this.screenSurfaceWarning = displaySurface
                    ? 'Please choose Entire Screen / full monitor sharing, not a single window or browser tab.'
                    : 'This browser did not confirm Entire Screen sharing. Please use Chrome or Edge and choose Entire Screen / full monitor.';
                this.screenStream.getTracks().forEach((track) => track.stop());
                this.screenStream = null;
                this.screenShareActive = false;
                this.monitoringRuntime().screenStream = null;
                this.monitoringRuntime().screenShareActive = false;
                $wire.recordMonitoringSignal('screen_share_wrong_surface', 'Employee did not share confirmed full monitor surface', { display_surface: displaySurface ?? 'unknown' });
                return false;
            }

            this.screenShareActive = true;
            this.screenResumeRequired = false;
            this.monitoringRuntime().screenStream = this.screenStream;
            this.monitoringRuntime().screenShareActive = true;
            this.attachScreenVideo();
            $wire.recordMonitoringSignal('screen_share_started', 'Employee granted full-screen share permission', { display_surface: displaySurface ?? 'unknown' });
            this.syncMonitoring(true, { skipLocation: true });
            this.captureScreenSnapshot('time_in');
            this.scheduleScreenshots();
            this.checkLiveScreenRequest();

            this.screenStream.getVideoTracks().forEach((track) => {
                track.addEventListener('ended', () => {
                    this.stopScreenShare(false);
                    $wire.recordMonitoringSignal('screen_share_stopped', 'Screen share was stopped by the employee', { reason: 'track_ended' });
                });
            });

            return true;
        } catch (error) {
            this.screenShareActive = false;
            this.screenResumeRequired = true;
            this.monitoringRuntime().screenStream = null;
            $wire.recordMonitoringSignal('screen_share_denied', 'Employee did not grant screen-share permission', { message: error?.message ?? 'Permission denied' });
            return false;
        }
    },
    scheduleScreenshots() {
        if (this.screenshotTimer) {
            clearInterval(this.screenshotTimer);
        }

        this.screenshotTimer = setInterval(
            () => this.captureScreenSnapshot('periodic'),
            Math.max(1, this.screenshotIntervalMinutes) * 60 * 1000
        );
    },
    syncLiveSnapshotCapture(request) {
        if (!request?.token || !this.screenShareActive) {
            if (this.liveSnapshotTimer && !request?.token) {
                clearInterval(this.liveSnapshotTimer);
                this.liveSnapshotTimer = null;
                this.liveSnapshotToken = null;
            }

            return;
        }

        if (this.liveSnapshotToken === request.token && this.liveSnapshotTimer) {
            return;
        }

        if (this.liveSnapshotTimer) {
            clearInterval(this.liveSnapshotTimer);
        }

        this.liveSnapshotToken = request.token;
        const intervalMs = Math.max(3, Number(request.intervalSeconds || 5)) * 1000;
        this.captureScreenSnapshot('live_snapshot');
        this.liveSnapshotTimer = setInterval(() => this.captureScreenSnapshot('live_snapshot'), intervalMs);
    },
    async checkLiveSnapshotRequest() {
        const request = await $wire.getLiveSnapshotRequest();
        this.syncLiveSnapshotCapture(request);
    },
    elapsedLabel() {
        const totalSeconds = Math.max(0, Math.floor((this.clockTick - this.monitoringStartedAt) / 1000));
        return this.formatDuration(totalSeconds);
    },
    onlineElapsedLabel() {
        const liveSeconds = navigator.onLine
            ? Math.floor((this.clockTick - this.onlineTickStartedAt) / 1000)
            : 0;

        return this.formatDuration(this.onlineSeconds + Math.max(0, liveSeconds));
    },
    formatDuration(totalSeconds) {
        const hours = String(Math.floor(totalSeconds / 3600)).padStart(2, '0');
        const minutes = String(Math.floor((totalSeconds % 3600) / 60)).padStart(2, '0');
        const seconds = String(totalSeconds % 60).padStart(2, '0');

        return `${hours}:${minutes}:${seconds}`;
    },
    monitoringStatusLabel() {
        if (this.isScreenShareLive()) return 'Monitoring active';
        if (this.screenResumeRequired) return 'Needs screen share';
        return 'Waiting for screen share';
    },
    monitoringStatusClass() {
        if (this.isScreenShareLive()) return 'bg-emerald-100 text-emerald-700';
        if (this.screenResumeRequired) return 'bg-amber-100 text-amber-700';
        return 'bg-rose-100 text-rose-700';
    },
    reconcileScreenShareState() {
        if (!this.screenStream) {
            return this.screenShareActive;
        }

        const hasLiveScreenTrack = this.hasLiveScreenTrack();

        if (!hasLiveScreenTrack) {
            this.screenShareActive = false;
            this.monitoringRuntime().screenStream = null;
            this.monitoringRuntime().screenShareActive = false;
        }

        return hasLiveScreenTrack;
    },
    monitoringPopoutMarkup() {
        return [
            '\x3Cdiv id=&quot;monitor&quot; style=&quot;font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, sans-serif; min-height: 100vh; margin: 0; background: radial-gradient(circle at top, rgba(37,99,235,.22), transparent 35%), linear-gradient(160deg, #09111f 0%, #142447 48%, #1e3a8a 100%); color: #fff; padding: 16px; box-sizing: border-box;&quot;\x3E',
            '\x3Cdiv style=&quot;display:flex; align-items:flex-start; justify-content:space-between; gap:12px;&quot;\x3E',
            '\x3Cdiv\x3E\x3Cdiv style=&quot;font-size:11px; letter-spacing:.24em; text-transform:uppercase; color:#bfdbfe; font-weight:800;&quot;\x3EJJWC HRIS\x3C/div\x3E\x3Ch1 style=&quot;font-size:28px; line-height:1; margin:8px 0 0; font-weight:900;&quot;\x3EWFH Monitor\x3C/h1\x3E\x3C/div\x3E',
            '\x3Cdiv id=&quot;popoutStatus&quot; style=&quot;border-radius:999px; padding:8px 12px; background:#dcfce7; color:#166534; font-size:12px; font-weight:800; white-space:nowrap;&quot;\x3EMonitoring active\x3C/div\x3E',
            '\x3C/div\x3E',
            '\x3Cdiv style=&quot;margin-top:16px; border-radius:24px; background:rgba(255,255,255,.12); padding:18px; box-shadow:0 24px 64px rgba(0,0,0,.28); backdrop-filter: blur(18px);&quot;\x3E',
            '\x3Cdiv style=&quot;font-size:11px; color:#dbeafe; text-transform:uppercase; letter-spacing:.18em; font-weight:800;&quot;\x3EDaily online time\x3C/div\x3E',
            '\x3Cdiv id=&quot;popoutElapsed&quot; style=&quot;font-size:42px; font-weight:950; line-height:1; margin-top:8px; letter-spacing:-0.04em;&quot;\x3E00:00:00\x3C/div\x3E',
            '\x3Cdiv style=&quot;margin-top:12px; display:flex; justify-content:space-between; gap:10px; border-top:1px solid rgba(255,255,255,.14); padding-top:12px;&quot;\x3E\x3Cspan style=&quot;color:#bfdbfe; font-size:12px; font-weight:800; text-transform:uppercase; letter-spacing:.14em;&quot;\x3ECurrent session\x3C/span\x3E\x3Cspan id=&quot;popoutOnline&quot; style=&quot;font-family:monospace; font-size:18px; font-weight:900;&quot;\x3E00:00:00\x3C/span\x3E\x3C/div\x3E',
            '\x3Cdiv id=&quot;popoutUpdated&quot; style=&quot;font-size:12px; color:#cbd5e1; margin-top:6px;&quot;\x3ELast updated --:--:--\x3C/div\x3E',
            '\x3C/div\x3E',
            '\x3Cdiv style=&quot;display:grid; gap:10px; margin-top:12px;&quot;\x3E',
            '\x3Cdiv style=&quot;border-radius:18px; background:rgba(255,255,255,.09); padding:12px 14px;&quot;\x3E\x3Cdiv style=&quot;font-size:11px; color:#cbd5e1; text-transform:uppercase; font-weight:800;&quot;\x3EScreen\x3C/div\x3E\x3Cdiv id=&quot;popoutScreen&quot; style=&quot;margin-top:4px; font-size:18px; font-weight:900;&quot;\x3EOff\x3C/div\x3E\x3C/div\x3E',
            '\x3C/div\x3E',
            '\x3Cbutton id=&quot;popoutScreenToggle&quot; type=&quot;button&quot; style=&quot;width:100%; margin-top:12px; border:0; border-radius:16px; padding:14px; background:#2563eb; color:#fff; font-weight:950; cursor:pointer; box-shadow:0 18px 40px rgba(37,99,235,.35);&quot;\x3EStart Screen Sharing\x3C/button\x3E',
            '\x3Cp style=&quot;font-size:11px; line-height:1.5; color:#dbeafe; margin-top:12px;&quot;\x3EKeep the main HRIS tab open. The button changes automatically between sharing and canceling when the live screen stream is present.\x3C/p\x3E',
            '\x3C/div\x3E',
        ].join('');
    },
    updateMonitoringPopout() {
        if (!this.monitoringPopout || this.monitoringPopout.closed) {
            this.monitoringPopout = null;
            return;
        }

        this.reconcileScreenShareState();
        const doc = this.monitoringPopout.document;
        const setText = (id, text) => {
            const element = doc.getElementById(id);
            if (element) element.textContent = text;
        };
        const setButtonState = (id, active) => {
            const element = doc.getElementById(id);
            if (!element) return;
            element.style.background = active ? '#dcfce7' : '#fee2e2';
            element.style.color = active ? '#166534' : '#991b1b';
        };

        setText('popoutElapsed', this.onlineElapsedLabel());
        setText('popoutOnline', this.elapsedLabel());
        setText('popoutUpdated', `Last updated ${new Date().toLocaleTimeString()}`);
        setText('popoutStatus', this.monitoringStatusLabel());
        setText('popoutScreen', this.isScreenShareLive() ? 'Sharing' : 'Off');
        setText('popoutMedia', this.liveMediaStream ? 'Active' : 'Off');
        setText('popoutMic', this.liveMediaMicOn ? 'Mute Mic' : 'Unmute Mic');
        setText('popoutCamera', this.liveMediaCameraOn ? 'Turn Camera Off' : 'Turn Camera On');
        setButtonState('popoutMic', this.liveMediaMicOn);
        setButtonState('popoutCamera', this.liveMediaCameraOn);
        const shareToggle = doc.getElementById('popoutScreenToggle');
        if (shareToggle) {
            shareToggle.textContent = this.isScreenShareLive() ? 'Cancel Screen Sharing' : 'Start Screen Sharing';
            shareToggle.style.background = this.isScreenShareLive() ? '#dc2626' : '#2563eb';
            shareToggle.style.boxShadow = this.isScreenShareLive() ? '0 18px 40px rgba(220,38,38,.30)' : '0 18px 40px rgba(37,99,235,.35)';
        }

        const status = doc.getElementById('popoutStatus');
        if (status) {
            status.style.background = this.isScreenShareLive() ? '#dcfce7' : '#fef3c7';
            status.style.color = this.isScreenShareLive() ? '#166534' : '#92400e';
        }
    },
    async openMonitoringPopout(auto = false) {
            if (!this.isScreenShareLive() && !this.reconcileScreenShareState()) {
                return;
            }

        if (this.monitoringPopout && !this.monitoringPopout.closed) {
            this.updateMonitoringPopout();
            return;
        }

        const features = 'popup=yes,width=360,height=520,resizable=yes,scrollbars=no';
        let popout = null;

        try {
            popout = window.documentPictureInPicture
                ? await window.documentPictureInPicture.requestWindow({ width: 360, height: 520 })
                : window.open('', 'jjwc-wfh-monitor', features);
        } catch (error) {
            popout = null;
        }

        if (!popout) {
            this.monitoringPopoutBlocked = true;

            if (!this.monitoringPopoutAttempted) {
                this.monitoringPopoutAttempted = true;
                $wire.recordMonitoringSignal('monitoring_popout_blocked', 'Monitoring floating window was blocked by the browser');
            }

            return;
        }

        this.monitoringPopoutBlocked = false;
        this.monitoringPopoutAttempted = true;
        this.monitoringPopout = popout;
        popout.document.open();
        popout.document.write(this.monitoringPopoutMarkup());
        popout.document.close();
        popout.document.getElementById('popoutScreenToggle')?.addEventListener('click', () => {
            if (this.isScreenShareLive()) {
                this.stopScreenShare();
            } else {
                this.startScreenShare();
            }
        });
        this.updateMonitoringPopout();

        if (this.monitoringPopoutTimer) {
            clearInterval(this.monitoringPopoutTimer);
        }

        this.monitoringPopoutTimer = setInterval(() => this.updateMonitoringPopout(), 1000);
        $wire.recordMonitoringSignal(
            auto ? 'monitoring_popout_auto_opened' : 'monitoring_popout_opened',
            auto ? 'Monitoring floating window opened automatically' : 'Employee opened monitoring floating window'
        );
    },
    closeMonitoringPopout() {
        if (this.monitoringPopout && !this.monitoringPopout.closed) {
            this.monitoringPopout.close();
        }

        this.monitoringPopout = null;

        if (this.monitoringPopoutTimer) {
            clearInterval(this.monitoringPopoutTimer);
            this.monitoringPopoutTimer = null;
        }
    },
    captureScreenSnapshot(captureType = 'periodic') {
        if (!this.screenVideo || !this.screenShareActive || !this.screenVideo.videoWidth) {
            return;
        }

        if (captureType === 'live_snapshot' && this.liveSnapshotUploading) {
            return;
        }

        const canvas = document.createElement('canvas');
        const maxWidth = 960;
        const ratio = Math.min(1, maxWidth / this.screenVideo.videoWidth);
        canvas.width = Math.max(1, Math.round(this.screenVideo.videoWidth * ratio));
        canvas.height = Math.max(1, Math.round(this.screenVideo.videoHeight * ratio));
        canvas.getContext('2d').drawImage(this.screenVideo, 0, 0, canvas.width, canvas.height);
        const upload = $wire.recordScreenSnapshot(canvas.toDataURL('image/jpeg', 0.55), captureType);

        if (captureType === 'live_snapshot' && upload?.finally) {
            this.liveSnapshotUploading = true;
            upload.finally(() => {
                this.liveSnapshotUploading = false;
            });
        }
    },
    async waitForIceGathering(peer) {
        if (peer.iceGatheringState === 'complete') {
            return;
        }

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
    rtcPeerConfig() {
        return {
            iceServers: Array.isArray(this.rtcIceServers) && this.rtcIceServers.length
                ? this.rtcIceServers
                : [{ urls: 'stun:stun.l.google.com:19302' }],
            iceTransportPolicy: 'all',
        };
    },
    sanitizeRtcDescription(description) {
        if (!description?.sdp) {
            return description;
        }

        const lines = String(description.sdp).split(/\r?\n/);
        const blockedPayloads = new Set();

        lines.forEach((line) => {
            const payload = line.match(/^a=(?:rtpmap|fmtp):(\d+)/)?.[1];

            if (!payload) return;

            if (/^a=rtpmap:\d+\s+flexfec-03\/90000/i.test(line) || /^a=fmtp:\d+.*repair-window=/i.test(line)) {
                blockedPayloads.add(payload);
            }
        });

        if (!blockedPayloads.size) {
            return description;
        }

        const sanitized = lines
            .map((line) => {
                if (!line.startsWith('m=video ')) return line;

                const parts = line.trim().split(/\s+/);
                return parts.filter((part, index) => index < 3 || !blockedPayloads.has(part)).join(' ');
            })
            .filter((line) => {
                const payload = line.match(/^a=(?:rtpmap|rtcp-fb|fmtp):(\d+)/)?.[1];
                return !payload || !blockedPayloads.has(payload);
            })
            .join('\r\n');

        return {
            type: description.type,
            sdp: sanitized.endsWith('\r\n') ? sanitized : `${sanitized}\r\n`,
        };
    },
    async checkLiveScreenRequest() {
        if (this.liveScreenAnswering) {
            return;
        }

        if (!window.RTCPeerConnection) {
            return;
        }

        const request = await $wire.getLiveScreenRequest();

        if (!request?.token) {
            this.liveScreenRequestPending = false;
            this.liveScreenRequestToken = null;
            this.liveScreenNeedsShareReportedToken = null;
            return;
        }

        this.liveScreenRequestPending = Boolean(request?.offer);
        this.liveScreenRequestToken = request.token;

        if (!request?.token || !request?.offer || request.token === this.liveScreenToken) {
            return;
        }

        const screenStream = this.hasLiveScreenTrack() ? this.screenStream : this.runtimeScreenStream();

        if (!this.hasLiveScreenTrackFor(screenStream)) {
            this.screenShareActive = false;
            this.screenResumeRequired = true;
            this.liveScreenRequestPending = true;

            if (this.liveScreenNeedsShareReportedToken !== request.token) {
                this.liveScreenNeedsShareReportedToken = request.token;
                await $wire.markLiveScreenNeedsShare(request.token);
            }

            return;
        }

        this.liveScreenAnswering = true;
        let peer = null;

        try {
            this.liveScreenToken = request.token;

            if (this.liveScreenPeer) {
                this.liveScreenPeer.close();
            }

            this.screenStream = screenStream;
            peer = new RTCPeerConnection(this.rtcPeerConfig());

            screenStream.getTracks().forEach((track) => peer.addTrack(track, screenStream));
            await peer.setRemoteDescription(new RTCSessionDescription(this.sanitizeRtcDescription(request.offer)));
            const answer = await peer.createAnswer();
            await peer.setLocalDescription(answer);
            await this.waitForIceGathering(peer);
            await $wire.publishLiveAnswer(request.token, this.sanitizeRtcDescription(peer.localDescription.toJSON()));
            this.liveScreenPeer = peer;
            this.liveScreenRequestPending = false;
            this.liveScreenNeedsShareReportedToken = null;
            this.screenResumeRequired = false;
        } catch (error) {
            if (peer) {
                peer.close();
            }

            this.liveScreenPeer = null;
            this.liveScreenToken = null;
            this.liveScreenRequestPending = true;
            await $wire.failLiveScreenAnswer(request.token, error?.message ?? 'Live screen answer failed');
        } finally {
            this.liveScreenAnswering = false;
        }
    },
    async checkLiveMediaRequest() {
        if (!window.RTCPeerConnection || !navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            return;
        }

        const request = await $wire.getLiveMediaRequest();

        if (!request?.token || !request?.offer || request.token === this.liveMediaToken) {
            return;
        }

        this.liveMediaToken = request.token;

        if (this.liveMediaPeer) {
            this.liveMediaPeer.close();
        }

        try {
            if (!this.liveMediaStream && !await this.startLiveMediaPreview()) {
                await $wire.failLiveMediaAnswer(request.token, 'Employee did not grant camera and microphone permission.');
                return;
            }

            const peer = new RTCPeerConnection(this.rtcPeerConfig());

            this.liveMediaStream.getTracks().forEach((track) => peer.addTrack(track, this.liveMediaStream));
            await peer.setRemoteDescription(new RTCSessionDescription(this.sanitizeRtcDescription(request.offer)));
            const answer = await peer.createAnswer();
            await peer.setLocalDescription(answer);
            await this.waitForIceGathering(peer);
            await $wire.publishLiveMediaAnswer(request.token, this.sanitizeRtcDescription(peer.localDescription.toJSON()));
            this.liveMediaPeer = peer;
        } catch (error) {
            await $wire.failLiveMediaAnswer(request.token, error?.message ?? 'Camera and microphone connection failed');
        }
    },
    syncLiveMediaTrackState() {
        this.liveMediaMicOn = !!this.liveMediaStream?.getAudioTracks().some((track) => track.readyState === 'live' && track.enabled);
        this.liveMediaCameraOn = !!this.liveMediaStream?.getVideoTracks().some((track) => track.readyState === 'live' && track.enabled);
    },
    attachLiveMediaPreview() {
        this.$nextTick(() => {
            const preview = this.$refs.liveMediaSelfPreview;

            if (preview) {
                preview.srcObject = this.liveMediaStream;
                preview.play();
            }
        });
    },
    async startLiveMediaPreview() {
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            $wire.recordMonitoringSignal('live_media_unavailable', 'Camera and microphone are not supported by this browser');
            return false;
        }

        if (this.liveMediaStream) {
            this.attachLiveMediaPreview();
            return true;
        }

        try {
            this.liveMediaStream = await navigator.mediaDevices.getUserMedia({
                video: true,
                audio: true,
            });
            this.liveMediaStream.getTracks().forEach((track) => {
                track.addEventListener('ended', () => {
                    this.syncLiveMediaTrackState();
                    $wire.recordMonitoringSignal('live_media_track_ended', 'Employee camera or microphone track ended', { kind: track.kind });
                });
            });
            this.syncLiveMediaTrackState();
            this.attachLiveMediaPreview();
            $wire.recordMonitoringSignal('live_media_preview_started', 'Employee opened camera and microphone preview');
            return true;
        } catch (error) {
            $wire.recordMonitoringSignal('live_media_denied', 'Employee did not grant camera and microphone permission', { message: error?.message ?? 'Permission denied' });
            return false;
        }
    },
    async toggleLiveMediaMic() {
        if (!this.liveMediaStream && !await this.startLiveMediaPreview()) return;

        const nextState = !this.liveMediaMicOn;
        this.liveMediaStream.getAudioTracks().forEach((track) => {
            track.enabled = nextState;
        });
        this.syncLiveMediaTrackState();
        $wire.recordMonitoringSignal(
            nextState ? 'live_media_mic_unmuted' : 'live_media_mic_muted',
            nextState ? 'Employee turned microphone on' : 'Employee muted microphone'
        );
    },
    async toggleLiveMediaCamera() {
        if (!this.liveMediaStream && !await this.startLiveMediaPreview()) return;

        const nextState = !this.liveMediaCameraOn;
        this.liveMediaStream.getVideoTracks().forEach((track) => {
            track.enabled = nextState;
        });
        this.syncLiveMediaTrackState();
        $wire.recordMonitoringSignal(
            nextState ? 'live_media_camera_on' : 'live_media_camera_off',
            nextState ? 'Employee turned camera on' : 'Employee turned camera off'
        );
    },
    stopLiveMedia(report = true) {
        if (this.liveMediaPeer) {
            this.liveMediaPeer.close();
        }

        if (this.liveMediaStream) {
            this.liveMediaStream.getTracks().forEach((track) => track.stop());
        }

        this.liveMediaPeer = null;
        this.liveMediaToken = null;
        this.liveMediaStream = null;
        this.liveMediaMicOn = false;
        this.liveMediaCameraOn = false;
        const preview = this.$refs.liveMediaSelfPreview;

        if (preview) {
            preview.srcObject = null;
        }

        this.updateMonitoringPopout();

        if (report) {
            $wire.recordMonitoringSignal('live_media_stopped_by_employee', 'Employee stopped camera and microphone sharing');
        }
    },
    stopScreenShare(report = true) {
        if (this.screenshotTimer) {
            clearInterval(this.screenshotTimer);
            this.screenshotTimer = null;
        }

        if (this.liveSnapshotTimer) {
            clearInterval(this.liveSnapshotTimer);
            this.liveSnapshotTimer = null;
            this.liveSnapshotToken = null;
        }

        if (this.screenStream) {
            this.screenStream.getTracks().forEach((track) => track.stop());
        }

        if (this.liveScreenPeer) {
            this.liveScreenPeer.close();
        }

        this.stopLiveMedia(false);

        this.screenStream = null;
        this.screenVideo = null;
        this.liveScreenPeer = null;
        this.liveScreenToken = null;
        this.screenShareActive = false;
        this.monitoringRuntime().screenStream = null;
        this.monitoringRuntime().screenShareActive = false;

        if (report) {
            $wire.recordMonitoringSignal('screen_share_stopped', 'Screen share stopped at Time Out');
        }
    },
    async confirmPunchWithMonitoring(verifyType) {
        if (this.punchSubmitting) {
            return;
        }

        this.punchSubmitting = true;

        try {
            if (verifyType === 'Morning In') {
                const screenShared = this.isScreenShareLive() || await this.startScreenShare();

                if (!screenShared) {
                    return;
                }
            }

            if (verifyType === 'Afternoon Out') {
                this.stopScreenShare();
            }

            await $wire.confirmYes();

            if (verifyType === 'Morning In') {
                this.syncMonitoring(true);
                this.captureScreenSnapshot('time_in');
            }
        } finally {
            this.punchSubmitting = false;
        }
    },
    init() {
        this.$nextTick(async () => {
            const restoredScreenShare = this.restoreScreenShareRuntime();
            const mustShareScreen = await $wire.shouldRequireMonitoringScreenShare();

            if (mustShareScreen && !restoredScreenShare && !this.hasLiveScreenTrack()) {
                this.screenShareActive = false;
                this.screenResumeRequired = true;
            }

            await this.syncMonitoring(true);
            this.checkLiveScreenRequest();
            this.screenResumeRequired = mustShareScreen && !this.hasLiveScreenTrack();
        });
        setInterval(() => this.syncMonitoring(true), 30000);
        setInterval(() => this.checkLiveSnapshotRequest(), 3000);
        this.resetAfkTimer();
        window.addEventListener('mousemove', () => this.markActivity('mouse'));
        window.addEventListener('keydown', () => this.markActivity('key'));
        window.addEventListener('click', () => this.markActivity('click'));
        window.addEventListener('touchstart', () => this.markActivity('touch'));
        document.addEventListener('visibilitychange', () => {
            this.syncMonitoring(true);

            if (document.hidden && this.screenShareActive) {
                this.openMonitoringPopout(true);
            } else if (!document.hidden) {
                this.closeMonitoringPopout();
            }
        });
        window.addEventListener('blur', () => {
            if (this.screenShareActive) {
                this.openMonitoringPopout(true);
            }
        });
        window.addEventListener('focus', () => this.closeMonitoringPopout());
        window.addEventListener('offline', () => $wire.recordMonitoringSignal('browser_offline', 'Browser went offline during WFH monitoring'));
        window.addEventListener('online', () => $wire.recordMonitoringSignal('browser_online', 'Browser came back online during WFH monitoring'));
        window.addEventListener('beforeunload', () => $wire.recordMonitoringSignal('before_unload', 'Employee left or refreshed the HRIS monitoring tab'));
        setInterval(() => {
            this.clockTick = Date.now();
            this.updateMonitoringPopout();
        }, 1000);
    },
}" class="w-full">

    @if ($scheduleType === 'WFH' || $wfhStatus === 'approved')
        <div x-show="monitoringFloatOpen" x-cloak wire:ignore.self wire:key="wfh-monitoring-floating-dock" class="fixed bottom-3 left-1/2 max-h-[calc(100dvh-1.5rem)] w-[min(42rem,calc(100vw-0.75rem))] -translate-x-1/2 overflow-y-auto rounded-3xl border border-white/15 bg-slate-950/90 p-2 text-white shadow-[0_24px_80px_rgba(15,23,42,0.55)] backdrop-blur-2xl sm:bottom-5 sm:w-[min(42rem,calc(100vw-1.5rem))] sm:rounded-[2rem] sm:p-2.5" style="z-index: 2147483644;">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex min-w-0 items-center gap-3">
                    <div class="relative flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl text-white shadow-lg shadow-blue-900/40"
                        :class="isScreenShareLive() ? 'bg-gradient-to-br from-blue-500 to-cyan-400' : 'bg-slate-800'">
                        <i class="bi bi-display-fill"></i>
                        <span x-show="!isScreenShareLive()" class="absolute h-0.5 w-8 rotate-45 rounded-full bg-rose-300 shadow"></span>
                        <span class="absolute -right-0.5 -top-0.5 h-3.5 w-3.5 rounded-full border-2 border-slate-950" :class="isScreenShareLive() ? 'bg-emerald-400' : 'bg-amber-400'"></span>
                    </div>
                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            <p class="text-[11px] font-black uppercase tracking-[0.22em] text-blue-200">WFH monitor</p>
                            <span class="rounded-full px-2 py-0.5 text-[10px] font-bold"
                                :class="liveScreenRequestPending && !isScreenShareLive() ? 'bg-amber-400 text-slate-950' : 'bg-white/10 text-slate-200'"
                                x-text="liveScreenRequestPending && !isScreenShareLive() ? 'Live view opening' : (isScreenShareLive() ? 'Auto floating ready' : 'Needs share')"></span>
                        </div>
                        <div class="mt-1 flex flex-wrap items-end gap-2 sm:gap-3">
                            <div class="min-w-[9.5rem] rounded-2xl bg-emerald-400/10 px-3 py-1.5 ring-1 ring-emerald-300/20">
                                <p class="text-[10px] font-bold uppercase tracking-[0.16em] text-emerald-200">Daily online</p>
                                <p class="font-mono text-xl font-black leading-none tracking-tight text-emerald-100 sm:text-2xl" x-text="onlineElapsedLabel()"></p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-[0.16em] text-slate-400">Current session</p>
                                <p class="font-mono text-sm font-black leading-none tracking-tight sm:text-base" x-text="elapsedLabel()"></p>
                            </div>
                            <span class="rounded-full px-2.5 py-1 text-[11px] font-black" :class="monitoringStatusClass()" x-text="monitoringStatusLabel()"></span>
                        </div>
                    </div>
                </div>

                <div class="flex flex-wrap items-center justify-center gap-2 sm:justify-end">
                    <button type="button" @click="isScreenShareLive() ? stopScreenShare() : startScreenShare()" class="relative flex h-12 w-12 items-center justify-center rounded-full text-white shadow-lg shadow-blue-950/30 transition"
                        :class="isScreenShareLive() ? 'bg-blue-600 hover:bg-blue-500' : 'bg-amber-500 hover:bg-amber-400'"
                        :title="isScreenShareLive() ? 'Cancel screen sharing' : 'Start screen sharing'">
                        <i class="bi bi-display-fill"></i>
                        <span x-show="!isScreenShareLive()" class="absolute h-0.5 w-8 rotate-45 rounded-full bg-white shadow"></span>
                    </button>
                    <button type="button" @click="monitoringFloatOpen = false" class="flex h-12 w-12 items-center justify-center rounded-full bg-white/10 text-white transition hover:bg-white/15" title="Hide monitor">
                        <i class="bi bi-chevron-down"></i>
                    </button>
                </div>
            </div>
            <div x-show="liveScreenRequestPending && !isScreenShareLive()" x-cloak class="mt-3 flex flex-col gap-2 rounded-2xl border border-amber-300/30 bg-amber-400/12 p-3 text-amber-50 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-xs font-black uppercase tracking-[0.18em] text-amber-200">Admin live view opening</p>
                    <p class="text-sm font-semibold text-white">Screen sharing must be active before the live feed can connect.</p>
                </div>
                <button type="button" @click="startScreenShare()" class="rounded-full bg-amber-400 px-4 py-2 text-sm font-black text-slate-950 transition hover:bg-amber-300">
                    Share screen
                </button>
            </div>
        </div>

        <button type="button" x-show="!monitoringFloatOpen" x-cloak @click="monitoringFloatOpen = true" class="fixed bottom-5 left-1/2 -translate-x-1/2 rounded-full bg-slate-950 px-4 py-3 text-sm font-bold text-white shadow-2xl hover:bg-slate-800" style="z-index: 2147483644;">
            <i class="bi bi-display-fill mr-1"></i> Show WFH Monitor
        </button>
    @endif

    <div x-show="afkPromptOpen" x-cloak class="fixed inset-0 flex items-start justify-center overflow-y-auto bg-slate-950/80 p-4 pt-20 sm:items-center sm:pt-4" style="z-index: 2147483647;">
        <div class="relative w-full max-w-lg rounded-2xl border border-amber-200 bg-white p-6 shadow-2xl dark:border-amber-500/30 dark:bg-slate-900" style="z-index: 2147483647;">
            <p class="text-xs font-bold uppercase tracking-[0.2em] text-amber-600 dark:text-amber-300">AFK Check</p>
            <h2 class="mt-2 text-2xl font-bold text-slate-900 dark:text-white">Are you still working?</h2>
            <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">
                The browser has not detected activity inside the HRIS tab. Please choose your current work status so the monitoring log stays accurate.
            </p>
            <div class="mt-5 grid gap-2 sm:grid-cols-2">
                <button type="button" @click="respondToAfk('Still Working')" class="rounded-lg bg-emerald-600 px-4 py-3 text-sm font-semibold text-white hover:bg-emerald-700">
                    Still Working
                </button>
                <button type="button" @click="respondToAfk('On Break')" class="rounded-lg bg-sky-600 px-4 py-3 text-sm font-semibold text-white hover:bg-sky-700">
                    On Break
                </button>
                <button type="button" @click="respondToAfk('In Meeting')" class="rounded-lg bg-indigo-600 px-4 py-3 text-sm font-semibold text-white hover:bg-indigo-700">
                    In Meeting
                </button>
                <button type="button" @click="respondToAfk('Field Work')" class="rounded-lg bg-orange-600 px-4 py-3 text-sm font-semibold text-white hover:bg-orange-700">
                    Field Work
                </button>
            </div>
        </div>
    </div>

    <div x-show="screenSurfaceWarning" x-cloak class="fixed inset-0 flex items-start justify-center overflow-y-auto bg-slate-950/80 p-4 pt-20 sm:items-center sm:pt-4" style="z-index: 2147483647;">
        <div class="relative w-full max-w-lg rounded-2xl border border-rose-200 bg-white p-6 shadow-2xl dark:border-rose-500/30 dark:bg-slate-900" style="z-index: 2147483647;">
            <p class="text-xs font-bold uppercase tracking-[0.2em] text-rose-600 dark:text-rose-300">Screen Share Required</p>
            <h2 class="mt-2 text-2xl font-bold text-slate-900 dark:text-white">Choose Entire Screen</h2>
            <p class="mt-2 text-sm text-slate-600 dark:text-slate-300" x-text="screenSurfaceWarning"></p>
            <div class="mt-4 rounded-lg bg-slate-100 p-3 text-sm text-slate-700 dark:bg-slate-800 dark:text-slate-200">
                In the browser sharing picker, select <strong>Entire Screen</strong> or your full monitor, then click Share. Window or tab sharing will be rejected for WFH monitoring.
            </div>
            <div class="mt-5 flex justify-end gap-2">
                <button type="button" @click="screenSurfaceWarning = null" class="rounded-lg bg-slate-500 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-600">
                    Close
                </button>
                <button type="button" @click="screenSurfaceWarning = null; startScreenShare()" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                    Retry Screen Share
                </button>
            </div>
        </div>
    </div>

    <div x-show="screenResumeRequired && !isScreenShareLive() && !screenSurfaceWarning" x-cloak class="fixed inset-0 flex items-start justify-center overflow-y-auto bg-slate-950/80 p-4 pt-20 sm:items-center sm:pt-4" style="z-index: 2147483646;">
        <div class="relative w-full max-w-lg rounded-[28px] border border-blue-200 bg-white p-6 shadow-2xl dark:border-blue-500/30 dark:bg-slate-900" style="z-index: 2147483647;">
            <p class="text-xs font-bold uppercase tracking-[0.2em] text-blue-600 dark:text-blue-300">Monitoring Still Active</p>
            <h2 class="mt-2 text-2xl font-bold text-slate-900 dark:text-white" x-text="liveScreenRequestPending ? 'Admin Live View Opening' : 'Screen Share Required'"></h2>
            <p class="mt-2 text-sm text-slate-600 dark:text-slate-300" x-text="liveScreenRequestPending ? 'The admin dashboard is opening your live screen. Please resume screen sharing so the feed can connect.' : 'You are currently timed in for WFH and have not timed out yet. Please resume screen sharing so monitoring can continue after the page refresh.'"></p>
            <div class="mt-4 rounded-lg bg-slate-100 p-3 text-sm text-slate-700 dark:bg-slate-800 dark:text-slate-200">
                In the browser picker, choose <strong>Entire Screen</strong> or your full monitor. Window or tab sharing will be rejected.
            </div>
            <button type="button" @click="startScreenShare()" class="mt-5 w-full rounded-full bg-blue-600 px-4 py-3 text-sm font-semibold text-white hover:bg-blue-700">
                <span x-text="liveScreenRequestPending ? 'Share Screen and Connect' : 'Start Screen Sharing'"></span>
            </button>
        </div>
    </div>

    <style>
        #map {
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            transition: all 0.3s ease;
        }

        #map:hover {
            box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
        }

        #map2 {
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            transition: all 0.3s ease;
        }

        #map2:hover {
            box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
        }

        @-webkit-keyframes spinner-border {
            to {
                transform: rotate(360deg);
            }
        }

        @keyframes spinner-border {
            to {
                transform: rotate(360deg);
            }
        }

        .spinner-border {
            display: inline-block;
            width: 1rem;
            height: 1rem;
            vertical-align: text-bottom;
            border: 2px solid currentColor;
            border-right-color: transparent;
            border-radius: 50%;
            -webkit-animation: spinner-border .75s linear infinite;
            animation: spinner-border .75s linear infinite;
            color: white;
        }

        .scrollbar-thin1::-webkit-scrollbar {
            width: 5px;
        }

        .scrollbar-thin1::-webkit-scrollbar-thumb {
            background-color: #1a1a1a4b;
        }

        .scrollbar-thin1::-webkit-scrollbar-track {
            background-color: #b6b6b6;
        }
    </style>

    <div class="w-full flex justify-center">
        <div class="flex justify-center w-full">
            <div class="w-full bg-white rounded-xl p-3 sm:p-8 shadow dark:bg-gray-800 overflow-x-visible border border-slate-200 dark:border-slate-700">

                @if ($isMyBirthday)
                    <x-birthday />
                @endif


                {{-- @if ($hasWFHLocation)
                    <div class="flex-col mb-4 justify-center w-full bg-gray-50 dark:bg-slate-700 border border-gray-300 dark:border-gray-800 overflow-hidden relative" style="border-radius: 8px;">
                        
                        <div>
                            <div class="flex justify-between px-4 pt-4">
                                <div class="text-sm font-semibold text-gray-900 dark:text-white h-10 text-left">
                                    <i class="bi bi-clock"></i> {{ $formattedTime2 ?: $formattedTime }}
                                </div>
                                <div class="relative">
                                    <i class="bi bi-three-dots-vertical cursor-pointer" @click="open = !open"></i>
                                    <div x-show="open" @click.away="open = false"
                                        class="absolute top-4 right-4 z-20 p-3 border border-gray-400 text-sm
                                        rounded-lg shadow-2xl bg-white dark:bg-slate-800" style="width: 250px">
                                        <span>{{ $locReqGranted ? 'Request to change WFH location' : 'Change WFH location is pending for approval' }}</span>
                                        @if ($locReqGranted)
                                            <p wire:click="toggleEditLocation('request')"
                                                class="mt-1 cursor-pointer px-2 py-2 text-gray-800 dark:text-white hover:text-blue-500 rounded w-full hover:bg-slate-50 dark:hover:bg-slate-700/20">
                                                <i class="bi bi-geo-alt"></i> Change WFH Location
                                            </p>
                                        @else
                                            <p class="mt-1 px-2 py-2 text-gray-800 dark:text-white text-left rounded w-full opacity-50">
                                                <i class="bi bi-check2-circle"></i> Request Sent
                                            </p>
                                        @endif

                                        <p wire:click="showLocReqHistory" @click="showWFHLocHistory = true" class="mt-1 px-2 cursor-pointer py-2 text-gray-800 dark:text-white hover:text-blue-500 text-left rounded w-full hover:bg-slate-50 dark:hover:bg-slate-700/20">
                                            <i class="bi bi-clock-history"></i> History
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div wire:ignore>
                                <div id="map" style="height: 250px; width: 100%; margin: 0;"></div>
                            </div>

                            <div class="text-sm flex p-4">
                                <div class="w-1/2">
                                   <span class="font-bold">WFH Location</span> <br>
                                    Lat: <span class="text-gray-800 dark:text-white">{{ $registeredLatitude ?? '...' }}</span> <br>
                                    Lng: <span class="text-gray-800 dark:text-white">{{ $registeredLongitude ?? '...' }}</span> <br>
                                </div>
                                <div class="w-1/2">
                                    <span class="font-bold">Currect Location</span> <br>
                                    Lat: <span class="{{ $isWithinRadius ? 'text-green-500' : 'text-red-500' }}">{{ $latitude ?? '...' }}</span> <br>
                                    Lng: <span class="{{ $isWithinRadius ? 'text-green-500' : 'text-red-500' }}">{{ $longitude ?? '...' }}</span> <br>
                                </div>
                            </div>
                        </div>

                        <div 
                            x-show="showWFHLocHistory" 
                            x-transition:enter="transition ease-out duration-300 transform"
                            x-transition:enter-start="translate-y-full opacity-0"
                            x-transition:enter-end="translate-y-0 opacity-100"
                            x-transition:leave="transition ease-in duration-200 transform"
                            x-transition:leave-start="translate-y-0 opacity-100"
                            x-transition:leave-end="translate-y-full opacity-0"
                            x-cloak 
                            class="absolute inset-0 bg-gray-50 dark:bg-slate-700 overflow-hidden w-full h-full z-50 flex">
                            <div class="p-6 overflow-hidden relative w-full">
                                <button @click="showWFHLocHistory = false" 
                                        class="px-3 text-white rounded-md absolute
                                        text-sm bg-gray-500 hover:bg-gray-600  
                                        focus:outline-none" title="Close" style="top: 15px; right: 15px; z-index: 11;">
                                        Close
                                </button>

                                <div class="w-full flex justify-between bg-gray-50 dark:bg-slate-700 z-10" style="position: sticky; top: 0;">
                                    <div class="w-full sm:w-1/3 sm:mr-4 mb-4">
                                        <label for="search" class="block text-md font-medium text-gray-800 dark:text-white mb-1"><i class="bi bi-clock-history"></i> WFH Location History</label>
                                        <input type="text" id="search" wire:model.live="search"
                                            class="px-2 py-1.5 block w-full shadow-sm sm:text-sm border border-gray-400 hover:bg-gray-300 rounded-md
                                                dark:hover:bg-slate-600 dark:border-slate-600
                                                dark:text-gray-300 dark:bg-gray-800"
                                            placeholder="Search address">
                                    </div>
                                </div>

                                <div class="border dark:border-gray-600 scrollbar-thin1 w-full" style="height: 280px; overflow-y:scroll;">
                                    <div class="overflow-x-auto w-full">
                                        <table class="w-full min-w-full">
                                            <thead class="bg-gray-100 dark:bg-gray-600">
                                                <tr class="whitespace-nowrap">
                                                    <th scope="col" class="px-5 py-3 text-left text-sm font-medium uppercase">
                                                        Date
                                                    </th>
                                                    <th scope="col" class="px-5 py-3 text-center text-sm font-medium uppercase">
                                                        Status
                                                    </th>
                                                    <th scope="col" class="px-5 py-3 text-center text-sm font-medium uppercase">
                                                        Address
                                                    </th>
                                                    <th scope="col" class="px-5 py-3 text-center text-sm font-medium uppercase">
                                                        Geolocation
                                                    </th>
                                                    <th scope="col" class="px-5 py-3 text-center text-sm font-medium uppercase">
                                                        Request Attachment
                                                    </th>
                                                    <th class="px-5 py-3 text-gray-100 text-sm font-medium text-center uppercase sticky right-0 bg-gray-600 dark:bg-gray-600">
                                                        Action
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-neutral-300 dark:divide-gray-600">
                                                @foreach ($history as $employee)
                                                    <tr class="text-neutral-800 dark:text-neutral-200">
                                                        <td class="px-5 py-4 text-left text-sm font-medium text-nowrap">
                                                            {{ \Carbon\Carbon::parse($employee->date_approved)->format('F d, Y') }}
                                                        </td>
                                                        <td class="px-5 py-4 text-center text-sm font-medium text-nowrap">
                                                            @if ($employee->status == 1)
                                                                <span
                                                                    class="text-xs text-white bg-green-500 rounded-lg py-1.5 px-4">Approved</span>
                                                            @elseif($employee->status == 2)
                                                                <span
                                                                class="text-xs text-white bg-red-500 rounded-lg py-1.5 px-4">Disapproved</span>
                                                            @endif
                                                        </td>
                                                        <td class="px-5 py-4 text-center text-sm font-medium text-nowrap">
                                                            {{ $employee->address ?? 'None' }}
                                                        </td>
                                                        <td class="px-5 py-4 text-center text-sm font-medium whitespace-nowrap">
                                                            Lat: {{ $employee->curr_lat ?? 'None' }} <br>
                                                            Lng: {{ $employee->curr_lng ?? 'None' }}
                                                        </td>
                                                        <td class="px-5 py-4 text-center text-sm font-medium whitespace-nowrap {{ $employee->attachment ? '' : 'opacity-30' }}">
                                                            {{ $employee->attachment ?? 'None' }}
                                                        </td>
                                                        <td class="px-5 py-4 text-sm font-medium text-center whitespace-nowrap sticky right-0 bg-white dark:bg-gray-800">
                                                            <div class="relative">
                                                                @php
                                                                    $thisName = trim($employee->surname . ', ' . $employee->first_name . ' ' . 
                                                                        ($employee->middle_name ? $employee->middle_name . ' ' : '') . 
                                                                        ($employee->name_extension ?? ''));
                                                                @endphp
                                                                <button wire:click="viewWFHLocHistory({{ $employee->id }})" @click="viewWFHLocHistory = true"
                                                                    class="peer inline-flex items-center justify-center px-4 py-2 text-sm font-medium tracking-wide text-blue-500 hover:text-blue-600
                                                                    focus:outline-none" title="View">
                                                                    <i class="bi bi-eye-fill"></i>
                                                                </button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                        @if ($history->isEmpty())
                                            <div class="p-4 text-center text-gray-500 dark:text-gray-300">
                                                No records!
                                            </div> 
                                        @endif
                                    </div>
                                    <div class="p-5 text-neutral-500 dark:text-neutral-200 bg-gray-100 dark:bg-gray-600">
                                        {{ $history->links() }}
                                    </div>

                                </div> 
                            </div>
                            <div class="w-full bg-gray-50 dark:bg-slate-700 z-10 absolute bottom-0" style="height: 15px;">
                            </div>
                        </div>

                        <div 
                            x-show="viewWFHLocHistory" 
                            x-transition:enter="transition ease-out duration-300 transform"
                            x-transition:enter-start="translate-y-full opacity-0"
                            x-transition:enter-end="translate-y-0 opacity-100"
                            x-transition:leave="transition ease-in duration-200 transform"
                            x-transition:leave-start="translate-y-0 opacity-100"
                            x-transition:leave-end="translate-y-full opacity-0"
                            x-cloak 
                            class="absolute inset-0 bg-gray-50 dark:bg-slate-700 overflow-hidden w-full h-full z-50 flex">
                            <div class="p-6 overflow-hidden relative w-full h-full">
                                <button @click="viewWFHLocHistory = false" 
                                        class="px-3 text-white rounded-md absolute
                                        text-sm bg-gray-500 hover:bg-gray-600  
                                        focus:outline-none" title="Close" style="top: 15px; right: 15px; z-index: 11;">
                                        Close
                                </button>

                                <div class="flex-col justify-center w-full bg-gray-200 dark:bg-slate-700 border border-gray-300 dark:border-gray-800 mb-2 mt-6 scrollbar-thin1" style="height: 330px; overflow-y:scroll;">
                                    <div wire:ignore class="w-full">
                                        <div id="map3" style="height: 250px; width: 100%; margin: 0;"></div>
                                    </div>
                    
                                    <div class="text-sm grid grid-cols-2 mt-2 px-4 mb-2">
                                        <div class="col-span-2 sm:col-span-1">
                                            Address: <span class="text-gray-800 dark:text-gray-50">{{ $address ?? '...' }}</span>
                                            Geolocation: <span class="text-gray-800 dark:text-gray-50">{{ 'Lat: ' . $registeredLatitude . ' | Lng: ' . $registeredLongitude }}</span><br>
                                        </div>
                                        <div class="col-span-2 sm:col-span-1">
                                            <span class="{{ $approveOnly ? 'hidden' : '' }}">
                                                Approved By: <span class="text-gray-800 dark:text-gray-50">{{ $approvedBy ?? '...' }}</span><br>
                                                Date Approved: <span class="text-gray-800 dark:text-gray-50">{{ $approvedDate ?? '...' }}</span>
                                            </span>
                                            <span class="{{ $approveOnly ? '' : 'hidden' }}">
                                                Disapproved By: <span class="text-gray-800 dark:text-gray-50">{{ $disapprovedBy ?? '...' }}</span><br>
                                                Date Disapproved: <span class="text-gray-800 dark:text-gray-50">{{ $disapprovedDate ?? '...' }}</span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                @else
                    <div class="flex justify-center mb-4">
                        <button wire:click="toggleEditLocation('register')" 
                            class="mt-4 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 w-full">
                            Register WFH Location
                        </button>
                    </div>
                @endif --}}

                <div class="w-full flex flex-col justify-center items-center">
                    <div
                        id="clock"
                        wire:ignore
                        x-data="{
                            now: new Date(),
                            timer: null,
                            init() {
                                this.timer = setInterval(() => {
                                    this.now = new Date();
                                }, 1000);
                            },
                            format() {
                                return this.now.toLocaleString('en-US', {
                                    weekday: 'long',
                                    year: 'numeric',
                                    month: 'long',
                                    day: 'numeric',
                                    hour: '2-digit',
                                    minute: '2-digit',
                                    second: '2-digit',
                                    hour12: true,
                                });
                            },
                        }"
                        x-init="init()"
                        x-text="format()"
                        class="text-lg font-semibold mb-4 text-gray-900 dark:text-white h-10 text-center"
                    >
                    </div>
                    @if ($scheduleType === 'WFH' || $wfhStatus === 'approved')
                        <div class="w-full max-w-5xl mb-5 grid gap-3 sm:grid-cols-4">
                            <div class="rounded-lg border border-slate-200 bg-slate-50 p-4 dark:border-slate-700 dark:bg-slate-900">
                                <p class="text-xs font-semibold uppercase text-slate-500 dark:text-slate-400">Monitoring</p>
                                <div class="mt-2 flex items-center gap-2">
                                    <span class="h-2.5 w-2.5 rounded-full
                                        {{ $monitoringState === 'Active' ? 'bg-emerald-500' : ($monitoringState === 'AFK' ? 'bg-amber-500' : ($monitoringState === 'On Break' ? 'bg-sky-500' : 'bg-rose-500')) }}">
                                    </span>
                                    <span class="text-lg font-semibold text-slate-900 dark:text-white">{{ $monitoringState }}</span>
                                </div>
                            </div>
                            <div class="rounded-lg border border-slate-200 bg-slate-50 p-4 dark:border-slate-700 dark:bg-slate-900">
                                <p class="text-xs font-semibold uppercase text-slate-500 dark:text-slate-400">Work Status</p>
                                <p class="mt-2 text-lg font-semibold text-slate-900 dark:text-white">{{ $monitoringWorkStatus }}</p>
                            </div>
                            <div class="rounded-lg border border-slate-200 bg-slate-50 p-4 dark:border-slate-700 dark:bg-slate-900">
                                <p class="text-xs font-semibold uppercase text-slate-500 dark:text-slate-400">Last Activity</p>
                                <p class="mt-2 text-lg font-semibold text-slate-900 dark:text-white">{{ $monitoringLastActivity ?? 'No active session' }}</p>
                            </div>
                            <div class="rounded-lg border border-slate-200 bg-slate-50 p-4 dark:border-slate-700 dark:bg-slate-900">
                                <p class="text-xs font-semibold uppercase text-slate-500 dark:text-slate-400">Daily Online Time</p>
                                <p class="mt-2 text-lg font-semibold text-emerald-700 dark:text-emerald-300" x-text="onlineElapsedLabel()"></p>
                                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Session: <span x-text="elapsedLabel()"></span></p>
                            </div>
                        </div>

                    @endif
                    <div
                        class="flex flex-col sm:flex-row justify-center items-center space-y-4 md:space-y-0 md:space-x-12">

                        <div
                            class="block max-w-sm p-6 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-900 dark:border-gray-700 relative">
                            <h5
                                class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white text-center">
                                WFH ATTENDANCE</h5>
                            <div class="grid grid-cols-1 gap-4 p-4">

                                <div class="flex justify-center">
                                    <button wire:click="confirmPunch('0', 'Morning In')"
                                        @if ($morningInDisabled) disabled @endif
                                        class="relative inline-flex items-center justify-center p-0.5 mb-2 mx-2 overflow-hidden text-sm font-medium text-gray-900 rounded-lg group bg-gradient-to-br from-purple-600 to-blue-500 group-hover:from-purple-600 group-hover:to-blue-500 hover:text-white dark:text-white focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 w-48 lg:w-64 disabled:opacity-50 disabled:cursor-not-allowed">
                                        <span
                                            class="relative px-10 py-2.5 bg-white dark:bg-gray-900 rounded-md group-hover:bg-opacity-0 w-48 lg:w-64 transition-all duration-75 ease-in group-disabled:bg-opacity-0 group-disabled:text-white">
                                            Time In
                                        </span>
                                    </button>
                                </div>

                                <!-- Break Out (WFH uses 5, onsite uses 4) -->
                                <div class="flex justify-center">
                                    <button wire:click="confirmPunch('{{ $breakOutPunchState }}', 'Break Out')"
                                        @if ($breakOutDisabled) disabled @endif
                                        class="relative inline-flex items-center justify-center p-0.5 mb-2 mx-2 overflow-hidden text-sm font-medium text-gray-900 rounded-lg group bg-gradient-to-br from-yellow-500 to-orange-500 group-hover:from-yellow-500 group-hover:to-orange-500 hover:text-white dark:text-white focus:ring-4 focus:outline-none focus:ring-orange-300 dark:focus:ring-orange-800 w-48 lg:w-64 disabled:opacity-50 disabled:cursor-not-allowed">
                                        <span
                                            class="relative px-10 py-2.5 bg-white dark:bg-gray-900 rounded-md group-hover:bg-opacity-0 w-48 lg:w-64 transition-all duration-75 ease-in group-disabled:bg-opacity-0 group-disabled:text-white">
                                            Break Out
                                        </span>
                                    </button>
                                </div>

                                <!-- Break In (WFH uses 4, onsite uses 5) -->
                                <div class="flex justify-center">
                                    <button wire:click="confirmPunch('{{ $breakInPunchState }}', 'Break In')"
                                        @if ($breakInDisabled) disabled @endif
                                        class="relative inline-flex items-center justify-center p-0.5 mb-2 mx-2 overflow-hidden text-sm font-medium text-gray-900 rounded-lg group bg-gradient-to-br from-yellow-500 to-orange-500 group-hover:from-yellow-500 group-hover:to-orange-500 hover:text-white dark:text-white focus:ring-4 focus:outline-none focus:ring-orange-300 dark:focus:ring-orange-800 w-48 lg:w-64 disabled:opacity-50 disabled:cursor-not-allowed">
                                        <span
                                            class="relative px-10 py-2.5 bg-white dark:bg-gray-900 rounded-md group-hover:bg-opacity-0 w-48 lg:w-64 transition-all duration-75 ease-in group-disabled:bg-opacity-0 group-disabled:text-white">
                                            Break In
                                        </span>
                                    </button>
                                </div>
                                {{-- <div class="flex justify-center">
                                    <button wire:click="confirmPunch('morningOut', 'Morning Out')"
                                        @if ($morningOutDisabled) disabled @endif
                                        class="relative inline-flex items-center justify-center p-0.5 mb-2 mx-2 overflow-hidden text-sm font-medium text-gray-900 rounded-lg group bg-gradient-to-br from-purple-600 to-blue-500 group-hover:from-purple-600 group-hover:to-blue-500 hover:text-white dark:text-white focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 w-48 lg:w-64 disabled:opacity-50 disabled:cursor-not-allowed">
                                        <span
                                            class="relative px-10 py-2.5 bg-white dark:bg-gray-900 rounded-md group-hover:bg-opacity-0 w-48 lg:w-64 transition-all duration-75 ease-in group-disabled:bg-opacity-0 group-disabled:text-white">
                                            Morning Out
                                        </span>
                                    </button>
                                </div>
                                <div class="flex justify-center">
                                    <button wire:click="confirmPunch('afternoonIn', 'Afternoon In')"
                                        @if ($afternoonInDisabled) disabled @endif
                                        class="relative inline-flex items-center justify-center p-0.5 mb-2 mx-2 overflow-hidden text-sm font-medium text-gray-900 rounded-lg group bg-gradient-to-br from-purple-600 to-blue-500 group-hover:from-purple-600 group-hover:to-blue-500 hover:text-white dark:text-white focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 w-48 lg:w-64 disabled:opacity-50 disabled:cursor-not-allowed">
                                        <span
                                            class="relative px-10 py-2.5 bg-white dark:bg-gray-900 rounded-md group-hover:bg-opacity-0 w-48 lg:w-64 transition-all duration-75 ease-in group-disabled:bg-opacity-0 group-disabled:text-white">
                                            Afternoon In
                                        </span>
                                    </button>
                                </div> --}}
                                <div class="flex justify-center">
                                    <button wire:click="confirmPunch('1', 'Afternoon Out')"
                                        @if ($afternoonOutDisabled) disabled @endif
                                        class="relative inline-flex items-center justify-center p-0.5 mb-2 mx-2 overflow-hidden text-sm font-medium text-gray-900 rounded-lg group bg-gradient-to-br from-purple-600 to-blue-500 group-hover:from-purple-600 group-hover:to-blue-500 hover:text-white dark:text-white focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 w-48 lg:w-64 disabled:opacity-50 disabled:cursor-not-allowed">
                                        <span
                                            class="relative px-10 py-2.5 bg-white dark:bg-gray-900 rounded-md group-hover:bg-opacity-0 w-48 lg:w-64 transition-all duration-75 ease-in group-disabled:bg-opacity-0 group-disabled:text-white">
                                            Time Out
                                        </span>
                                    </button>
                                </div>

                            </div>

                            @if ($scheduleType !== 'WFH' && $wfhStatus !== 'approved')
                                <div
                                    class="absolute inset-0 flex justify-center items-center bg-gray-700 bg-opacity-75 rounded-lg">
                                    <div class="text-center">
                                        <i class="bi bi-person-lock text-white" style="font-size: 5rem;"></i>
                                        <p class="mt-2 text-white font-bold">WFH is not available today</p>
                                    </div>
                                </div>
                                {{-- @elseif($scheduleType === 'WFH' && !$isWithinRadius) --}}
                                {{-- <div
                                    class="absolute inset-0 flex justify-center items-center bg-gray-700 bg-opacity-75 rounded-lg">
                                    <div class="text-center">
                                        <i class="bi bi-person-lock text-white" style="font-size: 5rem;"></i>
                                        <p class="mt-2 text-white font-bold">You are outside the allowed<br>location for WFH attendance</p>
                                    </div>
                                </div> --}}
                            @endif
                        </div>

                        <div
                            class="block max-w-sm p-6 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-900 dark:border-gray-700 relative">
                            <h3
                                class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white text-center">
                                {{ $scheduleType === 'WFH' ? 'WFH Punch Time' : 'Onsite Punch Time' }}
                            </h3>

                            @php
                                $today = \Carbon\Carbon::now()->format('l'); // Get the current day name
                            @endphp

                            <div class="mb-4">
                                <h4 class="text-xl font-semibold text-gray-900 dark:text-white text-center border-b">
                                    {{ $today }}
                                </h4>
                                {{-- 'Morning Out',
                                    'Afternoon In', --}}
                                <div class="mt-2 text-center">
                                    {{-- @if ($scheduleType === 'WFH')
                                        @foreach (['Morning In', 'Afternoon Out'] as $type)
                                            <div class="mb-2 text-center">
                                                <strong>{{ $type }}</strong>
                                                <div>
                                                    @forelse ($groupedTransactions[$type] ?? [] as $transaction)
                                                        <div class="text-gray-700 dark:text-gray-300">
                                                            {{ \Carbon\Carbon::parse($transaction->punch_time)->format('H:i:s') }}
                                                        </div>
                                                    @empty
                                                        <div class="text-gray-400">No punch time recorded</div>
                                                    @endforelse
                                                </div>
                                            </div>
                                        @endforeach
                                    @else --}}
                                    {{-- Onsite punch times from EmployeesDTR --}}
                                    {{-- <div class="mb-2 text-center">
                                            <strong>Time In</strong>
                                            <div>{{ $groupedTransactions->morning_in ?? 'No punch time recorded' }}
                                            </div>
                                        </div> --}}
                                    {{-- <div class="mb-2 text-center">
                                            <strong>Morning Out</strong>
                                            <div>{{ $groupedTransactions->morning_out ?? 'No punch time recorded' }}
                                            </div>
                                        </div>
                                        <div class="mb-2 text-center">
                                            <strong>Afternoon In</strong>
                                            <div>{{ $groupedTransactions->afternoon_in ?? 'No punch time recorded' }}
                                            </div>
                                        </div> --}}
                                    {{-- <div class="mb-2 text-center">
                                            <strong>Time Out</strong>
                                            <div>{{ $groupedTransactions->afternoon_out ?? 'No punch time recorded' }}
                                            </div>
                                        </div>
                                    @endif --}}
                                    @if ($scheduleType === 'WFH' || $wfhStatus === 'approved')
                                        @php
                                            $displayLabels = [
                                                'Morning In' => 'Time In',
                                                'Break Out' => 'Break Out',
                                                'Break In' => 'Break In',
                                                'Afternoon Out' => 'Time Out',
                                            ];
                                        @endphp

                                        @foreach (['Morning In', 'Break Out', 'Break In', 'Afternoon Out'] as $type)
                                            <div class="mb-2 text-center">
                                                <strong>{{ $displayLabels[$type] }}</strong>
                                                <div>
                                                    @forelse ($groupedTransactions[$type] ?? [] as $transaction)
                                                        <div class="text-gray-700 dark:text-gray-300">
                                                            {{ \Carbon\Carbon::parse($transaction->punch_time)->format('H:i:s') }}
                                                        </div>
                                                    @empty
                                                        <div class="text-gray-400">No punch time recorded</div>
                                                    @endforelse
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <!-- Onsite punch times from EmployeesDTR -->
                                        <div class="mb-2 text-center">
                                            <strong>Time In</strong>
                                            <div>{{ $groupedTransactions->morning_in ?? 'No punch time recorded' }}</div>
                                        </div>
                                        <div class="mb-2 text-center">
                                            <strong>Break Out</strong>
                                            <div>{{ $groupedTransactions->break_out ?? 'No punch time recorded' }}</div>
                                        </div>
                                        <div class="mb-2 text-center">
                                            <strong>Break In</strong>
                                            <div>{{ $groupedTransactions->break_in ?? 'No punch time recorded' }}</div>
                                        </div>
                                        <div class="mb-2 text-center">
                                            <strong>Time Out</strong>
                                            <div>{{ $groupedTransactions->afternoon_out ?? 'No punch time recorded' }}</div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <x-modal id="punchConfirmation" maxWidth="md" centered wire:model="showConfirmation">
                    <div class="p-4">
                        <div class="flex items-center justify-between pb-4">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-200">
                                Punch Confirmation
                            </h3>
                            <button wire:click="closeConfirmation"
                                class="text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 focus:outline-none">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </div>

                        <div class="space-y-6">
                            <p class="text-gray-700 dark:text-gray-300">
                                Are you sure you want to punch
                                @if ($verifyType === 'Morning In')
                                    Time In
                                @elseif($verifyType === 'Afternoon Out')
                                    Time Out
                                @elseif($verifyType === 'Break In')
                                    Break In
                                @elseif($verifyType === 'Break Out')
                                    Break Out
                                @else
                                    {{ $verifyType }}
                                @endif?
                            </p>

                            <!-- Action Buttons -->
                            <div class="mt-6 flex justify-end space-x-4">
                                <button type="button" @click="confirmPunchWithMonitoring(@js($verifyType))"
                                    :disabled="punchSubmitting"
                                    class="px-4 py-2 rounded-md bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:cursor-wait disabled:opacity-60 dark:focus:ring-offset-gray-800">
                                    <span x-show="!punchSubmitting">Yes</span>
                                    <span x-show="punchSubmitting" x-cloak>Saving...</span>
                                </button>
                                <button wire:click="closeConfirmation"
                                    class="px-4 py-2 rounded-md bg-gray-700 hover:bg-gray-800 text-white text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 dark:focus:ring-offset-gray-800">
                                    No
                                </button>
                            </div>
                        </div>
                    </div>
                </x-modal>

            </div>
        </div>
    </div>

    {{-- Add WFH Location Modal --}}
    {{-- <x-modal id="registerLocation" maxWidth="md" wire:model="editLocation" x-data @open-modal.window="initMap2()">
        <div class="p-4">
            <div class="rounded-lg mb-4  dark:text-gray-50 text-slate-900 font-bold">
                {{ $editLocMessage }} WFH Location
                <button @click="show = false" class="float-right focus:outline-none" wire:click='resetVariables'>
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="col-span-2">
                <label for="address" class="block text-sm font-medium text-gray-700 dark:text-slate-400">Address <span class="text-red-500">*</span></label>
                <input type="text" id="address" wire:model.live='address' class="mt-1 p-2 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:text-gray-300 dark:bg-gray-700">
                @error('address') 
                    <span class="text-red-500 text-sm">The address is required!</span> 
                @enderror
            </div>

            <div class="mt-4  mb-1">
                <label for="purpose" class="block text-sm font-medium text-gray-700 dark:text-slate-400">Geolocation <span class="text-red-500">*</span></label>
            </div>
            <div class="flex-col justify-center w-full bg-gray-200 dark:bg-slate-700 border border-gray-300 mb-2" style="border-radius: 8px;">
                <div style="border-radius: 8px 8px 0 0;">
                    <input id="locationSearch" type="text" 
                           placeholder="Search location..." 
                           class="px-2 py-1.5 block w-full shadow-sm sm:text-sm border border-gray-400 hover:bg-gray-300
                                dark:hover:bg-slate-600 dark:border-slate-600
                                dark:text-gray-300 dark:bg-gray-800" style="border-radius: 8px 8px 0 0;"/>
                </div>
                <div wire:ignore class="w-full">
                    <div id="map2" style="height: 250px; width: 100%; margin: 0;"></div>
                </div>

                <div class="text-sm flex mt-2 px-4">
                    <div class="w-1/2 mb-2">
                        Location Info: <br>
                        Lat: <span class="text-gray-800 dark:text-white">{{ $newLat ?? '...' }}</span> <br>
                        Lng: <span class="text-gray-800 dark:text-white">{{ $newLng ?? '...' }}</span> <br>
                    </div>
                </div>
                @error('newLat') 
                    <span class="text-red-500 text-sm">The geolocation is required!</span> 
                @enderror
                <div class="w-full" style="border-radius:  0 0 8px 8px;">
                    <button class="bg-blue-500 hover:bg-blue-700 text-white py-2 px-4 w-full text-sm" 
                            @click="getCurrentLocation" style="border-radius:  0 0 8px 8px;">
                        Use My Current Location
                    </button>
                </div>
            </div>

            <div class="mt-4 flex justify-end col-span-2">
                <button class="mr-2 bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded" wire:click='saveLocation'>
                    {{ $editLocMessage == 'Change' ? 'Send Request' : 'Save' }}
                </button>
                <p @click="show = false" class="bg-gray-400 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded cursor-pointer" wire:click='resetVariables'>
                    Cancel
                </p>
            </div>
        </div>
    </x-modal> --}}


</div>

{{-- <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBLp1y5i3ftfv5O_BN0_YSMd0VrXUht-Bs&libraries=places"></script>
<script>
    let map, map2, map3, searchBox;
    let marker, marker2, marker3;
    const defaultLocation = { lat: 14.5995, lng: 120.9842 };
    
    function initMap() {
        map = new google.maps.Map(document.getElementById("map"), {
            zoom: 15,
            center: defaultLocation,
            mapTypeControl: false,
            streetViewControl: false,
            fullscreenControl: true,
            zoomControl: true,
            styles: [
                {
                    featureType: "poi",
                    elementType: "labels",
                    stylers: [{ visibility: "off" }]
                }
            ]
        });
    }

    function initMap2() {
        map2 = new google.maps.Map(document.getElementById("map2"), {
            zoom: 15,
            center: defaultLocation,
            mapTypeControl: false,
            streetViewControl: false,
            fullscreenControl: true,
            zoomControl: true,
            styles: [
                {
                    featureType: "poi",
                    elementType: "labels",
                    stylers: [{ visibility: "off" }]
                }
            ]
        });
        marker2 = new google.maps.Marker({
            position: defaultLocation,
            map: map2,
            draggable: true,
            title: 'Your WFH Location',
            animation: google.maps.Animation.DROP
        });

        // Listen for marker drag events
        google.maps.event.addListener(marker2, 'dragend', function(event) {
            const lat = event.latLng.lat();
            const lng = event.latLng.lng();
            updateLivewireLocation(lat, lng);
        });

        const input = document.getElementById("locationSearch");
        searchBox = new google.maps.places.SearchBox(input);

        map2.addListener("bounds_changed", () => {
            searchBox.setBounds(map2.getBounds());
        });

        searchBox.addListener("places_changed", () => {
            const places = searchBox.getPlaces();

            if (places.length === 0) return;
            const place = places[0];
            if (!place.geometry || !place.geometry.location) return;

            const location = place.geometry.location;
            const lat = location.lat();
            const lng = location.lng();
            map2.setCenter(location);
            marker2.setPosition(location);
            updateLivewireLocation(lat, lng);
        });
    }
    function updateLivewireLocation(lat, lng) {
        @this.set('newLat', lat);
        @this.set('newLng', lng);
    }

    function getCurrentLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    map2.setCenter({ lat, lng });
                    marker2.setPosition({ lat, lng });
                    updateLivewireLocation(lat, lng);
                },
                (error) => {
                    alert("Unable to retrieve your location. Please allow location access or try again.");
                    console.error(error);
                }
            );
        } else {
            alert("Geolocation is not supported by your browser.");
        }
    }

    function updateMap() {
        const lat = @this.latitude;
        const lng = @this.longitude;

        if (lat && lng) {
            const newLocation = { lat: parseFloat(lat), lng: parseFloat(lng) };

            if (!map) {initMap();}
            map.setCenter(newLocation);
            if (marker) {
                marker.setPosition(newLocation);
            } else {
                marker = new google.maps.Marker({
                    position: newLocation,
                    map: map,
                    title: 'Your Location',
                    animation: google.maps.Animation.DROP
                });
            }
        }
    }

    document.addEventListener('livewire:initialized', () => {
        Livewire.on('init-map2', () => {
            initMap2();
        });
    });
    document.addEventListener('DOMContentLoaded', initMap);
    setInterval(updateMap , 5000);

    function viewWFHLocHistoryMap() {
        const lat = @this.registeredLatitude;
        const lng = @this.registeredLongitude;
        
        if (lat && lng) {
            if (!map3) {
                const defaultLocation = { lat: 14.5995, lng: 120.9842 };
                map3 = new google.maps.Map(document.getElementById("map3"), {
                    zoom: 15,
                    center: defaultLocation,
                    mapTypeControl: false,
                    streetViewControl: false,
                    fullscreenControl: true,
                    zoomControl: true,
                    styles: [
                        {
                            featureType: "poi",
                            elementType: "labels",
                            stylers: [{ visibility: "off" }]
                        }
                    ]
                });
            }

            const newLocation = { lat: parseFloat(lat), lng: parseFloat(lng) };
            map3.setCenter(newLocation);
            if (marker3) {
                marker3.setPosition(newLocation);
            } else {
                marker3 = new google.maps.Marker({
                    position: newLocation,
                    map: map3,
                    title: 'Your WFH Location',
                    animation: google.maps.Animation.DROP
                });
            }
        }
    }
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('showWFHLocHistory', () => {
            viewWFHLocHistoryMap();
        });
    });
</script> --}}
