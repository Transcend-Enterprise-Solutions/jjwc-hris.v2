import './bootstrap';
import { createApp } from 'vue';
import WfhMonitoringWall from './components/WfhMonitoringWall.vue';

// Import Chart.js
import { Chart } from 'chart.js';

// Import flatpickr
import flatpickr from 'flatpickr';

// import component from './components/component';
import dashboardCard01 from './components/dashboard-card-01';
import dashboardCard02 from './components/dashboard-card-02';
import dashboardCard03 from './components/dashboard-card-03';
import dashboardCard04 from './components/dashboard-card-04';
import dashboardCard05 from './components/dashboard-card-05';
import dashboardCard06 from './components/dashboard-card-06';
import dashboardCard08 from './components/dashboard-card-08';
import dashboardCard09 from './components/dashboard-card-09';
import dashboardCard11 from './components/dashboard-card-11';

const mountWfhMonitoringWall = () => {
  const root = document.getElementById('wfh-monitoring-wall');

  if (!root || root.__wfhMonitoringVue) {
    return;
  }

  const app = createApp(WfhMonitoringWall, {
    apiBase: root.dataset.apiBase,
    initialDate: root.dataset.initialDate,
    wallUrl: root.dataset.wallUrl,
    iceServers: JSON.parse(root.dataset.iceServers || '[]'),
  });

  app.mount(root);
  root.__wfhMonitoringVue = app;
};

document.addEventListener('DOMContentLoaded', mountWfhMonitoringWall);
document.addEventListener('livewire:navigated', mountWfhMonitoringWall);

window.wfhMonitoringAdmin = (wire, gpsSelectedSessionId = null, gpsTrailPoints = []) => ({
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
  gpsSelectedSessionId,
  gpsTrailPoints,
  rtcIceServers: [],
  root: null,
  gpsMap: null,
  gpsMapLayer: null,
  gpsMapMarkers: [],
  initFromServer(root) {
    this.root = root;
    this.rtcIceServers = JSON.parse(root?.dataset?.iceServers || '[]');
    this.refreshGpsTrailFromDom();
    this.init();
  },
  rtcPeerConfig() {
    return {
      iceServers: this.rtcIceServers?.length ? this.rtcIceServers : [{ urls: 'stun:stun.l.google.com:19302' }],
      iceTransportPolicy: 'all',
    };
  },
  refreshGpsTrailFromDom() {
    const root = this.root;
    const selectedSessionId = root?.dataset?.gpsSelectedSessionId;
    const trailTemplate = root?.querySelector?.('[data-wfh-gps-trail]');

    this.gpsSelectedSessionId = selectedSessionId && selectedSessionId !== 'null'
      ? Number(selectedSessionId)
      : null;

    if (trailTemplate?.textContent) {
      try {
        this.gpsTrailPoints = JSON.parse(trailTemplate.textContent);
      } catch {
        this.gpsTrailPoints = [];
      }
    }
  },
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
      this.refreshGpsTrailFromDom();
    } catch {
      // Keep the local tab change responsive even if Livewire needs another poll to refresh.
    }
  },
  async selectGpsSession(sessionId) {
    this.gpsSelectedSessionId = sessionId;
    await this.selectMonitoringSession(sessionId, 'gps');
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
        this.gpsMap.setView([14.5995, 120.9842], 12);
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

        marker.bindPopup(`
          <div style="min-width: 180px; font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, sans-serif;">
            <div style="font-weight: 700; color: #0f172a;">${this.escapeHtml(point.label || 'Employee location')}</div>
            <div style="margin-top: 2px; font-size: 12px; color: #64748b;">${this.escapeHtml(point.time || '')}</div>
            ${point.status ? `<div style="margin-top: 4px; font-size: 12px; color: #2563eb;">${this.escapeHtml(point.status)}</div>` : ''}
            ${point.accuracy ? `<div style="margin-top: 4px; font-size: 12px; color: #475569;">Accuracy ${this.escapeHtml(point.accuracy)}</div>` : ''}
          </div>
        `);

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
    await this.selectMonitoringSession(sessionId, 'screens');
    await this.$nextTick();
    this.liveSessionId = sessionId;
    this.liveEmployeeName = employeeName;
    this.liveStatus = 'Opening employee screen stream...';

    let request = null;

    try {
      request = await this.wire.requestLiveScreen(sessionId);
    } catch (error) {
      this.liveStatus = error?.message || 'Unable to open live screen for this session.';
      return;
    }

    if (!request?.token) {
      this.liveStatus = 'Unable to open live screen for this session.';
      return;
    }

    this.liveToken = request.token;
    const peer = new RTCPeerConnection(this.rtcPeerConfig());

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
  async startLiveMedia(sessionId, employeeName = null) {
    if (!window.RTCPeerConnection) {
      this.mediaStatus = 'WebRTC is not supported by this browser.';
      return;
    }

    this.stopLocalLiveMedia(false);
    await this.selectMonitoringSession(sessionId, 'screens');
    await this.$nextTick();
    this.mediaSessionId = sessionId;
    this.mediaEmployeeName = employeeName;
    this.mediaStatus = 'Opening employee camera and microphone...';

    let request = null;

    try {
      request = await this.wire.requestLiveMedia(sessionId);
    } catch (error) {
      this.mediaStatus = error?.message || 'Unable to open camera and microphone for this session.';
      return;
    }

    if (!request?.token) {
      this.mediaStatus = 'Unable to open camera and microphone for this session.';
      return;
    }

    this.mediaToken = request.token;
    const peer = new RTCPeerConnection(this.rtcPeerConfig());

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
    this.mediaStatus = 'Opening employee camera and microphone...';
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

document.addEventListener('alpine:init', () => {
  window.Alpine?.data?.('wfhMonitoringAdmin', window.wfhMonitoringAdmin);
});

const activateWfhMonitoringTab = (root, tabName) => {
  if (!root || !tabName) return;

  root.dataset.activeWfhTab = tabName;

  root.querySelectorAll('[data-wfh-tab-button]').forEach((button) => {
    const isActive = button.dataset.wfhTabButton === tabName;
    button.setAttribute('aria-selected', isActive ? 'true' : 'false');
    button.classList.toggle('wfh-tab-active', isActive);
  });

  root.querySelectorAll('[data-wfh-tab-panel]').forEach((panel) => {
    const isActive = panel.dataset.wfhTabPanel === tabName;
    panel.toggleAttribute('hidden', !isActive);
    panel.style.display = isActive ? '' : 'none';
    panel.removeAttribute('x-cloak');
  });

  if (tabName === 'gps') {
    requestAnimationFrame(() => {
      const alpine = root._x_dataStack?.[0];

      if (alpine?.renderGpsMap) {
        alpine.renderGpsMap(true);
      }
    });
  }
};

const initWfhMonitoringTabs = () => {
  document.querySelectorAll('[data-wfh-monitoring-tabs]').forEach((root) => {
    if (root.dataset.wfhTabsReady === 'true') return;

    root.dataset.wfhTabsReady = 'true';
    activateWfhMonitoringTab(root, root.dataset.activeWfhTab || 'sessions');

    root.addEventListener('click', (event) => {
      const button = event.target.closest('[data-wfh-tab-button]');

      if (!button || !root.contains(button)) return;

      activateWfhMonitoringTab(root, button.dataset.wfhTabButton);
    });
  });
};

document.addEventListener('DOMContentLoaded', initWfhMonitoringTabs);
document.addEventListener('livewire:navigated', initWfhMonitoringTabs);

// Define Chart.js default settings
/* eslint-disable prefer-destructuring */
Chart.defaults.font.family = '"Inter", sans-serif';
Chart.defaults.font.weight = '500';
Chart.defaults.plugins.tooltip.borderWidth = 1;
Chart.defaults.plugins.tooltip.displayColors = false;
Chart.defaults.plugins.tooltip.mode = 'nearest';
Chart.defaults.plugins.tooltip.intersect = false;
Chart.defaults.plugins.tooltip.position = 'nearest';
Chart.defaults.plugins.tooltip.caretSize = 0;
Chart.defaults.plugins.tooltip.caretPadding = 20;
Chart.defaults.plugins.tooltip.cornerRadius = 4;
Chart.defaults.plugins.tooltip.padding = 8;

// Register Chart.js plugin to add a bg option for chart area
Chart.register({
  id: 'chartAreaPlugin',
  // eslint-disable-next-line object-shorthand
  beforeDraw: (chart) => {
    if (chart.config.options.chartArea && chart.config.options.chartArea.backgroundColor) {
      const ctx = chart.canvas.getContext('2d');
      const { chartArea } = chart;
      ctx.save();
      ctx.fillStyle = chart.config.options.chartArea.backgroundColor;
      // eslint-disable-next-line max-len
      ctx.fillRect(chartArea.left, chartArea.top, chartArea.right - chartArea.left, chartArea.bottom - chartArea.top);
      ctx.restore();
    }
  },
});

document.addEventListener('DOMContentLoaded', () => {
  // Light switcher
  const lightSwitches = document.querySelectorAll('.light-switch');
  if (lightSwitches.length > 0) {
    lightSwitches.forEach((lightSwitch, i) => {
      if (localStorage.getItem('dark-mode') === 'true') {
        lightSwitch.checked = true;
      }
      lightSwitch.addEventListener('change', () => {
        const { checked } = lightSwitch;
        lightSwitches.forEach((el, n) => {
          if (n !== i) {
            el.checked = checked;
          }
        });
        document.documentElement.classList.add('[&_*]:!transition-none');
        if (lightSwitch.checked) {
          document.documentElement.classList.add('dark');
          document.querySelector('html').style.colorScheme = 'dark';
          localStorage.setItem('dark-mode', true);
          document.dispatchEvent(new CustomEvent('darkMode', { detail: { mode: 'on' } }));
        } else {
          document.documentElement.classList.remove('dark');
          document.querySelector('html').style.colorScheme = 'light';
          localStorage.setItem('dark-mode', false);
          document.dispatchEvent(new CustomEvent('darkMode', { detail: { mode: 'off' } }));
        }
        setTimeout(() => {
          document.documentElement.classList.remove('[&_*]:!transition-none');
        }, 1);
      });
    });
  }
  // Flatpickr
  flatpickr('.datepicker', {
    mode: 'range',
    static: true,
    monthSelectorType: 'static',
    dateFormat: 'M j, Y',
    defaultDate: [new Date().setDate(new Date().getDate() - 6), new Date()],
    prevArrow: '<svg class="fill-current" width="7" height="11" viewBox="0 0 7 11"><path d="M5.4 10.8l1.4-1.4-4-4 4-4L5.4 0 0 5.4z" /></svg>',
    nextArrow: '<svg class="fill-current" width="7" height="11" viewBox="0 0 7 11"><path d="M1.4 10.8L0 9.4l4-4-4-4L1.4 0l5.4 5.4z" /></svg>',
    onReady: (selectedDates, dateStr, instance) => {
      // eslint-disable-next-line no-param-reassign
      instance.element.value = dateStr.replace('to', '-');
      const customClass = instance.element.getAttribute('data-class');
      instance.calendarContainer.classList.add(customClass);
    },
    onChange: (selectedDates, dateStr, instance) => {
      // eslint-disable-next-line no-param-reassign
      instance.element.value = dateStr.replace('to', '-');
    },
  });
  dashboardCard01();
  dashboardCard02();
  dashboardCard03();
  dashboardCard04();
  dashboardCard05();
  dashboardCard06();
  dashboardCard08();
  dashboardCard09();
  dashboardCard11();
});
