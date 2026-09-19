<?php

use App\Models\BranchTransferRequest;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/** Direct DB insert for an employee with full branch/area/region chain — avoids factory FK deadlock under RefreshDatabase */
function insertBtEmployee(string $prefix = 'BT'): array
{
    $provinceId = DB::table('provinces')->insertGetId(['name' => "Prov {$prefix}", 'created_at' => now(), 'updated_at' => now()]);
    $regionId = DB::table('regions')->insertGetId(['province_id' => $provinceId, 'name' => "Reg {$prefix}", 'created_at' => now(), 'updated_at' => now()]);
    $areaId = DB::table('areas')->insertGetId(['region_id' => $regionId, 'name' => "Area {$prefix}", 'created_at' => now(), 'updated_at' => now()]);
    $branchId = DB::table('branches')->insertGetId([
        'areas_id' => $areaId,
        'branch_name' => "Branch {$prefix}",
        'code_branches' => 'BR'.substr(md5(uniqid($prefix)), 0, 6),
        'address' => 'Address',
        'whatsapp' => '62'.fake()->numerify('###########'),
        'latitude' => -6.0, 'longitude' => 106.0, 'radius_meters' => 500, 'is_active' => true,
        'created_at' => now(), 'updated_at' => now(),
    ]);
    $employeeId = DB::table('employees')->insertGetId([
        'employee_code' => 'EC-'.substr(md5(uniqid($prefix)), 0, 8),
        'full_name' => "Employee {$prefix}",
        'gender' => 'L',
        'birthdate' => '1990-01-01',
        'email' => strtolower($prefix).uniqid().'@test.example',
        'whatsapp_number' => '62'.fake()->numerify('###########'),
        'branch_id' => $branchId,
        'area_id' => $areaId,
        'region_id' => $regionId,
        'is_active' => true,
        'created_at' => now(), 'updated_at' => now(),
    ]);

    return ['employee_id' => $employeeId, 'branch_id' => $branchId, 'area_id' => $areaId, 'region_id' => $regionId];
}

/** Direct DB insert for a second branch (same area) — avoids Branch::factory() FK chain */
function insertBtBranch(int $areaId, string $prefix = 'BT2'): int
{
    return DB::table('branches')->insertGetId([
        'areas_id' => $areaId,
        'branch_name' => "Branch {$prefix}",
        'code_branches' => 'BR'.substr(md5(uniqid($prefix)), 0, 6),
        'address' => 'Address',
        'whatsapp' => '62'.fake()->numerify('###########'),
        'latitude' => -6.1, 'longitude' => 106.1, 'radius_meters' => 500, 'is_active' => true,
        'created_at' => now(), 'updated_at' => now(),
    ]);
}

beforeEach(function () {
    //
});

test('branch transfer request can be created', function () {
    $data = insertBtEmployee('BT1-'.uniqid());
    $employee = Employee::find($data['employee_id']);
    $newBranchId = insertBtBranch($data['area_id'], 'BT1-NB-'.uniqid());

    BranchTransferRequest::create([
        'employee_id' => $employee->id,
        'from_branch_id' => $employee->branch_id,
        'to_branch_id' => $newBranchId,
        'status' => 'review',
    ]);

    $this->assertDatabaseHas('branch_transfer_requests', [
        'employee_id' => $employee->id,
        'status' => 'review',
    ]);
});

test('branch transfer can be approved', function () {
    $reviewer = User::factory()->create(['email_verified_at' => now(), 'must_change_password' => false]);

    $data = insertBtEmployee('BT2-'.uniqid());
    $employee = Employee::find($data['employee_id']);
    $newBranchId = insertBtBranch($data['area_id'], 'BT2-NB-'.uniqid());
    $oldBranchId = $employee->branch_id;

    $transfer = BranchTransferRequest::create([
        'employee_id' => $employee->id,
        'from_branch_id' => $oldBranchId,
        'to_branch_id' => $newBranchId,
        'status' => 'review',
    ]);

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
    $reviewer = User::factory()->create(['email_verified_at' => now(), 'must_change_password' => false]);

    $data = insertBtEmployee('BT3-'.uniqid());
    $employee = Employee::find($data['employee_id']);
    $newBranchId = insertBtBranch($data['area_id'], 'BT3-NB-'.uniqid());
    $oldBranchId = $employee->branch_id;

    $transfer = BranchTransferRequest::create([
        'employee_id' => $employee->id,
        'from_branch_id' => $oldBranchId,
        'to_branch_id' => $newBranchId,
        'status' => 'review',
    ]);

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
    $data = insertBtEmployee('BT4-'.uniqid());
    $employee = Employee::find($data['employee_id']);
    $newBranchId = insertBtBranch($data['area_id'], 'BT4-NB-'.uniqid());

    $employee->update(['branch_id' => $newBranchId]);

    $this->assertDatabaseHas('employees', [
        'id' => $employee->id,
        'branch_id' => $newBranchId,
    ]);
});
