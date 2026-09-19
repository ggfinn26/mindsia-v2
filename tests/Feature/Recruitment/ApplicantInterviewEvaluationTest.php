<?php

use App\Models\ApplicantAccount;
use App\Models\ApplicantInterviewSchedule;
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
    $this->schedule = ApplicantInterviewSchedule::create([
        'job_application_id' => $this->application->id,
        'interviewer_employee_id' => $this->employee->id,
        'created_by_employee_id' => $this->employee->id,
        'interview_type' => 'offline',
        'scheduled_at' => now()->addDays(1),
    ]);
});

it('can store interview evaluation', function () {
    $data = [
        'score_education' => 3,
        'score_experience' => 3,
        'score_personality' => 4,
        'score_communication' => 3,
        'score_problem_solving' => 3,
        'decision' => 'accepted',
        'notes' => 'Sangat baik',
    ];
    $this->actingAs($this->user)
        ->post(route('recruitment.interviews.evaluation.store', $this->schedule), $data)
        ->assertSessionHasNoErrors();

    $this->assertDatabaseHas('applicant_interview_evaluations', [
        'interview_schedule_id' => $this->schedule->id,
        'decision' => 'accepted',
    ]);
});
