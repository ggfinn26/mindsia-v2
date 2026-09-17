<?php

use App\Models\Branch;
use App\Models\ClassRoom;
use App\Models\ClassTest;
use App\Models\Employee;
use App\Models\MemberCertificate;
use App\Models\MemberClass;
use App\Models\MemberRegistration;
use App\Models\Program;
use App\Models\User;
use Illuminate\Support\Facades\DB;

function insertTcEmployee(string $prefix = 'TC'): array
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

function insertTcMemberRegistration(int $programId, int $employeeId): int
{
    $memberDataId = DB::table('members_data')->insertGetId([
        'full_name' => 'TC Member '.uniqid(),
        'gender' => 'P',
        'birthdate' => '2001-06-15',
        'email' => 'tcmember'.uniqid().'@test.example',
        'whatsapp_number' => '62'.fake()->numerify('###########'),
        'is_active' => true,
        'created_at' => now(), 'updated_at' => now(),
    ]);

    return DB::table('members_registration')->insertGetId([
        'members_data_id' => $memberDataId,
        'program_id' => $programId,
        'employee_id' => $employeeId,
        'receipt_member_name' => 'TC Member',
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
    $data = insertTcEmployee('TCB'.uniqid());
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

    $this->regId = insertTcMemberRegistration($this->program->id, $this->employee->id);
    $memberClassId = DB::table('member_class')->insertGetId([
        'member_registration_id' => $this->regId,
        'class_id' => $this->classroom->id,
        'start_date' => today()->subWeek()->toDateString(),
        'status' => 'active',
        'created_at' => now(), 'updated_at' => now(),
    ]);
    $this->memberClass = MemberClass::find($memberClassId);
});

// ── TC-01 ── create class test ────────────────────────────────────────────────

test('TC-01 create class test saves test record', function () {
    $this->actingAs($this->boardUser)
        ->post(route('class-tests.store', $this->classroom), [
            'test_name' => 'Pre-Test Batch 1',
            'test_type' => 'pre_test',
            'date' => today()->toDateString(),
            'description' => 'Test awal semester',
            'criteria' => [
                ['criteria_name' => 'Listening', 'description' => 'Kemampuan mendengar'],
            ],
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('test', [
        'test_name' => 'Pre-Test Batch 1',
        'class_id' => $this->classroom->id,
        'test_type' => 'pre_test',
    ]);
});

// ── TC-02 ── create test without permission ───────────────────────────────────

test('TC-02 create class test without class.test.create returns 403', function () {
    $this->actingAs($this->regularUser)
        ->post(route('class-tests.store', $this->classroom), [
            'test_name' => 'Unauthorized Test',
            'test_type' => 'pre_test',
            'date' => today()->toDateString(),
        ])
        ->assertForbidden();
});

// ── TC-03 ── update class test ────────────────────────────────────────────────

test('TC-03 update class test saves new name', function () {
    $classTest = ClassTest::factory()->create([
        'class_id' => $this->classroom->id,
        'program_id' => $this->program->id,
    ]);

    $this->actingAs($this->boardUser)
        ->put(route('class-tests.update', [$this->classroom, $classTest]), [
            'test_name' => 'Updated Test Name',
            'test_type' => 'post_test',
            'date' => today()->toDateString(),
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('test', [
        'id' => $classTest->id,
        'test_name' => 'Updated Test Name',
    ]);
});

// ── TC-04 ── delete class test ────────────────────────────────────────────────

test('TC-04 delete class test removes record', function () {
    $classTest = ClassTest::factory()->create([
        'class_id' => $this->classroom->id,
        'program_id' => $this->program->id,
    ]);

    $this->actingAs($this->boardUser)
        ->delete(route('class-tests.destroy', [$this->classroom, $classTest]))
        ->assertRedirect();

    $this->assertDatabaseMissing('test', ['id' => $classTest->id]);
});

// ── TC-05 ── graduate members ─────────────────────────────────────────────────

test('TC-05 graduate classroom sets members to LULUS and creates certificates', function () {
    $this->actingAs($this->boardUser)
        ->post(route('classrooms.graduate', $this->classroom))
        ->assertRedirect(route('classrooms.show', $this->classroom));

    // Registration should be LULUS
    $this->assertDatabaseHas('members_registration', [
        'id' => $this->regId,
        'graduation_status' => 'LULUS',
    ]);

    // MemberClass should be completed
    $this->assertDatabaseHas('member_class', [
        'id' => $this->memberClass->id,
        'status' => 'completed',
    ]);

    // Certificate should be created
    $this->assertDatabaseHas('member_certificate', [
        'member_registration_id' => $this->regId,
        'certificate_available' => 'not_available',
        'certificate_taken' => 'not_taken',
    ]);

    // Classroom should be completed
    $this->assertDatabaseHas('classes', [
        'id' => $this->classroom->id,
        'status' => 'completed',
    ]);
});

// ── TC-06 ── graduate without permission ──────────────────────────────────────

test('TC-06 graduate classroom without class.graduate returns 403', function () {
    $this->actingAs($this->regularUser)
        ->post(route('classrooms.graduate', $this->classroom))
        ->assertForbidden();
});

// ── TC-07 ── graduate classroom with no active members returns error ───────────

test('TC-07 graduate classroom with no active members returns error redirect', function () {
    $emptyClassroom = ClassRoom::factory()->create([
        'branch_id' => $this->branch->id,
        'program_id' => $this->program->id,
        'tutor_id' => $this->employee->id,
        'status' => 'active',
    ]);

    $this->actingAs($this->boardUser)
        ->post(route('classrooms.graduate', $emptyClassroom))
        ->assertSessionHasErrors();
});

// ── TC-08 ── create certificate for registration ──────────────────────────────

test('TC-08 create certificate for registration saves record', function () {
    $this->actingAs($this->boardUser)
        ->post(route('registrations.certificates.store', MemberRegistration::find($this->regId)), [
            'certificate_number' => 'CERT-'.uniqid(),
            'certificate_available' => true,
            'certificate_hardcopy' => false,
            'certificate_taken' => false,
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('member_certificate', [
        'member_registration_id' => $this->regId,
    ]);
});

// ── TC-09 ── create duplicate certificate rejected ────────────────────────────

test('TC-09 create certificate when one already exists returns 422', function () {
    DB::table('member_certificate')->insert([
        'member_registration_id' => $this->regId,
        'certificate_available' => 'not_available',
        'certificate_hardcopy' => true,
        'certificate_taken' => 'not_taken',
        'created_at' => now(), 'updated_at' => now(),
    ]);

    $this->actingAs($this->boardUser)
        ->post(route('registrations.certificates.store', MemberRegistration::find($this->regId)), [
            'certificate_number' => 'CERT-DUP',
            'certificate_available' => true,
            'certificate_hardcopy' => false,
            'certificate_taken' => false,
        ])
        ->assertStatus(422);
});

// ── TC-10 ── update certificate data ─────────────────────────────────────────

test('TC-10 update certificate marks as taken', function () {
    $certId = DB::table('member_certificate')->insertGetId([
        'member_registration_id' => $this->regId,
        'certificate_available' => 'available',
        'certificate_hardcopy' => true,
        'certificate_taken' => 'not_taken',
        'created_at' => now(), 'updated_at' => now(),
    ]);
    $cert = MemberCertificate::find($certId);

    $this->actingAs($this->boardUser)
        ->patch(route('member-certificates.update', $cert), [
            'certificate_number' => 'CERT-001',
            'certificate_available' => 'available',
            'certificate_hardcopy' => true,
            'certificate_taken' => 'taken',
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('member_certificate', [
        'id' => $certId,
        'certificate_taken' => 'taken',
        'certificate_number' => 'CERT-001',
    ]);
});
