<?php

use App\Models\ClassRoom;
use App\Models\Curriculum;
use App\Models\CurriculumItem;
use App\Models\CurriculumSession;
use App\Models\Employee;
use App\Models\Program;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/** Insert full geo chain + employee, return employee id */
function insertSaEmployee(string $prefix = 'SA'): array
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
    // Tutor employee
    $tutorData = insertSaEmployee('TUT'.uniqid());
    $this->tutor = Employee::find($tutorData['employee_id']);
    $this->tutorUser = User::factory()->create([
        'employee_id' => $this->tutor->id,
        'email' => $this->tutor->email,
        'email_verified_at' => now(),
        'must_change_password' => false,
    ]);
    $this->tutorUser->assignRole('HRR');

    // Non-tutor employee
    $otherData = insertSaEmployee('OTH'.uniqid());
    $this->otherEmployee = Employee::find($otherData['employee_id']);
    $this->otherUser = User::factory()->create([
        'employee_id' => $this->otherEmployee->id,
        'email' => $this->otherEmployee->email,
        'email_verified_at' => now(),
        'must_change_password' => false,
    ]);
    $this->otherUser->assignRole('HRR');

    // Program + Curriculum
    $this->program = Program::factory()->create();
    $this->curriculum = Curriculum::create([
        'program_id' => $this->program->id,
        'curriculum_name' => 'Kurikulum Test SA',
        'is_active' => true,
    ]);
    $this->session = CurriculumSession::create([
        'curriculum_id' => $this->curriculum->id,
        'session_number' => 1,
        'session_title' => 'Sesi 1',
        'sort_order' => 1,
        'is_active' => true,
    ]);
    $this->item = CurriculumItem::create([
        'curriculum_session_id' => $this->session->id,
        'item_name' => 'Item Test',
        'sequence_number' => 1,
        'material_type' => 'external_link',
        'material_value' => 'https://example.com/materi',
        'is_active' => true,
    ]);

    // ClassRoom → program
    $this->classroom = ClassRoom::create([
        'program_id' => $this->program->id,
        'branch_id' => $tutorData['branch_id'],
        'class_name' => 'Kelas SA Test',
        'tutor_id' => $this->tutor->id,
        'day_of_week' => 'Senin',
        'week_count' => 4,
        'start_date' => now()->toDateString(),
        'end_date' => now()->addMonths(1)->toDateString(),
        'start_time_primary' => '09:00',
        'end_time_primary' => '11:00',
        'status' => 'active',
    ]);
});

// ── SA-01 ── unauthenticated ──────────────────────────────────────────────────

test('SA-01 unauthenticated class curriculum index redirects to login', function () {
    $this->get(route('class-curriculum.index', $this->classroom))
        ->assertRedirect(route('login'));
});

// ── SA-02 ── non-tutor non-enrolled employee blocked ──────────────────────────

test('SA-02 non-tutor non-enrolled employee gets 403', function () {
    $this->actingAs($this->otherUser)
        ->get(route('class-curriculum.index', $this->classroom))
        ->assertForbidden();
});

// ── SA-03 ── tutor can view curriculum ────────────────────────────────────────

test('SA-03 tutor of classroom can view curriculum index', function () {
    $this->actingAs($this->tutorUser)
        ->get(route('class-curriculum.index', $this->classroom))
        ->assertOk();
});

// ── SA-04 ── employee enrolled via MemberRegistration can view ────────────────

test('SA-04 employee with MemberClass registration can view curriculum', function () {
    // MemberData minimal insert
    $memberDataId = DB::table('members_data')->insertGetId([
        'full_name' => 'Member Test',
        'gender' => 'L',
        'birthdate' => '2000-01-01',
        'whatsapp_number' => '62'.fake()->numerify('###########'),
        'created_at' => now(), 'updated_at' => now(),
    ]);

    $registrationId = DB::table('members_registration')->insertGetId([
        'members_data_id' => $memberDataId,
        'program_id' => $this->program->id,
        'employee_id' => $this->otherEmployee->id,
        'receipt_member_name' => 'Member Test',
        'receipt_institution_name' => 'Institusi',
        'receipt_program_name' => $this->program->program_name,
        'original_price' => 1000000,
        'final_price' => 1000000,
        'graduation_status' => 'BELUM_LULUS',
        'payment_status' => 'unpaid',
        'installment_type' => '1',
        'created_at' => now(), 'updated_at' => now(),
    ]);

    DB::table('member_class')->insert([
        'member_registration_id' => $registrationId,
        'class_id' => $this->classroom->id,
        'start_date' => now()->toDateString(),
        'end_date' => now()->addMonths(1)->toDateString(),
        'status' => 'active',
        'created_at' => now(), 'updated_at' => now(),
    ]);

    $this->actingAs($this->otherUser)
        ->get(route('class-curriculum.index', $this->classroom))
        ->assertOk();
});

// ── SA-05 ── tutor can view item from correct curriculum ──────────────────────

test('SA-05 tutor can view item that belongs to classroom curriculum', function () {
    $this->actingAs($this->tutorUser)
        ->get(route('class-curriculum.show', [$this->classroom, $this->item]))
        ->assertOk();
});

// ── SA-06 ── item from different curriculum blocked ───────────────────────────

test('SA-06 tutor cannot view item from a different curriculum', function () {
    $otherProgram = Program::factory()->create();
    $otherCurriculum = Curriculum::create([
        'program_id' => $otherProgram->id,
        'curriculum_name' => 'Kurikulum Lain',
        'is_active' => true,
    ]);
    $otherSession = CurriculumSession::create([
        'curriculum_id' => $otherCurriculum->id,
        'session_number' => 1,
        'session_title' => 'Sesi Lain',
        'sort_order' => 1,
        'is_active' => true,
    ]);
    $otherItem = CurriculumItem::create([
        'curriculum_session_id' => $otherSession->id,
        'item_name' => 'Item Lain',
        'sequence_number' => 1,
        'material_type' => 'external_link',
        'material_value' => 'https://other.example.com',
        'is_active' => true,
    ]);

    // Tutor of classroom cannot access item from a different curriculum
    $this->actingAs($this->tutorUser)
        ->get(route('class-curriculum.show', [$this->classroom, $otherItem]))
        ->assertForbidden();
});

// ── SA-07 ── download external_link item returns 404 (no file) ────────────────

test('SA-07 download item with material_type=external_link returns 404', function () {
    $this->actingAs($this->tutorUser)
        ->get(route('class-curriculum.download', [$this->classroom, $this->item]))
        ->assertNotFound();
});

// ── SA-08 ── download file item with missing file returns 404 ─────────────────

test('SA-08 download file item with non-existent path returns 404', function () {
    $fileItem = CurriculumItem::create([
        'curriculum_session_id' => $this->session->id,
        'item_name' => 'File Materi',
        'sequence_number' => 2,
        'material_type' => 'file',
        'material_value' => 'curriculum/nonexistent-file.pdf',
        'is_active' => true,
    ]);

    $this->actingAs($this->tutorUser)
        ->get(route('class-curriculum.download', [$this->classroom, $fileItem]))
        ->assertNotFound();
});
