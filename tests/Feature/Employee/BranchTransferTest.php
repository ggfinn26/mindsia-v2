<?php

namespace Tests\Feature\Employee;

use App\Models\Area;
use App\Models\Branch;
use App\Models\BranchTransferRequest;
use App\Models\Employee;
use App\Models\EmploymentStatus;
use App\Models\Position;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

// Flow: contract-branch-transfer (BT-01 → BT-07)
// Covers: employee request, same-branch validation, ownership, reviewer permission,
//         direct transfer, approve/reject flow
class BranchTransferTest extends TestCase
{
    use RefreshDatabase;

    private User $employeeUser;
    private Employee $employee;
    private Branch $currentBranch;
    private Branch $otherBranch;

    protected function setUp(): void
    {
        parent::setUp();
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Employee with branch via cascade
        $this->employee = Employee::factory()->create();
        $this->currentBranch = $this->employee->branch;

        // Another branch in same area
        $this->otherBranch = Branch::factory()->create([
            'areas_id' => $this->currentBranch->areas_id,
        ]);

        $this->employeeUser = User::factory()->create([
            'employee_id' => $this->employee->id,
            'email' => $this->employee->email,
        ]);

        $position = Position::firstOrCreate(
            ['position_name' => 'Staff'],
            ['role_id' => Role::where('name', 'HRR')->first()?->id ?? 1, 'hierarchy_order' => 1]
        );

        EmploymentStatus::create([
            'employees_id' => $this->employee->id,
            'type_employment' => 'Kontrak',
            'join_date' => now()->subYear(),
            'position_id' => $position->id,
        ]);
    }

    // BT-01: Employee request pindah cabang → BranchTransferRequest created
    public function test_employee_request_branch_transfer(): void
    {
        $response = $this->actingAs($this->employeeUser)
            ->post(route('branch-transfers.request.store', $this->employee), [
                'to_branch_id' => $this->otherBranch->id,
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('branch_transfer_requests', [
            'employee_id' => $this->employee->id,
            'from_branch_id' => $this->currentBranch->id,
            'to_branch_id' => $this->otherBranch->id,
            'status' => 'review',
        ]);
    }

    // BT-02: Transfer to same branch → 422 (different validation)
    public function test_transfer_to_same_branch_rejected(): void
    {
        $this->actingAs($this->employeeUser)
            ->post(route('branch-transfers.request.store', $this->employee), [
                'to_branch_id' => $this->currentBranch->id,
            ])
            ->assertSessionHasErrors('to_branch_id');
    }

    // BT-03: Another employee cannot request transfer for someone else
    // EmployeePolicy::requestBranchTransfer checks $user->id === $employee->user_id
    public function test_other_employee_cannot_request_transfer_for_someone_else(): void
    {
        $otherEmployee = Employee::factory()->create();
        $otherUser = User::factory()->create([
            'employee_id' => $otherEmployee->id,
            'email' => $otherEmployee->email,
        ]);

        $this->actingAs($otherUser)
            ->post(route('branch-transfers.request.store', $this->employee), [
                'to_branch_id' => $this->otherBranch->id,
            ])
            ->assertStatus(403);
    }

    // BT-04: Reviewer needs organization.branch_transfer.review permission
    public function test_reviewer_needs_branch_transfer_review_permission(): void
    {
        $transfer = BranchTransferRequest::factory()->create([
            'employee_id' => $this->employee->id,
            'from_branch_id' => $this->currentBranch->id,
            'to_branch_id' => $this->otherBranch->id,
            'status' => 'review',
        ]);

        $unauthorized = User::factory()->create();
        $this->actingAs($unauthorized)
            ->put(route('branch-transfers.update', $transfer), [
                'status' => 'approved',
            ])
            ->assertStatus(403);
    }

    // BT-04b: Reviewer WITH permission can approve/reject
    public function test_reviewer_with_permission_can_approve(): void
    {
        $transfer = BranchTransferRequest::factory()->create([
            'employee_id' => $this->employee->id,
            'from_branch_id' => $this->currentBranch->id,
            'to_branch_id' => $this->otherBranch->id,
            'status' => 'review',
        ]);

        $reviewPerm = Permission::firstOrCreate(['name' => 'organization.branch_transfer.review', 'guard_name' => 'web']);
        $reviewer = User::factory()->create();
        $reviewer->givePermissionTo($reviewPerm);

        $this->actingAs($reviewer)
            ->put(route('branch-transfers.update', $transfer), [
                'status' => 'approved',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('branch_transfer_requests', [
            'id' => $transfer->id,
            'status' => 'approved',
            'reviewed_by' => $reviewer->id,
        ]);
    }

    // BT-06: Direct transfer by admin with employee.update permission
    public function test_direct_transfer_by_admin(): void
    {
        $updatePerm = Permission::firstOrCreate(['name' => 'employee.update', 'guard_name' => 'web']);
        $admin = User::factory()->create();
        $admin->givePermissionTo($updatePerm);

        $this->actingAs($admin)
            ->put(route('branch-transfers.direct.update', $this->employee), [
                'branch_id' => $this->otherBranch->id,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('employees', [
            'id' => $this->employee->id,
            'branch_id' => $this->otherBranch->id,
        ]);
    }

    // BT-07: Approve transfer → employee.branch_id updated
    public function test_approve_transfer_updates_employee_branch(): void
    {
        $transfer = BranchTransferRequest::factory()->create([
            'employee_id' => $this->employee->id,
            'from_branch_id' => $this->currentBranch->id,
            'to_branch_id' => $this->otherBranch->id,
            'status' => 'review',
        ]);

        $reviewPerm = Permission::firstOrCreate(['name' => 'organization.branch_transfer.review', 'guard_name' => 'web']);
        $reviewer = User::factory()->create();
        $reviewer->givePermissionTo($reviewPerm);

        $this->actingAs($reviewer)
            ->put(route('branch-transfers.update', $transfer), [
                'status' => 'approved',
            ]);

        $this->assertDatabaseHas('employees', [
            'id' => $this->employee->id,
            'branch_id' => $this->otherBranch->id,
        ]);
    }

    // BT-reject: Reject transfer → employee stays at same branch
    public function test_reject_transfer_employee_stays(): void
    {
        $transfer = BranchTransferRequest::factory()->create([
            'employee_id' => $this->employee->id,
            'from_branch_id' => $this->currentBranch->id,
            'to_branch_id' => $this->otherBranch->id,
            'status' => 'review',
        ]);

        $reviewPerm = Permission::firstOrCreate(['name' => 'organization.branch_transfer.review', 'guard_name' => 'web']);
        $reviewer = User::factory()->create();
        $reviewer->givePermissionTo($reviewPerm);

        $this->actingAs($reviewer)
            ->put(route('branch-transfers.update', $transfer), [
                'status' => 'rejected',
            ]);

        $this->assertDatabaseHas('employees', [
            'id' => $this->employee->id,
            'branch_id' => $this->currentBranch->id,
        ]);
        $this->assertDatabaseHas('branch_transfer_requests', [
            'id' => $transfer->id,
            'status' => 'rejected',
        ]);
    }

    // Auth: Unauthenticated → redirect login
    public function test_unauthenticated_redirect_to_login(): void
    {
        $this->get(route('branch-transfers.request.create', $this->employee))
            ->assertRedirect(route('login'));
    }
}
