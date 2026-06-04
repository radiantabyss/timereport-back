<?php
namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use RA\Auth\Domains\Team\Commands\ExpireInvitesCommand;
use RA\Auth\Domains\User\Commands\ExpireCodesCommand;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        ExpireInvitesCommand::class,
        ExpireCodesCommand::class,
    ];

    protected function commands()
    {
        $this->load(__DIR__.'/../Domains/Common/Commands');
        $this->load(__DIR__.'/../Domains/ConversionRate/Commands');

        require base_path('routes/console.php');
    }

    protected function schedule(Schedule $schedule) {
        $schedule->command('ra-auth:expire-invites')->hourly();
        $schedule->command('ra-auth:expire-codes')->hourly();

        $schedule->command('monitor-logs')->everyMinute();
        $schedule->command('backup-db hourly')->hourly()->timezone('Europe/Bucharest');
        $schedule->command('backup-db daily')->daily()->timezone('Europe/Bucharest');
        $schedule->command('backup-db monthly')->monthly()->timezone('Europe/Bucharest');
        $schedule->command('clean-db-backups')->daily()->timezone('Europe/Bucharest');

        $schedule->command('conversion-rate:bnr')->everyFourHours()->timezone('Europe/Bucharest');
    }
}
