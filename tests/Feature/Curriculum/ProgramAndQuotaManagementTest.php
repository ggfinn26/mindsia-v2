<?php

use App\Models\Branch;
use App\Models\BranchProgramQuota;
use App\Models\Employee;
use App\Models\Program;
use App\Models\User;
use Illuminate\Support\Facades\DB;

function insertPqEmployee(string $prefix = 'PQ'): int
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
    $employeeId = insertPqEmployee('PQ'.uniqid());
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

    $this->branch = Branch::find($this->employee->branch_id);
});

// ── PQ-01 ── unauthenticated ──────────────────────────────────────────────────

test('PQ-01 unauthenticated programs index redirects to login', function () {
    $this->get(route('programs.index'))->assertRedirect(route('login'));
});

// ── PQ-02 ── permission guard ─────────────────────────────────────────────────

test('PQ-02 programs index without permission returns 403', function () {
    $this->actingAs($this->regularUser)
        ->get(route('programs.index'))
        ->assertForbidden();
});

// ── PQ-03 ── store program ────────────────────────────────────────────────────

test('PQ-03 store program creates record and redirects to index', function () {
    $code = 'PRG-'.uniqid();

    $this->actingAs($this->boardUser)
        ->post(route('programs.store'), [
            'program_name' => 'IELTS Preparation',
            'program_code' => $code,
            'program_price' => 2500000,
        ])
        ->assertRedirect(route('programs.index'));

    $this->assertDatabaseHas('programs', [
        'program_code' => $code,
        'program_name' => 'IELTS Preparation',
    ]);
});

test('PQ-03b store program without permission returns 403', function () {
    $this->actingAs($this->regularUser)
        ->post(route('programs.store'), [
            'program_name' => 'Ditolak',
            'program_code' => 'PRG-REJECT',
            'program_price' => 0,
        ])
        ->assertForbidden();
});

// ── PQ-04 ── unique program_code ──────────────────────────────────────────────

test('PQ-04 store program with duplicate code returns validation error', function () {
    $code = 'PRG-DUP-'.uniqid();
    Program::factory()->create(['program_code' => $code]);

    $this->actingAs($this->boardUser)
        ->post(route('programs.store'), [
            'program_name' => 'Duplikat',
            'program_code' => $code,
            'program_price' => 1000000,
        ])
        ->assertRedirect()
        ->assertSessionHasErrors('program_code');
});

// ── PQ-05 ── destroy blocked when active classes exist ────────────────────────

test('PQ-05 destroy program with active class returns error flash', function () {
    $program = Program::factory()->create();
    $employeeId2 = insertPqEmployee('PQT'.uniqid());
    DB::table('classes')->insert([
        'program_id' => $program->id,
        'branch_id' => $this->branch->id,
        'class_name' => 'Active Class',
        'tutor_id' => $employeeId2,
        'day_of_week' => 'Senin',
        'week_count' => 4,
        'start_date' => now()->toDateString(),
        'end_date' => now()->addMonths(1)->toDateString(),
        'start_time_primary' => '09:00',
        'end_time_primary' => '11:00',
        'status' => 'active',
        'created_at' => now(), 'updated_at' => now(),
    ]);

    $this->actingAs($this->boardUser)
        ->delete(route('programs.destroy', $program))
        ->assertRedirect()
        ->assertSessionHas('error');

    $this->assertDatabaseHas('programs', ['id' => $program->id, 'is_active' => true]);
});

// ── PQ-06 ── destroy sets is_active=false when no active classes ──────────────

test('PQ-06 destroy program without active classes sets is_active false', function () {
    $program = Program::factory()->create();

    $this->actingAs($this->boardUser)
        ->delete(route('programs.destroy', $program))
        ->assertRedirect(route('programs.index'));

    $this->assertDatabaseHas('programs', ['id' => $program->id, 'is_active' => false]);
});

// ── PQ-07 ── store quota (upsert) ─────────────────────────────────────────────

test('PQ-07 store quota creates entry via updateOrCreate', function () {
    $program = Program::factory()->create();

    $this->actingAs($this->boardUser)
        ->post(route('programs.quotas.store', $program), [
            'branch_id' => $this->branch->id,
            'quota_limit' => 30,
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('branch_program_quotas', [
        'branch_id' => $this->branch->id,
        'program_id' => $program->id,
        'quota_limit' => 30,
    ]);
});

test('PQ-07b store quota upserts existing record', function () {
    $program = Program::factory()->create();
    BranchProgramQuota::create([
        'branch_id' => $this->branch->id,
        'program_id' => $program->id,
        'quota_limit' => 20,
    ]);

    $this->actingAs($this->boardUser)
        ->post(route('programs.quotas.store', $program), [
            'branch_id' => $this->branch->id,
            'quota_limit' => 50,
        ])
        ->assertRedirect();

    // Upsert: same branch+program → only one record for this pair
    $this->assertDatabaseHas('branch_program_quotas', [
        'branch_id' => $this->branch->id,
        'program_id' => $program->id,
        'quota_limit' => 50,
    ]);
    expect(
        \App\Models\BranchProgramQuota::where('branch_id', $this->branch->id)
            ->where('program_id', $program->id)
            ->count()
    )->toBe(1);
});

test('PQ-07c store quota without permission returns 403', function () {
    $program = Program::factory()->create();

    $this->actingAs($this->regularUser)
        ->post(route('programs.quotas.store', $program), [
            'branch_id' => $this->branch->id,
            'quota_limit' => 10,
        ])
        ->assertForbidden();
});

// ── PQ-08 ── update quota ─────────────────────────────────────────────────────

test('PQ-08 update quota modifies limit', function () {
    $program = Program::factory()->create();
    $quota = BranchProgramQuota::create([
        'branch_id' => $this->branch->id,
        'program_id' => $program->id,
        'quota_limit' => 20,
    ]);

    $this->actingAs($this->boardUser)
        ->put(route('quotas.update', $quota), [
            'quota_limit' => 40,
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('branch_program_quotas', ['id' => $quota->id, 'quota_limit' => 40]);
});

// ── PQ-09 ── delete quota ─────────────────────────────────────────────────────

test('PQ-09 delete quota removes record', function () {
    $program = Program::factory()->create();
    $quota = BranchProgramQuota::create([
        'branch_id' => $this->branch->id,
        'program_id' => $program->id,
        'quota_limit' => 20,
    ]);

    $this->actingAs($this->boardUser)
        ->delete(route('quotas.destroy', $quota))
        ->assertRedirect();

    $this->assertDatabaseMissing('branch_program_quotas', ['id' => $quota->id]);
});
