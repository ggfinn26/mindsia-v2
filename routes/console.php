<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ponytail: shared hosting (Hostinger) menonaktifkan proc_open, jadi Schedule::command() yang
// spawn sub-proses akan gagal. Pakai Schedule::call() + Artisan::call() agar command jalan
// in-process. Satu cron cukup: `* * * * * php artisan schedule:run`.
Schedule::call(fn () => Artisan::call('facility:check-rent-overdue'))
    ->dailyAt('00:00')->withoutOverlapping()->name('facility:check-rent-overdue');

Schedule::call(fn () => Artisan::call('facility:send-rent-due-soon'))
    ->dailyAt('00:00')->withoutOverlapping()->name('facility:send-rent-due-soon');

Schedule::call(fn () => Artisan::call('recruitment:close-expired-postings'))
    ->dailyAt('00:05')->withoutOverlapping()->name('recruitment:close-expired-postings');

// GAP-116: daily agar pay_date custom (bukan tanggal 3) tetap ter-trigger
Schedule::call(fn () => Artisan::call('payroll:generate-monthly-slips'))
    ->dailyAt('06:00')->withoutOverlapping()->name('payroll:generate-monthly-slips');

Schedule::call(fn () => Artisan::call('notification:alert-contract-expiry'))
    ->dailyAt('09:00')->withoutOverlapping()->name('notification:alert-contract-expiry');

Schedule::call(fn () => Artisan::call('telegram:sync', ['--limit' => 50]))
    ->everyFiveMinutes()->withoutOverlapping()->name('telegram:sync');
