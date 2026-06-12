<?php

namespace App\Support;

use App\Models\WfhMonitoringSessionRecord;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class WfhMonitoringReport
{
    public static function rows(Carbon $startDate, Carbon $endDate, ?string $search = null): Collection
    {
        $sessions = WfhMonitoringSessionRecord::query()
            ->with(['user.userData', 'latestLocationPing'])
            ->whereBetween('started_at', [$startDate->copy()->startOfDay(), $endDate->copy()->endOfDay()])
            ->when($search, function ($query, $search) {
                $query->whereHas('user', function ($userQuery) use ($search) {
                    $userQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('emp_code', 'like', "%{$search}%")
                        ->orWhereHas('userData', function ($userDataQuery) use ($search) {
                            $userDataQuery->where('surname', 'like', "%{$search}%")
                                ->orWhere('first_name', 'like', "%{$search}%");
                        });
                });
            })
            ->orderBy('started_at')
            ->get();

        return $sessions
            ->groupBy(fn ($session) => $session->started_at->toDateString().'|'.$session->user_id)
            ->sortKeys()
            ->map(function (Collection $dailySessions) {
                $first = $dailySessions->sortBy('started_at')->first();
                $last = $dailySessions->sortByDesc(fn ($session) => $session->ended_at ?: $session->last_activity_at ?: $session->started_at)->first();
                $user = $first->user;
                $onlineSeconds = $dailySessions->sum(fn ($session) => (int) $session->online_seconds);
                $activeSeconds = 0;
                $idleSeconds = 0;

                foreach ($dailySessions as $session) {
                    [$active, $idle] = WfhMonitoringClock::normalizeActivityTotals(
                        (int) $session->active_seconds,
                        (int) $session->idle_seconds,
                        (int) $session->online_seconds
                    );
                    $activeSeconds += $active;
                    $idleSeconds += $idle;
                }

                $latestLocationSession = $dailySessions
                    ->filter(fn ($session) => is_numeric($session->last_latitude) && is_numeric($session->last_longitude))
                    ->sortByDesc(fn ($session) => $session->latestLocationPing?->occurred_at ?: $session->last_activity_at)
                    ->first();
                $date = $first->started_at;
                $employeeName = $user?->userData
                    ? trim($user->userData->surname.', '.$user->userData->first_name.' '.($user->userData->middle_name ?? ''))
                    : ($user?->name ?: 'Unknown employee');
                $lastMoment = $last->ended_at ?: $last->last_activity_at;
                $activityRate = $onlineSeconds > 0 ? round(($activeSeconds / $onlineSeconds) * 100, 1) : 0;

                return [
                    $date->format('M d, Y'),
                    $date->format('l'),
                    $user?->emp_code ?: '-',
                    $employeeName,
                    $dailySessions->count(),
                    $first->started_at?->format('h:i:s A') ?: '-',
                    $lastMoment?->format('h:i:s A') ?: '-',
                    self::duration($onlineSeconds),
                    self::duration($activeSeconds),
                    self::duration($idleSeconds),
                    $activityRate,
                    $dailySessions->pluck('work_status')->filter()->unique()->implode(', ') ?: 'WFH',
                    $last->last_activity_at?->format('M d, Y h:i:s A') ?: '-',
                    is_numeric($latestLocationSession?->last_latitude) ? number_format((float) $latestLocationSession->last_latitude, 6, '.', '') : '-',
                    is_numeric($latestLocationSession?->last_longitude) ? number_format((float) $latestLocationSession->last_longitude, 6, '.', '') : '-',
                    is_numeric($latestLocationSession?->last_location_accuracy) ? round((float) $latestLocationSession->last_location_accuracy).' m' : '-',
                    WfhActivity::browserName($last->user_agent),
                    $last->device_platform ?: '-',
                    $dailySessions->sum(fn ($session) => (int) $session->keystroke_count),
                    $dailySessions->sum(fn ($session) => (int) $session->mouse_activity_count),
                    $dailySessions->sum(fn ($session) => (int) $session->click_count),
                    $dailySessions->sum(fn ($session) => (int) $session->touch_count),
                ];
            })
            ->sortBy(fn ($row) => $row[0].'|'.$row[3])
            ->values();
    }

    public static function duration(int $seconds): string
    {
        $seconds = max(0, $seconds);

        return sprintf('%02d:%02d:%02d', intdiv($seconds, 3600), intdiv($seconds % 3600, 60), $seconds % 60);
    }
}
