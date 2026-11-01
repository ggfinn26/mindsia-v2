<?php

namespace App\Console\Commands;

use App\Services\Attendance\AttendanceRuleEvaluator;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Throwable;

class EvaluateAttendanceRulesCommand extends Command
{
    protected $signature = 'attendance:evaluate-rules {--month= : Bulan Y-m; default bulan berjalan (dan bulan lalu pada 5 hari pertama)}';

    protected $description = 'Evaluasi aturan absensi (Attendance Rules) menjadi pelanggaran per pegawai beserta eksekusi aksinya';

    public function handle(AttendanceRuleEvaluator $evaluator): int
    {
        try {
            $months = $this->option('month')
                ? [Carbon::createFromFormat('Y-m', $this->option('month'))->startOfMonth()]
                : $this->defaultMonths();
        } catch (Throwable) {
            $this->error('Format --month harus Y-m.');

            return self::FAILURE;
        }

        foreach ($months as $month) {
            $result = $evaluator->evaluateMonth($month);
            $this->info("Aturan absensi {$month->format('Y-m')}: {$result['created']} baru, {$result['updated']} diperbarui, {$result['removed']} dihapus.");
        }

        return self::SUCCESS;
    }

    /** Bulan lalu ikut dievaluasi pada awal bulan agar koreksi akhir bulan tidak terlewat. @return list<Carbon> */
    private function defaultMonths(): array
    {
        $months = [now()->startOfMonth()];

        if (now()->day <= 5) {
            array_unshift($months, now()->subMonthNoOverflow()->startOfMonth());
        }

        return $months;
    }
}
