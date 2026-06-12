<?php

namespace App\Livewire\Admin;

use App\Models\WfhMonitoringEvent;
use App\Models\WfhMonitoringSessionRecord;
use App\Models\WfhMonitoringUrlRule;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('WFH Monitoring')]
class WfhMonitoring extends Component
{
    public $search = '';
    public $selectedMonitoringSessionId;
    public $gpsSelectedSessionId;
    public $newUrlPattern = '';
    public $newUrlClassification = 'productive';

    public function render()
    {
        $monitoringSessions = WfhMonitoringSessionRecord::query()
            ->with('user.userData')
            ->whereDate('started_at', Carbon::today())
            ->when($this->search, function ($query) {
                $search = trim($this->search);

                $query->whereHas('user', function ($userQuery) use ($search) {
                    $userQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('emp_code', 'like', "%{$search}%")
                        ->orWhereHas('userData', function ($userDataQuery) use ($search) {
                            $userDataQuery->where('surname', 'like', "%{$search}%")
                                ->orWhere('first_name', 'like', "%{$search}%");
                        });
                });
            })
            ->latest('last_activity_at')
            ->latest('started_at')
            ->get();

        $selectedMonitoringSession = $this->selectedMonitoringSessionId
            ? WfhMonitoringSessionRecord::with('user.userData')->find($this->selectedMonitoringSessionId)
            : $monitoringSessions->first();

        if ($selectedMonitoringSession) {
            $selectedMonitoringSession->load([
                'locationPings' => fn ($query) => $query->latest('occurred_at')->limit(6),
                'screenshots' => fn ($query) => $query->latest('captured_at')->limit(8),
                'events' => fn ($query) => $query->latest('occurred_at')->limit(12),
            ]);
        }

        $gpsSelectedSession = $this->gpsSelectedSessionId
            ? WfhMonitoringSessionRecord::with('user.userData')->find($this->gpsSelectedSessionId)
            : $selectedMonitoringSession;

        $gpsLocationTrail = [];
        $gpsCurrentLocationLabel = null;
        if ($gpsSelectedSession) {
            $gpsSelectedSession->load([
                'locationPings' => fn ($query) => $query->latest('occurred_at')->limit(40),
            ]);

            $gpsCurrentLocationLabel = $this->displayLocationLabel(
                $gpsSelectedSession->last_latitude,
                $gpsSelectedSession->last_longitude,
                $gpsSelectedSession->field_location_label,
            );

            $gpsLocationTrail = $gpsSelectedSession->locationPings
                ->sortBy('occurred_at')
                ->values()
                ->map(function ($ping) use ($gpsSelectedSession) {
                    $resolvedLocation = $this->displayLocationLabel(
                        $ping->latitude,
                        $ping->longitude,
                        $ping->location_label ?: $gpsSelectedSession->field_location_label,
                    );

                    return [
                        'lat' => (float) $ping->latitude,
                        'lng' => (float) $ping->longitude,
                        'time' => optional($ping->occurred_at)->format('M d, h:i A'),
                        'label' => $resolvedLocation,
                        'status' => $this->geofenceStatusLabel($ping->geofence_status),
                        'accuracy' => $ping->accuracy ? number_format((float) $ping->accuracy, 1) . 'm' : null,
                        'distance' => $ping->distance_from_geofence ? number_format((float) $ping->distance_from_geofence, 1) . 'm' : null,
                    ];
                })
                ->filter(fn ($point) => is_numeric($point['lat']) && is_numeric($point['lng']))
                ->values()
                ->all();
        }

        return view('livewire.admin.wfh-monitoring', [
            'monitoringSessions' => $monitoringSessions,
            'monitoringStats' => $this->buildMonitoringStats($monitoringSessions),
            'selectedMonitoringSession' => $selectedMonitoringSession,
            'gpsSelectedSession' => $gpsSelectedSession,
            'gpsLocationTrail' => $gpsLocationTrail,
            'gpsCurrentLocationLabel' => $gpsCurrentLocationLabel,
            'urlRules' => WfhMonitoringUrlRule::query()->latest()->limit(12)->get(),
        ]);
    }

    public function selectMonitoringSession($id, $module = null)
    {
        $this->selectedMonitoringSessionId = $id;
        $this->gpsSelectedSessionId = $id;
    }

    public function monitoringStateFor($session)
    {
        if (! $session || $session->status === 'ended') {
            return 'Ended';
        }

        if ($session->work_status === 'On Break') {
            return 'On Break';
        }

        if ($session->last_activity_at && Carbon::parse($session->last_activity_at)->lt(now()->subMinutes(2))) {
            return 'Offline';
        }

        if ($session->status === 'afk') {
            return 'AFK';
        }

        if (! $session->screen_share_active) {
            return 'Screen Off';
        }

        return 'Active';
    }

    public function monitoringStateClass($state)
    {
        return match ($state) {
            'Active' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300',
            'AFK' => 'bg-amber-100 text-amber-700 dark:bg-amber-500/10 dark:text-amber-300',
            'On Break' => 'bg-sky-100 text-sky-700 dark:bg-sky-500/10 dark:text-sky-300',
            'Offline' => 'bg-rose-100 text-rose-700 dark:bg-rose-500/10 dark:text-rose-300',
            'Screen Off' => 'bg-orange-100 text-orange-700 dark:bg-orange-500/10 dark:text-orange-300',
            default => 'bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-200',
        };
    }

    public function requestLiveScreen($sessionId)
    {
        $session = WfhMonitoringSessionRecord::find($sessionId);

        if (! $session || $session->status === 'ended') {
            return null;
        }

        $token = (string) str()->uuid();
        $meta = $session->meta ?? [];
        $meta['live_screen'] = [
            'token' => $token,
            'status' => 'requested',
            'requested_by' => Auth::id(),
            'requested_at' => now()->toIso8601String(),
            'offer' => null,
            'answer' => null,
        ];

        $session->update(['meta' => $meta]);
        $this->logAdminEvent($session, 'live_screen_requested', 'Supervisor requested live screen view');

        return ['token' => $token];
    }

    public function publishLiveOffer($sessionId, $token, $offer)
    {
        $session = WfhMonitoringSessionRecord::find($sessionId);

        if (! $session || ! is_array($offer)) {
            return;
        }

        $meta = $session->meta ?? [];
        $liveScreen = $meta['live_screen'] ?? [];

        if (($liveScreen['token'] ?? null) !== $token) {
            return;
        }

        $liveScreen['status'] = 'offer_ready';
        $liveScreen['offer'] = $offer;
        $liveScreen['offer_at'] = now()->toIso8601String();
        $meta['live_screen'] = $liveScreen;

        $session->update(['meta' => $meta]);
    }

    public function getLiveSignal($sessionId)
    {
        $session = WfhMonitoringSessionRecord::find($sessionId);
        $meta = $session?->meta ?? [];

        return $meta['live_screen'] ?? null;
    }

    public function stopLiveScreen($sessionId)
    {
        $session = WfhMonitoringSessionRecord::find($sessionId);

        if (! $session) {
            return;
        }

        $meta = $session->meta ?? [];

        if (isset($meta['live_screen'])) {
            $meta['live_screen']['status'] = 'stopped';
            $meta['live_screen']['stopped_at'] = now()->toIso8601String();
        }

        $session->update(['meta' => $meta]);
        $this->logAdminEvent($session, 'live_screen_stopped', 'Supervisor stopped live screen view');
    }

    public function requestLiveMedia($sessionId)
    {
        $session = WfhMonitoringSessionRecord::find($sessionId);

        if (! $session || $session->status === 'ended') {
            return null;
        }

        $token = (string) str()->uuid();
        $meta = $session->meta ?? [];
        $meta['live_media'] = [
            'token' => $token,
            'status' => 'requested',
            'requested_by' => Auth::id(),
            'requested_at' => now()->toIso8601String(),
            'offer' => null,
            'answer' => null,
        ];

        $session->update(['meta' => $meta]);
        $this->logAdminEvent($session, 'live_media_requested', 'Supervisor requested employee camera and microphone');

        return ['token' => $token];
    }

    public function publishLiveMediaOffer($sessionId, $token, $offer)
    {
        $session = WfhMonitoringSessionRecord::find($sessionId);

        if (! $session || ! is_array($offer)) {
            return;
        }

        $meta = $session->meta ?? [];
        $liveMedia = $meta['live_media'] ?? [];

        if (($liveMedia['token'] ?? null) !== $token) {
            return;
        }

        $liveMedia['status'] = 'offer_ready';
        $liveMedia['offer'] = $offer;
        $liveMedia['offer_at'] = now()->toIso8601String();
        $meta['live_media'] = $liveMedia;

        $session->update(['meta' => $meta]);
    }

    public function getLiveMediaSignal($sessionId)
    {
        $session = WfhMonitoringSessionRecord::find($sessionId);
        $meta = $session?->meta ?? [];

        return $meta['live_media'] ?? null;
    }

    public function stopLiveMedia($sessionId)
    {
        $session = WfhMonitoringSessionRecord::find($sessionId);

        if (! $session) {
            return;
        }

        $meta = $session->meta ?? [];

        if (isset($meta['live_media'])) {
            $meta['live_media']['status'] = 'stopped';
            $meta['live_media']['stopped_at'] = now()->toIso8601String();
        }

        $session->update(['meta' => $meta]);
        $this->logAdminEvent($session, 'live_media_stopped', 'Supervisor stopped employee camera and microphone view');
    }

    public function requestScreenSnapshot($sessionId)
    {
        $session = WfhMonitoringSessionRecord::find($sessionId);

        if (! $session || $session->status === 'ended') {
            $this->dispatch('swal', [
                'title' => 'Snapshot unavailable',
                'text' => 'This monitoring session is no longer active.',
                'icon' => 'warning',
            ]);

            return;
        }

        $session->update([
            'screenshot_request_pending' => true,
            'screenshot_requested_at' => now(),
            'screenshot_requested_by' => Auth::id(),
        ]);

        $this->selectedMonitoringSessionId = $session->id;
        $this->logAdminEvent($session, 'screenshot_requested', 'Supervisor requested an on-demand screen snapshot');

        $this->dispatch('swal', [
            'title' => 'Snapshot requested',
            'text' => 'The employee browser will capture it on the next monitoring heartbeat.',
            'icon' => 'success',
        ]);
    }

    public function startLiveSnapshots($sessionId)
    {
        $session = WfhMonitoringSessionRecord::find($sessionId);

        if (! $session || $session->status === 'ended') {
            return null;
        }

        $token = (string) str()->uuid();
        $meta = $session->meta ?? [];
        $meta['live_snapshots'] = [
            'token' => $token,
            'status' => 'active',
            'interval_seconds' => 5,
            'requested_by' => Auth::id(),
            'requested_at' => now()->toIso8601String(),
        ];

        unset($meta['live_screen']);

        $session->update([
            'meta' => $meta,
            'screenshot_request_pending' => true,
            'screenshot_requested_at' => now(),
            'screenshot_requested_by' => Auth::id(),
        ]);

        $this->selectedMonitoringSessionId = $session->id;
        $this->logAdminEvent($session, 'live_snapshots_started', 'Supervisor started shared-hosting live screen snapshots');

        return [
            'token' => $token,
            'intervalSeconds' => 5,
            'snapshot' => $this->latestScreenSnapshot($session),
        ];
    }

    public function getLatestScreenSnapshot($sessionId)
    {
        $session = WfhMonitoringSessionRecord::find($sessionId);

        return $this->latestScreenSnapshot($session);
    }

    public function stopLiveSnapshots($sessionId)
    {
        $session = WfhMonitoringSessionRecord::find($sessionId);

        if (! $session) {
            return;
        }

        $meta = $session->meta ?? [];

        if (isset($meta['live_snapshots'])) {
            $meta['live_snapshots']['status'] = 'stopped';
            $meta['live_snapshots']['stopped_at'] = now()->toIso8601String();
        }

        $session->update(['meta' => $meta]);
        $this->logAdminEvent($session, 'live_snapshots_stopped', 'Supervisor stopped live screen snapshots');
    }

    public function addUrlRule()
    {
        $this->validate([
            'newUrlPattern' => 'required|string|max:255',
            'newUrlClassification' => 'required|in:productive,non_productive,neutral',
        ]);

        WfhMonitoringUrlRule::updateOrCreate(
            ['pattern' => trim($this->newUrlPattern)],
            [
                'classification' => $this->newUrlClassification,
                'is_active' => true,
            ]
        );

        $this->newUrlPattern = '';
        $this->newUrlClassification = 'productive';
    }

    public function toggleUrlRule($ruleId)
    {
        $rule = WfhMonitoringUrlRule::find($ruleId);

        if ($rule) {
            $rule->update(['is_active' => ! $rule->is_active]);
        }
    }

    public function employeeDisplayName($user)
    {
        if (! $user) {
            return 'Unknown employee';
        }

        if ($user->userData) {
            return trim($user->userData->surname . ', ' . $user->userData->first_name . ' ' . ($user->userData->middle_name ?? ''));
        }

        return $user->name;
    }

    public function geofenceStatusLabel($status)
    {
        return match ($status) {
            'inside' => 'Inside approved location',
            'outside' => 'Outside approved location',
            'unregistered', 'location_not_registered' => '',
            'unknown' => 'Location unavailable',
            default => ucfirst((string) $status),
        };
    }

    public function displayLocationLabel($latitude, $longitude, ?string $fallback = null)
    {
        if (! is_numeric($latitude) || ! is_numeric($longitude)) {
            return $fallback ? trim($fallback) : null;
        }

        $fallback = $fallback ? trim($fallback) : null;

        if ($fallback) {
            return $fallback;
        }

        return number_format((float) $latitude, 5) . ', ' . number_format((float) $longitude, 5);
    }

    protected function latestScreenSnapshot($session)
    {
        if (! $session) {
            return null;
        }

        $screenshot = $session->screenshots()
            ->latest('captured_at')
            ->first();

        if (! $screenshot) {
            return null;
        }

        return [
            'id' => $screenshot->id,
            'url' => route('wfh-monitoring.screenshot', $screenshot),
            'capturedAt' => optional($screenshot->captured_at)->format('M d, h:i:s A'),
            'captureType' => str_replace('_', ' ', $screenshot->capture_type),
        ];
    }

    protected function buildMonitoringStats($sessions)
    {
        return [
            'total' => $sessions->count(),
            'active' => $sessions->filter(fn ($session) => $this->monitoringStateFor($session) === 'Active')->count(),
            'offline' => $sessions->filter(fn ($session) => $this->monitoringStateFor($session) === 'Offline')->count(),
            'afk' => $sessions->filter(fn ($session) => $this->monitoringStateFor($session) === 'AFK')->count(),
            'on_break' => $sessions->filter(fn ($session) => $this->monitoringStateFor($session) === 'On Break')->count(),
            'screen_off' => $sessions->filter(fn ($session) => $this->monitoringStateFor($session) === 'Screen Off')->count(),
            'geofence_alerts' => $sessions->where('geofence_status', 'outside')->count(),
        ];
    }

    protected function logAdminEvent($session, $type, $label, array $payload = [])
    {
        if (! $session) {
            return;
        }

        WfhMonitoringEvent::create([
            'wfh_monitoring_session_id' => $session->id,
            'user_id' => $session->user_id,
            'event_type' => $type,
            'label' => $label,
            'details' => $payload ? json_encode($payload) : null,
            'payload' => array_merge($payload, [
                'admin_user_id' => Auth::id(),
            ]),
            'occurred_at' => now(),
        ]);
    }
}
