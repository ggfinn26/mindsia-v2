<?php

namespace Tests\Feature\Kpi;

use App\Models\Employee;
use App\Models\EmployeeWorkAttendanceLog;
use App\Services\Kpi\KpiDataSourceService;
use Carbon\Carbon;
use Tests\TestCase;

// Sumber data KPI attendance.check_out_compliance_rate: hari hadir yang lupa absen keluar menurunkan skor.
// Bobot dan target diatur client di template KPI; tes ini hanya menjaga nilai yang ditarik.
class KpiCheckOutComplianceTest extends TestCase
{
    private function logPresent(Employee $employee, string $date, bool $missed = false): void
    {
        EmployeeWorkAttendanceLog::factory()->present()->create([
            'employee_id' => $employee->id,
            'branch_id' => $employee->branch_id,
            'attendance_date' => $date,
            'is_check_out_missed' => $missed,
        ]);
    }

    private function resolve(Employee $employee, string $start = '2026-09-01', string $end = '2026-09-30'): ?array
    {
        return app(KpiDataSourceService::class)->resolve(
            'attendance.check_out_compliance_rate', $employee, Carbon::parse($start), Carbon::parse($end),
        );
    }

    public function test_sumber_data_terdaftar_dan_bisa_dipilih_di_template(): void
    {
        $this->assertContains('attendance.check_out_compliance_rate', KpiDataSourceService::ALLOWED_SOURCES);
        $this->assertArrayHasKey('attendance.check_out_compliance_rate', KpiDataSourceService::SOURCE_LABELS);
    }

    public function test_lupa_absen_keluar_menurunkan_persentase_kepatuhan(): void
    {
        $employee = Employee::factory()->create();

        foreach (range(1, 8) as $day) {
            $this->logPresent($employee, sprintf('2026-09-%02d', $day));
        }
        $this->logPresent($employee, '2026-09-09', missed: true);
        $this->logPresent($employee, '2026-09-10', missed: true);

        $result = $this->resolve($employee);

        $this->assertSame(80.0, $result['value']);
        $this->assertSame('employee_work_attendance_logs', $result['source_type']);
    }

    public function test_semua_absen_keluar_lengkap_menghasilkan_100(): void
    {
        $employee = Employee::factory()->create();
        $this->logPresent($employee, '2026-09-01');

        $this->assertSame(100.0, $this->resolve($employee)['value']);
    }

    public function test_tanpa_hari_hadir_di_periode_menghasilkan_null(): void
    {
        $employee = Employee::factory()->create();
        $this->logPresent($employee, '2026-08-31', missed: true);

        $this->assertNull($this->resolve($employee));
    }

    public function test_hanya_log_dalam_periode_yang_dihitung(): void
    {
        $employee = Employee::factory()->create();
        $this->logPresent($employee, '2026-09-01');
        $this->logPresent($employee, '2026-10-01', missed: true);

        $this->assertSame(100.0, $this->resolve($employee)['value']);
    }
}
