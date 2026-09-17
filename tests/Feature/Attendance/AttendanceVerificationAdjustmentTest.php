<?php

namespace Tests\Feature\Attendance;

use App\Models\Branch;
use App\Models\Employee;
use App\Models\EmployeeWorkAttendanceLog;
use App\Models\EmploymentStatus;
use App\Models\Position;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\MockInterface;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

// Flow: attendance-verification-adjustment (verify, adjust)
// Scenarios: VA-01 to VA-12
class AttendanceVerificationAdjustmentTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Employee $employee;

    private Branch $branch;

    protected function setUp(): void
    {
        parent::setUp();

        $this->branch = Branch::factory()->create();

        $this->employee = Employee::factory()->create([
            'branch_id' => $this->branch->id,
        ]);

        $this->user = User::factory()->create([
            'employee_id' => $this->employee->id,
        ]);
    }

    private function assignPosition(Employee $employee, string $roleName, int $hierarchyOrder): EmploymentStatus
    {
        $role = $roleName ? Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']) : null;

        $position = Position::create([
            'position_name' => "Pos {$roleName} {$employee->id}",
            'role_id' => $role?->id,
            'hierarchy_order' => $hierarchyOrder,
        ]);

        $status = EmploymentStatus::create([
            'employees_id' => $employee->id,
            'type_employment' => 'Kontrak',
            'join_date' => now()->subYear(),
            'position_id' => $position->id,
            'contract_start_date' => now()->subYear(),
            'contract_end_date' => now()->addYear(),
            'setup_incomplete' => false,
        ]);

        $employee->refresh();

        return $status;
    }

    private function grantAdjustmentPermission(User $user): void
    {
        $permission = Permission::firstOrCreate([
            'name' => 'attendance.adjustment.create',
            'guard_name' => 'web',
        ]);
        $user->givePermissionTo($permission);
    }

    private function createCheckedInLog(Employee $employee, string $date = null): EmployeeWorkAttendanceLog
    {
        return EmployeeWorkAttendanceLog::factory()->checkedIn()->create([
            'employee_id' => $employee->id,
            'branch_id' => $this->branch->id,
            'attendance_date' => $date ?? now()->toDateString(),
        ]);
    }

    // =============================================
    // VERIFY
    // =============================================

    // VA-01: Verify attendance — success
    public function test_va01_verify_berhasil(): void
    {
        $this->assignPosition($this->employee, 'HRR', 2);
        $this->grantAdjustmentPermission($this->user);

        $log = $this->createCheckedInLog($this->employee);

        $response = $this->actingAs($this->user)
            ->post(route('work-attendance.verify', $log));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('employee_work_attendance_logs', [
            'id' => $log->id,
            'verified_by_employee_id' => $this->employee->id,
        ]);

        // verified_at should be set (not null)
        $this->assertNotNull($log->fresh()->verified_at);
    }

    // VA-02: Verify without permission → 403
    public function test_va02_verify_tanpa_permission(): void
    {
        $this->assignPosition($this->employee, 'PIC', 4);
        // No grantAdjustmentPermission — user does NOT have attendance.adjustment.create

        $log = $this->createCheckedInLog($this->employee);

        $response = $this->actingAs($this->user)
            ->post(route('work-attendance.verify', $log));

        $response->assertStatus(403);

        $this->assertDatabaseHas('employee_work_attendance_logs', [
            'id' => $log->id,
            'verified_by_employee_id' => null,
        ]);
    }

    // VA-03: Re-verification overwrites previous verifier (GAP — no guard against re-verify)
    public function test_va03_reverify_overwrites_previous_verifier(): void
    {
        $this->assignPosition($this->employee, 'HRR', 2);
        $this->grantAdjustmentPermission($this->user);

        $log = $this->createCheckedInLog($this->employee);

        // First verify by this user
        $this->actingAs($this->user)
            ->post(route('work-attendance.verify', $log));

        $this->assertDatabaseHas('employee_work_attendance_logs', [
            'id' => $log->id,
            'verified_by_employee_id' => $this->employee->id,
        ]);

        // Second verify by a different user — overwrites
        $otherEmployee = Employee::factory()->create(['branch_id' => $this->branch->id]);
        $this->assignPosition($otherEmployee, 'CHRO', 1);
        $otherUser = User::factory()->create(['employee_id' => $otherEmployee->id]);
        $this->grantAdjustmentPermission($otherUser);

        $this->actingAs($otherUser)
            ->post(route('work-attendance.verify', $log));

        // GAP: verifier is overwritten — should probably prevent re-verification
        $this->assertDatabaseHas('employee_work_attendance_logs', [
            'id' => $log->id,
            'verified_by_employee_id' => $otherEmployee->id,
        ]);
    }

    // =============================================
    // ADJUST
    // =============================================

    // VA-04: Adjust status successfully with adjustment history
    public function test_va04_adjust_status_berhasil(): void
    {
        $this->assignPosition($this->employee, 'HRR', 2);
        $this->grantAdjustmentPermission($this->user);

        $log = $this->createCheckedInLog($this->employee);

        $response = $this->actingAs($this->user)
            ->post(route('work-attendance.adjust', $log), [
                'status' => 'sick',
                'adjustment_reason' => 'Ternyata sakit, bukan hadir',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Log updated
        $this->assertDatabaseHas('employee_work_attendance_logs', [
            'id' => $log->id,
            'status' => 'sick',
        ]);

        // Adjustment history created
        $this->assertDatabaseHas('employee_work_attendance_adjustment_histories', [
            'attendance_log_id' => $log->id,
            'adjusted_by_employee_id' => $this->employee->id,
            'previous_status' => 'checked_in',
            'new_status' => 'sick',
            'adjustment_reason' => 'Ternyata sakit, bukan hadir',
        ]);
    }

    // VA-05: Adjust check-in and check-out times
    public function test_va05_adjust_check_in_check_out_time(): void
    {
        $this->assignPosition($this->employee, 'HRR', 2);
        $this->grantAdjustmentPermission($this->user);

        $log = EmployeeWorkAttendanceLog::factory()->present()->create([
            'employee_id' => $this->employee->id,
            'branch_id' => $this->branch->id,
            'attendance_date' => now()->toDateString(),
            'check_in' => now()->setTime(8, 30)->format('Y-m-d H:i:s'),
            'check_out' => now()->setTime(17, 0)->format('Y-m-d H:i:s'),
        ]);

        $newCheckIn = now()->setTime(9, 0)->format('Y-m-d H:i:s');
        $newCheckOut = now()->setTime(17, 30)->format('Y-m-d H:i:s');

        $response = $this->actingAs($this->user)
            ->post(route('work-attendance.adjust', $log), [
                'check_in' => $newCheckIn,
                'check_out' => $newCheckOut,
                'adjustment_reason' => 'Koreksi waktu absen',
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('employee_work_attendance_logs', [
            'id' => $log->id,
            'check_in' => $newCheckIn,
            'check_out' => $newCheckOut,
        ]);

        // Adjustment history captures previous times
        $this->assertDatabaseHas('employee_work_attendance_adjustment_histories', [
            'attendance_log_id' => $log->id,
            'previous_check_in' => $log->check_in,
            'new_check_in' => $newCheckIn,
        ]);
    }

    // VA-06: Adjust without adjustment_reason → validation error
    public function test_va06_adjust_tanpa_adjustment_reason(): void
    {
        $this->assignPosition($this->employee, 'HRR', 2);
        $this->grantAdjustmentPermission($this->user);

        $log = $this->createCheckedInLog($this->employee);

        $response = $this->actingAs($this->user)
            ->post(route('work-attendance.adjust', $log), [
                'status' => 'sick',
                // adjustment_reason missing — required field
            ]);

        $response->assertSessionHasErrors('adjustment_reason');

        // Log should NOT be updated
        $this->assertDatabaseHas('employee_work_attendance_logs', [
            'id' => $log->id,
            'status' => 'checked_in',
        ]);
    }

    // VA-07: Adjust with invalid ENUM (present_late, late) — GAP: not in DB enum
    public function test_va07_adjust_invalid_enum_present_late(): void
    {
        $this->assignPosition($this->employee, 'HRR', 2);
        $this->grantAdjustmentPermission($this->user);

        $log = $this->createCheckedInLog($this->employee);

        // AdjustAttendanceRequest allows 'present_late' but DB enum doesn't have it
        $response = $this->actingAs($this->user)
            ->post(route('work-attendance.adjust', $log), [
                'status' => 'present_late',
                'adjustment_reason' => 'Koreksi status',
            ]);

        // FormRequest validation passes (present_late is in the in: rule)
        // But DB will reject it — this is a GAP
        // Expect either validation error or DB error depending on MySQL strict mode
        // Since AdjustAttendanceRequest allows it, we expect redirect (success from controller)
        // but DB will throw a PDOException for invalid enum value
        $response->assertStatus(500);
    }

    // VA-08: Adjust without permission → 403
    public function test_va08_adjust_tanpa_permission(): void
    {
        $this->assignPosition($this->employee, 'PIC', 4);
        // No grantAdjustmentPermission

        $log = $this->createCheckedInLog($this->employee);

        $response = $this->actingAs($this->user)
            ->post(route('work-attendance.adjust', $log), [
                'status' => 'sick',
                'adjustment_reason' => 'Koreksi status',
            ]);

        $response->assertStatus(403);

        $this->assertDatabaseHas('employee_work_attendance_logs', [
            'id' => $log->id,
            'status' => 'checked_in',
        ]);
    }

    // VA-09: Adjust for non-existent log → 404
    public function test_va09_adjust_log_tidak_ada(): void
    {
        $this->assignPosition($this->employee, 'HRR', 2);
        $this->grantAdjustmentPermission($this->user);

        $nonExistentId = 99999;

        $response = $this->actingAs($this->user)
            ->post(route('work-attendance.adjust', $nonExistentId), [
                'status' => 'sick',
                'adjustment_reason' => 'Test',
            ]);

        $response->assertStatus(404);
    }

    // VA-10: Adjust history snapshot captures previous values correctly
    public function test_va10_adjust_history_snapshot_benar(): void
    {
        $this->assignPosition($this->employee, 'HRR', 2);
        $this->grantAdjustmentPermission($this->user);

        $originalCheckIn = now()->setTime(8, 15)->format('Y-m-d H:i:s');
        $originalCheckOut = now()->setTime(17, 0)->format('Y-m-d H:i:s');

        $log = EmployeeWorkAttendanceLog::factory()->present()->create([
            'employee_id' => $this->employee->id,
            'branch_id' => $this->branch->id,
            'attendance_date' => now()->toDateString(),
            'check_in' => $originalCheckIn,
            'check_out' => $originalCheckOut,
            'late_minutes' => 15,
        ]);

        $newCheckIn = now()->setTime(8, 0)->format('Y-m-d H:i:s');
        $newStatus = 'present';
        $newLateMinutes = 0;

        $this->actingAs($this->user)
            ->post(route('work-attendance.adjust', $log), [
                'status' => $newStatus,
                'check_in' => $newCheckIn,
                'late_minutes' => $newLateMinutes,
                'adjustment_reason' => 'Koreksi waktu masuk',
            ]);

        // History captures original values
        $this->assertDatabaseHas('employee_work_attendance_adjustment_histories', [
            'attendance_log_id' => $log->id,
            'adjusted_by_employee_id' => $this->employee->id,
            'previous_status' => 'present',
            'previous_check_in' => $originalCheckIn,
            'previous_check_out' => $originalCheckOut,
            'new_status' => $newStatus,
            'new_check_in' => $newCheckIn,
            'new_check_out' => $originalCheckOut,
            'adjustment_reason' => 'Koreksi waktu masuk',
        ]);

        // Log updated with new values
        $this->assertDatabaseHas('employee_work_attendance_logs', [
            'id' => $log->id,
            'status' => $newStatus,
            'check_in' => $newCheckIn,
            'late_minutes' => $newLateMinutes,
        ]);
    }

    // VA-11: Adjust status to sick
    public function test_va11_adjust_status_sick(): void
    {
        $this->assignPosition($this->employee, 'HRR', 2);
        $this->grantAdjustmentPermission($this->user);

        $log = $this->createCheckedInLog($this->employee);

        $this->actingAs($this->user)
            ->post(route('work-attendance.adjust', $log), [
                'status' => 'sick',
                'adjustment_reason' => 'Karyawan sakit setelah check-in',
            ]);

        $this->assertDatabaseHas('employee_work_attendance_logs', [
            'id' => $log->id,
            'status' => 'sick',
        ]);
    }

    // VA-12: Verify then adjust on the same record
    public function test_va12_verify_then_adjust_on_same_record(): void
    {
        $this->assignPosition($this->employee, 'HRR', 2);
        $this->grantAdjustmentPermission($this->user);

        $log = $this->createCheckedInLog($this->employee);

        // Verify first
        $this->actingAs($this->user)
            ->post(route('work-attendance.verify', $log));

        $this->assertNotNull($log->fresh()->verified_at);
        $this->assertEquals($this->employee->id, $log->fresh()->verified_by_employee_id);

        // Then adjust
        $this->actingAs($this->user)
            ->post(route('work-attendance.adjust', $log), [
                'status' => 'sick',
                'adjustment_reason' => 'Ternyata sakit',
            ]);

        // Status changed
        $this->assertDatabaseHas('employee_work_attendance_logs', [
            'id' => $log->id,
            'status' => 'sick',
            'verified_by_employee_id' => $this->employee->id,
        ]);

        // Verification still intact (adjust doesn't clear it)
        $this->assertNotNull($log->fresh()->verified_at);
    }
}
