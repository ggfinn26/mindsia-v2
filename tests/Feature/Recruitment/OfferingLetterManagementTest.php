<?php

use App\Models\ApplicantAccount;
use App\Models\ApplicantMasterData;
use App\Models\Branch;
use App\Models\Employee;
use App\Models\JobApplication;
use App\Models\JobPermintaan;
use App\Models\JobPosting;
use App\Models\Position;
use App\Models\User;

beforeEach(function () {
    $this->branch = Branch::factory()->create();
    $this->employee = Employee::factory()->create(['branch_id' => $this->branch->id]);
    $this->user = User::factory()->create(['employee_id' => $this->employee->id]);
    $this->user->assignRole('Super Admin');

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

    $this->position = Position::firstOrCreate(['position_name' => 'Tutor']);
    $this->permintaan = JobPermintaan::create([
        'branch_id' => $this->branch->id,
        'requested_by_employee_id' => $this->employee->id,
        'status' => 'approved',
    ]);
    $this->posting = JobPosting::create([
        'job_permintaan_id' => $this->permintaan->id,
        'branch_id' => $this->branch->id,
        'position_id' => $this->position->id,
        'title' => 'Tutor',
        'status' => 'published',
        'created_by_employee_id' => $this->employee->id,
        'job_description' => 'Test job description for this posting.',
        'job_responsibilities' => 'Test responsibilities.',
        'job_requirements_text' => 'Test requirements.',
        'closing_date' => now()->addDays(10)->format('Y-m-d'),
    ]);
    $this->application = JobApplication::create([
        'applicant_id' => $this->applicant->id,
        'job_posting_id' => $this->posting->id,
        'status' => 'interview',
        'applied_at' => now(),
    ]);
});

it('can store offering letter', function () {
    $data = [
        'offered_salary' => 5000000,
        'start_date' => now()->addDays(7)->format('Y-m-d'),
        'notes' => 'Tawaran awal',
    ];
    $this->actingAs($this->user)
        ->post(route('recruitment.application.offering-letter.store', $this->application), $data)
        ->assertSessionHasNoErrors();

    $this->assertDatabaseHas('offering_letters', [
        'job_application_id' => $this->application->id,
        'offered_salary' => 5000000,
    ]);
});
