<?php

use App\Models\Branch;
use App\Models\MemberData;
use App\Models\MemberRegistration;
use App\Models\Program;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('nomor registrasi pakai kode cabang member, bukan employee perujuk', function () {
    $branch = Branch::factory()->create(['code_branches' => 'A1']);
    $member = MemberData::factory()->create(['branch_id' => $branch->id]);
    $program = Program::factory()->create(['program_code' => 'TOEFL']);

    // employee_id null: dulu menghasilkan XX, sekarang harus ambil cabang member.
    $registration = MemberRegistration::factory()->create([
        'members_data_id' => $member->id,
        'program_id' => $program->id,
        'employee_id' => null,
    ]);

    expect($registration->formatted_number)->toContain('A1/TOEFL/');
    expect($registration->formatted_number)->not->toContain('XX');
});

it('fallback XX kalau member tidak punya cabang', function () {
    $member = MemberData::factory()->create(['branch_id' => null]);
    $program = Program::factory()->create(['program_code' => 'EM']);

    $registration = MemberRegistration::factory()->create([
        'members_data_id' => $member->id,
        'program_id' => $program->id,
    ]);

    expect($registration->formatted_number)->toStartWith('XX/EM/');
});
