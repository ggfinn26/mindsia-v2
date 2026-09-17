<?php

use App\Models\Employee;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/** Direct DB insert for employee (with full geo chain) — avoids factory FK deadlock under RefreshDatabase */
function insertMsEmployee(string $prefix = 'MS'): int
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
    // boardUser: CEO with marketing.kpi.view permission; has an employee record
    $boardEmployeeId = insertMsEmployee('MSB'.uniqid());
    $this->boardEmployee = Employee::find($boardEmployeeId);
    $this->boardUser = User::factory()->create([
        'employee_id' => $boardEmployeeId,
        'email' => $this->boardEmployee->email,
        'email_verified_at' => now(),
        'must_change_password' => false,
    ]);
    $this->boardUser->assignRole('CEO');

    // regularUser: HRR, no marketing.kpi.view; has its own employee record
    $regularEmployeeId = insertMsEmployee('MSR'.uniqid());
    $this->regularEmployee = Employee::find($regularEmployeeId);
    $this->regularUser = User::factory()->create([
        'employee_id' => $regularEmployeeId,
        'email' => $this->regularEmployee->email,
        'email_verified_at' => now(),
        'must_change_password' => false,
    ]);
    $this->regularUser->assignRole('HRR');
});

test('MS-01 unauthenticated kpi index redirects to login', function () {
    $this->get(route('marketing-kpi.index'))
        ->assertRedirect(route('login'));
});

test('MS-02 kpi index without permission returns 403', function () {
    $this->actingAs($this->regularUser)
        ->get(route('marketing-kpi.index'))
        ->assertForbidden();
});

test('MS-03 kpi index with marketing.kpi.view returns 200', function () {
    $this->actingAs($this->boardUser)
        ->get(route('marketing-kpi.index'))
        ->assertOk();
});

test('MS-04 kpi show without permission for own employee returns 200', function () {
    // regularUser has no marketing.kpi.view but accesses their own employee profile
    $this->actingAs($this->regularUser)
        ->get(route('marketing-kpi.show', $this->regularEmployee))
        ->assertOk();
});

test('MS-05 kpi show without permission for another employee returns 403', function () {
    // regularUser tries to view boardUser's employee profile — different employee_id
    $this->actingAs($this->regularUser)
        ->get(route('marketing-kpi.show', $this->boardEmployee))
        ->assertForbidden();
});

test('MS-06 kpi show with marketing.kpi.view for any employee returns 200', function () {
    // boardUser has CEO role (marketing.kpi.view) — can view any employee
    $this->actingAs($this->boardUser)
        ->get(route('marketing-kpi.show', $this->regularEmployee))
        ->assertOk();
});
