<?php

use App\Models\Employee;
use App\Models\MarketingKpiRankingRule;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/** Direct DB insert for employee (with full geo chain) — avoids factory FK deadlock under RefreshDatabase */
function insertMrrEmployee(string $prefix = 'MRR'): int
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
    $employeeId = insertMrrEmployee('MRR'.uniqid());
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

test('MR-01 unauthenticated rules page redirects to login', function () {
    $this->get(route('marketing-kpi.rules'))
        ->assertRedirect(route('login'));
});

test('MR-02 rules page without permission returns 403', function () {
    $this->actingAs($this->regularUser)
        ->get(route('marketing-kpi.rules'))
        ->assertForbidden();
});

test('MR-03 rules page with permission returns 200', function () {
    $this->actingAs($this->boardUser)
        ->get(route('marketing-kpi.rules'))
        ->assertOk();
});

test('MR-04 storeRule without permission returns 403', function () {
    $this->actingAs($this->regularUser)
        ->post(route('marketing-kpi.rules.store'), [
            'rule_code' => 'RULE-A-'.uniqid(),
            'rule_name' => 'Rule Alpha',
            'revenue_basis' => 'cash_collected',
        ])
        ->assertForbidden();
});

test('MR-05 storeRule with permission persists rule as inactive', function () {
    $ruleCode = 'RULE-B-'.uniqid();
    $this->actingAs($this->boardUser)
        ->post(route('marketing-kpi.rules.store'), [
            'rule_code' => $ruleCode,
            'rule_name' => 'Rule Beta',
            'revenue_basis' => 'cash_collected',
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('marketing_kpi_ranking_rules', [
        'rule_code' => $ruleCode,
        'rule_name' => 'Rule Beta',
        'is_active' => false,
    ]);
});

test('MR-06 storeRule with duplicate rule_code returns validation error', function () {
    $dupCode = 'RULE-DUP-'.uniqid();
    MarketingKpiRankingRule::create([
        'rule_code' => $dupCode,
        'rule_name' => 'Existing Rule',
        'revenue_basis' => 'cash_collected',
        'is_active' => false,
        'created_by_employee_id' => $this->employee->id,
    ]);

    $this->actingAs($this->boardUser)
        ->post(route('marketing-kpi.rules.store'), [
            'rule_code' => $dupCode,
            'rule_name' => 'Duplicate',
            'revenue_basis' => 'cash_collected',
        ])
        ->assertRedirect()
        ->assertSessionHasErrors('rule_code');
});

test('MR-07 activateRule without permission returns 403', function () {
    $rule = MarketingKpiRankingRule::create([
        'rule_code' => 'RULE-C-'.uniqid(),
        'rule_name' => 'Rule Charlie',
        'revenue_basis' => 'cash_collected',
        'is_active' => false,
        'created_by_employee_id' => $this->employee->id,
    ]);

    $this->actingAs($this->regularUser)
        ->post(route('marketing-kpi.rules.activate', $rule))
        ->assertForbidden();
});

test('MR-08 activateRule enforces single active rule', function () {
    $uid = uniqid();
    $rule1 = MarketingKpiRankingRule::create([
        'rule_code' => 'RULE-D-'.$uid,
        'rule_name' => 'Rule Delta',
        'revenue_basis' => 'cash_collected',
        'is_active' => false,
        'created_by_employee_id' => $this->employee->id,
    ]);

    $rule2 = MarketingKpiRankingRule::create([
        'rule_code' => 'RULE-E-'.$uid,
        'rule_name' => 'Rule Echo',
        'revenue_basis' => 'cash_collected',
        'is_active' => false,
        'created_by_employee_id' => $this->employee->id,
    ]);

    // Activate rule1 — should be active, rule2 should remain inactive
    $this->actingAs($this->boardUser)
        ->post(route('marketing-kpi.rules.activate', $rule1))
        ->assertRedirect();

    expect($rule1->fresh()->is_active)->toBeTrue();
    expect($rule2->fresh()->is_active)->toBeFalse();

    // Activate rule2 — should flip: rule2 active, rule1 inactive
    $this->actingAs($this->boardUser)
        ->post(route('marketing-kpi.rules.activate', $rule2))
        ->assertRedirect();

    expect($rule2->fresh()->is_active)->toBeTrue();
    expect($rule1->fresh()->is_active)->toBeFalse();
});
