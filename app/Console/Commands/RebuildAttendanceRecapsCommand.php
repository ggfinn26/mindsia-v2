<?php

namespace App\Console\Commands;

use App\Models\EmployeeAttendanceMonthlyRecap;
use App\Repositories\AttendanceRecapRepository;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class RebuildAttendanceRecapsCommand extends Command
{
    protected $signature = 'attendance:rebuild-recaps {--year= : Batasi ke tahun tertentu} {--month= : Batasi ke bulan tertentu (1-12)}';

    protected $description = 'Hitung ulang rekap absensi bulanan yang sudah tersimpan (mis. setelah aturan hari terjadwal berubah)';

    public function handle(AttendanceRecapRepository $repo): void
    {
        $count = 0;

        EmployeeAttendanceMonthlyRecap::query()
            ->when($this->option('year'), fn ($q, $year) => $q->where('period_year', $year))
            ->when($this->option('month'), fn ($q, $month) => $q->where('period_month', $month))
            ->orderBy('id')
            ->each(function (EmployeeAttendanceMonthlyRecap $recap) use ($repo, &$count) {
                $repo->upsertForEmployee($recap->employee_id, Carbon::create($recap->period_year, $recap->period_month, 1));
                $count++;
            });

        $this->info("Rekap dihitung ulang: {$count}.");
    }
}
