<?php

namespace App\Console\Commands;

use App\Models\EmployeeSessionAttendanceLog;
use App\Models\EmployeeWorkAttendanceLog;
use Illuminate\Console\Command;

class FinalizeMissedCheckOutCommand extends Command
{
    protected $signature = 'attendance:finalize-missed-checkout';

    protected $description = 'Log check-in (kerja dan sesi) tanpa check-out dari hari sebelumnya otomatis jadi Hadir (lupa absen keluar)';

    public function handle(): void
    {
        $count = 0;

        // Per model (bukan mass update) agar EmployeeWorkAttendanceLogObserver menyegarkan rekap bulanan.
        EmployeeWorkAttendanceLog::where('status', 'checked_in')
            ->whereNull('check_out')
            ->where('attendance_date', '<', now()->toDateString())
            ->chunkById(200, function ($logs) use (&$count) {
                foreach ($logs as $log) {
                    $log->update(['status' => 'present', 'is_check_out_missed' => true]);
                    $count++;
                }
            });

        // Absensi sesi tutor: sesi yang tanggalnya sudah lewat dan belum check-out. Tanpa ini sesi tidak dibayar.
        EmployeeSessionAttendanceLog::where('status', 'checked_in')
            ->whereNull('check_out')
            ->whereHas('sessionSchedule.classSchedule', fn ($q) => $q->where('schedule_date', '<', now()->toDateString()))
            ->chunkById(200, function ($logs) use (&$count) {
                foreach ($logs as $log) {
                    $log->update(['status' => 'present', 'is_check_out_missed' => true]);
                    $count++;
                }
            });

        $this->info("Difinalisasi lupa absen keluar: {$count} log.");
    }
}
