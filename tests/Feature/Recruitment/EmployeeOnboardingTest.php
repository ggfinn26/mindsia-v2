<?php

use App\Models\ApplicantAccount;
use App\Models\ApplicantMasterData;
use App\Models\Branch;
use App\Models\Employee;
use App\Models\JobPosting;
use App\Models\JobPermintaan;
use App\Models\Position;
use App\Models\JobApplication;
use App\Models\OfferingLetter;
use App\Models\EmployeeOnboarding;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

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
    
    $this->position = Position::create(['code' => 'POS-05', 'name' => 'Tutor', 'is_active' => true]);
    $this->permintaan = JobPermintaan::create([
        'branch_id' => $this->branch->id,
        'requested_by_employee_id' => $this->employee->id,
        'status' => 'approved'
    ]);
    $this->posting = JobPosting::create([
        'job_permintaan_id' => $this->permintaan->id,
        'branch_id' => $this->branch->id,
        'position_id' => $this->position->id,
        'title' => 'Tutor',
        'status' => 'published',
        'created_by_employee_id' => $this->employee->id,
        'closing_date' => now()->addDays(10)->format('Y-m-d')
    ]);
    $this->application = JobApplication::create([
        'applicant_id' => $this->applicant->id,
        'job_posting_id' => $this->posting->id,
        'status' => 'offering_accepted'
    ]);
    $this->offering = OfferingLetter::create([
        'job_application_id' => $this->application->id,
        'status' => 'accepted',
        'offered_salary' => 5000000,
        'agreed_salary' => 5000000,
        'offered_position_id' => $this->position->id,
        'joining_date' => now()->addDays(3)->format('Y-m-d'),
    ]);
});

it('can store onboarding', function () {
    $data = [
        'branch_id' => $this->branch->id,
        'position_id' => $this->position->id,
        'onboarding_start_date' => now()->format('Y-m-d'),
        'onboarding_end_date' => now()->addDays(14)->format('Y-m-d'),
        'notes' => 'Onboarding awal'
    ];
    $this->actingAs($this->user)
         ->post(route('recruitment.application.onboarding.store', $this->application), $data)
         ->assertSessionHasNoErrors();
         
    $this->assertDatabaseHas('employee_onboardings', [
        'job_application_id' => $this->application->id,
    ]);
});

it('can review onboarding', function () {
    $onboarding = EmployeeOnboarding::create([
        'job_application_id' => $this->application->id,
        'offering_letter_id' => $this->offering->id,
        'branch_id' => $this->branch->id,
        'position_id' => $this->position->id,
        'onboarding_start_date' => now()->format('Y-m-d'),
        'onboarding_end_date' => now()->addDays(14)->format('Y-m-d'),
        'status' => 'pending_review'
    ]);

    $this->actingAs($this->user)
         ->post(route('recruitment.onboarding.review', $onboarding), [
             'status' => 'approved',
             'review_notes' => 'Lanjut'
         ])
         ->assertSessionHasNoErrors();
         
    $this->assertDatabaseHas('employee_onboardings', [
        'id' => $onboarding->id,
        'status' => 'approved'
    ]);
});


it('can complete onboarding', function () {
    $onboarding = EmployeeOnboarding::create([
        'job_application_id' => $this->application->id,
        'offering_letter_id' => $this->offering->id,
        'branch_id' => $this->branch->id,
        'position_id' => $this->position->id,
        'onboarding_start_date' => now()->format('Y-m-d'),
        'onboarding_end_date' => now()->addDays(14)->format('Y-m-d'),
        'status' => 'approved'
    ]);

    $this->actingAs($this->user)
         ->post(route('recruitment.onboarding.complete', $onboarding))
         ->assertSessionHasNoErrors();
         
    $this->assertDatabaseHas('employee_onboardings', [
        'id' => $onboarding->id,
        'status' => 'completed'
    ]);
});

