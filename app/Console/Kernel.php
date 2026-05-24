<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Jobs\AutoSaveDtrRecords;
use App\Jobs\AutoSaveDtrRecordsMonthly;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('inspire')->hourly();
        $schedule->command('fetch:biotime-transactions')->everyMinute();
        $schedule->job(new AutoSaveDtrRecords())->everyMinute();
        $schedule->job(new AutoSaveDtrRecordsMonthly())->everyMinute()->withoutOverlapping();
        $schedule->command('calculate:monthly-leave-credits')->lastDayOfMonth('22:00');
        $schedule->command('leave-credits:transfer')->lastDayOfMonth('22:30');
        $schedule->command('credits:calculate-monthly')->lastDayOfMonth('23:00');
        $schedule->command('leave-credits:reset')->yearlyOn(1, 1, '00:00');
        $schedule->command('credits:calculate')->everyMinute();
        $schedule->command('fetch:biotime-transactions-monthly')->dailyAt('01:00');
        $schedule->command('queue:work --stop-when-empty --max-time=50')->everyMinute()->withoutOverlapping();
        $schedule->command('app:generate-attendance-notices')->lastDayOfMonth('23:00');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }

}
