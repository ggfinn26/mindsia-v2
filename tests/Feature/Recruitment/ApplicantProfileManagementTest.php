<?php

use App\Models\ApplicantAccount;
use App\Models\ApplicantMasterData;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->account = ApplicantAccount::create([
        'email' => 'pelamar@example.com',
        'password' => bcrypt('password'),
        'email_verified_at' => now(),
        'is_active' => true,
    ]);

    $this->applicant = ApplicantMasterData::create([
        'applicant_account_id' => $this->account->id,
        'full_name' => 'Budi Pelamar',
        'email' => 'pelamar@example.com',
        'whatsapp_number' => '08123456789',
    ]);
});

it('can view profile', function () {
    $this->actingAs($this->account, 'applicant')
         ->get(route('applicant.profile.show'))
         ->assertOk()
         ->assertViewIs('applicant.profile.show')
         ->assertViewHas('applicant');
});

it('can update profile', function () {
    $data = [
        'full_name' => 'Budi Updated',
        'whatsapp_number' => '0899999999',
        'birth_date' => '1995-01-01',
        'gender' => 'L',
        'city' => 'Jakarta'
    ];

    $this->actingAs($this->account, 'applicant')
         ->patch(route('applicant.profile.update'), $data)
         ->assertSessionHasNoErrors()
         ->assertRedirect(route('applicant.profile.show'));

    $this->assertDatabaseHas('applicants_master_data', [
        'id' => $this->applicant->id,
        'full_name' => 'Budi Updated',
    ]);
});

it('can add education', function () {
    $data = [
        'institution_name' => 'UI',
        'degree' => 'S1',
        'major' => 'Ilmu Komputer',
        'start_year' => '2013',
        'end_year' => '2017',
        'gpa' => '3.50'
    ];

    $this->actingAs($this->account, 'applicant')
         ->post(route('applicant.profile.education.store'), $data)
         ->assertSessionHasNoErrors();

    $this->assertDatabaseHas('applicant_educations', [
        'applicant_id' => $this->applicant->id,
        'institution_name' => 'UI'
    ]);
});

