<?php

namespace App\Console\Commands;

use App\Services\Attendance\DailyAttendanceJobService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Throwable;

class AutoPresentAttendanceCommand extends Command
{
    protected $signature = 'attendance:auto-present {--date= : Tanggal Y-m-d, default hari ini}';

    protected $description = 'Catat hadir otomatis untuk pegawai yang tidak wajib absen, mulai dari jam mulai jadwal masing-masing';

    public function handle(DailyAttendanceJobService $service): int
    {
        try {
            $date = $this->option('date') ? Carbon::createFromFormat('Y-m-d', $this->option('date'))->startOfDay() : now()->startOfDay();
        } catch (Throwable) {
            $this->error('Format --date harus Y-m-d.');

            return self::FAILURE;
        }

        $result = $service->autoPresent($date);

        $this->info("Hadir otomatis {$date->toDateString()}: {$result['created']} dicatat, {$result['skipped_no_branch']} dilewati (tanpa cabang).");

        return self::SUCCESS;
    }
}
