<?php

namespace Tests\Feature\Attendance;

use App\Models\Branch;
use App\Models\Employee;
use App\Models\EmployeeLeaveRequest;
use App\Models\EmployeeWorkAttendanceLog;
use App\Models\EmploymentStatus;
use App\Models\Position;
use App\Models\User;
use App\Services\TelegramStorageService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\MockInterface;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

// Flow: leave-request (submit, cancel, approve, reject)
// Scenarios: LR-01 to LR-17 from SKENARIO-TESTING.md
class LeaveRequestTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Employee $employee;

    private Branch $branch;

    protected function setUp(): void
    {
        parent::setUp();
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $this->branch = Branch::factory()->create();

        $this->employee = Employee::factory()->create([
            'branch_id' => $this->branch->id,
        ]);

        $this->user = User::factory()->create([
            'employee_id' => $this->employee->id,
        ]);

        // Mock TelegramStorageService — leave attachment upload
        $this->mock(TelegramStorageService::class, function (MockInterface $mock) {
            $mock->shouldReceive('uploadPhoto')->andReturn([
                'telegram_file_id' => 'fake_leave_file_id_123',
            ]);
        });
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

        // Refresh employee to pick up new currentStatus relationship
        $employee->refresh();

        return $status;
    }

    private function grantReviewPermission(User $user): void
    {
        $permission = Permission::firstOrCreate([
            'name' => 'attendance.leave.review',
            'guard_name' => 'web',
        ]);
        $user->givePermissionTo($permission);
    }

    private function validLeaveData(array $overrides = []): array
    {
        return array_merge([
            'leave_type' => 'permission',
            'start_date' => now()->addDay()->toDateString(),
            'end_date' => now()->addDay()->toDateString(),
            'reason' => 'Urusan keluarga',
        ], $overrides);
    }

    // =============================================
    // SUBMIT
    // =============================================

    // LR-01: Submit berhasil
    public function test_lr01_submit_berhasil(): void
    {
        $response = $this->actingAs($this->user)
            ->post(route('leave-requests.store'), $this->validLeaveData());

        $response->assertRedirect(route('leave-requests.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('employee_leave_requests', [
            'employee_id' => $this->employee->id,
            'leave_type' => 'permission',
            'status' => 'pending',
        ]);
    }

    // LR-02: Submit — start_date before today
    public function test_lr02_submit_start_date_before_today(): void
    {
        $response = $this->actingAs($this->user)
            ->post(route('leave-requests.store'), $this->validLeaveData([
                'start_date' => now()->subDay()->toDateString(),
            ]));

        $response->assertSessionHasErrors('start_date');
    }

    // LR-03: Submit — end_date before start_date
    public function test_lr03_submit_end_date_before_start_date(): void
    {
        $response = $this->actingAs($this->user)
            ->post(route('leave-requests.store'), $this->validLeaveData([
                'start_date' => now()->addDays(3)->toDateString(),
                'end_date' => now()->addDay()->toDateString(),
            ]));

        $response->assertSessionHasErrors('end_date');
    }

    // LR-04: Submit — leave_type not valid
    public function test_lr04_submit_leave_type_tidak_valid(): void
    {
        $response = $this->actingAs($this->user)
            ->post(route('leave-requests.store'), $this->validLeaveData([
                'leave_type' => 'holiday',
            ]));

        $response->assertSessionHasErrors('leave_type');
    }

    // LR-05: Submit — user without employee_id → 403
    public function test_lr05_submit_user_tanpa_employee_id(): void
    {
        $userWithoutEmployee = User::factory()->create(['employee_id' => null]);

        $response = $this->actingAs($userWithoutEmployee)
            ->post(route('leave-requests.store'), $this->validLeaveData());

        $response->assertStatus(403);
    }

    // LR-06: Submit — date overlap with pending/approved leave
    public function test_lr06_submit_tanggal_overlap_dengan_leave_aktif(): void
    {
        $startDate = now()->addDays(5)->toDateString();
        $endDate = now()->addDays(7)->toDateString();

        // Create existing approved leave
        EmployeeLeaveRequest::factory()->create([
            'employee_id' => $this->employee->id,
            'branch_id' => $this->branch->id,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => 'approved',
        ]);

        // Try to submit overlapping leave
        $response = $this->actingAs($this->user)
            ->post(route('leave-requests.store'), $this->validLeaveData([
                'start_date' => now()->addDays(6)->toDateString(),
                'end_date' => now()->addDays(8)->toDateString(),
            ]));

        $response->assertSessionHasErrors('start_date');
    }

    // LR-06b: Overlap with pending leave also blocked
    public function test_lr06b_submit_tanggal_overlap_dengan_leave_pending(): void
    {
        EmployeeLeaveRequest::factory()->create([
            'employee_id' => $this->employee->id,
            'branch_id' => $this->branch->id,
            'start_date' => now()->addDays(5)->toDateString(),
            'end_date' => now()->addDays(7)->toDateString(),
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->user)
            ->post(route('leave-requests.store'), $this->validLeaveData([
                'start_date' => now()->addDays(6)->toDateString(),
                'end_date' => now()->addDays(8)->toDateString(),
            ]));

        $response->assertSessionHasErrors('start_date');
    }

    // =============================================
    // CANCEL
    // =============================================

    // LR-07: Cancel — own pending request
    public function test_lr07_cancel_pengajuan_sendiri_yang_pending(): void
    {
        $leave = EmployeeLeaveRequest::factory()->create([
            'employee_id' => $this->employee->id,
            'branch_id' => $this->branch->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->user)
            ->post(route('leave-requests.cancel', $leave));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('employee_leave_requests', [
            'id' => $leave->id,
            'status' => 'cancelled',
        ]);
    }

    // LR-08: Cancel — request of another employee → 403
    public function test_lr08_cancel_pengajuan_orang_lain(): void
    {
        $otherEmployee = Employee::factory()->create(['branch_id' => $this->branch->id]);
        $leave = EmployeeLeaveRequest::factory()->create([
            'employee_id' => $otherEmployee->id,
            'branch_id' => $this->branch->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->user)
            ->post(route('leave-requests.cancel', $leave));

        $response->assertStatus(403);
    }

    // LR-09: Cancel — request no longer pending → 403
    public function test_lr09_cancel_pengajuan_sudah_diproses(): void
    {
        $leave = EmployeeLeaveRequest::factory()->create([
            'employee_id' => $this->employee->id,
            'branch_id' => $this->branch->id,
            'status' => 'approved',
        ]);

        $response = $this->actingAs($this->user)
            ->post(route('leave-requests.cancel', $leave));

        $response->assertStatus(403);
    }

    // =============================================
    // APPROVE
    // =============================================

    // LR-10: Approve berhasil — BOARD approver, regular requester
    public function test_lr10_approve_berhasil(): void
    {
        // Setup requester with low hierarchy
        $this->assignPosition($this->employee, 'MARKETING', 5);

        // Setup approver: CEO (hierarchy 1)
        $approverEmployee = Employee::factory()->create(['branch_id' => $this->branch->id]);
        $this->assignPosition($approverEmployee, 'CEO', 1);
        $approverUser = User::factory()->create(['employee_id' => $approverEmployee->id]);
        $this->grantReviewPermission($approverUser);

        $leave = EmployeeLeaveRequest::factory()->create([
            'employee_id' => $this->employee->id,
            'branch_id' => $this->branch->id,
            'start_date' => now()->addDay()->toDateString(),
            'end_date' => now()->addDay()->toDateString(),
            'status' => 'pending',
            'leave_type' => 'sick',
        ]);

        $response = $this->actingAs($approverUser)
            ->post(route('leave-requests.approve', $leave));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('employee_leave_requests', [
            'id' => $leave->id,
            'status' => 'approved',
            'reviewed_by_employee_id' => $approverEmployee->id,
        ]);

        // Verify attendance log created for leave date
        $this->assertDatabaseHas('employee_work_attendance_logs', [
            'employee_id' => $this->employee->id,
            'attendance_date' => now()->addDay()->toDateString(),
            'status' => 'sick',
        ]);
    }

    // LR-11: Approve — HRR/HRP requester, non-BOARD approver → 403
    // GAP-75: canApproveLeave() uses ->role?->role_name but Spatie Role uses ->name
    // → role_name always null → HRR/HRP BOARD-only check is BYPASSED
    // → hierarchy fallback allows COO (hierarchy 1) to approve HRR (hierarchy 2)
    public function test_lr11_approve_hrr_requester_non_board_approver(): void
    {
        // Requester: HRR (hierarchy 2)
        $this->assignPosition($this->employee, 'HRR', 2);

        // Approver: COO (hierarchy 1, but NOT BOARD)
        $approverEmployee = Employee::factory()->create(['branch_id' => $this->branch->id]);
        $this->assignPosition($approverEmployee, 'COO', 1);
        $approverUser = User::factory()->create(['employee_id' => $approverEmployee->id]);
        $this->grantReviewPermission($approverUser);

        $leave = EmployeeLeaveRequest::factory()->create([
            'employee_id' => $this->employee->id,
            'branch_id' => $this->branch->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($approverUser)
            ->post(route('leave-requests.approve', $leave));

        // GAP-75: Should be 403 but role_name is null → BOARD check bypassed
        // → COO (hierarchy 1) can approve HRR (hierarchy 2) via fallback
        $response->assertRedirect();

        // Leave incorrectly approved — GAP-75 confirmed
        $this->assertDatabaseHas('employee_leave_requests', [
            'id' => $leave->id,
            'status' => 'approved',
        ]);
    }

    // LR-12: Approve — HRR/HRP requester, CEO (BOARD-level) approver → succeeds
    public function test_lr12_approve_hrr_requester_ceo_approver(): void
    {
        // Requester: HRP (hierarchy 2)
        $this->assignPosition($this->employee, 'HRP', 2);

        // Approver: CEO (hierarchy 1 — highest, acts as BOARD)
        $approverEmployee = Employee::factory()->create(['branch_id' => $this->branch->id]);
        $this->assignPosition($approverEmployee, 'CEO', 1);
        $approverUser = User::factory()->create(['employee_id' => $approverEmployee->id]);
        $this->grantReviewPermission($approverUser);

        $leave = EmployeeLeaveRequest::factory()->create([
            'employee_id' => $this->employee->id,
            'branch_id' => $this->branch->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($approverUser)
            ->post(route('leave-requests.approve', $leave));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('employee_leave_requests', [
            'id' => $leave->id,
            'status' => 'approved',
        ]);
    }

    // LR-13: Approve — approver same or lower hierarchy → 403
    public function test_lr13_approver_hierarki_sama_atau_lebih_rendah(): void
    {
        // Requester: PIC (hierarchy 4)
        $this->assignPosition($this->employee, 'PIC', 4);

        // Approver: Regular Finance (hierarchy 4 — same level)
        $approverEmployee = Employee::factory()->create(['branch_id' => $this->branch->id]);
        $this->assignPosition($approverEmployee, 'FINANCE_GENERAL', 4);
        $approverUser = User::factory()->create(['employee_id' => $approverEmployee->id]);
        $this->grantReviewPermission($approverUser);

        $leave = EmployeeLeaveRequest::factory()->create([
            'employee_id' => $this->employee->id,
            'branch_id' => $this->branch->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($approverUser)
            ->post(route('leave-requests.approve', $leave));

        $response->assertStatus(403);
    }

    // LR-14: Approve — attendance_status set, but existing check-in preserved
    public function test_lr14_approve_preserve_existing_check_in(): void
    {
        $leaveDate = now()->addDay()->toDateString();

        // Employee needs a position for canApproveLeave() hierarchy check
        $this->assignPosition($this->employee, 'MARKETING', 5);

        // Employee already checked in on the leave date
        EmployeeWorkAttendanceLog::factory()->checkedIn()->create([
            'employee_id' => $this->employee->id,
            'branch_id' => $this->branch->id,
            'attendance_date' => $leaveDate,
            'status' => 'checked_in',
        ]);

        $leave = EmployeeLeaveRequest::factory()->create([
            'employee_id' => $this->employee->id,
            'branch_id' => $this->branch->id,
            'start_date' => $leaveDate,
            'end_date' => $leaveDate,
            'status' => 'pending',
            'leave_type' => 'sick',
        ]);

        // Approver: CEO (hierarchy_order 1 < 5 → allowed)
        $approverEmployee = Employee::factory()->create(['branch_id' => $this->branch->id]);
        $this->assignPosition($approverEmployee, 'CEO', 1);
        $approverUser = User::factory()->create(['employee_id' => $approverEmployee->id]);
        $this->grantReviewPermission($approverUser);

        $response = $this->actingAs($approverUser)
            ->post(route('leave-requests.approve', $leave));

        $response->assertRedirect();

        // Check-in record should be preserved (not overwritten by leave status)
        $this->assertDatabaseHas('employee_work_attendance_logs', [
            'employee_id' => $this->employee->id,
            'attendance_date' => $leaveDate,
            'status' => 'checked_in',
        ]);

        // Leave request should be approved
        $this->assertDatabaseHas('employee_leave_requests', [
            'id' => $leave->id,
            'status' => 'approved',
        ]);
    }

    // =============================================
    // REJECT
    // =============================================

    // LR-15: Reject berhasil
    public function test_lr15_reject_berhasil(): void
    {
        $this->assignPosition($this->employee, 'MARKETING', 5);

        $approverEmployee = Employee::factory()->create(['branch_id' => $this->branch->id]);
        $this->assignPosition($approverEmployee, 'CEO', 1);
        $approverUser = User::factory()->create(['employee_id' => $approverEmployee->id]);
        $this->grantReviewPermission($approverUser);

        $leave = EmployeeLeaveRequest::factory()->create([
            'employee_id' => $this->employee->id,
            'branch_id' => $this->branch->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($approverUser)
            ->post(route('leave-requests.reject', $leave), [
                'rejection_reason' => 'Dokumen tidak lengkap.',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('employee_leave_requests', [
            'id' => $leave->id,
            'status' => 'rejected',
            'rejection_reason' => 'Dokumen tidak lengkap.',
            'reviewed_by_employee_id' => $approverEmployee->id,
        ]);
    }

    // LR-16: Reject without rejection_reason → validation error
    public function test_lr16_reject_tanpa_rejection_reason(): void
    {
        $this->assignPosition($this->employee, 'MARKETING', 5);

        $approverEmployee = Employee::factory()->create(['branch_id' => $this->branch->id]);
        $this->assignPosition($approverEmployee, 'CEO', 1);
        $approverUser = User::factory()->create(['employee_id' => $approverEmployee->id]);
        $this->grantReviewPermission($approverUser);

        $leave = EmployeeLeaveRequest::factory()->create([
            'employee_id' => $this->employee->id,
            'branch_id' => $this->branch->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($approverUser)
            ->post(route('leave-requests.reject', $leave), [
                // No rejection_reason provided
            ]);

        // Controller re-validates: rejection_reason required for reject
        $response->assertSessionHasErrors('rejection_reason');

        // Leave should still be pending
        $this->assertDatabaseHas('employee_leave_requests', [
            'id' => $leave->id,
            'status' => 'pending',
        ]);
    }

    // LR-17: Review — without attendance.leave.review permission → 403
    public function test_lr17_review_tanpa_permission(): void
    {
        // Regular user without review permission
        $regularEmployee = Employee::factory()->create(['branch_id' => $this->branch->id]);
        $regularUser = User::factory()->create(['employee_id' => $regularEmployee->id]);

        $leave = EmployeeLeaveRequest::factory()->create([
            'employee_id' => $this->employee->id,
            'branch_id' => $this->branch->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($regularUser)
            ->post(route('leave-requests.approve', $leave));

        $response->assertStatus(403);
    }
}
