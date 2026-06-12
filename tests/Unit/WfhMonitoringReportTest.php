<?php

namespace Tests\Unit;

use App\Support\WfhMonitoringReport;
use PHPUnit\Framework\TestCase;

class WfhMonitoringReportTest extends TestCase
{
    public function test_it_formats_durations_for_excel(): void
    {
        $this->assertSame('00:00:00', WfhMonitoringReport::duration(0));
        $this->assertSame('01:01:01', WfhMonitoringReport::duration(3661));
        $this->assertSame('27:00:00', WfhMonitoringReport::duration(97200));
    }
}
