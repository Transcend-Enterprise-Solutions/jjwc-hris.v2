<?php

namespace App\Support;

use Carbon\Carbon;

class WfhMonitoringClock
{
    public static function sessionStartedAt($session): ?Carbon
    {
        if (! $session) {
            return null;
        }

        $meta = is_array($session->meta ?? null) ? $session->meta : [];
        $startedAt = $meta['timer_reset_at'] ?? $meta['session_started_at'] ?? null;

        if ($startedAt) {
            return Carbon::parse($startedAt);
        }

        return $session->started_at ? Carbon::parse($session->started_at) : null;
    }

    public static function normalizeActivityTotals(int $activeSeconds, int $idleSeconds, int $onlineSeconds): array
    {
        $activeSeconds = max(0, $activeSeconds);
        $idleSeconds = max(0, $idleSeconds);
        $onlineSeconds = max(0, $onlineSeconds);
        $totalActivitySeconds = $activeSeconds + $idleSeconds;

        if ($onlineSeconds === 0 || $totalActivitySeconds === 0) {
            return [0, 0];
        }

        if ($totalActivitySeconds <= $onlineSeconds) {
            return [$activeSeconds, $idleSeconds];
        }

        $normalizedActive = (int) round($onlineSeconds * ($activeSeconds / $totalActivitySeconds));

        return [
            min($onlineSeconds, $normalizedActive),
            max(0, $onlineSeconds - $normalizedActive),
        ];
    }

    public static function clampActivityWindow(int $activeSeconds, int $idleSeconds, int $elapsedSeconds): array
    {
        return self::normalizeActivityTotals($activeSeconds, $idleSeconds, $elapsedSeconds);
    }
}
