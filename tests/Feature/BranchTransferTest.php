<?php

use App\Models\Branch;
use App\Models\BranchTransferRequest;
use App\Models\Employee;
use App\Models\User;

test('branch transfer request can be created', function () {
    $employee = Employee::factory()->create();
    $newBranch = Branch::factory()->create();

    BranchTransferRequest::create([
        'employee_id' => $employee->id,
        'from_branch_id' => $employee->branch_id,
        'to_branch_id' => $newBranch->id,
        'status' => 'review',
    ]);

    $this->assertDatabaseHas('branch_transfer_requests', [
        'employee_id' => $employee->id,
        'status' => 'review',
    ]);
});

test('branch transfer can be approved', function () {
    $reviewer = User::factory()->create();
    $transfer = BranchTransferRequest::factory()->create(['status' => 'review']);
    $oldBranchId = $transfer->employee->branch_id;

    $transfer->update([
        'status' => 'approved',
        'reviewed_by' => $reviewer->id,
        'reviewed_at' => now(),
    ]);
    $transfer->employee->update(['branch_id' => $transfer->to_branch_id]);

    $this->assertDatabaseHas('branch_transfer_requests', [
        'id' => $transfer->id,
        'status' => 'approved',
    ]);
    $this->assertDatabaseHas('employees', [
        'id' => $transfer->employee->id,
        'branch_id' => $transfer->to_branch_id,
    ]);
});

test('branch transfer can be rejected', function () {
    $reviewer = User::factory()->create();
    $transfer = BranchTransferRequest::factory()->create(['status' => 'review']);
    $oldBranchId = $transfer->employee->branch_id;

    $transfer->update([
        'status' => 'rejected',
        'reviewed_by' => $reviewer->id,
        'reviewed_at' => now(),
    ]);

    $this->assertDatabaseHas('branch_transfer_requests', [
        'id' => $transfer->id,
        'status' => 'rejected',
    ]);
    $this->assertDatabaseHas('employees', [
        'id' => $transfer->employee->id,
        'branch_id' => $oldBranchId,
    ]);
});

test('employee can be transferred directly', function () {
    $employee = Employee::factory()->create();
    $newBranch = Branch::factory()->create();

    $employee->update(['branch_id' => $newBranch->id]);

    $this->assertDatabaseHas('employees', [
        'id' => $employee->id,
        'branch_id' => $newBranch->id,
    ]);
});
