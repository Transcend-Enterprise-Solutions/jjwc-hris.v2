<?php

namespace App\Http\Controllers\Api;

use App\Exports\WfhMonitoringReportExport;
use App\Http\Controllers\Controller;
use App\Models\WfhMonitoringEvent;
use App\Models\WfhMonitoringScreenshot;
use App\Models\WfhMonitoringSessionRecord;
use App\Models\WfhMonitoringUrlRule;
use App\Support\WfhActivity;
use App\Support\WfhMonitoringClock;
use App\Support\WfhMonitoringReport;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class WfhMonitoringController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $date = $this->dateFromRequest($request);
        $search = trim((string) $request->query('search', ''));

        $sessions = WfhMonitoringSessionRecord::query()
            ->with(['user.userData', 'latestScreenshot', 'latestLocationPing'])
            ->whereDate('started_at', $date)
            ->when($search, function ($query) use ($search) {
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
            ->limit(min((int) $request->query('limit', 80), 150))
            ->get();

        return response()->json([
            'date' => $date->toDateString(),
            'stats' => $this->stats($sessions),
            'sessions' => $sessions->map(fn ($session) => $this->sessionPayload($session))->values(),
        ]);
    }

    public function show(WfhMonitoringSessionRecord $session): JsonResponse
    {
        $session->load([
            'user.userData',
            'locationPings' => fn ($query) => $query->latest('occurred_at')->limit(40),
            'screenshots' => fn ($query) => $query->latest('captured_at')->limit(12),
            'latestScreenshot',
            'latestLocationPing',
        ]);
        $activityDate = Carbon::parse($session->started_at ?: $session->created_at);
        $events = WfhMonitoringEvent::query()
            ->where('user_id', $session->user_id)
            ->whereBetween('occurred_at', [
                $activityDate->copy()->startOfDay(),
                $activityDate->copy()->endOfDay(),
            ])
            ->whereNotIn('event_type', [
                'live_screen_requested',
                'live_screen_stopped',
                'live_media_requested',
                'live_media_stopped',
                'live_snapshots_started',
                'live_snapshots_stopped',
                'snapshot_requested',
                'screenshot_captured',
            ])
            ->latest('occurred_at')
            ->limit(80)
            ->get();

        return response()->json([
            'session' => $this->sessionPayload($session, true),
            'events' => $events->map(fn ($event) => $this->eventPayload($event))->values(),
            'locations' => $this->locationTrail($session),
            'screenshots' => $session->screenshots->map(fn ($screenshot) => $this->screenshotPayload($screenshot))->values(),
        ]);
    }

    public function gps(WfhMonitoringSessionRecord $session): JsonResponse
    {
        $session->load([
            'user.userData',
            'locationPings' => fn ($query) => $query->latest('occurred_at')->limit(80),
        ]);

        return response()->json([
            'session' => $this->sessionPayload($session),
            'locations' => $this->locationTrail($session),
        ]);
    }

    public function report(Request $request)
    {
        $validated = $request->validate([
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'employee' => ['nullable', 'string', 'max:100'],
        ]);
        $startDate = Carbon::parse($validated['start_date'])->startOfDay();
        $endDate = Carbon::parse($validated['end_date'])->endOfDay();

        abort_if($startDate->diffInDays($endDate) > 366, 422, 'The report range cannot exceed 366 days.');

        $employee = trim((string) ($validated['employee'] ?? '')) ?: null;
        $rows = WfhMonitoringReport::rows($startDate, $endDate, $employee);
        $filename = "WFH-Daily-Report-{$startDate->format('Ymd')}-{$endDate->format('Ymd')}.xlsx";

        return Excel::download(new WfhMonitoringReportExport($rows, $startDate, $endDate, $employee), $filename);
    }

    public function reportPreview(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'employee' => ['nullable', 'string', 'max:100'],
        ]);
        $startDate = Carbon::parse($validated['start_date'])->startOfDay();
        $endDate = Carbon::parse($validated['end_date'])->endOfDay();

        abort_if($startDate->diffInDays($endDate) > 366, 422, 'The report range cannot exceed 366 days.');

        $employee = trim((string) ($validated['employee'] ?? '')) ?: null;
        $rows = WfhMonitoringReport::rows($startDate, $endDate, $employee);

        return response()->json([
            'total' => $rows->count(),
            'rows' => $rows->take(100)->map(fn ($row) => [
                'date' => $row[0],
                'employeeId' => $row[2],
                'employeeName' => $row[3],
                'sessions' => $row[4],
                'firstTimeIn' => $row[5],
                'lastActivity' => $row[6],
                'online' => $row[7],
                'active' => $row[8],
                'idle' => $row[9],
                'activityRate' => $row[10],
                'workStatus' => $row[11],
                'latitude' => $row[13],
                'longitude' => $row[14],
            ])->values(),
        ]);
    }

    public function rules(): JsonResponse
    {
        return response()->json([
            'rules' => WfhMonitoringUrlRule::query()
                ->latest()
                ->limit(100)
                ->get()
                ->map(fn ($rule) => [
                    'id' => $rule->id,
                    'pattern' => $rule->pattern,
                    'classification' => $rule->classification,
                    'isActive' => (bool) $rule->is_active,
                ]),
        ]);
    }

    public function requestLiveScreen(WfhMonitoringSessionRecord $session): JsonResponse
    {
        if ($session->status === 'ended') {
            return response()->json(['message' => 'Session has ended.'], 422);
        }

        $token = (string) str()->uuid();
        $meta = $session->meta ?? [];

        unset($meta['live_snapshots']);

        $meta['live_screen'] = [
            'token' => $token,
            'status' => 'requested',
            'requested_by' => 'wfh-monitoring-api',
            'requested_at' => now()->toIso8601String(),
            'offer' => null,
            'answer' => null,
        ];

        $session->update([
            'meta' => $meta,
            'screenshot_request_pending' => false,
        ]);
        $this->logEvent($session, 'live_screen_requested', 'Supervisor opened live screen view');

        return response()->json(['token' => $token]);
    }

    public function publishLiveScreenOffer(Request $request, WfhMonitoringSessionRecord $session): JsonResponse
    {
        $validated = $request->validate([
            'token' => 'required|string',
            'offer' => 'required|array',
        ]);

        $this->updateLiveSignal($session, 'live_screen', $validated['token'], [
            'status' => 'offer_ready',
            'offer' => $validated['offer'],
            'offer_at' => now()->toIso8601String(),
        ]);

        return response()->json(['ok' => true]);
    }

    public function liveScreenSignal(WfhMonitoringSessionRecord $session): JsonResponse
    {
        return response()->json([
            'signal' => ($session->meta ?? [])['live_screen'] ?? null,
        ]);
    }

    public function stopLiveScreen(WfhMonitoringSessionRecord $session): JsonResponse
    {
        $this->stopSignal($session, 'live_screen');
        $this->logEvent($session, 'live_screen_stopped', 'Supervisor stopped live screen view');

        return response()->json(['ok' => true]);
    }

    public function requestLiveMedia(WfhMonitoringSessionRecord $session): JsonResponse
    {
        if ($session->status === 'ended') {
            return response()->json(['message' => 'Session has ended.'], 422);
        }

        $token = (string) str()->uuid();
        $meta = $session->meta ?? [];
        $meta['live_media'] = [
            'token' => $token,
            'status' => 'requested',
            'requested_by' => 'wfh-monitoring-api',
            'requested_at' => now()->toIso8601String(),
            'offer' => null,
            'answer' => null,
        ];

        $session->update(['meta' => $meta]);
        $this->logEvent($session, 'live_media_requested', 'Supervisor opened employee camera and microphone');

        return response()->json(['token' => $token]);
    }

    public function publishLiveMediaOffer(Request $request, WfhMonitoringSessionRecord $session): JsonResponse
    {
        $validated = $request->validate([
            'token' => 'required|string',
            'offer' => 'required|array',
        ]);

        $this->updateLiveSignal($session, 'live_media', $validated['token'], [
            'status' => 'offer_ready',
            'offer' => $validated['offer'],
            'offer_at' => now()->toIso8601String(),
        ]);

        return response()->json(['ok' => true]);
    }

    public function liveMediaSignal(WfhMonitoringSessionRecord $session): JsonResponse
    {
        return response()->json([
            'signal' => ($session->meta ?? [])['live_media'] ?? null,
        ]);
    }

    public function stopLiveMedia(WfhMonitoringSessionRecord $session): JsonResponse
    {
        $this->stopSignal($session, 'live_media');
        $this->logEvent($session, 'live_media_stopped', 'Supervisor stopped employee camera and microphone view');

        return response()->json(['ok' => true]);
    }

    public function requestSnapshot(WfhMonitoringSessionRecord $session): JsonResponse
    {
        if ($session->status === 'ended') {
            return response()->json(['message' => 'Session has ended.'], 422);
        }

        $session->update([
            'screenshot_request_pending' => true,
            'screenshot_requested_at' => now(),
            'screenshot_requested_by' => auth()->id(),
        ]);

        $this->logEvent($session, 'screenshot_requested', 'Supervisor requested a screen snapshot');

        return response()->json([
            'ok' => true,
            'session' => $this->sessionPayload($session->fresh(['user.userData', 'latestScreenshot'])),
        ]);
    }

    public function startLiveSnapshots(WfhMonitoringSessionRecord $session): JsonResponse
    {
        if ($session->status === 'ended') {
            return response()->json(['message' => 'Session has ended.'], 422);
        }

        $token = (string) str()->uuid();
        $meta = $session->meta ?? [];
        $meta['live_snapshots'] = [
            'token' => $token,
            'status' => 'active',
            'interval_seconds' => 5,
            'requested_by' => auth()->id() ?: 'wfh-monitoring-api',
            'requested_at' => now()->toIso8601String(),
        ];

        unset($meta['live_screen'], $meta['live_media']);

        $session->update([
            'meta' => $meta,
            'screenshot_request_pending' => true,
            'screenshot_requested_at' => now(),
            'screenshot_requested_by' => auth()->id(),
        ]);

        $this->logEvent($session, 'live_snapshots_started', 'Supervisor started live screen snapshots');

        return response()->json([
            'token' => $token,
            'intervalSeconds' => 5,
            'snapshot' => $session->latestScreenshot ? $this->screenshotPayload($session->latestScreenshot) : null,
        ]);
    }

    public function latestScreenshot(WfhMonitoringSessionRecord $session): JsonResponse
    {
        $screenshot = $session->latestScreenshot;

        return response()->json([
            'snapshot' => $screenshot ? $this->screenshotPayload($screenshot) : null,
        ]);
    }

    public function stopLiveSnapshots(WfhMonitoringSessionRecord $session): JsonResponse
    {
        $meta = $session->meta ?? [];

        if (isset($meta['live_snapshots'])) {
            $meta['live_snapshots']['status'] = 'stopped';
            $meta['live_snapshots']['stopped_at'] = now()->toIso8601String();
        }

        $session->update([
            'meta' => $meta,
            'screenshot_request_pending' => false,
        ]);
        $this->logEvent($session, 'live_snapshots_stopped', 'Supervisor stopped live screen snapshots');

        return response()->json(['ok' => true]);
    }

    public function screenshot(WfhMonitoringScreenshot $screenshot)
    {
        abort_unless(Storage::disk('public')->exists($screenshot->path), 404);

        return Storage::disk('public')->response($screenshot->path);
    }

    protected function updateLiveSignal(WfhMonitoringSessionRecord $session, string $key, string $token, array $changes): void
    {
        $meta = $session->meta ?? [];
        $signal = $meta[$key] ?? [];

        abort_unless(($signal['token'] ?? null) === $token, 409, 'Live signal token does not match.');

        $meta[$key] = array_merge($signal, $changes);
        $session->update(['meta' => $meta]);
    }

    protected function stopSignal(WfhMonitoringSessionRecord $session, string $key): void
    {
        $meta = $session->meta ?? [];

        if (isset($meta[$key])) {
            $meta[$key]['status'] = 'stopped';
            $meta[$key]['stopped_at'] = now()->toIso8601String();
        }

        $session->update(['meta' => $meta]);
    }

    protected function sessionPayload(WfhMonitoringSessionRecord $session, bool $includeMeta = false): array
    {
        [$activeSeconds, $idleSeconds] = WfhMonitoringClock::normalizeActivityTotals(
            (int) ($session->active_seconds ?? 0),
            (int) ($session->idle_seconds ?? 0),
            (int) ($session->online_seconds ?? 0)
        );

        $payload = [
            'id' => $session->id,
            'employee' => $this->employeePayload($session),
            'state' => $this->monitoringState($session),
            'status' => $session->status,
            'workStatus' => $session->work_status,
            'screenShareActive' => (bool) $session->screen_share_active,
            'geofenceStatus' => is_numeric($session->last_latitude) && is_numeric($session->last_longitude) ? 'located' : 'unavailable',
            'startedAt' => WfhMonitoringClock::sessionStartedAt($session)?->toIso8601String(),
            'endedAt' => optional($session->ended_at)->toIso8601String(),
            'lastActivityAt' => optional($session->last_activity_at)->toIso8601String(),
            'onlineSeconds' => (int) ($session->online_seconds ?? 0),
            'activeSeconds' => $activeSeconds,
            'idleSeconds' => $idleSeconds,
            'activityCount' => (int) ($session->activity_count ?? 0),
            'activityScore' => (int) ($session->activity_score ?? 0),
            'browserName' => WfhActivity::browserName($session->user_agent),
            'currentPageTitle' => $session->browser_tab_title,
            'currentPageUrl' => $session->browser_url,
            'visibilityState' => $session->visibility_state,
            'devicePlatform' => $session->device_platform,
            'isPwa' => (bool) $session->is_pwa,
            'urlClassification' => $session->url_classification,
            'interactions' => [
                'keystrokes' => (int) ($session->keystroke_count ?? 0),
                'mouseMoves' => (int) ($session->mouse_activity_count ?? 0),
                'clicks' => (int) ($session->click_count ?? 0),
                'touches' => (int) ($session->touch_count ?? 0),
            ],
            'lastLocation' => $this->locationPayload($session),
            'latestScreenshot' => $session->relationLoaded('latestScreenshot') && $session->latestScreenshot
                ? $this->screenshotPayload($session->latestScreenshot)
                : null,
            'liveSnapshotsActive' => (($session->meta ?? [])['live_snapshots']['status'] ?? null) === 'active',
        ];

        if ($includeMeta) {
            $payload['meta'] = $session->meta ?? [];
        }

        return $payload;
    }

    protected function employeePayload(WfhMonitoringSessionRecord $session): array
    {
        $user = $session->user;

        return [
            'id' => $user?->id,
            'empCode' => $user?->emp_code,
            'name' => $this->employeeDisplayName($user),
        ];
    }

    protected function eventPayload(WfhMonitoringEvent $event): array
    {
        return [
            'id' => $event->id,
            'type' => $event->event_type,
            'label' => $event->label,
            'details' => $event->details,
            'payload' => $event->payload,
            'occurredAt' => optional($event->occurred_at)->toIso8601String(),
        ];
    }

    protected function locationTrail(WfhMonitoringSessionRecord $session): array
    {
        return $session->locationPings
            ->sortBy('occurred_at')
            ->values()
            ->map(fn ($ping) => [
                'id' => $ping->id,
                'lat' => (float) $ping->latitude,
                'lng' => (float) $ping->longitude,
                'accuracy' => $ping->accuracy ? (float) $ping->accuracy : null,
                'status' => 'Location reported',
                'locationLabel' => $ping->location_label,
                'occurredAt' => optional($ping->occurred_at)->toIso8601String(),
            ])
            ->filter(fn ($point) => is_finite($point['lat']) && is_finite($point['lng']))
            ->values()
            ->all();
    }

    protected function locationPayload(WfhMonitoringSessionRecord $session): ?array
    {
        if (! is_numeric($session->last_latitude) || ! is_numeric($session->last_longitude)) {
            return null;
        }

        $latestPing = $session->relationLoaded('latestLocationPing') ? $session->latestLocationPing : null;

        return [
            'lat' => (float) $session->last_latitude,
            'lng' => (float) $session->last_longitude,
            'accuracy' => $session->last_location_accuracy ? (float) $session->last_location_accuracy : null,
            'status' => 'Location reported',
            'label' => $session->field_location_label,
            'source' => $latestPing?->source ?: 'browser',
            'occurredAt' => optional($latestPing?->occurred_at ?: $session->last_activity_at)->toIso8601String(),
        ];
    }

    protected function screenshotPayload(WfhMonitoringScreenshot $screenshot): array
    {
        return [
            'id' => $screenshot->id,
            'url' => route('wfh-monitoring.screenshot', $screenshot),
            'captureType' => $screenshot->capture_type,
            'capturedAt' => optional($screenshot->captured_at)->toIso8601String(),
        ];
    }

    protected function stats($sessions): array
    {
        return [
            'total' => $sessions->count(),
            'active' => $sessions->filter(fn ($session) => $this->monitoringState($session) === 'Active')->count(),
            'offline' => $sessions->filter(fn ($session) => $this->monitoringState($session) === 'Offline')->count(),
            'afk' => $sessions->filter(fn ($session) => $this->monitoringState($session) === 'AFK')->count(),
            'onBreak' => $sessions->filter(fn ($session) => $this->monitoringState($session) === 'On Break')->count(),
            'screenOff' => $sessions->filter(fn ($session) => $this->monitoringState($session) === 'Screen Off')->count(),
            'located' => $sessions->filter(fn ($session) => is_numeric($session->last_latitude) && is_numeric($session->last_longitude))->count(),
        ];
    }

    protected function monitoringState(WfhMonitoringSessionRecord $session): string
    {
        if ($session->status === 'ended') {
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

    protected function employeeDisplayName($user): string
    {
        if (! $user) {
            return 'Unknown employee';
        }

        if ($user->userData) {
            return trim($user->userData->surname.', '.$user->userData->first_name.' '.($user->userData->middle_name ?? ''));
        }

        return (string) $user->name;
    }

    protected function dateFromRequest(Request $request): Carbon
    {
        return $request->query('date')
            ? Carbon::parse($request->query('date'))->startOfDay()
            : Carbon::today();
    }

    protected function logEvent(WfhMonitoringSessionRecord $session, string $type, string $label): void
    {
        WfhMonitoringEvent::create([
            'wfh_monitoring_session_id' => $session->id,
            'user_id' => $session->user_id,
            'event_type' => $type,
            'label' => $label,
            'payload' => ['source' => 'wfh-monitoring-api'],
            'occurred_at' => now(),
        ]);
    }
}
