<?php

namespace App\Services\Kpi;

use App\Models\Employee;
use App\Models\EmployeeAttendanceMonthlyRecap;
use App\Models\EmployeeSessionAttendanceLog;
use App\Models\MarketingKpiSnapshot;
use App\Models\MarketingPerformance;
use App\Models\MemberSessionAssessment;

class KpiDataSourceService
{
    public const ALLOWED_SOURCES = [
        'attendance.total_present',
        'attendance.total_absent',
        'attendance.total_late',
        'attendance.total_late_minutes',
        'attendance.total_sick',
        'attendance.total_permission',
        'attendance.total_leave',
        'attendance.attendance_rate',
        'marketing_performance.classes_actual',
        'marketing_performance.prospective_count',
        'marketing_performance.fixed_count',
        'marketing_performance.omzet_actual',
        'marketing_performance.cash_actual',
        'marketing_kpi.mpi_score',
        'marketing_kpi.classes_percentage',
        'session_attendance.count_present',
        'session_assessment.count_filled',
        'session_assessment.avg_score',
    ];

    /**
     * Resolve actual_value from data_source_type for a given employee + period.
     * Returns ['value' => float|null, 'source_type' => string, 'source_id' => int|null] or null.
     */
    public function resolve(string $dataSourceType, Employee $employee, \DateTimeInterface $periodStart, \DateTimeInterface $periodEnd): ?array
    {
        return match ($dataSourceType) {
            'attendance.total_present' => $this->fromAttendanceRecap($employee, $periodStart, 'total_present'),
            'attendance.total_absent' => $this->fromAttendanceRecap($employee, $periodStart, 'total_absent'),
            'attendance.total_late' => $this->fromAttendanceRecap($employee, $periodStart, 'total_late'),
            'attendance.total_late_minutes' => $this->fromAttendanceRecap($employee, $periodStart, 'total_late_minutes'),
            'attendance.total_sick' => $this->fromAttendanceRecap($employee, $periodStart, 'total_sick'),
            'attendance.total_permission' => $this->fromAttendanceRecap($employee, $periodStart, 'total_permission'),
            'attendance.total_leave' => $this->fromAttendanceRecap($employee, $periodStart, 'total_leave'),
            'attendance.attendance_rate' => $this->attendanceRate($employee, $periodStart),
            'marketing_performance.classes_actual' => $this->fromMarketingPerformance($employee, $periodStart, 'classes_actual'),
            'marketing_performance.prospective_count' => $this->fromMarketingPerformance($employee, $periodStart, 'prospective_members_count'),
            'marketing_performance.fixed_count' => $this->fromMarketingPerformance($employee, $periodStart, 'fixed_members_count'),
            'marketing_performance.omzet_actual' => $this->fromMarketingPerformance($employee, $periodStart, 'registration_value_actual'),
            'marketing_performance.cash_actual' => $this->fromMarketingPerformance($employee, $periodStart, 'cash_collected_actual'),
            'marketing_kpi.mpi_score' => $this->fromMarketingKpiSnapshot($employee, $periodStart, 'mpi_score'),
            'marketing_kpi.classes_percentage' => $this->fromMarketingKpiSnapshot($employee, $periodStart, 'classes_percentage'),
            'session_attendance.count_present' => $this->sessionAttendanceCount($employee, $periodStart, $periodEnd),
            'session_assessment.count_filled' => $this->sessionAssessmentCount($employee, $periodStart, $periodEnd),
            'session_assessment.avg_score' => $this->sessionAssessmentAvg($employee, $periodStart, $periodEnd),
            default => null,
        };
    }

    private function fromAttendanceRecap(Employee $employee, \DateTimeInterface $periodStart, string $column): ?array
    {
        $recap = EmployeeAttendanceMonthlyRecap::where('employee_id', $employee->id)
            ->where('period_year', $periodStart->format('Y'))
            ->where('period_month', $periodStart->format('n'))
            ->first();

        if (! $recap) {
            return null;
        }

        return [
            'value' => (float) $recap->{$column},
            'source_type' => 'employee_attendance_monthly_recaps',
            'source_id' => $recap->id,
        ];
    }

    private function attendanceRate(Employee $employee, \DateTimeInterface $periodStart): ?array
    {
        $recap = EmployeeAttendanceMonthlyRecap::where('employee_id', $employee->id)
            ->where('period_year', $periodStart->format('Y'))
            ->where('period_month', $periodStart->format('n'))
            ->first();

        if (! $recap || $recap->total_scheduled_working_days === 0) {
            return null;
        }

        return [
            'value' => round($recap->total_present / $recap->total_scheduled_working_days * 100, 2),
            'source_type' => 'employee_attendance_monthly_recaps',
            'source_id' => $recap->id,
        ];
    }

    private function fromMarketingPerformance(Employee $employee, \DateTimeInterface $periodStart, string $column): ?array
    {
        $perf = MarketingPerformance::where('employee_id', $employee->id)
            ->where('period_year', $periodStart->format('Y'))
            ->where('period_month', $periodStart->format('n'))
            ->first();

        if (! $perf) {
            return null;
        }

        return [
            'value' => (float) $perf->{$column},
            'source_type' => 'marketing_performances',
            'source_id' => $perf->id,
        ];
    }

    private function fromMarketingKpiSnapshot(Employee $employee, \DateTimeInterface $periodStart, string $column): ?array
    {
        $snapshot = MarketingKpiSnapshot::where('employee_id', $employee->id)
            ->where('period_year', $periodStart->format('Y'))
            ->where('period_month', $periodStart->format('n'))
            ->latest()
            ->first();

        if (! $snapshot) {
            return null;
        }

        return [
            'value' => (float) $snapshot->{$column},
            'source_type' => 'marketing_kpi_snapshots',
            'source_id' => $snapshot->id,
        ];
    }

    private function sessionAttendanceCount(Employee $employee, \DateTimeInterface $start, \DateTimeInterface $end): ?array
    {
        $count = EmployeeSessionAttendanceLog::where('employee_id', $employee->id)
            ->where('status', 'present')
            ->whereBetween('created_at', [$start, $end])
            ->count();

        return [
            'value' => (float) $count,
            'source_type' => 'employee_session_attendance_logs',
            'source_id' => null,
        ];
    }

    private function sessionAssessmentCount(Employee $employee, \DateTimeInterface $start, \DateTimeInterface $end): ?array
    {
        $count = MemberSessionAssessment::where('employee_id', $employee->id)
            ->whereBetween('created_at', [$start, $end])
            ->count();

        return [
            'value' => (float) $count,
            'source_type' => 'member_session_assessments',
            'source_id' => null,
        ];
    }

    private function sessionAssessmentAvg(Employee $employee, \DateTimeInterface $start, \DateTimeInterface $end): ?array
    {
        $avg = MemberSessionAssessment::where('employee_id', $employee->id)
            ->whereBetween('created_at', [$start, $end])
            ->avg('score');

        if ($avg === null) {
            return null;
        }

        return [
            'value' => round((float) $avg, 2),
            'source_type' => 'member_session_assessments',
            'source_id' => null,
        ];
    }
}
