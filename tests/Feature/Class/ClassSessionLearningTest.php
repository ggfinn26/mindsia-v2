<?php

use App\Models\Branch;
use App\Models\ClassRoom;
use App\Models\ClassSchedule;
use App\Models\Employee;
use App\Models\MemberClass;
use App\Models\MemberSessionAssessment;
use App\Models\Program;
use App\Models\User;
use Illuminate\Support\Facades\DB;

function insertSlEmployee(string $prefix = 'SL'): array
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
    $data = insertSlEmployee('SLB'.uniqid());
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

    // Past schedule (yesterday) — can record attendance. Use 14:00 to avoid conflict with observer-generated schedules at 09:00.
    $pastId = DB::table('class_schedules')->insertGetId([
        'class_id' => $this->classroom->id,
        'schedule_date' => today()->subDay()->toDateString(),
        'start_time' => '14:00',
        'end_time' => '16:00',
        'created_at' => now(), 'updated_at' => now(),
    ]);
    $this->pastSchedule = ClassSchedule::find($pastId);

    // Future schedule — cannot record attendance
    $futureId = DB::table('class_schedules')->insertGetId([
        'class_id' => $this->classroom->id,
        'schedule_date' => today()->addDay()->toDateString(),
        'start_time' => '14:00',
        'end_time' => '16:00',
        'created_at' => now(), 'updated_at' => now(),
    ]);
    $this->futureSchedule = ClassSchedule::find($futureId);

    // Member class enrollment
    $memberDataId = DB::table('members_data')->insertGetId([
        'full_name' => 'SL Member '.uniqid(),
        'gender' => 'P',
        'birthdate' => '2001-05-10',
        'email' => 'slmember'.uniqid().'@test.example',
        'whatsapp_number' => '62'.fake()->numerify('###########'),
        'is_active' => true,
        'created_at' => now(), 'updated_at' => now(),
    ]);
    $regId = DB::table('members_registration')->insertGetId([
        'members_data_id' => $memberDataId,
        'program_id' => $this->program->id,
        'employee_id' => $this->employee->id,
        'receipt_member_name' => 'SL Member',
        'receipt_institution_name' => 'Test',
        'receipt_program_name' => 'Test',
        'original_price' => 1000000,
        'final_price' => 1000000,
        'graduation_status' => 'BELUM_LULUS',
        'payment_status' => 'paid_full',
        'created_at' => now(), 'updated_at' => now(),
    ]);
    $memberClassId = DB::table('member_class')->insertGetId([
        'member_registration_id' => $regId,
        'class_id' => $this->classroom->id,
        'start_date' => today()->subWeek()->toDateString(),
        'status' => 'active',
        'created_at' => now(), 'updated_at' => now(),
    ]);
    $this->memberClass = MemberClass::find($memberClassId);
});

// ── SL-01 ── record attendance for past session ───────────────────────────────

test('SL-01 record attendance for past session saves records', function () {
    $this->actingAs($this->boardUser)
        ->post(route('member-attendance.store', $this->pastSchedule), [
            'attendances' => [
                [
                    'member_class_id' => $this->memberClass->id,
                    'status' => 'present',
                    'notes' => null,
                ],
            ],
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('member_attendance', [
        'member_class_id' => $this->memberClass->id,
        'class_schedule_id' => $this->pastSchedule->id,
        'status' => 'present',
    ]);
});

// ── SL-02 ── attendance without permission ────────────────────────────────────

test('SL-02 record attendance without class.attendance.record returns 403', function () {
    $this->actingAs($this->regularUser)
        ->post(route('member-attendance.store', $this->pastSchedule), [
            'attendances' => [
                ['member_class_id' => $this->memberClass->id, 'status' => 'present', 'notes' => null],
            ],
        ])
        ->assertForbidden();
});

// ── SL-03 ── attendance before schedule date rejected ─────────────────────────

test('SL-03 record attendance for future session returns 422', function () {
    $this->actingAs($this->boardUser)
        ->post(route('member-attendance.store', $this->futureSchedule), [
            'attendances' => [
                ['member_class_id' => $this->memberClass->id, 'status' => 'present', 'notes' => null],
            ],
        ])
        ->assertStatus(422);
});

// ── SL-04 ── invalid member_class_id rejected ────────────────────────────────

test('SL-04 record attendance with wrong member_class_id returns 403', function () {
    $otherClassroom = ClassRoom::factory()->create([
        'branch_id' => $this->branch->id,
        'program_id' => $this->program->id,
        'tutor_id' => $this->employee->id,
    ]);

    $memberDataId = DB::table('members_data')->insertGetId([
        'full_name' => 'Other Member '.uniqid(),
        'gender' => 'L',
        'birthdate' => '2000-01-01',
        'email' => 'othersl'.uniqid().'@test.example',
        'whatsapp_number' => '62'.fake()->numerify('###########'),
        'is_active' => true,
        'created_at' => now(), 'updated_at' => now(),
    ]);
    $otherRegId = DB::table('members_registration')->insertGetId([
        'members_data_id' => $memberDataId,
        'program_id' => $this->program->id,
        'employee_id' => $this->employee->id,
        'receipt_member_name' => 'Other',
        'receipt_institution_name' => 'Test',
        'receipt_program_name' => 'Test',
        'original_price' => 1000000,
        'final_price' => 1000000,
        'graduation_status' => 'BELUM_LULUS',
        'payment_status' => 'paid_full',
        'created_at' => now(), 'updated_at' => now(),
    ]);
    $otherMemberClassId = DB::table('member_class')->insertGetId([
        'member_registration_id' => $otherRegId,
        'class_id' => $otherClassroom->id,
        'start_date' => today()->subWeek()->toDateString(),
        'status' => 'active',
        'created_at' => now(), 'updated_at' => now(),
    ]);

    $this->actingAs($this->boardUser)
        ->post(route('member-attendance.store', $this->pastSchedule), [
            'attendances' => [
                // member_class_id from different classroom
                ['member_class_id' => $otherMemberClassId, 'status' => 'present', 'notes' => null],
            ],
        ])
        ->assertForbidden();
});

// ── SL-05 ── update session material for past session ────────────────────────

test('SL-05 update material for past session saves record', function () {
    $this->actingAs($this->boardUser)
        ->patch(route('class-schedules.material', $this->pastSchedule), [
            'material_taught' => 'Materi Kosakata Dasar',
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('class_schedules', [
        'id' => $this->pastSchedule->id,
        'material_taught' => 'Materi Kosakata Dasar',
    ]);
});

// ── SL-06 ── update material for future session rejected ──────────────────────

test('SL-06 update material for future session returns 422', function () {
    $this->actingAs($this->boardUser)
        ->patch(route('class-schedules.material', $this->futureSchedule), [
            'material_taught' => 'Materi Masa Depan',
        ])
        ->assertStatus(422);
});

// ── SL-07 ── record session assessment ───────────────────────────────────────

test('SL-07 record session assessment saves score', function () {
    $this->actingAs($this->boardUser)
        ->post(route('member-assessments.store', $this->pastSchedule), [
            'member_class_id' => $this->memberClass->id,
            'score' => 85,
            'notes' => 'Bagus',
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('member_session_assessments', [
        'member_class_id' => $this->memberClass->id,
        'class_schedule_id' => $this->pastSchedule->id,
        'score' => 85,
    ]);
});

// ── SL-08 ── assessment without permission ────────────────────────────────────

test('SL-08 record assessment without class.assessment.record returns 403', function () {
    $this->actingAs($this->regularUser)
        ->post(route('member-assessments.store', $this->pastSchedule), [
            'member_class_id' => $this->memberClass->id,
            'score' => 85,
            'notes' => null,
        ])
        ->assertForbidden();
});
