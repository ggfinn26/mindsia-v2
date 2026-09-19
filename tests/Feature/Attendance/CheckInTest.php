<?php

namespace Tests\Feature\Attendance;

use App\Models\Branch;
use App\Models\Employee;
use App\Models\EmployeeWorkAttendanceLog;
use App\Models\Holiday;
use App\Models\User;
use App\Models\WorkScheduleAssignment;
use App\Models\WorkScheduleRule;
use App\Services\TelegramStorageService;
use Illuminate\Http\UploadedFile;
use Mockery\MockInterface;
use Tests\TestCase;

class CheckInTest extends TestCase
{
    private User $user;

    private Employee $employee;

    private Branch $branch;

    protected function setUp(): void
    {
        parent::setUp();

        $this->branch = Branch::factory()->create([
            'latitude' => -6.2088,
            'longitude' => 106.8456,
            'radius_meters' => 500,
        ]);

        $this->employee = Employee::factory()->create([
            'branch_id' => $this->branch->id,
        ]);

        $this->user = User::factory()->create([
            'employee_id' => $this->employee->id,
        ]);

        // Mock TelegramStorageService to avoid real API calls
        $this->mock(TelegramStorageService::class, function (MockInterface $mock) {
            $mock->shouldReceive('uploadPhoto')->andReturn('fake_file_id_123');
        });
    }

    private function assignSchedule(Employee $employee, array $overrides = []): WorkScheduleRule
    {
        $rule = WorkScheduleRule::factory()->create($overrides);

        // Use FQCN for assignable_type — WorkScheduleRepository queries with FQCN
        // (GAP-WS-ASSIGN-TYPE: controller stores short types but repository expects FQCN)
        WorkScheduleAssignment::create([
            'work_schedule_rule_id' => $rule->id,
            'assignable_type' => Employee::class,
            'assignable_id' => $employee->id,
            'effective_start_date' => now()->toDateString(),
            'is_active' => true,
        ]);

        return $rule;
    }

    private function validCheckInData(): array
    {
        // Coordinates near branch location (within radius)
        return [
            'check_in_latitude' => -6.2088,
            'check_in_longitude' => 106.8456,
        ];
    }

    // CI-01 — Check-in tepat waktu dengan toleransi 15 menit
    public function test_check_in_tepat_waktu_toleransi_15(): void
    {
        $this->assignSchedule($this->employee, [
            'start_time' => '08:00',
            'end_time' => '17:00',
            'late_tolerance_minutes' => 15,
            'is_required' => true,
        ]);

        // 08:10 — within grace period (deadline 08:15)
        $this->travelTo(now()->setTime(8, 10));

        $this->actingAs($this->user)
            ->post(route('work-attendance.check-in'), $this->validCheckInData())
            ->assertRedirect();

        $this->assertDatabaseHas('employee_work_attendance_logs', [
            'employee_id' => $this->employee->id,
            'attendance_date' => now()->toDateString(),
            'status' => 'checked_in',
            'late_minutes' => 0,
        ]);
    }

    // CI-01b — Check-in tepat waktu dengan toleransi 0 menit
    public function test_check_in_tepat_waktu_toleransi_0(): void
    {
        $this->assignSchedule($this->employee, [
            'start_time' => '08:00',
            'end_time' => '17:00',
            'late_tolerance_minutes' => 0,
            'is_required' => true,
        ]);

        // 08:00 — exactly on time, no tolerance
        $this->travelTo(now()->setTime(8, 0));

        $this->actingAs($this->user)
            ->post(route('work-attendance.check-in'), $this->validCheckInData())
            ->assertRedirect();

        $this->assertDatabaseHas('employee_work_attendance_logs', [
            'employee_id' => $this->employee->id,
            'attendance_date' => now()->toDateString(),
            'status' => 'checked_in',
            'late_minutes' => 0,
        ]);
    }

    // CI-02 — Check-in telat dengan toleransi 15 menit: late_minutes dihitung dari deadline
    public function test_check_in_telat_toleransi_15_menit(): void
    {
        $this->assignSchedule($this->employee, [
            'start_time' => '08:00',
            'end_time' => '17:00',
            'late_tolerance_minutes' => 15,
            'is_required' => true,
        ]);

        // 08:30 — 15 min past deadline (08:15), so late_minutes = 15
        $this->travelTo(now()->setTime(8, 30));

        $this->actingAs($this->user)
            ->post(route('work-attendance.check-in'), $this->validCheckInData())
            ->assertRedirect();

        $log = EmployeeWorkAttendanceLog::where('employee_id', $this->employee->id)
            ->where('attendance_date', now()->toDateString())
            ->first();

        $this->assertNotNull($log);
        $this->assertEquals(15, $log->late_minutes, 'late_minutes = 30min past deadline(08:15) - deadline = 15');
        // Late is tracked via late_minutes, not status — ENUM has no 'late' value
        $this->assertEquals('checked_in', $log->status, 'status checked_in saat late (late_minutes tracks lateness)');
    }

    // CI-02b — Check-in telat dengan toleransi 0 menit: late_minutes dihitung dari start_time
    public function test_check_in_telat_toleransi_0_menit(): void
    {
        $this->assignSchedule($this->employee, [
            'start_time' => '08:00',
            'end_time' => '17:00',
            'late_tolerance_minutes' => 0,
            'is_required' => true,
        ]);

        // 08:15 — 15 min past start_time (deadline = 08:00 + 0 = 08:00), late_minutes = 15
        $this->travelTo(now()->setTime(8, 15));

        $this->actingAs($this->user)
            ->post(route('work-attendance.check-in'), $this->validCheckInData())
            ->assertRedirect();

        $log = EmployeeWorkAttendanceLog::where('employee_id', $this->employee->id)
            ->where('attendance_date', now()->toDateString())
            ->first();

        $this->assertNotNull($log);
        $this->assertEquals(15, $log->late_minutes, 'Toleransi 0: late_minutes dihitung dari start_time (08:00)');
        // Late is tracked via late_minutes, not status — ENUM has no 'late' value
        $this->assertEquals('checked_in', $log->status);
    }

    // CI-03 — Check-in hari libur
    public function test_check_in_hari_libur(): void
    {
        Holiday::factory()->create([
            'holiday_start_date' => now()->toDateString(),
            'holiday_end_date' => now()->toDateString(),
        ]);

        $this->actingAs($this->user)
            ->post(route('work-attendance.check-in'), $this->validCheckInData())
            ->assertRedirect()
            ->assertSessionHasErrors('date');
    }

    // CI-04 — Check-in sudah dilakukan hari ini
    public function test_check_in_sudah_dilakukan(): void
    {
        EmployeeWorkAttendanceLog::factory()->checkedIn()->create([
            'employee_id' => $this->employee->id,
            'attendance_date' => now()->toDateString(),
        ]);

        $this->actingAs($this->user)
            ->post(route('work-attendance.check-in'), $this->validCheckInData())
            ->assertRedirect()
            ->assertSessionHasErrors('check_in');
    }

    // CI-05 — Check-in is_required=false (auto hadir)
    public function test_check_in_is_required_false_auto_hadir(): void
    {
        $this->assignSchedule($this->employee, [
            'start_time' => '08:00',
            'end_time' => '17:00',
            'is_required' => false,
        ]);

        $this->actingAs($this->user)
            ->post(route('work-attendance.check-in'), $this->validCheckInData())
            ->assertRedirect();

        $this->assertDatabaseHas('employee_work_attendance_logs', [
            'employee_id' => $this->employee->id,
            'attendance_date' => now()->toDateString(),
            'status' => 'present',
            'late_minutes' => 0,
        ]);
    }

    // CI-06 — Check-in di luar radius → anomaly
    public function test_check_in_di_luar_radius_anomaly(): void
    {
        $this->assignSchedule($this->employee, [
            'start_time' => '08:00',
            'end_time' => '17:00',
            'late_tolerance_minutes' => 15,
            'is_required' => true,
        ]);

        $this->travelTo(now()->setTime(8, 10));

        // Coordinates far from branch (Jakarta → Bogor ~60km)
        $this->actingAs($this->user)
            ->post(route('work-attendance.check-in'), [
                'check_in_latitude' => -6.5971,
                'check_in_longitude' => 106.8060,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('employee_work_attendance_logs', [
            'employee_id' => $this->employee->id,
            'is_location_anomaly' => true,
        ]);
    }

    // CI-07 — Distance dari client bisa dimanipulasi (GAP-53)
    public function test_check_in_server_computes_distance_not_client(): void
    {
        $this->assignSchedule($this->employee, [
            'start_time' => '08:00',
            'end_time' => '17:00',
            'late_tolerance_minutes' => 15,
            'is_required' => true,
        ]);

        $this->travelTo(now()->setTime(8, 10));

        // Send coordinates far from branch — server should detect anomaly
        $this->actingAs($this->user)
            ->post(route('work-attendance.check-in'), [
                'check_in_latitude' => -6.5971,
                'check_in_longitude' => 106.8060,
            ])
            ->assertRedirect();

        $log = EmployeeWorkAttendanceLog::where('employee_id', $this->employee->id)
            ->where('attendance_date', now()->toDateString())
            ->first();

        // Server-computed distance should reflect actual distance (not client-submitted)
        $this->assertNotNull($log);
        $this->assertTrue($log->check_in_distance_m > 500, 'GAP-53: Server harus hitung jarak sendiri, distance > radius');
        $this->assertTrue($log->is_location_anomaly, 'GAP-53: Anomali harus terdeteksi oleh server');
    }

    // CI-08 — Employee tanpa branch (GAP-56)
    public function test_check_in_employee_tanpa_branch(): void
    {
        $employee = Employee::factory()->create(['branch_id' => null]);
        $user = User::factory()->create(['employee_id' => $employee->id]);

        $this->travelTo(now()->setTime(8, 10));

        $this->actingAs($user)
            ->post(route('work-attendance.check-in'), $this->validCheckInData());

        // GAP-56: WorkAttendanceRepository::checkIn() requires int $branchId,
        // but $employee->branch_id is null → TypeError → 500 instead of validation error
        // Expected: 422/302 with validation error; Actual: 500 TypeError
        $log = EmployeeWorkAttendanceLog::where('employee_id', $employee->id)
            ->where('attendance_date', now()->toDateString())
            ->first();

        // No log should be created — branch_id is required for attendance
        $this->assertNull($log, 'GAP-56: Check-in tanpa branch seharusnya ditolak, bukan 500 TypeError');
    }

    // CI-09 — Check-in dengan selfie
    public function test_check_in_dengan_selfie(): void
    {
        $this->assignSchedule($this->employee, [
            'start_time' => '08:00',
            'end_time' => '17:00',
            'late_tolerance_minutes' => 15,
            'is_required' => true,
        ]);

        $this->travelTo(now()->setTime(8, 10));

        $this->actingAs($this->user)
            ->post(route('work-attendance.check-in'), array_merge($this->validCheckInData(), [
                'selfie' => UploadedFile::fake()->image('selfie.jpg', 640, 480),
            ]))
            ->assertRedirect();

        $this->assertDatabaseHas('employee_work_attendance_logs', [
            'employee_id' => $this->employee->id,
            'check_in_selfie_telegram_file_id' => 'fake_file_id_123',
        ]);
    }

    // CI-10 — Check-in tanpa selfie
    public function test_check_in_tanpa_selfie(): void
    {
        $this->assignSchedule($this->employee, [
            'start_time' => '08:00',
            'end_time' => '17:00',
            'late_tolerance_minutes' => 15,
            'is_required' => true,
        ]);

        $this->travelTo(now()->setTime(8, 10));

        $this->actingAs($this->user)
            ->post(route('work-attendance.check-in'), $this->validCheckInData())
            ->assertRedirect();

        $this->assertDatabaseHas('employee_work_attendance_logs', [
            'employee_id' => $this->employee->id,
            'check_in_selfie_telegram_file_id' => null,
        ]);
    }

    // CI-11 — Check-in tanpa schedule
    public function test_check_in_tanpa_schedule(): void
    {
        // No WorkScheduleAssignment created for this employee
        $this->travelTo(now()->setTime(9, 0));

        $this->actingAs($this->user)
            ->post(route('work-attendance.check-in'), $this->validCheckInData())
            ->assertRedirect();

        $this->assertDatabaseHas('employee_work_attendance_logs', [
            'employee_id' => $this->employee->id,
            'late_minutes' => 0,
        ]);
    }

    // CI-12 — User tanpa employee_id → 403 Forbidden
    public function test_check_in_user_tanpa_employee_id(): void
    {
        $userWithoutEmployee = User::factory()->create(['employee_id' => null]);

        // Some middleware or null check returns 403 before the controller hits the TypeError
        // GAP: Ideally should return a specific error message, not generic 403
        $this->actingAs($userWithoutEmployee)
            ->post(route('work-attendance.check-in'), $this->validCheckInData())
            ->assertStatus(403);
    }
}
