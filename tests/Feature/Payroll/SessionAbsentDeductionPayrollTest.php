<?php

namespace Tests\Feature\Payroll;

use App\Models\Branch;
use App\Models\ClassRoom;
use App\Models\ClassSchedule;
use App\Models\Employee;
use App\Models\EmployeeAttendanceMonthlyRecap;
use App\Models\EmployeeCompensation;
use App\Models\EmployeePayroll;
use App\Models\EmployeeSessionAttendanceLog;
use App\Models\EmployeeWorkAttendanceLog;
use App\Models\EmploymentStatus;
use App\Models\Holiday;
use App\Models\LeavePaySetting;
use App\Models\PayrollComponent;
use App\Models\PayrollPeriod;
use App\Models\Position;
use App\Models\Program;
use App\Models\SessionSchedule;
use App\Models\WorkScheduleAssignment;
use App\Models\WorkScheduleDay;
use App\Models\WorkScheduleRule;
use App\Services\Payroll\PayrollGenerationService;
use Carbon\Carbon;
use Tests\TestCase;

// Alpha sesi tutor: satu potongan per HARI (bukan per sesi) = gaji pokok / hari terjadwal, untuk tanggal yang punya sesi
// terjadwal tetapi tidak satu pun dihadiri. Kalender: September 2026, gaji pokok 2.600.000, 26 hari terjadwal -> 100.000/hari.
class SessionAbsentDeductionPayrollTest extends TestCase
{
    private Employee $tutor;

    private Position $position;

    private PayrollPeriod $period;

    private Branch $branch;

    private WorkScheduleRule $rule;

    protected function setUp(): void
    {
        parent::setUp();

        $this->travelTo(Carbon::create(2026, 10, 1, 9, 0));
        $this->branch = Branch::factory()->create();
        $this->position = Position::create(['position_name' => 'Tutor'.uniqid(), 'hierarchy_order' => 6]);
        // Jadwal Senin-Sabtu: membuat log kerja menghitung ulang rekap dari jadwal, jadi tutor harus punya jadwal
        $this->rule = WorkScheduleRule::factory()->create(['is_required' => true, 'is_active' => true]);
        foreach ([1, 2, 3, 4, 5, 6] as $day) {
            WorkScheduleDay::create(['work_schedule_rule_id' => $this->rule->id, 'day_of_week' => $day]);
        }
        $this->tutor = $this->employee();

        PayrollComponent::firstOrCreate(['component_code' => 'ABSENT_DEDUCTION'], ['component_name' => 'Potongan Alpha', 'component_type' => 'deduction', 'calculation_method' => 'manual', 'is_active' => true]);
        $this->period = PayrollPeriod::create(['period_year' => 2026, 'period_month' => 9, 'status' => 'finalized', 'confirmed_at' => now()]);
    }

    private function employee(float $salary = 2_600_000, array $recap = []): Employee
    {
        $employee = Employee::factory()->create(['branch_id' => $this->branch->id, 'is_active' => true]);
        EmploymentStatus::create(['employees_id' => $employee->id, 'position_id' => $this->position->id, 'type_employment' => 'part_time']);
        $base = PayrollComponent::firstOrCreate(['component_code' => 'BASE_SALARY'], ['component_name' => 'Gaji Pokok', 'component_type' => 'earning', 'calculation_method' => 'fixed', 'is_active' => true]);
        EmployeeCompensation::create(['employee_id' => $employee->id, 'payroll_component_id' => $base->id, 'value' => $salary]);
        WorkScheduleAssignment::create([
            'work_schedule_rule_id' => $this->rule->id, 'assignable_type' => Employee::class, 'assignable_id' => $employee->id,
            'effective_start_date' => '2026-01-01', 'is_active' => true,
        ]);
        EmployeeAttendanceMonthlyRecap::create(array_merge([
            'employee_id' => $employee->id, 'period_year' => 2026, 'period_month' => 9,
            'total_scheduled_working_days' => 26, 'total_effective_working_days' => 0, 'total_present' => 0,
        ], $recap));

        return $employee;
    }

    private function makeSession(string $date, ?Employee $tutor = null): SessionSchedule
    {
        $tutor ??= $this->tutor;
        $classRoom = ClassRoom::factory()->create(['program_id' => Program::factory()->create()->id, 'branch_id' => $this->branch->id, 'tutor_id' => $tutor->id]);
        $schedule = ClassSchedule::create(['class_id' => $classRoom->id, 'schedule_date' => $date, 'start_time' => '11:00', 'end_time' => '13:00', 'late_tolerance_minutes' => 15]);

        return SessionSchedule::create(['class_schedule_id' => $schedule->id, 'employee_id' => $tutor->id]);
    }

    private function attend(SessionSchedule $session, string $status = 'present'): void
    {
        EmployeeSessionAttendanceLog::create(['session_schedule_id' => $session->id, 'employee_id' => $session->employee_id, 'status' => $status, 'late_minutes' => 0]);
    }

    private function workLog(string $date, string $status): void
    {
        EmployeeWorkAttendanceLog::factory()->create(['employee_id' => $this->tutor->id, 'branch_id' => $this->branch->id, 'attendance_date' => $date, 'status' => $status]);
    }

    private function setting(string $type, bool $paid): void
    {
        LeavePaySetting::create(['position_id' => $this->position->id, 'leave_type' => $type, 'is_paid' => $paid]);
    }

    /** @return array{0: int, 1: float}|null [hari, jumlah] baris "Potongan alpha sesi" */
    private function sessionDeduction(?Employee $employee = null): ?array
    {
        app(PayrollGenerationService::class)->generate($this->period);
        $payroll = EmployeePayroll::where('payroll_period_id', $this->period->id)->where('employee_id', ($employee ?? $this->tutor)->id)->firstOrFail();
        $item = $payroll->items()->where('description', 'like', 'Potongan alpha sesi%')->first();

        return $item ? [(int) $item->quantity, (float) $item->total_amount] : null;
    }

    public function test_hari_tanpa_sesi_yang_dihadiri_dipotong_per_hari(): void
    {
        foreach (['2026-09-02', '2026-09-09', '2026-09-16'] as $date) {
            $this->makeSession($date);
        }

        $this->assertSame([3, 300_000.0], $this->sessionDeduction());
    }

    public function test_dua_sesi_di_hari_yang_sama_tidak_dihadiri_dihitung_satu_hari(): void
    {
        $this->makeSession('2026-09-02');
        $this->makeSession('2026-09-02');

        $this->assertSame([1, 100_000.0], $this->sessionDeduction());
    }

    public function test_hadir_sebagian_di_hari_itu_bukan_alpha(): void
    {
        $this->attend($this->makeSession('2026-09-02'));
        $this->makeSession('2026-09-02');   // sesi kedua di hari yang sama tidak dihadiri

        $this->assertNull($this->sessionDeduction());
    }

    public function test_sudah_check_in_tetapi_belum_difinalkan_tetap_dianggap_hadir(): void
    {
        $this->attend($this->makeSession('2026-09-02'), 'checked_in');

        $this->assertNull($this->sessionDeduction());
    }

    public function test_hari_sakit_izin_cuti_dan_libur_pada_log_kerja_dikecualikan(): void
    {
        foreach (['2026-09-02' => 'sick', '2026-09-03' => 'permission', '2026-09-04' => 'leave', '2026-09-05' => 'holiday'] as $date => $status) {
            $this->makeSession($date);
            $this->workLog($date, $status);
        }
        $this->makeSession('2026-09-07');   // satu-satunya hari tanpa alasan

        $this->assertSame([1, 100_000.0], $this->sessionDeduction());
    }

    public function test_libur_nasional_dikecualikan(): void
    {
        $this->makeSession('2026-09-10');
        Holiday::create(['holiday_name' => 'Libur', 'holiday_start_date' => '2026-09-10', 'holiday_end_date' => '2026-09-10']);

        $this->assertNull($this->sessionDeduction());
    }

    public function test_tanggal_yang_belum_lewat_tidak_dipotong(): void
    {
        $this->travelTo(Carbon::create(2026, 9, 15, 9, 0));
        $this->makeSession('2026-09-10');
        $this->makeSession('2026-09-15');   // hari ini
        $this->makeSession('2026-09-20');   // belum tiba

        $this->assertSame([1, 100_000.0], $this->sessionDeduction());
    }

    public function test_sesi_bulan_dan_tahun_lain_serta_milik_tutor_lain_tidak_dihitung(): void
    {
        $this->makeSession('2026-08-31');
        $this->makeSession('2026-10-01');
        $this->makeSession('2025-09-02');
        $this->makeSession('2026-09-02', $this->employee());

        $this->assertNull($this->sessionDeduction());
    }

    public function test_hari_yang_sudah_alpha_kerja_tidak_dipotong_dua_kali(): void
    {
        $tutor = $this->employee(recap: ['total_absent' => 1]);
        $this->tutor = $tutor;
        $this->makeSession('2026-09-02');
        $this->workLog('2026-09-02', 'absent');   // sudah dihitung sebagai alpha kerja
        $this->makeSession('2026-09-03');             // alpha sesi saja

        app(PayrollGenerationService::class)->generate($this->period);
        $payroll = EmployeePayroll::where('payroll_period_id', $this->period->id)->where('employee_id', $tutor->id)->firstOrFail();

        $this->assertEquals(1, (int) $payroll->items()->where('description', 'like', 'Potongan alpha:%')->value('quantity'));
        $this->assertEquals(1, (int) $payroll->items()->where('description', 'like', 'Potongan alpha sesi%')->value('quantity'));
    }

    public function test_bila_alpha_kerja_dibayar_hari_itu_tetap_dihitung_alpha_sesi(): void
    {
        $this->setting('absent', true);                    // alpha kerja tidak dipotong
        $this->makeSession('2026-09-02');
        $this->workLog('2026-09-02', 'absent');

        $this->assertSame([1, 100_000.0], $this->sessionDeduction());
    }

    public function test_pengaturan_posisi_alpha_sesi_dibayar_menonaktifkan_potongan(): void
    {
        $this->setting('absent_session', true);
        $this->makeSession('2026-09-02');

        $this->assertNull($this->sessionDeduction());
    }

    public function test_total_potongan_tidak_melebihi_hari_terjadwal_dan_alpha_kerja_didahulukan(): void
    {
        $tutor = $this->employee(recap: ['total_absent' => 20]);
        $this->tutor = $tutor;
        foreach (range(1, 12) as $day) {
            $this->makeSession(sprintf('2026-09-%02d', $day));
        }

        $this->assertSame([6, 600_000.0], $this->sessionDeduction());   // 26 - 20 alpha kerja = 6 hari tersisa
    }

    public function test_tutor_tanpa_gaji_pokok_tidak_dipotong(): void
    {
        $tutor = $this->employee(salary: 0);
        $this->makeSession('2026-09-02', $tutor);

        $this->assertNull($this->sessionDeduction($tutor));
    }
}
