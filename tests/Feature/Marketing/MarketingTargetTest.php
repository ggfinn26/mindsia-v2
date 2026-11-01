<?php

use App\Models\Employee;
use App\Models\MarketingKpiSnapshot;
use App\Models\User;
use App\Repositories\Marketing\MarketingTargetRepository;
use Illuminate\Support\Facades\DB;

/** Direct DB insert for employee (with full geo chain) — avoids factory FK deadlock under RefreshDatabase */
function insertMtEmployee(string $prefix = 'MT'): int
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
    $employeeId = insertMtEmployee('MT'.uniqid());
    $this->employee = Employee::find($employeeId);
    $this->boardUser = User::factory()->create([
        'employee_id' => $employeeId,
        'email' => $this->employee->email,
        'email_verified_at' => now(),
        'must_change_password' => false,
    ]);
    $this->boardUser->assignRole('CEO');

    $this->regularUser = User::factory()->create();
    $this->regularUser->assignRole('HRR');
});

test('MT-01 unauthenticated index redirects to login', function () {
    $this->get(route('marketing-target.index'))
        ->assertRedirect(route('login'));
});

test('MT-02 index without permission returns 403', function () {
    $this->actingAs($this->regularUser)
        ->get(route('marketing-target.index'))
        ->assertForbidden();
});

test('MT-03 index with permission returns 200', function () {
    $this->actingAs($this->boardUser)
        ->get(route('marketing-target.index'))
        ->assertOk();
});

test('MT-04 setEmployeeTarget without permission returns 403', function () {
    $this->actingAs($this->regularUser)
        ->post(route('marketing-target.employee'), [
            'employee_id' => $this->employee->id,
            'period_month' => 6,
            'period_year' => 2026,
            'classes_target' => 10,
            'omzet_target' => 5000000,
        ])
        ->assertForbidden();
});

test('MT-05 setEmployeeTarget with permission stores marketing_targets row', function () {
    $this->actingAs($this->boardUser)
        ->post(route('marketing-target.employee'), [
            'employee_id' => $this->employee->id,
            'period_month' => 6,
            'period_year' => 2026,
            'classes_target' => 10,
            'omzet_target' => 5000000,
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('marketing_targets', [
        'employee_id' => $this->employee->id,
        'period_month' => 6,
        'period_year' => 2026,
        'classes_target' => 10,
    ]);
});

test('MT-06 setPositionDefault without permission returns 403', function () {
    $positionId = DB::table('positions')->insertGetId([
        'position_name' => 'Marketer',
        'created_at' => now(), 'updated_at' => now(),
    ]);

    $this->actingAs($this->regularUser)
        ->post(route('marketing-target.position-default'), [
            'position_id' => $positionId,
            'period_month' => 6,
            'period_year' => 2026,
            'classes_target' => 8,
            'omzet_target' => 3000000,
        ])
        ->assertForbidden();
});

test('MT-07 setPositionDefault with permission stores marketing_target_defaults row', function () {
    $positionId = DB::table('positions')->insertGetId([
        'position_name' => 'Marketer',
        'created_at' => now(), 'updated_at' => now(),
    ]);

    $this->actingAs($this->boardUser)
        ->post(route('marketing-target.position-default'), [
            'position_id' => $positionId,
            'period_month' => 6,
            'period_year' => 2026,
            'classes_target' => 8,
            'omzet_target' => 3000000,
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('marketing_target_defaults', [
        'position_id' => $positionId,
        'period_month' => 6,
        'period_year' => 2026,
        'classes_target' => 8,
    ]);
});

it('regenerates the snapshot when an employee target changes', function () {
    $employee = Employee::factory()->create();
    $month = now()->month;
    $year = now()->year;

    app(MarketingTargetRepository::class)->setEmployeeTarget([
        'employee_id' => $employee->id, 'period_month' => $month, 'period_year' => $year,
        'classes_target' => 3, 'omzet_target' => 1000000, 'created_by_employee_id' => $employee->id,
    ]);
    $latest = fn () => MarketingKpiSnapshot::where('employee_id', $employee->id)
        ->where('period_month', $month)->where('period_year', $year)->latest('id')->first();
    expect((float) $latest()->omzet_target)->toBe(1000000.0);

    app(MarketingTargetRepository::class)->setEmployeeTarget([
        'employee_id' => $employee->id, 'period_month' => $month, 'period_year' => $year,
        'classes_target' => 5, 'omzet_target' => 2500000, 'created_by_employee_id' => $employee->id,
    ]);
    expect((float) $latest()->omzet_target)->toBe(2500000.0);
    expect($latest()->classes_target)->toBe(5);
});

it('does not regenerate a finalized snapshot when the target changes', function () {
    $employee = Employee::factory()->create();
    $month = now()->month;
    $year = now()->year;
    $repo = app(MarketingTargetRepository::class);

    $repo->setEmployeeTarget(['employee_id' => $employee->id, 'period_month' => $month, 'period_year' => $year, 'classes_target' => 3, 'omzet_target' => 1000000, 'created_by_employee_id' => $employee->id]);
    MarketingKpiSnapshot::where('employee_id', $employee->id)->update(['finalized_at' => now()]);
    $before = MarketingKpiSnapshot::where('employee_id', $employee->id)->count();

    $repo->setEmployeeTarget(['employee_id' => $employee->id, 'period_month' => $month, 'period_year' => $year, 'classes_target' => 9, 'omzet_target' => 9000000, 'created_by_employee_id' => $employee->id]);

    expect(MarketingKpiSnapshot::where('employee_id', $employee->id)->count())->toBe($before);
});
