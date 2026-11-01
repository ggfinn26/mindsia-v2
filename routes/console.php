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

// Batas absen keluar 23:59: log check-in tanpa check-out kemarin jadi Hadir + is_check_out_missed (dampak KPI, bukan potongan).
Schedule::call(fn () => Artisan::call('attendance:finalize-missed-checkout'))
    ->name('attendance:finalize-missed-checkout')->withoutOverlapping()->dailyAt('00:05');

// Jam kerja mengikuti jadwal masing-masing pegawai (diatur per posisi/role): job sering jalan, tiap pegawai baru diproses
// ketika jam mulai (auto-hadir) atau jam selesai (alpha) jadwalnya tiba. Aman dijalankan berulang.
Schedule::call(fn () => Artisan::call('attendance:auto-present'))
    ->name('attendance:auto-present')->withoutOverlapping()->everyFiveMinutes();

Schedule::call(fn () => Artisan::call('attendance:mark-absent'))
    ->name('attendance:mark-absent')->withoutOverlapping()->hourlyAt(10);

Schedule::call(fn () => Artisan::call('recruitment:close-expired-postings'))
    ->name('recruitment:close-expired-postings')->withoutOverlapping()->dailyAt('00:05');

// GAP-116: daily agar pay_date custom (bukan tanggal 3) tetap ter-trigger
Schedule::call(fn () => Artisan::call('payroll:generate-monthly-slips'))
    ->name('payroll:generate-monthly-slips')->withoutOverlapping()->dailyAt('06:00');

Schedule::call(fn () => Artisan::call('notification:alert-contract-expiry'))
    ->name('notification:alert-contract-expiry')->withoutOverlapping()->dailyAt('09:00');

// Sosialisasi yang jadwalnya sudah lewat -> Menunggu Validasi (nunggu laporan divalidasi).
Schedule::call(fn () => Artisan::call('socialization:await-validation'))
    ->name('socialization:await-validation')->withoutOverlapping()->everyThirtyMinutes();

// ponytail: telegram:sync dinonaktifkan - TelegramStorageService::syncFilesFromGroup()
// tidak pernah diimplementasi (command + job rujuk method hantu, error 13x/hari).
// Storage = local disk primary, Telegram cuma backup async, jadi polling fallback ini
// belum dibutuhkan. Re-enable kalau method-nya sudah ditulis + ditest.
