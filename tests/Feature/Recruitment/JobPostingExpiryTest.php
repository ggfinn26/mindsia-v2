<?php

use App\Models\Branch;
use App\Models\Employee;
use App\Models\JobPermintaan;
use App\Models\JobPosting;
use App\Models\Position;
use Illuminate\Support\Facades\Artisan;

beforeEach(function () {
    $this->branch = Branch::factory()->create();
    $this->employee = Employee::factory()->create(['branch_id' => $this->branch->id]);

    $this->position = Position::firstOrCreate(['position_name' => 'Tutor']);

    $this->permintaan = JobPermintaan::create([
        'branch_id' => $this->branch->id,
        'requested_by_employee_id' => $this->employee->id,
        'status' => JobPermintaan::STATUS_APPROVED,
    ]);
});

it('closes expired job postings', function () {
    $expiredPosting = JobPosting::create([
        'job_permintaan_id' => $this->permintaan->id,
        'branch_id' => $this->branch->id,
        'position_id' => $this->position->id,
        'title' => 'Tutor 1',
        'status' => 'published',
        'job_description' => 'Test job description.',
        'job_responsibilities' => 'Test responsibilities.',
        'job_requirements_text' => 'Test requirements.',
        'closing_date' => now()->subDays(1)->format('Y-m-d'),
        'created_by_employee_id' => $this->employee->id,
    ]);

    $activePosting = JobPosting::create([
        'job_permintaan_id' => $this->permintaan->id,
        'branch_id' => $this->branch->id,
        'position_id' => $this->position->id,
        'title' => 'Tutor 2',
        'status' => 'published',
        'job_description' => 'Test job description.',
        'job_responsibilities' => 'Test responsibilities.',
        'job_requirements_text' => 'Test requirements.',
        'closing_date' => now()->addDays(5)->format('Y-m-d'),
        'created_by_employee_id' => $this->employee->id,
    ]);

    Artisan::call('recruitment:close-expired-postings');

    $this->assertDatabaseHas('job_postings', [
        'id' => $expiredPosting->id,
        'status' => 'closed',
    ]);

    $this->assertDatabaseHas('job_postings', [
        'id' => $activePosting->id,
        'status' => 'published',
    ]);
});
