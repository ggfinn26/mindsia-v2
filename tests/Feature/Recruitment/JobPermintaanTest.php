<?php

use App\Models\Branch;
use App\Models\Employee;
use App\Models\JobPermintaan;
use App\Models\User;
use App\Models\Position;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->branch = Branch::factory()->create();
    $this->employee = Employee::factory()->create(['branch_id' => $this->branch->id]);
    $this->user = User::factory()->create(['employee_id' => $this->employee->id]);
    $this->user->assignRole('Super Admin');
});

it('can list job permintaan', function () {
    JobPermintaan::create([
        'branch_id' => $this->branch->id,
        'requested_by_employee_id' => $this->employee->id,
        'status' => 'draft'
    ]);

    $this->actingAs($this->user)
         ->get(route('recruitment.permintaan.index'))
         ->assertOk()
         ->assertViewIs('recruitment.permintaan.index')
         ->assertViewHas('permintaans');
});

it('can view create job permintaan form', function () {
    $this->actingAs($this->user)
         ->get(route('recruitment.permintaan.create'))
         ->assertOk()
         ->assertViewIs('recruitment.permintaan.create');
});

it('can store job permintaan', function () {
    $data = [
        'position_id' => 1,
        'headcount' => 2,
        'reason' => 'Perlu orang baru'
    ];
    $this->actingAs($this->user)
         ->post(route('recruitment.permintaan.store'), $data)
         ->assertSessionHasNoErrors();
         
    $this->assertDatabaseHas('job_permintaan', [
        'branch_id' => $this->branch->id,
        'requested_by_employee_id' => $this->employee->id
    ]);
});

it('can show job permintaan', function () {
    $permintaan = JobPermintaan::create([
        'branch_id' => $this->branch->id,
        'requested_by_employee_id' => $this->employee->id,
        'status' => 'draft'
    ]);

    $this->actingAs($this->user)
         ->get(route('recruitment.permintaan.show', $permintaan))
         ->assertOk()
         ->assertViewIs('recruitment.permintaan.show')
         ->assertViewHas('jobPermintaan');
});

