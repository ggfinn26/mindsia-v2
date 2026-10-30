<?php

namespace Tests\Feature\Payroll;

use App\Models\Employee;
use App\Models\EmployeeAttendanceMonthlyRecap;
use App\Models\EmployeeAttendanceSetting;
use App\Models\EmployeeCompensation;
use App\Models\EmployeePayroll;
use App\Models\EmploymentStatus;
use App\Models\PayrollComponent;
use App\Models\PayrollPeriod;
use App\Models\Position;
use App\Services\Payroll\PayrollGenerationService;
use Tests\TestCase;

// Siapa yang masuk payroll dan data rekap bulan mana yang dipakai.
class PayrollEligibilityTest extends TestCase
{
    private static int $seq = 0;

    private PayrollPeriod $period;

    private PayrollComponent $baseSalary;

    protected function setUp(): void
    {
        parent::setUp();

        $seq = ++self::$seq;
        $this->period = PayrollPeriod::create([
            'period_year' => 2040 + intdiv($seq, 12),
            'period_month' => ($seq % 12) + 1,
            'status' => 'finalized',
            'confirmed_at' => now(),
        ]);

        $this->baseSalary = PayrollComponent::firstOrCreate(
            ['component_code' => 'BASE_SALARY'],
            ['component_name' => 'Gaji Pokok', 'component_type' => 'earning', 'calculation_method' => 'fixed', 'is_active' => true],
        );
    }

    private function employee(float $salary = 10_000_000): Employee
    {
        $employee = Employee::factory()->create(['is_active' => true]);
        $position = Position::create(['position_name' => 'Pos'.uniqid(), 'hierarchy_order' => 5]);
        EmploymentStatus::create(['employees_id' => $employee->id, 'position_id' => $position->id, 'type_employment' => 'permanent']);
        EmployeeCompensation::create(['employee_id' => $employee->id, 'payroll_component_id' => $this->baseSalary->id, 'value' => $salary]);

        return $employee;
    }

    private function recap(Employee $employee, int $year, int $month, array $overrides = []): void
    {
        EmployeeAttendanceMonthlyRecap::create(array_merge([
            'employee_id' => $employee->id,
            'period_year' => $year,
            'period_month' => $month,
            'total_scheduled_working_days' => 26,
            'total_effective_working_days' => 20,
            'total_present' => 20,
        ], $overrides));
    }

    private function generate(): void
    {
        app(PayrollGenerationService::class)->generate($this->period);
    }

    private function payroll(Employee $employee): ?EmployeePayroll
    {
        return EmployeePayroll::where('payroll_period_id', $this->period->id)->where('employee_id', $employee->id)->first();
    }

    public function test_pegawai_tidak_wajib_absen_tetap_digaji_walau_tidak_pernah_punya_log(): void
    {
        // Board/C-level: is_required = false, tidak pernah check-in, jadi tidak pernah punya rekap
        $employee = $this->employee(25_000_000);
        EmployeeAttendanceSetting::create(['employee_id' => $employee->id, 'is_required' => false, 'is_session_required' => false, 'attendance_scope' => 'anywhere']);

        $this->generate();

        $payroll = $this->payroll($employee);
        $this->assertNotNull($payroll, 'Pegawai yang tidak wajib absen tidak boleh terlewat dari payroll.');
        $this->assertEquals(25_000_000.0, (float) $payroll->net_amount);
    }

    public function test_pegawai_wajib_absen_tanpa_rekap_tetap_dilewati(): void
    {
        $employee = $this->employee();

        $this->generate();

        $this->assertNull($this->payroll($employee));
    }

    public function test_hanya_rekap_bulan_periode_yang_dipakai(): void
    {
        $employee = $this->employee();
        $year = $this->period->period_year;
        $month = $this->period->period_month;

        $prev = \Carbon\Carbon::create($year, $month, 1)->subMonth();
        $this->recap($employee, $prev->year, $prev->month, ['total_present' => 5, 'total_effective_working_days' => 5]);
        $this->recap($employee, $year - 1, $month, ['total_present' => 7, 'total_effective_working_days' => 7]);
        $this->recap($employee, $year, $month, ['total_present' => 18, 'total_effective_working_days' => 18]);

        $this->generate();

        $this->assertSame(18, (int) $this->payroll($employee)->days_present);
    }

    public function test_hanya_rekap_bulan_lain_berarti_tidak_digaji_bukan_memakai_bulan_lain(): void
    {
        $employee = $this->employee();
        $prev = \Carbon\Carbon::create($this->period->period_year, $this->period->period_month, 1)->subMonth();
        $this->recap($employee, $prev->year, $prev->month);

        $this->generate();

        $this->assertNull($this->payroll($employee));
    }

    public function test_generate_dua_kali_tidak_menggandakan_payroll(): void
    {
        $employee = $this->employee();
        $this->recap($employee, $this->period->period_year, $this->period->period_month);

        $this->generate();
        $this->generate();

        $this->assertSame(1, EmployeePayroll::where('payroll_period_id', $this->period->id)->where('employee_id', $employee->id)->count());
    }

    public function test_snapshot_payroll_mengikuti_rekap(): void
    {
        $employee = $this->employee();
        $this->recap($employee, $this->period->period_year, $this->period->period_month, [
            'total_scheduled_working_days' => 25, 'total_present' => 17, 'total_absent' => 3, 'total_sick' => 2,
            'total_permission' => 1, 'total_leave' => 2, 'total_holiday' => 1, 'total_late' => 4, 'total_effective_working_days' => 17,
        ]);

        $this->generate();

        $payroll = $this->payroll($employee);
        $this->assertSame([25, 17, 3, 2, 1, 2, 1, 4], [
            (int) $payroll->scheduled_working_days, (int) $payroll->days_present, (int) $payroll->days_absent, (int) $payroll->days_sick,
            (int) $payroll->days_permission, (int) $payroll->days_leave, (int) $payroll->days_holiday, (int) $payroll->days_late,
        ]);
    }
}
