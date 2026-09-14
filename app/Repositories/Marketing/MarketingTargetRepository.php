<?php

namespace App\Repositories\Marketing;

use App\Models\Employee;
use App\Models\MarketingTarget;
use App\Models\MarketingTargetDefault;
use Carbon\Carbon;

class MarketingTargetRepository
{
    public function resolve(Employee $employee, int $month, int $year): array
    {
        $employeeTarget = MarketingTarget::where('employee_id', $employee->id)
            ->where('period_month', $month)
            ->where('period_year', $year)
            ->first();

        if ($employeeTarget) {
            return [
                'classes_target' => $employeeTarget->classes_target,
                'omzet_target' => (float) $employeeTarget->omzet_target,
                'source' => 'employee',
            ];
        }

        $positionTarget = MarketingTargetDefault::where('position_id', $employee->position_id)
            ->where('period_month', $month)
            ->where('period_year', $year)
            ->first();

        if ($positionTarget) {
            return [
                'classes_target' => $positionTarget->classes_target,
                'omzet_target' => (float) $positionTarget->omzet_target,
                'source' => 'position',
            ];
        }

        return [
            'classes_target' => $this->autoClassesTarget($month, $year),
            'omzet_target' => 0.0,
            'source' => 'auto',
        ];
    }

    public function setEmployeeTarget(array $data): MarketingTarget
    {
        return MarketingTarget::updateOrCreate(
            [
                'employee_id' => $data['employee_id'],
                'period_month' => $data['period_month'],
                'period_year' => $data['period_year'],
            ],
            $data
        );
    }

    public function setPositionDefault(array $data): MarketingTargetDefault
    {
        return MarketingTargetDefault::updateOrCreate(
            [
                'position_id' => $data['position_id'],
                'period_month' => $data['period_month'],
                'period_year' => $data['period_year'],
            ],
            $data
        );
    }

    private function autoClassesTarget(int $month, int $year): int
    {
        $start = Carbon::create($year, $month, 1)->startOfMonth();
        $end = $start->copy()->endOfMonth();
        $workingDays = 0;

        for ($d = $start->copy(); $d->lte($end); $d->addDay()) {
            if ($d->isWeekday()) {
                $workingDays++;
            }
        }

        return $workingDays * 2;
    }
}
