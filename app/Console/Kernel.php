<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('telegram:sync --limit=50')
            ->everyFiveMinutes()
            ->withoutOverlapping()
            ->onSuccess(function () {
                \Illuminate\Support\Facades\Log::info('Telegram sync completed');
            })
            ->onFailure(function () {
                \Illuminate\Support\Facades\Log::error('Telegram sync failed');
            });
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
    }
}
