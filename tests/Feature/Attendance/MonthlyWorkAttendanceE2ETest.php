<?php

namespace Tests\Feature\Attendance;

use App\Models\Branch;
use App\Models\Employee;
use App\Models\EmployeeAttendanceMonthlyRecap;
use App\Models\EmployeeCompensation;
use App\Models\EmployeeKpiEvaluation;
use App\Models\EmployeeLeaveRequest;
use App\Models\EmployeePayroll;
use App\Models\EmployeeWorkAttendanceLog;
use App\Models\EmploymentStatus;
use App\Models\Holiday;
use App\Models\KpiEvaluatorAssignment;
use App\Models\KpiGradeRule;
use App\Models\KpiTemplate;
use App\Models\KpiTemplateIndicator;
use App\Models\LeavePaySetting;
use App\Models\PayrollComponent;
use App\Models\PayrollPeriod;
use App\Models\Position;
use App\Models\User;
use App\Models\WorkScheduleAssignment;
use App\Models\WorkScheduleDay;
use App\Models\WorkScheduleRule;
use App\Services\Payroll\PayrollGenerationService;
use App\Services\TelegramStorageService;
use Carbon\Carbon;
use Mockery\MockInterface;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

// E2E satu bulan (September 2026) lewat route HTTP asli, dari absensi harian sampai payroll:
//   jadwal Senin-Sabtu 07:30-16:00 -> check-in/out -> lupa check-out -> sakit disetujui atasan -> libur nasional
//   -> rekap bulanan -> evaluasi KPI (otomatis + manual) -> payroll dengan potongan sakit tidak berbayar.
// Kalender: 1 Sep = Selasa. Hari Senin-Sabtu = 26, libur Kamis 17 Sep -> 25 hari terjadwal.
class MonthlyWorkAttendanceE2ETest extends TestCase
{
    private const LAT = -6.2088;

    private const LNG = 106.8456;

    private Branch $branch;

    private Employee $employee;

    private User $user;

    private User $approver;

    private Position $position;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mock(TelegramStorageService::class, function (MockInterface $mock) {
            $mock->shouldReceive('uploadFile')->andReturn(['file_id' => 'fake']);
            $mock->shouldReceive('uploadPhoto')->andReturn('fake');
        });

        $this->branch = Branch::factory()->create(['latitude' => self::LAT, 'longitude' => self::LNG, 'radius_meters' => 500]);
        $this->employee = Employee::factory()->create(['branch_id' => $this->branch->id, 'is_active' => true, 'join_date' => '2024-01-01']);
        $this->user = User::factory()->create(['employee_id' => $this->employee->id]);

        $this->position = Position::create(['position_name' => 'Staff'.uniqid(), 'hierarchy_order' => 5]);
        EmploymentStatus::create(['employees_id' => $this->employee->id, 'position_id' => $this->position->id, 'type_employment' => 'permanent']);

        // Atasan (hierarki 1) dengan hak review cuti
        $boss = Employee::factory()->create(['branch_id' => $this->branch->id, 'is_active' => false]);
        $bossPosition = Position::create(['position_name' => 'Atasan'.uniqid(), 'hierarchy_order' => 1]);
        EmploymentStatus::create(['employees_id' => $boss->id, 'position_id' => $bossPosition->id, 'type_employment' => 'permanent']);
        $this->approver = User::factory()->create(['employee_id' => $boss->id]);
        $this->approver->givePermissionTo(Permission::firstOrCreate(['name' => 'attendance.leave.review', 'guard_name' => 'web']));

        // Jadwal Reguler Senin-Sabtu 07:30-16:00, toleransi telat 15 menit
        $rule = WorkScheduleRule::factory()->create([
            'start_time' => '07:30', 'end_time' => '16:00', 'late_tolerance_minutes' => 15,
            'early_leave_tolerance_minutes' => 0, 'is_required' => true, 'is_active' => true,
        ]);
        foreach ([1, 2, 3, 4, 5, 6] as $day) {
            WorkScheduleDay::create(['work_schedule_rule_id' => $rule->id, 'day_of_week' => $day]);
        }
        WorkScheduleAssignment::create([
            'work_schedule_rule_id' => $rule->id, 'assignable_type' => Employee::class, 'assignable_id' => $this->employee->id,
            'effective_start_date' => '2026-01-01', 'is_active' => true,
        ]);

        Holiday::create(['holiday_name' => 'Libur Nasional', 'holiday_start_date' => '2026-09-17', 'holiday_end_date' => '2026-09-17']);
    }

    private function at(int $month, int $day, int $hour, int $minute): void
    {
        $this->travelTo(Carbon::create(2026, $month, $day, $hour, $minute));
    }

    private function checkIn(int $day, int $hour, int $minute, float $lat = self::LAT, float $lng = self::LNG)
    {
        $this->at(9, $day, $hour, $minute);

        return $this->actingAs($this->user)->post(route('work-attendance.check-in'), ['check_in_latitude' => $lat, 'check_in_longitude' => $lng]);
    }

    private function checkOut(int $day, int $hour, int $minute, array $data = ['check_out_latitude' => self::LAT, 'check_out_longitude' => self::LNG])
    {
        $this->at(9, $day, $hour, $minute);

        return $this->actingAs($this->user)->post(route('work-attendance.check-out'), $data);
    }

    private function log(string $date): ?EmployeeWorkAttendanceLog
    {
        return EmployeeWorkAttendanceLog::where('employee_id', $this->employee->id)->whereDate('attendance_date', $date)->first();
    }

    private function recap(): EmployeeAttendanceMonthlyRecap
    {
        return EmployeeAttendanceMonthlyRecap::where('employee_id', $this->employee->id)->where('period_year', 2026)->where('period_month', 9)->firstOrFail();
    }

    /** Menjalankan seluruh September; mengembalikan setelah posisi waktu di 1 Oktober. */
    private function runSeptember(): void
    {
        // Data bulan/tahun lain yang tidak boleh mencemari angka September
        EmployeeWorkAttendanceLog::factory()->present()->create(['employee_id' => $this->employee->id, 'branch_id' => $this->branch->id, 'attendance_date' => '2026-08-31', 'is_check_out_missed' => true]);
        EmployeeWorkAttendanceLog::factory()->present()->create(['employee_id' => $this->employee->id, 'branch_id' => $this->branch->id, 'attendance_date' => '2025-09-02', 'is_check_out_missed' => true]);
        EmployeeWorkAttendanceLog::factory()->present()->create(['employee_id' => $this->employee->id, 'branch_id' => $this->branch->id, 'attendance_date' => '2026-10-01']);

        // Selasa 1: tepat waktu, lengkap
        $this->checkIn(1, 7, 30)->assertSessionHasNoErrors();
        $this->checkOut(1, 16, 0)->assertSessionHasNoErrors();
        // Rabu 2: telat 10 menit, check-out tanpa lokasi (GPS ditolak)
        $this->checkIn(2, 7, 55)->assertSessionHasNoErrors();
        $this->checkOut(2, 16, 5, [])->assertSessionHasNoErrors();
        // Kamis 3: check-in, lupa check-out
        $this->checkIn(3, 7, 30)->assertSessionHasNoErrors();
        // Jumat 4 00:05: scheduler memfinalkan hari kemarin; lalu check-in dari lokasi jauh ditolak, dari kantor diterima
        $this->at(9, 4, 0, 5);
        $this->artisan('attendance:finalize-missed-checkout')->assertSuccessful();
        $this->checkIn(4, 7, 35, -7.2575, 112.7521)->assertSessionHasErrors('check_in_latitude');
        $this->checkIn(4, 7, 40)->assertSessionHasNoErrors();
        $this->checkOut(4, 16, 0)->assertSessionHasNoErrors();
        // Sabtu 5: hari kerja
        $this->checkIn(5, 7, 40)->assertSessionHasNoErrors();
        $this->checkOut(5, 16, 0)->assertSessionHasNoErrors();
        // Minggu 6: tidak ada jadwal
        $this->checkIn(6, 7, 40)->assertSessionHasErrors('schedule');
        // Senin 7: ajukan sakit Selasa-Rabu, atasan menyetujui
        $this->at(9, 7, 9, 0);
        $this->actingAs($this->user)->post(route('leave-requests.store'), [
            'leave_type' => 'sick', 'start_date' => '2026-09-08', 'end_date' => '2026-09-09', 'reason' => 'Demam',
        ])->assertSessionHasNoErrors();
        $leave = EmployeeLeaveRequest::where('employee_id', $this->employee->id)->firstOrFail();
        $this->actingAs($this->approver)->post(route('leave-requests.approve', $leave))->assertSessionHas('success');
        // Selasa 8: sedang sakit, check-in ditolak
        $this->checkIn(8, 7, 40)->assertSessionHasErrors('check_in');
        // Kamis 17: libur nasional
        $this->checkIn(17, 7, 40)->assertSessionHasErrors('date');

        $this->at(10, 1, 9, 0);
    }

    public function test_absensi_harian_menghasilkan_log_dan_rekap_yang_benar(): void
    {
        $this->runSeptember();

        $this->assertSame(['present', 0, false], [$this->log('2026-09-01')->status, $this->log('2026-09-01')->late_minutes, $this->log('2026-09-01')->is_check_out_missed]);
        $this->assertSame(['present', 10], [$this->log('2026-09-02')->status, $this->log('2026-09-02')->late_minutes]);
        $this->assertNull($this->log('2026-09-02')->check_out_latitude);

        $missed = $this->log('2026-09-03');
        $this->assertSame('present', $missed->status);
        $this->assertTrue($missed->is_check_out_missed);
        $this->assertNull($missed->check_out);

        $this->assertSame('present', $this->log('2026-09-04')->status);
        $this->assertSame('present', $this->log('2026-09-05')->status);
        $this->assertNull($this->log('2026-09-06'), 'Minggu tanpa jadwal tidak boleh punya log.');
        $this->assertSame('sick', $this->log('2026-09-08')->status);
        $this->assertSame('sick', $this->log('2026-09-09')->status);
        $this->assertNull($this->log('2026-09-17'), 'Libur nasional tidak boleh punya log.');

        $recap = $this->recap();
        $this->assertSame(25, (int) $recap->total_scheduled_working_days);
        $this->assertSame(5, (int) $recap->total_present);
        $this->assertSame(0, (int) $recap->total_checked_in);
        $this->assertSame(2, (int) $recap->total_sick);
        $this->assertSame(1, (int) $recap->total_late);
        $this->assertSame(10, (int) $recap->total_late_minutes);
        $this->assertSame(0, (int) $recap->total_absent);
    }

    public function test_kpi_otomatis_langsung_terhitung_lalu_dikunci_dan_difinalisasi(): void
    {
        $this->runSeptember();

        $evaluator = Employee::factory()->create(['is_active' => false]);
        $evaluatorUser = User::factory()->create(['employee_id' => $evaluator->id]);
        $evaluatorUser->assignRole('CEO');

        $template = KpiTemplate::create(['template_code' => 'TPL-'.uniqid(), 'template_name' => 'Disiplin', 'is_active' => true, 'created_by_employee_id' => $evaluator->id]);
        foreach ([['attendance.attendance_rate', 60], ['attendance.check_out_compliance_rate', 20], [null, 20]] as $i => [$source, $weight]) {
            KpiTemplateIndicator::create([
                'kpi_template_id' => $template->id, 'indicator_code' => 'IND-'.($i + 1), 'indicator_name' => 'Indikator '.($i + 1),
                'target_value' => 100, 'weight' => $weight, 'sequence_number' => $i + 1, 'data_source_type' => $source, 'is_active' => true,
            ]);
        }
        KpiEvaluatorAssignment::create([
            'evaluator_employee_id' => $evaluator->id, 'evaluatee_employee_id' => $this->employee->id, 'kpi_template_id' => $template->id,
            'is_active' => true, 'effective_start_date' => '2026-01-01', 'created_by_employee_id' => $evaluator->id,
        ]);
        KpiGradeRule::create(['grade' => 'D', 'minimum_score' => 0, 'maximum_score' => 50]);

        $this->actingAs($evaluatorUser)->post('/kpi/evaluations', [
            'employee_id' => $this->employee->id, 'kpi_template_id' => $template->id, 'period_type' => 'monthly',
            'period_start_date' => '2026-09-01', 'period_end_date' => '2026-09-30',
        ])->assertSessionHasNoErrors();

        $evaluation = EmployeeKpiEvaluation::where('employee_id', $this->employee->id)->firstOrFail();
        [$rate, $compliance, $manual] = $evaluation->items()->orderBy('id')->get()->all();

        // Kehadiran 5/25 = 20% -> skor 12; kepatuhan absen keluar 4/5 = 80% -> skor 16; manual belum diisi
        $this->assertEquals([20.0, 20.0, 12.0], [(float) $rate->actual_value, (float) $rate->achievement_percentage, (float) $rate->score]);
        $this->assertEquals([80.0, 80.0, 16.0], [(float) $compliance->actual_value, (float) $compliance->achievement_percentage, (float) $compliance->score]);
        $this->assertNull($manual->actual_value);
        $this->assertEquals(0.0, (float) $manual->score);

        // Nilai otomatis tidak bisa ditimpa
        $this->actingAs($evaluatorUser)->put("/kpi/evaluations/{$evaluation->id}/items/{$rate->id}", ['actual_value' => 100])->assertSessionHasErrors('actual_value');
        $this->assertEquals(20.0, (float) $rate->fresh()->actual_value);

        // Finalisasi ditolak selama masih ada item tanpa nilai aktual
        $this->actingAs($evaluatorUser)->post("/kpi/evaluations/{$evaluation->id}/finalize", [])->assertSessionHasErrors();
        $this->assertSame('draft', $evaluation->fresh()->status);

        // Isi item manual, lalu finalisasi: 12 + 16 + 10 = 38
        $this->actingAs($evaluatorUser)->put("/kpi/evaluations/{$evaluation->id}/items/{$manual->id}", ['actual_value' => 50])->assertSessionHasNoErrors();
        $this->actingAs($evaluatorUser)->post("/kpi/evaluations/{$evaluation->id}/finalize", [])->assertSessionHasNoErrors();

        $final = $evaluation->fresh();
        $this->assertSame('finalized', $final->status);
        $this->assertEquals(38.0, (float) $final->total_score);
        $this->assertSame('D', $final->grade);
    }

    public function test_payroll_memotong_sakit_tidak_berbayar_sesuai_hari_terjadwal(): void
    {
        $this->runSeptember();

        LeavePaySetting::create(['position_id' => $this->position->id, 'leave_type' => 'sick', 'is_paid' => false]);
        $base = PayrollComponent::firstOrCreate(['component_code' => 'BASE_SALARY'], ['component_name' => 'Gaji Pokok', 'component_type' => 'earning', 'calculation_method' => 'fixed', 'is_active' => true]);
        PayrollComponent::firstOrCreate(['component_code' => 'LEAVE_DEDUCTION'], ['component_name' => 'Potongan Izin', 'component_type' => 'deduction', 'calculation_method' => 'manual', 'is_active' => true]);
        EmployeeCompensation::create(['employee_id' => $this->employee->id, 'payroll_component_id' => $base->id, 'value' => 2_500_000]);

        $period = PayrollPeriod::create(['period_year' => 2026, 'period_month' => 9, 'status' => 'finalized', 'confirmed_at' => now()]);
        $admin = User::factory()->create(['employee_id' => Employee::factory()->create(['is_active' => false])->id]);
        $admin->givePermissionTo(['payroll.period.view', 'payroll.period.generate']);

        $this->actingAs($admin)->post(route('payroll.periods.generate', $period))->assertSessionHas('success');

        $payroll = EmployeePayroll::where('payroll_period_id', $period->id)->where('employee_id', $this->employee->id)->firstOrFail();
        $this->assertSame([25, 5, 2, 1, 0], [
            (int) $payroll->scheduled_working_days, (int) $payroll->days_present, (int) $payroll->days_sick, (int) $payroll->days_late, (int) $payroll->days_absent,
        ]);

        // Potongan = 2.500.000 / 25 hari terjadwal x 2 hari sakit = 200.000
        $item = $payroll->items()->where('source_type', 'leave')->firstOrFail();
        $this->assertEquals([2, 100_000.0, 200_000.0], [(int) $item->quantity, (float) $item->unit_value, (float) $item->total_amount]);
        $this->assertEquals(2_300_000.0, (float) $payroll->net_amount);
    }

    public function test_payroll_dua_kali_tidak_menggandakan_potongan(): void
    {
        $this->runSeptember();

        LeavePaySetting::create(['position_id' => $this->position->id, 'leave_type' => 'sick', 'is_paid' => false]);
        $base = PayrollComponent::firstOrCreate(['component_code' => 'BASE_SALARY'], ['component_name' => 'Gaji Pokok', 'component_type' => 'earning', 'calculation_method' => 'fixed', 'is_active' => true]);
        PayrollComponent::firstOrCreate(['component_code' => 'LEAVE_DEDUCTION'], ['component_name' => 'Potongan Izin', 'component_type' => 'deduction', 'calculation_method' => 'manual', 'is_active' => true]);
        EmployeeCompensation::create(['employee_id' => $this->employee->id, 'payroll_component_id' => $base->id, 'value' => 2_500_000]);
        $period = PayrollPeriod::create(['period_year' => 2026, 'period_month' => 9, 'status' => 'finalized', 'confirmed_at' => now()]);

        $service = app(PayrollGenerationService::class);
        $service->generate($period);
        $service->generate($period);

        $payrolls = EmployeePayroll::where('payroll_period_id', $period->id)->where('employee_id', $this->employee->id)->get();
        $this->assertCount(1, $payrolls);
        $this->assertSame(1, $payrolls->first()->items()->where('source_type', 'leave')->count());
        $this->assertEquals(2_300_000.0, (float) $payrolls->first()->net_amount);
    }
}
