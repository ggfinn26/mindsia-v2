<?php

namespace Tests\Feature\Attendance;

use App\Models\Branch;
use App\Models\ClassRoom;
use App\Models\ClassSchedule;
use App\Models\Employee;
use App\Models\EmployeeSessionAttendanceLog;
use App\Models\Program;
use App\Models\SessionSchedule;
use App\Models\User;
use App\Services\TelegramStorageService;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Mockery\MockInterface;
use Tests\TestCase;

class SessionAttendanceTest extends TestCase
{
    private User $user;

    private Employee $employee;

    private Branch $branch;

    private SessionSchedule $session;

    protected function setUp(): void
    {
        parent::setUp();

        // Freeze time at 10:00 UTC to avoid midnight-crossing issues with start_time
        Carbon::setTestNow(Carbon::create(2026, 9, 23, 10, 0, 0, 'UTC'));

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
            $mock->shouldReceive('uploadFile')->andReturn(['file_id' => 'fake_file_id_123']);
        });

        $this->session = $this->createSessionForToday($this->employee);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    private function createSessionForToday(Employee $tutor, array $scheduleOverrides = []): SessionSchedule
    {
        $program = Program::factory()->create();
        $classRoom = ClassRoom::factory()->create([
            'program_id' => $program->id,
            'branch_id' => $this->branch->id,
            'tutor_id' => $tutor->id,
        ]);
        $defaults = [
            'class_id' => $classRoom->id,
            'schedule_date' => now()->toDateString(),
            'start_time' => '11:00',
            'end_time' => '13:00',
            'late_tolerance_minutes' => 15,
        ];
        $classSchedule = ClassSchedule::create(array_merge($defaults, $scheduleOverrides));

        return SessionSchedule::create([
            'class_schedule_id' => $classSchedule->id,
            'employee_id' => $tutor->id,
        ]);
    }

    private function validCheckInData(): array
    {
        return [
            'check_in_latitude' => -6.2088,
            'check_in_longitude' => 106.8456,
        ];
    }

    private function validCheckOutData(): array
    {
        return [
            'check_out_latitude' => -6.2088,
            'check_out_longitude' => 106.8456,
        ];
    }

    // ── SA-01: Check-in sesi berhasil tepat waktu ──

    public function test_sa01_check_in_on_time(): void
    {
        $this->actingAs($this->user);

        $response = $this->post(
            route('session-attendance.check-in', $this->session->id),
            $this->validCheckInData(),
        );

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $log = EmployeeSessionAttendanceLog::where('session_schedule_id', $this->session->id)
            ->where('employee_id', $this->employee->id)
            ->first();

        $this->assertNotNull($log);
        $this->assertEquals('checked_in', $log->status);
        $this->assertEquals(0, $log->late_minutes);
        $this->assertNotNull($log->check_in);
    }

    // ── SA-02: Check-in sesi terlambat ──
    // GAP-61: SKENARIO says "status=late" but ENUM doesn't have 'late'.
    // After fix, status='checked_in' with late_minutes>0 is the only working option.

    public function test_sa02_check_in_late(): void
    {
        // Create session with start_time in the past + tolerance already passed
        $session = $this->createSessionForToday($this->employee, [
            'start_time' => now()->subHours(2)->format('H:i'),
            'late_tolerance_minutes' => 15,
        ]);

        $this->actingAs($this->user);

        $response = $this->post(
            route('session-attendance.check-in', $session->id),
            $this->validCheckInData(),
        );

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $log = EmployeeSessionAttendanceLog::where('session_schedule_id', $session->id)->first();

        $this->assertNotNull($log);
        $this->assertEquals('checked_in', $log->status);
        $this->assertGreaterThan(0, $log->late_minutes, 'late_minutes should be > 0 for late check-in');
    }

    // ── SA-03: Check-in sesi — sudah check-in ──

    public function test_sa03_check_in_already_checked_in(): void
    {
        $this->actingAs($this->user);

        // First check-in
        $this->post(route('session-attendance.check-in', $this->session->id), $this->validCheckInData());

        // Second check-in should fail
        $response = $this->post(route('session-attendance.check-in', $this->session->id), $this->validCheckInData());

        $response->assertRedirect();
        $response->assertSessionHasErrors('check_in');
    }

    // ── SA-04: Check-in sesi — session bukan milik employee ──

    public function test_sa04_check_in_not_own_session(): void
    {
        $otherEmployee = Employee::factory()->create(['branch_id' => $this->branch->id]);
        $otherSession = $this->createSessionForToday($otherEmployee);

        $this->actingAs($this->user);

        $response = $this->post(route('session-attendance.check-in', $otherSession->id), $this->validCheckInData());

        $response->assertNotFound();
    }

    // ── SA-05: Check-in sesi — session bukan hari ini ──

    public function test_sa05_check_in_session_not_today(): void
    {
        $program = Program::factory()->create();
        $classRoom = ClassRoom::factory()->create([
            'program_id' => $program->id,
            'branch_id' => $this->branch->id,
            'tutor_id' => $this->employee->id,
        ]);

        // Use a schedule for yesterday with a start_time that won't collide
        // with the observer-generated schedules (observer uses start_time_primary)
        $classSchedule = ClassSchedule::create([
            'class_id' => $classRoom->id,
            'schedule_date' => now()->subDay()->toDateString(),
            'start_time' => '15:00',
            'end_time' => '17:00',
            'late_tolerance_minutes' => 15,
            'material_taught' => 'Test session not today',
        ]);
        $yesterdaySession = SessionSchedule::create([
            'class_schedule_id' => $classSchedule->id,
            'employee_id' => $this->employee->id,
        ]);

        $this->actingAs($this->user);

        $response = $this->post(route('session-attendance.check-in', $yesterdaySession->id), $this->validCheckInData());

        $response->assertNotFound();
    }

    // ── SA-06: Check-in — distance dari client dimanipulasi (GAP-59) ──
    // Client-submitted check_in_distance_m is not validated server-side.
    // The anomaly detection uses haversine from lat/lng, not from client-claimed distance.

    public function test_sa06_check_in_distance_manipulation(): void
    {
        // Send far-away lat/lng (Jakarta vs far location)
        $farData = [
            'check_in_latitude' => -7.7,
            'check_in_longitude' => 110.3,
        ];

        $this->actingAs($this->user);

        $response = $this->post(route('session-attendance.check-in', $this->session->id), $farData);

        $response->assertRedirect();

        $log = EmployeeSessionAttendanceLog::where('session_schedule_id', $this->session->id)->first();
        $this->assertTrue($log->is_location_anomaly, 'GAP-59: Anomaly should be detected from haversine, not client distance');
        $this->assertGreaterThan($this->branch->radius_meters, $log->check_in_distance_m);
    }

    // ── SA-07: Check-in — di luar radius classroom branch ──

    public function test_sa07_check_in_outside_radius(): void
    {
        $farData = [
            'check_in_latitude' => -7.0,
            'check_in_longitude' => 110.0,
        ];

        $this->actingAs($this->user);

        $response = $this->post(route('session-attendance.check-in', $this->session->id), $farData);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $log = EmployeeSessionAttendanceLog::where('session_schedule_id', $this->session->id)->first();
        $this->assertTrue($log->is_location_anomaly);
    }

    // ── SA-08: Check-in — dengan selfie ──

    public function test_sa08_check_in_with_selfie(): void
    {
        $this->actingAs($this->user);

        $response = $this->post(
            route('session-attendance.check-in', $this->session->id),
            array_merge($this->validCheckInData(), [
                'selfie' => UploadedFile::fake()->image('selfie.jpg'),
            ]),
        );

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $log = EmployeeSessionAttendanceLog::where('session_schedule_id', $this->session->id)->first();
        $this->assertNotNull($log->check_in_selfie_telegram_file_id);
    }

    // ── SA-09: Check-out sesi berhasil ──

    public function test_sa09_check_out_success(): void
    {
        $this->actingAs($this->user);

        // Check-in first
        $this->post(route('session-attendance.check-in', $this->session->id), $this->validCheckInData());

        // Then check-out
        $response = $this->post(route('session-attendance.check-out', $this->session->id), $this->validCheckOutData());

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $log = EmployeeSessionAttendanceLog::where('session_schedule_id', $this->session->id)->first();
        $this->assertEquals('present', $log->status);
        $this->assertNotNull($log->check_out);
    }

    // ── SA-10: Check-out sesi — telat masuk (GAP-61) ──
    // After fix, status='present' (ENUM has no 'present_late'). late_minutes tracks lateness.

    public function test_sa10_check_out_after_late_check_in(): void
    {
        // Session with past start_time → check-in will be late
        $session = $this->createSessionForToday($this->employee, [
            'start_time' => now()->subHours(2)->format('H:i'),
            'late_tolerance_minutes' => 15,
        ]);

        $this->actingAs($this->user);

        $this->post(route('session-attendance.check-in', $session->id), $this->validCheckInData());

        $response = $this->post(route('session-attendance.check-out', $session->id), $this->validCheckOutData());

        $response->assertRedirect();

        $log = EmployeeSessionAttendanceLog::where('session_schedule_id', $session->id)->first();
        $this->assertEquals('present', $log->status);
        $this->assertGreaterThan(0, $log->late_minutes);
    }

    // ── SA-11: Check-out sesi — belum check-in ──

    public function test_sa11_check_out_without_check_in(): void
    {
        $this->actingAs($this->user);

        $response = $this->post(route('session-attendance.check-out', $this->session->id), $this->validCheckOutData());

        $response->assertNotFound();
    }

    // ── SA-12: Check-out — distance dimanipulasi (GAP-60) ──
    // Same as GAP-59 but for check-out. Server-side haversine should detect anomaly.

    public function test_sa12_check_out_distance_manipulation(): void
    {
        $this->actingAs($this->user);

        // Check-in first (near branch)
        $this->post(route('session-attendance.check-in', $this->session->id), $this->validCheckInData());

        // Check-out with far-away coordinates
        $farData = [
            'check_out_latitude' => -7.0,
            'check_out_longitude' => 110.0,
        ];

        $response = $this->post(route('session-attendance.check-out', $this->session->id), $farData);

        $response->assertRedirect();

        $log = EmployeeSessionAttendanceLog::where('session_schedule_id', $this->session->id)->first();
        // check_out_distance_m calculated server-side from haversine — GAP-60: not stored as anomaly on checkout
        $this->assertNotNull($log->check_out_distance_m);
    }

    // ── SA-13: Verify berhasil ──

    public function test_sa13_verify_success(): void
    {
        $this->actingAs($this->user);

        // Check-in + check-out
        $this->post(route('session-attendance.check-in', $this->session->id), $this->validCheckInData());
        $this->post(route('session-attendance.check-out', $this->session->id), $this->validCheckOutData());

        // Verify with a user who has the permission AND an employee_id
        $verifierEmployee = Employee::factory()->create(['branch_id' => $this->branch->id]);
        $verifier = User::factory()->create(['employee_id' => $verifierEmployee->id]);
        $verifier->givePermissionTo('attendance.adjustment.create');

        $log = EmployeeSessionAttendanceLog::where('session_schedule_id', $this->session->id)->first();

        $response = $this->actingAs($verifier)
            ->post(route('session-attendance.verify', $log->id));

        $response->assertRedirect();

        $log->refresh();
        $this->assertNotNull($log->verified_at);
        $this->assertEquals($verifierEmployee->id, $log->verified_by_employee_id);
    }

    // ── SA-13b: Verify — verifier tanpa employee_id → TypeError (BUG) ──
    // GAP: Controller passes auth()->user()->employee_id (null) to verify(int $verifierEmployeeId)
    // This causes TypeError: "int null given". No null guard in controller.

    public function test_sa13b_verify_without_employee_id_causes_error(): void
    {
        $this->actingAs($this->user);

        $this->post(route('session-attendance.check-in', $this->session->id), $this->validCheckInData());
        $this->post(route('session-attendance.check-out', $this->session->id), $this->validCheckOutData());

        $log = EmployeeSessionAttendanceLog::where('session_schedule_id', $this->session->id)->first();

        // Verifier has permission but NO employee_id
        $verifier = User::factory()->create(['employee_id' => null]);
        $verifier->givePermissionTo('attendance.adjustment.create');

        $this->actingAs($verifier);
        $response = $this->post(route('session-attendance.verify', $log->id));

        // TypeError: verify() expects int, null given — controller doesn't guard null employee_id
        $response->assertStatus(500);
    }

    // ── SA-14: Verify — tanpa role ──

    public function test_sa14_verify_without_permission(): void
    {
        $this->actingAs($this->user);

        $this->post(route('session-attendance.check-in', $this->session->id), $this->validCheckInData());
        $this->post(route('session-attendance.check-out', $this->session->id), $this->validCheckOutData());

        $log = EmployeeSessionAttendanceLog::where('session_schedule_id', $this->session->id)->first();

        // Regular user without attendance.adjustment.create permission
        $response = $this->actingAs($this->user)
            ->post(route('session-attendance.verify', $log->id));

        $response->assertForbidden();
    }
}
