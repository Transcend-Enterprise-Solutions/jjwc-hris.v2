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

      <nav class="wfh-wall__tabs" aria-label="WFH monitoring workspace">
        <button type="button" :class="{ active: activeView === 'monitor' }" @click="activeView = 'monitor'">
          <i class="bi bi-display"></i>
          Live Monitor
        </button>
        <button type="button" :class="{ active: activeView === 'locations' }" @click="activeView = 'locations'">
          <i class="bi bi-geo-alt"></i>
          Location Map
        </button>
      </nav>

      <main v-show="activeView === 'monitor'" class="wfh-wall__layout">
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
              <p>Selected Screen Monitor</p>
              <h2>{{ selectedSession?.employee?.name || 'No employee selected' }}</h2>
              <span>{{ selectedSession?.employee?.empCode || 'Choose an active employee to view the latest screen frame' }}</span>
            </div>

            <div class="wfh-wall__button-row">
              <span class="wfh-wall__auto-badge" :class="{ live: snapshotActive }">
                <i class="bi bi-image"></i>
                {{ snapshotActive ? 'Refreshing every 5s' : 'Latest screenshot mode' }}
              </span>
              <span class="wfh-wall__auto-badge" v-if="snapshotCapturedAt">
                <i class="bi bi-clock"></i>
                {{ relativeTime(snapshotCapturedAt) }}
              </span>
              <button class="wfh-wall__button wfh-wall__button--primary" type="button" @click="startSnapshotMonitor({ force: true })" :disabled="!selectedSession || snapshotBusy">
                <i class="bi bi-play-circle"></i>
                Start
              </button>
              <button class="wfh-wall__button wfh-wall__button--danger" type="button" @click="stopSnapshotMonitor()" :disabled="!selectedSession || snapshotBusy || !snapshotActive">
                <i class="bi bi-stop-circle"></i>
                Stop
              </button>
            </div>
          </div>

          <div class="wfh-wall__snapshot-frame">
            <img v-if="snapshotUrl" :src="snapshotUrl" alt="Latest employee screen frame" />
            <div v-else class="wfh-wall__video-empty">
              <i class="bi bi-display"></i>
              <strong>{{ snapshotEmptyTitle }}</strong>
              <span>{{ snapshotStatus }}</span>
            </div>
            <div class="wfh-wall__snapshot-caption">
              <span :class="['wfh-wall__live-dot', { connected: snapshotActive }]"></span>
              <strong>{{ snapshotStatus }}</strong>
              <small v-if="snapshotCapturedAt">{{ formatTime(snapshotCapturedAt) }}</small>
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
            <h3>Screen Capture</h3>
            <ol class="wfh-wall__steps">
              <li :class="{ done: Boolean(selectedSession) }">
                <i class="bi bi-person-check"></i>
                <span>Employee selected</span>
              </li>
              <li :class="{ done: Boolean(selectedSession?.screenShareActive) }">
                <i class="bi bi-display"></i>
                <span>Screen sharing active</span>
              </li>
              <li :class="{ done: snapshotActive }">
                <i class="bi bi-arrow-repeat"></i>
                <span>5-second capture loop</span>
              </li>
              <li :class="{ done: Boolean(snapshotUrl) }">
                <i class="bi bi-image"></i>
                <span>Latest frame received</span>
              </li>
            </ol>
            <p class="wfh-wall__helper-text">
              The employee browser keeps one latest screenshot only, so storage stays light on shared hosting.
            </p>
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
            <div class="wfh-wall__card-head">
              <h3>Recent Activity</h3>
              <select v-model="activityFilter" class="wfh-wall__activity-filter">
                <option value="all">All</option>
                <option value="screen">Screen</option>
                <option value="location">Location</option>
                <option value="session">Session</option>
                <option value="alerts">Alerts</option>
              </select>
            </div>
            <ol class="wfh-wall__events">
              <li v-for="event in filteredEvents" :key="event.id">
                <span></span>
                <div>
                  <strong>{{ event.label || event.type }}</strong>
                  <small>{{ formatTime(event.occurredAt) }}</small>
                </div>
              </li>
              <li v-if="!filteredEvents.length" class="empty">No matching activity.</li>
            </ol>
          </article>
        </aside>
      </main>

      <main v-show="activeView === 'locations'" class="wfh-wall__location-layout">
        <section class="wfh-wall__map-panel wfh-wall__map-panel--immersive">
          <div class="wfh-wall__viewer-head">
            <div>
              <p>Employee Locations</p>
              <h2>Current GPS Map</h2>
              <span>{{ locationSessions.length }} employee{{ locationSessions.length === 1 ? '' : 's' }} reporting location</span>
            </div>
            <div class="wfh-wall__button-row">
              <button class="wfh-wall__button wfh-wall__button--muted" type="button" @click="fitLocationMap">
                <i class="bi bi-crosshair"></i>
                Recenter
              </button>
              <button class="wfh-wall__button wfh-wall__button--muted" type="button" @click="selectLatestLocation" :disabled="!latestLocationSession">
                <i class="bi bi-broadcast-pin"></i>
                Latest ping
              </button>
              <a v-if="selectedMapsUrl" class="wfh-wall__button wfh-wall__button--primary" :href="selectedMapsUrl" target="_blank" rel="noopener">
                <i class="bi bi-box-arrow-up-right"></i>
                Open maps
              </a>
            </div>
          </div>

          <div class="wfh-wall__map-metrics">
            <article v-for="metric in locationMetricCards" :key="metric.key" :class="['wfh-wall__map-metric', `is-${metric.key}`]">
              <span>{{ metric.label }}</span>
              <strong>{{ metric.value }}</strong>
            </article>
          </div>

          <div class="wfh-wall__map-wrap">
            <div ref="mapEl" class="wfh-wall__map"></div>
            <div v-if="mapLoading" class="wfh-wall__map-overlay">
              <span class="wfh-wall__spinner"></span>
              <strong>Loading map</strong>
              <small>Preparing the live employee location wall.</small>
            </div>
            <div v-if="mapError" class="wfh-wall__map-overlay is-error">
              <i class="bi bi-exclamation-triangle"></i>
              <strong>Map unavailable</strong>
              <small>{{ mapError }}</small>
              <button type="button" @click="renderLocationMap">Retry map</button>
            </div>
            <div v-if="!locationSessions.length" class="wfh-wall__map-empty">
              <i class="bi bi-geo-alt"></i>
              <strong>No GPS pings yet</strong>
              <span>Locations appear after the employee browser sends a monitoring heartbeat.</span>
            </div>
            <div v-if="!mapLoading && !mapError" class="wfh-wall__map-tools">
              <button
                v-for="type in mapTypeOptions"
                :key="type.key"
                :class="{ active: mapType === type.key }"
                type="button"
                @click="setMapType(type.key)"
              >
                <i :class="type.icon"></i>
                {{ type.label }}
              </button>
            </div>
            <div class="wfh-wall__map-legend">
              <span><i class="inside"></i> Inside</span>
              <span><i class="outside"></i> Outside</span>
              <span><i class="unknown"></i> Unknown</span>
            </div>
          </div>
        </section>

        <aside class="wfh-wall__location-side">
          <article class="wfh-wall__detail-card">
            <div class="wfh-wall__card-head">
              <h3>Map Roster</h3>
              <span>{{ locationSessions.length }} online</span>
            </div>
            <div class="wfh-wall__location-list wfh-wall__location-list--large">
              <button
                v-for="session in locationSessions"
                :key="`loc-${session.id}`"
                type="button"
                :class="{ selected: selectedSessionId === session.id }"
                @click="selectLocationSession(session.id)"
              >
                <span :class="['wfh-wall__state-dot', stateClass(session.state)]"></span>
                <span>
                  <strong>{{ session.employee?.name || 'Unknown employee' }}</strong>
                  <small>{{ locationSummary(session) }}</small>
                </span>
                <em>{{ relativeTime(session.lastLocation?.occurredAt || session.lastActivityAt) }}</em>
              </button>
              <p v-if="!locationSessions.length">No employee location pings yet.</p>
            </div>
          </article>

          <article class="wfh-wall__detail-card">
            <div class="wfh-wall__card-head">
              <h3>Selected Location</h3>
              <span>{{ selectedLocation ? relativeTime(selectedLocation.occurredAt || selectedSession?.lastActivityAt) : '-' }}</span>
            </div>
            <dl>
              <div><dt>Employee</dt><dd>{{ selectedSession?.employee?.name || '-' }}</dd></div>
              <div><dt>Status</dt><dd>{{ selectedLocation?.status || '-' }}</dd></div>
              <div><dt>Latitude</dt><dd>{{ coordinateLabel(selectedLocation?.lat) }}</dd></div>
              <div><dt>Longitude</dt><dd>{{ coordinateLabel(selectedLocation?.lng) }}</dd></div>
              <div><dt>Accuracy</dt><dd>{{ accuracyLabel(selectedLocation?.accuracy) }}</dd></div>
              <div><dt>Source</dt><dd>{{ selectedLocation?.source || 'Browser GPS' }}</dd></div>
            </dl>
            <a v-if="selectedMapsUrl" class="wfh-wall__wide-link" :href="selectedMapsUrl" target="_blank" rel="noopener">
              <i class="bi bi-map"></i>
              View selected location
            </a>
          </article>

          <article class="wfh-wall__detail-card">
            <div class="wfh-wall__card-head">
              <h3>GPS Signal</h3>
              <span>{{ selectedSession?.geofenceStatus || 'unknown' }}</span>
            </div>
            <div class="wfh-wall__signal-card">
              <div>
                <strong>{{ selectedSession?.employee?.empCode || '-' }}</strong>
                <span>{{ selectedSession?.workStatus || 'WFH' }}</span>
              </div>
              <div>
                <strong>{{ duration(selectedSession?.onlineSeconds) }}</strong>
                <span>Online time</span>
              </div>
            </div>
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
  iceServers: {
    type: Array,
    default: () => [{ urls: 'stun:stun.l.google.com:19302' }],
  },
});

const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
const wallRoot = ref(null);
const liveVideo = ref(null);
const liveMediaVideo = ref(null);
const mapEl = ref(null);
const activeView = ref('monitor');
const selectedDate = ref(props.initialDate || new Date().toISOString().slice(0, 10));
const search = ref('');
const sessions = ref([]);
const stats = ref({});
const selectedSessionId = ref(null);
const selectedDetails = ref(null);
const selectedEvents = ref([]);
const activityFilter = ref('all');
const errorMessage = ref('');
const liveStatus = ref('Select an active employee to open live monitoring.');
const liveBusy = ref(false);
const liveConnected = ref(false);
const liveSessionId = ref(null);
const liveToken = ref(null);
const livePeer = ref(null);
const liveRequestSeq = ref(0);
const liveMediaStatus = ref('Select an employee to open camera and microphone.');
const liveMediaBusy = ref(false);
const liveMediaConnected = ref(false);
const liveMediaAudioBlocked = ref(false);
const liveMediaSessionId = ref(null);
const liveMediaToken = ref(null);
const liveMediaPeer = ref(null);
const liveMediaStream = ref(null);
const liveMediaRequestSeq = ref(0);
const isFullscreen = ref(false);
const isAutoRefreshing = ref(false);
const mapType = ref('roadmap');
const mapLoading = ref(false);
const mapError = ref('');
const refreshTimer = ref(null);
const signalTimer = ref(null);
const mediaSignalTimer = ref(null);
const searchTimer = ref(null);
const snapshotTimer = ref(null);
const snapshotSessionId = ref(null);
const snapshotToken = ref(null);
const snapshotUrl = ref('');
const snapshotCapturedAt = ref('');
const snapshotStatus = ref('Select an employee to view their latest screen frame.');
const snapshotBusy = ref(false);
const snapshotActive = ref(false);
let locationMap = null;
let locationBounds = null;
let locationMarkers = new Map();
let locationInfoWindow = null;
let googleMapsLoadPromise = null;

const GOOGLE_MAPS_API_KEY = 'AIzaSyBvGOC4HUPjiDuOE2yr7CwbnC4j6vsa274';
const PH_CENTER = { lat: 12.8797, lng: 121.7740 };
const PH_ZOOM = 6;
const mapTypeOptions = [
  { key: 'roadmap', label: 'Map', icon: 'bi bi-map' },
  { key: 'satellite', label: 'Satellite', icon: 'bi bi-globe-asia-australia' },
  { key: 'terrain', label: 'Terrain', icon: 'bi bi-layers' },
];

const rtcConfiguration = () => ({
  iceServers: props.iceServers?.length ? props.iceServers : [{ urls: 'stun:stun.l.google.com:19302' }],
  iceTransportPolicy: 'all',
});

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

const selectedLocation = computed(() => selectedSession.value?.lastLocation || null);

const latestLocationSession = computed(() => {
  return [...locationSessions.value].sort((a, b) => {
    const first = new Date(a.lastLocation?.occurredAt || a.lastActivityAt || 0).getTime();
    const second = new Date(b.lastLocation?.occurredAt || b.lastActivityAt || 0).getTime();

    return second - first;
  })[0] || null;
});

const locationMetricCards = computed(() => {
  const located = locationSessions.value;

  return [
    { key: 'located', label: 'Located', value: located.length },
    { key: 'inside', label: 'Inside', value: located.filter((session) => locationStatusKind(session) === 'inside').length },
    { key: 'outside', label: 'Outside', value: located.filter((session) => locationStatusKind(session) === 'outside').length },
    { key: 'unknown', label: 'Unknown', value: located.filter((session) => locationStatusKind(session) === 'unknown').length },
  ];
});

const liveStatusTitle = computed(() => {
  if (!selectedSession.value) return 'No employee selected';
  if (liveBusy.value) return 'Opening live feed';
  if (liveStatus.value === 'Employee screen share is not active yet.') return 'Waiting for screen share';
  if (liveSessionId.value) return 'Opening employee screen';
  return 'Live feed not started';
});

const snapshotEmptyTitle = computed(() => {
  if (!selectedSession.value) return 'No employee selected';
  if (snapshotBusy.value) return 'Starting screen monitor';
  if (!selectedSession.value?.screenShareActive) return 'Screen sharing is off';
  return 'Waiting for latest screen frame';
});

const filteredEvents = computed(() => {
  const filter = activityFilter.value;

  if (filter === 'all') return selectedEvents.value;

  return selectedEvents.value.filter((event) => {
    const haystack = `${event.type || ''} ${event.label || ''}`.toLowerCase();

    if (filter === 'screen') return haystack.includes('screen') || haystack.includes('snapshot');
    if (filter === 'location') return haystack.includes('location') || haystack.includes('geofence') || haystack.includes('gps');
    if (filter === 'session') return haystack.includes('session') || haystack.includes('monitoring') || haystack.includes('time');
    if (filter === 'alerts') return haystack.includes('alert') || haystack.includes('denied') || haystack.includes('offline') || haystack.includes('afk');

    return true;
  });
});

const canOpenLiveForSession = (session = selectedSession.value) => {
  const state = String(session?.state || '').toLowerCase();

  return Boolean(session?.id) && !state.includes('offline') && !state.includes('ended');
};

const selectedMapsUrl = computed(() => {
  const location = selectedLocation.value;

  if (!location?.lat || !location?.lng) return '';

  return `https://www.google.com/maps?q=${encodeURIComponent(`${location.lat},${location.lng}`)}`;
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
      await startSnapshotMonitor();
    } else if (selectedSessionId.value && !sessions.value.some((session) => session.id === selectedSessionId.value)) {
      selectedSessionId.value = sessions.value[0]?.id || null;
      await loadSelectedDetails();
      await startSnapshotMonitor();
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
    if (payload.session?.latestScreenshot?.url && payload.session.id === snapshotSessionId.value) {
      applySnapshot(payload.session.latestScreenshot);
    }
  } catch (error) {
    if (!silent) errorMessage.value = error.message || 'Unable to load selected employee details.';
  }
};

const selectSession = async (sessionId) => {
  if (sessionId !== selectedSessionId.value) {
    await stopSnapshotMonitor({ report: true, resetStatus: false });
  }

  selectedSessionId.value = sessionId;
  snapshotStatus.value = 'Opening latest screen frame...';
  await loadSelectedDetails();
  await startSnapshotMonitor({ force: true });
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

const sanitizeRtcDescription = (description) => {
  if (!description?.sdp) return description;

  const lines = String(description.sdp).split(/\r?\n/);
  const blockedPayloads = new Set();

  lines.forEach((line) => {
    const payload = line.match(/^a=(?:rtpmap|fmtp):(\d+)/)?.[1];

    if (!payload) return;

    if (
      /^a=rtpmap:\d+\s+flexfec-03\/90000/i.test(line)
      || /^a=rtpmap:\d+\s+telephone-event\/\d+/i.test(line)
      || /^a=fmtp:\d+.*repair-window=/i.test(line)
    ) {
      blockedPayloads.add(payload);
    }
  });

  if (!blockedPayloads.size) return description;

  const sanitized = lines
    .map((line) => {
      if (!/^m=(?:audio|video) /.test(line)) return line;

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
};

const applySnapshot = (snapshot) => {
  if (!snapshot?.url) return;

  const separator = snapshot.url.includes('?') ? '&' : '?';
  snapshotUrl.value = `${snapshot.url}${separator}v=${Date.now()}`;
  snapshotCapturedAt.value = snapshot.capturedAt || '';
  snapshotStatus.value = 'Latest screen frame received.';
};

const refreshLatestSnapshot = async () => {
  if (!snapshotSessionId.value) return;

  try {
    const payload = await apiFetch(`/sessions/${snapshotSessionId.value}/live-snapshots/latest`);
    if (snapshotSessionId.value !== selectedSessionId.value) return;

    if (payload.snapshot?.url) {
      applySnapshot(payload.snapshot);
      snapshotActive.value = true;
    } else {
      snapshotStatus.value = selectedSession.value?.screenShareActive
        ? 'Waiting for the employee browser to upload the first frame.'
        : 'Employee screen sharing is not active.';
    }
  } catch (error) {
    snapshotStatus.value = error.message || 'Unable to refresh the latest screen frame.';
  }
};

const stopSnapshotPolling = () => {
  if (snapshotTimer.value) {
    window.clearInterval(snapshotTimer.value);
    snapshotTimer.value = null;
  }
};

const startSnapshotPolling = (intervalSeconds = 5) => {
  stopSnapshotPolling();
  refreshLatestSnapshot();
  snapshotTimer.value = window.setInterval(refreshLatestSnapshot, Math.max(5, Number(intervalSeconds || 5)) * 1000);
};

const startSnapshotMonitor = async ({ force = false } = {}) => {
  if (!canOpenLiveForSession()) {
    snapshotStatus.value = selectedSession.value ? 'Selected employee is not available for monitoring.' : 'Select an employee to view their latest screen frame.';
    return;
  }

  if (!force && snapshotSessionId.value === selectedSessionId.value && snapshotActive.value) return;

  stopSnapshotPolling();
  snapshotBusy.value = true;
  snapshotUrl.value = '';
  snapshotCapturedAt.value = '';
  snapshotStatus.value = 'Starting 5-second screen frame refresh...';

  try {
    const sessionId = selectedSessionId.value;
    const request = await apiFetch(`/sessions/${sessionId}/live-snapshots/start`, { method: 'POST' });

    if (sessionId !== selectedSessionId.value) return;

    snapshotSessionId.value = sessionId;
    snapshotToken.value = request.token || null;
    snapshotActive.value = true;
    snapshotStatus.value = 'Waiting for the employee browser to upload the first frame.';
    applySnapshot(request.snapshot);
    startSnapshotPolling(request.intervalSeconds || 5);
  } catch (error) {
    snapshotActive.value = false;
    snapshotStatus.value = error.message || 'Unable to start latest screen frame monitoring.';
  } finally {
    snapshotBusy.value = false;
  }
};

const stopSnapshotMonitor = async ({ report = true, resetStatus = true } = {}) => {
  const sessionId = snapshotSessionId.value;

  stopSnapshotPolling();
  snapshotSessionId.value = null;
  snapshotToken.value = null;
  snapshotActive.value = false;

  if (report && sessionId) {
    await apiFetch(`/sessions/${sessionId}/live-snapshots/stop`, { method: 'POST' }).catch(() => {});
  }

  if (resetStatus) {
    snapshotStatus.value = 'Screen frame monitoring stopped.';
  }
};

const openSelectedLive = async (options = {}) => {
  await startSnapshotMonitor(options);
};

const startLiveScreen = async ({ force = false } = {}) => {
  if (!selectedSessionId.value || !window.RTCPeerConnection) {
    liveStatus.value = 'This browser does not support live screen viewing.';
    return;
  }

  if (!force && liveSessionId.value === selectedSessionId.value && (liveBusy.value || livePeer.value)) {
    return;
  }

  await stopLiveScreen({ report: false, resetStatus: false });
  const sessionId = selectedSessionId.value;
  const requestSeq = liveRequestSeq.value + 1;
  liveRequestSeq.value = requestSeq;
  liveBusy.value = true;
  errorMessage.value = '';
  liveStatus.value = 'Opening selected employee screen...';

  try {
    const request = await apiFetch(`/sessions/${sessionId}/live-screen/request`, { method: 'POST' });

    if (!request?.token) {
      throw new Error('Unable to open live feed.');
    }

    if (requestSeq !== liveRequestSeq.value || sessionId !== selectedSessionId.value) return;

    const token = request.token;
    liveSessionId.value = sessionId;
    liveToken.value = token;

    const peer = new RTCPeerConnection(rtcConfiguration());

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

    if (requestSeq !== liveRequestSeq.value || sessionId !== selectedSessionId.value || liveToken.value !== token) {
      peer.close();
      return;
    }

    await apiFetch(`/sessions/${sessionId}/live-screen/offer`, {
      method: 'POST',
      body: {
        token,
        offer: sanitizeRtcDescription(peer.localDescription.toJSON()),
      },
    });

    if (requestSeq !== liveRequestSeq.value || sessionId !== selectedSessionId.value || liveToken.value !== token) {
      peer.close();
      return;
    }

    livePeer.value = peer;
    liveStatus.value = 'Opening employee screen stream...';
    startSignalPolling();
  } catch (error) {
    if (requestSeq === liveRequestSeq.value) {
      await stopLiveScreen({ report: true, resetStatus: false });
      errorMessage.value = error.message || 'Unable to start live feed.';
      liveStatus.value = errorMessage.value;
    }
  } finally {
    if (requestSeq === liveRequestSeq.value) {
      liveBusy.value = false;
    }
  }
};

const playLiveMedia = () => {
  if (!liveMediaVideo.value) return;

  liveMediaVideo.value.muted = false;
  liveMediaVideo.value.volume = 1;
  liveMediaVideo.value.play()
    .then(() => {
      liveMediaAudioBlocked.value = false;
      if (liveMediaConnected.value) {
        liveMediaStatus.value = 'Receiving camera and microphone in real time.';
      }
    })
    .catch(() => {
      liveMediaAudioBlocked.value = true;
      liveMediaStatus.value = 'Camera is live. Click Enable audio to hear the microphone.';
    });
};

const startLiveMedia = async ({ force = false } = {}) => {
  if (!selectedSessionId.value || !window.RTCPeerConnection) {
    liveMediaStatus.value = 'This browser does not support camera and microphone viewing.';
    return;
  }

  if (!force && liveMediaSessionId.value === selectedSessionId.value && (liveMediaBusy.value || liveMediaPeer.value)) {
    return;
  }

  await stopLiveMedia({ report: false, resetStatus: false });
  const sessionId = selectedSessionId.value;
  const requestSeq = liveMediaRequestSeq.value + 1;
  liveMediaRequestSeq.value = requestSeq;
  liveMediaBusy.value = true;
  liveMediaStatus.value = 'Opening selected employee camera and microphone...';

  try {
    const request = await apiFetch(`/sessions/${sessionId}/live-media/request`, { method: 'POST' });

    if (!request?.token) {
      throw new Error('Unable to open camera and microphone feed.');
    }

    if (requestSeq !== liveMediaRequestSeq.value || sessionId !== selectedSessionId.value) return;

    const token = request.token;
    liveMediaSessionId.value = sessionId;
    liveMediaToken.value = token;

    const peer = new RTCPeerConnection(rtcConfiguration());

    peer.addTransceiver('video', { direction: 'recvonly' });
    peer.addTransceiver('audio', { direction: 'recvonly' });
    peer.ontrack = async (event) => {
      const stream = event.streams?.[0] || liveMediaStream.value || new MediaStream();

      if (!event.streams?.[0] && !stream.getTracks().includes(event.track)) {
        stream.addTrack(event.track);
      }

      liveMediaStream.value = stream;
      await nextTick();

      if (liveMediaVideo.value) {
        liveMediaVideo.value.srcObject = stream;
        playLiveMedia();
      }

      liveMediaConnected.value = true;
      liveMediaStatus.value = 'Receiving camera and microphone in real time.';
    };
    peer.onconnectionstatechange = () => {
      const state = peer.connectionState;

      if (['connected', 'completed'].includes(state)) {
        liveMediaConnected.value = true;
        liveMediaStatus.value = 'Receiving camera and microphone in real time.';
        return;
      }

      if (['failed', 'disconnected', 'closed'].includes(state)) {
        liveMediaConnected.value = false;
        liveMediaStatus.value = state === 'closed' ? 'Camera and microphone feed stopped.' : 'Camera and microphone feed disconnected.';
        return;
      }

      liveMediaStatus.value = `Camera and microphone feed ${state}.`;
    };

    const offer = await peer.createOffer();
    await peer.setLocalDescription(offer);
    await waitForIceGathering(peer);

    if (requestSeq !== liveMediaRequestSeq.value || sessionId !== selectedSessionId.value || liveMediaToken.value !== token) {
      peer.close();
      return;
    }

    await apiFetch(`/sessions/${sessionId}/live-media/offer`, {
      method: 'POST',
      body: {
        token,
        offer: sanitizeRtcDescription(peer.localDescription.toJSON()),
      },
    });

    if (requestSeq !== liveMediaRequestSeq.value || sessionId !== selectedSessionId.value || liveMediaToken.value !== token) {
      peer.close();
      return;
    }

    liveMediaPeer.value = peer;
    liveMediaStatus.value = 'Opening employee camera and microphone...';
    startMediaSignalPolling();
  } catch (error) {
    if (requestSeq === liveMediaRequestSeq.value) {
      await stopLiveMedia({ report: true, resetStatus: false });
      liveMediaStatus.value = error.message || 'Unable to open camera and microphone feed.';
    }
  } finally {
    if (requestSeq === liveMediaRequestSeq.value) {
      liveMediaBusy.value = false;
    }
  }
};

const pollLiveSignal = async () => {
  if (!liveSessionId.value || !liveToken.value || !livePeer.value) return;

  try {
    const payload = await apiFetch(`/sessions/${liveSessionId.value}/live-screen/signal`);
    const signal = payload.signal || null;

    if (signal?.token !== liveToken.value) return;

    if (signal.status === 'answer_failed') {
      liveConnected.value = false;
      liveStatus.value = signal.error || 'Employee browser could not open the live feed.';
      return;
    }

    if (signal.status === 'awaiting_screen_share') {
      liveConnected.value = false;
      liveStatus.value = 'Employee screen share is not active yet.';
      return;
    }

    if (signal.answer && !livePeer.value.currentRemoteDescription) {
      await livePeer.value.setRemoteDescription(new RTCSessionDescription(sanitizeRtcDescription(signal.answer)));
      liveStatus.value = 'Connecting live screen stream...';
    }

    if (signal.status === 'stopped') {
      await stopLiveScreen({ report: false });
    }
  } catch {
    liveStatus.value = 'Live feed signal check failed.';
  }
};

const pollLiveMediaSignal = async () => {
  if (!liveMediaSessionId.value || !liveMediaToken.value || !liveMediaPeer.value) return;

  try {
    const payload = await apiFetch(`/sessions/${liveMediaSessionId.value}/live-media/signal`);
    const signal = payload.signal || null;

    if (signal?.token !== liveMediaToken.value) return;

    if (signal.status === 'answer_failed') {
      liveMediaConnected.value = false;
      liveMediaStatus.value = signal.error || 'Employee browser could not open camera and microphone.';
      return;
    }

    if (signal.answer && !liveMediaPeer.value.currentRemoteDescription) {
      await liveMediaPeer.value.setRemoteDescription(new RTCSessionDescription(sanitizeRtcDescription(signal.answer)));
      liveMediaStatus.value = 'Connecting camera and microphone...';
    }

    if (signal.status === 'stopped') {
      await stopLiveMedia({ report: false });
    }
  } catch {
    liveMediaStatus.value = 'Camera and microphone signal check failed.';
  }
};

const startSignalPolling = () => {
  stopSignalPolling();
  pollLiveSignal();
  signalTimer.value = window.setInterval(pollLiveSignal, 1200);
};

const startMediaSignalPolling = () => {
  stopMediaSignalPolling();
  pollLiveMediaSignal();
  mediaSignalTimer.value = window.setInterval(pollLiveMediaSignal, 1200);
};

const stopSignalPolling = () => {
  if (signalTimer.value) {
    window.clearInterval(signalTimer.value);
    signalTimer.value = null;
  }
};

const stopMediaSignalPolling = () => {
  if (mediaSignalTimer.value) {
    window.clearInterval(mediaSignalTimer.value);
    mediaSignalTimer.value = null;
  }
};

const stopLiveScreen = async ({ report = true, resetStatus = true } = {}) => {
  liveRequestSeq.value += 1;
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

const stopLiveMedia = async ({ report = true, resetStatus = true } = {}) => {
  liveMediaRequestSeq.value += 1;
  stopMediaSignalPolling();

  const sessionId = liveMediaSessionId.value;

  if (liveMediaPeer.value) {
    liveMediaPeer.value.close();
  }

  if (liveMediaVideo.value) {
    liveMediaVideo.value.srcObject = null;
  }

  liveMediaPeer.value = null;
  liveMediaToken.value = null;
  liveMediaSessionId.value = null;
  liveMediaStream.value = null;
  liveMediaConnected.value = false;
  liveMediaAudioBlocked.value = false;

  if (report && sessionId) {
    await apiFetch(`/sessions/${sessionId}/live-media/stop`, { method: 'POST' }).catch(() => {});
  }

  if (resetStatus) {
    liveMediaStatus.value = 'Camera and microphone feed stopped.';
  }
};

const stopSelectedLive = async ({ resetStatus = true } = {}) => {
  await stopSnapshotMonitor({ report: true, resetStatus });
};

const loadGoogleMaps = () => {
  if (window.google?.maps?.Map) {
    return Promise.resolve();
  }

  if (googleMapsLoadPromise) {
    return googleMapsLoadPromise;
  }

  googleMapsLoadPromise = new Promise((resolve, reject) => {
    const existingScript = document.getElementById('wfh-google-maps-script');

    if (existingScript) {
      let attempts = 0;
      const timer = window.setInterval(() => {
        if (window.google?.maps?.Map) {
          window.clearInterval(timer);
          resolve();
        }

        attempts += 1;
        if (attempts > 150) {
          window.clearInterval(timer);
          reject(new Error('Google Maps took too long to load.'));
        }
      }, 100);
      return;
    }

    const callbackName = `__wfhGoogleMapsReady_${Date.now()}`;
    window[callbackName] = () => {
      delete window[callbackName];
      resolve();
    };

    const script = document.createElement('script');
    script.id = 'wfh-google-maps-script';
    script.async = true;
    script.defer = true;
    script.src = `https://maps.googleapis.com/maps/api/js?key=${GOOGLE_MAPS_API_KEY}&libraries=places,geometry&callback=${callbackName}`;
    script.onerror = () => reject(new Error('Failed to load Google Maps.'));
    document.head.appendChild(script);
  });

  return googleMapsLoadPromise;
};

const mapStyles = [
  { featureType: 'poi', elementType: 'labels', stylers: [{ visibility: 'off' }] },
  { featureType: 'transit', elementType: 'labels', stylers: [{ visibility: 'off' }] },
  { featureType: 'water', stylers: [{ color: '#b3d1f5' }] },
  { featureType: 'landscape', stylers: [{ color: '#f5f5f0' }] },
  { featureType: 'road.highway', elementType: 'geometry', stylers: [{ color: '#ffffff' }] },
  { featureType: 'road.arterial', elementType: 'geometry', stylers: [{ color: '#f8f8f8' }] },
  { featureType: 'administrative.country', elementType: 'geometry.stroke', stylers: [{ color: '#94a3b8' }, { weight: 1.2 }] },
  { featureType: 'administrative.province', elementType: 'geometry.stroke', stylers: [{ color: '#cbd5e1' }, { weight: 0.8 }] },
];

const renderLocationMap = async () => {
  if (activeView.value !== 'locations' || !mapEl.value) return;

  const showLoader = !locationMap;
  if (showLoader) {
    mapLoading.value = true;
  }
  mapError.value = '';

  try {
    await loadGoogleMaps();
    await nextTick();

    const G = window.google.maps;

    if (!locationMap) {
      locationMap = new G.Map(mapEl.value, {
        center: PH_CENTER,
        zoom: PH_ZOOM,
        mapTypeId: G.MapTypeId.ROADMAP,
        zoomControl: true,
        mapTypeControl: false,
        streetViewControl: false,
        fullscreenControl: false,
        styles: mapStyles,
      });

      locationInfoWindow = new G.InfoWindow({ disableAutoPan: false });
    }

    locationMarkers.forEach((marker) => marker.setMap(null));
    locationMarkers = new Map();
    locationBounds = new G.LatLngBounds();
    locationMap.setMapTypeId(mapTypeToGoogleId(mapType.value));

    const points = locationSessions.value.map((session) => ({
      session,
      lat: Number(session.lastLocation.lat),
      lng: Number(session.lastLocation.lng),
    }));

    if (!points.length) {
      locationInfoWindow?.close();
      locationMap.setCenter(PH_CENTER);
      locationMap.setZoom(PH_ZOOM);
      return;
    }

    points.forEach(({ session, lat, lng }) => {
      const selected = selectedSessionId.value === session.id;
      const size = selected ? { width: 58, height: 70 } : { width: 48, height: 60 };
      const marker = new G.Marker({
        position: { lat, lng },
        map: locationMap,
        icon: {
          url: toDataUrl(locationPinSvg(session, selected)),
          scaledSize: new G.Size(size.width, size.height),
          anchor: new G.Point(size.width / 2, size.height),
        },
        title: session.employee?.name || 'Employee location',
        zIndex: selected ? 1000 : 50,
      });

      marker.addListener('click', () => selectLocationSession(session.id));
      marker.addListener('mouseover', () => {
        locationInfoWindow?.setContent(locationPopupHtml(session));
        locationInfoWindow?.open({ map: locationMap, anchor: marker });
      });

      locationBounds.extend(marker.getPosition());
      locationMarkers.set(session.id, marker);
    });

    if (points.length === 1) {
      locationMap.setCenter({ lat: points[0].lat, lng: points[0].lng });
      locationMap.setZoom(15);
    } else {
      locationMap.fitBounds(locationBounds, 60);
      G.event.addListenerOnce(locationMap, 'idle', () => {
        if ((locationMap.getZoom() ?? 0) > 15) {
          locationMap.setZoom(15);
        }
      });
    }
  } catch (error) {
    mapError.value = error.message || 'Unable to load the employee location map.';
  } finally {
    if (showLoader) {
      mapLoading.value = false;
    }
  }
};

const locationStatusKind = (session) => {
  const status = String(session?.lastLocation?.status || session?.geofenceStatus || '').toLowerCase();

  if (status.includes('inside')) return 'inside';
  if (status.includes('outside')) return 'outside';
  return 'unknown';
};

const locationPinSvg = (session, selected = false) => {
  const kind = locationStatusKind(session);
  const label = escapeHtml(initials(session.employee?.name));
  const colors = {
    inside: '#10b981',
    outside: '#e11d48',
    unknown: '#64748b',
  };
  const color = colors[kind] || colors.unknown;
  const width = selected ? 58 : 48;
  const height = selected ? 70 : 60;
  const center = width / 2;
  const radius = selected ? 18 : 15;
  const top = selected ? 24 : 20;
  const fontSize = selected ? 13 : 11;
  const ring = selected ? `<circle cx="${center}" cy="${top}" r="${radius + 8}" fill="${color}" opacity="0.18"/>` : '';

  return `<svg xmlns="http://www.w3.org/2000/svg" width="${width}" height="${height}" viewBox="0 0 ${width} ${height}">
    ${ring}
    <path d="M${center},${height - 4}
      C${center},${height - 4} ${center - radius - 11},${top + radius + 13} ${center - radius - 11},${top}
      A${radius + 11},${radius + 11} 0 1 1 ${center + radius + 11},${top}
      C${center + radius + 11},${top + radius + 13} ${center},${height - 4} ${center},${height - 4}Z"
      fill="${color}" stroke="white" stroke-width="${selected ? 3 : 2}" stroke-linejoin="round"/>
    <circle cx="${center}" cy="${top}" r="${radius}" fill="rgba(15,23,42,0.88)" stroke="rgba(255,255,255,0.7)" stroke-width="1"/>
    <text x="${center}" y="${top + 4}" text-anchor="middle" font-family="Inter, Arial, sans-serif" font-size="${fontSize}" font-weight="800" fill="white">${label}</text>
  </svg>`;
};

const toDataUrl = (svg) => {
  return `data:image/svg+xml;charset=UTF-8,${encodeURIComponent(svg)}`;
};

const mapTypeToGoogleId = (type) => {
  const G = window.google?.maps;

  if (!G) return type;

  return {
    roadmap: G.MapTypeId.ROADMAP,
    satellite: G.MapTypeId.HYBRID,
    terrain: G.MapTypeId.TERRAIN,
  }[type] || G.MapTypeId.ROADMAP;
};

const locationPopupHtml = (session) => {
  const location = session.lastLocation || {};
  const name = escapeHtml(session.employee?.name || 'Unknown employee');
  const empCode = escapeHtml(session.employee?.empCode || 'No employee ID');
  const status = escapeHtml(location.status || location.label || 'Location available');
  const lat = coordinateLabel(location.lat);
  const lng = coordinateLabel(location.lng);
  const accuracy = accuracyLabel(location.accuracy);
  const lastPing = escapeHtml(relativeTime(location.occurredAt || session.lastActivityAt));
  const mapsUrl = `https://www.google.com/maps?q=${encodeURIComponent(`${location.lat},${location.lng}`)}`;
  const kind = locationStatusKind(session);

  return `
    <div class="wfh-map-popup">
      <div class="wfh-map-popup__header">
        <div class="wfh-map-popup__avatar">${escapeHtml(initials(session.employee?.name))}</div>
        <div>
          <strong>${name}</strong>
          <span>${empCode}</span>
        </div>
      </div>
      <div class="wfh-map-popup__status is-${kind}">
        <i></i>
        <span>${status}</span>
      </div>
      <div class="wfh-map-popup__grid">
        <div><span>Latitude</span><strong>${escapeHtml(lat)}</strong></div>
        <div><span>Longitude</span><strong>${escapeHtml(lng)}</strong></div>
        <div><span>Accuracy</span><strong>${escapeHtml(accuracy)}</strong></div>
        <div><span>Last ping</span><strong>${lastPing}</strong></div>
      </div>
      <a class="wfh-map-popup__link" href="${mapsUrl}" target="_blank" rel="noopener noreferrer">View in Google Maps</a>
    </div>
  `;
};

const fitLocationMap = () => {
  if (!locationMap) {
    renderLocationMap();
    return;
  }

  if (locationBounds && !locationBounds.isEmpty?.()) {
    locationMap.fitBounds(locationBounds, 60);
    return;
  }

  renderLocationMap();
};

const setMapType = (type) => {
  mapType.value = type;

  if (!locationMap) return;

  locationMap.setMapTypeId(mapTypeToGoogleId(type));
};

const openSelectedLocationPopup = () => {
  if (!locationMap || !selectedSessionId.value || !locationInfoWindow) return;

  const marker = locationMarkers.get(selectedSessionId.value);
  const session = locationSessions.value.find((item) => item.id === selectedSessionId.value);

  if (!marker || !session) return;

  locationInfoWindow.setContent(locationPopupHtml(session));
  locationInfoWindow.open({ map: locationMap, anchor: marker });
  locationMap.panTo(marker.getPosition());
};

const selectLocationSession = async (sessionId) => {
  if (activeView.value === 'monitor') {
    await selectSession(sessionId);
  } else {
    selectedSessionId.value = sessionId;
    await loadSelectedDetails();
  }

  await nextTick();
  await renderLocationMap();
  openSelectedLocationPopup();
};

const selectLatestLocation = () => {
  if (!latestLocationSession.value) return;

  selectLocationSession(latestLocationSession.value.id);
};

const locationSummary = (session) => {
  const location = session.lastLocation || {};
  const accuracy = location.accuracy ? `, ${accuracyLabel(location.accuracy)}` : '';

  return `${location.status || location.label || 'Location available'}${accuracy}`;
};

const coordinateLabel = (value) => {
  const number = Number(value);

  return Number.isFinite(number) ? number.toFixed(6) : '-';
};

const accuracyLabel = (value) => {
  const number = Number(value);

  return Number.isFinite(number) ? `${Math.round(number)}m` : '-';
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

watch([sessions, selectedSessionId, activeView], () => {
  nextTick(() => renderLocationMap());
}, { deep: true });

onMounted(async () => {
  document.addEventListener('fullscreenchange', syncFullscreenState);
  await loadSessions();
  refreshTimer.value = window.setInterval(() => {
    loadSessions({ silent: true });
    loadSelectedDetails({ silent: true });
  }, 5000);
});

onBeforeUnmount(() => {
  document.removeEventListener('fullscreenchange', syncFullscreenState);
  window.clearInterval(refreshTimer.value);
  window.clearTimeout(searchTimer.value);
  stopSnapshotMonitor({ report: true });
  if (locationMap) {
    locationMarkers.forEach((marker) => marker.setMap(null));
    locationMarkers = new Map();
    locationInfoWindow?.close();
    locationMap = null;
    locationInfoWindow = null;
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
.wfh-wall__tabs,
.wfh-wall__layout,
.wfh-wall__location-layout {
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

.wfh-wall__auto-badge {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  min-height: 40px;
  border: 1px solid var(--wall-border);
  border-radius: 999px;
  padding: 0 13px;
  background: var(--wall-panel-strong);
  color: var(--wall-muted);
  font-size: 12px;
  font-weight: 900;
  white-space: nowrap;
}

.wfh-wall__auto-badge.live {
  border-color: rgba(16, 185, 129, 0.35);
  background: rgba(16, 185, 129, 0.12);
  color: #047857;
}

:global(.dark) .wfh-wall__auto-badge.live {
  color: #6ee7b7;
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

.wfh-wall__tabs {
  display: flex;
  gap: 8px;
  padding-top: 0;
  padding-bottom: 14px;
  background: var(--wall-bg);
}

.wfh-wall__tabs button {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  min-height: 40px;
  border: 1px solid var(--wall-border);
  border-radius: 8px;
  padding: 0 14px;
  background: var(--wall-panel);
  color: var(--wall-muted);
  font-size: 13px;
  font-weight: 800;
}

.wfh-wall__tabs button.active {
  border-color: #2563eb;
  background: #2563eb;
  color: #fff;
}

.wfh-wall__layout {
  display: grid;
  grid-template-columns: minmax(260px, 320px) minmax(0, 1.35fr) minmax(320px, 420px);
  gap: 14px;
  padding-bottom: 20px;
  background: var(--wall-bg);
  overflow-x: clip;
}

.wfh-wall__location-layout {
  display: grid;
  grid-template-columns: minmax(0, 1fr) minmax(300px, 380px);
  gap: 14px;
  padding-bottom: 20px;
  background: var(--wall-bg);
}

.wfh-wall__roster,
.wfh-wall__viewer,
.wfh-wall__map-panel,
.wfh-wall__location-side,
.wfh-wall__side,
.wfh-wall__detail-card {
  min-width: 0;
  border: 1px solid var(--wall-border);
  border-radius: 12px;
  background: var(--wall-panel);
}

.wfh-wall__viewer {
  overflow: hidden;
}

.wfh-wall__side,
.wfh-wall__location-side {
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
  flex-wrap: wrap;
  gap: 12px;
  padding: 14px;
  border-bottom: 1px solid var(--wall-border);
}

.wfh-wall__viewer-head > div:first-child {
  min-width: min(320px, 100%);
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

.wfh-wall__feed-status {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 10px;
  padding: 12px 14px 0;
}

.wfh-wall__feed-status article {
  min-width: 0;
  border: 1px solid var(--wall-border);
  border-radius: 10px;
  padding: 10px 12px;
  background: var(--wall-panel-strong);
}

.wfh-wall__feed-status article.connected {
  border-color: rgba(16, 185, 129, 0.34);
  background: color-mix(in srgb, #10b981 11%, var(--wall-panel));
}

.wfh-wall__feed-status span,
.wfh-wall__feed-status small {
  display: block;
  overflow: hidden;
  color: var(--wall-muted);
  font-size: 12px;
  text-overflow: ellipsis;
}

.wfh-wall__feed-status span {
  font-weight: 800;
  text-transform: uppercase;
}

.wfh-wall__feed-status strong {
  display: block;
  margin-top: 4px;
  color: var(--wall-text);
  font-size: 14px;
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

.wfh-wall__video-frame,
.wfh-wall__snapshot-frame {
  position: relative;
  display: grid;
  place-items: center;
  width: calc(100% - 28px);
  height: clamp(420px, 58dvh, 720px);
  min-height: 0;
  margin: 14px;
  border: 1px solid var(--wall-border);
  border-radius: 12px;
  background: var(--wall-video);
  overflow: hidden;
}

.wfh-wall__video-frame video,
.wfh-wall__snapshot-frame img {
  width: 100%;
  height: 100%;
  object-fit: contain;
  background: #020617;
}

.wfh-wall__snapshot-frame {
  background: #020617;
}

.wfh-wall__snapshot-caption {
  position: absolute;
  left: 14px;
  right: 14px;
  bottom: 14px;
  display: flex;
  align-items: center;
  gap: 9px;
  min-height: 42px;
  border: 1px solid rgba(148, 163, 184, 0.28);
  border-radius: 10px;
  padding: 9px 12px;
  background: rgba(15, 23, 42, 0.82);
  color: #e5eefb;
  backdrop-filter: blur(10px);
}

.wfh-wall__snapshot-caption strong {
  overflow: hidden;
  font-size: 13px;
  font-weight: 900;
  white-space: nowrap;
  text-overflow: ellipsis;
}

.wfh-wall__snapshot-caption small {
  margin-left: auto;
  color: #cbd5e1;
  font-size: 12px;
  white-space: nowrap;
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

.wfh-wall__media-tile {
  position: absolute;
  top: 14px;
  right: 14px;
  display: grid;
  width: min(260px, calc(42% - 14px));
  aspect-ratio: 16 / 10;
  border: 1px solid rgba(148, 163, 184, 0.32);
  border-radius: 12px;
  background: rgba(15, 23, 42, 0.82);
  box-shadow: 0 18px 55px rgba(2, 6, 23, 0.32);
  overflow: hidden;
}

.wfh-wall__media-tile.connected {
  border-color: rgba(16, 185, 129, 0.45);
}

.wfh-wall__media-tile video {
  width: 100%;
  height: 100%;
  object-fit: cover;
  background: #020617;
}

.wfh-wall__media-empty {
  position: absolute;
  inset: 0;
  display: grid;
  place-content: center;
  gap: 5px;
  padding: 14px;
  color: #94a3b8;
  text-align: center;
}

.wfh-wall__media-empty i {
  color: #38bdf8;
  font-size: 24px;
}

.wfh-wall__media-empty strong {
  color: #e5eefb;
  font-size: 13px;
}

.wfh-wall__media-empty span {
  display: -webkit-box;
  overflow: hidden;
  color: #94a3b8;
  font-size: 11px;
  line-height: 1.35;
  -webkit-box-orient: vertical;
  -webkit-line-clamp: 2;
}

.wfh-wall__media-caption {
  position: absolute;
  left: 8px;
  right: 8px;
  bottom: 8px;
  display: flex;
  align-items: center;
  gap: 7px;
  border-radius: 999px;
  padding: 6px 8px;
  background: rgba(2, 6, 23, 0.72);
  color: #e5eefb;
  backdrop-filter: blur(10px);
}

.wfh-wall__media-caption strong {
  overflow: hidden;
  font-size: 11px;
  font-weight: 900;
  white-space: nowrap;
  text-overflow: ellipsis;
}

.wfh-wall__audio-button {
  position: absolute;
  right: 8px;
  top: 8px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  min-height: 30px;
  border: 1px solid rgba(125, 211, 252, 0.44);
  border-radius: 999px;
  padding: 0 10px;
  background: rgba(14, 165, 233, 0.9);
  color: #f0f9ff;
  font-size: 11px;
  font-weight: 900;
  box-shadow: 0 10px 28px rgba(2, 6, 23, 0.28);
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

.wfh-wall__activity-filter {
  min-height: 34px;
  border: 1px solid var(--wall-border);
  border-radius: 8px;
  padding: 0 10px;
  background: var(--wall-input);
  color: var(--wall-text);
  font-size: 12px;
  font-weight: 800;
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

.wfh-wall__steps {
  display: grid;
  gap: 8px;
  margin-top: 12px;
  padding: 0;
  list-style: none;
}

.wfh-wall__steps li {
  display: grid;
  grid-template-columns: 30px minmax(0, 1fr) auto;
  align-items: center;
  gap: 9px;
  min-height: 38px;
  border: 1px solid var(--wall-border);
  border-radius: 10px;
  padding: 7px 9px;
  background: var(--wall-panel-strong);
  color: var(--wall-muted);
}

.wfh-wall__steps li::after {
  content: "Waiting";
  border-radius: 999px;
  padding: 3px 8px;
  background: var(--wall-soft);
  color: var(--wall-muted);
  font-size: 10px;
  font-weight: 900;
  text-transform: uppercase;
}

.wfh-wall__steps li.done {
  border-color: rgba(16, 185, 129, 0.36);
  background: rgba(16, 185, 129, 0.1);
  color: var(--wall-text);
}

.wfh-wall__steps li.done::after {
  content: "Ready";
  background: rgba(16, 185, 129, 0.18);
  color: #047857;
}

:global(.dark) .wfh-wall__steps li.done::after {
  color: #6ee7b7;
}

.wfh-wall__steps i {
  display: inline-grid;
  place-items: center;
  width: 28px;
  height: 28px;
  border-radius: 8px;
  background: var(--wall-soft);
  color: var(--wall-muted);
  font-size: 15px;
}

.wfh-wall__steps li.done i {
  background: #10b981;
  color: #042318;
}

.wfh-wall__steps span {
  min-width: 0;
  overflow: hidden;
  color: inherit;
  font-size: 13px;
  font-weight: 800;
  white-space: nowrap;
  text-overflow: ellipsis;
}

.wfh-wall__helper-text {
  margin-top: 12px;
  border-left: 3px solid #2563eb;
  border-radius: 8px;
  padding: 10px 11px;
  background: rgba(37, 99, 235, 0.08);
  color: var(--wall-muted);
  font-size: 12px;
  line-height: 1.45;
}

.wfh-wall__map {
  position: relative;
  z-index: 1;
  height: min(70dvh, 820px);
  margin-top: 12px;
  border: 1px solid var(--wall-border);
  border-radius: 0 0 12px 12px;
  background: var(--wall-video);
  overflow: hidden;
}

.wfh-wall__map-panel--immersive {
  overflow: hidden;
}

.wfh-wall__map-metrics {
  display: grid;
  grid-template-columns: repeat(4, minmax(0, 1fr));
  gap: 10px;
  padding: 12px 14px;
  border-bottom: 1px solid var(--wall-border);
}

.wfh-wall__map-metric {
  min-height: 64px;
  border: 1px solid var(--wall-border);
  border-radius: 10px;
  padding: 10px 12px;
  background: var(--wall-panel-strong);
}

.wfh-wall__map-metric span {
  display: block;
  color: var(--wall-muted);
  font-size: 11px;
  font-weight: 900;
  text-transform: uppercase;
}

.wfh-wall__map-metric strong {
  display: block;
  margin-top: 6px;
  color: var(--wall-text);
  font-size: 26px;
  line-height: 1;
}

.wfh-wall__map-metric.is-inside strong { color: #059669; }
.wfh-wall__map-metric.is-outside strong { color: #e11d48; }
.wfh-wall__map-metric.is-unknown strong { color: #64748b; }

.wfh-wall__map-wrap {
  position: relative;
  background: var(--wall-video);
}

.wfh-wall__map-panel .wfh-wall__map {
  margin-top: 0;
  border-width: 0;
  border-top: 1px solid var(--wall-border);
}

.wfh-wall__map-empty {
  position: absolute;
  inset: 0;
  z-index: 2;
  display: grid;
  place-content: center;
  gap: 8px;
  padding: 24px;
  color: var(--wall-muted);
  text-align: center;
  pointer-events: none;
}

.wfh-wall__map-empty i {
  color: #0ea5e9;
  font-size: 34px;
}

.wfh-wall__map-empty strong {
  color: var(--wall-text);
  font-size: 18px;
}

.wfh-wall__map-overlay {
  position: absolute;
  inset: 0;
  z-index: 5;
  display: grid;
  place-content: center;
  gap: 8px;
  padding: 24px;
  background: color-mix(in srgb, var(--wall-panel) 78%, transparent);
  color: var(--wall-muted);
  text-align: center;
  backdrop-filter: blur(8px);
}

.wfh-wall__map-overlay strong {
  color: var(--wall-text);
  font-size: 18px;
}

.wfh-wall__map-overlay small {
  max-width: 340px;
  font-size: 13px;
}

.wfh-wall__map-overlay.is-error i {
  color: #e11d48;
  font-size: 32px;
}

.wfh-wall__map-overlay button {
  justify-self: center;
  min-height: 38px;
  border: 0;
  border-radius: 8px;
  padding: 0 14px;
  background: #2563eb;
  color: #fff;
  font-size: 13px;
  font-weight: 800;
}

.wfh-wall__spinner {
  justify-self: center;
  width: 34px;
  height: 34px;
  border: 3px solid color-mix(in srgb, #2563eb 20%, transparent);
  border-top-color: #2563eb;
  border-radius: 999px;
  animation: wfh-spin 0.9s linear infinite;
}

@keyframes wfh-spin {
  to { transform: rotate(360deg); }
}

.wfh-wall__map-tools {
  position: absolute;
  left: 14px;
  bottom: 14px;
  z-index: 4;
  display: grid;
  overflow: hidden;
  border: 1px solid var(--wall-border);
  border-radius: 12px;
  background: color-mix(in srgb, var(--wall-panel) 94%, transparent);
  box-shadow: 0 14px 40px rgba(15, 23, 42, 0.18);
  backdrop-filter: blur(12px);
}

.wfh-wall__map-tools button {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  min-height: 36px;
  border: 0;
  border-bottom: 1px solid var(--wall-border);
  padding: 0 12px;
  background: transparent;
  color: var(--wall-muted);
  font-size: 12px;
  font-weight: 800;
  text-align: left;
}

.wfh-wall__map-tools button:last-child {
  border-bottom: 0;
}

.wfh-wall__map-tools button.active {
  background: #2563eb;
  color: #fff;
}

.wfh-wall__map-legend {
  position: absolute;
  right: 14px;
  bottom: 14px;
  z-index: 420;
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  max-width: calc(100% - 28px);
  border: 1px solid var(--wall-border);
  border-radius: 999px;
  padding: 8px 10px;
  background: color-mix(in srgb, var(--wall-panel) 88%, transparent);
  box-shadow: 0 14px 40px rgba(15, 23, 42, 0.18);
  backdrop-filter: blur(12px);
}

.wfh-wall__map-legend span {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  color: var(--wall-muted);
  font-size: 11px;
  font-weight: 900;
  text-transform: uppercase;
}

.wfh-wall__map-legend i {
  width: 9px;
  height: 9px;
  border-radius: 999px;
}

.wfh-wall__map-legend i.inside { background: #10b981; }
.wfh-wall__map-legend i.outside { background: #e11d48; }
.wfh-wall__map-legend i.unknown { background: #64748b; }

.wfh-wall__location-list {
  display: grid;
  gap: 8px;
  max-height: 170px;
  overflow: auto;
  margin-top: 10px;
}

.wfh-wall__location-list button {
  display: grid;
  grid-template-columns: auto minmax(0, 1fr) auto;
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

.wfh-wall__location-list em {
  color: var(--wall-muted);
  font-size: 11px;
  font-style: normal;
  font-weight: 800;
  white-space: nowrap;
}

.wfh-wall__location-list--large {
  max-height: min(50dvh, 520px);
}

.wfh-wall__wide-link {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  width: 100%;
  min-height: 40px;
  margin-top: 14px;
  border: 1px solid #2563eb;
  border-radius: 8px;
  background: #2563eb;
  color: #fff;
  font-size: 13px;
  font-weight: 900;
}

.wfh-wall__signal-card {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 10px;
  margin-top: 12px;
}

.wfh-wall__signal-card > div {
  border: 1px solid var(--wall-border);
  border-radius: 10px;
  padding: 12px;
  background: var(--wall-panel-strong);
}

.wfh-wall__signal-card strong,
.wfh-wall__signal-card span {
  display: block;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.wfh-wall__signal-card strong {
  color: var(--wall-text);
  font-size: 15px;
}

.wfh-wall__signal-card span {
  margin-top: 4px;
  color: var(--wall-muted);
  font-size: 12px;
}

:global(.gm-style .gm-style-iw-c) {
  padding: 0 !important;
  border-radius: 16px !important;
  background: color-mix(in srgb, var(--wall-panel) 96%, transparent) !important;
  box-shadow: 0 22px 70px rgba(15, 23, 42, 0.28) !important;
  backdrop-filter: blur(18px);
}

:global(.gm-style .gm-style-iw-d) {
  overflow: visible !important;
  padding: 0 !important;
}

:global(.gm-style .gm-style-iw-tc::after) {
  background: var(--wall-panel) !important;
}

:global(.gm-style .gm-ui-hover-effect) {
  display: none !important;
}

:global(.wfh-map-popup) {
  width: 306px;
  padding: 16px;
  color: var(--wall-text);
  font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
}

:global(.wfh-map-popup__header) {
  display: flex;
  align-items: center;
  gap: 12px;
  padding-bottom: 12px;
  border-bottom: 1px solid var(--wall-border);
}

:global(.wfh-map-popup__avatar) {
  display: grid;
  place-items: center;
  width: 42px;
  height: 42px;
  border: 1px solid rgba(16, 185, 129, 0.32);
  border-radius: 12px;
  background: rgba(16, 185, 129, 0.12);
  color: #047857;
  font-size: 13px;
  font-weight: 1000;
}

:global(.wfh-map-popup__header strong),
:global(.wfh-map-popup__header span) {
  display: block;
}

:global(.wfh-map-popup__header strong) {
  color: var(--wall-text);
  font-size: 16px;
  line-height: 1.2;
}

:global(.wfh-map-popup__header span) {
  margin-top: 3px;
  color: var(--wall-muted);
  font-size: 12px;
  font-weight: 700;
}

:global(.wfh-map-popup__status) {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  margin-top: 12px;
  border-radius: 999px;
  padding: 7px 10px;
  background: rgba(100, 116, 139, 0.12);
  color: var(--wall-muted);
  font-size: 12px;
  font-weight: 900;
}

:global(.wfh-map-popup__status i) {
  width: 9px;
  height: 9px;
  border-radius: 999px;
  background: currentColor;
}

:global(.wfh-map-popup__status.is-inside) {
  background: rgba(16, 185, 129, 0.12);
  color: #047857;
}

:global(.wfh-map-popup__status.is-outside) {
  background: rgba(225, 29, 72, 0.12);
  color: #be123c;
}

:global(.wfh-map-popup__grid) {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 8px;
  margin-top: 12px;
}

:global(.wfh-map-popup__grid div) {
  border: 1px solid var(--wall-border);
  border-radius: 10px;
  padding: 9px;
  background: var(--wall-panel-strong);
}

:global(.wfh-map-popup__grid span),
:global(.wfh-map-popup__grid strong) {
  display: block;
}

:global(.wfh-map-popup__grid span) {
  color: var(--wall-muted);
  font-size: 10px;
  font-weight: 900;
  text-transform: uppercase;
}

:global(.wfh-map-popup__grid strong) {
  margin-top: 4px;
  color: var(--wall-text);
  font-size: 12px;
}

:global(.wfh-map-popup__link) {
  display: flex;
  justify-content: center;
  width: 100%;
  margin-top: 12px;
  border-radius: 10px;
  padding: 10px;
  background: #2563eb;
  color: #fff;
  font-size: 12px;
  font-weight: 900;
  text-decoration: none;
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
  .wfh-wall__location-layout,
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

  .wfh-wall__feed-status,
  .wfh-wall__map-metrics {
    grid-template-columns: 1fr;
  }

  .wfh-wall__tabs {
    flex-direction: column;
  }

  .wfh-wall__tabs button {
    width: 100%;
  }

  .wfh-wall__video-frame {
    min-height: 320px;
    height: 420px;
    aspect-ratio: auto;
  }

  .wfh-wall__media-tile {
    top: 10px;
    right: 10px;
    width: min(190px, calc(100% - 20px));
  }

  .wfh-wall__map-tools {
    left: 10px;
    bottom: 62px;
  }

  .wfh-wall__map-legend {
    left: 10px;
    right: 10px;
    justify-content: center;
    border-radius: 12px;
  }

  .wfh-wall__map {
    height: 420px;
  }
}
</style>
