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

        $schedule->command('notification:alert-contract-expiry')
            ->dailyAt('09:00')
            ->withoutOverlapping();

        // GAP-116: pakai daily agar pay_date yang custom (bukan tanggal 3) tetap ter-trigger
        $schedule->command('payroll:generate-monthly-slips')
            ->dailyAt('06:00')
            ->withoutOverlapping();

        $schedule->command('facility:check-rent-overdue')
            ->dailyAt('00:00')
            ->withoutOverlapping();

        $schedule->command('facility:send-rent-due-soon')
            ->dailyAt('00:00')
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
