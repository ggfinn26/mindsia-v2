<?php

namespace App\Observers;

use App\Models\Employee;
use App\Services\Marketing\MarketingSnapshotService;

class MarketingSnapshotObserver
{
    public function __construct(
        private readonly MarketingSnapshotService $service,
    ) {}

    public function triggerAppend(int $employeeId, int $month, int $year): void
    {
        $employee = Employee::with('branch.area.region')->findOrFail($employeeId);

        $this->service->append($employee, $month, $year);
    }
}
