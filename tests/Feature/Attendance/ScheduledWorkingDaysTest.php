<?php

namespace Tests\Feature\Attendance;

use App\Models\Employee;
use App\Models\EmployeeAttendanceMonthlyRecap;
use App\Models\EmployeeAttendanceSetting;
use App\Models\EmployeeWorkAttendanceLog;
use App\Models\Holiday;
use App\Models\Position;
use App\Models\WorkScheduleAssignment;
use App\Models\WorkScheduleDay;
use App\Models\WorkScheduleRule;
use App\Repositories\AttendanceRecapRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

// Hari terjadwal di rekap = hari kerja menurut jadwal efektif pegawai, bukan asumsi Senin-Jumat.
// September 2026: 30 hari, mulai Selasa. Minggu = 6, 13, 20, 27 -> Senin-Sabtu = 26 hari, Senin-Jumat = 22 hari.
class ScheduledWorkingDaysTest extends TestCase
{
    private Employee $employee;

    private AttendanceRecapRepository $repo;

    protected function setUp(): void
    {
        parent::setUp();

        $this->employee = Employee::factory()->create(['join_date' => '2024-01-01']);
        $this->repo = app(AttendanceRecapRepository::class);
    }

    /** @param list<int> $days ISO 1=Senin .. 7=Minggu; kosong = berlaku setiap hari */
    private function rule(array $days, bool $required = true): WorkScheduleRule
    {
        $rule = WorkScheduleRule::factory()->create(['is_required' => $required, 'is_active' => true]);

        foreach ($days as $day) {
            WorkScheduleDay::create(['work_schedule_rule_id' => $rule->id, 'day_of_week' => $day]);
        }

        return $rule;
    }

    private function assign(WorkScheduleRule $rule, string $type, int $id, string $from = '2026-01-01', ?string $to = null): void
    {
        WorkScheduleAssignment::create([
            'work_schedule_rule_id' => $rule->id,
            'assignable_type' => $type,
            'assignable_id' => $id,
            'effective_start_date' => $from,
            'effective_end_date' => $to,
            'is_active' => true,
        ]);
    }

    private function assignToEmployee(array $days = [1, 2, 3, 4, 5, 6], bool $required = true, string $from = '2026-01-01', ?string $to = null): void
    {
        $this->assign($this->rule($days, $required), Employee::class, $this->employee->id, $from, $to);
    }

    private function days(): int
    {
        return $this->repo->scheduledWorkingDays($this->employee->fresh(), 2026, 9);
    }

    public function test_jadwal_senin_sabtu_menghitung_sabtu(): void
    {
        $this->assignToEmployee([1, 2, 3, 4, 5, 6]);

        $this->assertSame(26, $this->days());
    }

    public function test_jadwal_senin_jumat_tidak_menghitung_sabtu(): void
    {
        $this->assignToEmployee([1, 2, 3, 4, 5]);

        $this->assertSame(22, $this->days());
    }

    public function test_tanpa_jadwal_tidak_ada_hari_terjadwal(): void
    {
        $this->assertSame(0, $this->days());
    }

    public function test_jadwal_tidak_wajib_absen_tetap_punya_hari_terjadwal(): void
    {
        // Mereka dicatat hadir otomatis oleh job di hari-hari terjadwal, jadi hari itu tetap terjadwal
        $this->assignToEmployee([1, 2, 3, 4, 5, 6], required: false);

        $this->assertSame(26, $this->days());
    }

    public function test_setting_pegawai_tidak_wajib_absen_tetap_punya_hari_terjadwal(): void
    {
        $this->assignToEmployee();
        EmployeeAttendanceSetting::create(['employee_id' => $this->employee->id, 'is_required' => false, 'is_session_required' => false, 'attendance_scope' => 'anywhere']);

        $this->assertSame(26, $this->days());
    }

    // ---- libur nasional -------------------------------------------------------------------------

    public function test_libur_nasional_mengurangi_hari_terjadwal(): void
    {
        $this->assignToEmployee();
        Holiday::create(['holiday_name' => 'Libur', 'holiday_start_date' => '2026-09-10', 'holiday_end_date' => '2026-09-11']);

        $this->assertSame(24, $this->days());
    }

    public function test_libur_yang_melewati_batas_bulan_hanya_dihitung_dalam_bulan_ini(): void
    {
        $this->assignToEmployee();
        Holiday::create(['holiday_name' => 'Libur', 'holiday_start_date' => '2026-08-30', 'holiday_end_date' => '2026-09-02']);

        $this->assertSame(24, $this->days()); // hanya 1 dan 2 September
    }

    public function test_libur_di_hari_non_kerja_tidak_mengurangi_dua_kali(): void
    {
        $this->assignToEmployee();
        Holiday::create(['holiday_name' => 'Libur Minggu', 'holiday_start_date' => '2026-09-06', 'holiday_end_date' => '2026-09-06']);

        $this->assertSame(26, $this->days());
    }

    // ---- tanggal bergabung / berhenti ------------------------------------------------------------

    public function test_hari_sebelum_tanggal_bergabung_tidak_dihitung(): void
    {
        $this->employee->update(['join_date' => '2026-09-16']);
        $this->assignToEmployee();

        $this->assertSame(13, $this->days()); // 16-30 Sep Senin-Sabtu
    }

    public function test_bergabung_bulan_berikutnya_tidak_ada_hari_terjadwal(): void
    {
        $this->employee->update(['join_date' => '2026-10-05']);
        $this->assignToEmployee();

        $this->assertSame(0, $this->days());
    }

    public function test_hari_setelah_off_boarding_tidak_dihitung(): void
    {
        $this->assignToEmployee();
        $statusId = DB::table('employment_status')->insertGetId([
            'employees_id' => $this->employee->id,
            'type_employment' => 'contract',
            'position_id' => Position::first()->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('off_boarding_status')->insert([
            'employment_status_id' => $statusId,
            'off_boarding_date' => '2026-09-15',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->assertSame(13, $this->days()); // 1-15 Sep Senin-Sabtu
    }

    // ---- masa berlaku & prioritas penugasan ---------------------------------------------------------

    public function test_penugasan_hanya_dihitung_pada_masa_berlakunya(): void
    {
        $this->assignToEmployee(from: '2026-09-16');
        $this->assertSame(13, $this->days());

        WorkScheduleAssignment::query()->delete();
        $this->assignToEmployee(from: '2026-01-01', to: '2026-09-15');
        $this->assertSame(13, $this->days());
    }

    public function test_hari_di_luar_jadwal_pegawai_jatuh_ke_jadwal_posisi(): void
    {
        $position = Position::first();
        DB::table('employment_status')->insert([
            'employees_id' => $this->employee->id,
            'type_employment' => 'permanent',
            'position_id' => $position->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->assignToEmployee([1, 2, 3, 4, 5]);                                   // pegawai: Senin-Jumat
        $this->assign($this->rule([6]), Position::class, $position->id);            // posisi: Sabtu

        $this->assertSame(26, $this->days()); // Sabtu jatuh ke jadwal posisi
    }

    // ---- terintegrasi ke rekap & KPI -----------------------------------------------------------------

    public function test_rekap_menyimpan_hari_terjadwal_dari_jadwal_pegawai(): void
    {
        $this->assignToEmployee();
        Holiday::create(['holiday_name' => 'Libur', 'holiday_start_date' => '2026-09-10', 'holiday_end_date' => '2026-09-10']);

        EmployeeWorkAttendanceLog::factory()->present()->create([
            'employee_id' => $this->employee->id,
            'branch_id' => $this->employee->branch_id,
            'attendance_date' => '2026-09-15',
        ]);

        $recap = EmployeeAttendanceMonthlyRecap::where('employee_id', $this->employee->id)
            ->where('period_year', 2026)->where('period_month', 9)->first();

        $this->assertSame(25, (int) $recap->total_scheduled_working_days);
        $this->assertSame(1, (int) $recap->total_present);
    }

    public function test_command_rebuild_memperbaiki_rekap_lama(): void
    {
        $this->assignToEmployee();
        EmployeeAttendanceMonthlyRecap::create([
            'employee_id' => $this->employee->id,
            'period_year' => 2026,
            'period_month' => 9,
            'total_scheduled_working_days' => 22, // hitungan lama Senin-Jumat
            'total_present' => 0,
        ]);

        $this->artisan('attendance:rebuild-recaps', ['--year' => 2026, '--month' => 9])->assertSuccessful();

        $this->assertSame(26, (int) EmployeeAttendanceMonthlyRecap::where('employee_id', $this->employee->id)
            ->where('period_year', 2026)->where('period_month', 9)->value('total_scheduled_working_days'));
    }

    public function test_penghitungan_tidak_bergantung_pada_tanggal_hari_ini(): void
    {
        $this->assignToEmployee();
        Carbon::setTestNow('2026-12-31');

        $this->assertSame(26, $this->days());

        Carbon::setTestNow();
    }
}
