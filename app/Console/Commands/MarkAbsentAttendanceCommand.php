<?php

namespace App\Console\Commands;

use App\Services\Attendance\DailyAttendanceJobService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Throwable;

class MarkAbsentAttendanceCommand extends Command
{
    protected $signature = 'attendance:mark-absent {--date= : Hanya tanggal ini (Y-m-d)} {--lookback=2 : Berapa hari ke belakang yang dikejar bila job sempat mati}';

    protected $description = 'Catat alpha untuk pegawai wajib absen yang tidak punya log, setelah jam selesai jadwalnya (libur dan izin dilewati)';

    public function handle(DailyAttendanceJobService $service): int
    {
        try {
            $dates = $this->option('date')
                ? [Carbon::createFromFormat('Y-m-d', $this->option('date'))->startOfDay()]
                : $this->recentDates(max(0, (int) $this->option('lookback')));
        } catch (Throwable) {
            $this->error('Format --date harus Y-m-d.');

            return self::FAILURE;
        }

        foreach ($dates as $date) {
            $result = $service->markAbsent($date);
            $this->info("Alpha otomatis {$date->toDateString()}: {$result['created']} dicatat, {$result['skipped_no_branch']} dilewati (tanpa cabang).");
        }

        return self::SUCCESS;
    }

    /** @return list<Carbon> dari yang terlama sampai hari ini */
    private function recentDates(int $lookback): array
    {
        return collect(range($lookback, 0))->map(fn (int $back) => now()->startOfDay()->subDays($back))->all();
    }
}
