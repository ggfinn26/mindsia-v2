<?php

namespace Tests\Feature\Attendance;

use App\Models\Branch;
use App\Models\Employee;
use App\Models\EmployeeAttendanceMonthlyRecap;
use App\Models\EmployeeLeaveRequest;
use App\Models\EmployeeWorkAttendanceLog;
use App\Models\Holiday;
use App\Models\User;
use App\Models\WorkScheduleAssignment;
use App\Models\WorkScheduleDay;
use App\Models\WorkScheduleRule;
use App\Repositories\HolidayRepository;
use App\Repositories\LeaveRequestRepository;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use RuntimeException;
use Tests\TestCase;

// Integritas pengajuan izin/sakit/cuti: hanya pending yang bisa diproses, persetujuan atomik, log absensi dan rekap
// sesuai hari kerja pegawai (bukan hari libur/non-kerja), dan data absensi asli tidak tertimpa.
// September 2026: 1 Sep = Selasa. Jadwal Senin-Sabtu.
class LeaveApprovalIntegrityTest extends TestCase
{
    private Employee $employee;

    private Employee $reviewer;

    private LeaveRequestRepository $repo;

    protected function setUp(): void
    {
        parent::setUp();

        $branch = Branch::factory()->create();
        $this->employee = Employee::factory()->create(['branch_id' => $branch->id, 'join_date' => '2024-01-01']);
        $this->reviewer = Employee::factory()->create(['branch_id' => $branch->id]);
        $this->repo = app(LeaveRequestRepository::class);

        $rule = WorkScheduleRule::factory()->create(['is_required' => true, 'is_active' => true]);
        foreach ([1, 2, 3, 4, 5, 6] as $day) {
            WorkScheduleDay::create(['work_schedule_rule_id' => $rule->id, 'day_of_week' => $day]);
        }
        WorkScheduleAssignment::create([
            'work_schedule_rule_id' => $rule->id,
            'assignable_type' => Employee::class,
            'assignable_id' => $this->employee->id,
            'effective_start_date' => '2026-01-01',
            'is_active' => true,
        ]);
    }

    private function request(string $type, string $from, string $to, string $status = 'pending'): EmployeeLeaveRequest
    {
        return EmployeeLeaveRequest::create([
            'employee_id' => $this->employee->id,
            'branch_id' => $this->employee->branch_id,
            'leave_type' => $type,
            'start_date' => $from,
            'end_date' => $to,
            'reason' => 'Uji',
            'status' => $status,
        ]);
    }

    private function logs(): Collection
    {
        return EmployeeWorkAttendanceLog::where('employee_id', $this->employee->id)
            ->orderBy('attendance_date')->pluck('status', 'attendance_date')
            ->mapWithKeys(fn ($status, $date) => [substr((string) $date, 0, 10) => $status]);
    }

    private function recap(): EmployeeAttendanceMonthlyRecap
    {
        return EmployeeAttendanceMonthlyRecap::where('employee_id', $this->employee->id)
            ->where('period_year', 2026)->where('period_month', 9)->firstOrFail();
    }

    // ---- hanya pending yang bisa diproses ----------------------------------------------------------------

    public function test_pengajuan_yang_sudah_diproses_tidak_bisa_disetujui_lagi(): void
    {
        foreach (['approved', 'rejected', 'cancelled'] as $status) {
            $request = $this->request('sick', '2026-09-01', '2026-09-01', $status);

            try {
                $this->repo->approve($request, $this->reviewer);
                $this->fail("Pengajuan berstatus {$status} seharusnya tidak bisa disetujui.");
            } catch (ValidationException $e) {
                $this->assertArrayHasKey('status', $e->errors());
            }

            $this->assertSame($status, $request->fresh()->status);
        }

        $this->assertCount(0, $this->logs(), 'Tidak boleh ada log absensi dari pengajuan yang gagal disetujui.');
    }

    public function test_menolak_pengajuan_yang_sudah_disetujui_tidak_meninggalkan_log_cuti_yatim(): void
    {
        $request = $this->request('leave', '2026-09-01', '2026-09-02');
        $this->repo->approve($request, $this->reviewer);

        try {
            $this->repo->reject($request->fresh(), $this->reviewer, 'Salah klik');
            $this->fail('Pengajuan yang sudah disetujui seharusnya tidak bisa ditolak.');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('status', $e->errors());
        }

        $this->assertSame('approved', $request->fresh()->status);
        $this->assertEquals(['2026-09-01' => 'leave', '2026-09-02' => 'leave'], $this->logs()->all());
    }

    public function test_pembatalan_hanya_untuk_pengajuan_pending(): void
    {
        $approved = $this->request('sick', '2026-09-03', '2026-09-03', 'approved');

        $this->expectException(ValidationException::class);
        $this->repo->cancel($approved);
    }

    public function test_menolak_pengajuan_pending_tidak_membuat_log_absensi(): void
    {
        $request = $this->request('sick', '2026-09-01', '2026-09-03');

        $this->repo->reject($request, $this->reviewer, 'Tidak ada surat dokter');

        $this->assertSame('rejected', $request->fresh()->status);
        $this->assertCount(0, $this->logs());
    }

    public function test_persetujuan_atomik_status_kembali_pending_bila_log_gagal_dibuat(): void
    {
        $this->app->instance(HolidayRepository::class, new class extends HolidayRepository
        {
            public function isHoliday(string $date): bool
            {
                throw new RuntimeException('database putus');
            }
        });
        $repo = app(LeaveRequestRepository::class);
        $request = $this->request('sick', '2026-09-01', '2026-09-02');

        try {
            $repo->approve($request, $this->reviewer);
            $this->fail('Seharusnya melempar RuntimeException.');
        } catch (RuntimeException) {
        }

        $this->assertSame('pending', $request->fresh()->status);
        $this->assertNull($request->fresh()->reviewed_by_employee_id);
    }

    // ---- log absensi & rekap -------------------------------------------------------------------------------

    public function test_sakit_tiga_hari_kerja_masuk_ke_rekap_tanpa_mengubah_hari_terjadwal(): void
    {
        $this->repo->approve($this->request('sick', '2026-09-01', '2026-09-03'), $this->reviewer);

        $this->assertEquals(['2026-09-01' => 'sick', '2026-09-02' => 'sick', '2026-09-03' => 'sick'], $this->logs()->all());

        $recap = $this->recap();
        $this->assertSame(3, (int) $recap->total_sick);
        $this->assertSame(0, (int) $recap->total_present);
        $this->assertSame(0, (int) $recap->total_permission + (int) $recap->total_leave);
        $this->assertSame(26, (int) $recap->total_scheduled_working_days);
    }

    public function test_tiga_jenis_pengajuan_masuk_ke_kolom_rekap_masing_masing(): void
    {
        $this->repo->approve($this->request('sick', '2026-09-01', '2026-09-01'), $this->reviewer);
        $this->repo->approve($this->request('permission', '2026-09-02', '2026-09-03'), $this->reviewer);
        $this->repo->approve($this->request('leave', '2026-09-04', '2026-09-04'), $this->reviewer);

        $recap = $this->recap();
        $this->assertSame([1, 2, 1], [(int) $recap->total_sick, (int) $recap->total_permission, (int) $recap->total_leave]);
    }

    public function test_hari_minggu_dan_libur_nasional_dalam_rentang_tidak_dibuatkan_log(): void
    {
        Holiday::create(['holiday_name' => 'Libur', 'holiday_start_date' => '2026-09-07', 'holiday_end_date' => '2026-09-07']);

        // Sabtu 5, Minggu 6 (non-kerja), Senin 7 (libur), Selasa 8
        $this->repo->approve($this->request('leave', '2026-09-05', '2026-09-08'), $this->reviewer);

        $this->assertEquals(['2026-09-05' => 'leave', '2026-09-08' => 'leave'], $this->logs()->all());
        $this->assertSame(2, (int) $this->recap()->total_leave);
    }

    public function test_kehadiran_asli_tidak_tertimpa_tetapi_absen_dan_telat_diganti_status_cuti(): void
    {
        EmployeeWorkAttendanceLog::factory()->present()->create(['employee_id' => $this->employee->id, 'branch_id' => $this->employee->branch_id, 'attendance_date' => '2026-09-01']);
        EmployeeWorkAttendanceLog::factory()->checkedIn()->create(['employee_id' => $this->employee->id, 'branch_id' => $this->employee->branch_id, 'attendance_date' => '2026-09-02']);
        EmployeeWorkAttendanceLog::factory()->create(['employee_id' => $this->employee->id, 'branch_id' => $this->employee->branch_id, 'attendance_date' => '2026-09-03', 'status' => 'absent']);

        $this->repo->approve($this->request('sick', '2026-09-01', '2026-09-03'), $this->reviewer);

        $this->assertEquals(['2026-09-01' => 'present', '2026-09-02' => 'checked_in', '2026-09-03' => 'sick'], $this->logs()->all());
    }

    public function test_pegawai_tanpa_jadwal_tetap_dibuatkan_log_di_semua_hari(): void
    {
        WorkScheduleAssignment::query()->delete();

        $this->repo->approve($this->request('sick', '2026-09-05', '2026-09-07'), $this->reviewer);

        $this->assertCount(3, $this->logs(), 'Tanpa jadwal sama sekali, semua hari dalam rentang dicatat (perilaku terdokumentasi LR-21).');
    }

    // ---- check-in di hari izin ---------------------------------------------------------------------------------

    public function test_check_in_di_hari_sakit_ditolak_dan_status_sakit_tidak_tertimpa(): void
    {
        $this->employee->branch->update(['latitude' => -6.2088, 'longitude' => 106.8456, 'radius_meters' => 500]);
        $user = User::factory()->create(['employee_id' => $this->employee->id]);
        $this->repo->approve($this->request('sick', '2026-09-02', '2026-09-02'), $this->reviewer);

        $this->travelTo(Carbon::create(2026, 9, 2, 8, 0));

        $this->actingAs($user)
            ->post(route('work-attendance.check-in'), ['check_in_latitude' => -6.2088, 'check_in_longitude' => 106.8456])
            ->assertSessionHasErrors('check_in');

        $log = EmployeeWorkAttendanceLog::where('employee_id', $this->employee->id)->whereDate('attendance_date', '2026-09-02')->first();
        $this->assertSame('sick', $log->status);
        $this->assertNull($log->check_in);
    }

    public function test_cuti_untuk_tanggal_sebelum_penugasan_berlaku_tetap_menyaring_akhir_pekan_walau_disetujui_di_akhir_pekan(): void
    {
        // Penugasan Senin-Jumat baru berlaku mulai Oktober; cuti diajukan untuk September (historis) dan disetujui hari Sabtu
        WorkScheduleAssignment::query()->delete();
        $rule = WorkScheduleRule::factory()->create(['is_required' => true, 'is_active' => true]);
        foreach ([1, 2, 3, 4, 5] as $day) {
            WorkScheduleDay::create(['work_schedule_rule_id' => $rule->id, 'day_of_week' => $day]);
        }
        WorkScheduleAssignment::create([
            'work_schedule_rule_id' => $rule->id, 'assignable_type' => Employee::class, 'assignable_id' => $this->employee->id,
            'effective_start_date' => '2026-10-01', 'is_active' => true,
        ]);

        foreach (['2026-10-31' => 'Sabtu', '2026-11-01' => 'Minggu', '2026-11-02' => 'Senin'] as $today => $label) {
            EmployeeWorkAttendanceLog::query()->delete();
            $this->travelTo(Carbon::parse($today.' 10:00'));

            // Jumat 4, Sabtu 5, Minggu 6, Senin 7 September
            $this->repo->approve($this->request('leave', '2026-09-04', '2026-09-07'), $this->reviewer);

            $this->assertEquals(['2026-09-04' => 'leave', '2026-09-07' => 'leave'], $this->logs()->all(), "Disetujui pada hari {$label}.");
            EmployeeLeaveRequest::query()->delete();
        }
    }

    // ---- pengajuan ------------------------------------------------------------------------------------------------

    public function test_rentang_bersebelahan_boleh_tetapi_satu_hari_yang_sama_ditolak(): void
    {
        $this->repo->create($this->employee, ['leave_type' => 'sick', 'start_date' => '2026-09-10', 'end_date' => '2026-09-11', 'reason' => 'a']);

        $this->repo->create($this->employee, ['leave_type' => 'sick', 'start_date' => '2026-09-12', 'end_date' => '2026-09-12', 'reason' => 'b']);
        $this->repo->create($this->employee, ['leave_type' => 'sick', 'start_date' => '2026-09-09', 'end_date' => '2026-09-09', 'reason' => 'c']);

        $this->expectException(ValidationException::class);
        $this->repo->create($this->employee, ['leave_type' => 'permission', 'start_date' => '2026-09-11', 'end_date' => '2026-09-13', 'reason' => 'bentrok']);
    }

    public function test_pengajuan_ditolak_atau_dibatalkan_tidak_memblokir_pengajuan_baru(): void
    {
        $this->request('sick', '2026-09-10', '2026-09-10', 'rejected');
        $this->request('sick', '2026-09-10', '2026-09-10', 'cancelled');

        $new = $this->repo->create($this->employee, ['leave_type' => 'sick', 'start_date' => '2026-09-10', 'end_date' => '2026-09-10', 'reason' => 'ulang']);

        $this->assertSame('pending', $new->status);
    }

    public function test_pegawai_tanpa_cabang_mendapat_pesan_jelas_bukan_error_database(): void
    {
        $areaStaff = Employee::factory()->create(['branch_id' => null, 'is_hq' => false]);

        try {
            $this->repo->create($areaStaff, ['leave_type' => 'sick', 'start_date' => '2026-09-10', 'end_date' => '2026-09-10', 'reason' => 'x']);
            $this->fail('Seharusnya ditolak.');
        } catch (ValidationException $e) {
            $this->assertStringContainsString('cabang', $e->errors()['employee'][0]);
        }
    }
}
