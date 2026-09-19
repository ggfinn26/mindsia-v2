<?php

namespace Tests\Feature\Kpi;

use App\Models\KpiEvaluatorAssignment;
use App\Models\KpiGradeRule;
use App\Models\KpiTemplate;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

// Flow: kpi-grade-and-assignment (KG-01 to KG-09)
// SKENARIO-TESTING.md: Grade rule CRUD, evaluator assignment, validation
class KpiGradeAndAssignmentTest extends TestCase
{
    private User $boardUser;

    private User $regularUser;

    private int $evaluatorId;

    private int $evaluateeId;

    protected function setUp(): void
    {
        parent::setUp();

        $uid = uniqid();
        $this->evaluatorId = DB::table('employees')->insertGetId([
            'employee_code' => "EMP-EVR-{$uid}",
            'full_name' => 'Evaluator',
            'gender' => 'L',
            'birthdate' => '1985-01-01',
            'email' => "evr.{$uid}@test.com",
            'whatsapp_number' => '628100001111',
            'is_hq' => true,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $uid2 = uniqid();
        $this->evaluateeId = DB::table('employees')->insertGetId([
            'employee_code' => "EMP-EVE-{$uid2}",
            'full_name' => 'Evaluatee',
            'gender' => 'P',
            'birthdate' => '1990-01-01',
            'email' => "eve.{$uid2}@test.com",
            'whatsapp_number' => '628100002222',
            'is_hq' => true,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->boardUser = User::factory()->create(['employee_id' => $this->evaluatorId]);
        $this->boardUser->assignRole('CEO');

        $this->regularUser = User::factory()->create();
        $this->regularUser->assignRole('HRR');
    }

    // KG-01: buat grade rule berhasil
    public function test_can_create_grade_rule(): void
    {
        $this->actingAs($this->boardUser)
            ->post('/kpi/grade-rules', [
                'grade' => 'A',
                'minimum_score' => 90,
                'maximum_score' => 100,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('kpi_grade_rules', [
            'grade' => 'A',
            'minimum_score' => 90,
        ]);
    }

    // KG-02: minimum_score duplikat → 422
    public function test_cannot_create_grade_rule_with_duplicate_minimum_score(): void
    {
        KpiGradeRule::create(['grade' => 'A', 'minimum_score' => 90]);

        $this->actingAs($this->boardUser)
            ->post('/kpi/grade-rules', [
                'grade' => 'B',
                'minimum_score' => 90,
            ])
            ->assertSessionHasErrors('minimum_score');
    }

    // KG-03: hapus grade rule — kpi.grade_rule.delete middleware blocks regularUser
    // GAP-175 skenario said "tidak ada authorize" — FALSE, middleware 'can:kpi.grade_rule.delete' exists
    public function test_user_without_delete_permission_cannot_delete_grade_rule(): void
    {
        $rule = KpiGradeRule::create(['grade' => 'C', 'minimum_score' => 60]);

        $this->actingAs($this->regularUser)
            ->delete("/kpi/grade-rules/{$rule->id}")
            ->assertStatus(403);

        $this->assertDatabaseHas('kpi_grade_rules', ['id' => $rule->id]);
    }

    // KG-04: index protected by kpi.grade_rule.view middleware
    // GAP-177 skenario said "tidak ada permission check" — FALSE, middleware exists
    public function test_user_without_view_permission_cannot_access_grade_rules(): void
    {
        $this->actingAs($this->regularUser)
            ->get('/kpi/grade-rules')
            ->assertStatus(403);
    }

    // KG-05: gradeFor() lookup
    public function test_grade_lookup_returns_correct_grade(): void
    {
        KpiGradeRule::create(['grade' => 'A', 'minimum_score' => 85, 'maximum_score' => 100]);
        KpiGradeRule::create(['grade' => 'B', 'minimum_score' => 70, 'maximum_score' => 84]);
        KpiGradeRule::create(['grade' => 'C', 'minimum_score' => 55, 'maximum_score' => 69]);

        $this->assertEquals('A', KpiGradeRule::gradeFor(90));
        $this->assertEquals('B', KpiGradeRule::gradeFor(75));
        $this->assertEquals('C', KpiGradeRule::gradeFor(60));
        $this->assertNull(KpiGradeRule::gradeFor(40));
    }

    // KG-06: buat assignment evaluator berhasil
    public function test_can_create_evaluator_assignment(): void
    {
        $template = KpiTemplate::create([
            'template_code' => 'TPL-ASSIGN-001',
            'template_name' => 'Assignment Template',
            'is_active' => true,
            'created_by_employee_id' => $this->evaluatorId,
        ]);

        $this->actingAs($this->boardUser)
            ->post('/kpi/evaluator-assignments', [
                'evaluator_employee_id' => $this->evaluatorId,
                'evaluatee_employee_id' => $this->evaluateeId,
                'kpi_template_id' => $template->id,
                'effective_start_date' => now()->toDateString(),
                'is_active' => 1,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('kpi_evaluator_assignments', [
            'evaluator_employee_id' => $this->evaluatorId,
            'evaluatee_employee_id' => $this->evaluateeId,
        ]);
    }

    // KG-07: evaluator sama dengan evaluatee → 422
    public function test_cannot_assign_evaluator_as_own_evaluatee(): void
    {
        $this->actingAs($this->boardUser)
            ->post('/kpi/evaluator-assignments', [
                'evaluator_employee_id' => $this->evaluatorId,
                'evaluatee_employee_id' => $this->evaluatorId,
                'effective_start_date' => now()->toDateString(),
                'is_active' => 1,
            ])
            ->assertSessionHasErrors('evaluator_employee_id');
    }

    // KG-08: duplicate active assignment → 422
    // GAP-182 skenario said "duplikat tersimpan" — FALSE, withValidator() checks existing active assignment
    public function test_cannot_create_duplicate_active_assignment(): void
    {
        $template = KpiTemplate::create([
            'template_code' => 'TPL-DUP-001',
            'template_name' => 'Dup Template',
            'is_active' => true,
            'created_by_employee_id' => $this->evaluatorId,
        ]);

        KpiEvaluatorAssignment::create([
            'evaluator_employee_id' => $this->evaluatorId,
            'evaluatee_employee_id' => $this->evaluateeId,
            'kpi_template_id' => $template->id,
            'is_active' => true,
            'effective_start_date' => now()->subMonth()->toDateString(),
            'created_by_employee_id' => $this->evaluatorId,
        ]);

        $this->actingAs($this->boardUser)
            ->post('/kpi/evaluator-assignments', [
                'evaluator_employee_id' => $this->evaluatorId,
                'evaluatee_employee_id' => $this->evaluateeId,
                'kpi_template_id' => $template->id,
                'effective_start_date' => now()->toDateString(),
                'is_active' => 1,
            ])
            ->assertSessionHasErrors('evaluatee_employee_id');
    }
}
