<template>
  <section ref="wallRoot" class="wfh-wall w-full">
    <div class="wfh-wall__shell">
      <header class="wfh-wall__header">
        <div>
          <p class="wfh-wall__eyebrow">WFH Monitoring</p>
          <h1 class="wfh-wall__title">Live Screen Command Center</h1>
        </div>

        <div class="wfh-wall__actions">
          <div class="wfh-wall__search">
            <i class="bi bi-search"></i>
            <input v-model.trim="search" type="search" placeholder="Search employee or ID" @keydown.enter="loadSessions" />
          </div>
          <input v-model="selectedDate" class="wfh-wall__date" type="date" @change="loadSessions" />
          <button class="wfh-wall__button wfh-wall__button--muted" type="button" title="Refresh monitoring data" @click="loadSessions">
            <i class="bi bi-arrow-clockwise"></i>
            Refresh
          </button>
          <a v-if="wallUrl" class="wfh-wall__button wfh-wall__button--muted" :href="wallUrl" target="_blank" rel="noopener" title="Open WFH monitoring in a clean browser tab">
            <i class="bi bi-box-arrow-up-right"></i>
            Open wall
          </a>
          <button class="wfh-wall__button wfh-wall__button--primary" type="button" title="Use the full screen for monitoring" @click="toggleFullscreen">
            <i :class="isFullscreen ? 'bi bi-fullscreen-exit' : 'bi bi-fullscreen'"></i>
            {{ isFullscreen ? 'Exit' : 'Fullscreen' }}
          </button>
        </div>
      </header>

      <div class="wfh-wall__stats" aria-label="WFH monitoring summary">
        <article v-for="stat in statCards" :key="stat.key" :class="['wfh-wall__stat', `is-${stat.key}`]">
          <span>{{ stat.label }}</span>
          <strong>{{ stat.value }}</strong>
        </article>
      </div>

      <nav class="wfh-wall__tabs" aria-label="WFH monitoring views">
        <button v-for="mode in modes" :key="mode.key" :class="{ active: activeMode === mode.key }" type="button" @click="activeMode = mode.key">
          <i :class="mode.icon"></i>
          {{ mode.label }}
        </button>
      </nav>

      <div v-if="errorMessage" class="wfh-wall__alert">
        <i class="bi bi-exclamation-triangle"></i>
        <span>{{ errorMessage }}</span>
      </div>

      <main v-show="activeMode === 'wall'" class="wfh-wall__grid-layout">
        <aside class="wfh-wall__roster">
          <div class="wfh-wall__panel-title">
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

        <section class="wfh-wall__monitor-bank">
          <div class="wfh-wall__bank-toolbar">
            <div>
              <strong>Screen Wall</strong>
              <span>Latest available frame per employee</span>
            </div>
            <div class="wfh-wall__button-row">
              <button class="wfh-wall__button wfh-wall__button--muted" type="button" @click="requestSelectedSnapshot" :disabled="!selectedSession">
                <i class="bi bi-camera"></i>
                Snapshot
              </button>
              <button class="wfh-wall__button wfh-wall__button--success" type="button" @click="startSelectedLiveSnapshots" :disabled="!selectedSession || selectedLiveActive">
                <i class="bi bi-broadcast"></i>
                Live feed
              </button>
            </div>
          </div>

          <div class="wfh-wall__screen-grid">
            <article
              v-for="session in sessions"
              :key="`tile-${session.id}`"
              :class="['wfh-wall__screen-tile', { selected: selectedSessionId === session.id }]"
              @click="selectSession(session.id)"
            >
              <div class="wfh-wall__screen-frame">
                <img v-if="session.latestScreenshot?.url" :src="session.latestScreenshot.url" :alt="`${session.employee?.name || 'Employee'} screen snapshot`" />
                <div v-else class="wfh-wall__empty-frame">
                  <i class="bi bi-display"></i>
                  <span>No frame yet</span>
                </div>
              </div>
              <footer>
                <div>
                  <strong>{{ session.employee?.name || 'Unknown employee' }}</strong>
                  <small>{{ session.latestScreenshot ? formatTime(session.latestScreenshot.capturedAt) : 'Waiting for first capture' }}</small>
                </div>
                <span :class="['wfh-wall__badge', stateClass(session.state)]">{{ session.state }}</span>
              </footer>
            </article>
          </div>
        </section>
      </main>

      <main v-show="activeMode === 'focus'" class="wfh-wall__focus">
        <section class="wfh-wall__viewer">
          <div class="wfh-wall__viewer-header">
            <div>
              <p>Selected Employee</p>
              <h2>{{ selectedSession?.employee?.name || 'No employee selected' }}</h2>
              <span>{{ selectedSession?.employee?.empCode || 'Choose an employee from the wall' }}</span>
            </div>
            <div class="wfh-wall__button-row">
              <button class="wfh-wall__button wfh-wall__button--muted" type="button" @click="requestSelectedSnapshot" :disabled="!selectedSession || actionBusy">
                <i class="bi bi-camera"></i>
                Request snapshot
              </button>
              <button class="wfh-wall__button wfh-wall__button--success" type="button" @click="startSelectedLiveSnapshots" :disabled="!selectedSession || selectedLiveActive || actionBusy">
                <i class="bi bi-play-circle"></i>
                Start live snapshots
              </button>
              <button class="wfh-wall__button wfh-wall__button--danger" type="button" @click="stopSelectedLiveSnapshots" :disabled="!selectedSession || !selectedLiveActive || actionBusy">
                <i class="bi bi-stop-circle"></i>
                Stop
              </button>
            </div>
          </div>

          <div class="wfh-wall__large-frame">
            <img v-if="selectedSnapshotUrl" :src="selectedSnapshotUrl" :alt="`${selectedSession?.employee?.name || 'Selected employee'} current screen`" />
            <div v-else class="wfh-wall__empty-frame">
              <i class="bi bi-display"></i>
              <strong>No screen frame yet</strong>
              <span>Ask the employee to keep the WFH attendance page open and screen sharing active.</span>
            </div>
            <div class="wfh-wall__frame-caption">
              <span>{{ selectedLiveActive ? 'Live snapshots active' : 'Latest saved frame' }}</span>
              <strong>{{ selectedSnapshotTime }}</strong>
            </div>
          </div>
        </section>

        <aside class="wfh-wall__details">
          <article class="wfh-wall__detail-card">
            <h3>Session</h3>
            <dl>
              <div><dt>Status</dt><dd><span :class="['wfh-wall__badge', stateClass(selectedSession?.state)]">{{ selectedSession?.state || '-' }}</span></dd></div>
              <div><dt>Work mode</dt><dd>{{ selectedSession?.workStatus || '-' }}</dd></div>
              <div><dt>Online</dt><dd>{{ duration(selectedSession?.onlineSeconds) }}</dd></div>
              <div><dt>Active</dt><dd>{{ duration(selectedSession?.activeSeconds) }}</dd></div>
              <div><dt>Last activity</dt><dd>{{ relativeTime(selectedSession?.lastActivityAt) }}</dd></div>
            </dl>
          </article>

          <article class="wfh-wall__detail-card">
            <h3>Snapshot Timeline</h3>
            <div class="wfh-wall__thumbs">
              <button v-for="shot in selectedScreenshots" :key="shot.id" type="button" @click="overrideSnapshot = shot">
                <img :src="shot.url" alt="WFH screen thumbnail" />
                <span>{{ formatTime(shot.capturedAt) }}</span>
              </button>
              <p v-if="!selectedScreenshots.length">No snapshots uploaded yet.</p>
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

      <main v-show="activeMode === 'activity'" class="wfh-wall__activity">
        <section class="wfh-wall__detail-card">
          <h3>Location Trail</h3>
          <div class="wfh-wall__location-grid">
            <article v-for="point in selectedLocations" :key="point.id">
              <strong>{{ point.locationLabel || point.status || 'Location ping' }}</strong>
              <span>{{ formatTime(point.occurredAt) }}</span>
              <small>Lat {{ point.lat }}, Lng {{ point.lng }}</small>
            </article>
            <p v-if="!selectedLocations.length">No GPS pings for this session yet.</p>
          </div>
        </section>

        <section class="wfh-wall__detail-card">
          <h3>Screen Notes</h3>
          <p class="wfh-wall__plain-text">
            This view uses uploaded screen frames, so it works on Hostinger shared hosting without websocket or TURN server setup. Peer video controls can stay available later, but snapshots are the stable monitoring path.
          </p>
        </section>
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
const activeMode = ref('wall');
const selectedDate = ref(props.initialDate || new Date().toISOString().slice(0, 10));
const search = ref('');
const sessions = ref([]);
const stats = ref({});
const selectedSessionId = ref(null);
const selectedDetails = ref(null);
const selectedScreenshots = ref([]);
const selectedEvents = ref([]);
const selectedLocations = ref([]);
const liveSnapshot = ref(null);
const overrideSnapshot = ref(null);
const errorMessage = ref('');
const actionBusy = ref(false);
const isFullscreen = ref(false);
const isAutoRefreshing = ref(false);
const refreshTimer = ref(null);
const liveTimer = ref(null);
const searchTimer = ref(null);

const modes = [
  { key: 'wall', label: 'Monitor Wall', icon: 'bi bi-grid-3x3-gap' },
  { key: 'focus', label: 'Selected Screen', icon: 'bi bi-display' },
  { key: 'activity', label: 'Activity / GPS', icon: 'bi bi-activity' },
];

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

const selectedLiveActive = computed(() => Boolean(selectedSession.value?.liveSnapshotsActive));

const selectedSnapshot = computed(() => {
  return overrideSnapshot.value || liveSnapshot.value || selectedSession.value?.latestScreenshot || null;
});

const selectedSnapshotUrl = computed(() => {
  if (!selectedSnapshot.value?.url) return '';
  return `${selectedSnapshot.value.url}${selectedSnapshot.value.url.includes('?') ? '&' : '?'}v=${encodeURIComponent(selectedSnapshot.value.capturedAt || selectedSnapshot.value.id || Date.now())}`;
});

const selectedSnapshotTime = computed(() => {
  return selectedSnapshot.value?.capturedAt ? formatTime(selectedSnapshot.value.capturedAt) : 'Waiting for first frame';
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
    } else if (selectedSessionId.value && activeMode.value !== 'wall') {
      await loadSelectedDetails({ silent: true });
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
    selectedScreenshots.value = [];
    selectedEvents.value = [];
    selectedLocations.value = [];
    return;
  }

  try {
    const payload = await apiFetch(`/sessions/${selectedSessionId.value}`);
    selectedDetails.value = payload.session || null;
    selectedScreenshots.value = payload.screenshots || [];
    selectedEvents.value = payload.events || [];
    selectedLocations.value = payload.locations || [];
  } catch (error) {
    if (!silent) errorMessage.value = error.message || 'Unable to load selected employee details.';
  }
};

const selectSession = async (sessionId) => {
  selectedSessionId.value = sessionId;
  liveSnapshot.value = null;
  overrideSnapshot.value = null;
  await loadSelectedDetails();
};

const requestSelectedSnapshot = async () => {
  if (!selectedSessionId.value) return;
  actionBusy.value = true;
  errorMessage.value = '';

  try {
    const payload = await apiFetch(`/sessions/${selectedSessionId.value}/snapshot/request`, { method: 'POST' });
    selectedDetails.value = payload.session || selectedDetails.value;
    await loadSessions({ silent: true });
  } catch (error) {
    errorMessage.value = error.message || 'Snapshot request failed.';
  } finally {
    actionBusy.value = false;
  }
};

const startSelectedLiveSnapshots = async () => {
  if (!selectedSessionId.value) return;
  actionBusy.value = true;
  errorMessage.value = '';

  try {
    const payload = await apiFetch(`/sessions/${selectedSessionId.value}/live-snapshots/start`, { method: 'POST' });
    liveSnapshot.value = payload.snapshot || liveSnapshot.value;
    await loadSelectedDetails({ silent: true });
    await loadSessions({ silent: true });
    activeMode.value = 'focus';
    startLivePolling(Math.max(3, Number(payload.intervalSeconds || 5)));
  } catch (error) {
    errorMessage.value = error.message || 'Unable to start live snapshots.';
  } finally {
    actionBusy.value = false;
  }
};

const stopSelectedLiveSnapshots = async () => {
  if (!selectedSessionId.value) return;
  actionBusy.value = true;

  try {
    await apiFetch(`/sessions/${selectedSessionId.value}/live-snapshots/stop`, { method: 'POST' });
    stopLivePolling();
    await loadSelectedDetails({ silent: true });
    await loadSessions({ silent: true });
  } catch (error) {
    errorMessage.value = error.message || 'Unable to stop live snapshots.';
  } finally {
    actionBusy.value = false;
  }
};

const pollLatestSnapshot = async () => {
  if (!selectedSessionId.value) return;

  try {
    const payload = await apiFetch(`/sessions/${selectedSessionId.value}/live-snapshots/latest`);
    if (payload.snapshot) {
      liveSnapshot.value = payload.snapshot;
      overrideSnapshot.value = null;
    }
  } catch {
    stopLivePolling();
  }
};

const startLivePolling = (intervalSeconds = 5) => {
  stopLivePolling();
  pollLatestSnapshot();
  liveTimer.value = window.setInterval(pollLatestSnapshot, intervalSeconds * 1000);
};

const stopLivePolling = () => {
  if (liveTimer.value) {
    window.clearInterval(liveTimer.value);
    liveTimer.value = null;
  }
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

watch(search, () => {
  window.clearTimeout(searchTimer.value);
  searchTimer.value = window.setTimeout(() => loadSessions(), 350);
});

watch(activeMode, async (mode) => {
  if (mode !== 'wall' && selectedSessionId.value) {
    await nextTick();
    loadSelectedDetails({ silent: true });
  }
});

onMounted(async () => {
  document.addEventListener('fullscreenchange', syncFullscreenState);
  await loadSessions();
  refreshTimer.value = window.setInterval(() => loadSessions({ silent: true }), 5000);
});

onBeforeUnmount(() => {
  document.removeEventListener('fullscreenchange', syncFullscreenState);
  window.clearInterval(refreshTimer.value);
  window.clearTimeout(searchTimer.value);
  stopLivePolling();
});
</script>

<style scoped>
.wfh-wall {
  color: #d9e6f7;
}

.wfh-wall__shell {
  width: min(100%, 1780px);
  min-height: calc(100dvh - 9.5rem);
  border: 1px solid rgba(148, 163, 184, 0.22);
  border-radius: 16px;
  background: #07111f;
  box-shadow: 0 24px 90px rgba(2, 6, 23, 0.25);
  overflow: hidden;
}

.wfh-wall__header,
.wfh-wall__stats,
.wfh-wall__tabs,
.wfh-wall__grid-layout,
.wfh-wall__focus,
.wfh-wall__activity {
  padding-left: clamp(14px, 2vw, 28px);
  padding-right: clamp(14px, 2vw, 28px);
}

.wfh-wall__header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  padding-top: 22px;
  padding-bottom: 16px;
  border-bottom: 1px solid rgba(148, 163, 184, 0.16);
  background: linear-gradient(180deg, #0f1e32 0%, #07111f 100%);
}

.wfh-wall__eyebrow {
  color: #67e8f9;
  font-size: 12px;
  font-weight: 800;
  letter-spacing: 0;
  text-transform: uppercase;
}

.wfh-wall__title {
  margin-top: 4px;
  color: #f8fafc;
  font-size: clamp(22px, 2.3vw, 34px);
  line-height: 1.08;
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
  height: 42px;
  border-radius: 8px;
  border: 1px solid rgba(148, 163, 184, 0.24);
  background: #101c2f;
  color: #e2e8f0;
}

.wfh-wall__search {
  display: flex;
  align-items: center;
  gap: 8px;
  min-width: min(280px, 100%);
  padding: 0 12px;
}

.wfh-wall__search input,
.wfh-wall__date {
  outline: none;
}

.wfh-wall__search input {
  width: 100%;
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
  transition: transform 120ms ease, border-color 120ms ease, background 120ms ease;
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
  border-color: #3b82f6;
  color: #fff;
}

.wfh-wall__button--success {
  background: #10b981;
  border-color: #34d399;
  color: #032115;
}

.wfh-wall__button--danger {
  background: #e11d48;
  border-color: #fb7185;
  color: #fff;
}

.wfh-wall__button--muted {
  background: #17243a;
}

.wfh-wall__stats {
  display: grid;
  grid-template-columns: repeat(6, minmax(120px, 1fr));
  gap: 12px;
  padding-top: 18px;
  padding-bottom: 14px;
}

.wfh-wall__stat {
  min-height: 86px;
  border: 1px solid rgba(148, 163, 184, 0.2);
  border-radius: 10px;
  padding: 14px;
  background: #101c2f;
}

.wfh-wall__stat span {
  display: block;
  color: #9fb0c6;
  font-size: 12px;
  font-weight: 800;
  text-transform: uppercase;
}

.wfh-wall__stat strong {
  display: block;
  margin-top: 10px;
  color: #f8fafc;
  font-size: 32px;
  line-height: 1;
}

.wfh-wall__stat.is-active strong { color: #6ee7b7; }
.wfh-wall__stat.is-afk strong { color: #fde68a; }
.wfh-wall__stat.is-onBreak strong { color: #7dd3fc; }
.wfh-wall__stat.is-screenOff strong { color: #fdba74; }
.wfh-wall__stat.is-geofenceAlerts strong { color: #fda4af; }

.wfh-wall__tabs {
  display: flex;
  gap: 8px;
  padding-bottom: 16px;
}

.wfh-wall__tabs button {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  min-height: 42px;
  border-radius: 8px;
  padding: 0 16px;
  color: #9fb0c6;
  font-size: 14px;
  font-weight: 800;
}

.wfh-wall__tabs button.active {
  background: #1d4ed8;
  color: #eff6ff;
}

.wfh-wall__alert {
  display: flex;
  align-items: center;
  gap: 10px;
  margin: 0 28px 14px;
  border: 1px solid rgba(251, 113, 133, 0.35);
  border-radius: 10px;
  padding: 12px 14px;
  background: rgba(127, 29, 29, 0.35);
  color: #fecdd3;
  font-weight: 700;
}

.wfh-wall__grid-layout {
  display: grid;
  grid-template-columns: minmax(280px, 360px) minmax(0, 1fr);
  gap: 16px;
  padding-bottom: 24px;
}

.wfh-wall__roster,
.wfh-wall__monitor-bank,
.wfh-wall__viewer,
.wfh-wall__details,
.wfh-wall__detail-card {
  border: 1px solid rgba(148, 163, 184, 0.2);
  border-radius: 12px;
  background: #0b1628;
}

.wfh-wall__panel-title,
.wfh-wall__bank-toolbar,
.wfh-wall__viewer-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  padding: 16px;
  border-bottom: 1px solid rgba(148, 163, 184, 0.15);
}

.wfh-wall__panel-title strong,
.wfh-wall__bank-toolbar strong {
  display: block;
  color: #f8fafc;
  font-size: 15px;
}

.wfh-wall__panel-title span:not(.wfh-wall__pulse),
.wfh-wall__bank-toolbar span {
  color: #93a4bb;
  font-size: 12px;
}

.wfh-wall__pulse {
  width: 10px;
  height: 10px;
  border-radius: 999px;
  background: #64748b;
}

.wfh-wall__pulse.live {
  background: #34d399;
  box-shadow: 0 0 0 6px rgba(52, 211, 153, 0.12);
}

.wfh-wall__employee-list {
  display: grid;
  gap: 8px;
  max-height: 68dvh;
  overflow: auto;
  padding: 12px;
}

.wfh-wall__employee {
  display: grid;
  grid-template-columns: auto minmax(0, 1fr) auto;
  align-items: center;
  gap: 10px;
  width: 100%;
  border: 1px solid transparent;
  border-radius: 10px;
  padding: 12px;
  background: #121f33;
  text-align: left;
}

.wfh-wall__employee.selected {
  border-color: #60a5fa;
  background: #162947;
}

.wfh-wall__employee-main,
.wfh-wall__employee-side {
  min-width: 0;
}

.wfh-wall__employee-main strong,
.wfh-wall__screen-tile footer strong {
  display: block;
  overflow: hidden;
  color: #f8fafc;
  font-size: 14px;
  white-space: nowrap;
  text-overflow: ellipsis;
}

.wfh-wall__employee-main small,
.wfh-wall__employee-side small,
.wfh-wall__screen-tile footer small {
  display: block;
  margin-top: 3px;
  color: #8fa1b8;
  font-size: 12px;
}

.wfh-wall__employee-side {
  text-align: right;
}

.wfh-wall__state-dot {
  width: 10px;
  height: 10px;
  border-radius: 999px;
  background: #94a3b8;
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

.state-active { background-color: #10b981; color: #06251a; }
.state-break { background-color: #38bdf8; color: #082f49; }
.state-afk { background-color: #f59e0b; color: #231600; }
.state-screen { background-color: #fb923c; color: #2d1604; }
.state-offline { background-color: #f43f5e; color: #fff1f2; }
.state-neutral { background-color: #64748b; color: #f8fafc; }

.wfh-wall__monitor-bank {
  min-width: 0;
}

.wfh-wall__screen-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
  gap: 12px;
  max-height: 68dvh;
  overflow: auto;
  padding: 14px;
}

.wfh-wall__screen-tile {
  min-width: 0;
  border: 1px solid rgba(148, 163, 184, 0.2);
  border-radius: 12px;
  background: #111d30;
  overflow: hidden;
  cursor: pointer;
}

.wfh-wall__screen-tile.selected {
  border-color: #67e8f9;
  box-shadow: 0 0 0 2px rgba(103, 232, 249, 0.14);
}

.wfh-wall__screen-frame,
.wfh-wall__large-frame {
  position: relative;
  display: grid;
  place-items: center;
  background: #020617;
}

.wfh-wall__screen-frame {
  aspect-ratio: 16 / 9;
}

.wfh-wall__screen-frame img,
.wfh-wall__large-frame img,
.wfh-wall__thumbs img {
  width: 100%;
  height: 100%;
  object-fit: contain;
}

.wfh-wall__empty-frame {
  display: grid;
  place-items: center;
  gap: 8px;
  padding: 24px;
  color: #8fa1b8;
  text-align: center;
}

.wfh-wall__empty-frame i {
  color: #38bdf8;
  font-size: 30px;
}

.wfh-wall__empty-frame strong {
  color: #f8fafc;
  font-size: 18px;
}

.wfh-wall__screen-tile footer {
  display: grid;
  grid-template-columns: minmax(0, 1fr) auto;
  align-items: center;
  gap: 10px;
  padding: 10px 12px;
}

.wfh-wall__focus {
  display: grid;
  grid-template-columns: minmax(0, 1fr) 360px;
  gap: 16px;
  padding-bottom: 24px;
}

.wfh-wall__viewer-header p,
.wfh-wall__viewer-header span {
  color: #93a4bb;
  font-size: 12px;
}

.wfh-wall__viewer-header h2 {
  margin-top: 2px;
  color: #f8fafc;
  font-size: clamp(20px, 2vw, 30px);
  font-weight: 800;
  line-height: 1.1;
}

.wfh-wall__large-frame {
  min-height: min(68dvh, 760px);
  aspect-ratio: 16 / 9;
  margin: 16px;
  border: 1px solid rgba(148, 163, 184, 0.18);
  border-radius: 12px;
  overflow: hidden;
}

.wfh-wall__frame-caption {
  position: absolute;
  right: 14px;
  bottom: 14px;
  display: flex;
  align-items: center;
  gap: 10px;
  border: 1px solid rgba(148, 163, 184, 0.2);
  border-radius: 8px;
  padding: 8px 10px;
  background: rgba(2, 6, 23, 0.78);
}

.wfh-wall__frame-caption span {
  color: #93a4bb;
  font-size: 12px;
}

.wfh-wall__frame-caption strong {
  color: #f8fafc;
  font-size: 12px;
}

.wfh-wall__details {
  display: grid;
  gap: 12px;
  align-content: start;
  border: 0;
  background: transparent;
}

.wfh-wall__detail-card {
  padding: 16px;
}

.wfh-wall__detail-card h3 {
  color: #f8fafc;
  font-size: 15px;
  font-weight: 800;
}

.wfh-wall__detail-card dl {
  display: grid;
  gap: 10px;
  margin-top: 14px;
}

.wfh-wall__detail-card dl div {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
}

.wfh-wall__detail-card dt {
  color: #93a4bb;
  font-size: 12px;
}

.wfh-wall__detail-card dd {
  color: #f8fafc;
  font-size: 13px;
  font-weight: 800;
}

.wfh-wall__thumbs {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 8px;
  margin-top: 12px;
}

.wfh-wall__thumbs button {
  overflow: hidden;
  border: 1px solid rgba(148, 163, 184, 0.2);
  border-radius: 8px;
  background: #020617;
}

.wfh-wall__thumbs img {
  aspect-ratio: 16 / 9;
}

.wfh-wall__thumbs span {
  display: block;
  padding: 6px;
  color: #93a4bb;
  font-size: 11px;
}

.wfh-wall__events {
  display: grid;
  gap: 10px;
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
  background: #38bdf8;
}

.wfh-wall__events strong {
  display: block;
  color: #e2e8f0;
  font-size: 13px;
}

.wfh-wall__events small {
  color: #8fa1b8;
  font-size: 12px;
}

.wfh-wall__events .empty {
  display: block;
  color: #8fa1b8;
  font-size: 13px;
}

.wfh-wall__activity {
  display: grid;
  grid-template-columns: minmax(0, 1fr) 380px;
  gap: 16px;
  padding-bottom: 24px;
}

.wfh-wall__location-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
  gap: 10px;
  margin-top: 12px;
}

.wfh-wall__location-grid article {
  border: 1px solid rgba(148, 163, 184, 0.18);
  border-radius: 10px;
  padding: 12px;
  background: #111d30;
}

.wfh-wall__location-grid strong,
.wfh-wall__location-grid span,
.wfh-wall__location-grid small {
  display: block;
}

.wfh-wall__location-grid strong {
  color: #e2e8f0;
  font-size: 13px;
}

.wfh-wall__location-grid span,
.wfh-wall__location-grid small,
.wfh-wall__plain-text,
.wfh-wall__thumbs p,
.wfh-wall__location-grid p {
  color: #93a4bb;
  font-size: 13px;
  line-height: 1.6;
}

.wfh-wall:fullscreen {
  overflow: auto;
  background: #07111f;
}

.wfh-wall:fullscreen .wfh-wall__shell {
  width: 100%;
  min-height: 100dvh;
  border: 0;
  border-radius: 0;
}

@media (max-width: 1280px) {
  .wfh-wall__stats {
    grid-template-columns: repeat(3, minmax(120px, 1fr));
  }

  .wfh-wall__grid-layout,
  .wfh-wall__focus,
  .wfh-wall__activity {
    grid-template-columns: 1fr;
  }

  .wfh-wall__employee-list,
  .wfh-wall__screen-grid {
    max-height: none;
  }
}

@media (max-width: 760px) {
  .wfh-wall__shell {
    border-radius: 10px;
  }

  .wfh-wall__header,
  .wfh-wall__bank-toolbar,
  .wfh-wall__viewer-header {
    align-items: stretch;
    flex-direction: column;
  }

  .wfh-wall__actions,
  .wfh-wall__button-row,
  .wfh-wall__tabs {
    width: 100%;
  }

  .wfh-wall__search,
  .wfh-wall__date,
  .wfh-wall__button,
  .wfh-wall__tabs button {
    flex: 1 1 100%;
    width: 100%;
  }

  .wfh-wall__stats {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }

  .wfh-wall__screen-grid {
    grid-template-columns: 1fr;
  }

  .wfh-wall__large-frame {
    min-height: 320px;
  }
}
</style>
