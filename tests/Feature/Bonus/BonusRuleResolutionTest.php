<?php

namespace Tests\Feature\Bonus;

use App\Models\EmployeeAttendanceMonthlyRecap;
use App\Models\KpiBonusRule;
use App\Models\KpiBonusRuleTier;
use App\Models\MarketingBonusRule;
use App\Models\MarketingBonusRuleTier;
use App\Models\MarketingPerformance;
use App\Models\SpecialBonusRule;
use App\Models\SpecialBonusRuleCondition;
use App\Models\User;
use App\Services\Bonus\BonusRuleResolverService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class BonusRuleResolutionTest extends TestCase
{
    use RefreshDatabase;

    private BonusRuleResolverService $resolver;

    private int $employeeId;

    private int $positionId;

    private int $roleId;

    private float $baseSalary = 10_000_000;

    private User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->resolver = app(BonusRuleResolverService::class);

        // Create employee via DB::table to avoid FK deadlock chain
        $this->employeeId = $this->insertEmployee('BR-RES', hq: true);

        // Create Spatie Role + Position
        $this->roleId = DB::table('roles')->insertGetId([
            'name' => 'TEST_ROLE_BR',
            'guard_name' => 'web',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->positionId = DB::table('positions')->insertGetId([
            'position_name' => 'Test Position BR',
            'role_id' => $this->roleId,
            'hierarchy_order' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create admin user — MUST be before any factory creates (BonusRuleObserver needs auth)
        $this->adminUser = User::factory()->create(['employee_id' => $this->employeeId]);
        $this->actingAs($this->adminUser);
    }

    // ============================================================
    // Marketing Bonus Resolve (BR-01 to BR-05)
    // ============================================================

    // BR-01 — Resolve marketing bonus — satu rule global match tier
    public function test_br01_resolve_marketing_bonus_satu_rule_global_match_tier(): void
    {
        $rule = MarketingBonusRule::factory()->globalScope()->create([
            'rule_code' => 'MKT-RESOLVE-01',
            'bonus_basis' => 'marketing_mpi',
            'reward_basis' => 'base_salary',
            'is_active' => true,
        ]);

        MarketingBonusRuleTier::factory()->create([
            'marketing_bonus_rule_id' => $rule->id,
            'minimum_tenure_months' => 0,
            'maximum_tenure_months' => 24,
            'minimum_achievement_percentage' => 0,
            'maximum_achievement_percentage' => null,
            'reward_type' => 'percentage',
            'reward_value' => 5.00,
        ]);

        // Seed marketing performance with achievement_percentage
        MarketingPerformance::create([
            'employee_id' => $this->employeeId,
            'period_year' => 2026,
            'period_month' => 9,
            'achievement_percentage' => 100.00,
            'cash_collected_actual' => 50_000_000,
            'status' => 'approved',
        ]);

        $results = $this->resolver->resolveMarketingBonus(
            $this->employeeId,
            $this->positionId,
            $this->roleId,
            2026, 9,
            $this->baseSalary,
            tenureMonths: 12,
        );

        $this->assertCount(1, $results);
        $this->assertEquals('marketing', $results[0]['type']);
        $this->assertEquals(500_000, $results[0]['calculated_amount']); // 5% of 10M
    }

    // BR-02 — Resolve marketing bonus — multi-scope dijumlahkan
    public function test_br02_resolve_marketing_bonus_multi_scope(): void
    {
        // Global rule: 5% base salary
        $globalRule = MarketingBonusRule::factory()->globalScope()->create([
            'rule_code' => 'MKT-GLOBAL',
            'bonus_basis' => 'marketing_mpi',
            'reward_basis' => 'base_salary',
            'is_active' => true,
        ]);

        MarketingBonusRuleTier::factory()->create([
            'marketing_bonus_rule_id' => $globalRule->id,
            'minimum_tenure_months' => 0,
            'maximum_tenure_months' => null,
            'minimum_achievement_percentage' => 0,
            'maximum_achievement_percentage' => null,
            'reward_type' => 'percentage',
            'reward_value' => 5.00,
        ]);

        // Per-role rule: 3% base salary
        $roleRule = MarketingBonusRule::factory()->roleScope($this->roleId)->create([
            'rule_code' => 'MKT-ROLE',
            'bonus_basis' => 'marketing_mpi',
            'reward_basis' => 'base_salary',
            'is_active' => true,
        ]);

        MarketingBonusRuleTier::factory()->create([
            'marketing_bonus_rule_id' => $roleRule->id,
            'minimum_tenure_months' => 0,
            'maximum_tenure_months' => null,
            'minimum_achievement_percentage' => 0,
            'maximum_achievement_percentage' => null,
            'reward_type' => 'percentage',
            'reward_value' => 3.00,
        ]);

        // Seed marketing performance
        MarketingPerformance::create([
            'employee_id' => $this->employeeId,
            'period_year' => 2026,
            'period_month' => 9,
            'achievement_percentage' => 100.00,
            'cash_collected_actual' => 50_000_000,
            'status' => 'approved',
        ]);

        $results = $this->resolver->resolveMarketingBonus(
            $this->employeeId,
            $this->positionId,
            $this->roleId,
            2026, 9,
            $this->baseSalary,
            tenureMonths: 12,
        );

        $this->assertCount(2, $results);
        $total = collect($results)->sum('calculated_amount');
        $this->assertEquals(800_000, $total); // 5% + 3% of 10M = 800K
    }

    // BR-03 — Resolve marketing bonus — tidak ada tier match
    public function test_br03_resolve_marketing_bonus_tidak_ada_tier_match(): void
    {
        $rule = MarketingBonusRule::factory()->globalScope()->create([
            'rule_code' => 'MKT-NOTIER',
            'bonus_basis' => 'marketing_mpi',
            'reward_basis' => 'base_salary',
            'is_active' => true,
        ]);

        MarketingBonusRuleTier::factory()->create([
            'marketing_bonus_rule_id' => $rule->id,
            'minimum_tenure_months' => 0,
            'maximum_tenure_months' => null,
            'minimum_achievement_percentage' => 80.00,
            'maximum_achievement_percentage' => 100.00,
            'reward_type' => 'percentage',
            'reward_value' => 10.00,
        ]);

        // No marketing performance → achievement = 0.0 → below 80% threshold
        $results = $this->resolver->resolveMarketingBonus(
            $this->employeeId,
            $this->positionId,
            $this->roleId,
            2026, 9,
            $this->baseSalary,
            tenureMonths: 12,
        );

        $this->assertEmpty($results, 'No tier match when achievement below threshold');
    }

    // BR-04 — Resolve marketing bonus — rule is_active=false dilewati
    public function test_br04_resolve_marketing_bonus_inactive_rule_dilewati(): void
    {
        MarketingBonusRule::factory()->inactive()->globalScope()->create([
            'rule_code' => 'MKT-INACTIVE',
            'bonus_basis' => 'marketing_mpi',
            'reward_basis' => 'base_salary',
        ]);

        $results = $this->resolver->resolveMarketingBonus(
            $this->employeeId,
            $this->positionId,
            $this->roleId,
            2026, 9,
            $this->baseSalary,
            tenureMonths: 12,
        );

        $this->assertEmpty($results, 'Inactive rules should be skipped by forEmployee scope');
    }

    // BR-05 — Resolve marketing bonus — bonus_basis=revenue
    public function test_br05_resolve_marketing_bonus_revenue_basis(): void
    {
        $rule = MarketingBonusRule::factory()->globalScope()->create([
            'rule_code' => 'MKT-REVENUE',
            'bonus_basis' => 'revenue',
            'revenue_basis' => 'cash_collected',
            'reward_basis' => 'base_salary',
            'is_active' => true,
        ]);

        MarketingBonusRuleTier::factory()->create([
            'marketing_bonus_rule_id' => $rule->id,
            'minimum_tenure_months' => 0,
            'maximum_tenure_months' => null,
            'minimum_achievement_percentage' => 0,
            'maximum_achievement_percentage' => null,
            'reward_type' => 'percentage',
            'reward_value' => 3.00,
        ]);

        // Create marketing performance with revenue data
        MarketingPerformance::create([
            'employee_id' => $this->employeeId,
            'period_year' => 2026,
            'period_month' => 9,
            'cash_collected_actual' => 50000000,
            'achievement_percentage' => 100.00,
            'status' => 'approved',
        ]);

        $results = $this->resolver->resolveMarketingBonus(
            $this->employeeId,
            $this->positionId,
            $this->roleId,
            2026, 9,
            $this->baseSalary,
            tenureMonths: 12,
        );

        // revenue basis: resolveRevenue('cash_collected', ...) returns cash_collected_actual
        // tier match: achievement >= 0 → match → reward = 3% of base_salary
        $this->assertCount(1, $results);
        $this->assertEquals(300_000, $results[0]['calculated_amount']); // 3% of 10M
    }

    // ============================================================
    // KPI Bonus Resolve (BR-06 to BR-08)
    // ============================================================

    // BR-06 — Resolve KPI bonus — tier match berhasil
    public function test_br06_resolve_kpi_bonus_tier_match(): void
    {
        $rule = KpiBonusRule::factory()->globalScope()->create([
            'rule_code' => 'KPI-RESOLVE-01',
            'reward_basis' => 'base_salary',
            'is_active' => true,
        ]);

        KpiBonusRuleTier::factory()->create([
            'kpi_bonus_rule_id' => $rule->id,
            'minimum_score' => 80.00,
            'maximum_score' => 100.00,
            'reward_type' => 'percentage',
            'reward_value' => 10.00,
        ]);

        $results = $this->resolver->resolveKpiBonus(
            $this->employeeId,
            $this->positionId,
            $this->roleId,
            kpiScore: 90.00,
            baseSalary: $this->baseSalary,
        );

        $this->assertCount(1, $results);
        $this->assertEquals('kpi', $results[0]['type']);
        $this->assertEquals(1_000_000, $results[0]['calculated_amount']); // 10% of 10M
    }

    // BR-07 — Resolve KPI bonus — tidak ada tier match
    public function test_br07_resolve_kpi_bonus_tidak_ada_tier_match(): void
    {
        $rule = KpiBonusRule::factory()->globalScope()->create([
            'rule_code' => 'KPI-NO-MATCH',
            'reward_basis' => 'base_salary',
            'is_active' => true,
        ]);

        KpiBonusRuleTier::factory()->create([
            'kpi_bonus_rule_id' => $rule->id,
            'minimum_score' => 70.00,
            'maximum_score' => 100.00,
            'reward_type' => 'percentage',
            'reward_value' => 10.00,
        ]);

        $results = $this->resolver->resolveKpiBonus(
            $this->employeeId,
            $this->positionId,
            $this->roleId,
            kpiScore: 50.00,
            baseSalary: $this->baseSalary,
        );

        $this->assertEmpty($results, 'No KPI bonus when score below tier minimum');
    }

    // BR-08 — Resolve KPI bonus — reward_type=fixed
    public function test_br08_resolve_kpi_bonus_reward_type_fixed(): void
    {
        $rule = KpiBonusRule::factory()->globalScope()->create([
            'rule_code' => 'KPI-FIXED',
            'reward_basis' => 'base_salary',
            'is_active' => true,
        ]);

        KpiBonusRuleTier::factory()->create([
            'kpi_bonus_rule_id' => $rule->id,
            'minimum_score' => 0,
            'maximum_score' => 100.00,
            'reward_type' => 'fixed',
            'reward_value' => 500000,
        ]);

        $results = $this->resolver->resolveKpiBonus(
            $this->employeeId,
            $this->positionId,
            $this->roleId,
            kpiScore: 85.00,
            baseSalary: $this->baseSalary,
        );

        $this->assertCount(1, $results);
        $this->assertEquals(500000, $results[0]['calculated_amount']);
        $this->assertEquals($this->baseSalary, $results[0]['base_amount']); // base_salary basis
    }

    // ============================================================
    // Special Bonus Resolve (BR-09 to BR-14)
    // ============================================================

    // BR-09 — Resolve special bonus — condition_mode=all, semua terpenuhi
    public function test_br09_resolve_special_bonus_all_mode_semua_terpenuhi(): void
    {
        $rule = SpecialBonusRule::factory()->create([
            'rule_code' => 'SPC-ALL-PASS',
            'scope_type' => 'global',
            'reward_type' => 'fixed',
            'reward_value' => 500000,
            'reward_basis' => 'base_salary',
            'condition_mode' => 'all',
            'is_active' => true,
        ]);

        // Condition 1: attendance_days_present >= 22
        SpecialBonusRuleCondition::create([
            'special_bonus_rule_id' => $rule->id,
            'metric_code' => 'attendance_days_present',
            'period_type' => 'payroll_period',
            'operator' => '>=',
            'target_value' => 22,
            'data_source' => 'attendance_days_present',
            'is_active' => true,
        ]);

        // Seed attendance recap
        EmployeeAttendanceMonthlyRecap::create([
            'employee_id' => $this->employeeId,
            'period_year' => 2026,
            'period_month' => 9,
            'total_scheduled_working_days' => 26,
            'total_effective_working_days' => 26,
            'total_present' => 23,
        ]);

        $results = $this->resolver->resolveSpecialBonus(
            $this->employeeId,
            $this->positionId,
            $this->roleId,
            baseSalary: $this->baseSalary,
            periodYear: 2026,
            periodMonth: 9,
        );

        $this->assertCount(1, $results);
        $this->assertEquals('special', $results[0]['type']);
        $this->assertEquals(500_000, $results[0]['calculated_amount']); // fixed 500K
        $this->assertTrue($results[0]['conditions'][0]['passed']);
    }

    // BR-10 — Resolve special bonus — condition_mode=all, satu gagal
    public function test_br10_resolve_special_bonus_all_mode_satu_gagal(): void
    {
        $rule = SpecialBonusRule::factory()->create([
            'rule_code' => 'SPC-ALL-FAIL',
            'scope_type' => 'global',
            'reward_type' => 'fixed',
            'reward_value' => 500000,
            'reward_basis' => 'base_salary',
            'condition_mode' => 'all',
            'is_active' => true,
        ]);

        // Condition 1: attendance >= 22 (passes with recap data)
        SpecialBonusRuleCondition::create([
            'special_bonus_rule_id' => $rule->id,
            'metric_code' => 'attendance_min',
            'period_type' => 'payroll_period',
            'operator' => '>=',
            'target_value' => 22,
            'data_source' => 'attendance_days_present',
            'is_active' => true,
        ]);

        // Condition 2: unknown data_source (fail-safe → null → not passed)
        SpecialBonusRuleCondition::create([
            'special_bonus_rule_id' => $rule->id,
            'metric_code' => 'unknown_metric',
            'period_type' => 'payroll_period',
            'operator' => '>=',
            'target_value' => 100,
            'data_source' => 'nonexistent_data_source',
            'is_active' => true,
        ]);

        // Seed attendance recap so condition 1 would pass
        EmployeeAttendanceMonthlyRecap::create([
            'employee_id' => $this->employeeId,
            'period_year' => 2026,
            'period_month' => 9,
            'total_scheduled_working_days' => 26,
            'total_effective_working_days' => 26,
            'total_present' => 23,
        ]);

        $results = $this->resolver->resolveSpecialBonus(
            $this->employeeId,
            $this->positionId,
            $this->roleId,
            baseSalary: $this->baseSalary,
            periodYear: 2026,
            periodMonth: 9,
        );

        // condition_mode=all: one condition unknown → not eligible
        $this->assertEmpty($results, 'condition_mode=all requires all conditions to pass — unknown data_source fails');
    }

    // BR-11 — Resolve special bonus — condition_mode=any, satu terpenuhi
    public function test_br11_resolve_special_bonus_any_mode_satu_terpenuhi(): void
    {
        $rule = SpecialBonusRule::factory()->create([
            'rule_code' => 'SPC-ANY-PASS',
            'scope_type' => 'global',
            'reward_type' => 'fixed',
            'reward_value' => 300000,
            'reward_basis' => 'fixed',
            'condition_mode' => 'any',
            'is_active' => true,
        ]);

        // Condition 1: unknown (fails — returns null)
        SpecialBonusRuleCondition::create([
            'special_bonus_rule_id' => $rule->id,
            'metric_code' => 'unknown',
            'period_type' => 'payroll_period',
            'operator' => '>=',
            'target_value' => 100,
            'data_source' => 'nonexistent_source',
            'is_active' => true,
        ]);

        // Condition 2: attendance present >= 0 (passes if recap exists)
        SpecialBonusRuleCondition::create([
            'special_bonus_rule_id' => $rule->id,
            'metric_code' => 'attendance_present',
            'period_type' => 'payroll_period',
            'operator' => '>=',
            'target_value' => 0,
            'data_source' => 'attendance_days_present',
            'is_active' => true,
        ]);

        // Seed attendance recap so condition 2 passes
        EmployeeAttendanceMonthlyRecap::create([
            'employee_id' => $this->employeeId,
            'period_year' => 2026,
            'period_month' => 9,
            'total_scheduled_working_days' => 26,
            'total_effective_working_days' => 26,
            'total_present' => 23,
        ]);

        $results = $this->resolver->resolveSpecialBonus(
            $this->employeeId,
            $this->positionId,
            $this->roleId,
            baseSalary: $this->baseSalary,
            periodYear: 2026,
            periodMonth: 9,
        );

        // condition_mode=any: one condition passes → eligible
        $this->assertCount(1, $results);
        $this->assertEquals('special', $results[0]['type']);
        $this->assertEquals(300_000, $results[0]['calculated_amount']); // fixed 300K
    }

    // BR-12 — Resolve special bonus — data_source tidak dikenal (fail-safe)
    public function test_br12_resolve_special_bonus_unknown_data_source(): void
    {
        // Verify that unknown data_source returns null (fail-safe behavior)
        $dataSource = app(\App\Services\Bonus\BonusDataSourceService::class);
        $result = $dataSource->resolveMetric('unknown_metric_xyz', $this->employeeId, 2026, 9);

        $this->assertNull($result, 'Unknown data_source should return null (fail-safe)');
    }

    // BR-13 — Resolve special bonus — 0 kondisi + condition_mode=all (prevents vacuous truth)
    public function test_br13_resolve_special_bonus_zero_conditions_all_mode(): void
    {
        SpecialBonusRule::factory()->create([
            'rule_code' => 'SPC-NO-COND',
            'scope_type' => 'global',
            'reward_type' => 'fixed',
            'reward_value' => 1000000,
            'reward_basis' => 'fixed',
            'condition_mode' => 'all',
            'is_active' => true,
        ]);

        // No conditions created for this rule
        $results = $this->resolver->resolveSpecialBonus(
            $this->employeeId,
            $this->positionId,
            $this->roleId,
            baseSalary: $this->baseSalary,
            periodYear: 2026,
            periodMonth: 9,
        );

        // The resolver checks: if ($rule->conditions->isEmpty()) { continue; }
        // This prevents vacuous truth — rule with 0 conditions is NOT eligible
        $this->assertEmpty($results, 'Rule with 0 conditions and condition_mode=all should NOT be eligible (prevents vacuous truth)');
    }

    // BR-14 — Resolve special bonus — conditions snapshot di conditionResults
    public function test_br14_resolve_special_bonus_conditions_snapshot(): void
    {
        $rule = SpecialBonusRule::factory()->create([
            'rule_code' => 'SPC-SNAPSHOT',
            'scope_type' => 'global',
            'reward_type' => 'fixed',
            'reward_value' => 200000,
            'reward_basis' => 'fixed',
            'condition_mode' => 'all',
            'is_active' => true,
        ]);

        SpecialBonusRuleCondition::create([
            'special_bonus_rule_id' => $rule->id,
            'metric_code' => 'attendance_present',
            'period_type' => 'payroll_period',
            'operator' => '>=',
            'target_value' => 0, // always passes if data exists
            'data_source' => 'attendance_days_present',
            'is_active' => true,
        ]);

        // Seed attendance recap
        EmployeeAttendanceMonthlyRecap::create([
            'employee_id' => $this->employeeId,
            'period_year' => 2026,
            'period_month' => 9,
            'total_scheduled_working_days' => 26,
            'total_effective_working_days' => 26,
            'total_present' => 23,
        ]);

        $results = $this->resolver->resolveSpecialBonus(
            $this->employeeId,
            $this->positionId,
            $this->roleId,
            baseSalary: $this->baseSalary,
            periodYear: 2026,
            periodMonth: 9,
        );

        $this->assertCount(1, $results);
        $this->assertArrayHasKey('conditions', $results[0], 'Result should include conditions snapshot');
        $this->assertArrayHasKey('actual_value', $results[0]['conditions'][0]);
        $this->assertArrayHasKey('passed', $results[0]['conditions'][0]);
        $this->assertTrue($results[0]['conditions'][0]['passed']);
        $this->assertEquals(23.0, $results[0]['conditions'][0]['actual_value']); // total_present = 23
    }

    // ============================================================
    // Helper
    // ============================================================

    private array $warnings = [];

    private function addWarning(string $gap, string $scenario, string $message): void
    {
        $this->warnings[] = compact('gap', 'scenario', 'message');
    }

    /** Direct DB insert for employee — bypasses factory FK deadlock chain */
    private function insertEmployee(string $prefix = 'EMP', bool $hq = false): int
    {
        if ($hq) {
            return DB::table('employees')->insertGetId([
                'employee_code' => 'EC-'.substr(md5(uniqid($prefix)), 0, 8),
                'full_name' => "Employee {$prefix}",
                'gender' => 'L',
                'birthdate' => '1990-01-01',
                'email' => strtolower($prefix).'@test.example',
                'whatsapp_number' => '62'.fake()->numerify('###########'),
                'is_hq' => true,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

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
            'email' => strtolower($prefix).'@test.example',
            'whatsapp_number' => '62'.fake()->numerify('###########'),
            'branch_id' => $branchId,
            'area_id' => $areaId,
            'region_id' => $regionId,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    protected function tearDown(): void
    {
        foreach ($this->warnings as $w) {
            echo "\n  ⚠️  {$w['gap']} [{$w['scenario']}]: {$w['message']}";
        }
        parent::tearDown();
    }
}
