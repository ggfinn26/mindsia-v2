<?php

namespace App\Observers;

use App\Models\Employee;
use App\Models\MarketingKpiSnapshot;
use App\Models\MarketingPerformance;
use App\Models\MarketingTarget;
use App\Models\MemberPayment;
use App\Models\ProspectiveMember;
use App\Services\Marketing\MarketingSnapshotService;

class MarketingSnapshotObserver
{
    public function __construct(
        private readonly MarketingSnapshotService $service,
    ) {}

    public function triggerForEmployee(int $employeeId, int $month, int $year): void
    {
        $employee = Employee::with('branch.area.region')->find($employeeId);
        if ($employee) {
            $this->service->append($employee, $month, $year);
        }
    }

    public function prospectiveMemberSaved(ProspectiveMember $model): void
    {
        if (! $model->captured_by_employee_id) {
            return;
        }

        $this->triggerForEmployee(
            $model->captured_by_employee_id,
            now()->month,
            now()->year
        );
    }

    public function marketingPerformanceSaved(MarketingPerformance $model): void
    {
        $this->triggerForEmployee(
            $model->employee_id,
            $model->period_month,
            $model->period_year
        );
    }

    public function paymentSaved(MemberPayment $payment): void
    {
        if ($payment->payment_status !== 'paid') {
            return;
        }

        $employeeId = $payment->registration?->memberData?->referred_by_employee_id;
        if (! $employeeId) {
            return;
        }

        $paidAt = $payment->paid_at ?? now();
        $this->triggerForEmployee($employeeId, (int) $paidAt->month, (int) $paidAt->year);
    }

    /**
     * Employee-specific target changed: regen that employee's snapshot for the
     * target period, unless the period is already finalized.
     */
    public function employeeTargetSaved(MarketingTarget $target): void
    {
        if ($this->isFinalized($target->employee_id, $target->period_month, $target->period_year)) {
            return;
        }

        $this->triggerForEmployee($target->employee_id, $target->period_month, $target->period_year);
    }

    /**
     * Position/global default target changed: regen the current open period for
     * every employee that already has a non-finalized snapshot in it.
     */
    public function defaultTargetSaved(): void
    {
        $month = now()->month;
        $year = now()->year;

        MarketingKpiSnapshot::query()
            ->where('period_month', $month)
            ->where('period_year', $year)
            ->whereNull('finalized_at')
            ->distinct()
            ->pluck('employee_id')
            ->each(fn (int $employeeId) => $this->triggerForEmployee($employeeId, $month, $year));
    }

    private function isFinalized(int $employeeId, int $month, int $year): bool
    {
        return MarketingKpiSnapshot::where('employee_id', $employeeId)
            ->where('period_month', $month)
            ->where('period_year', $year)
            ->whereNotNull('finalized_at')
            ->exists();
    }
}
