<?php

namespace Tests\Feature\Attendance;

use App\Models\Branch;
use App\Models\Employee;
use App\Models\EmployeeAttendanceMonthlyRecap;
use App\Models\EmployeeCompensation;
use App\Models\EmployeeLeaveRequest;
use App\Models\EmployeePayroll;
use App\Models\EmployeeWorkAttendanceLog;
use App\Models\EmploymentStatus;
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

// E2E dengan scheduler yang berjalan seperti aslinya (auto-hadir tiap 5 menit, alpha menit ke-10 tiap jam, finalisasi
// lupa-absen 00:05), untuk dua pegawai pada September 2026:
//   M = wajib absen, Senin-Sabtu 07:30-16:00.   A = tidak wajib absen (auto-hadir), Senin-Jumat 08:00-17:00.
class DailyAttendanceJobsE2ETest extends TestCase
{
    private const LAT = -6.2088;

    private const LNG = 106.8456;

    private Branch $branch;

    private Employee $manual;

    private Employee $auto;

    private User $manualUser;

    private Position $staff;

    protected function setUp(): void
    {
        parent::setUp();

        $this->branch = Branch::factory()->create(['latitude' => self::LAT, 'longitude' => self::LNG, 'radius_meters' => 500]);

        $this->staff = Position::create(['position_name' => 'Staff'.uniqid(), 'hierarchy_order' => 5]);
        $exec = Position::create(['position_name' => 'Eksekutif'.uniqid(), 'hierarchy_order' => 2]);

        $this->manual = Employee::factory()->create(['branch_id' => $this->branch->id, 'is_active' => true, 'join_date' => '2024-01-01']);
        $this->auto = Employee::factory()->create(['branch_id' => $this->branch->id, 'is_active' => true, 'join_date' => '2024-01-01']);
        EmploymentStatus::create(['employees_id' => $this->manual->id, 'position_id' => $this->staff->id, 'type_employment' => 'permanent']);
        EmploymentStatus::create(['employees_id' => $this->auto->id, 'position_id' => $exec->id, 'type_employment' => 'permanent']);
        $this->manualUser = User::factory()->create(['employee_id' => $this->manual->id]);

        $this->schedule($this->staff, '07:30', '16:00', required: true, days: [1, 2, 3, 4, 5, 6]);
        $this->schedule($exec, '08:00', '17:00', required: false, days: [1, 2, 3, 4, 5]);
    }

    private function schedule(Position $position, string $start, string $end, bool $required, array $days): void
    {
        $rule = WorkScheduleRule::factory()->create([
            'start_time' => $start, 'end_time' => $end, 'late_tolerance_minutes' => 15,
            'early_leave_tolerance_minutes' => 0, 'is_required' => $required, 'is_active' => true,
        ]);
        foreach ($days as $day) {
            WorkScheduleDay::create(['work_schedule_rule_id' => $rule->id, 'day_of_week' => $day]);
        }
        WorkScheduleAssignment::create([
            'work_schedule_rule_id' => $rule->id, 'assignable_type' => Position::class, 'assignable_id' => $position->id,
            'effective_start_date' => '2026-01-01', 'is_active' => true,
        ]);
    }

    /** Waktu berjalan ke hari/jam itu lalu menjalankan job persis seperti jadwal cron. */
    private function tick(int $day, int $hour, int $minute): void
    {
        $this->travelTo(Carbon::create(2026, 9, $day, $hour, $minute));

        $this->artisan('attendance:auto-present')->assertSuccessful();
        if ($minute === 10) {
            $this->artisan('attendance:mark-absent')->assertSuccessful();
        }
        if ($hour === 0 && $minute === 5) {
            $this->artisan('attendance:finalize-missed-checkout')->assertSuccessful();
        }
    }

    private function manualCheckIn(int $day, int $hour, int $minute)
    {
        $this->travelTo(Carbon::create(2026, 9, $day, $hour, $minute));

        return $this->actingAs($this->manualUser)->post(route('work-attendance.check-in'), ['check_in_latitude' => self::LAT, 'check_in_longitude' => self::LNG]);
    }

    private function manualCheckOut(int $day, int $hour, int $minute)
    {
        $this->travelTo(Carbon::create(2026, 9, $day, $hour, $minute));

        return $this->actingAs($this->manualUser)->post(route('work-attendance.check-out'), []);
    }

    private function statusOf(Employee $employee, string $date): ?string
    {
        return EmployeeWorkAttendanceLog::where('employee_id', $employee->id)->whereDate('attendance_date', $date)->value('status');
    }

    private function recap(Employee $employee): EmployeeAttendanceMonthlyRecap
    {
        return EmployeeAttendanceMonthlyRecap::where('employee_id', $employee->id)->where('period_year', 2026)->where('period_month', 9)->firstOrFail();
    }

    private function runSeptember(): void
    {
        // Selasa 1: M absen normal, A otomatis hadir jam 08:00
        $this->tick(1, 7, 25);
        $this->assertNull($this->statusOf($this->auto, '2026-09-01'), 'A belum dicatat sebelum jam mulai jadwalnya (08:00).');
        $this->manualCheckIn(1, 7, 30)->assertSessionHasNoErrors();
        $this->tick(1, 8, 0);
        $this->manualCheckOut(1, 16, 0)->assertSessionHasNoErrors();
        $this->tick(1, 16, 10);

        // Rabu 2: M tidak masuk; A otomatis hadir; 16:10 M dicatat alpha. Sore M mengajukan sakit untuk Kamis, disetujui.
        $this->tick(2, 8, 0);
        $this->tick(2, 15, 10);
        $this->assertNull($this->statusOf($this->manual, '2026-09-02'), 'M belum dicatat alpha sebelum jam selesai jadwalnya (16:00).');
        $this->tick(2, 16, 10);
        $request = EmployeeLeaveRequest::create([
            'employee_id' => $this->manual->id, 'branch_id' => $this->branch->id, 'leave_type' => 'sick',
            'start_date' => '2026-09-03', 'end_date' => '2026-09-03', 'reason' => 'Demam', 'status' => 'pending',
        ]);
        app(LeaveRequestRepository::class)->approve($request, Employee::factory()->create());

        // Kamis 3: M sakit (job tidak mengubah); A hadir otomatis. Malamnya Jumat 4 ditetapkan libur lewat halaman admin.
        $this->tick(3, 8, 0);
        $this->tick(3, 16, 10);
        $admin = User::factory()->create();
        $admin->givePermissionTo('attendance.holiday.manage');
        $this->actingAs($admin)->post(route('holidays.store'), ['holiday_name' => 'Libur Uji', 'holiday_start_date' => '2026-09-04', 'holiday_end_date' => '2026-09-04'])->assertSessionHasNoErrors();

        // Jumat 4 (libur): tidak ada job yang mencatat, check-in ditolak
        $this->tick(4, 8, 0);
        $this->tick(4, 16, 10);
        $this->manualCheckIn(4, 7, 40)->assertSessionHasErrors('date');

        // Sabtu 5: A tidak terjadwal. M tidak masuk, 16:10 dicatat alpha, lalu 18:00 M datang terlambat dan lupa check-out.
        $this->tick(5, 8, 0);
        $this->tick(5, 16, 10);
        $this->manualCheckIn(5, 18, 0)->assertSessionHasNoErrors();

        // Minggu 6 00:05: lupa absen keluar Sabtu difinalkan. Minggu tidak ada jadwal.
        $this->tick(6, 0, 5);
        $this->tick(6, 8, 0);
        $this->tick(6, 16, 10);

        // Senin 7: A otomatis hadir; M tidak masuk sampai 16:10
        $this->tick(7, 8, 0);
        $this->tick(7, 16, 10);

        $this->travelTo(Carbon::create(2026, 10, 1, 9, 0));
    }

    public function test_log_harian_dari_dua_job_cuti_libur_dan_absensi_manual(): void
    {
        $this->runSeptember();

        // M (wajib absen)
        $this->assertSame(
            ['present', 'absent', 'sick', null, 'present', null, 'absent'],
            collect(['01', '02', '03', '04', '05', '06', '07'])->map(fn ($d) => $this->statusOf($this->manual, "2026-09-{$d}"))->all(),
        );
        $saturday = EmployeeWorkAttendanceLog::where('employee_id', $this->manual->id)->whereDate('attendance_date', '2026-09-05')->first();
        $this->assertTrue($saturday->is_check_out_missed);
        $this->assertGreaterThan(0, $saturday->late_minutes);
        $this->assertNull($saturday->check_out);

        // A (tidak wajib absen): hadir otomatis di hari kerja Senin-Jumat, bukan libur, bukan Sabtu/Minggu
        $this->assertSame(
            ['present', 'present', 'present', null, null, null, 'present'],
            collect(['01', '02', '03', '04', '05', '06', '07'])->map(fn ($d) => $this->statusOf($this->auto, "2026-09-{$d}"))->all(),
        );
        $this->assertSame('2026-09-01 08:00:00', EmployeeWorkAttendanceLog::where('employee_id', $this->auto->id)->whereDate('attendance_date', '2026-09-01')->first()->check_in->format('Y-m-d H:i:s'));
    }

    public function test_rekap_kpi_dan_payroll_dari_hasil_job(): void
    {
        $this->runSeptember();

        // Rekap M: terjadwal Senin-Sabtu 26 hari - libur Jumat 4 = 25; hadir 1 + 5 Sep; sakit 1; alpha 2 dan 7
        $m = $this->recap($this->manual);
        $this->assertSame([25, 2, 1, 2, 1], [
            (int) $m->total_scheduled_working_days, (int) $m->total_present, (int) $m->total_sick, (int) $m->total_absent, (int) $m->total_late,
        ]);
        // Rekap A: terjadwal Senin-Jumat 22 - libur = 21; hadir 1, 2, 3, 7
        $a = $this->recap($this->auto);
        $this->assertSame([21, 4, 0], [(int) $a->total_scheduled_working_days, (int) $a->total_present, (int) $a->total_absent]);

        // KPI M: kehadiran 2/25 = 8%; kepatuhan absen keluar 1 dari 2 hari hadir = 50%
        $kpi = fn (string $source) => app(KpiDataSourceService::class)->resolve($source, $this->manual, Carbon::parse('2026-09-01'), Carbon::parse('2026-09-30'))['value'];
        $this->assertSame([8.0, 50.0], [$kpi('attendance.attendance_rate'), $kpi('attendance.check_out_compliance_rate')]);
        $this->assertSame(2.0, $kpi('attendance.total_absent'));

        // Payroll: M dipotong 2 hari alpha = 2 x (2.600.000 / 25); 1 hari sakit tetap digaji; A digaji penuh tanpa potongan
        $base = PayrollComponent::firstOrCreate(['component_code' => 'BASE_SALARY'], ['component_name' => 'Gaji Pokok', 'component_type' => 'earning', 'calculation_method' => 'fixed', 'is_active' => true]);
        PayrollComponent::firstOrCreate(['component_code' => 'LEAVE_DEDUCTION'], ['component_name' => 'Potongan Izin', 'component_type' => 'deduction', 'calculation_method' => 'manual', 'is_active' => true]);
        PayrollComponent::firstOrCreate(['component_code' => 'ABSENT_DEDUCTION'], ['component_name' => 'Potongan Alpha', 'component_type' => 'deduction', 'calculation_method' => 'manual', 'is_active' => true]);
        EmployeeCompensation::create(['employee_id' => $this->manual->id, 'payroll_component_id' => $base->id, 'value' => 2_600_000]);
        EmployeeCompensation::create(['employee_id' => $this->auto->id, 'payroll_component_id' => $base->id, 'value' => 10_000_000]);

        $period = PayrollPeriod::create(['period_year' => 2026, 'period_month' => 9, 'status' => 'finalized', 'confirmed_at' => now()]);
        app(PayrollGenerationService::class)->generate($period);

        $payM = EmployeePayroll::where('payroll_period_id', $period->id)->where('employee_id', $this->manual->id)->firstOrFail();
        $this->assertSame([25, 2, 1, 2], [(int) $payM->scheduled_working_days, (int) $payM->days_present, (int) $payM->days_sick, (int) $payM->days_absent]);
        $alpha = $payM->items()->where('component_code_snapshot', 'ABSENT_DEDUCTION')->firstOrFail();
        $this->assertEquals([2, 104_000.0, 208_000.0], [(int) $alpha->quantity, (float) $alpha->unit_value, (float) $alpha->total_amount]);
        $this->assertNull($payM->items()->where('source_type', 'leave')->first(), 'Sakit tetap digaji, tidak ada potongan izin.');
        $this->assertEquals(2_392_000.0, (float) $payM->net_amount);

        $payA = EmployeePayroll::where('payroll_period_id', $period->id)->where('employee_id', $this->auto->id)->firstOrFail();
        $this->assertSame([4, 0], [(int) $payA->days_present, (int) $payA->days_absent]);
        $this->assertEquals(10_000_000.0, (float) $payA->net_amount);
    }

    public function test_menjalankan_scheduler_berulang_kali_tidak_mengubah_hasil(): void
    {
        $this->runSeptember();
        $before = EmployeeWorkAttendanceLog::orderBy('id')->get(['employee_id', 'attendance_date', 'status', 'late_minutes'])->toArray();
        $recapBefore = $this->recap($this->manual)->only(['total_present', 'total_absent', 'total_sick', 'total_scheduled_working_days']);

        foreach (range(1, 7) as $day) {
            $this->tick($day, 16, 10);
            $this->tick($day, 8, 0);
        }
        $this->travelTo(Carbon::create(2026, 10, 1, 9, 0));

        $this->assertSame(count($before), EmployeeWorkAttendanceLog::count());
        $this->assertEquals($recapBefore, $this->recap($this->manual)->only(['total_present', 'total_absent', 'total_sick', 'total_scheduled_working_days']));
    }
}
