<?php

namespace App\Livewire\User;

use App\Models\DTRSchedule;
use App\Models\EmployeesDtr;
use App\Models\Notification;
use App\Models\TransactionWFH;
use App\Models\Wfh;
use App\Models\WfhLocation;
use App\Models\WfhLocationRequests;
use App\Models\WfhMonitoringEvent;
use App\Models\WfhMonitoringLocationPing;
use App\Models\WfhMonitoringScreenshot;
use App\Models\WfhMonitoringSessionRecord;
use App\Models\WfhMonitoringUrlRule;
use Carbon\Carbon;
use DateTime;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class WfhAttendance extends Component
{
    use WithFileUploads;
    use WithPagination;

    public $isWFHDay;

    public $showConfirmation = false;

    public $punchState;

    public $errorMessage;

    public $verifyType;

    public $editLocation;

    public $hasWFHLocation;

    public $address;

    public $search;

    public $morningInDisabled = false;

    public $morningOutDisabled = true;

    public $afternoonInDisabled = true;

    public $afternoonOutDisabled = true;

    public $breakInDisabled = true;

    public $breakOutDisabled = true;

    public $scheduleType = 'WFH'; // Default value

    public $registeredLatitude;

    public $registeredLongitude;

    public $latitude = null;

    public $longitude = null;

    public $formattedTime = null;

    public $formattedTime2 = null;

    public $isWithinRadius;

    public $locReqGranted = true;

    public $hasRequested;

    public $approvedBy;

    public $approvedDate;

    public $disapprovedBy;

    public $disapprovedDate;

    public $newLat;

    public $newLng;

    public $editLocMessage;

    public $pageSize = 10;

    public $pageSizes = [10, 20, 30, 50, 100];

    public $approveOnly;

    public $isMyBirthday;

    public $wfhStatus;

    public $monitoringState = 'Offline';

    public $monitoringWorkStatus = 'WFH';

    public $monitoringLastActivity;

    public $monitoringSessionStartedAt;

    public $monitoringOnlineSeconds = 0;

    public $monitoringScreenShareActive = false;

    public $afkThresholdMinutes = 10;

    public $screenshotIntervalMinutes = 5;

    public $locationIntervalMinutes = 30;

    public $fieldWorkLocationLabel;

    public $fieldWorkPhoto;

    #[On('locationUpdated')]
    public function handleLocationUpdate($locationData)
    {
        if (is_string($locationData)) {
            $locationData = json_decode($locationData, true);
        }

        $this->latitude = $locationData['latitude'] ?? null;
        $this->longitude = $locationData['longitude'] ?? null;
        $this->formattedTime = $locationData['formattedTime'] ?? null;
        $this->formattedTime2 = $locationData['formattedTime2'] ?? null;

        // Check if within allowed radius and update UI accordingly
        $this->isWithinRadius = $this->isWithinAllowedRadius();
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        // Radius of the Earth in meters
        $R = 6371000;

        $lat1 = deg2rad($lat1);
        $lon1 = deg2rad($lon1);
        $lat2 = deg2rad($lat2);
        $lon2 = deg2rad($lon2);

        // Differences in coordinates
        $dLat = $lat2 - $lat1;
        $dLon = $lon2 - $lon1;

        // Haversine formula
        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos($lat1) * cos($lat2) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        // Distance in meters
        $distance = $R * $c;

        return $distance;
    }

    // Method to check if current location is within radius
    private function isWithinAllowedRadius()
    {
        if (! $this->hasWFHLocation || ! $this->latitude || ! $this->longitude) {
            return false;
        }

        $distance = $this->calculateDistance(
            $this->registeredLatitude,
            $this->registeredLongitude,
            $this->latitude,
            $this->longitude
        );

        // Check if within 20 meters
        return $distance <= 20;
    }

    // public function checkWFHDay()
    // {
    //     $user = Auth::user();
    //     $today = Carbon::now()->format('l');
    //     $currentDate = Carbon::now()->format('Y-m-d');
    //     $startOfMonth = Carbon::now()->startOfMonth()->format('Y-m-d');

    //     // Get the most recent active schedule for the current month
    //     $schedule = DTRSchedule::where('emp_code', $user->emp_code)
    //         ->where(function ($query) use ($startOfMonth, $currentDate) {
    //             $query->where('start_date', '>=', $startOfMonth)
    //                 ->orWhere(function ($q) use ($currentDate) {
    //                     $q->where('end_date', '>=', $currentDate);
    //                 });
    //         })
    //         ->orderBy('start_date', 'desc')
    //         ->first();

    //     if ($schedule) {
    //         $wfhDays = explode(',', $schedule->wfh_days);
    //         $startDate = Carbon::parse($schedule->start_date)->format('Y-m-d');
    //         $endDate = Carbon::parse($schedule->end_date)->format('Y-m-d');

    //         if (in_array($today, $wfhDays) && $currentDate >= $startDate && $currentDate <= $endDate) {
    //             $this->scheduleType = 'WFH';
    //         } else {
    //             $this->scheduleType = 'Onsite';
    //         }
    //     } else {
    //         $this->scheduleType = 'Onsite';
    //     }

    //     // Check if there is an approved WFH request for today
    //     $wfhRequest = Wfh::where('user_id', $user->id)
    //         ->where('wfhDay', $currentDate)
    //         ->where('status', 'approved')
    //         ->first();

    //     $this->wfhStatus = $wfhRequest ? 'approved' : null;
    // }
    public function checkWFHDay()
    {
        $user = Auth::user();
        $today = Carbon::now()->format('l'); // Day name (Monday, Tuesday, etc.)
        $currentDate = Carbon::now()->format('Y-m-d'); // Today's date

        // Get ONLY the schedule that is ACTIVE TODAY
        $schedule = DTRSchedule::where('emp_code', $user->emp_code)
            ->where('start_date', '<=', $currentDate)  // Schedule started on or before today
            ->where('end_date', '>=', $currentDate)    // Schedule ends on or after today
            ->orderBy('start_date', 'desc')  // If multiple active schedules, get the most recent
            ->first();

        if ($schedule) {
            $wfhDays = explode(',', $schedule->wfh_days);

            // Check if today's day name is in the WFH days list
            if (in_array($today, $wfhDays)) {
                $this->scheduleType = 'WFH';
            } else {
                $this->scheduleType = 'Onsite';
            }
        } else {
            // No active schedule found for today
            $this->scheduleType = 'Onsite';
        }

        // Still check for approved one-day WFH requests (this can override the schedule)
        $wfhRequest = Wfh::where('user_id', $user->id)
            ->where('wfhDay', $currentDate)
            ->where('status', 'approved')
            ->first();

        $this->wfhStatus = $wfhRequest ? 'approved' : null;
    }

    public function confirmPunch($state, $verifyType)
    {
        $this->punchState = $state;
        $this->verifyType = $verifyType;
        $this->showConfirmation = true;
    }

    public function closeConfirmation()
    {
        $this->showConfirmation = false;
        $this->errorMessage = null;
    }

    public function confirmYes()
    {
        $this->showConfirmation = false;
        $this->punch($this->punchState, $this->verifyType);
    }

    public function punch($state, $verifyType)
    {
        $user = Auth::user();
        $punchTime = Carbon::now();
        $existingPunch = TransactionWFH::where('emp_code', $user->emp_code)
            ->where('punch_state_display', 'WFH')
            ->where('verify_type_display', $verifyType)
            ->whereDate('punch_time', Carbon::today())
            ->oldest('punch_time')
            ->first();

        if ($existingPunch) {
            $this->refreshMonitoringState();
            $this->updateButtonStates();
            $this->showConfirmation = false;

            $this->dispatch('swal', [
                'title' => "{$verifyType} was already recorded.",
                'text' => 'Duplicate punch was ignored.',
                'icon' => 'info',
            ]);

            return;
        }

        TransactionWFH::create([
            'emp_code' => $user->emp_code,
            'punch_time' => $punchTime,
            'punch_state' => $state,
            'punch_state_display' => 'WFH',
            'verify_type_display' => $verifyType,
        ]);

        $this->syncMonitoringWithPunch($verifyType);

        // Update button states
        $this->updateButtonStates();

        $this->dispatch('swal', [
            'title' => "You have successfully punched the $verifyType!",
            'icon' => 'success',
        ]);
    }

    public function morningIn()
    {
        $this->punch(0, 'Morning In');
    }

    public function morningOut()
    {
        $this->punch(1, 'Morning Out');
    }

    public function afternoonIn()
    {
        $this->punch(0, 'Afternoon In');
    }

    public function afternoonOut()
    {
        $this->punch(1, 'Afternoon Out');
    }

    protected function usesWfhBreakMapping(): bool
    {
        return $this->scheduleType === 'WFH' || $this->wfhStatus === 'approved';
    }

    protected function getBreakPunchStates(): array
    {
        return $this->usesWfhBreakMapping() ? [5, 4] : [4, 5];
    }

    public function breakOut()
    {
        [$breakOutState] = $this->getBreakPunchStates();
        $this->punch($breakOutState, 'Break Out');
    }

    public function breakIn()
    {
        [, $breakInState] = $this->getBreakPunchStates();
        $this->punch($breakInState, 'Break In');
    }

    public function resetVariables()
    {
        $this->errorMessage = null;
        $this->editLocation = null;
        $this->showConfirmation = null;
        $this->newLat = null;
        $this->newLng = null;
        $this->editLocMessage = null;
        $this->approvedBy = null;
        $this->approvedDate = null;
        $this->disapprovedBy = null;
        $this->disapprovedDate = null;
    }

    public function updateButtonStates()
    {
        $user = Auth::user();
        $todayTransactions = TransactionWFH::where('emp_code', $user->emp_code)
            ->where('punch_state_display', 'WFH')
            ->whereDate('punch_time', Carbon::today())
            ->get();

        // Reset all buttons
        $this->morningInDisabled = false;
        $this->breakOutDisabled = true;
        $this->breakInDisabled = true;
        $this->afternoonOutDisabled = true;

        $hasMorningIn = $todayTransactions->contains('verify_type_display', 'Morning In');
        $hasBreakOut = $todayTransactions->contains('verify_type_display', 'Break Out');
        $hasBreakIn = $todayTransactions->contains('verify_type_display', 'Break In');
        $hasAfternoonOut = $todayTransactions->contains('verify_type_display', 'Afternoon Out');

        if ($hasMorningIn) {
            $this->morningInDisabled = true;
            $this->breakOutDisabled = false; // Break Out is next after Time In
        }

        if ($hasBreakOut) {
            $this->breakOutDisabled = true;
            $this->breakInDisabled = false; // Break In is next after Break Out
        }

        if ($hasBreakIn) {
            $this->breakInDisabled = true;
            $this->afternoonOutDisabled = false;
        }

        if ($hasAfternoonOut) {
            $this->afternoonOutDisabled = true;
        }
    }

    public function resetButtonStatesIfNeeded()
    {
        $this->updateButtonStates();
    }

    public function recordMonitoringHeartbeat(
        $browserTitle = null,
        $browserUrl = null,
        $visibilityState = 'visible',
        $latitude = null,
        $longitude = null,
        $accuracy = null,
        $screenShareActive = false,
        $isPwa = false,
        $platform = null,
        $userAgent = null,
        $activityMetrics = []
    )
    {
        if (! $this->isWfhAttendanceAvailable()) {
            return ['captureScreen' => false];
        }

        $session = $this->getOpenMonitoringSession();

        if (! $session && $this->hasOpenWfhTimeInToday()) {
            $session = $this->startMonitoringSession('Monitoring restored from active WFH time-in');
        }

        if (! $session) {
            return ['captureScreen' => false];
        }

        $session = $session->fresh();
        $now = now();
        $wasAfk = $session->status === 'afk';
        $isVisible = $visibilityState === 'visible';
        $previousVisibilityState = $session->visibility_state;
        $previousGeofenceStatus = $session->geofence_status;
        $meta = $session->meta ?? [];
        $onlineSeconds = (int) ($session->online_seconds ?? 0);
        $lastOnlineAccountedAt = ! empty($meta['last_online_accounted_at'])
            ? Carbon::parse($meta['last_online_accounted_at'])
            : null;

        $onlineSeconds += $lastOnlineAccountedAt
            ? min(60, max(0, $lastOnlineAccountedAt->diffInSeconds($now)))
            : 0;
        $meta['last_online_accounted_at'] = $now->toIso8601String();

        $geofence = $this->resolveGeofenceStatus($latitude, $longitude);
        $activityMetrics = is_array($activityMetrics) ? $activityMetrics : [];
        $urlClassification = $this->classifyMonitoringUrl($browserUrl);
        $captureScreen = (bool) $session->screenshot_request_pending;
        $activeSeconds = max(0, (int) ($activityMetrics['activeSeconds'] ?? 0));
        $idleSeconds = max(0, (int) ($activityMetrics['idleSeconds'] ?? 0));
        $keystrokes = max(0, (int) ($activityMetrics['keystrokes'] ?? 0));
        $mouseMoves = max(0, (int) ($activityMetrics['mouseMoves'] ?? 0));
        $clicks = max(0, (int) ($activityMetrics['clicks'] ?? 0));
        $touches = max(0, (int) ($activityMetrics['touches'] ?? 0));
        $activityScore = $this->calculateActivityScore($activeSeconds, $idleSeconds, $keystrokes, $mouseMoves, $clicks, $touches);

        $session->update([
            'status' => $isVisible && $wasAfk ? 'active' : $session->status,
            'browser_tab_title' => $browserTitle ?: $session->browser_tab_title,
            'browser_url' => $browserUrl ?: $session->browser_url,
            'last_activity_at' => $isVisible ? now() : $session->last_activity_at,
            'last_latitude' => $latitude ?: $session->last_latitude,
            'last_longitude' => $longitude ?: $session->last_longitude,
            'last_location_accuracy' => $accuracy ?: $session->last_location_accuracy,
            'last_geofence_distance' => $geofence['distance'],
            'geofence_status' => $geofence['status'],
            'visibility_state' => $visibilityState,
            'last_focused_at' => $isVisible ? now() : $session->last_focused_at,
            'last_blurred_at' => ! $isVisible ? now() : $session->last_blurred_at,
            'screen_share_active' => (bool) $screenShareActive,
            'screen_share_started_at' => $screenShareActive && ! $session->screen_share_started_at ? now() : $session->screen_share_started_at,
            'screen_share_ended_at' => ! $screenShareActive && $session->screen_share_active ? now() : $session->screen_share_ended_at,
            'device_platform' => $platform ?: $session->device_platform,
            'is_pwa' => (bool) $isPwa,
            'user_agent' => $userAgent ?: $session->user_agent,
            'activity_count' => $isVisible ? $session->activity_count + 1 : $session->activity_count,
            'active_seconds' => $session->active_seconds + $activeSeconds,
            'idle_seconds' => $session->idle_seconds + $idleSeconds,
            'keystroke_count' => $session->keystroke_count + $keystrokes,
            'mouse_activity_count' => $session->mouse_activity_count + $mouseMoves,
            'click_count' => $session->click_count + $clicks,
            'touch_count' => $session->touch_count + $touches,
            'activity_score' => $activityScore,
            'url_classification' => $urlClassification,
            'total_monitored_minutes' => max(0, Carbon::parse($session->started_at)->diffInMinutes(now())),
            'online_seconds' => $onlineSeconds,
            'screenshot_request_pending' => false,
            'meta' => array_merge($meta, [
                'last_visibility_state' => $visibilityState,
                'last_latitude' => $latitude,
                'last_longitude' => $longitude,
                'screen_share_active' => (bool) $screenShareActive,
                'last_activity_metrics' => $activityMetrics,
            ]),
        ]);

        if ($latitude && $longitude && $this->shouldRecordLocationPing($session)) {
            WfhMonitoringLocationPing::create([
                'wfh_monitoring_session_id' => $session->id,
                'user_id' => Auth::id(),
                'latitude' => $latitude,
                'longitude' => $longitude,
                'accuracy' => $accuracy,
                'distance_from_geofence' => $geofence['distance'],
                'geofence_status' => $geofence['status'],
                'source' => 'browser',
                'location_label' => $session->field_location_label,
                'photo_path' => $session->field_photo_path,
                'occurred_at' => now(),
            ]);

            if ($geofence['status'] === 'outside' && $this->shouldLogGeofenceAlert($session, $previousGeofenceStatus)) {
                $this->logMonitoringEvent($session, 'geofence_alert', 'Employee location is outside the approved WFH geofence', [
                    'distance_meters' => $geofence['distance'],
                    'allowed_radius_meters' => $geofence['radius'],
                ]);
            }
        }

        if ($wasAfk && $isVisible) {
            $this->logMonitoringEvent($session, 'session_resumed', 'Activity detected after AFK');
        }

        if (! $isVisible && $previousVisibilityState !== $visibilityState) {
            $this->logMonitoringEvent($session, 'background_monitoring_paused', 'HRIS tab is not in the foreground', [
                'visibility_state' => $visibilityState,
            ]);
        }

        $dailyOnlineSeconds = $this->getDailyOnlineSeconds($session, $onlineSeconds);

        $this->refreshMonitoringState();

        return [
            'captureScreen' => $captureScreen,
            'liveSnapshots' => $this->getLiveSnapshotRequest(),
            'afkThresholdMinutes' => (int) $session->afk_threshold_minutes,
            'screenshotIntervalMinutes' => (int) $session->screenshot_interval_minutes,
            'locationIntervalMinutes' => (int) $session->location_interval_minutes,
            'onlineSeconds' => $dailyOnlineSeconds,
            'sessionStartedAt' => optional($session->started_at)->toIso8601String(),
            'screenShareActive' => (bool) $screenShareActive,
        ];
    }

    public function shouldRequireMonitoringScreenShare()
    {
        return $this->isWfhAttendanceAvailable() && $this->hasOpenWfhTimeInToday();
    }

    public function recordMonitoringSignal($type, $label, $payload = [])
    {
        $session = $this->getOpenMonitoringSession();

        if (! $session) {
            return;
        }

        if (in_array($type, ['screen_share_stopped', 'browser_offline', 'before_unload'], true)) {
            $session->update([
                'tamper_alerted_at' => now(),
                'screen_share_active' => $type === 'screen_share_stopped' ? false : $session->screen_share_active,
                'screen_share_ended_at' => $type === 'screen_share_stopped' ? now() : $session->screen_share_ended_at,
            ]);
        }

        if ($type === 'browser_offline') {
            $session->update(['offline_alerted_at' => now()]);
        }

        $this->logMonitoringEvent($session, $type, $label, is_array($payload) ? $payload : []);
        $this->refreshMonitoringState();
    }

    public function recordScreenSnapshot($imageData, $captureType = 'periodic')
    {
        $session = $this->getOpenMonitoringSession();

        if (! $session || ! is_string($imageData) || ! str_starts_with($imageData, 'data:image/')) {
            return;
        }

        [$meta, $content] = array_pad(explode(',', $imageData, 2), 2, null);

        if (! $content) {
            return;
        }

        $binary = base64_decode($content, true);

        if (! $binary) {
            return;
        }

        $extension = str_contains($meta, 'image/png') ? 'png' : 'jpg';
        $mime = $extension === 'png' ? 'image/png' : 'image/jpeg';
        $path = 'wfh-monitoring/screenshots/' . Auth::id() . '/' . now()->format('YmdHis') . '-' . Str::random(8) . '.' . $extension;

        Storage::disk('public')->put($path, $binary);

        $normalizedCaptureType = in_array($captureType, ['periodic', 'on_demand', 'time_in', 'live_snapshot'], true) ? $captureType : 'periodic';

        WfhMonitoringScreenshot::create([
            'wfh_monitoring_session_id' => $session->id,
            'user_id' => Auth::id(),
            'path' => $path,
            'capture_type' => $normalizedCaptureType,
            'mime_type' => $mime,
            'size_bytes' => strlen($binary),
            'captured_at' => now(),
        ]);

        if ($normalizedCaptureType === 'live_snapshot') {
            $oldSnapshots = WfhMonitoringScreenshot::query()
                ->where('wfh_monitoring_session_id', $session->id)
                ->where('capture_type', 'live_snapshot')
                ->latest('captured_at')
                ->skip(120)
                ->take(50)
                ->get();

            foreach ($oldSnapshots as $oldSnapshot) {
                Storage::disk('public')->delete($oldSnapshot->path);
                $oldSnapshot->delete();
            }
        }

        $this->logMonitoringEvent($session, 'screenshot_captured', 'Screen snapshot captured from active screen share', [
            'path' => $path,
            'capture_type' => $normalizedCaptureType,
        ]);
    }

    public function getLiveSnapshotRequest()
    {
        $session = $this->getOpenMonitoringSession();
        $meta = $session?->meta ?? [];
        $liveSnapshots = $meta['live_snapshots'] ?? null;

        if (! $session || ! $liveSnapshots || ($liveSnapshots['status'] ?? null) !== 'active') {
            return null;
        }

        return [
            'token' => $liveSnapshots['token'] ?? null,
            'intervalSeconds' => max(3, min(30, (int) ($liveSnapshots['interval_seconds'] ?? 5))),
        ];
    }

    public function getLiveScreenRequest()
    {
        $session = $this->getOpenMonitoringSession();
        $meta = $session?->meta ?? [];
        $liveScreen = $meta['live_screen'] ?? null;

        if (! $session || ! $liveScreen || ! in_array($liveScreen['status'] ?? null, ['requested', 'offer_ready'], true)) {
            return null;
        }

        return [
            'sessionId' => $session->id,
            'token' => $liveScreen['token'] ?? null,
            'status' => $liveScreen['status'] ?? null,
            'offer' => $liveScreen['offer'] ?? null,
        ];
    }

    public function publishLiveAnswer($token, $answer)
    {
        $session = $this->getOpenMonitoringSession();

        if (! $session || ! is_array($answer)) {
            return;
        }

        $meta = $session->meta ?? [];
        $liveScreen = $meta['live_screen'] ?? [];

        if (($liveScreen['token'] ?? null) !== $token) {
            return;
        }

        $liveScreen['status'] = 'answer_ready';
        $liveScreen['answer'] = $answer;
        $liveScreen['answered_at'] = now()->toIso8601String();
        $meta['live_screen'] = $liveScreen;

        $session->update(['meta' => $meta]);
        $this->logMonitoringEvent($session, 'live_screen_answered', 'Employee browser connected live screen stream');
    }

    public function getLiveMediaRequest()
    {
        $session = $this->getOpenMonitoringSession();
        $meta = $session?->meta ?? [];
        $liveMedia = $meta['live_media'] ?? null;

        if (! $session || ! $liveMedia || ! in_array($liveMedia['status'] ?? null, ['requested', 'offer_ready'], true)) {
            return null;
        }

        return [
            'sessionId' => $session->id,
            'token' => $liveMedia['token'] ?? null,
            'status' => $liveMedia['status'] ?? null,
            'offer' => $liveMedia['offer'] ?? null,
        ];
    }

    public function publishLiveMediaAnswer($token, $answer)
    {
        $session = $this->getOpenMonitoringSession();

        if (! $session || ! is_array($answer)) {
            return;
        }

        $meta = $session->meta ?? [];
        $liveMedia = $meta['live_media'] ?? [];

        if (($liveMedia['token'] ?? null) !== $token) {
            return;
        }

        $liveMedia['status'] = 'answer_ready';
        $liveMedia['answer'] = $answer;
        $liveMedia['answered_at'] = now()->toIso8601String();
        $meta['live_media'] = $liveMedia;

        $session->update(['meta' => $meta]);
        $this->logMonitoringEvent($session, 'live_media_answered', 'Employee approved camera and microphone live stream');
    }

    public function respondToAfkPrompt($response)
    {
        $session = $this->getOpenMonitoringSession();

        if (! $session) {
            return;
        }

        $allowed = ['Still Working', 'On Break', 'In Meeting', 'Field Work'];
        $response = in_array($response, $allowed, true) ? $response : 'Still Working';
        $workStatus = match ($response) {
            'On Break' => 'On Break',
            'In Meeting' => 'Meeting',
            'Field Work' => 'Field Work',
            default => 'WFH',
        };

        $session->update([
            'status' => $response === 'On Break' ? 'active' : 'active',
            'work_status' => $workStatus,
            'last_activity_at' => now(),
            'afk_responded_at' => now(),
            'afk_response' => $response,
        ]);

        $this->logMonitoringEvent($session, 'afk_response', 'Employee responded to AFK prompt', [
            'response' => $response,
            'work_status' => $workStatus,
        ]);

        $this->refreshMonitoringState();
    }

    public function setMonitoringWorkStatus($status)
    {
        $allowed = ['WFH', 'Field Work', 'On Break', 'Meeting'];

        if (! in_array($status, $allowed, true)) {
            return;
        }

        $this->updateMonitoringWorkStatus($status, 'Employee declared work status: ' . $status);
    }

    public function submitFieldWorkProof()
    {
        $this->validate([
            'fieldWorkLocationLabel' => 'required|string|max:255',
            'fieldWorkPhoto' => 'nullable|image|max:4096',
        ]);

        $session = $this->getOpenMonitoringSession() ?: $this->startMonitoringSession('Monitoring started from field work proof');
        $photoPath = $this->fieldWorkPhoto
            ? $this->fieldWorkPhoto->store('wfh-monitoring/field-work/' . Auth::id(), 'public')
            : $session->field_photo_path;

        $session->update([
            'work_status' => 'Field Work',
            'field_location_label' => $this->fieldWorkLocationLabel,
            'field_photo_path' => $photoPath,
            'last_activity_at' => now(),
        ]);

        $this->logMonitoringEvent($session, 'field_work_proof', 'Employee submitted field work location proof', [
            'location_label' => $this->fieldWorkLocationLabel,
            'photo_path' => $photoPath,
        ]);

        $this->fieldWorkLocationLabel = null;
        $this->fieldWorkPhoto = null;
        $this->refreshMonitoringState();

        $this->dispatch('swal', [
            'title' => 'Field work proof submitted.',
            'icon' => 'success',
        ]);
    }

    protected function syncMonitoringWithPunch($verifyType)
    {
        if (! $this->isWfhAttendanceAvailable()) {
            return;
        }

        if ($verifyType === 'Morning In') {
            $this->startMonitoringSession('Monitoring started from Time In', true);
            return;
        }

        if ($verifyType === 'Break Out') {
            $this->updateMonitoringWorkStatus('On Break', 'Employee started break');
            return;
        }

        if ($verifyType === 'Break In') {
            $this->updateMonitoringWorkStatus('WFH', 'Employee returned from break');
            return;
        }

        if ($verifyType === 'Afternoon Out') {
            $this->endMonitoringSession('Monitoring ended from Time Out');
        }
    }

    protected function startMonitoringSession($label, bool $resetTimer = false)
    {
        $session = $this->getOpenMonitoringSession();

        if ($session) {
            $now = now();
            $meta = $session->meta ?? [];

            if ($resetTimer) {
                unset($meta['live_screen'], $meta['live_media'], $meta['live_snapshots']);
                $meta['timer_reset_at'] = $now->toIso8601String();
                $meta['timer_reset_reason'] = $label;
            }

            $updates = [
                'status' => 'active',
                'work_status' => 'WFH',
                'last_activity_at' => $now,
                'visibility_state' => 'visible',
                'meta' => array_merge($meta, [
                    'last_online_accounted_at' => $now->toIso8601String(),
                ]),
            ];

            if ($resetTimer) {
                $updates = array_merge($updates, [
                    'started_at' => $now,
                    'ended_at' => null,
                    'total_monitored_minutes' => 0,
                    'online_seconds' => 0,
                    'offline_alerted_at' => null,
                    'afk_started_at' => null,
                    'afk_responded_at' => null,
                    'afk_response' => null,
                    'afk_excused' => false,
                    'afk_excuse_notes' => null,
                    'activity_count' => 1,
                    'active_seconds' => 0,
                    'idle_seconds' => 0,
                    'keystroke_count' => 0,
                    'mouse_activity_count' => 0,
                    'click_count' => 0,
                    'touch_count' => 0,
                    'activity_score' => 0,
                    'screenshot_request_pending' => false,
                    'screenshot_requested_at' => null,
                    'screenshot_requested_by' => null,
                ]);
            }

            $session->update($updates);

            $this->logMonitoringEvent($session, 'session_resumed', $label);
            $this->refreshMonitoringState();

            return $session;
        }

        $session = WfhMonitoringSessionRecord::create([
            'user_id' => Auth::id(),
            'status' => 'active',
            'work_status' => 'WFH',
            'started_at' => now(),
            'shift_end_at' => $this->getCurrentShiftEndAt(),
            'last_activity_at' => now(),
            'visibility_state' => 'visible',
            'screen_share_active' => false,
            'consented_at' => now(),
            'consent_version' => 'TES-HRIS-2026-002-R1',
            'activity_count' => 1,
            'afk_threshold_minutes' => $this->afkThresholdMinutes,
            'screenshot_interval_minutes' => $this->screenshotIntervalMinutes,
            'location_interval_minutes' => $this->locationIntervalMinutes,
            'notes' => 'Auto-started from WFH Time In',
            'meta' => [
                'source' => 'wfh_attendance',
                'last_latitude' => $this->latitude,
                'last_longitude' => $this->longitude,
                'last_online_accounted_at' => now()->toIso8601String(),
            ],
        ]);

        $this->logMonitoringEvent($session, 'session_started', $label);
        $this->refreshMonitoringState();

        return $session;
    }

    protected function endMonitoringSession($label)
    {
        $session = $this->getOpenMonitoringSession();

        if (! $session) {
            return;
        }

        $session->update([
            'status' => 'ended',
            'ended_at' => now(),
            'work_status' => 'Logged Out',
            'screen_share_active' => false,
            'screen_share_ended_at' => now(),
            'total_monitored_minutes' => max(0, Carbon::parse($session->started_at)->diffInMinutes(now())),
        ]);

        $this->logMonitoringEvent($session, 'session_ended', $label);
        $this->refreshMonitoringState();
    }

    protected function updateMonitoringWorkStatus($status, $label)
    {
        $session = $this->getOpenMonitoringSession() ?: $this->startMonitoringSession('Monitoring started from active WFH punch');

        if (! $session) {
            return;
        }

        $session->update([
            'status' => 'active',
            'work_status' => $status,
            'last_activity_at' => now(),
        ]);

        $this->logMonitoringEvent($session, 'status_changed', $label, [
            'work_status' => $status,
        ]);

        $this->refreshMonitoringState();
    }

    protected function refreshMonitoringState()
    {
        $session = $this->getOpenMonitoringSession();

        if (! $session) {
            $this->monitoringState = 'Offline';
            $this->monitoringWorkStatus = 'Logged Out';
            $this->monitoringLastActivity = null;
            $this->monitoringSessionStartedAt = null;
            $this->monitoringOnlineSeconds = $this->getDailyOnlineSeconds();
            $this->monitoringScreenShareActive = false;

            return;
        }

        $session = $session->fresh();
        $this->evaluateMonitoringAfk($session);
        $session = $session->fresh();

        $this->monitoringState = $this->getMonitoringState($session);
        $this->monitoringWorkStatus = $session->work_status;
        $this->monitoringLastActivity = optional($session->last_activity_at)->diffForHumans();
        $this->monitoringSessionStartedAt = optional($session->started_at)->toIso8601String();
        $this->monitoringOnlineSeconds = $this->getDailyOnlineSeconds($session, (int) ($session->online_seconds ?? 0));
        $this->monitoringScreenShareActive = (bool) $session->screen_share_active;
    }

    protected function getDailyOnlineSeconds($currentSession = null, $currentOnlineSeconds = null)
    {
        $query = WfhMonitoringSessionRecord::query()
            ->where('user_id', Auth::id())
            ->whereDate('started_at', Carbon::today());

        if ($currentSession) {
            $query->where('id', '!=', $currentSession->id);
        }

        return (int) $query->sum('online_seconds') + (int) ($currentOnlineSeconds ?? 0);
    }

    protected function evaluateMonitoringAfk($session)
    {
        if (! $session || $session->status !== 'active' || $session->work_status === 'On Break') {
            return;
        }

        $lastActivity = $session->last_activity_at ?? $session->started_at;

        if (Carbon::parse($lastActivity)->diffInMinutes(now()) >= (int) $session->afk_threshold_minutes) {
            $session->update([
                'status' => 'afk',
                'afk_started_at' => $session->afk_started_at ?: now(),
            ]);
            $this->logMonitoringEvent($session, 'afk_detected', 'AFK threshold reached', [
                'threshold_minutes' => $session->afk_threshold_minutes,
            ]);
        }
    }

    protected function getMonitoringState($session)
    {
        if (! $session || $session->status === 'ended') {
            return 'Offline';
        }

        if ($session->work_status === 'On Break') {
            return 'On Break';
        }

        if ($session->status === 'afk') {
            return 'AFK';
        }

        if ($session->last_activity_at && Carbon::parse($session->last_activity_at)->lt(now()->subMinutes(2))) {
            if (! $session->offline_alerted_at) {
                $session->update(['offline_alerted_at' => now()]);
                $this->logMonitoringEvent($session, 'offline_alert', 'Employee monitoring heartbeat stopped during WFH hours');
            }

            return 'Offline';
        }

        return 'Active';
    }

    protected function getOpenMonitoringSession()
    {
        return WfhMonitoringSessionRecord::query()
            ->where('user_id', Auth::id())
            ->where('status', '!=', 'ended')
            ->latest('started_at')
            ->first();
    }

    protected function hasOpenWfhTimeInToday()
    {
        $user = Auth::user();
        $todayTransactions = TransactionWFH::where('emp_code', $user->emp_code)
            ->where('punch_state_display', 'WFH')
            ->whereDate('punch_time', Carbon::today())
            ->get();

        return $todayTransactions->contains('verify_type_display', 'Morning In')
            && ! $todayTransactions->contains('verify_type_display', 'Afternoon Out');
    }

    protected function isWfhAttendanceAvailable()
    {
        return $this->scheduleType === 'WFH' || $this->wfhStatus === 'approved';
    }

    protected function getCurrentShiftEndAt()
    {
        $schedule = DTRSchedule::where('emp_code', Auth::user()->emp_code)
            ->where('start_date', '<=', Carbon::today())
            ->where('end_date', '>=', Carbon::today())
            ->orderBy('start_date', 'desc')
            ->first();

        if (! $schedule || ! $schedule->default_end_time) {
            return null;
        }

        return $schedule->getEffectiveEndTime(Carbon::today());
    }

    protected function classifyMonitoringUrl($url)
    {
        if (! $url) {
            return 'unclassified';
        }

        $rules = WfhMonitoringUrlRule::where('is_active', true)->get();

        foreach ($rules as $rule) {
            if ($rule->pattern && str_contains(strtolower($url), strtolower($rule->pattern))) {
                return $rule->classification;
            }
        }

        return 'unclassified';
    }

    protected function shouldRecordLocationPing($session): bool
    {
        $latestPing = WfhMonitoringLocationPing::query()
            ->where('wfh_monitoring_session_id', $session->id)
            ->latest('occurred_at')
            ->first();

        if (! $latestPing) {
            return true;
        }

        $intervalMinutes = max(1, (int) ($session->location_interval_minutes ?? $this->locationIntervalMinutes));

        return Carbon::parse($latestPing->occurred_at)->lte(now()->subMinutes($intervalMinutes));
    }

    protected function shouldLogGeofenceAlert($session, $previousGeofenceStatus): bool
    {
        if ($previousGeofenceStatus !== 'outside') {
            return true;
        }

        $recentAlertExists = WfhMonitoringEvent::query()
            ->where('wfh_monitoring_session_id', $session->id)
            ->where('event_type', 'geofence_alert')
            ->where('occurred_at', '>=', now()->subMinutes(15))
            ->exists();

        return ! $recentAlertExists;
    }

    protected function calculateActivityScore($activeSeconds, $idleSeconds, $keystrokes, $mouseMoves, $clicks, $touches)
    {
        $totalSeconds = max(1, $activeSeconds + $idleSeconds);
        $activeRatio = min(1, $activeSeconds / $totalSeconds);
        $interactionScore = min(1, (($keystrokes * 2) + $mouseMoves + ($clicks * 3) + ($touches * 2)) / 120);

        return (int) round((($activeRatio * 0.65) + ($interactionScore * 0.35)) * 100);
    }

    protected function logMonitoringEvent($session, $type, $label, array $payload = [])
    {
        WfhMonitoringEvent::create([
            'wfh_monitoring_session_id' => $session->id,
            'user_id' => Auth::id(),
            'event_type' => $type,
            'label' => $label,
            'details' => $payload ? json_encode($payload) : null,
            'payload' => $payload ?: null,
            'occurred_at' => now(),
        ]);
    }

    protected function resolveGeofenceStatus($latitude, $longitude)
    {
        $radius = 20;

        if (! $latitude || ! $longitude) {
            return [
                'status' => 'unknown',
                'distance' => null,
                'radius' => $radius,
            ];
        }

        $wfhLocation = WfhLocation::where('user_id', Auth::id())->first();

        if (! $wfhLocation || ! $wfhLocation->latitude || ! $wfhLocation->longitude) {
            return [
                'status' => 'location_not_registered',
                'distance' => null,
                'radius' => $radius,
            ];
        }

        $distance = $this->calculateDistance(
            (float) $wfhLocation->latitude,
            (float) $wfhLocation->longitude,
            (float) $latitude,
            (float) $longitude
        );

        return [
            'status' => $distance <= $radius ? 'inside' : 'outside',
            'distance' => round($distance, 2),
            'radius' => $radius,
        ];
    }

    public function toggleEditLocation($type)
    {
        if ($type == 'request') {
            $this->editLocMessage = 'Change';
        } else {
            $this->editLocMessage = 'Register';
        }
        $this->editLocation = true;
        $this->dispatch('init-map2');
    }

    public function saveLocation()
    {
        try {
            $this->validate([
                'address' => 'required',
                'newLat' => 'required',
                'newLng' => 'required',
            ]);

            $user = Auth::user();
            $wfhLoc = WfhLocation::where('user_id', $user->id)->first();

            if ($this->editLocMessage == 'Change') {
                WfhLocationRequests::create([
                    'user_id' => $user->id,
                    'address' => $this->address,
                    'curr_lat' => $this->newLat,
                    'curr_lng' => $this->newLng,
                    'status' => 0,
                ]);
                $this->dispatch('swal', [
                    'title' => 'New WFH location requested successfully!',
                    'icon' => 'success',
                ]);
                $this->locReqGranted = false;

                // Create a notification entry
                Notification::create([
                    'user_id' => $user->id,
                    'type' => 'locrequest',
                    'notif' => 'locrequest',
                    'read' => 0,
                ]);
            } else {
                $wfhReq = WfhLocationRequests::create([
                    'user_id' => $user->id,
                    'address' => $this->address,
                    'curr_lat' => $this->newLat,
                    'curr_lng' => $this->newLng,
                    'status' => 1,
                    'approver' => 'First-time Registration',
                    'date_approved' => now(),
                ]);

                WfhLocation::create([
                    'user_id' => $user->id,
                    'address' => $this->address,
                    'latitude' => $this->newLat,
                    'longitude' => $this->newLng,
                    'wfh_loc_req_id' => $wfhReq->id,
                ]);
                $this->dispatch('swal', [
                    'title' => 'WFH location added successfully!',
                    'icon' => 'success',
                ]);
                $this->hasWFHLocation = true;
            }
            $this->resetVariables();
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function showLocReqHistory()
    {
        try {

        } catch (Exception $e) {
            throw $e;
        }
    }

    public function viewWFHLocHistory($id)
    {
        try {
            $wfhLocRequest = WfhLocationRequests::where('wfh_location_requests.id', $id)
                ->join('users', 'users.id', 'wfh_location_requests.user_id')
                ->select([
                    'wfh_location_requests.*',
                    'users.name',
                ])
                ->first();

            if ($wfhLocRequest->status == 2) {
                $this->approveOnly = true;
            } else {
                $this->approveOnly = false;

            }
            $this->registeredLatitude = floatval($wfhLocRequest->curr_lat);
            $this->registeredLongitude = floatval($wfhLocRequest->curr_lng);
            $this->address = $wfhLocRequest->address;
            $this->approvedBy = $wfhLocRequest->approver;
            $this->approvedDate = $wfhLocRequest->date_approved;
            $this->disapprovedBy = $wfhLocRequest->disapprover;
            $this->disapprovedDate = $wfhLocRequest->date_disapproved;

            $this->dispatch('showWFHLocHistory');
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function mount()
    {
        $user = Auth::user();
        $wfhLocation = WfhLocation::where('user_id', $user->id)->first();
        if ($wfhLocation) {
            $this->hasWFHLocation = true;
            $this->registeredLatitude = floatval($wfhLocation->latitude);
            $this->registeredLongitude = floatval($wfhLocation->longitude);
            $this->hasRequested = $wfhLocation->status ? false : true;
        }

        $wfhLocationRequest = WfhLocationRequests::where('user_id', $user->id)
            ->where('status', 0)
            ->orderBy('created_at', 'desc')
            ->first();
        if ($wfhLocationRequest) {
            $this->locReqGranted = false;
        }

        $birthday = new DateTime($user->userData->date_of_birth);
        $today = new DateTime();
        $this->isMyBirthday = ($birthday->format('m-d') === $today->format('m-d'));
        $this->refreshMonitoringState();
    }

    public function render()
    {
        $this->checkWFHDay();
        $this->resetButtonStatesIfNeeded();
        $this->refreshMonitoringState();

        if ($this->scheduleType === 'WFH' || $this->wfhStatus === 'approved') {
            $transactions = TransactionWFH::where('emp_code', Auth::user()->emp_code)
                ->whereDate('punch_time', Carbon::today())
                ->orderBy('punch_time', 'asc')
                ->get();
        } else {
            // Fetch onsite punch times from EmployeesDTR table
            $transactions = EmployeesDtr::where('emp_code', Auth::user()->emp_code)
                ->whereDate('date', Carbon::today())
                ->first();
        }

        $groupedTransactions = ($this->scheduleType === 'WFH' || $this->wfhStatus === 'approved')
            ? $transactions->groupBy('verify_type_display')
            : $transactions;

        [$breakOutPunchState, $breakInPunchState] = $this->getBreakPunchStates();

        $userId = Auth::user()->id;
        $history = WfhLocationRequests::where('user_id', $userId)
            ->when($this->search, function ($query) {
                return $query->search(trim($this->search));
            })
            ->orderBy('created_at', 'ASC')
            ->paginate($this->pageSize);

        return view('livewire.user.wfh-attendance', [
            'groupedTransactions' => $groupedTransactions,
            'scheduleType' => $this->scheduleType,
            'history' => $history,
            'wfhStatus' => $this->wfhStatus,
            'breakOutPunchState' => $breakOutPunchState,
            'breakInPunchState' => $breakInPunchState,
        ]);
    }
}
