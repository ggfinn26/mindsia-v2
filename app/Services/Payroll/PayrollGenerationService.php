<?php

namespace App\Services\Payroll;

use App\Models\AttendanceRuleViolation;
use App\Models\Employee;
use App\Models\EmployeeAttendanceMonthlyRecap;
use App\Models\EmployeeKpiEvaluation;
use App\Models\EmployeePayroll;
use App\Models\EmployeeSessionAttendanceLog;
use App\Models\LeavePaySetting;
use App\Models\PayrollBonusCalculation;
use App\Models\PayrollBonusConditionSnapshot;
use App\Models\PayrollComponent;
use App\Models\PayrollItem;
use App\Models\PayrollPeriod;
use App\Models\SessionSchedule;
use App\Repositories\AttendanceRecapRepository;
use App\Repositories\AttendanceSettingsRepository;
use App\Repositories\Payroll\EmployeePayrollRepository;
use App\Services\Attendance\AttendanceRuleEvaluator;
use App\Services\Attendance\SessionAbsenceService;
use App\Services\Bonus\BonusRuleResolverService;
use Carbon\Carbon;
use Illuminate\Support\Carbon as SupportCarbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use LogicException;

class PayrollGenerationService
{
    public function __construct(
        private readonly EmployeePayrollRepository $payrollRepo,
        private readonly SessionRateResolverService $sessionRateResolver,
        private readonly BonusRuleResolverService $bonusResolver,
        private readonly PayrollNotificationService $notificationService,
        private readonly AttendanceSettingsRepository $attendanceSettings,
        private readonly AttendanceRecapRepository $recapRepo,
        private readonly SessionAbsenceService $sessionAbsence,
        private readonly AttendanceRuleEvaluator $ruleEvaluator,
    ) {}

    public function generate(PayrollPeriod $period): void
    {
        if ($period->status !== 'finalized') {
            throw new LogicException('Payroll hanya bisa di-generate saat periode sudah finalized.');
        }

        DB::transaction(function () use ($period) {
            $this->payrollRepo->deleteByPeriod($period);

            $employees = Employee::active()->with(['user.roles', 'currentStatus.position', 'branch', 'compensations'])->get();

            foreach ($employees as $employee) {
                $this->generateForEmployee($period, $employee);
            }
        });

        $this->notificationService->notifyPayrollReady($period);
    }

    /**
     * Run full payroll calculation inside a rollback transaction — returns preview data without persisting.
     * Ponytail: transactional preview reuses exact same code path, no duplication.
     *
     * @return array<int, array{employee_name: string, net_amount: float}>
     */
    public function preview(PayrollPeriod $period): array
    {
        if ($period->status !== 'finalized') {
            throw new LogicException('Preview hanya bisa dilakukan saat periode sudah finalized.');
        }

        DB::beginTransaction();

        try {
            $this->payrollRepo->deleteByPeriod($period);

            $employees = Employee::active()->with(['user.roles', 'currentStatus.position', 'branch', 'compensations'])->get();

            foreach ($employees as $employee) {
                $this->generateForEmployee($period, $employee);
            }

            $preview = $this->payrollRepo->allByPeriod($period)
                ->map(fn ($p) => [
                    'employee_code' => $p->employee_code_snapshot,
                    'employee_name' => $p->employee_name_snapshot,
                    'branch' => $p->branch_name_snapshot,
                    'total_earnings' => (float) $p->total_earnings,
                    'total_deductions' => (float) $p->total_deductions,
                    'net_amount' => (float) $p->net_amount,
                ])
                ->toArray();

            DB::rollBack();

            return $preview;
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    private function generateForEmployee(PayrollPeriod $period, Employee $employee): void
    {
        $recap = EmployeeAttendanceMonthlyRecap::where('employee_id', $employee->id)
            ->where('period_year', $period->period_year)
            ->where('period_month', $period->period_month)
            ->first();

        if (! $recap && ! $this->attendanceSettings->resolve($employee)->isRequired) {
            // Pegawai yang tidak wajib absen (mis. Board) tidak pernah punya log, jadi tidak pernah punya rekap.
            // Bentuk rekap kosong agar tetap digaji; gaji tetap dibayar penuh.
            $recap = $this->recapRepo->upsertForEmployee($employee->id, SupportCarbon::create($period->period_year, $period->period_month, 1));
        }

        if (! $recap) {
            Log::warning("Skipping employee without attendance recap: {$employee->full_name} (ID: {$employee->id})");

            return;
        }

        [$totalSessions, $attendedSessions, $lateSessions] = $this->querySessionCounts($employee, $period);

        $payroll = $this->payrollRepo->create([
            'payroll_period_id' => $period->id,
            'employee_id' => $employee->id,
            'employee_code_snapshot' => $employee->employee_code,
            'employee_name_snapshot' => $employee->full_name,
            'position_name_snapshot' => $employee->currentStatus?->position?->position_name,
            'branch_name_snapshot' => $employee->branch?->branch_name,
            'scheduled_working_days' => $recap->total_scheduled_working_days,
            'effective_working_days' => $recap->total_effective_working_days,
            'days_present' => $recap->total_present,
            'days_absent' => $recap->total_absent,
            'days_sick' => $recap->total_sick,
            'days_permission' => $recap->total_permission,
            'days_leave' => $recap->total_leave,
            'days_holiday' => $recap->total_holiday,
            'days_late' => $recap->total_late,
            'total_sessions' => $totalSessions,
            'attended_sessions' => $attendedSessions,
            'absent_sessions' => max(0, $totalSessions - $attendedSessions),
            'late_sessions' => $lateSessions,
            'total_earnings' => 0,
            'total_deductions' => 0,
            'net_amount' => 0,
            'payment_status' => 'unpaid',
        ]);

        $components = PayrollComponent::active()->get();
        $compensations = $employee->compensations->keyBy('payroll_component_id');
        $sessionRate = $this->sessionRateResolver->resolve($employee);
        $paidLeaveDays = $this->paidLeaveDays($employee, $recap);
        $baseSalary = 0.0;
        $percentageComponents = [];

        foreach ($components as $component) {
            if ($component->calculation_method === 'percentage') {
                $percentageComponents[] = $component;

                continue;
            }

            $item = $this->calculateItem($component, $compensations, $recap, $attendedSessions, $sessionRate, $paidLeaveDays);

            if ($item === null) {
                continue;
            }

            $created = PayrollItem::create(array_merge($item, [
                'employee_payroll_id' => $payroll->id,
                'payroll_component_id' => $component->id,
                'component_code_snapshot' => $component->component_code,
                'component_name_snapshot' => $component->component_name,
                'component_type_snapshot' => $component->component_type,
                'source_type' => 'compensation',
            ]));

            if ($component->component_code === 'BASE_SALARY' && $component->component_type === 'earning') {
                $baseSalary = (float) $created->total_amount;
            }
        }

        foreach ($percentageComponents as $component) {
            $compensation = $compensations->get($component->id);

            if (! $compensation) {
                continue;
            }

            $amount = $baseSalary * (float) $compensation->value / 100;

            PayrollItem::create([
                'employee_payroll_id' => $payroll->id,
                'payroll_component_id' => $component->id,
                'component_code_snapshot' => $component->component_code,
                'component_name_snapshot' => $component->component_name,
                'component_type_snapshot' => $component->component_type,
                'quantity' => 1,
                'unit_value' => (float) $compensation->value,
                'total_amount' => $amount,
                'source_type' => 'compensation',
            ]);
        }

        // Evaluasi ulang aturan absensi tepat sebelum potongan dihitung: absensi bisa dikoreksi setelah job harian berjalan
        $this->ruleEvaluator->evaluateEmployee($employee, Carbon::create($period->period_year, $period->period_month, 1));
        $this->applyAttendanceDeductions($payroll, $employee, $period, $baseSalary);
        $this->applyLeaveDeductions($payroll, $employee, $period, $baseSalary);
        $this->applyBonuses($payroll, $employee, $period, $baseSalary);

        $this->payrollRepo->updateTotals($payroll);
    }

    /** @return array{quantity: float, unit_value: float, total_amount: float}|null */
    private function calculateItem(PayrollComponent $component, Collection $compensations, EmployeeAttendanceMonthlyRecap $recap, int $attendedSessions, float $sessionRate, int $paidLeaveDays): ?array
    {
        $compensation = $compensations->get($component->id);

        return match ($component->calculation_method) {
            'fixed' => $compensation ? ['quantity' => 1, 'unit_value' => (float) $compensation->value, 'total_amount' => (float) $compensation->value] : null,
            'daily' => $compensation ? $this->dailyItem($compensation->value, $recap, $paidLeaveDays) : null,
            'session' => ['quantity' => $attendedSessions, 'unit_value' => $sessionRate, 'total_amount' => $attendedSessions * $sessionRate],
            'percentage' => null,
            'manual' => null,
        };
    }

    /**
     * Gaji harian: hari hadir ditambah hari izin/sakit/cuti yang berstatus dibayar. Total tidak melebihi hari terjadwal
     * (hari hadir di luar jadwal tetap dibayar penuh).
     *
     * @return array{quantity: float, unit_value: float, total_amount: float}
     */
    private function dailyItem(mixed $dailyValue, EmployeeAttendanceMonthlyRecap $recap, int $paidLeaveDays): array
    {
        $worked = (int) $recap->total_effective_working_days;
        $quantity = $worked + min($paidLeaveDays, max(0, (int) $recap->total_scheduled_working_days - $worked));

        return ['quantity' => $quantity, 'unit_value' => (float) $dailyValue, 'total_amount' => (float) $dailyValue * $quantity];
    }

    /** Hari izin/sakit/cuti pada rekap yang berstatus DIBAYAR untuk posisi pegawai (pengaturan posisi atau kebijakan bawaan). */
    private function paidLeaveDays(Employee $employee, EmployeeAttendanceMonthlyRecap $recap): int
    {
        $positionId = $employee->currentStatus?->position_id;
        $days = ['permission' => (int) $recap->total_permission, 'sick' => (int) $recap->total_sick, 'leave' => (int) $recap->total_leave];

        return array_sum(array_map(
            fn (string $type) => $positionId && LeavePaySetting::isPaidFor($positionId, $type) ? $days[$type] : 0,
            array_keys($days),
        ));
    }

    /**
     * Potongan gaji pokok per hari terjadwal (gaji pokok / hari terjadwal x hari dipotong), dalam dua baris terpisah:
     *  - ALPHA: selalu dipotong, tidak bergantung pengaturan;
     *  - IZIN/SAKIT/CUTI tidak berbayar: sesuai pengaturan posisi, bawaan: izin dipotong, sakit dan cuti digaji.
     * Total hari dipotong tidak melebihi hari terjadwal. Board tidak pernah dipotong.
     */
    private function applyLeaveDeductions(EmployeePayroll $payroll, Employee $employee, PayrollPeriod $period, float $baseSalary): void
    {
        // BOARD employees are always paid in full regardless of leave_pay_settings
        $boardRoles = ['Board', 'CEO', 'COO', 'CHRO', 'CPO', 'CFO', 'CMO'];
        if ($employee->user?->hasAnyRole($boardRoles)) {
            return;
        }

        $positionId = $employee->currentStatus?->position_id;
        $scheduledDays = (int) $payroll->scheduled_working_days;

        if (! $positionId || $scheduledDays === 0 || $baseSalary === 0.0) {
            return;
        }

        // Alpha kerja lalu alpha sesi lalu izin/sakit/cuti; masing-masing bisa diatur dibayar/tidak per posisi.
        $absentDays = LeavePaySetting::isPaidFor($positionId, 'absent') ? 0 : min((int) $payroll->days_absent, $scheduledDays);
        $sessionAbsentDays = LeavePaySetting::isPaidFor($positionId, 'absent_session')
            ? 0
            : min($this->absentSessionDays($employee, $period, excludeWorkAbsent: $absentDays > 0), $scheduledDays - $absentDays);

        $unpaidLeaveDays = 0;
        foreach (['permission' => 'days_permission', 'sick' => 'days_sick', 'leave' => 'days_leave'] as $type => $column) {
            if (! LeavePaySetting::isPaidFor($positionId, $type)) {
                $unpaidLeaveDays += (int) $payroll->{$column};
            }
        }
        $unpaidLeaveDays = min($unpaidLeaveDays, $scheduledDays - $absentDays - $sessionAbsentDays);

        $this->createDeductionItem($payroll, $employee, 'ABSENT_DEDUCTION', $absentDays, $baseSalary, $scheduledDays, 'attendance', 'Potongan alpha');
        $this->createDeductionItem($payroll, $employee, 'ABSENT_DEDUCTION', $sessionAbsentDays, $baseSalary, $scheduledDays, 'attendance', 'Potongan alpha sesi');
        $this->createDeductionItem($payroll, $employee, 'LEAVE_DEDUCTION', $unpaidLeaveDays, $baseSalary, $scheduledDays, 'leave', 'Potongan izin tidak berbayar');
    }

    /** Jumlah hari alpha sesi tutor pada periode payroll (definisi di SessionAbsenceService). */
    private function absentSessionDays(Employee $employee, PayrollPeriod $period, bool $excludeWorkAbsent): int
    {
        $start = Carbon::create($period->period_year, $period->period_month, 1)->startOfDay();

        return $this->sessionAbsence->analyze($employee, $start, $start->copy()->endOfMonth(), $excludeWorkAbsent)['absent']->count();
    }

    private function createDeductionItem(EmployeePayroll $payroll, Employee $employee, string $componentCode, int $days, float $baseSalary, int $scheduledDays, string $sourceType, string $label): void
    {
        if ($days === 0) {
            return;
        }

        $dailyRate = round($baseSalary / $scheduledDays, 2);
        $amount = round($baseSalary / $scheduledDays * $days, 2);
        $component = PayrollComponent::where('component_code', $componentCode)->active()->first();

        if (! $component) {
            Log::warning("PayrollGeneration: missing {$componentCode} component", [
                'employee_id' => $employee->id,
                'days' => $days,
                'deduction_amount' => $amount,
            ]);

            return;
        }

        PayrollItem::create([
            'employee_payroll_id' => $payroll->id,
            'payroll_component_id' => $component->id,
            'component_code_snapshot' => $component->component_code,
            'component_name_snapshot' => $component->component_name,
            'component_type_snapshot' => 'deduction',
            'quantity' => $days,
            'unit_value' => $dailyRate,
            'total_amount' => $amount,
            'source_type' => $sourceType,
            'description' => "{$label}: {$days} hari",
        ]);
    }

    private function applyAttendanceDeductions(EmployeePayroll $payroll, Employee $employee, PayrollPeriod $period, float $baseSalary): void
    {
        $violations = AttendanceRuleViolation::where('employee_id', $employee->id)
            ->whereHas('rule', fn ($q) => $q->where('is_active', true))   // aturan yang dinonaktifkan tidak lagi memotong
            ->whereYear('period_start_date', $period->period_year)
            ->whereMonth('period_start_date', $period->period_month)
            ->with(['actionExecutions.action.payrollAction.payrollComponent'])
            ->get();

        foreach ($violations as $violation) {
            foreach ($violation->actionExecutions as $execution) {
                $action = $execution->action;

                if ($action->action_type !== 'payroll_deduction' || ! $action->payrollAction) {
                    continue;
                }

                $payrollAction = $action->payrollAction;
                $component = $payrollAction->payrollComponent;

                if (! $component) {
                    continue;
                }

                $amount = match ($payrollAction->deduction_type) {
                    'fixed_amount' => (float) $payrollAction->deduction_value,
                    'per_minute' => (float) $violation->trigger_value * (float) $payrollAction->deduction_value,
                    'percentage' => $baseSalary * (float) $payrollAction->deduction_value / 100,
                    default => 0.0,
                };

                PayrollItem::create([
                    'employee_payroll_id' => $payroll->id,
                    'payroll_component_id' => $component->id,
                    'component_code_snapshot' => $component->component_code,
                    'component_name_snapshot' => $component->component_name,
                    'component_type_snapshot' => 'deduction',
                    'quantity' => 1,
                    'unit_value' => $amount,
                    'total_amount' => $amount,
                    'source_type' => 'attendance',
                    'source_id' => $violation->id,
                    'description' => "Potongan pelanggaran absensi: {$violation->id}",
                ]);

                if ($execution->status === 'pending') {
                    $execution->update(['status' => 'processed', 'executed_at' => now(), 'result_notes' => "Dipotong di payroll {$period->period_year}-{$period->period_month}"]);
                }
            }
        }
    }

    private function applyBonuses(EmployeePayroll $payroll, Employee $employee, PayrollPeriod $period, float $baseSalary): void
    {
        $positionId = $employee->currentStatus?->position_id ?? 0;
        $roleId = $employee->user?->roles->first()?->id ?? 0;
        $periodEnd = Carbon::create($period->period_year, $period->period_month, 1)->endOfMonth();
        $tenureMonths = $this->tenureMonths($employee, $periodEnd);

        // Tanpa evaluasi KPI final untuk bulan ini, bonus KPI dilewati (bukan dihitung dengan skor 0 yang bisa jatuh ke tier terendah).
        $kpiScore = $this->resolveKpiScore($employee->id, $period);

        $bonuses = array_merge(
            $this->bonusResolver->resolveMarketingBonus($employee->id, $positionId, $roleId, $period->period_year, $period->period_month, $baseSalary, $tenureMonths),
            $kpiScore === null ? [] : $this->bonusResolver->resolveKpiBonus($employee->id, $positionId, $roleId, $kpiScore, $baseSalary),
            $this->bonusResolver->resolveSpecialBonus($employee->id, $positionId, $roleId, $baseSalary, $period->period_year, $period->period_month),
        );

        foreach ($bonuses as $bonus) {
            $bonusComponent = PayrollComponent::where('component_code', 'BONUS_'.strtoupper($bonus['type']))->active()->first();

            if (! $bonusComponent) {
                Log::warning('PayrollGeneration: missing bonus component', [
                    'expected_code' => 'BONUS_'.strtoupper($bonus['type']),
                    'employee_id' => $employee->id,
                    'period' => "{$period->period_year}-{$period->period_month}",
                ]);

                continue;
            }

            $item = PayrollItem::create([
                'employee_payroll_id' => $payroll->id,
                'payroll_component_id' => $bonusComponent->id,
                'component_code_snapshot' => $bonusComponent->component_code,
                'component_name_snapshot' => $bonusComponent->component_name,
                'component_type_snapshot' => 'earning',
                'quantity' => 1,
                'unit_value' => $bonus['calculated_amount'],
                'total_amount' => $bonus['calculated_amount'],
                'source_type' => 'bonus',
                'source_id' => $bonus['rule_id'],
                'description' => $bonus['rule_name'],
            ]);

            $calc = PayrollBonusCalculation::create([
                'employee_payroll_id' => $payroll->id,
                'payroll_item_id' => $item->id,
                'calculation_key' => "{$payroll->id}_{$bonus['type']}_{$bonus['rule_id']}",
                'bonus_type' => $bonus['type'],
                'rule_id' => $bonus['rule_id'],
                'rule_code_snapshot' => $bonus['rule_code'],
                'rule_name_snapshot' => $bonus['rule_name'],
                'reward_type_snapshot' => $bonus['reward_type'],
                'reward_basis_snapshot' => $bonus['reward_basis'],
                'reward_value_snapshot' => $bonus['reward_value'],
                'base_amount_snapshot' => $bonus['base_amount'],
                'calculated_amount' => $bonus['calculated_amount'],
            ]);

            if (isset($bonus['conditions'])) {
                foreach ($bonus['conditions'] as $cond) {
                    PayrollBonusConditionSnapshot::create([
                        'payroll_bonus_calculation_id' => $calc->id,
                        'metric_code_snapshot' => $cond['metric_code'],
                        'data_source_snapshot' => $cond['data_source'],
                        'period_type_snapshot' => 'monthly',
                        'operator_snapshot' => $cond['operator'],
                        'target_value_snapshot' => $cond['target_value'],
                        'actual_value_snapshot' => $cond['actual_value'],
                        'condition_passed' => $cond['passed'],
                    ]);
                }
            }
        }
    }

    /**
     * Skor evaluasi KPI final yang periodenya BERAKHIR di bulan payroll ini. Evaluasi bulan/tahun lain tidak
     * dipakai (tidak ada carry-over); bila ada beberapa (mis. bulanan dan kuartalan), yang terakhir dipakai.
     */
    private function resolveKpiScore(int $employeeId, PayrollPeriod $period): ?float
    {
        $start = Carbon::create($period->period_year, $period->period_month, 1)->startOfDay();

        $score = EmployeeKpiEvaluation::where('employee_id', $employeeId)
            ->where('status', 'finalized')
            ->whereBetween('period_end_date', [$start->toDateString(), $start->copy()->endOfMonth()->toDateString()])
            ->orderByDesc('period_end_date')
            ->orderByDesc('id')
            ->value('total_score');

        return $score === null ? null : (float) $score;
    }

    /** Masa kerja penuh (bulan) dari tanggal bergabung sampai akhir periode payroll, bukan sampai hari ini. */
    private function tenureMonths(Employee $employee, Carbon $periodEnd): int
    {
        $joined = $employee->join_date ?? $employee->currentStatus?->created_at;

        if (! $joined || Carbon::parse($joined)->startOfDay()->gt($periodEnd)) {
            return 0;
        }

        return (int) floor(Carbon::parse($joined)->startOfDay()->diffInMonths($periodEnd));
    }

    /** @return array{0: int, 1: int, 2: int} [total, attended, late] */
    private function querySessionCounts(Employee $employee, PayrollPeriod $period): array
    {
        $total = SessionSchedule::where('employee_id', $employee->id)
            ->whereHas('classSchedule', fn ($q) => $q
                ->whereYear('schedule_date', $period->period_year)
                ->whereMonth('schedule_date', $period->period_month))
            ->count();

        $logs = EmployeeSessionAttendanceLog::where('employee_id', $employee->id)
            ->where('status', 'present')
            ->whereHas('sessionSchedule.classSchedule', fn ($q) => $q
                ->whereYear('schedule_date', $period->period_year)
                ->whereMonth('schedule_date', $period->period_month))
            ->get(['late_minutes']);

        $attended = $logs->count();
        $late = $logs->filter(fn ($log) => $log->late_minutes > 0)->count();

        return [$total, $attended, $late];
    }
}
