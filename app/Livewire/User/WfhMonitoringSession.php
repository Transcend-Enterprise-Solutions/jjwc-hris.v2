<?php

namespace App\Livewire\User;

use App\Models\WfhMonitoringEvent;
use App\Models\WfhMonitoringSessionRecord;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('WFH Monitoring Session')]
class WfhMonitoringSession extends Component
{
    public $workStatus = 'WFH';

    public $sessionNotes = '';

    public $browserTabTitle = '';

    public $browserUrl = '';

    public $afkThresholdMinutes = 10;

    public $activeSession = null;

    public $recentSessions = [];

    public $sessionEvents = [];

    public function mount(): void
    {
        $this->refreshMonitoringState();
    }

    public function refreshMonitoringState(): void
    {
        $this->activeSession = WfhMonitoringSessionRecord::query()
            ->where('user_id', Auth::id())
            ->where('status', '!=', 'ended')
            ->latest('started_at')
            ->first();

        if ($this->activeSession) {
            $this->workStatus = $this->activeSession->work_status;
            $this->afkThresholdMinutes = (int) $this->activeSession->afk_threshold_minutes;
        }

        $this->recentSessions = WfhMonitoringSessionRecord::query()
            ->where('user_id', Auth::id())
            ->latest('started_at')
            ->limit(5)
            ->get();

        $this->sessionEvents = $this->activeSession
            ? WfhMonitoringEvent::query()
                ->where('wfh_monitoring_session_id', $this->activeSession->id)
                ->latest('occurred_at')
                ->limit(12)
                ->get()
            : collect();

        $this->evaluateAfkState();
    }

    public function startSession(): void
    {
        $this->validate([
            'workStatus' => ['required', Rule::in(['WFH', 'Field Work', 'On Break', 'Meeting'])],
            'sessionNotes' => 'nullable|string|max:500',
        ]);

        if ($this->activeSession) {
            $this->dispatch('swal', [
                'title' => 'Session already active',
                'text' => 'End the current monitoring session before starting a new one.',
                'icon' => 'warning',
            ]);

            return;
        }

        $session = WfhMonitoringSessionRecord::create([
            'user_id' => Auth::id(),
            'status' => 'active',
            'work_status' => $this->workStatus,
            'browser_tab_title' => $this->browserTabTitle ?: null,
            'browser_url' => $this->browserUrl ?: null,
            'started_at' => now(),
            'last_activity_at' => now(),
            'activity_count' => 1,
            'afk_threshold_minutes' => (int) $this->afkThresholdMinutes,
            'notes' => $this->sessionNotes ?: null,
            'meta' => [
                'source' => 'browser',
                'started_from' => 'jjwc-hris-v2',
            ],
        ]);

        $this->logEvent($session, 'session_started', 'Monitoring session started');
        $this->reset(['sessionNotes']);
        $this->refreshMonitoringState();

        $this->dispatch('swal', [
            'title' => 'Monitoring started',
            'text' => 'WFH monitoring session is now active.',
            'icon' => 'success',
        ]);
    }

    public function endSession(): void
    {
        if (! $this->activeSession) {
            $this->dispatch('swal', [
                'title' => 'No active session',
                'text' => 'There is no monitoring session to end.',
                'icon' => 'info',
            ]);

            return;
        }

        $session = $this->activeSession;
        $session->update([
            'status' => 'ended',
            'ended_at' => now(),
        ]);

        $this->logEvent($session, 'session_ended', 'Monitoring session ended');
        $this->refreshMonitoringState();

        $this->dispatch('swal', [
            'title' => 'Monitoring ended',
            'text' => 'Your monitoring session has been closed.',
            'icon' => 'success',
        ]);
    }

    public function updateWorkStatus(string $status): void
    {
        if (! in_array($status, ['WFH', 'Field Work', 'On Break', 'Meeting'], true)) {
            $this->dispatch('swal', [
                'title' => 'Invalid work status',
                'text' => 'Please choose one of the supported work status values.',
                'icon' => 'error',
            ]);

            return;
        }

        if (! $this->activeSession) {
            $this->dispatch('swal', [
                'title' => 'Start a session first',
                'text' => 'Work status is tracked only while a monitoring session is active.',
                'icon' => 'warning',
            ]);

            return;
        }

        $this->workStatus = $status;
        $this->activeSession->update([
            'work_status' => $status,
            'last_activity_at' => now(),
        ]);

        $this->logEvent($this->activeSession, 'status_changed', "Work status changed to {$status}", [
            'work_status' => $status,
        ]);

        $this->refreshMonitoringState();
    }

    public function recordHeartbeat(): void
    {
        if (! $this->activeSession) {
            return;
        }

        $session = $this->activeSession->fresh();
        $wasAfk = $session->status === 'afk';

        $session->update([
            'last_activity_at' => now(),
            'activity_count' => $session->activity_count + 1,
            'browser_tab_title' => $this->browserTabTitle ?: $session->browser_tab_title,
            'browser_url' => $this->browserUrl ?: $session->browser_url,
            'status' => $wasAfk ? 'active' : $session->status,
        ]);

        if ($wasAfk) {
            $this->logEvent($session, 'session_resumed', 'Activity detected after AFK');
        }

        $this->refreshMonitoringState();
    }

    public function syncBrowserContext(string $title = '', string $url = ''): void
    {
        $this->browserTabTitle = $title;
        $this->browserUrl = $url;

        if (! $this->activeSession) {
            return;
        }

        $this->activeSession->update([
            'browser_tab_title' => $title ?: $this->activeSession->browser_tab_title,
            'browser_url' => $url ?: $this->activeSession->browser_url,
        ]);
    }

    protected function evaluateAfkState(): void
    {
        if (! $this->activeSession) {
            return;
        }

        $session = $this->activeSession->fresh();
        $lastActivity = $session->last_activity_at ?? $session->started_at;
        $inactiveMinutes = Carbon::parse($lastActivity)->diffInMinutes(now());

        if ($inactiveMinutes >= (int) $session->afk_threshold_minutes && $session->status === 'active') {
            $session->update(['status' => 'afk']);
            $this->logEvent($session, 'afk_detected', 'AFK threshold reached', [
                'inactive_minutes' => $inactiveMinutes,
                'threshold_minutes' => $session->afk_threshold_minutes,
            ]);
        }
    }

    protected function logEvent(WfhMonitoringSessionRecord $session, string $type, string $label, array $payload = []): void
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

    public function getActiveSessionDurationProperty(): string
    {
        if (! $this->activeSession) {
            return '00:00:00';
        }

        $session = $this->activeSession->fresh();
        $end = $session->ended_at ?? now();
        $seconds = $session->started_at->diffInSeconds($end);

        return gmdate('H:i:s', $seconds);
    }

    public function getSessionStateProperty(): string
    {
        if (! $this->activeSession) {
            return 'No session';
        }

        $status = $this->activeSession->fresh()->status;

        return $status === 'afk' ? 'AFK' : ucfirst($status);
    }

    public function render()
    {
        return view('livewire.user.wfh-monitoring-session');
    }
}
