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
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        if (env("APP_ENV") == 'production') {
            $schedule->command('email:ttnotconfirm48')->everyFourHours();
            $schedule->command('email:ttnotconfirm5d')->everySixHours();
            $schedule->command('email:requestnotsubmited510d')->everySixHours();
            //--------
            if (env("BEWOTEC_DAVINCI_SCHEDULED_TASKS")) {
                $schedule->command('davinci:import')->dailyAt('05:00');
                $schedule->command('davinci:cleanup')->dailyAt('09:00');
            }
        } else {
            $schedule->command('email:ttnotconfirm48')->everyMinute();
            $schedule->command('email:ttnotconfirm5d')->everyMinute();
            $schedule->command('email:requestnotsubmited10d')->everyMinute();
            //--------
            if (env("BEWOTEC_DAVINCI_SCHEDULED_TASKS")) {
                $schedule->command('davinci:import')->dailyAt('12:45');
                $schedule->command('davinci:cleanup')->dailyAt('05:00');
            }
        }
        $schedule->command('backup:clean')->dailyAt('20:00');
        $schedule->command('backup:run --only-db')->dailyAt('22:00');
        // $schedule->command('backup:run --only-db')->everyMinute();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
