<?php

use App\Models\Branch;
use App\Models\Employee;
use App\Models\JobPermintaan;
use App\Models\JobPosting;
use App\Models\User;
use App\Models\Position;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->branch = Branch::factory()->create();
    $this->employee = Employee::factory()->create(['branch_id' => $this->branch->id]);
    $this->user = User::factory()->create(['employee_id' => $this->employee->id]);
    $this->user->assignRole('Super Admin');
    
    $this->position = Position::create([
        'code' => 'POS-01',
        'name' => 'Tutor',
        'is_active' => true
    ]);

    $this->permintaan = JobPermintaan::create([
        'branch_id' => $this->branch->id,
        'requested_by_employee_id' => $this->employee->id,
        'status' => JobPermintaan::STATUS_APPROVED
    ]);
});

it('can view job posting create form', function () {
    $this->actingAs($this->user)
         ->get(route('recruitment.postings.create', ['job_permintaan_id' => $this->permintaan->id]))
         ->assertOk()
         ->assertViewIs('recruitment.posting.create');
});

it('can list job postings', function () {
    JobPosting::create([
        'job_permintaan_id' => $this->permintaan->id,
        'branch_id' => $this->branch->id,
        'position_id' => $this->position->id,
        'title' => 'Tutor',
        'status' => 'draft',
        'created_by_employee_id' => $this->employee->id
    ]);

    $this->actingAs($this->user)
         ->get(route('recruitment.postings.index'))
         ->assertOk()
         ->assertViewHas('postings');
});

it('can store job posting', function () {
    $data = [
        'job_permintaan_id' => $this->permintaan->id,
        'title' => 'Tutor English',
        'slug' => 'tutor-english',
        'job_description' => 'Mengajar',
        'requirements' => 'Bisa bahasa inggris',
        'employment_type' => 'Full Time',
        'min_salary' => 1000000,
        'max_salary' => 2000000,
        'is_remote' => false,
        'publish_date' => now()->format('Y-m-d'),
        'closing_date' => now()->addDays(30)->format('Y-m-d'),
    ];
    $this->actingAs($this->user)
         ->post(route('recruitment.postings.store'), $data)
         ->assertSessionHasNoErrors();
         
    $this->assertDatabaseHas('job_postings', [
        'title' => 'Tutor English'
    ]);
});

it('can publish job posting', function () {
    $posting = JobPosting::create([
        'job_permintaan_id' => $this->permintaan->id,
        'branch_id' => $this->branch->id,
        'position_id' => $this->position->id,
        'title' => 'Tutor',
        'status' => 'draft',
        'created_by_employee_id' => $this->employee->id
    ]);

    $this->actingAs($this->user)
         ->post(route('recruitment.postings.publish', $posting))
         ->assertSessionHasNoErrors();
         
    $this->assertDatabaseHas('job_postings', [
        'id' => $posting->id,
        'status' => 'published'
    ]);
});

