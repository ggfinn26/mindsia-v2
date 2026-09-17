<?php

namespace Tests\Feature\Attendance;

use App\Models\Branch;
use App\Models\Employee;
use App\Models\EmployeeWorkAttendanceLog;
use App\Models\User;
use App\Models\WorkScheduleAssignment;
use App\Models\WorkScheduleRule;
use App\Services\TelegramStorageService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\MockInterface;
use Tests\TestCase;

class CheckOutTest extends TestCase
{
    use RefreshDatabase;

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

        $this->mock(TelegramStorageService::class, function (MockInterface $mock) {
            $mock->shouldReceive('uploadPhoto')->andReturn('fake_file_id_456');
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

    private function validCheckOutData(): array
    {
        return [
            'check_out_latitude' => -6.2088,
            'check_out_longitude' => 106.8456,
        ];
    }

    private function createCheckedInLog(Employee $employee, array $overrides = []): EmployeeWorkAttendanceLog
    {
        return EmployeeWorkAttendanceLog::factory()->checkedIn()->create(array_merge([
            'employee_id' => $employee->id,
            'branch_id' => $employee->branch_id,
            'attendance_date' => now()->toDateString(),
        ], $overrides));
    }

    // CO-01 — Check-out berhasil tepat waktu
    public function test_check_out_berhasil_tepat_waktu(): void
    {
        $this->assignSchedule($this->employee, [
            'start_time' => '08:00',
            'end_time' => '17:00',
            'early_leave_tolerance_minutes' => 15,
        ]);

        $log = $this->createCheckedInLog($this->employee, ['late_minutes' => 0]);

        // 17:00 — exactly on time
        $this->travelTo(now()->setTime(17, 0));

        $this->actingAs($this->user)
            ->post(route('work-attendance.check-out'), $this->validCheckOutData())
            ->assertRedirect();

        $this->assertDatabaseHas('employee_work_attendance_logs', [
            'id' => $log->id,
            'status' => 'present',
            'early_leave_minutes' => 0,
        ]);

        $this->assertNotNull($log->fresh()->check_out);
    }

    // CO-02 — Check-out berhasil tapi telat masuk (GAP-54)
    public function test_check_out_telat_masuk_status_present_late(): void
    {
        $this->assignSchedule($this->employee, [
            'start_time' => '08:00',
            'end_time' => '17:00',
            'early_leave_tolerance_minutes' => 15,
        ]);

        $log = $this->createCheckedInLog($this->employee, ['late_minutes' => 30]);

        $this->travelTo(now()->setTime(17, 0));

        $this->actingAs($this->user)
            ->post(route('work-attendance.check-out'), $this->validCheckOutData())
            ->assertRedirect();

        $freshLog = $log->fresh();

        // ENUM has no 'present_late' — status is always 'present' after check-out;
        // late_minutes field tracks whether the employee was late at check-in
        $this->assertEquals('present', $freshLog->status, 'status present setelah check-out (late_minutes tracks lateness)');
        $this->assertEquals(30, $freshLog->late_minutes, 'late_minutes tetap tercatat dari check-in');
    }

    // CO-03 — Check-out terlalu awal (GAP-57)
    public function test_check_out_terlalu_awal_diblokir(): void
    {
        $this->assignSchedule($this->employee, [
            'start_time' => '08:00',
            'end_time' => '17:00',
            'early_leave_tolerance_minutes' => 15,
        ]);

        $this->createCheckedInLog($this->employee);

        // 16:00 — 1 hour before end_time, tolerance is 15 min
        $this->travelTo(now()->setTime(16, 0));

        $this->actingAs($this->user)
            ->post(route('work-attendance.check-out'), $this->validCheckOutData())
            ->assertRedirect()
            ->assertSessionHasErrors('check_out');

        // Verify check_out was NOT saved
        $log = EmployeeWorkAttendanceLog::where('employee_id', $this->employee->id)
            ->where('attendance_date', now()->toDateString())
            ->first();

        $this->assertNull($log->check_out, 'GAP-57: check_out tidak boleh tersimpan jika terlalu awal');
    }

    // CO-04 — Check-out belum check-in
    public function test_check_out_belum_check_in(): void
    {
        // No check-in log exists for today
        $this->travelTo(now()->setTime(17, 0));

        $this->actingAs($this->user)
            ->post(route('work-attendance.check-out'), $this->validCheckOutData())
            ->assertRedirect()
            ->assertSessionHasErrors('check_out');
    }

    // CO-05 — Distance dari client bisa dimanipulasi (GAP-58)
    public function test_check_out_server_computes_distance_not_client(): void
    {
        $this->assignSchedule($this->employee, [
            'start_time' => '08:00',
            'end_time' => '17:00',
            'early_leave_tolerance_minutes' => 15,
        ]);

        $this->createCheckedInLog($this->employee);

        $this->travelTo(now()->setTime(17, 0));

        // Send coordinates far from branch
        $this->actingAs($this->user)
            ->post(route('work-attendance.check-out'), [
                'check_out_latitude' => -6.5971,
                'check_out_longitude' => 106.8060,
            ])
            ->assertRedirect();

        $log = EmployeeWorkAttendanceLog::where('employee_id', $this->employee->id)
            ->where('attendance_date', now()->toDateString())
            ->first();

        // GAP-58: Server should compute distance, not trust client
        $this->assertNotNull($log);
        $this->assertTrue($log->check_out_distance_m > 500, 'GAP-58: Server harus hitung jarak sendiri');
    }

    // CO-06 — Check-out dengan selfie
    public function test_check_out_dengan_selfie(): void
    {
        $this->assignSchedule($this->employee, [
            'start_time' => '08:00',
            'end_time' => '17:00',
            'early_leave_tolerance_minutes' => 15,
        ]);

        $this->createCheckedInLog($this->employee);

        $this->travelTo(now()->setTime(17, 0));

        $this->actingAs($this->user)
            ->post(route('work-attendance.check-out'), array_merge($this->validCheckOutData(), [
                'selfie' => \Illuminate\Http\UploadedFile::fake()->image('selfie_out.jpg', 640, 480),
            ]))
            ->assertRedirect();

        $this->assertDatabaseHas('employee_work_attendance_logs', [
            'employee_id' => $this->employee->id,
            'check_out_selfie_telegram_file_id' => 'fake_file_id_456',
        ]);
    }

    // CO-07 — Check-out tanpa selfie
    public function test_check_out_tanpa_selfie(): void
    {
        $this->assignSchedule($this->employee, [
            'start_time' => '08:00',
            'end_time' => '17:00',
            'early_leave_tolerance_minutes' => 15,
        ]);

        $this->createCheckedInLog($this->employee);

        $this->travelTo(now()->setTime(17, 0));

        $this->actingAs($this->user)
            ->post(route('work-attendance.check-out'), $this->validCheckOutData())
            ->assertRedirect();

        $this->assertDatabaseHas('employee_work_attendance_logs', [
            'employee_id' => $this->employee->id,
            'check_out_selfie_telegram_file_id' => null,
        ]);
    }

    // CO-08 — Early leave tercatat (checkout tepat sebelum threshold)
    public function test_check_out_early_leave_tercatat(): void
    {
        $this->assignSchedule($this->employee, [
            'start_time' => '08:00',
            'end_time' => '17:00',
            'early_leave_tolerance_minutes' => 0,
        ]);

        $this->createCheckedInLog($this->employee);

        // 16:30 — 30 min before end_time, 0 tolerance
        // calculateEarlyLeaveMinutes: 16:30 < 17:00 - 0 = 17:00 → early_leave = 30
        // But service blocks when early_leave_minutes > 0 → error
        // Per flow.md: "Tidak ada pulang cepat (early_leave_minutes dihapus)"
        // So this scenario is blocked per the latest decision
        $this->travelTo(now()->setTime(16, 30));

        $this->actingAs($this->user)
            ->post(route('work-attendance.check-out'), $this->validCheckOutData())
            ->assertRedirect()
            ->assertSessionHasErrors('check_out');
    }

    // CO-09 — Check-out tanpa schedule
    public function test_check_out_tanpa_schedule(): void
    {
        $this->createCheckedInLog($this->employee);

        $this->travelTo(now()->setTime(17, 0));

        // No schedule → early_leave_minutes = 0 → allowed
        $this->actingAs($this->user)
            ->post(route('work-attendance.check-out'), $this->validCheckOutData())
            ->assertRedirect();

        $this->assertDatabaseHas('employee_work_attendance_logs', [
            'employee_id' => $this->employee->id,
            'early_leave_minutes' => 0,
        ]);
    }

    // CO-10 — User tanpa employee_id → 403 Forbidden
    public function test_check_out_user_tanpa_employee_id(): void
    {
        $userWithoutEmployee = User::factory()->create(['employee_id' => null]);

        // Some middleware or null check returns 403 before TypeError (same as CI-12)
        // GAP: Ideally should return a specific error message, not generic 403
        $this->actingAs($userWithoutEmployee)
            ->post(route('work-attendance.check-out'), $this->validCheckOutData())
            ->assertStatus(403);
    }
}
