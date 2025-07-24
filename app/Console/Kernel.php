<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\DailyTask;
use App\Console\Commands\CheckExpiredSubscriptions;
use App\Console\Commands\RestoreSubscriptions;
use App\Console\Commands\UpdateUnknownSubscriptions;
use App\Console\Commands\CheckUserSubscriptions;
use App\Console\Commands\CheckUserPayments;
use App\Console\Commands\UpdateSpecificSubscriptions;
use App\Console\Commands\FixUnknownSubscriptionLevels;
use App\Console\Commands\ListSubscriptionProducts;
use App\Console\Commands\CheckSubscriptionHistory;
use App\Console\Commands\ConvertNegativeSubscriptions;
use App\Console\Commands\ConvertTransitionalSubscriptions;
use App\Console\Commands\CheckLostSubscriptions;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\DailyTask::class,
        // Commands\CheckExpiredSubscriptions::class,
        Commands\RestoreSubscriptions::class,
        Commands\UpdateUnknownSubscriptions::class,
        Commands\CheckUserSubscriptions::class,
        Commands\CheckUserPayments::class,
        Commands\UpdateSpecificSubscriptions::class,
        Commands\FixUnknownSubscriptionLevels::class,
        Commands\ListSubscriptionProducts::class,
        Commands\CheckSubscriptionHistory::class,
        Commands\ConvertNegativeSubscriptions::class,
        Commands\ConvertTransitionalSubscriptions::class,
        \App\Console\Commands\CheckLostSubscriptions::class,
        Commands\MigrateData::class,
        Commands\MigrateAvatars::class,
        Commands\TestMigration::class,
    ];

    protected function schedule(Schedule $schedule)
    {
   //     $schedule->command('task:daily')->everyMinute(); // Запланируйте выполнение вашей команды
   // $schedule->command('subscriptions:check-expired')->daily();
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
