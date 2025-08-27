<?php

namespace App\Console;

use App\Jobs\UserApp\UserAppInactiveJob;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param Application $app
     * @param Dispatcher $events
     * @param UserAppInactiveJob $appInactiveJob
     */

    public function __construct(
        Application               $app,
        Dispatcher                $events,
        public UserAppInactiveJob $appInactiveJob
    ) {
        parent::__construct($app, $events);
    }

    protected function schedule(Schedule $schedule): void
    {
        $schedule->job($this->appInactiveJob)
            ->cron('0 */6 * * *');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
