<?php

namespace Tests\Feature\Attendance;

use App\Models\Branch;
use App\Models\Employee;
use App\Models\EmployeeAttendanceMonthlyRecap;
use App\Models\EmployeeAttendanceSetting;
use App\Models\EmployeeLeaveRequest;
use App\Models\EmployeeWorkAttendanceLog;
use App\Models\EmploymentStatus;
use App\Models\Holiday;
use App\Models\Position;
use App\Models\User;
use App\Models\WorkScheduleAssignment;
use App\Models\WorkScheduleDay;
use App\Models\WorkScheduleRule;
use App\Repositories\LeaveRequestRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

// Job auto-hadir (tidak wajib absen) dan job alpha (wajib absen tapi tidak ada log).
// Kalender: 1 Sep 2026 = Selasa; Minggu = 6, 13, 20, 27. Jadwal Senin-Sabtu kecuali disebut lain.
class DailyAttendanceJobsTest extends TestCase
{
    private Branch $branch;

    protected function setUp(): void
    {
        parent::setUp();
        $this->branch = Branch::factory()->create();
    }

    private function employee(array $attributes = []): Employee
    {
        return Employee::factory()->create(array_merge(['branch_id' => $this->branch->id, 'is_active' => true, 'join_date' => '2024-01-01'], $attributes));
    }

    /** @param list<int> $days ISO 1=Senin; kosong = berlaku setiap hari */
    private function rule(string $start = '08:00', string $end = '17:00', bool $required = true, array $days = [1, 2, 3, 4, 5, 6]): WorkScheduleRule
    {
        $rule = WorkScheduleRule::factory()->create(['start_time' => $start, 'end_time' => $end, 'is_required' => $required, 'is_active' => true]);
        foreach ($days as $day) {
            WorkScheduleDay::create(['work_schedule_rule_id' => $rule->id, 'day_of_week' => $day]);
        }

        return $rule;
    }

    private function assign(WorkScheduleRule $rule, Employee|Position $target): void
    {
        WorkScheduleAssignment::create([
            'work_schedule_rule_id' => $rule->id, 'assignable_type' => $target::class, 'assignable_id' => $target->id,
            'effective_start_date' => '2026-01-01', 'is_active' => true,
        ]);
    }

    private function at(int $day, int $hour = 0, int $minute = 0, int $month = 9): void
    {
        $this->travelTo(Carbon::create(2026, $month, $day, $hour, $minute));
    }

    private function auto(array $options = []): void
    {
        $this->artisan('attendance:auto-present', $options)->assertSuccessful();
    }

    private function absent(array $options = []): void
    {
        $this->artisan('attendance:mark-absent', $options)->assertSuccessful();
    }

    private function log(Employee $employee, string $date): ?EmployeeWorkAttendanceLog
    {
        return EmployeeWorkAttendanceLog::where('employee_id', $employee->id)->whereDate('attendance_date', $date)->first();
    }

    private function statusOn(Employee $employee, string $date): ?string
    {
        return $this->log($employee, $date)?->status;
    }

    private function recap(Employee $employee, int $month = 9): EmployeeAttendanceMonthlyRecap
    {
        return EmployeeAttendanceMonthlyRecap::where('employee_id', $employee->id)->where('period_year', 2026)->where('period_month', $month)->firstOrFail();
    }

    private function autoEmployee(string $start = '08:00'): Employee
    {
        $employee = $this->employee();
        $this->assign($this->rule($start, '17:00', required: false), $employee);

        return $employee;
    }

    private function manualEmployee(string $end = '17:00'): Employee
    {
        $employee = $this->employee();
        $this->assign($this->rule('08:00', $end, required: true), $employee);

        return $employee;
    }

    // =========================================================================================================
    // AUTO-HADIR
    // =========================================================================================================

    public function test_auto_hadir_dicatat_tepat_pada_jam_mulai_jadwal(): void
    {
        $employee = $this->autoEmployee('08:00');

        $this->at(1, 7, 59);
        $this->auto();
        $this->assertNull($this->log($employee, '2026-09-01'), 'Belum jam mulai.');

        $this->at(1, 8, 0);
        $this->auto();

        $log = $this->log($employee, '2026-09-01');
        $this->assertSame('present', $log->status);
        $this->assertSame('2026-09-01 08:00:00', $log->check_in->format('Y-m-d H:i:s'));
        $this->assertNull($log->check_out);
        $this->assertSame([0, 0, false, $this->branch->id], [$log->late_minutes, $log->early_leave_minutes, $log->is_location_anomaly, $log->branch_id]);
        $this->assertStringContainsString('otomatis', $log->notes);
    }

    public function test_jam_mulai_mengikuti_jadwal_posisi_masing_masing(): void
    {
        $early = Position::create(['position_name' => 'Pagi'.uniqid(), 'hierarchy_order' => 5]);
        $late = Position::create(['position_name' => 'Siang'.uniqid(), 'hierarchy_order' => 5]);
        $this->assign($this->rule('08:00', '17:00', required: false), $early);
        $this->assign($this->rule('10:00', '18:00', required: false), $late);

        $a = $this->employee();
        $b = $this->employee();
        EmploymentStatus::create(['employees_id' => $a->id, 'position_id' => $early->id, 'type_employment' => 'permanent']);
        EmploymentStatus::create(['employees_id' => $b->id, 'position_id' => $late->id, 'type_employment' => 'permanent']);

        $this->at(1, 9, 0);
        $this->auto();
        $this->assertSame(['present', null], [$this->statusOn($a, '2026-09-01'), $this->statusOn($b, '2026-09-01')]);

        $this->at(1, 10, 0);
        $this->auto();
        $this->assertSame('2026-09-01 10:00:00', $this->log($b, '2026-09-01')->check_in->format('Y-m-d H:i:s'));
        $this->assertSame('2026-09-01 08:00:00', $this->log($a, '2026-09-01')->check_in->format('Y-m-d H:i:s'));
    }

    public function test_aturan_tunggal_wajib_absen_setting_atau_jadwal_cukup_salah_satu_untuk_otomatis(): void
    {
        $byRule = $this->autoEmployee();                       // aturan jadwal tidak wajib
        $bySetting = $this->employee();                        // jadwal wajib, tetapi setting pegawai tidak wajib
        $this->assign($this->rule(required: true), $bySetting);
        EmployeeAttendanceSetting::create(['employee_id' => $bySetting->id, 'is_required' => false, 'is_session_required' => false, 'attendance_scope' => 'anywhere']);
        $manual = $this->manualEmployee();                     // keduanya wajib

        $this->at(1, 9, 0);
        $this->auto();

        $this->assertSame(['present', 'present', null], [$this->statusOn($byRule, '2026-09-01'), $this->statusOn($bySetting, '2026-09-01'), $this->statusOn($manual, '2026-09-01')]);
    }

    public function test_auto_hadir_dilewati_di_libur_nasional_dan_hari_di_luar_jadwal(): void
    {
        $employee = $this->autoEmployee();
        Holiday::create(['holiday_name' => 'Libur', 'holiday_start_date' => '2026-09-02', 'holiday_end_date' => '2026-09-02']);

        $this->at(2, 12, 0);   // libur
        $this->auto();
        $this->at(6, 12, 0);   // Minggu, di luar jadwal Senin-Sabtu
        $this->auto();

        $this->assertNull($this->log($employee, '2026-09-02'));
        $this->assertNull($this->log($employee, '2026-09-06'));
    }

    public function test_jadwal_tanpa_hari_kerja_berlaku_setiap_hari_termasuk_minggu(): void
    {
        $employee = $this->employee();
        $this->assign($this->rule('08:00', '17:00', required: false, days: []), $employee);

        $this->at(6, 12, 0);   // Minggu
        $this->auto();

        $this->assertSame('present', $this->statusOn($employee, '2026-09-06'));
    }

    public function test_auto_hadir_dilewati_sebelum_bergabung_setelah_off_boarding_nonaktif_tanpa_jadwal_atau_cabang(): void
    {
        $notYet = $this->autoEmployee();
        $notYet->update(['join_date' => '2026-09-10']);

        $offBoarded = $this->autoEmployee();
        $statusId = DB::table('employment_status')->insertGetId(['employees_id' => $offBoarded->id, 'type_employment' => 'contract', 'position_id' => Position::create(['position_name' => 'X'.uniqid(), 'hierarchy_order' => 5])->id, 'created_at' => now(), 'updated_at' => now()]);
        DB::table('off_boarding_status')->insert(['employment_status_id' => $statusId, 'off_boarding_date' => '2026-08-31', 'created_at' => now(), 'updated_at' => now()]);

        $inactive = $this->autoEmployee();
        $inactive->update(['is_active' => false]);

        $noSchedule = $this->employee();

        $noBranch = $this->employee(['branch_id' => null]);
        $this->assign($this->rule(required: false), $noBranch);

        $ok = $this->autoEmployee();

        $this->at(1, 12, 0);
        $this->artisan('attendance:auto-present')->expectsOutputToContain('1 dilewati')->assertSuccessful();

        foreach ([$notYet, $offBoarded, $inactive, $noSchedule, $noBranch] as $employee) {
            $this->assertNull($this->log($employee, '2026-09-01'), "Pegawai #{$employee->id} seharusnya dilewati.");
        }
        $this->assertSame('present', $this->statusOn($ok, '2026-09-01'));
    }

    public function test_auto_hadir_tidak_menimpa_log_yang_sudah_ada_apa_pun_statusnya(): void
    {
        $this->at(1, 12, 0);
        $logs = [];
        foreach (['present', 'checked_in', 'sick', 'permission', 'leave', 'absent'] as $status) {
            $employee = $this->autoEmployee();
            EmployeeWorkAttendanceLog::factory()->create(['employee_id' => $employee->id, 'branch_id' => $this->branch->id, 'attendance_date' => '2026-09-01', 'status' => $status]);
            $logs[$status] = $employee;
        }

        $this->auto();

        foreach ($logs as $status => $employee) {
            $this->assertSame($status, $this->statusOn($employee, '2026-09-01'));
        }
    }

    public function test_auto_hadir_idempoten(): void
    {
        $employee = $this->autoEmployee();
        $this->at(1, 9, 0);

        $this->auto();
        $this->at(1, 14, 0);
        $this->auto();
        $this->auto();

        $this->assertSame(1, EmployeeWorkAttendanceLog::where('employee_id', $employee->id)->count());
        $this->assertSame('2026-09-01 08:00:00', $this->log($employee, '2026-09-01')->check_in->format('Y-m-d H:i:s'), 'Jalan ulang tidak menggeser jam.');
    }

    public function test_opsi_tanggal_mengejar_tanggal_lampau_dan_mengabaikan_tanggal_depan(): void
    {
        $employee = $this->autoEmployee();
        $this->at(10, 9, 0);

        $this->auto(['--date' => '2026-09-03']);   // lampau: boleh walau jam sekarang lain
        $this->auto(['--date' => '2026-09-12']);   // depan: dilewati

        $this->assertSame('2026-09-03 08:00:00', $this->log($employee, '2026-09-03')->check_in->format('Y-m-d H:i:s'));
        $this->assertNull($this->log($employee, '2026-09-12'));
    }

    public function test_format_tanggal_salah_ditolak(): void
    {
        $this->artisan('attendance:auto-present', ['--date' => '03-09-2026'])->assertFailed();
        $this->artisan('attendance:mark-absent', ['--date' => 'bukan-tanggal'])->assertFailed();
    }

    public function test_jadwal_tanpa_jam_mulai_langsung_berlaku_dari_awal_hari(): void
    {
        $employee = $this->employee();
        $rule = $this->rule(required: false);
        $rule->update(['start_time' => null, 'end_time' => null]);
        $this->assign($rule, $employee);

        $this->at(1, 0, 5);
        $this->auto();

        $log = $this->log($employee, '2026-09-01');
        $this->assertSame('present', $log->status);
        $this->assertSame('2026-09-01 00:00:00', $log->check_in->format('Y-m-d H:i:s'));
    }

    public function test_cuti_yang_disetujui_sebelum_job_tidak_ditimpa_dan_sesudah_job_tidak_menghapus_hadir(): void
    {
        $this->at(1, 6, 0);
        $before = $this->autoEmployee();
        $after = $this->autoEmployee();
        $repo = app(LeaveRequestRepository::class);
        $reviewer = $this->employee();
        $make = fn (Employee $e) => EmployeeLeaveRequest::create([
            'employee_id' => $e->id, 'branch_id' => $this->branch->id, 'leave_type' => 'sick',
            'start_date' => '2026-09-01', 'end_date' => '2026-09-01', 'reason' => 'x', 'status' => 'pending',
        ]);

        $repo->approve($make($before), $reviewer);   // pegawai auto-hadir: aturan jadwal tidak wajib, cuti tidak membuat log
        $this->at(1, 9, 0);
        $this->auto();
        $repo->approve($make($after), $reviewer);

        $this->assertSame('present', $this->statusOn($before, '2026-09-01'), 'Auto-hadir tidak butuh cuti; tetap hadir.');
        $this->assertSame('present', $this->statusOn($after, '2026-09-01'), 'Kehadiran yang sudah tercatat tidak ditimpa cuti.');
    }

    public function test_pegawai_auto_hadir_yang_check_in_manual_tidak_error_dan_log_tidak_bergeser(): void
    {
        $employee = $this->autoEmployee('08:00');
        $user = User::factory()->create(['employee_id' => $employee->id]);

        $this->at(1, 8, 0);
        $this->auto();

        $this->at(1, 9, 30);
        $this->actingAs($user)->post(route('work-attendance.check-in'), ['check_in_latitude' => -6.2, 'check_in_longitude' => 106.8])->assertSessionHasNoErrors();

        $log = $this->log($employee, '2026-09-01');
        $this->assertSame(['present', '08:00'], [$log->status, $log->check_in->format('H:i')]);
    }

    public function test_auto_hadir_masuk_rekap_sebagai_hadir_dengan_hari_terjadwal(): void
    {
        $employee = $this->autoEmployee();
        foreach ([1, 2, 3] as $day) {
            $this->at($day, 9, 0);
            $this->auto();
        }

        $recap = $this->recap($employee);
        $this->assertSame([3, 26, 0], [(int) $recap->total_present, (int) $recap->total_scheduled_working_days, (int) $recap->total_absent]);
    }

    // =========================================================================================================
    // ALPHA
    // =========================================================================================================

    public function test_alpha_hari_ini_baru_dicatat_setelah_jam_selesai_jadwal(): void
    {
        $employee = $this->manualEmployee('17:00');

        $this->at(1, 16, 59);
        $this->absent();
        $this->assertNull($this->log($employee, '2026-09-01'), 'Masih jam kerja.');

        $this->at(1, 17, 0);
        $this->absent();

        $log = $this->log($employee, '2026-09-01');
        $this->assertSame(['absent', 0], [$log->status, $log->late_minutes]);
        $this->assertNull($log->check_in);
        $this->assertStringContainsString('Alpha', $log->notes);
    }

    public function test_alpha_mengejar_hari_lampau_dalam_batas_lookback(): void
    {
        $employee = $this->manualEmployee();

        $this->at(5, 8, 0);   // Sabtu pagi; 4, 3 = dua hari ke belakang; 2 Sep di luar jangkauan
        $this->absent();

        $this->assertSame(['absent', 'absent', null, null], [
            $this->statusOn($employee, '2026-09-04'), $this->statusOn($employee, '2026-09-03'),
            $this->statusOn($employee, '2026-09-02'), $this->statusOn($employee, '2026-09-05'),
        ]);
    }

    public function test_lookback_nol_hanya_hari_ini_dan_lookback_besar_menjangkau_lebih_jauh(): void
    {
        $employee = $this->manualEmployee();
        $this->at(8, 20, 0);

        $this->absent(['--lookback' => 0]);
        $this->assertSame(['absent', null], [$this->statusOn($employee, '2026-09-08'), $this->statusOn($employee, '2026-09-07')]);

        $this->absent(['--lookback' => 5]);
        $this->assertSame('absent', $this->statusOn($employee, '2026-09-03'));
        $this->assertNull($this->statusOn($employee, '2026-09-06'), 'Minggu di luar jadwal.');
    }

    public function test_alpha_dilewati_di_libur_hari_non_kerja_dan_pegawai_yang_tidak_berhak(): void
    {
        $holidayEmp = $this->manualEmployee();
        Holiday::create(['holiday_name' => 'Libur', 'holiday_start_date' => '2026-09-02', 'holiday_end_date' => '2026-09-02']);
        $notYet = $this->manualEmployee();
        $notYet->update(['join_date' => '2026-09-10']);
        $inactive = $this->manualEmployee();
        $inactive->update(['is_active' => false]);
        $noSchedule = $this->employee();
        $autoEmp = $this->autoEmployee();           // wajib hadir otomatis, bukan alpha
        $noBranch = $this->employee(['branch_id' => null]);
        $this->assign($this->rule(), $noBranch);

        $this->at(2, 20, 0);
        $this->artisan('attendance:mark-absent', ['--date' => '2026-09-02'])->expectsOutputToContain('0 dicatat')->assertSuccessful();   // libur
        $this->at(7, 20, 0);
        $this->artisan('attendance:mark-absent', ['--date' => '2026-09-06'])->assertSuccessful();   // Minggu
        $this->artisan('attendance:mark-absent', ['--date' => '2026-09-05'])->expectsOutputToContain('1 dilewati')->assertSuccessful();

        foreach ([[$holidayEmp, '2026-09-02'], [$holidayEmp, '2026-09-06'], [$notYet, '2026-09-05'], [$inactive, '2026-09-05'], [$noSchedule, '2026-09-05'], [$autoEmp, '2026-09-05'], [$noBranch, '2026-09-05']] as [$employee, $date]) {
            $this->assertNull($this->log($employee, $date), "Pegawai #{$employee->id} tanggal {$date} seharusnya tidak alpha.");
        }
        $this->assertSame('absent', $this->statusOn($holidayEmp, '2026-09-05'));
    }

    public function test_alpha_dilewati_bila_sudah_ada_log_hadir_izin_atau_cuti(): void
    {
        $this->at(3, 20, 0);
        $logs = [];
        foreach (['present', 'checked_in', 'sick', 'permission', 'leave'] as $status) {
            $employee = $this->manualEmployee();
            EmployeeWorkAttendanceLog::factory()->create(['employee_id' => $employee->id, 'branch_id' => $this->branch->id, 'attendance_date' => '2026-09-03', 'status' => $status]);
            $logs[$status] = $employee;
        }
        $gap = $this->manualEmployee();

        $this->absent(['--date' => '2026-09-03']);

        foreach ($logs as $status => $employee) {
            $this->assertSame($status, $this->statusOn($employee, '2026-09-03'));
        }
        $this->assertSame('absent', $this->statusOn($gap, '2026-09-03'));
    }

    public function test_cuti_disetujui_untuk_hari_depan_membuat_job_alpha_melewatinya(): void
    {
        $employee = $this->manualEmployee();
        $request = EmployeeLeaveRequest::create([
            'employee_id' => $employee->id, 'branch_id' => $this->branch->id, 'leave_type' => 'leave',
            'start_date' => '2026-09-08', 'end_date' => '2026-09-09', 'reason' => 'x', 'status' => 'pending',
        ]);
        app(LeaveRequestRepository::class)->approve($request, $this->employee());

        $this->at(10, 8, 0);
        $this->absent();   // lookback 2: 8, 9, 10 -> 8 dan 9 sudah cuti; 10 belum jam selesai

        $this->assertSame(['leave', 'leave', null], [$this->statusOn($employee, '2026-09-08'), $this->statusOn($employee, '2026-09-09'), $this->statusOn($employee, '2026-09-10')]);
    }

    public function test_pengajuan_ditolak_atau_dibatalkan_tidak_menghalangi_alpha(): void
    {
        $employee = $this->manualEmployee();
        foreach (['rejected', 'cancelled'] as $status) {
            EmployeeLeaveRequest::create([
                'employee_id' => $employee->id, 'branch_id' => $this->branch->id, 'leave_type' => 'sick',
                'start_date' => '2026-09-03', 'end_date' => '2026-09-03', 'reason' => 'x', 'status' => $status,
            ]);
        }

        $this->at(3, 20, 0);
        $this->absent();

        $this->assertSame('absent', $this->statusOn($employee, '2026-09-03'));
    }

    public function test_alpha_idempoten_dan_masuk_rekap_tanpa_mengubah_hari_terjadwal(): void
    {
        $employee = $this->manualEmployee();

        $this->at(3, 20, 0);
        $this->absent();
        $this->absent();

        $this->assertSame(1, EmployeeWorkAttendanceLog::where('employee_id', $employee->id)->whereDate('attendance_date', '2026-09-03')->count());
        $recap = $this->recap($employee);
        $this->assertSame([3, 26], [(int) $recap->total_absent, (int) $recap->total_scheduled_working_days]);   // 1, 2, 3 Sep
    }

    public function test_pegawai_yang_check_in_setelah_dicatat_alpha_menggantikan_alpha(): void
    {
        $employee = $this->manualEmployee('17:00');
        $employee->branch->update(['latitude' => -6.2088, 'longitude' => 106.8456, 'radius_meters' => 500]);
        $user = User::factory()->create(['employee_id' => $employee->id]);

        $this->at(1, 17, 10);
        $this->absent();
        $this->assertSame('absent', $this->statusOn($employee, '2026-09-01'));

        $this->at(1, 17, 40);
        $this->actingAs($user)->post(route('work-attendance.check-in'), ['check_in_latitude' => -6.2088, 'check_in_longitude' => 106.8456])->assertSessionHasNoErrors();

        $this->assertSame('checked_in', $this->statusOn($employee, '2026-09-01'));
        $this->assertSame(0, (int) $this->recap($employee)->total_absent);
    }

    public function test_cuti_yang_disetujui_setelah_alpha_menggantikan_alpha(): void
    {
        $employee = $this->manualEmployee();
        $this->at(3, 20, 0);
        $this->absent();
        $request = EmployeeLeaveRequest::create([
            'employee_id' => $employee->id, 'branch_id' => $this->branch->id, 'leave_type' => 'sick',
            'start_date' => '2026-09-03', 'end_date' => '2026-09-03', 'reason' => 'x', 'status' => 'pending',
        ]);

        app(LeaveRequestRepository::class)->approve($request, $this->employee());

        $recap = $this->recap($employee);
        $this->assertSame(['sick', 2, 1], [$this->statusOn($employee, '2026-09-03'), (int) $recap->total_absent, (int) $recap->total_sick]);   // alpha 1 & 2 Sep tetap
    }

    public function test_libur_yang_ditetapkan_setelah_alpha_tidak_dihitung_alpha_dan_pulih_bila_dihapus(): void
    {
        $employee = $this->manualEmployee();
        $this->at(3, 20, 0);
        $this->absent();
        $this->assertSame(3, (int) $this->recap($employee)->total_absent);

        $holiday = Holiday::create(['holiday_name' => 'Libur', 'holiday_start_date' => '2026-09-02', 'holiday_end_date' => '2026-09-02']);

        $recap = $this->recap($employee);
        $this->assertSame([2, 25], [(int) $recap->total_absent, (int) $recap->total_scheduled_working_days]);
        $this->assertSame('absent', $this->statusOn($employee, '2026-09-02'), 'Log tetap ada; hanya tidak dihitung.');

        $holiday->delete();
        $this->assertSame(3, (int) $this->recap($employee)->total_absent);
    }

    public function test_job_tengah_malam_menutup_hari_kemarin(): void
    {
        $employee = $this->manualEmployee();

        $this->at(2, 0, 10);   // job 00:10 hari berikutnya
        $this->absent();

        $this->assertSame('absent', $this->statusOn($employee, '2026-09-01'));
        $this->assertNull($this->log($employee, '2026-09-02'), 'Hari berjalan belum selesai.');
    }

    public function test_jadwal_tanpa_jam_selesai_alpha_menunggu_hari_berganti(): void
    {
        $employee = $this->employee();
        $rule = $this->rule(required: true);
        $rule->update(['end_time' => null]);
        $this->assign($rule, $employee);

        $this->at(3, 23, 0);
        $this->absent(['--lookback' => 1]);

        $this->assertSame(['absent', null], [$this->statusOn($employee, '2026-09-02'), $this->statusOn($employee, '2026-09-03')]);
    }

    public function test_auto_hadir_dan_alpha_di_hari_yang_sama_tidak_saling_mengganggu(): void
    {
        $auto = $this->autoEmployee('08:00');
        $manual = $this->manualEmployee('17:00');

        $this->at(1, 17, 5);
        $this->auto();
        $this->absent();

        $this->assertSame(['present', 'absent'], [$this->statusOn($auto, '2026-09-01'), $this->statusOn($manual, '2026-09-01')]);
    }

    public function test_dua_job_terdaftar_di_scheduler(): void
    {
        $this->artisan('schedule:list')->expectsOutputToContain('attendance:auto-present')->assertSuccessful();
        $this->artisan('schedule:list')->expectsOutputToContain('attendance:mark-absent')->assertSuccessful();
    }
}
