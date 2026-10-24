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
    ->name('facility:check-rent-overdue')->withoutOverlapping()->dailyAt('00:00');

Schedule::call(fn () => Artisan::call('facility:send-rent-due-soon'))
    ->name('facility:send-rent-due-soon')->withoutOverlapping()->dailyAt('00:00');

Schedule::call(fn () => Artisan::call('recruitment:close-expired-postings'))
    ->name('recruitment:close-expired-postings')->withoutOverlapping()->dailyAt('00:05');

// GAP-116: daily agar pay_date custom (bukan tanggal 3) tetap ter-trigger
Schedule::call(fn () => Artisan::call('payroll:generate-monthly-slips'))
    ->name('payroll:generate-monthly-slips')->withoutOverlapping()->dailyAt('06:00');

Schedule::call(fn () => Artisan::call('notification:alert-contract-expiry'))
    ->name('notification:alert-contract-expiry')->withoutOverlapping()->dailyAt('09:00');

// ponytail: telegram:sync dinonaktifkan - TelegramStorageService::syncFilesFromGroup()
// tidak pernah diimplementasi (command + job rujuk method hantu, error 13x/hari).
// Storage = local disk primary, Telegram cuma backup async, jadi polling fallback ini
// belum dibutuhkan. Re-enable kalau method-nya sudah ditulis + ditest.
