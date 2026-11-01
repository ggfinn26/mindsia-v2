<?php

namespace App\Services\Attendance;

use App\Models\AttendanceRule;
use App\Models\AttendanceRuleActionExecution;
use App\Models\AttendanceRuleViolation;
use App\Models\Employee;
use App\Models\EmployeeAttendanceMonthlyRecap;
use App\Models\EmployeeWarningLetter;
use App\Models\EmployeeWorkAttendanceLog;
use App\Repositories\AttendanceRecapRepository;
use App\Repositories\HolidayRepository;
use App\Repositories\WorkScheduleRepository;
use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Mengubah aturan absensi (UI Attendance Rules) menjadi pelanggaran per pegawai, lalu membuat eksekusi aksinya.
 *
 * Idempoten: satu pelanggaran per (pegawai, aturan, periode); dijalankan ulang memperbarui nilainya, membuat eksekusi
 * yang belum ada, dan menghapus pelanggaran yang sudah tidak terpenuhi (kecuali sudah dirujuk surat peringatan).
 * Pelanggaran hanya dibuat bila nilai metriknya > 0, jadi operator seperti "<=" tidak menjerat semua orang.
 * Aksi non-payroll (surat peringatan, notifikasi, dst.) dibiarkan `pending` untuk diproses HR; potongan gaji diproses
 * saat payroll di-generate.
 */
class AttendanceRuleEvaluator
{
    public function __construct(
        private readonly WorkScheduleRepository $scheduleRepo,
        private readonly HolidayRepository $holidayRepo,
        private readonly AttendanceRecapRepository $recapRepo,
        private readonly SessionAbsenceService $sessions,
    ) {}

    /** @return array{created: int, updated: int, removed: int} */
    public function evaluateMonth(CarbonInterface $month, ?CarbonInterface $now = null): array
    {
        $totals = ['created' => 0, 'updated' => 0, 'removed' => 0];
        $rules = $this->activeRules();

        // Tanpa aturan aktif tetap perlu membersihkan pelanggaran sisa dari aturan yang baru dinonaktifkan
        $monthStart = Carbon::instance($month)->startOfMonth();
        if ($rules->isEmpty() && ! AttendanceRuleViolation::whereBetween('period_start_date', [$monthStart->toDateString(), $monthStart->copy()->endOfMonth()->toDateString()])->exists()) {
            return $totals;
        }

        Employee::active()->with('currentStatus.position')->chunkById(100, function ($employees) use ($month, $now, $rules, &$totals) {
            foreach ($employees as $employee) {
                foreach ($this->evaluateEmployee($employee, $month, $now, $rules) as $key => $count) {
                    $totals[$key] += $count;
                }
            }
        });

        return $totals;
    }

    /**
     * @param  Collection<int, AttendanceRule>|null  $rules  aturan aktif (dimuat sekali oleh pemanggil bila banyak pegawai)
     * @return array{created: int, updated: int, removed: int}
     */
    public function evaluateEmployee(Employee $employee, CarbonInterface $month, ?CarbonInterface $now = null, ?Collection $rules = null): array
    {
        $result = ['created' => 0, 'updated' => 0, 'removed' => 0];
        $rules ??= $this->activeRules();

        $monthStart = Carbon::instance($month)->startOfMonth();
        $monthEnd = $monthStart->copy()->endOfMonth()->startOfDay();
        $upTo = $monthEnd->min(Carbon::instance($now ?? now())->startOfDay());

        if ($upTo->lt($monthStart)) {
            return $result;
        }

        $found = [];
        foreach ($rules as $rule) {
            foreach ($this->hits($rule, $employee, $monthStart, $monthEnd, $upTo, $now) as [$start, $end, $value]) {
                $found[$rule->id.'|'.$start->toDateString().'|'.$end->toDateString()] = [$rule, $start, $end, $value];
            }
        }

        return DB::transaction(function () use ($employee, $monthStart, $monthEnd, $found, $result) {
            $existing = AttendanceRuleViolation::where('employee_id', $employee->id)
                ->whereBetween('period_start_date', [$monthStart->toDateString(), $monthEnd->toDateString()])
                ->get()
                ->keyBy(fn ($v) => $v->attendance_rule_id.'|'.$v->period_start_date->toDateString().'|'.$v->period_end_date->toDateString());

            foreach ($found as $key => [$rule, $start, $end, $value]) {
                $violation = $existing->get($key);

                if ($violation) {
                    if ((float) $violation->trigger_value !== (float) $value) {
                        $violation->update(['trigger_value' => $value]);
                        $result['updated']++;
                    }
                } else {
                    $violation = AttendanceRuleViolation::create([
                        'employee_id' => $employee->id,
                        'attendance_rule_id' => $rule->id,
                        'period_start_date' => $start->toDateString(),
                        'period_end_date' => $end->toDateString(),
                        'trigger_value' => $value,
                    ]);
                    $result['created']++;
                }

                foreach ($rule->actions as $action) {
                    AttendanceRuleActionExecution::firstOrCreate(
                        ['attendance_rule_violation_id' => $violation->id, 'attendance_rule_action_id' => $action->id],
                        ['status' => 'pending'],
                    );
                }
            }

            foreach ($existing->diffKeys($found) as $stale) {
                if (EmployeeWarningLetter::where('attendance_rule_violation_id', $stale->id)->exists()) {
                    continue;   // sudah menjadi dasar surat peringatan: pertahankan sebagai riwayat
                }
                $stale->actionExecutions()->delete();
                $stale->delete();
                $result['removed']++;
            }

            return $result;
        });
    }

    /** @return Collection<int, AttendanceRule> */
    private function activeRules(): Collection
    {
        return AttendanceRule::with('actions')->where('is_active', true)->get();
    }

    /** @return list<array{0: Carbon, 1: Carbon, 2: float}> [awal periode, akhir periode, nilai] */
    private function hits(AttendanceRule $rule, Employee $employee, Carbon $monthStart, Carbon $monthEnd, Carbon $upTo, ?CarbonInterface $now): array
    {
        $isSession = $rule->attendance_type === 'session';

        if ($rule->trigger_type === 'daily_late') {
            $byDate = $isSession
                ? $this->sessions->lateMinutesByDate($employee, $monthStart, $upTo)
                : $this->workLateMinutesByDate($employee, $monthStart, $upTo);

            return $byDate
                ->filter(fn ($minutes) => $minutes > 0 && $this->compare((float) $minutes, $rule->trigger_operator, (float) $rule->trigger_value))
                ->map(fn ($minutes, $date) => [Carbon::parse($date), Carbon::parse($date), (float) $minutes])
                ->values()->all();
        }

        $value = $isSession
            ? $this->sessionMetric($rule->trigger_type, $employee, $monthStart, $upTo, $now)
            : $this->workMetric($rule->trigger_type, $employee, $monthStart, $upTo);

        if ($value === null || $value <= 0 || ! $this->compare($value, $rule->trigger_operator, (float) $rule->trigger_value)) {
            return [];
        }

        return [[$monthStart->copy(), $monthEnd->copy(), $value]];
    }

    private function workMetric(string $trigger, Employee $employee, Carbon $monthStart, Carbon $upTo): ?float
    {
        if ($trigger === 'consecutive_absence') {
            return (float) $this->longestStreak($this->scheduledDates($employee, $monthStart, $upTo), $this->workStatuses($employee, $monthStart, $upTo));
        }

        $recap = EmployeeAttendanceMonthlyRecap::where('employee_id', $employee->id)
            ->where('period_year', $monthStart->year)->where('period_month', $monthStart->month)->first();

        return $recap ? (float) match ($trigger) {
            'monthly_absence' => $recap->total_absent,
            'monthly_late_count' => $recap->total_late,
            'monthly_late_minutes' => $recap->total_late_minutes,
            default => 0,
        } : null;
    }

    private function sessionMetric(string $trigger, Employee $employee, Carbon $monthStart, Carbon $upTo, ?CarbonInterface $now): ?float
    {
        if (in_array($trigger, ['monthly_late_count', 'monthly_late_minutes'], true)) {
            $late = $this->sessions->lateTotals($employee, $monthStart, $upTo);

            return (float) ($trigger === 'monthly_late_count' ? $late['count'] : $late['minutes']);
        }

        $analysis = $this->sessions->analyze($employee, $monthStart, $upTo, excludeWorkAbsent: false, now: $now);

        return (float) match ($trigger) {
            'monthly_absence' => $analysis['absent']->count(),
            'consecutive_absence' => $this->longestStreak($analysis['dates']->all(), $analysis['absent']->flip()->map(fn () => 'absent')->all()),
            default => 0,
        };
    }

    /** Hari kerja terjadwal (bukan libur, dalam masa kerja) sampai $upTo, terurut. @return list<string> */
    private function scheduledDates(Employee $employee, Carbon $monthStart, Carbon $upTo): array
    {
        $window = $this->recapRepo->employmentWindow($employee, $monthStart, $upTo);

        if (! $window) {
            return [];
        }

        [$from, $to] = $window;
        $holidays = array_flip($this->holidayRepo->datesBetween($from->toDateString(), $to->toDateString()));
        $dates = array_keys($this->scheduleRepo->resolveForPeriod($employee, $from->toDateString(), $to->toDateString()));

        return collect($dates)->reject(fn ($date) => isset($holidays[$date]))->sort()->values()->all();
    }

    /** @return array<string, string> tanggal => status log kerja */
    private function workStatuses(Employee $employee, Carbon $from, Carbon $to): array
    {
        return EmployeeWorkAttendanceLog::where('employee_id', $employee->id)
            ->whereBetween('attendance_date', [$from->toDateString(), $to->toDateString()])
            ->get(['status', 'attendance_date'])
            ->mapWithKeys(fn ($log) => [$log->attendance_date->toDateString() => $log->status])
            ->all();
    }

    /** @return Collection<string, int> tanggal => menit terlambat (> 0) */
    private function workLateMinutesByDate(Employee $employee, Carbon $from, Carbon $to): Collection
    {
        return EmployeeWorkAttendanceLog::where('employee_id', $employee->id)
            ->whereBetween('attendance_date', [$from->toDateString(), $to->toDateString()])
            ->where('late_minutes', '>', 0)
            ->orderBy('attendance_date')
            ->get(['attendance_date', 'late_minutes'])
            ->mapWithKeys(fn ($log) => [$log->attendance_date->toDateString() => (int) $log->late_minutes]);
    }

    /**
     * Rentetan alpha terpanjang pada urutan hari; hari berstatus selain absent (hadir, izin, sakit, cuti, atau belum ada
     * log) memutus rentetan.
     *
     * @param  list<string>  $dates  terurut
     * @param  array<string, string>  $statuses  tanggal => status
     */
    private function longestStreak(array $dates, array $statuses): int
    {
        $longest = $current = 0;

        foreach ($dates as $date) {
            $current = ($statuses[$date] ?? null) === 'absent' ? $current + 1 : 0;
            $longest = max($longest, $current);
        }

        return $longest;
    }

    private function compare(float $actual, string $operator, float $target): bool
    {
        return match ($operator) {
            '>=' => $actual >= $target,
            '<=' => $actual <= $target,
            '>' => $actual > $target,
            '<' => $actual < $target,
            '=' => $actual == $target,
            '!=' => $actual != $target,
            default => false,
        };
    }
}
