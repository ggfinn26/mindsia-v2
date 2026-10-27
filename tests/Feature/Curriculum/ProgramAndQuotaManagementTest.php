<?php

use App\Models\Branch;
use App\Models\BranchProgramQuota;
use App\Models\Employee;
use App\Models\Program;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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

test('PQ-03c store sanitizes description - keeps basic formatting, strips scripts', function () {
    $code = 'PRG-'.uniqid();

    $this->actingAs($this->boardUser)
        ->post(route('programs.store'), [
            'program_name' => 'Formatted',
            'program_code' => $code,
            'program_price' => 1000000,
            'program_description' => '<p style="text-align: justify;"><strong>Tebal</strong></p>'
                .'<ul><li>Poin</li></ul><script>alert(1)</script><a href="javascript:evil()">x</a>',
        ])
        ->assertRedirect(route('programs.index'));

    $saved = Program::where('program_code', $code)->value('program_description');

    expect($saved)
        ->toContain('<strong>Tebal</strong>')
        ->toContain('text-align:justify')
        ->toContain('<ul>')
        ->not->toContain('<script>')
        ->not->toContain('javascript:');
});

test('PQ-03e tagline strips rich-text markup for card teasers', function () {
    $program = Program::factory()->create([
        'program_description' => '<p style="text-align:center;"><strong>EMP</strong> program keren</p><ul><li>Satu</li></ul>',
    ]);

    expect($program->tagline)
        ->toBe('EMP program keren Satu')
        ->not->toContain('<');
});

test('PQ-03d store with blank description stores null', function () {
    $code = 'PRG-'.uniqid();

    $this->actingAs($this->boardUser)
        ->post(route('programs.store'), [
            'program_name' => 'Blank',
            'program_code' => $code,
            'program_price' => 1000000,
            'program_description' => '   ',
        ])
        ->assertRedirect(route('programs.index'));

    expect(Program::where('program_code', $code)->value('program_description'))->toBeNull();
});

test('PQ-03f store program persists landing fields, drops empty rows, auto-slugs', function () {
    $code = 'PRG-'.uniqid();

    $this->actingAs($this->boardUser)
        ->post(route('programs.store'), [
            'program_name' => 'Landing Rich Program',
            'program_code' => $code,
            'program_price' => 3000000,
            'tagline' => 'Program penuh fitur',
            'badge' => 'Favorit',
            'target_level' => 'Lanjutan',
            'duration' => '3 Bulan',
            'facilities' => ['Sertifikat', '', 'Kelas garansi'],
            'benefits' => ['Interaktif'],
            'schedule' => [
                ['day' => 'Senin', 'start' => '08:00', 'end' => '10:00'],
                ['day' => '', 'start' => '', 'end' => ''],
            ],
            'faqs' => [
                ['q' => 'Berapa lama?', 'a' => '3 bulan'],
                ['q' => '', 'a' => 'tanpa tanya'],
            ],
        ])
        ->assertRedirect(route('programs.index'));

    $program = Program::where('program_code', $code)->first();

    expect($program->slug)->toBe('landing-rich-program');
    expect($program->getRawOriginal('tagline'))->toBe('Program penuh fitur');
    expect($program->badge)->toBe('Favorit');
    expect($program->facilities)->toBe(['Sertifikat', 'Kelas garansi']);
    expect($program->benefits)->toBe(['Interaktif']);
    expect($program->schedule)->toHaveCount(1);
    expect($program->schedule[0]['day'])->toBe('Senin');
    expect($program->faqs)->toHaveCount(1);
    expect($program->faqs[0]['q'])->toBe('Berapa lama?');
});

test('PQ-03g store rejects schedule day outside primary days', function () {
    $this->actingAs($this->boardUser)
        ->post(route('programs.store'), [
            'program_name' => 'Bad Day',
            'program_code' => 'PRG-'.uniqid(),
            'program_price' => 1000000,
            'schedule' => [
                ['day' => 'Kamis', 'start' => '08:00', 'end' => '10:00'],
            ],
        ])
        ->assertRedirect()
        ->assertSessionHasErrors('schedule.0.day');
});

test('PQ-03h store program stores uploaded gallery images', function () {
    Storage::fake('public');
    $code = 'PRG-'.uniqid();

    $this->actingAs($this->boardUser)
        ->post(route('programs.store'), [
            'program_name' => 'With Gallery',
            'program_code' => $code,
            'program_price' => 1000000,
            'gallery_images' => [
                UploadedFile::fake()->image('a.jpg'),
                UploadedFile::fake()->image('b.jpg'),
            ],
        ])
        ->assertRedirect(route('programs.index'));

    $program = Program::where('program_code', $code)->first();
    expect($program->gallery)->toHaveCount(2);
    Storage::disk('public')->assertExists($program->gallery[0]);
});

test('PQ-03i update removes selected gallery images and appends new ones', function () {
    Storage::fake('public');
    $program = Program::factory()->create([
        'gallery' => ['programs/gallery/old1.jpg', 'programs/gallery/old2.jpg'],
    ]);

    $this->actingAs($this->boardUser)
        ->put(route('programs.update', $program), [
            'program_name' => $program->program_name,
            'program_code' => $program->program_code,
            'program_price' => $program->program_price,
            'remove_gallery' => ['programs/gallery/old1.jpg'],
            'gallery_images' => [UploadedFile::fake()->image('new.jpg')],
        ])
        ->assertRedirect();

    $program->refresh();
    expect($program->gallery)->toHaveCount(2)
        ->and($program->gallery)->toContain('programs/gallery/old2.jpg')
        ->and($program->gallery)->not->toContain('programs/gallery/old1.jpg');
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
            'quotas' => [
                $this->branch->id => 30,
            ],
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
            'quotas' => [
                $this->branch->id => 50,
            ],
        ])
        ->assertRedirect();

    // Upsert: same branch+program → only one record for this pair
    $this->assertDatabaseHas('branch_program_quotas', [
        'branch_id' => $this->branch->id,
        'program_id' => $program->id,
        'quota_limit' => 50,
    ]);
    expect(
        BranchProgramQuota::where('branch_id', $this->branch->id)
            ->where('program_id', $program->id)
            ->count()
    )->toBe(1);
});

test('PQ-07c store quota without permission returns 403', function () {
    $program = Program::factory()->create();

    $this->actingAs($this->regularUser)
        ->post(route('programs.quotas.store', $program), [
            'quotas' => [
                $this->branch->id => 10,
            ],
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
