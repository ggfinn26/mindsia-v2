<?php

use App\Models\Branch;
use App\Models\ClassRoom;
use App\Models\Employee;
use App\Models\MemberClass;
use App\Models\Program;
use App\Models\User;
use Illuminate\Support\Facades\DB;

function insertMtEmployee(string $prefix = 'MT'): array
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

    return ['employee_id' => $employeeId, 'branch_id' => $branchId];
}

function insertMtMemberRegistration(int $programId, int $employeeId): int
{
    $memberDataId = DB::table('members_data')->insertGetId([
        'full_name' => 'Member '.uniqid(),
        'gender' => 'L',
        'birthdate' => '2000-01-01',
        'email' => 'mtmember'.uniqid().'@test.example',
        'whatsapp_number' => '62'.fake()->numerify('###########'),
        'is_active' => true,
        'created_at' => now(), 'updated_at' => now(),
    ]);

    return DB::table('members_registration')->insertGetId([
        'members_data_id' => $memberDataId,
        'program_id' => $programId,
        'employee_id' => $employeeId,
        'receipt_member_name' => 'Test',
        'receipt_institution_name' => 'Test',
        'receipt_program_name' => 'Test',
        'original_price' => 1000000,
        'final_price' => 1000000,
        'graduation_status' => 'BELUM_LULUS',
        'payment_status' => 'paid_full',
        'created_at' => now(), 'updated_at' => now(),
    ]);
}

beforeEach(function () {
    $data = insertMtEmployee('MTB'.uniqid());
    $this->employee = Employee::find($data['employee_id']);
    $this->branch = Branch::find($data['branch_id']);

    $this->boardUser = User::factory()->create([
        'employee_id' => $this->employee->id,
        'email' => $this->employee->email,
        'email_verified_at' => now(),
        'must_change_password' => false,
    ]);
    $this->boardUser->assignRole('CEO');

    $this->regularUser = User::factory()->create(['email_verified_at' => now(), 'must_change_password' => false]);
    $this->regularUser->assignRole('HRR');

    $this->program = Program::factory()->create();

    $this->classroom = ClassRoom::factory()->create([
        'branch_id' => $this->branch->id,
        'program_id' => $this->program->id,
        'tutor_id' => $this->employee->id,
        'status' => 'active',
    ]);

    $this->targetClassroom = ClassRoom::factory()->create([
        'branch_id' => $this->branch->id,
        'program_id' => $this->program->id,
        'tutor_id' => $this->employee->id,
        'status' => 'active',
    ]);

    $this->regId = insertMtMemberRegistration($this->program->id, $this->employee->id);
});

// ── MT-01 ── enroll member ────────────────────────────────────────────────────

test('MT-01 enroll member into classroom creates member_class record', function () {
    $this->actingAs($this->boardUser)
        ->post(route('member-class.store', $this->classroom), [
            'member_registration_id' => $this->regId,
            'start_date' => today()->toDateString(),
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('member_class', [
        'member_registration_id' => $this->regId,
        'class_id' => $this->classroom->id,
        'status' => 'active',
    ]);
});

// ── MT-02 ── enroll without permission ────────────────────────────────────────

test('MT-02 enroll member without class.manage returns 403', function () {
    $this->actingAs($this->regularUser)
        ->post(route('member-class.store', $this->classroom), [
            'member_registration_id' => $this->regId,
            'start_date' => today()->toDateString(),
        ])
        ->assertForbidden();
});

// ── MT-03 ── transfer member between classrooms ───────────────────────────────

test('MT-03 transfer member sets old enrollment inactive and creates new active enrollment', function () {
    $memberClassId = DB::table('member_class')->insertGetId([
        'member_registration_id' => $this->regId,
        'class_id' => $this->classroom->id,
        'start_date' => today()->toDateString(),
        'status' => 'active',
        'created_at' => now(), 'updated_at' => now(),
    ]);

    $memberClass = MemberClass::find($memberClassId);

    $this->actingAs($this->boardUser)
        ->put(route('member-class.transfer', $memberClass), [
            'new_class_id' => $this->targetClassroom->id,
        ])
        ->assertRedirect();

    // Old enrollment should be inactive
    $this->assertDatabaseHas('member_class', [
        'id' => $memberClassId,
        'status' => 'inactive',
    ]);

    // New enrollment should be active
    $this->assertDatabaseHas('member_class', [
        'member_registration_id' => $this->regId,
        'class_id' => $this->targetClassroom->id,
        'status' => 'active',
    ]);
});

// ── MT-04 ── transfer without permission ──────────────────────────────────────

test('MT-04 transfer member without class.manage returns 403', function () {
    $memberClassId = DB::table('member_class')->insertGetId([
        'member_registration_id' => $this->regId,
        'class_id' => $this->classroom->id,
        'start_date' => today()->toDateString(),
        'status' => 'active',
        'created_at' => now(), 'updated_at' => now(),
    ]);
    $memberClass = MemberClass::find($memberClassId);

    $this->actingAs($this->regularUser)
        ->put(route('member-class.transfer', $memberClass), [
            'new_class_id' => $this->targetClassroom->id,
        ])
        ->assertForbidden();
});

// ── MT-05 ── remove member from classroom ─────────────────────────────────────

test('MT-05 remove member deletes member_class record', function () {
    $memberClassId = DB::table('member_class')->insertGetId([
        'member_registration_id' => $this->regId,
        'class_id' => $this->classroom->id,
        'start_date' => today()->toDateString(),
        'status' => 'active',
        'created_at' => now(), 'updated_at' => now(),
    ]);
    $memberClass = MemberClass::find($memberClassId);

    $this->actingAs($this->boardUser)
        ->delete(route('member-class.destroy', $memberClass))
        ->assertRedirect();

    $this->assertDatabaseMissing('member_class', ['id' => $memberClassId]);
});

// ── MT-06 ── remove member without permission ─────────────────────────────────

test('MT-06 remove member without class.manage returns 403', function () {
    $memberClassId = DB::table('member_class')->insertGetId([
        'member_registration_id' => $this->regId,
        'class_id' => $this->classroom->id,
        'start_date' => today()->toDateString(),
        'status' => 'active',
        'created_at' => now(), 'updated_at' => now(),
    ]);
    $memberClass = MemberClass::find($memberClassId);

    $this->actingAs($this->regularUser)
        ->delete(route('member-class.destroy', $memberClass))
        ->assertForbidden();
});
