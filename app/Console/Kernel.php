<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        // Check for expiring medicines daily at 9 AM
        $schedule->command('stock:check-expiry')
                 ->dailyAt('09:00');

        // Run every minute to check if it's time to send alerts
        $schedule->command('alerts:send-daily')->everyMinute();
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
