<?php

namespace Tests\Unit;

use App\Jobs\AutoSaveDtrRecords;
use App\Models\DTRSchedule;
use App\Models\TransactionWFH;
use Illuminate\Support\Collection;
use ReflectionMethod;
use Tests\TestCase;

class WfhDtrExtractionTest extends TestCase
{
    public function test_wfh_dtr_uses_verify_type_labels_before_numeric_punch_state(): void
    {
        $result = $this->extract([
            ['2026-08-14 07:32:00', 9, 'Morning In'],
            ['2026-08-14 12:05:00', 5, 'Break Out'],
            ['2026-08-14 12:35:00', 4, 'Break In'],
            ['2026-08-14 17:02:00', 9, 'Afternoon Out'],
        ]);

        $this->assertSame('07:32:00', $result['time_in']?->format('H:i:s'));
        $this->assertSame('12:05:00', $result['break_out']?->format('H:i:s'));
        $this->assertSame('12:35:00', $result['break_in']?->format('H:i:s'));
        $this->assertSame('17:02:00', $result['time_out']?->format('H:i:s'));
    }

    public function test_wfh_dtr_does_not_treat_break_out_as_time_in_when_morning_label_exists(): void
    {
        $result = $this->extract([
            ['2026-08-14 07:32:00', 0, 'Morning In'],
            ['2026-08-14 12:05:00', 0, 'Break Out'],
        ]);

        $this->assertSame('07:32:00', $result['time_in']?->format('H:i:s'));
        $this->assertSame('12:05:00', $result['break_out']?->format('H:i:s'));
        $this->assertNull($result['time_out']);
    }

    private function extract(array $punches): array
    {
        $job = new AutoSaveDtrRecords();
        $method = new ReflectionMethod($job, 'extractTimeData');
        $method->setAccessible(true);

        return $method->invoke(
            $job,
            $this->transactions($punches),
            '264',
            '2026-08-14',
            $this->schedule(),
            true
        );
    }

    private function transactions(array $punches): Collection
    {
        return collect($punches)->map(fn (array $punch) => new TransactionWFH([
            'emp_code' => '264',
            'punch_time' => $punch[0],
            'punch_state' => $punch[1],
            'punch_state_display' => 'WFH',
            'verify_type_display' => $punch[2],
        ]));
    }

    private function schedule(): DTRSchedule
    {
        return new DTRSchedule([
            'emp_code' => '264',
            'wfh_days' => 'Friday',
            'default_start_time' => '09:00:00',
            'default_end_time' => '18:00:00',
            'start_date' => '2026-08-01',
            'end_date' => '2026-08-31',
            'is_flexi' => false,
            'has_break' => true,
            'is_overnight' => false,
            'is_24hours' => false,
        ]);
    }
}
