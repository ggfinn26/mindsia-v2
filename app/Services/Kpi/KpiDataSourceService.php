<?php

namespace App\Services\Kpi;

use App\Models\Employee;
use App\Models\EmployeeAttendanceMonthlyRecap;
use App\Models\EmployeeSessionAttendanceLog;
use App\Models\EmployeeWorkAttendanceLog;
use App\Models\MarketingKpiSnapshot;
use App\Models\MarketingPerformance;
use App\Models\MemberSessionAssessment;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class KpiDataSourceService
{
    public const SOURCE_LABELS = [
        'attendance.total_present' => 'Absensi: Total Hari Hadir',
        'attendance.total_absent' => 'Absensi: Total Hari Tidak Hadir',
        'attendance.total_late' => 'Absensi: Total Keterlambatan',
        'attendance.total_late_minutes' => 'Absensi: Total Menit Terlambat',
        'attendance.total_sick' => 'Absensi: Total Hari Sakit',
        'attendance.total_permission' => 'Absensi: Total Hari Izin',
        'attendance.total_leave' => 'Absensi: Total Hari Cuti',
        'attendance.attendance_rate' => 'Absensi: Tingkat Kehadiran (%)',
        'attendance.check_out_compliance_rate' => 'Absensi: Kepatuhan Absen Keluar (%)',
        'marketing_performance.classes_actual' => 'Marketing: Jumlah Kelas Aktual',
        'marketing_performance.prospective_count' => 'Marketing: Jumlah Calon Member',
        'marketing_performance.fixed_count' => 'Marketing: Jumlah Member Tetap',
        'marketing_performance.omzet_actual' => 'Marketing: Omzet Aktual (Rp)',
        'marketing_performance.cash_actual' => 'Marketing: Cash Terkumpul (Rp)',
        'marketing_kpi.mpi_score' => 'MPI: Skor MPI',
        'marketing_kpi.classes_percentage' => 'MPI: Persentase Kelas (%)',
        'session_attendance.count_present' => 'Sesi Mengajar: Jumlah Sesi Hadir',
        'session_assessment.count_filled' => 'Penilaian Sesi: Jumlah Penilaian Diisi',
        'session_assessment.avg_score' => 'Penilaian Sesi: Rata-rata Skor',
    ];

    public const ALLOWED_SOURCES = [
        'attendance.total_present',
        'attendance.total_absent',
        'attendance.total_late',
        'attendance.total_late_minutes',
        'attendance.total_sick',
        'attendance.total_permission',
        'attendance.total_leave',
        'attendance.attendance_rate',
        'attendance.check_out_compliance_rate',
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
            'attendance.total_present' => $this->fromAttendanceRecap($employee, $periodStart, $periodEnd, 'total_present'),
            'attendance.total_absent' => $this->fromAttendanceRecap($employee, $periodStart, $periodEnd, 'total_absent'),
            'attendance.total_late' => $this->fromAttendanceRecap($employee, $periodStart, $periodEnd, 'total_late'),
            'attendance.total_late_minutes' => $this->fromAttendanceRecap($employee, $periodStart, $periodEnd, 'total_late_minutes'),
            'attendance.total_sick' => $this->fromAttendanceRecap($employee, $periodStart, $periodEnd, 'total_sick'),
            'attendance.total_permission' => $this->fromAttendanceRecap($employee, $periodStart, $periodEnd, 'total_permission'),
            'attendance.total_leave' => $this->fromAttendanceRecap($employee, $periodStart, $periodEnd, 'total_leave'),
            'attendance.attendance_rate' => $this->attendanceRate($employee, $periodStart, $periodEnd),
            'attendance.check_out_compliance_rate' => $this->checkOutComplianceRate($employee, $periodStart, $periodEnd),
            'marketing_performance.classes_actual' => $this->fromMarketingPerformance($employee, $periodStart, $periodEnd, 'classes_actual'),
            'marketing_performance.prospective_count' => $this->fromMarketingPerformance($employee, $periodStart, $periodEnd, 'prospective_members_count'),
            'marketing_performance.fixed_count' => $this->fromMarketingPerformance($employee, $periodStart, $periodEnd, 'fixed_members_count'),
            'marketing_performance.omzet_actual' => $this->fromMarketingPerformance($employee, $periodStart, $periodEnd, 'registration_value_actual'),
            'marketing_performance.cash_actual' => $this->fromMarketingPerformance($employee, $periodStart, $periodEnd, 'cash_collected_actual'),
            'marketing_kpi.mpi_score' => $this->fromMarketingKpiSnapshot($employee, $periodStart, $periodEnd, 'mpi_score'),
            'marketing_kpi.classes_percentage' => $this->fromMarketingKpiSnapshot($employee, $periodStart, $periodEnd, 'classes_percentage'),
            'session_attendance.count_present' => $this->sessionAttendanceCount($employee, $periodStart, $periodEnd),
            'session_assessment.count_filled' => $this->sessionAssessmentCount($employee, $periodStart, $periodEnd),
            'session_assessment.avg_score' => $this->sessionAssessmentAvg($employee, $periodStart, $periodEnd),
            default => null,
        };
    }

    /**
     * Rekap bulanan untuk SEMUA bulan dalam periode. Kalau ada bulan yang belum punya rekap, kembalikan null
     * (bukan jumlah parsial) agar nilai yang salah tidak lolos diam-diam.
     *
     * @return Collection<int, EmployeeAttendanceMonthlyRecap>|null
     */
    private function recapsInPeriod(Employee $employee, \DateTimeInterface $start, \DateTimeInterface $end): ?Collection
    {
        $from = Carbon::instance($start)->startOfMonth();
        $to = Carbon::instance($end)->startOfMonth();
        $months = (($to->year - $from->year) * 12) + ($to->month - $from->month) + 1;

        $recaps = EmployeeAttendanceMonthlyRecap::where('employee_id', $employee->id)
            ->whereRaw('(period_year * 12 + period_month) between ? and ?', [
                $from->year * 12 + $from->month,
                $to->year * 12 + $to->month,
            ])
            ->get();

        return $months > 0 && $recaps->count() === $months ? $recaps : null;
    }

    private function fromAttendanceRecap(Employee $employee, \DateTimeInterface $start, \DateTimeInterface $end, string $column): ?array
    {
        $recaps = $this->recapsInPeriod($employee, $start, $end);

        if (! $recaps) {
            return null;
        }

        return [
            'value' => (float) $recaps->sum($column),
            'source_type' => 'employee_attendance_monthly_recaps',
            'source_id' => $recaps->count() === 1 ? $recaps->first()->id : null,
        ];
    }

    /** Rekap bulan tunggal untuk sumber yang memang bulanan; periode multi-bulan tidak didukung (null). */
    private function singleMonth(\DateTimeInterface $start, \DateTimeInterface $end): ?array
    {
        $from = Carbon::instance($start);
        $to = Carbon::instance($end);

        return $from->isSameMonth($to) ? [$from->year, $from->month] : null;
    }

    /**
     * Persentase hari hadir yang disertai absen keluar: (hadir - lupa absen keluar) / hadir x 100.
     * Dibaca langsung dari log agar akurat untuk periode apa pun. Bobot dan target diatur client di template KPI.
     */
    private function checkOutComplianceRate(Employee $employee, \DateTimeInterface $start, \DateTimeInterface $end): ?array
    {
        $present = EmployeeWorkAttendanceLog::where('employee_id', $employee->id)
            ->where('status', 'present')
            ->whereBetween('attendance_date', [$start->format('Y-m-d'), $end->format('Y-m-d')]);

        $total = (clone $present)->count();

        if ($total === 0) {
            return null;
        }

        $missed = (clone $present)->where('is_check_out_missed', true)->count();

        return [
            'value' => round(($total - $missed) / $total * 100, 2),
            'source_type' => 'employee_work_attendance_logs',
            'source_id' => null,
        ];
    }

    private function attendanceRate(Employee $employee, \DateTimeInterface $start, \DateTimeInterface $end): ?array
    {
        $recaps = $this->recapsInPeriod($employee, $start, $end);
        $scheduled = (int) $recaps?->sum('total_scheduled_working_days');

        if (! $recaps || $scheduled <= 0) {
            return null;
        }

        return [
            'value' => min(100.0, round($recaps->sum('total_present') / $scheduled * 100, 2)),
            'source_type' => 'employee_attendance_monthly_recaps',
            'source_id' => $recaps->count() === 1 ? $recaps->first()->id : null,
        ];
    }

    private function fromMarketingPerformance(Employee $employee, \DateTimeInterface $start, \DateTimeInterface $end, string $column): ?array
    {
        $month = $this->singleMonth($start, $end);

        $perf = $month ? MarketingPerformance::where('employee_id', $employee->id)
            ->where('period_year', $month[0])
            ->where('period_month', $month[1])
            ->first() : null;

        if (! $perf) {
            return null;
        }

        return [
            'value' => (float) $perf->{$column},
            'source_type' => 'marketing_performances',
            'source_id' => $perf->id,
        ];
    }

    private function fromMarketingKpiSnapshot(Employee $employee, \DateTimeInterface $start, \DateTimeInterface $end, string $column): ?array
    {
        $month = $this->singleMonth($start, $end);

        $snapshot = $month ? MarketingKpiSnapshot::where('employee_id', $employee->id)
            ->where('period_year', $month[0])
            ->where('period_month', $month[1])
            ->latest()
            ->first() : null;

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
            ->whereBetween('created_at', [Carbon::instance($start)->startOfDay(), Carbon::instance($end)->endOfDay()])
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
            ->whereBetween('created_at', [Carbon::instance($start)->startOfDay(), Carbon::instance($end)->endOfDay()])
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
            ->whereBetween('created_at', [Carbon::instance($start)->startOfDay(), Carbon::instance($end)->endOfDay()])
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
