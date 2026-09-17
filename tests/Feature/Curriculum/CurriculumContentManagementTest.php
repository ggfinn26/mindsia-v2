<?php

use App\Models\Curriculum;
use App\Models\CurriculumItem;
use App\Models\CurriculumSession;
use App\Models\Employee;
use App\Models\Program;
use App\Models\User;
use Illuminate\Support\Facades\DB;

function insertCcEmployee(string $prefix = 'CC'): int
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

    return DB::table('employees')->insertGetId([
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
}

beforeEach(function () {
    $employeeId = insertCcEmployee('CC'.uniqid());
    $this->employee = Employee::find($employeeId);
    $this->boardUser = User::factory()->create([
        'employee_id' => $employeeId,
        'email' => $this->employee->email,
        'email_verified_at' => now(),
        'must_change_password' => false,
    ]);
    $this->boardUser->assignRole('CEO');

    $this->regularUser = User::factory()->create(['email_verified_at' => now(), 'must_change_password' => false]);
    $this->regularUser->assignRole('HRR');

    $this->program = Program::factory()->create();
});

// ── CC-01 ── unauthenticated ──────────────────────────────────────────────────

test('CC-01 unauthenticated curriculum index redirects to login', function () {
    $this->get(route('curriculums.index'))->assertRedirect(route('login'));
});

// ── CC-02 ── permission guard ─────────────────────────────────────────────────

test('CC-02 curriculum index without permission returns 403', function () {
    $this->actingAs($this->regularUser)
        ->get(route('curriculums.index'))
        ->assertForbidden();
});

// ── CC-03 ── index ────────────────────────────────────────────────────────────

test('CC-03 curriculum index with permission returns 200', function () {
    $this->actingAs($this->boardUser)
        ->get(route('curriculums.index'))
        ->assertOk();
});

// ── CC-04 ── store curriculum ─────────────────────────────────────────────────

test('CC-04 store curriculum creates record and redirects to show', function () {
    $this->actingAs($this->boardUser)
        ->post(route('curriculums.store'), [
            'program_id' => $this->program->id,
            'curriculum_name' => 'Kurikulum IELTS Level 1',
            'description' => 'Deskripsi kurikulum',
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('curriculums', [
        'program_id' => $this->program->id,
        'curriculum_name' => 'Kurikulum IELTS Level 1',
    ]);
});

test('CC-04b store curriculum without permission returns 403', function () {
    $this->actingAs($this->regularUser)
        ->post(route('curriculums.store'), [
            'program_id' => $this->program->id,
            'curriculum_name' => 'Kurikulum Ditolak',
        ])
        ->assertForbidden();
});

// ── CC-05 ── store session ────────────────────────────────────────────────────

test('CC-05 store session creates record and redirects', function () {
    $curriculum = Curriculum::create([
        'program_id' => $this->program->id,
        'curriculum_name' => 'Kurikulum Test',
        'is_active' => true,
    ]);

    $this->actingAs($this->boardUser)
        ->post(route('curriculums.sessions.store', $curriculum), [
            'session_number' => 1,
            'session_title' => 'PERTEMUAN 1 — SPEAKING',
        ])
        ->assertRedirect(route('curriculums.show', $curriculum));

    $this->assertDatabaseHas('curriculum_sessions', [
        'curriculum_id' => $curriculum->id,
        'session_number' => 1,
        'session_title' => 'PERTEMUAN 1 — SPEAKING',
    ]);
});

// ── CC-06 ── store item ───────────────────────────────────────────────────────

test('CC-06 store item creates record with external_link', function () {
    $curriculum = Curriculum::create(['program_id' => $this->program->id, 'curriculum_name' => 'Test', 'is_active' => true]);
    $session = CurriculumSession::create([
        'curriculum_id' => $curriculum->id,
        'session_number' => 1,
        'session_title' => 'Sesi 1',
        'sort_order' => 1,
        'is_active' => true,
    ]);

    $this->actingAs($this->boardUser)
        ->post(route('curriculum-sessions.items.store', $session), [
            'item_name' => 'Buku Speaking',
            'sequence_number' => 1,
            'material_type' => 'external_link',
            'material_value' => 'https://example.com/book',
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('curriculum_items', [
        'curriculum_session_id' => $session->id,
        'item_name' => 'Buku Speaking',
        'material_type' => 'external_link',
    ]);
});

test('CC-06b store item without permission returns 403', function () {
    $curriculum = Curriculum::create(['program_id' => $this->program->id, 'curriculum_name' => 'Test', 'is_active' => true]);
    $session = CurriculumSession::create([
        'curriculum_id' => $curriculum->id,
        'session_number' => 1,
        'session_title' => 'Sesi 1',
        'sort_order' => 1,
        'is_active' => true,
    ]);

    $this->actingAs($this->regularUser)
        ->post(route('curriculum-sessions.items.store', $session), [
            'item_name' => 'Item',
            'sequence_number' => 1,
            'material_type' => 'external_link',
            'material_value' => 'https://example.com',
        ])
        ->assertForbidden();
});

// ── CC-07 ── update curriculum ────────────────────────────────────────────────

test('CC-07 update curriculum modifies record', function () {
    $curriculum = Curriculum::create([
        'program_id' => $this->program->id,
        'curriculum_name' => 'Nama Lama',
        'is_active' => true,
    ]);

    $this->actingAs($this->boardUser)
        ->put(route('curriculums.update', $curriculum), [
            'curriculum_name' => 'Nama Baru',
        ])
        ->assertRedirect(route('curriculums.show', $curriculum));

    $this->assertDatabaseHas('curriculums', ['id' => $curriculum->id, 'curriculum_name' => 'Nama Baru']);
});

// ── CC-08 ── update session ───────────────────────────────────────────────────

test('CC-08 update session modifies record', function () {
    $curriculum = Curriculum::create(['program_id' => $this->program->id, 'curriculum_name' => 'C', 'is_active' => true]);
    $session = CurriculumSession::create([
        'curriculum_id' => $curriculum->id,
        'session_number' => 1,
        'session_title' => 'Judul Lama',
        'sort_order' => 0,
        'is_active' => true,
    ]);

    $this->actingAs($this->boardUser)
        ->put(route('curriculum-sessions.update', $session), [
            'session_number' => 1,
            'session_title' => 'Judul Baru',
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('curriculum_sessions', ['id' => $session->id, 'session_title' => 'Judul Baru']);
});

// ── CC-09 ── update item ──────────────────────────────────────────────────────

test('CC-09 update item modifies record', function () {
    $curriculum = Curriculum::create(['program_id' => $this->program->id, 'curriculum_name' => 'C', 'is_active' => true]);
    $session = CurriculumSession::create([
        'curriculum_id' => $curriculum->id, 'session_number' => 1,
        'session_title' => 'S', 'sort_order' => 0, 'is_active' => true,
    ]);
    $item = CurriculumItem::create([
        'curriculum_session_id' => $session->id,
        'item_name' => 'Item Lama',
        'sequence_number' => 1,
        'material_type' => 'external_link',
        'material_value' => 'https://old.example.com',
        'is_active' => true,
    ]);

    $this->actingAs($this->boardUser)
        ->put(route('curriculum-items.update', $item), [
            'item_name' => 'Item Baru',
            'sequence_number' => 1,
            'material_type' => 'external_link',
            'material_value' => 'https://new.example.com',
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('curriculum_items', ['id' => $item->id, 'item_name' => 'Item Baru']);
});

// ── CC-10 ── destroy item ─────────────────────────────────────────────────────

test('CC-10 destroy item deletes record', function () {
    $curriculum = Curriculum::create(['program_id' => $this->program->id, 'curriculum_name' => 'C', 'is_active' => true]);
    $session = CurriculumSession::create([
        'curriculum_id' => $curriculum->id, 'session_number' => 1,
        'session_title' => 'S', 'sort_order' => 0, 'is_active' => true,
    ]);
    $item = CurriculumItem::create([
        'curriculum_session_id' => $session->id,
        'item_name' => 'Item Hapus',
        'sequence_number' => 1,
        'material_type' => 'external_link',
        'material_value' => 'https://example.com',
        'is_active' => true,
    ]);

    $this->actingAs($this->boardUser)
        ->delete(route('curriculum-items.destroy', $item))
        ->assertRedirect();

    $this->assertDatabaseMissing('curriculum_items', ['id' => $item->id]);
});

// ── CC-11 ── destroy session ──────────────────────────────────────────────────

test('CC-11 destroy session deletes record', function () {
    $curriculum = Curriculum::create(['program_id' => $this->program->id, 'curriculum_name' => 'C', 'is_active' => true]);
    $session = CurriculumSession::create([
        'curriculum_id' => $curriculum->id, 'session_number' => 1,
        'session_title' => 'Hapus Sesi', 'sort_order' => 0, 'is_active' => true,
    ]);

    $this->actingAs($this->boardUser)
        ->delete(route('curriculum-sessions.destroy', $session))
        ->assertRedirect();

    $this->assertDatabaseMissing('curriculum_sessions', ['id' => $session->id]);
});

// ── CC-12 ── destroy curriculum ───────────────────────────────────────────────

test('CC-12 destroy curriculum deletes record', function () {
    $curriculum = Curriculum::create([
        'program_id' => $this->program->id,
        'curriculum_name' => 'Hapus Kurikulum',
        'is_active' => true,
    ]);

    $this->actingAs($this->boardUser)
        ->delete(route('curriculums.destroy', $curriculum))
        ->assertRedirect(route('curriculums.index'));

    $this->assertDatabaseMissing('curriculums', ['id' => $curriculum->id]);
});
