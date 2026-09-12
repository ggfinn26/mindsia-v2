<?php

namespace App\Services\Bonus;

use App\Models\EmployeeAttendanceMonthlyRecap;
use Illuminate\Support\Facades\DB;

class BonusDataSourceService
{
    private array $cache = [];

    public function resolveMarketingAchievement(int $employeeId, int $periodYear, int $periodMonth): float
    {
        $key = "{$employeeId}:{$periodYear}:{$periodMonth}";

        if (! array_key_exists($key, $this->cache)) {
            $this->cache[$key] = (float) DB::table('marketing_performances')
                ->where('employee_id', $employeeId)
                ->where('period_year', $periodYear)
                ->where('period_month', $periodMonth)
                ->value('achievement_percentage');
        }

        return $this->cache[$key];
    }

    public function resolveRevenue(string $revenueBasis, int $employeeId, int $periodYear, int $periodMonth): float
    {
        // ponytail: not implemented — expand when marketing payment tables are stable
        throw new \LogicException("resolveRevenue('{$revenueBasis}') not yet implemented.");
    }

    public function resolveMetric(string $dataSource, int $employeeId, int $periodYear, int $periodMonth): ?float
    {
        return match ($dataSource) {
            'attendance_days_present' => $this->resolveAttendanceDaysPresent($employeeId, $periodYear, $periodMonth),
            'marketing_achievement' => $this->resolveMarketingAchievement($employeeId, $periodYear, $periodMonth),
            default => null, // fail-safe: unknown data_source → condition not met
        };
    }

    private function resolveAttendanceDaysPresent(int $employeeId, int $periodYear, int $periodMonth): ?float
    {
        $recap = EmployeeAttendanceMonthlyRecap::where('employee_id', $employeeId)
            ->where('recap_year', $periodYear)
            ->where('recap_month', $periodMonth)
            ->first();

        return $recap ? (float) $recap->days_present : null;
    }
}
