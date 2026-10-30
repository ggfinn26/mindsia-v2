<?php

namespace Tests\Feature\Attendance;

use App\Models\Branch;
use App\Models\Employee;
use App\Models\EmployeeAttendanceMonthlyRecap;
use App\Models\EmployeeAttendanceSetting;
use App\Models\EmployeeCompensation;
use App\Models\EmployeeLeaveRequest;
use App\Models\EmployeePayroll;
use App\Models\EmployeeWorkAttendanceLog;
use App\Models\EmploymentStatus;
use App\Models\Holiday;
use App\Models\LeavePaySetting;
use App\Models\PayrollComponent;
use App\Models\PayrollPeriod;
use App\Models\Position;
use App\Models\User;
use App\Models\WorkScheduleAssignment;
use App\Models\WorkScheduleDay;
use App\Models\WorkScheduleRule;
use App\Repositories\LeaveRequestRepository;
use App\Services\Kpi\KpiDataSourceService;
use App\Services\Payroll\PayrollGenerationService;
use Carbon\Carbon;
use Tests\TestCase;

// Libur nasional di semua alur: rekap (hari terjadwal, total_holiday, izin yang jatuh di hari libur), check-in/out,
// KPI, dan payroll. Kalender: 1 Sep 2026 = Selasa; Senin-Sabtu = 26 hari; Minggu = 6, 13, 20, 27.
class HolidayHandlingTest extends TestCase
{
    private Employee $employee;

    private Branch $branch;

    private WorkScheduleRule $rule;

    protected function setUp(): void
    {
        parent::setUp();

        $this->branch = Branch::factory()->create(['latitude' => -6.2088, 'longitude' => 106.8456, 'radius_meters' => 500]);
        $this->employee = Employee::factory()->create(['branch_id' => $this->branch->id, 'join_date' => '2024-01-01', 'is_active' => true]);

        $this->rule = WorkScheduleRule::factory()->create(['start_time' => '07:30', 'end_time' => '16:00', 'is_required' => true, 'is_active' => true]);
        foreach ([1, 2, 3, 4, 5, 6] as $day) {
            WorkScheduleDay::create(['work_schedule_rule_id' => $this->rule->id, 'day_of_week' => $day]);
        }
        WorkScheduleAssignment::create([
            'work_schedule_rule_id' => $this->rule->id, 'assignable_type' => Employee::class, 'assignable_id' => $this->employee->id,
            'effective_start_date' => '2026-01-01', 'is_active' => true,
        ]);
    }

    private function log(string $date, string $status = 'present', array $extra = []): EmployeeWorkAttendanceLog
    {
        return EmployeeWorkAttendanceLog::factory()->create(array_merge([
            'employee_id' => $this->employee->id, 'branch_id' => $this->branch->id, 'attendance_date' => $date, 'status' => $status,
        ], $extra));
    }

    private function holiday(string $from, ?string $to = null): Holiday
    {
        return Holiday::create(['holiday_name' => 'Libur '.uniqid(), 'holiday_start_date' => $from, 'holiday_end_date' => $to ?? $from]);
    }

    private function recap(int $year = 2026, int $month = 9): EmployeeAttendanceMonthlyRecap
    {
        return EmployeeAttendanceMonthlyRecap::where('employee_id', $this->employee->id)
            ->where('period_year', $year)->where('period_month', $month)->firstOrFail();
    }

    private function scheduled(int $year = 2026, int $month = 9): int
    {
        return (int) $this->recap($year, $month)->total_scheduled_working_days;
    }

    // ---- rekap mengikuti perubahan libur ---------------------------------------------------------------

    public function test_libur_baru_langsung_menyegarkan_rekap_yang_sudah_ada(): void
    {
        $this->log('2026-09-01');
        $this->assertSame(26, $this->scheduled());

        $this->holiday('2026-09-10');

        $this->assertSame(25, $this->scheduled());
        $this->assertSame(1, (int) $this->recap()->total_holiday);
    }

    public function test_libur_diubah_tanggalnya_menyegarkan_bulan_lama_dan_baru(): void
    {
        $this->log('2026-08-31', 'present');
        $this->log('2026-09-01', 'present');
        $holiday = $this->holiday('2026-08-31'); // Senin, hari kerja
        $this->assertSame([25, 26], [$this->scheduled(2026, 8), $this->scheduled()]);

        $holiday->update(['holiday_start_date' => '2026-09-10', 'holiday_end_date' => '2026-09-10']);

        $this->assertSame([26, 25], [$this->scheduled(2026, 8), $this->scheduled()], 'Bulan lama harus pulih dan bulan baru berkurang.');
    }

    public function test_libur_dihapus_mengembalikan_hari_terjadwal(): void
    {
        $this->log('2026-09-01');
        $holiday = $this->holiday('2026-09-10', '2026-09-11');
        $this->assertSame(24, $this->scheduled());

        $holiday->delete();

        $this->assertSame([26, 0], [$this->scheduled(), (int) $this->recap()->total_holiday]);
    }

    public function test_libur_lintas_bulan_menyegarkan_kedua_bulan(): void
    {
        $this->log('2026-08-31');
        $this->log('2026-09-01');

        $this->holiday('2026-08-30', '2026-09-02'); // Min 30 (non-kerja), Sen 31, Sel 1, Rab 2

        $this->assertSame([25, 24], [$this->scheduled(2026, 8), $this->scheduled()]);
        $this->assertSame([1, 2], [(int) $this->recap(2026, 8)->total_holiday, (int) $this->recap()->total_holiday]);
    }

    public function test_libur_lintas_tahun_menyegarkan_desember_dan_januari(): void
    {
        $this->log('2026-12-22');
        $this->log('2027-01-04');
        $decBefore = $this->scheduled(2026, 12);
        $janBefore = $this->scheduled(2027, 1);

        $this->holiday('2026-12-24', '2027-01-02'); // Kam 24 - Sab 2 Jan

        // Desember: 24, 25, 26, 28, 29, 30, 31 (Minggu 27 bukan hari kerja) = 7 hari; Januari: 1, 2 = 2 hari
        $this->assertSame([$decBefore - 7, $janBefore - 2], [$this->scheduled(2026, 12), $this->scheduled(2027, 1)]);
    }

    public function test_libur_di_hari_minggu_tidak_mengurangi_dan_bukan_libur_kerja(): void
    {
        $this->log('2026-09-01');

        $this->holiday('2026-09-06'); // Minggu

        $this->assertSame([26, 0], [$this->scheduled(), (int) $this->recap()->total_holiday]);
    }

    public function test_libur_tumpang_tindih_tidak_menggandakan_pengurangan(): void
    {
        $this->log('2026-09-01');

        $this->holiday('2026-09-10');
        $this->holiday('2026-09-10', '2026-09-11');
        $this->holiday('2026-09-11');

        $this->assertSame(24, $this->scheduled()); // hanya tanggal 10 dan 11
    }

    public function test_libur_sebelum_tanggal_bergabung_tidak_dihitung(): void
    {
        $this->employee->update(['join_date' => '2026-09-20']);
        $this->log('2026-09-21');

        $this->holiday('2026-09-10');   // sebelum bergabung
        $this->holiday('2026-09-22');   // sesudah bergabung

        // Bergabung Minggu 20 Sep: hari Senin-Sabtu 21-26 dan 28-30 Sep = 9, dikurangi libur Selasa 22 Sep = 8.
        // Libur 10 Sep jatuh sebelum bergabung sehingga tidak dihitung.
        $this->assertSame([8, 1], [$this->scheduled(), (int) $this->recap()->total_holiday]);
    }

    public function test_pegawai_tanpa_jadwal_tidak_punya_hari_libur_kerja(): void
    {
        WorkScheduleAssignment::query()->delete();
        $this->log('2026-09-01');

        $this->holiday('2026-09-10');

        $this->assertSame([0, 0], [$this->scheduled(), (int) $this->recap()->total_holiday]);
    }

    // ---- izin/sakit/cuti & hadir di hari libur --------------------------------------------------------------

    public function test_sakit_yang_belakangan_jatuh_di_hari_libur_tidak_dihitung_tetapi_log_tetap_ada(): void
    {
        $request = EmployeeLeaveRequest::create([
            'employee_id' => $this->employee->id, 'branch_id' => $this->branch->id, 'leave_type' => 'sick',
            'start_date' => '2026-09-09', 'end_date' => '2026-09-10', 'reason' => 'x', 'status' => 'pending',
        ]);
        app(LeaveRequestRepository::class)->approve($request, Employee::factory()->create());
        $this->assertSame(2, (int) $this->recap()->total_sick);

        $holiday = $this->holiday('2026-09-10');

        $this->assertSame(1, (int) $this->recap()->total_sick, 'Sakit di hari libur tidak boleh dihitung (dan tidak boleh dipotong).');
        $this->assertSame('sick', EmployeeWorkAttendanceLog::where('employee_id', $this->employee->id)->whereDate('attendance_date', '2026-09-10')->value('status'));

        $holiday->delete();
        $this->assertSame(2, (int) $this->recap()->total_sick, 'Libur dihapus: hitungan kembali.');
    }

    public function test_cuti_alpha_dan_izin_di_hari_libur_tidak_dihitung(): void
    {
        $this->log('2026-09-10', 'leave');
        $this->log('2026-09-11', 'permission');
        $this->log('2026-09-12', 'absent');
        $this->log('2026-09-14', 'leave');   // hari biasa

        $this->holiday('2026-09-10', '2026-09-12');

        $recap = $this->recap();
        $this->assertSame([1, 0, 0], [(int) $recap->total_leave, (int) $recap->total_permission, (int) $recap->total_absent]);
    }

    public function test_hadir_di_hari_libur_tetap_dihitung_hadir_dan_tidak_mengubah_hari_terjadwal(): void
    {
        $this->log('2026-09-17', 'present');   // masuk di hari libur (lembur/koreksi)
        $this->holiday('2026-09-17');

        $recap = $this->recap();
        $this->assertSame([1, 25], [(int) $recap->total_present, (int) $recap->total_scheduled_working_days]);
    }

    // ---- check-in / check-out -------------------------------------------------------------------------------

    public function test_check_in_di_hari_libur_ditolak_termasuk_untuk_pegawai_tidak_wajib_absen(): void
    {
        $this->holiday('2026-09-10');
        $user = User::factory()->create(['employee_id' => $this->employee->id]);
        EmployeeAttendanceSetting::create(['employee_id' => $this->employee->id, 'is_required' => false, 'is_session_required' => false, 'attendance_scope' => 'anywhere']);

        $this->travelTo(Carbon::create(2026, 9, 10, 8, 0));
        $this->actingAs($user)->post(route('work-attendance.check-in'), ['check_in_latitude' => -6.2088, 'check_in_longitude' => 106.8456])
            ->assertSessionHasErrors('date');

        $this->assertDatabaseMissing('employee_work_attendance_logs', ['employee_id' => $this->employee->id]);
    }

    public function test_check_out_tetap_bisa_walau_hari_itu_belakangan_ditetapkan_libur(): void
    {
        $user = User::factory()->create(['employee_id' => $this->employee->id]);

        $this->travelTo(Carbon::create(2026, 9, 10, 7, 30));
        $this->actingAs($user)->post(route('work-attendance.check-in'), ['check_in_latitude' => -6.2088, 'check_in_longitude' => 106.8456])->assertSessionHasNoErrors();

        $this->holiday('2026-09-10');

        $this->travelTo(Carbon::create(2026, 9, 10, 16, 0));
        $this->actingAs($user)->post(route('work-attendance.check-out'), [])->assertSessionHasNoErrors();
        $this->assertSame('present', EmployeeWorkAttendanceLog::where('employee_id', $this->employee->id)->value('status'));
    }

    public function test_libur_dikelola_lewat_halaman_admin_menyegarkan_rekap(): void
    {
        $this->log('2026-09-01');
        $admin = User::factory()->create();
        $admin->givePermissionTo('attendance.holiday.manage');

        $this->actingAs($admin)->post(route('holidays.store'), [
            'holiday_name' => 'Libur Uji', 'holiday_start_date' => '2026-09-10', 'holiday_end_date' => '2026-09-10',
        ])->assertRedirect(route('holidays.index'));
        $this->assertSame(25, $this->scheduled());

        $holiday = Holiday::where('holiday_name', 'Libur Uji')->firstOrFail();
        $this->actingAs($admin)->put(route('holidays.update', $holiday), [
            'holiday_name' => 'Libur Uji', 'holiday_start_date' => '2026-09-10', 'holiday_end_date' => '2026-09-12',
        ]);
        $this->assertSame(23, $this->scheduled()); // 10, 11, 12

        $this->actingAs($admin)->delete(route('holidays.destroy', $holiday));
        $this->assertSame(26, $this->scheduled());
    }

    // ---- KPI & payroll ----------------------------------------------------------------------------------------

    public function test_kpi_kehadiran_mengikuti_libur_yang_ditambahkan_setelah_rekap_terbentuk(): void
    {
        foreach (['2026-09-01', '2026-09-02', '2026-09-03', '2026-09-04', '2026-09-05'] as $date) {
            $this->log($date);
        }
        $kpi = fn () => app(KpiDataSourceService::class)->resolve('attendance.attendance_rate', $this->employee, Carbon::parse('2026-09-01'), Carbon::parse('2026-09-30'))['value'];
        $this->assertSame(19.23, $kpi()); // 5 / 26

        $this->holiday('2026-09-17');

        $this->assertSame(20.0, $kpi()); // 5 / 25
    }

    public function test_payroll_memakai_hari_terjadwal_setelah_libur_dan_tidak_memotong_sakit_di_hari_libur(): void
    {
        $position = Position::create(['position_name' => 'Staff'.uniqid(), 'hierarchy_order' => 5]);
        EmploymentStatus::create(['employees_id' => $this->employee->id, 'position_id' => $position->id, 'type_employment' => 'permanent']);
        LeavePaySetting::create(['position_id' => $position->id, 'leave_type' => 'sick', 'is_paid' => false]);
        $base = PayrollComponent::firstOrCreate(['component_code' => 'BASE_SALARY'], ['component_name' => 'Gaji Pokok', 'component_type' => 'earning', 'calculation_method' => 'fixed', 'is_active' => true]);
        PayrollComponent::firstOrCreate(['component_code' => 'LEAVE_DEDUCTION'], ['component_name' => 'Potongan Izin', 'component_type' => 'deduction', 'calculation_method' => 'manual', 'is_active' => true]);
        EmployeeCompensation::create(['employee_id' => $this->employee->id, 'payroll_component_id' => $base->id, 'value' => 2_500_000]);

        $this->log('2026-09-01');
        $this->log('2026-09-09', 'sick');
        $this->log('2026-09-10', 'sick');
        $this->holiday('2026-09-10');   // satu hari sakit jatuh di hari libur; 25 hari terjadwal

        $period = PayrollPeriod::create(['period_year' => 2026, 'period_month' => 9, 'status' => 'finalized', 'confirmed_at' => now()]);
        app(PayrollGenerationService::class)->generate($period);

        $payroll = EmployeePayroll::where('payroll_period_id', $period->id)->where('employee_id', $this->employee->id)->firstOrFail();
        $this->assertSame([25, 1, 1], [(int) $payroll->scheduled_working_days, (int) $payroll->days_sick, (int) $payroll->days_holiday]);

        // 2.500.000 / 25 hari terjadwal x 1 hari sakit (bukan 2: yang jatuh di hari libur tidak dihitung) = 100.000
        $item = $payroll->items()->where('source_type', 'leave')->firstOrFail();
        $this->assertEquals(1, (int) $item->quantity);
        $this->assertEquals(100_000.0, round((float) $item->total_amount, 2));
    }

    // ---- penugasan jadwal, aturan, dan setting ----------------------------------------------------------------

    public function test_mengubah_masa_berlaku_penugasan_menyegarkan_rekap(): void
    {
        $this->log('2026-09-01');
        $this->assertSame(26, $this->scheduled());

        WorkScheduleAssignment::where('assignable_id', $this->employee->id)->first()->update(['effective_end_date' => '2026-09-15']);
        $this->assertSame(13, $this->scheduled()); // 1-15 Sep Senin-Sabtu, tanpa Minggu 6 dan 13

        WorkScheduleAssignment::where('assignable_id', $this->employee->id)->first()->delete();   // per model, seperti controller
        $this->assertSame(0, $this->scheduled());
    }

    public function test_menambah_penugasan_level_posisi_menyegarkan_rekap_pegawai_di_posisi_itu(): void
    {
        WorkScheduleAssignment::query()->delete();
        $position = Position::create(['position_name' => 'Staff'.uniqid(), 'hierarchy_order' => 5]);
        EmploymentStatus::create(['employees_id' => $this->employee->id, 'position_id' => $position->id, 'type_employment' => 'permanent']);
        $this->log('2026-09-01');
        $this->assertSame(0, $this->scheduled());

        WorkScheduleAssignment::create([
            'work_schedule_rule_id' => $this->rule->id, 'assignable_type' => Position::class, 'assignable_id' => $position->id,
            'effective_start_date' => '2026-01-01', 'is_active' => true,
        ]);

        $this->assertSame(26, $this->scheduled());
    }

    public function test_mengubah_hari_kerja_aturan_menyegarkan_rekap_pemakainya(): void
    {
        $this->log('2026-09-01');
        $admin = User::factory()->create();
        $admin->givePermissionTo('attendance.schedule_rule.manage');

        $this->actingAs($admin)->patch(route('work-schedule-rules.update', $this->rule), [
            'setting_name' => $this->rule->setting_name, 'start_time' => '07:30', 'end_time' => '16:00',
            'late_tolerance_minutes' => 15, 'early_leave_tolerance_minutes' => 0, 'work_days' => [1, 2, 3, 4, 5],
        ])->assertSessionHasNoErrors();

        $this->assertSame(22, $this->scheduled()); // Senin-Jumat
    }

    public function test_setting_tidak_wajib_absen_tidak_mengubah_hari_terjadwal(): void
    {
        $this->log('2026-09-01');

        EmployeeAttendanceSetting::create(['employee_id' => $this->employee->id, 'is_required' => false, 'is_session_required' => false, 'attendance_scope' => 'anywhere']);

        $this->assertSame(26, $this->scheduled());
    }
}
