<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('recruitment:close-expired-postings')
            ->dailyAt('00:05')
            ->withoutOverlapping();

        $schedule->command('telegram:sync --limit=50')
            ->everyFiveMinutes()
            ->withoutOverlapping()
            ->onSuccess(function () {
                Log::info('Telegram sync completed');
            })
            ->onFailure(function () {
                Log::error('Telegram sync failed');
            });
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
    }
}
