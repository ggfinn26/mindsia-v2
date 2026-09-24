<?php

namespace App\Observers;

use App\Models\Employee;
use App\Models\MarketingPerformance;
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
}
