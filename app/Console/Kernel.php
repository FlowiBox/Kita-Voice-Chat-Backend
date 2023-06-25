<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\UpdateRoomUserNowCron::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
//       $schedule->command('monthly_update')->everyMinute();
         //$schedule->command('monthly_update')->monthly ();
         $schedule->command('update-room-user-now:cron')->everyMinute();
         $schedule->command('remove-background:cron')->daily();
         $schedule->command('users:reset-monthly-diamond')->monthly();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }

    /**
     * Get the timezone that should be used by default for scheduled events.
     *
     * @return \DateTimeZone|string|null
    */
    protected function scheduleTimezone()
    {
        return 'Africa/Cairo';
    }
}
