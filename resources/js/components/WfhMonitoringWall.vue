<template>
  <section ref="wallRoot" class="wfh-wall w-full">
    <div class="wfh-wall__shell">
      <header class="wfh-wall__header">
        <div>
          <p class="wfh-wall__eyebrow">WFH Monitoring</p>
          <h1 class="wfh-wall__title">Live Screen Command Center</h1>
        </div>

        <div class="wfh-wall__actions">
          <label class="wfh-wall__search">
            <i class="bi bi-search"></i>
            <input v-model.trim="search" type="search" placeholder="Search employee or ID" @keydown.enter="loadSessions" />
          </label>
          <input v-model="selectedDate" class="wfh-wall__date" type="date" @change="loadSessions" />
          <button class="wfh-wall__button wfh-wall__button--muted" type="button" @click="loadSessions">
            <i class="bi bi-arrow-clockwise"></i>
            Refresh
          </button>
          <a v-if="wallUrl" class="wfh-wall__button wfh-wall__button--muted" :href="wallUrl" target="_blank" rel="noopener">
            <i class="bi bi-box-arrow-up-right"></i>
            Open wall
          </a>
          <button class="wfh-wall__button wfh-wall__button--primary" type="button" @click="toggleFullscreen">
            <i :class="isFullscreen ? 'bi bi-fullscreen-exit' : 'bi bi-fullscreen'"></i>
            {{ isFullscreen ? 'Exit' : 'Fullscreen' }}
          </button>
        </div>
      </header>

      <div class="wfh-wall__stats">
        <article v-for="stat in statCards" :key="stat.key" :class="['wfh-wall__stat', `is-${stat.key}`]">
          <span>{{ stat.label }}</span>
          <strong>{{ stat.value }}</strong>
        </article>
      </div>

      <div v-if="errorMessage" class="wfh-wall__alert">
        <i class="bi bi-exclamation-triangle"></i>
        <span>{{ errorMessage }}</span>
      </div>

      <main class="wfh-wall__layout">
        <aside class="wfh-wall__roster">
          <div class="wfh-wall__panel-head">
            <div>
              <strong>Employees</strong>
              <span>{{ sessions.length }} monitored today</span>
            </div>
            <span class="wfh-wall__pulse" :class="{ live: isAutoRefreshing }"></span>
          </div>

          <div class="wfh-wall__employee-list">
            <button
              v-for="session in sessions"
              :key="session.id"
              :class="['wfh-wall__employee', { selected: selectedSessionId === session.id }]"
              type="button"
              @click="selectSession(session.id)"
            >
              <span :class="['wfh-wall__state-dot', stateClass(session.state)]"></span>
              <span class="wfh-wall__employee-main">
                <strong>{{ session.employee?.name || 'Unknown employee' }}</strong>
                <small>{{ session.employee?.empCode || 'No employee ID' }}</small>
              </span>
              <span class="wfh-wall__employee-side">
                <span :class="['wfh-wall__badge', stateClass(session.state)]">{{ session.state }}</span>
                <small>{{ relativeTime(session.lastActivityAt) }}</small>
              </span>
            </button>
          </div>
        </aside>

        <section class="wfh-wall__viewer">
          <div class="wfh-wall__viewer-head">
            <div>
              <p>Selected Live Feed</p>
              <h2>{{ selectedSession?.employee?.name || 'No employee selected' }}</h2>
              <span>{{ selectedSession?.employee?.empCode || 'Choose an active employee to open a live screen feed' }}</span>
            </div>

            <div class="wfh-wall__button-row">
              <button class="wfh-wall__button wfh-wall__button--success" type="button" @click="startLiveScreen" :disabled="!selectedSession || liveBusy || liveConnected">
                <i class="bi bi-play-circle"></i>
                Start live feed
              </button>
              <button class="wfh-wall__button wfh-wall__button--danger" type="button" @click="stopLiveScreen" :disabled="!selectedSession || liveBusy || !liveSessionId">
                <i class="bi bi-stop-circle"></i>
                Stop feed
              </button>
            </div>
          </div>

          <div class="wfh-wall__video-frame">
            <video ref="liveVideo" autoplay playsinline muted></video>
            <div v-if="!liveConnected" class="wfh-wall__video-empty">
              <i class="bi bi-display"></i>
              <strong>{{ liveStatusTitle }}</strong>
              <span>{{ liveStatus }}</span>
            </div>
            <div class="wfh-wall__live-caption">
              <span :class="['wfh-wall__live-dot', { connected: liveConnected }]"></span>
              <strong>{{ liveConnected ? 'Live screen connected' : 'Live feed standby' }}</strong>
              <small>{{ liveStatus }}</small>
            </div>
          </div>
        </section>

        <aside class="wfh-wall__side">
          <article class="wfh-wall__detail-card">
            <h3>Session</h3>
            <dl>
              <div><dt>Status</dt><dd><span :class="['wfh-wall__badge', stateClass(selectedSession?.state)]">{{ selectedSession?.state || '-' }}</span></dd></div>
              <div><dt>Work mode</dt><dd>{{ selectedSession?.workStatus || '-' }}</dd></div>
              <div><dt>Online</dt><dd>{{ duration(selectedSession?.onlineSeconds) }}</dd></div>
              <div><dt>Active</dt><dd>{{ duration(selectedSession?.activeSeconds) }}</dd></div>
              <div><dt>Idle</dt><dd>{{ duration(selectedSession?.idleSeconds) }}</dd></div>
              <div><dt>Last activity</dt><dd>{{ relativeTime(selectedSession?.lastActivityAt) }}</dd></div>
            </dl>
          </article>

          <article class="wfh-wall__detail-card">
            <div class="wfh-wall__card-head">
              <h3>Current Locations</h3>
              <span>{{ locationSessions.length }} with GPS</span>
            </div>
            <div ref="mapEl" class="wfh-wall__map"></div>
            <div class="wfh-wall__location-list">
              <button
                v-for="session in locationSessions"
                :key="`loc-${session.id}`"
                type="button"
                :class="{ selected: selectedSessionId === session.id }"
                @click="selectSession(session.id)"
              >
                <span :class="['wfh-wall__state-dot', stateClass(session.state)]"></span>
                <span>
                  <strong>{{ session.employee?.name || 'Unknown employee' }}</strong>
                  <small>{{ session.lastLocation?.status || session.lastLocation?.label || 'Location available' }}</small>
                </span>
              </button>
              <p v-if="!locationSessions.length">No employee location pings yet.</p>
            </div>
          </article>

          <article class="wfh-wall__detail-card">
            <h3>Screen Matrix</h3>
            <div class="wfh-wall__mini-grid">
              <button
                v-for="session in sessions"
                :key="`mini-${session.id}`"
                :class="['wfh-wall__mini-tile', { selected: selectedSessionId === session.id }]"
                type="button"
                @click="selectSession(session.id)"
              >
                <span :class="['wfh-wall__state-dot', stateClass(session.state)]"></span>
                <strong>{{ initials(session.employee?.name) }}</strong>
                <small>{{ session.employee?.empCode || session.id }}</small>
              </button>
            </div>
          </article>

          <article class="wfh-wall__detail-card">
            <h3>Recent Activity</h3>
            <ol class="wfh-wall__events">
              <li v-for="event in selectedEvents" :key="event.id">
                <span></span>
                <div>
                  <strong>{{ event.label || event.type }}</strong>
                  <small>{{ formatTime(event.occurredAt) }}</small>
                </div>
              </li>
              <li v-if="!selectedEvents.length" class="empty">No recent events.</li>
            </ol>
          </article>
        </aside>
      </main>
    </div>
  </section>
</template>

<script setup>
import { computed, nextTick, onBeforeUnmount, onMounted, ref, watch } from 'vue';

const props = defineProps({
  apiBase: {
    type: String,
    required: true,
  },
  initialDate: {
    type: String,
    default: '',
  },
  wallUrl: {
    type: String,
    default: '',
  },
});

const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
const wallRoot = ref(null);
const liveVideo = ref(null);
const mapEl = ref(null);
const selectedDate = ref(props.initialDate || new Date().toISOString().slice(0, 10));
const search = ref('');
const sessions = ref([]);
const stats = ref({});
const selectedSessionId = ref(null);
const selectedDetails = ref(null);
const selectedEvents = ref([]);
const errorMessage = ref('');
const liveStatus = ref('Select an employee and start a live feed.');
const liveBusy = ref(false);
const liveConnected = ref(false);
const liveSessionId = ref(null);
const liveToken = ref(null);
const livePeer = ref(null);
const isFullscreen = ref(false);
const isAutoRefreshing = ref(false);
const refreshTimer = ref(null);
const signalTimer = ref(null);
const searchTimer = ref(null);
const leafletReady = ref(false);
let locationMap = null;
let locationLayer = null;

const statCards = computed(() => [
  { key: 'total', label: 'Total', value: stats.value.total || 0 },
  { key: 'active', label: 'Active', value: stats.value.active || 0 },
  { key: 'afk', label: 'AFK', value: stats.value.afk || 0 },
  { key: 'onBreak', label: 'On Break', value: stats.value.onBreak || 0 },
  { key: 'screenOff', label: 'Screen Off', value: stats.value.screenOff || 0 },
  { key: 'geofenceAlerts', label: 'Geofence', value: stats.value.geofenceAlerts || 0 },
]);

const selectedSession = computed(() => {
  return selectedDetails.value || sessions.value.find((session) => session.id === selectedSessionId.value) || null;
});

const locationSessions = computed(() => {
  return sessions.value.filter((session) => {
    return Number.isFinite(Number(session.lastLocation?.lat)) && Number.isFinite(Number(session.lastLocation?.lng));
  });
});

const liveStatusTitle = computed(() => {
  if (!selectedSession.value) return 'No employee selected';
  if (liveBusy.value) return 'Connecting live feed';
  if (liveSessionId.value) return 'Waiting for employee browser';
  return 'Live feed not started';
});

const apiUrl = (path, params = {}) => {
  const url = new URL(`${props.apiBase.replace(/\/$/, '')}/${path.replace(/^\//, '')}`, window.location.origin);
  Object.entries(params).forEach(([key, value]) => {
    if (value !== undefined && value !== null && value !== '') {
      url.searchParams.set(key, value);
    }
  });
  return url.toString();
};

const apiFetch = async (path, options = {}) => {
  const response = await fetch(apiUrl(path, options.params), {
    method: options.method || 'GET',
    headers: {
      Accept: 'application/json',
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': csrfToken,
      ...(options.headers || {}),
    },
    credentials: 'same-origin',
    body: options.body ? JSON.stringify(options.body) : undefined,
  });

  if (!response.ok) {
    const payload = await response.json().catch(() => ({}));
    throw new Error(payload.message || `Request failed with status ${response.status}`);
  }

  return response.json();
};

const loadSessions = async ({ silent = false } = {}) => {
  if (!silent) errorMessage.value = '';
  isAutoRefreshing.value = true;

  try {
    const payload = await apiFetch('/sessions', {
      params: {
        date: selectedDate.value,
        search: search.value,
        limit: 120,
      },
    });

    stats.value = payload.stats || {};
    sessions.value = payload.sessions || [];

    if (!selectedSessionId.value && sessions.value.length) {
      selectedSessionId.value = sessions.value[0].id;
      await loadSelectedDetails();
    } else if (selectedSessionId.value && !sessions.value.some((session) => session.id === selectedSessionId.value)) {
      selectedSessionId.value = sessions.value[0]?.id || null;
      await loadSelectedDetails();
    }
  } catch (error) {
    if (!silent) errorMessage.value = error.message || 'Unable to load WFH monitoring sessions.';
  } finally {
    isAutoRefreshing.value = false;
  }
};

const loadSelectedDetails = async ({ silent = false } = {}) => {
  if (!selectedSessionId.value) {
    selectedDetails.value = null;
    selectedEvents.value = [];
    return;
  }

  try {
    const payload = await apiFetch(`/sessions/${selectedSessionId.value}`);
    selectedDetails.value = payload.session || null;
    selectedEvents.value = payload.events || [];
  } catch (error) {
    if (!silent) errorMessage.value = error.message || 'Unable to load selected employee details.';
  }
};

const selectSession = async (sessionId) => {
  if (sessionId !== selectedSessionId.value) {
    await stopLiveScreen({ report: true, resetStatus: false });
  }

  selectedSessionId.value = sessionId;
  liveStatus.value = 'Ready to start live feed.';
  await loadSelectedDetails();
};

const waitForIceGathering = async (peer) => {
  if (peer.iceGatheringState === 'complete') return;

  await new Promise((resolve) => {
    const timeout = window.setTimeout(resolve, 3000);
    peer.addEventListener('icegatheringstatechange', () => {
      if (peer.iceGatheringState === 'complete') {
        window.clearTimeout(timeout);
        resolve();
      }
    });
  });
};

const startLiveScreen = async () => {
  if (!selectedSessionId.value || !window.RTCPeerConnection) {
    liveStatus.value = 'This browser does not support live screen viewing.';
    return;
  }

  await stopLiveScreen({ report: false, resetStatus: false });
  liveBusy.value = true;
  errorMessage.value = '';
  liveStatus.value = 'Requesting employee live screen permission...';

  try {
    const request = await apiFetch(`/sessions/${selectedSessionId.value}/live-screen/request`, { method: 'POST' });

    if (!request?.token) {
      throw new Error('Unable to create live feed request.');
    }

    liveSessionId.value = selectedSessionId.value;
    liveToken.value = request.token;

    const peer = new RTCPeerConnection({
      iceServers: [{ urls: 'stun:stun.l.google.com:19302' }],
    });

    peer.addTransceiver('video', { direction: 'recvonly' });
    peer.ontrack = async (event) => {
      const stream = event.streams?.[0] || new MediaStream([event.track]);
      await nextTick();

      if (liveVideo.value) {
        liveVideo.value.srcObject = stream;
        liveVideo.value.play().catch(() => {});
      }

      liveConnected.value = true;
      liveStatus.value = 'Receiving employee screen in real time.';
    };
    peer.onconnectionstatechange = () => {
      const state = peer.connectionState;

      if (['connected', 'completed'].includes(state)) {
        liveConnected.value = true;
        liveStatus.value = 'Receiving employee screen in real time.';
        return;
      }

      if (['failed', 'disconnected', 'closed'].includes(state)) {
        liveConnected.value = false;
        liveStatus.value = state === 'closed' ? 'Live feed stopped.' : 'Live feed disconnected.';
        return;
      }

      liveStatus.value = `Live feed ${state}.`;
    };

    const offer = await peer.createOffer();
    await peer.setLocalDescription(offer);
    await waitForIceGathering(peer);
    await apiFetch(`/sessions/${selectedSessionId.value}/live-screen/offer`, {
      method: 'POST',
      body: {
        token: liveToken.value,
        offer: peer.localDescription.toJSON(),
      },
    });

    livePeer.value = peer;
    liveStatus.value = 'Waiting for employee browser to answer...';
    startSignalPolling();
  } catch (error) {
    await stopLiveScreen({ report: true, resetStatus: false });
    errorMessage.value = error.message || 'Unable to start live feed.';
    liveStatus.value = errorMessage.value;
  } finally {
    liveBusy.value = false;
  }
};

const pollLiveSignal = async () => {
  if (!liveSessionId.value || !liveToken.value || !livePeer.value) return;

  try {
    const payload = await apiFetch(`/sessions/${liveSessionId.value}/live-screen/signal`);
    const signal = payload.signal || null;

    if (signal?.token !== liveToken.value) return;

    if (signal.answer && !livePeer.value.currentRemoteDescription) {
      await livePeer.value.setRemoteDescription(new RTCSessionDescription(signal.answer));
      liveStatus.value = 'Connecting live screen stream...';
    }

    if (signal.status === 'stopped') {
      await stopLiveScreen({ report: false });
    }
  } catch {
    liveStatus.value = 'Live feed signal check failed.';
  }
};

const startSignalPolling = () => {
  stopSignalPolling();
  pollLiveSignal();
  signalTimer.value = window.setInterval(pollLiveSignal, 1200);
};

const stopSignalPolling = () => {
  if (signalTimer.value) {
    window.clearInterval(signalTimer.value);
    signalTimer.value = null;
  }
};

const stopLiveScreen = async ({ report = true, resetStatus = true } = {}) => {
  stopSignalPolling();

  const sessionId = liveSessionId.value;

  if (livePeer.value) {
    livePeer.value.close();
  }

  if (liveVideo.value) {
    liveVideo.value.srcObject = null;
  }

  livePeer.value = null;
  liveToken.value = null;
  liveSessionId.value = null;
  liveConnected.value = false;

  if (report && sessionId) {
    await apiFetch(`/sessions/${sessionId}/live-screen/stop`, { method: 'POST' }).catch(() => {});
  }

  if (resetStatus) {
    liveStatus.value = 'Live feed stopped.';
  }
};

const loadLeaflet = async () => {
  if (window.L) {
    leafletReady.value = true;
    return;
  }

  if (!document.querySelector('link[data-wfh-leaflet]')) {
    const link = document.createElement('link');
    link.dataset.wfhLeaflet = 'true';
    link.rel = 'stylesheet';
    link.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
    link.integrity = 'sha256-p4NxAoJBhIIN+hmNHrzRCf9tT0L0rGfF8IuF9G+Gx3Y=';
    link.crossOrigin = '';
    document.head.appendChild(link);
  }

  await new Promise((resolve, reject) => {
    const existing = document.querySelector('script[data-wfh-leaflet]');

    if (existing) {
      existing.addEventListener('load', resolve, { once: true });
      existing.addEventListener('error', reject, { once: true });
      return;
    }

    const script = document.createElement('script');
    script.dataset.wfhLeaflet = 'true';
    script.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
    script.integrity = 'sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=';
    script.crossOrigin = '';
    script.onload = resolve;
    script.onerror = reject;
    document.head.appendChild(script);
  });

  leafletReady.value = true;
};

const renderLocationMap = async () => {
  if (!leafletReady.value || !window.L || !mapEl.value) return;

  if (!locationMap) {
    locationMap = window.L.map(mapEl.value, {
      zoomControl: true,
      scrollWheelZoom: true,
      attributionControl: false,
    });

    window.L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 19,
    }).addTo(locationMap);

    locationLayer = window.L.layerGroup().addTo(locationMap);
  }

  locationLayer.clearLayers();

  const points = locationSessions.value.map((session) => {
    return {
      session,
      lat: Number(session.lastLocation.lat),
      lng: Number(session.lastLocation.lng),
    };
  });

  if (!points.length) {
    locationMap.setView([14.5995, 120.9842], 11);
    setTimeout(() => locationMap?.invalidateSize(true), 120);
    return;
  }

  const bounds = [];

  points.forEach(({ session, lat, lng }) => {
    bounds.push([lat, lng]);

    const marker = window.L.circleMarker([lat, lng], {
      radius: selectedSessionId.value === session.id ? 11 : 8,
      color: selectedSessionId.value === session.id ? '#2563eb' : '#0f766e',
      weight: 3,
      fillColor: selectedSessionId.value === session.id ? '#93c5fd' : '#5eead4',
      fillOpacity: 0.95,
    }).addTo(locationLayer);

    marker.bindPopup(`
      <div style="min-width: 180px; font-family: Inter, ui-sans-serif, system-ui;">
        <div style="font-weight: 800; color: #0f172a;">${escapeHtml(session.employee?.name || 'Unknown employee')}</div>
        <div style="margin-top: 3px; color: #64748b; font-size: 12px;">${escapeHtml(session.employee?.empCode || '')}</div>
        <div style="margin-top: 6px; color: #0f766e; font-size: 12px; font-weight: 700;">${escapeHtml(session.lastLocation?.status || session.lastLocation?.label || 'Location available')}</div>
      </div>
    `);

    marker.on('click', () => selectSession(session.id));
  });

  if (bounds.length === 1) {
    locationMap.setView(bounds[0], 15);
  } else {
    locationMap.fitBounds(bounds, { padding: [28, 28], maxZoom: 15 });
  }

  setTimeout(() => locationMap?.invalidateSize(true), 120);
};

const escapeHtml = (value = '') => {
  return String(value).replace(/[&<>"']/g, (char) => ({
    '&': '&amp;',
    '<': '&lt;',
    '>': '&gt;',
    '"': '&quot;',
    "'": '&#39;',
  }[char]));
};

const toggleFullscreen = async () => {
  if (!document.fullscreenElement) {
    await wallRoot.value?.requestFullscreen?.();
  } else {
    await document.exitFullscreen?.();
  }
};

const syncFullscreenState = () => {
  isFullscreen.value = Boolean(document.fullscreenElement);
};

const stateClass = (state = '') => {
  const normalized = String(state).toLowerCase();
  if (normalized.includes('active')) return 'state-active';
  if (normalized.includes('break')) return 'state-break';
  if (normalized.includes('afk')) return 'state-afk';
  if (normalized.includes('screen')) return 'state-screen';
  if (normalized.includes('offline') || normalized.includes('ended')) return 'state-offline';
  return 'state-neutral';
};

const formatTime = (value) => {
  if (!value) return '-';
  return new Intl.DateTimeFormat('en-PH', {
    month: 'short',
    day: '2-digit',
    hour: '2-digit',
    minute: '2-digit',
    second: '2-digit',
  }).format(new Date(value));
};

const relativeTime = (value) => {
  if (!value) return 'No activity';
  const seconds = Math.max(0, Math.floor((Date.now() - new Date(value).getTime()) / 1000));
  if (seconds < 5) return 'Just now';
  if (seconds < 60) return `${seconds}s ago`;
  const minutes = Math.floor(seconds / 60);
  if (minutes < 60) return `${minutes}m ago`;
  return `${Math.floor(minutes / 60)}h ago`;
};

const duration = (seconds = 0) => {
  const value = Math.max(0, Number(seconds || 0));
  const hours = Math.floor(value / 3600);
  const minutes = Math.floor((value % 3600) / 60);
  const secs = Math.floor(value % 60);
  return [hours, minutes, secs].map((part) => String(part).padStart(2, '0')).join(':');
};

const initials = (name = '') => {
  return String(name)
    .split(/[,\s]+/)
    .filter(Boolean)
    .slice(0, 2)
    .map((part) => part[0]?.toUpperCase())
    .join('') || '--';
};

watch(search, () => {
  window.clearTimeout(searchTimer.value);
  searchTimer.value = window.setTimeout(() => loadSessions(), 350);
});

watch([sessions, selectedSessionId, leafletReady], () => {
  nextTick(() => renderLocationMap());
}, { deep: true });

onMounted(async () => {
  document.addEventListener('fullscreenchange', syncFullscreenState);
  await loadSessions();
  loadLeaflet()
    .then(() => nextTick(() => renderLocationMap()))
    .catch(() => {
      leafletReady.value = false;
    });
  refreshTimer.value = window.setInterval(() => {
    loadSessions({ silent: true });
    loadSelectedDetails({ silent: true });
  }, 5000);
});

onBeforeUnmount(() => {
  document.removeEventListener('fullscreenchange', syncFullscreenState);
  window.clearInterval(refreshTimer.value);
  window.clearTimeout(searchTimer.value);
  stopLiveScreen({ report: true });
  if (locationMap) {
    locationMap.remove();
    locationMap = null;
    locationLayer = null;
  }
});
</script>

<style scoped>
.wfh-wall {
  --wall-bg: #f4f7fb;
  --wall-shell: #ffffff;
  --wall-panel: #ffffff;
  --wall-panel-strong: #f8fafc;
  --wall-border: rgba(100, 116, 139, 0.22);
  --wall-text: #0f172a;
  --wall-muted: #64748b;
  --wall-soft: #e2e8f0;
  --wall-input: #ffffff;
  --wall-video: #e5e7eb;
  --wall-shadow: 0 24px 70px rgba(15, 23, 42, 0.14);
  color: var(--wall-text);
}

:global(.dark) .wfh-wall {
  --wall-bg: #07111f;
  --wall-shell: #07111f;
  --wall-panel: #0b1628;
  --wall-panel-strong: #111d30;
  --wall-border: rgba(148, 163, 184, 0.22);
  --wall-text: #e5eefb;
  --wall-muted: #9fb0c6;
  --wall-soft: #1e293b;
  --wall-input: #101c2f;
  --wall-video: #020617;
  --wall-shadow: 0 24px 90px rgba(2, 6, 23, 0.28);
}

.wfh-wall__shell {
  width: min(100%, 1800px);
  min-height: calc(100dvh - 8.75rem);
  border: 1px solid var(--wall-border);
  border-radius: 14px;
  background: var(--wall-shell);
  box-shadow: var(--wall-shadow);
  overflow: hidden;
}

.wfh-wall__header,
.wfh-wall__stats,
.wfh-wall__layout {
  padding-left: clamp(14px, 1.5vw, 24px);
  padding-right: clamp(14px, 1.5vw, 24px);
}

.wfh-wall__header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  padding-top: 18px;
  padding-bottom: 14px;
  border-bottom: 1px solid var(--wall-border);
  background: var(--wall-panel);
}

.wfh-wall__eyebrow {
  color: #0284c7;
  font-size: 12px;
  font-weight: 800;
  letter-spacing: 0;
  text-transform: uppercase;
}

:global(.dark) .wfh-wall__eyebrow {
  color: #67e8f9;
}

.wfh-wall__title {
  margin-top: 4px;
  color: var(--wall-text);
  font-size: clamp(22px, 2vw, 30px);
  line-height: 1.1;
  font-weight: 800;
  letter-spacing: 0;
}

.wfh-wall__actions,
.wfh-wall__button-row {
  display: flex;
  align-items: center;
  gap: 10px;
  flex-wrap: wrap;
}

.wfh-wall__search,
.wfh-wall__date,
.wfh-wall__button {
  height: 40px;
  border-radius: 8px;
  border: 1px solid var(--wall-border);
  background: var(--wall-input);
  color: var(--wall-text);
}

.wfh-wall__search {
  display: flex;
  align-items: center;
  gap: 8px;
  min-width: min(270px, 100%);
  padding: 0 12px;
}

.wfh-wall__search input {
  width: 100%;
  min-width: 0;
  border: 0;
  outline: none;
  background: transparent;
  color: inherit;
  font-size: 14px;
}

.wfh-wall__date {
  padding: 0 12px;
  font-size: 14px;
}

.wfh-wall__button {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  padding: 0 14px;
  font-size: 13px;
  font-weight: 800;
  transition: transform 120ms ease, opacity 120ms ease;
}

.wfh-wall__button:disabled {
  opacity: 0.45;
  cursor: not-allowed;
}

.wfh-wall__button:not(:disabled):hover {
  transform: translateY(-1px);
}

.wfh-wall__button--primary {
  background: #2563eb;
  border-color: #2563eb;
  color: #fff;
}

.wfh-wall__button--success {
  background: #10b981;
  border-color: #10b981;
  color: #042318;
}

.wfh-wall__button--danger {
  background: #e11d48;
  border-color: #e11d48;
  color: #fff;
}

.wfh-wall__button--muted {
  background: var(--wall-panel-strong);
}

.wfh-wall__stats {
  display: grid;
  grid-template-columns: repeat(6, minmax(110px, 1fr));
  gap: 10px;
  padding-top: 14px;
  padding-bottom: 14px;
  background: var(--wall-bg);
}

.wfh-wall__stat {
  min-height: 76px;
  border: 1px solid var(--wall-border);
  border-radius: 10px;
  padding: 12px;
  background: var(--wall-panel);
}

.wfh-wall__stat span {
  display: block;
  color: var(--wall-muted);
  font-size: 12px;
  font-weight: 800;
  text-transform: uppercase;
}

.wfh-wall__stat strong {
  display: block;
  margin-top: 8px;
  color: var(--wall-text);
  font-size: 28px;
  line-height: 1;
}

.wfh-wall__stat.is-active strong { color: #059669; }
.wfh-wall__stat.is-afk strong { color: #d97706; }
.wfh-wall__stat.is-onBreak strong { color: #0284c7; }
.wfh-wall__stat.is-screenOff strong { color: #ea580c; }
.wfh-wall__stat.is-geofenceAlerts strong { color: #e11d48; }

.wfh-wall__alert {
  display: flex;
  align-items: center;
  gap: 10px;
  margin: 0 24px 14px;
  border: 1px solid rgba(225, 29, 72, 0.32);
  border-radius: 10px;
  padding: 12px 14px;
  background: rgba(225, 29, 72, 0.1);
  color: #be123c;
  font-weight: 700;
}

:global(.dark) .wfh-wall__alert {
  color: #fecdd3;
}

.wfh-wall__layout {
  display: grid;
  grid-template-columns: minmax(270px, 340px) minmax(0, 1fr) minmax(300px, 360px);
  gap: 14px;
  padding-bottom: 20px;
  background: var(--wall-bg);
}

.wfh-wall__roster,
.wfh-wall__viewer,
.wfh-wall__side,
.wfh-wall__detail-card {
  border: 1px solid var(--wall-border);
  border-radius: 12px;
  background: var(--wall-panel);
}

.wfh-wall__side {
  display: grid;
  gap: 12px;
  align-content: start;
  border: 0;
  background: transparent;
}

.wfh-wall__panel-head,
.wfh-wall__viewer-head {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  padding: 14px;
  border-bottom: 1px solid var(--wall-border);
}

.wfh-wall__panel-head strong,
.wfh-wall__viewer-head h2 {
  color: var(--wall-text);
}

.wfh-wall__panel-head strong {
  display: block;
  font-size: 15px;
}

.wfh-wall__panel-head span:not(.wfh-wall__pulse),
.wfh-wall__viewer-head p,
.wfh-wall__viewer-head span {
  color: var(--wall-muted);
  font-size: 12px;
}

.wfh-wall__viewer-head h2 {
  margin-top: 2px;
  font-size: clamp(20px, 2vw, 28px);
  font-weight: 800;
  line-height: 1.1;
}

.wfh-wall__pulse,
.wfh-wall__state-dot,
.wfh-wall__live-dot {
  width: 10px;
  height: 10px;
  border-radius: 999px;
  background: #64748b;
}

.wfh-wall__pulse.live,
.wfh-wall__live-dot.connected {
  background: #10b981;
  box-shadow: 0 0 0 6px rgba(16, 185, 129, 0.14);
}

.wfh-wall__employee-list {
  display: grid;
  gap: 8px;
  max-height: min(64dvh, 720px);
  overflow: auto;
  padding: 10px;
}

.wfh-wall__employee {
  display: grid;
  grid-template-columns: auto minmax(0, 1fr) auto;
  align-items: center;
  gap: 10px;
  width: 100%;
  border: 1px solid transparent;
  border-radius: 10px;
  padding: 11px;
  background: var(--wall-panel-strong);
  text-align: left;
}

.wfh-wall__employee.selected {
  border-color: #3b82f6;
  box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.12);
}

.wfh-wall__employee-main,
.wfh-wall__employee-side {
  min-width: 0;
}

.wfh-wall__employee-main strong {
  display: block;
  overflow: hidden;
  color: var(--wall-text);
  font-size: 14px;
  white-space: nowrap;
  text-overflow: ellipsis;
}

.wfh-wall__employee-main small,
.wfh-wall__employee-side small {
  display: block;
  margin-top: 3px;
  color: var(--wall-muted);
  font-size: 12px;
}

.wfh-wall__employee-side {
  text-align: right;
}

.wfh-wall__badge {
  display: inline-flex;
  align-items: center;
  min-height: 24px;
  border-radius: 999px;
  padding: 0 10px;
  font-size: 12px;
  font-weight: 800;
  white-space: nowrap;
}

.state-active { background-color: #10b981; color: #042318; }
.state-break { background-color: #38bdf8; color: #082f49; }
.state-afk { background-color: #f59e0b; color: #231600; }
.state-screen { background-color: #fb923c; color: #2d1604; }
.state-offline { background-color: #f43f5e; color: #fff1f2; }
.state-neutral { background-color: #64748b; color: #f8fafc; }

.wfh-wall__video-frame {
  position: relative;
  display: grid;
  place-items: center;
  min-height: min(66dvh, 760px);
  aspect-ratio: 16 / 9;
  margin: 14px;
  border: 1px solid var(--wall-border);
  border-radius: 12px;
  background: var(--wall-video);
  overflow: hidden;
}

.wfh-wall__video-frame video {
  width: 100%;
  height: 100%;
  object-fit: contain;
  background: #020617;
}

.wfh-wall__video-empty {
  position: absolute;
  inset: 0;
  display: grid;
  place-content: center;
  gap: 8px;
  padding: 24px;
  color: var(--wall-muted);
  text-align: center;
}

.wfh-wall__video-empty i {
  color: #0284c7;
  font-size: 34px;
}

:global(.dark) .wfh-wall__video-empty i {
  color: #38bdf8;
}

.wfh-wall__video-empty strong {
  color: var(--wall-text);
  font-size: 18px;
}

.wfh-wall__live-caption {
  position: absolute;
  right: 14px;
  bottom: 14px;
  display: grid;
  grid-template-columns: auto auto;
  align-items: center;
  gap: 4px 8px;
  max-width: min(460px, calc(100% - 28px));
  border: 1px solid var(--wall-border);
  border-radius: 8px;
  padding: 9px 11px;
  background: color-mix(in srgb, var(--wall-panel) 88%, transparent);
  box-shadow: 0 10px 30px rgba(15, 23, 42, 0.18);
}

.wfh-wall__live-caption small {
  grid-column: 2;
  overflow: hidden;
  color: var(--wall-muted);
  font-size: 12px;
  white-space: nowrap;
  text-overflow: ellipsis;
}

.wfh-wall__live-caption strong {
  color: var(--wall-text);
  font-size: 13px;
}

.wfh-wall__detail-card {
  padding: 14px;
}

.wfh-wall__detail-card h3 {
  color: var(--wall-text);
  font-size: 15px;
  font-weight: 800;
}

.wfh-wall__card-head {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 10px;
}

.wfh-wall__card-head span {
  color: var(--wall-muted);
  font-size: 12px;
  font-weight: 700;
}

.wfh-wall__detail-card dl {
  display: grid;
  gap: 10px;
  margin-top: 12px;
}

.wfh-wall__detail-card dl div {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
}

.wfh-wall__detail-card dt,
.wfh-wall__events small {
  color: var(--wall-muted);
  font-size: 12px;
}

.wfh-wall__detail-card dd {
  color: var(--wall-text);
  font-size: 13px;
  font-weight: 800;
}

.wfh-wall__map {
  height: 240px;
  margin-top: 12px;
  border: 1px solid var(--wall-border);
  border-radius: 10px;
  background: var(--wall-video);
  overflow: hidden;
}

.wfh-wall__location-list {
  display: grid;
  gap: 8px;
  max-height: 170px;
  overflow: auto;
  margin-top: 10px;
}

.wfh-wall__location-list button {
  display: grid;
  grid-template-columns: auto minmax(0, 1fr);
  align-items: center;
  gap: 9px;
  border: 1px solid transparent;
  border-radius: 8px;
  padding: 9px;
  background: var(--wall-panel-strong);
  text-align: left;
}

.wfh-wall__location-list button.selected {
  border-color: #3b82f6;
}

.wfh-wall__location-list strong,
.wfh-wall__location-list small {
  display: block;
  overflow: hidden;
  white-space: nowrap;
  text-overflow: ellipsis;
}

.wfh-wall__location-list strong {
  color: var(--wall-text);
  font-size: 13px;
}

.wfh-wall__location-list small,
.wfh-wall__location-list p {
  color: var(--wall-muted);
  font-size: 12px;
}

.wfh-wall__mini-grid {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 8px;
  margin-top: 12px;
}

.wfh-wall__mini-tile {
  position: relative;
  display: grid;
  place-items: center;
  min-height: 82px;
  border: 1px solid var(--wall-border);
  border-radius: 10px;
  background: var(--wall-video);
}

.wfh-wall__mini-tile.selected {
  border-color: #3b82f6;
}

.wfh-wall__mini-tile .wfh-wall__state-dot {
  position: absolute;
  top: 8px;
  left: 8px;
}

.wfh-wall__mini-tile strong {
  color: var(--wall-text);
  font-size: 18px;
  font-weight: 900;
}

.wfh-wall__mini-tile small {
  color: var(--wall-muted);
  font-size: 11px;
}

.wfh-wall__events {
  display: grid;
  gap: 10px;
  max-height: 280px;
  overflow: auto;
  margin-top: 12px;
}

.wfh-wall__events li {
  display: grid;
  grid-template-columns: auto minmax(0, 1fr);
  gap: 10px;
}

.wfh-wall__events li > span {
  width: 8px;
  height: 8px;
  margin-top: 6px;
  border-radius: 999px;
  background: #0284c7;
}

.wfh-wall__events strong {
  display: block;
  color: var(--wall-text);
  font-size: 13px;
}

.wfh-wall__events .empty {
  display: block;
  color: var(--wall-muted);
  font-size: 13px;
}

.wfh-wall:fullscreen {
  overflow: auto;
  background: var(--wall-bg);
}

.wfh-wall:fullscreen .wfh-wall__shell {
  width: 100%;
  min-height: 100dvh;
  border: 0;
  border-radius: 0;
}

@media (max-width: 1500px) {
  .wfh-wall__layout {
    grid-template-columns: minmax(260px, 320px) minmax(0, 1fr);
  }

  .wfh-wall__side {
    grid-column: 1 / -1;
    grid-template-columns: repeat(3, minmax(0, 1fr));
  }
}

@media (max-width: 1100px) {
  .wfh-wall__stats {
    grid-template-columns: repeat(3, minmax(110px, 1fr));
  }

  .wfh-wall__layout,
  .wfh-wall__side {
    grid-template-columns: 1fr;
  }

  .wfh-wall__employee-list {
    max-height: none;
  }
}

@media (max-width: 760px) {
  .wfh-wall__header,
  .wfh-wall__viewer-head {
    align-items: stretch;
    flex-direction: column;
  }

  .wfh-wall__actions,
  .wfh-wall__button-row {
    width: 100%;
  }

  .wfh-wall__search,
  .wfh-wall__date,
  .wfh-wall__button {
    flex: 1 1 100%;
    width: 100%;
  }

  .wfh-wall__stats {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }

  .wfh-wall__video-frame {
    min-height: 320px;
    aspect-ratio: auto;
  }
}
</style>
