<?php

use App\Models\Branch;
use App\Models\ClassRoom;
use App\Models\Employee;
use App\Models\MemberClass;
use App\Models\Program;
use App\Models\User;
use Illuminate\Support\Facades\DB;

function insertClassEmployee(string $prefix = 'CLS'): array
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

beforeEach(function () {
    $data = insertClassEmployee('CLB'.uniqid());
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

    // Start date on a Monday (Senin) at or after today
    $monday = now()->startOfWeek()->addWeek();
    $this->validPayload = [
        'program_id' => $this->program->id,
        'branch_id' => $this->branch->id,
        'class_name' => 'Kelas Test '.uniqid(),
        'tutor_id' => $this->employee->id,
        'day_of_week' => 'Senin',
        'week_count' => 4,
        'start_date' => $monday->toDateString(),
        'start_time_primary' => '09:00',
        'end_time_primary' => '11:00',
        'start_time_secondary' => null,
        'end_time_secondary' => null,
    ];
});

// ── CC-01 ── unauthenticated ──────────────────────────────────────────────────

test('CC-01 unauthenticated classroom index redirects to login', function () {
    $this->get(route('classrooms.index'))->assertRedirect(route('login'));
});

// ── CC-02 ── permission guard ─────────────────────────────────────────────────

test('CC-02 classroom index without class.manage returns 403', function () {
    $this->actingAs($this->regularUser)
        ->get(route('classrooms.index'))
        ->assertForbidden();
});

// ── CC-03 ── index ────────────────────────────────────────────────────────────

test('CC-03 classroom index with class.manage returns 200', function () {
    $this->actingAs($this->boardUser)
        ->get(route('classrooms.index'))
        ->assertOk();
});

// ── CC-04 ── create form ──────────────────────────────────────────────────────

test('CC-04 classroom create form returns 200', function () {
    $this->actingAs($this->boardUser)
        ->get(route('classrooms.create'))
        ->assertOk();
});

// ── CC-05 ── store classroom ──────────────────────────────────────────────────

test('CC-05 store classroom creates record and redirects', function () {
    $this->actingAs($this->boardUser)
        ->post(route('classrooms.store'), $this->validPayload)
        ->assertRedirect();

    $this->assertDatabaseHas('classes', [
        'class_name' => $this->validPayload['class_name'],
        'program_id' => $this->program->id,
        'day_of_week' => 'Senin',
    ]);
});

// ── CC-06 ── show classroom ───────────────────────────────────────────────────

test('CC-06 show classroom returns 200', function () {
    $classroom = ClassRoom::factory()->create([
        'branch_id' => $this->branch->id,
        'program_id' => $this->program->id,
        'tutor_id' => $this->employee->id,
    ]);

    $this->actingAs($this->boardUser)
        ->get(route('classrooms.show', $classroom))
        ->assertOk();
});

// ── CC-07 ── edit form ────────────────────────────────────────────────────────

test('CC-07 classroom edit form returns 200', function () {
    $classroom = ClassRoom::factory()->create([
        'branch_id' => $this->branch->id,
        'program_id' => $this->program->id,
        'tutor_id' => $this->employee->id,
    ]);

    $this->actingAs($this->boardUser)
        ->get(route('classrooms.edit', $classroom))
        ->assertOk();
});

// ── CC-08 ── update classroom ─────────────────────────────────────────────────

test('CC-08 update classroom saves new name', function () {
    $classroom = ClassRoom::factory()->create([
        'branch_id' => $this->branch->id,
        'program_id' => $this->program->id,
        'tutor_id' => $this->employee->id,
        'status' => 'planned',
    ]);

    $newName = 'Updated Class '.uniqid();
    $monday = now()->startOfWeek()->addWeek();

    $this->actingAs($this->boardUser)
        ->put(route('classrooms.update', $classroom), array_merge($this->validPayload, [
            'class_name' => $newName,
        ]))
        ->assertRedirect();

    $this->assertDatabaseHas('classes', ['id' => $classroom->id, 'class_name' => $newName]);
});

// ── CC-09 ── delete planned classroom without members ─────────────────────────

test('CC-09 delete planned classroom without members succeeds', function () {
    $classroom = ClassRoom::factory()->create([
        'branch_id' => $this->branch->id,
        'program_id' => $this->program->id,
        'tutor_id' => $this->employee->id,
        'status' => 'planned',
    ]);

    $this->actingAs($this->boardUser)
        ->delete(route('classrooms.destroy', $classroom))
        ->assertRedirect(route('classrooms.index'));

    $this->assertDatabaseMissing('classes', ['id' => $classroom->id]);
});

// ── CC-10 ── delete active classroom rejected ─────────────────────────────────

test('CC-10 delete active classroom is rejected', function () {
    $classroom = ClassRoom::factory()->create([
        'branch_id' => $this->branch->id,
        'program_id' => $this->program->id,
        'tutor_id' => $this->employee->id,
        'status' => 'active',
    ]);

    $this->actingAs($this->boardUser)
        ->delete(route('classrooms.destroy', $classroom))
        ->assertSessionHasErrors();

    $this->assertDatabaseHas('classes', ['id' => $classroom->id]);
});

// ── CC-11 ── delete classroom with members rejected ───────────────────────────

test('CC-11 delete planned classroom with enrolled members is rejected', function () {
    $classroom = ClassRoom::factory()->create([
        'branch_id' => $this->branch->id,
        'program_id' => $this->program->id,
        'tutor_id' => $this->employee->id,
        'status' => 'planned',
    ]);

    // Seed a member registration and enroll
    $memberDataId = DB::table('members_data')->insertGetId([
        'full_name' => 'Test Member '.uniqid(),
        'gender' => 'L',
        'birthdate' => '2000-01-01',
        'email' => 'member'.uniqid().'@test.example',
        'whatsapp_number' => '62'.fake()->numerify('###########'),
        'is_active' => true,
        'created_at' => now(), 'updated_at' => now(),
    ]);
    $regId = DB::table('members_registration')->insertGetId([
        'members_data_id' => $memberDataId,
        'program_id' => $this->program->id,
        'employee_id' => $this->employee->id,
        'receipt_member_name' => 'Test',
        'receipt_institution_name' => 'Test',
        'receipt_program_name' => 'Test',
        'original_price' => 1000000,
        'final_price' => 1000000,
        'graduation_status' => 'BELUM_LULUS',
        'payment_status' => 'paid_full',
        'created_at' => now(), 'updated_at' => now(),
    ]);
    DB::table('member_class')->insert([
        'member_registration_id' => $regId,
        'class_id' => $classroom->id,
        'start_date' => today()->toDateString(),
        'status' => 'active',
        'created_at' => now(), 'updated_at' => now(),
    ]);

    $this->actingAs($this->boardUser)
        ->delete(route('classrooms.destroy', $classroom))
        ->assertSessionHasErrors();

    $this->assertDatabaseHas('classes', ['id' => $classroom->id]);
});
