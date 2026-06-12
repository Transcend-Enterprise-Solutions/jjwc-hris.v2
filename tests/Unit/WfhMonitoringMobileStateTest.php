<?php

namespace Tests\Unit;

use App\Livewire\Admin\WfhMonitoring;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class WfhMonitoringMobileStateTest extends TestCase
{
    public function test_unsupported_mobile_capture_is_reported_as_mobile_monitoring(): void
    {
        $component = new WfhMonitoring;
        $session = $this->session([
            'screen_share_supported' => false,
            'monitoring_mode' => 'mobile',
        ]);

        $this->assertSame('Mobile', $component->monitoringStateFor($session));
    }

    public function test_supported_mobile_capture_is_reported_as_screen_off_when_stopped(): void
    {
        $component = new WfhMonitoring;
        $session = $this->session([
            'screen_share_supported' => true,
            'monitoring_mode' => 'screen',
        ]);

        $this->assertSame('Screen Off', $component->monitoringStateFor($session));
    }

    private function session(array $meta): object
    {
        return (object) [
            'status' => 'active',
            'work_status' => 'Working',
            'last_activity_at' => Carbon::now(),
            'screen_share_active' => false,
            'meta' => $meta,
            'user_agent' => 'Mozilla/5.0 (Linux; Android 15; Mobile)',
            'device_platform' => 'Linux armv8l',
        ];
    }
}
