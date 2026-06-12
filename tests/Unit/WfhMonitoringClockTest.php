<?php

namespace Tests\Unit;

use App\Support\WfhMonitoringClock;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class WfhMonitoringClockTest extends TestCase
{
    public function test_timer_reset_marker_is_used_as_the_current_session_start(): void
    {
        $session = (object) [
            'started_at' => Carbon::parse('2026-06-12 00:48:25', 'Asia/Manila'),
            'meta' => ['timer_reset_at' => '2026-06-12T08:48:25+08:00'],
        ];

        $this->assertSame(
            '2026-06-12T08:48:25+08:00',
            WfhMonitoringClock::sessionStartedAt($session)?->toIso8601String()
        );
    }

    public function test_activity_totals_are_scaled_to_online_time(): void
    {
        [$active, $idle] = WfhMonitoringClock::normalizeActivityTotals(1247, 23354, 477);

        $this->assertSame(477, $active + $idle);
        $this->assertLessThan($idle, $active);
    }

    public function test_activity_window_cannot_exceed_the_heartbeat_elapsed_time(): void
    {
        [$active, $idle] = WfhMonitoringClock::clampActivityWindow(20, 30, 5);

        $this->assertSame(5, $active + $idle);
    }
}
