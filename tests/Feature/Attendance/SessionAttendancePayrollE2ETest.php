<?php

namespace Tests\Feature\Attendance;

use App\Models\Branch;
use App\Models\ClassRoom;
use App\Models\ClassSchedule;
use App\Models\Employee;
use App\Models\EmployeeAttendanceMonthlyRecap;
use App\Models\EmployeePayroll;
use App\Models\EmployeeSessionAttendanceLog;
use App\Models\EmploymentStatus;
use App\Models\PayrollComponent;
use App\Models\PayrollPeriod;
use App\Models\Position;
use App\Models\Program;
use App\Models\SessionCompensationRule;
use App\Models\SessionSchedule;
use App\Models\User;
use App\Services\Payroll\PayrollGenerationService;
use App\Services\TelegramStorageService;
use Carbon\Carbon;
use Mockery\MockInterface;
use Tests\TestCase;

// E2E absensi sesi tutor sampai payroll (September 2026), lewat route HTTP asli:
//   check-in/out per hari -> command lupa-absen-keluar -> hitungan sesi di payroll -> gaji sesi.
class SessionAttendancePayrollE2ETest extends TestCase
{
    private const LAT = -6.2088;

    private const LNG = 106.8456;

    private Branch $branch;

    private Employee $tutor;

    private User $tutorUser;

    private PayrollPeriod $period;

    /** @var array<string, SessionSchedule> */
    private array $sessions = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->mock(TelegramStorageService::class, function (MockInterface $mock) {
            $mock->shouldReceive('uploadFile')->andReturn(['file_id' => 'fake']);
        });

        $this->branch = Branch::factory()->create(['latitude' => self::LAT, 'longitude' => self::LNG, 'radius_meters' => 500]);
        $this->tutor = Employee::factory()->create(['branch_id' => $this->branch->id, 'is_active' => true]);
        $this->tutorUser = User::factory()->create(['employee_id' => $this->tutor->id]);

        $position = Position::create(['position_name' => 'Tutor'.uniqid(), 'hierarchy_order' => 6]);
        EmploymentStatus::create(['employees_id' => $this->tutor->id, 'position_id' => $position->id, 'type_employment' => 'part_time']);

        SessionCompensationRule::create([
            'rule_code' => 'RULE-'.uniqid(), 'rule_name' => 'Tarif Tutor', 'scope_type' => 'employee',
            'employee_id' => $this->tutor->id, 'amount_per_session' => 50_000, 'is_active' => true,
        ]);
        PayrollComponent::firstOrCreate(
            ['component_code' => 'SESSION_FEE'],
            ['component_name' => 'Honor Sesi', 'component_type' => 'earning', 'calculation_method' => 'session', 'is_active' => true],
        );

        // Tutor wajib absen kerja juga, jadi punya rekap bulan itu
        EmployeeAttendanceMonthlyRecap::create([
            'employee_id' => $this->tutor->id, 'period_year' => 2026, 'period_month' => 9,
            'total_scheduled_working_days' => 26, 'total_effective_working_days' => 0, 'total_present' => 0,
        ]);

        $this->period = PayrollPeriod::create(['period_year' => 2026, 'period_month' => 9, 'status' => 'finalized', 'confirmed_at' => now()]);

        foreach (['2026-09-02', '2026-09-03', '2026-09-04', '2026-09-08', '2026-09-09'] as $date) {
            $this->sessions[$date] = $this->makeSession($date, $this->tutor);
        }
    }

    private function makeSession(string $date, Employee $tutor): SessionSchedule
    {
        $classRoom = ClassRoom::factory()->create([
            'program_id' => Program::factory()->create()->id, 'branch_id' => $this->branch->id, 'tutor_id' => $tutor->id,
        ]);
        $schedule = ClassSchedule::create([
            'class_id' => $classRoom->id, 'schedule_date' => $date, 'start_time' => '11:00', 'end_time' => '13:00', 'late_tolerance_minutes' => 15,
        ]);

        return SessionSchedule::create(['class_schedule_id' => $schedule->id, 'employee_id' => $tutor->id]);
    }

    private function at(int $day, int $hour, int $minute): void
    {
        $this->travelTo(Carbon::create(2026, 9, $day, $hour, $minute));
    }

    private function checkIn(string $date, int $day, int $hour, int $minute): void
    {
        $this->at($day, $hour, $minute);
        $this->actingAs($this->tutorUser)
            ->post(route('session-attendance.check-in', $this->sessions[$date]->id), ['check_in_latitude' => self::LAT, 'check_in_longitude' => self::LNG])
            ->assertSessionHasNoErrors();
    }

    private function checkOut(string $date, int $day, int $hour, int $minute, array $data = ['check_out_latitude' => self::LAT, 'check_out_longitude' => self::LNG]): void
    {
        $this->at($day, $hour, $minute);
        $this->actingAs($this->tutorUser)
            ->post(route('session-attendance.check-out', $this->sessions[$date]->id), $data)
            ->assertSessionHasNoErrors();
    }

    private function logOf(string $date): ?EmployeeSessionAttendanceLog
    {
        return EmployeeSessionAttendanceLog::where('session_schedule_id', $this->sessions[$date]->id)->first();
    }

    private function payroll(): EmployeePayroll
    {
        app(PayrollGenerationService::class)->generate($this->period);

        return EmployeePayroll::where('payroll_period_id', $this->period->id)->where('employee_id', $this->tutor->id)->firstOrFail();
    }

    private function noiseSessions(): void
    {
        // Data di luar bulan, tahun, dan pemilik yang berbeda tidak boleh ikut terhitung
        $other = Employee::factory()->create(['branch_id' => $this->branch->id]);
        foreach ([['2026-08-31', $this->tutor], ['2026-10-01', $this->tutor], ['2025-09-02', $this->tutor], ['2026-09-02', $other]] as [$date, $owner]) {
            $session = $this->makeSession($date, $owner);
            EmployeeSessionAttendanceLog::create(['session_schedule_id' => $session->id, 'employee_id' => $owner->id, 'status' => 'present', 'late_minutes' => 0]);
        }
    }

    public function test_alur_sesi_sebulan_sampai_payroll(): void
    {
        $this->noiseSessions();

        // 2 Sep: tepat waktu, lengkap
        $this->checkIn('2026-09-02', 2, 10, 55);
        $this->checkOut('2026-09-02', 2, 13, 5);
        // 3 Sep: terlambat 15 menit (jadwal 11:00 + toleransi 15), check-out tanpa lokasi (GPS ditolak)
        $this->checkIn('2026-09-03', 3, 11, 30);
        $this->checkOut('2026-09-03', 3, 13, 0, []);
        // 4 Sep: check-in tapi lupa check-out
        $this->checkIn('2026-09-04', 4, 11, 0);
        // 8 dan 9 Sep: tidak hadir sama sekali

        $first = $this->logOf('2026-09-02');
        $this->assertSame(['present', 0], [$first->status, $first->late_minutes]);
        $second = $this->logOf('2026-09-03');
        $this->assertSame(['present', 15], [$second->status, $second->late_minutes]);
        $this->assertNull($second->check_out_latitude);
        $this->assertNull($second->check_out_distance_m);
        $this->assertSame('checked_in', $this->logOf('2026-09-04')->status);

        // Sebelum command: sesi yang lupa check-out TIDAK dihitung (uang tutor hilang)
        $before = $this->payroll();
        $this->assertSame([5, 2, 3, 1], [(int) $before->total_sessions, (int) $before->attended_sessions, (int) $before->absent_sessions, (int) $before->late_sessions]);

        // Lewat 23:59: command 00:05 hari berikutnya memfinalkan sesi yang lupa check-out
        $this->at(5, 0, 5);
        $this->artisan('attendance:finalize-missed-checkout')->assertSuccessful();

        $missed = $this->logOf('2026-09-04');
        $this->assertSame('present', $missed->status);
        $this->assertTrue($missed->is_check_out_missed);
        $this->assertNull($missed->check_out);

        $after = $this->payroll();
        $this->assertSame([5, 3, 2, 1], [(int) $after->total_sessions, (int) $after->attended_sessions, (int) $after->absent_sessions, (int) $after->late_sessions]);
        $this->assertEquals(150_000.0, (float) $after->items()->where('component_code_snapshot', 'SESSION_FEE')->value('total_amount'));
    }

    public function test_command_tidak_menyentuh_sesi_hari_ini_atau_yang_sudah_check_out(): void
    {
        $this->checkIn('2026-09-02', 2, 10, 55);
        $this->checkOut('2026-09-02', 2, 13, 0);
        $this->checkIn('2026-09-03', 3, 11, 0);

        // Command berjalan pada 3 Sep malam: sesi 3 Sep belum lewat harinya
        $this->at(3, 23, 0);
        $this->artisan('attendance:finalize-missed-checkout')->assertSuccessful();

        $this->assertSame('present', $this->logOf('2026-09-02')->status);
        $this->assertFalse($this->logOf('2026-09-02')->is_check_out_missed);
        $this->assertSame('checked_in', $this->logOf('2026-09-03')->status);
        $this->assertFalse($this->logOf('2026-09-03')->is_check_out_missed);
    }

    public function test_command_idempoten_dan_tidak_membuat_log_untuk_sesi_tanpa_kehadiran(): void
    {
        $this->checkIn('2026-09-04', 4, 11, 0);

        $this->at(5, 0, 5);
        $this->artisan('attendance:finalize-missed-checkout')->assertSuccessful();
        $this->artisan('attendance:finalize-missed-checkout')->assertSuccessful();

        $this->assertSame(1, EmployeeSessionAttendanceLog::where('employee_id', $this->tutor->id)->count());
        $this->assertTrue($this->logOf('2026-09-04')->is_check_out_missed);
    }

    public function test_check_out_setelah_lewat_hari_ditolak_karena_sesi_bukan_hari_ini(): void
    {
        $this->checkIn('2026-09-04', 4, 11, 0);

        $this->at(5, 8, 0);
        $this->actingAs($this->tutorUser)
            ->post(route('session-attendance.check-out', $this->sessions['2026-09-04']->id), ['check_out_latitude' => self::LAT, 'check_out_longitude' => self::LNG]);

        $this->assertNull($this->logOf('2026-09-04')->check_out, 'Batas absen keluar 23:59: tidak bisa check-out di hari berikutnya.');
    }

    public function test_sesi_orang_lain_tidak_bisa_diabsen(): void
    {
        $other = Employee::factory()->create(['branch_id' => $this->branch->id]);
        $foreign = $this->makeSession('2026-09-02', $other);

        $this->at(2, 10, 55);
        $this->actingAs($this->tutorUser)
            ->post(route('session-attendance.check-in', $foreign->id), ['check_in_latitude' => self::LAT, 'check_in_longitude' => self::LNG])
            ->assertStatus(404);
    }
}
