<?php

namespace Tests\Feature\Kpi;

use App\Models\EmployeeKpiEvaluation;
use App\Models\EmployeeKpiEvaluationItem;
use App\Models\KpiEvaluatorAssignment;
use App\Models\KpiGradeRule;
use App\Models\KpiTemplate;
use App\Models\KpiTemplateIndicator;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

// Flow: create-kpi-evaluation + finalize-kpi (KE-01 to KE-09, FK-01 to FK-05)
// SKENARIO-TESTING.md: Create evaluation, update items, finalize with grade lookup
class KpiEvaluationTest extends TestCase
{
    private User $evaluatorUser;

    private User $otherUser;

    private int $evaluatorId;

    private int $evaluateeId;

    private KpiTemplate $template;

    protected function setUp(): void
    {
        parent::setUp();

        $uid = uniqid();
        $this->evaluatorId = DB::table('employees')->insertGetId([
            'employee_code' => "EMP-KE-EVR-{$uid}",
            'full_name' => 'KPI Evaluator',
            'gender' => 'L',
            'birthdate' => '1985-01-01',
            'email' => "ke.evr.{$uid}@test.com",
            'whatsapp_number' => '628100003333',
            'is_hq' => true,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $uid2 = uniqid();
        $this->evaluateeId = DB::table('employees')->insertGetId([
            'employee_code' => "EMP-KE-EVE-{$uid2}",
            'full_name' => 'KPI Evaluatee',
            'gender' => 'P',
            'birthdate' => '1990-01-01',
            'email' => "ke.eve.{$uid2}@test.com",
            'whatsapp_number' => '628100004444',
            'is_hq' => true,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->evaluatorUser = User::factory()->create(['employee_id' => $this->evaluatorId]);
        $this->evaluatorUser->assignRole('CEO');

        $this->otherUser = User::factory()->create();
        $this->otherUser->assignRole('HRR');

        $this->template = KpiTemplate::create([
            'template_code' => 'TPL-EVAL-001',
            'template_name' => 'Eval Template',
            'is_active' => true,
            'created_by_employee_id' => $this->evaluatorId,
        ]);

        KpiEvaluatorAssignment::create([
            'evaluator_employee_id' => $this->evaluatorId,
            'evaluatee_employee_id' => $this->evaluateeId,
            'kpi_template_id' => $this->template->id,
            'is_active' => true,
            'effective_start_date' => now()->subMonth()->toDateString(),
            'created_by_employee_id' => $this->evaluatorId,
        ]);
    }

    private function createEvaluation(array $overrides = []): EmployeeKpiEvaluation
    {
        return EmployeeKpiEvaluation::create(array_merge([
            'employee_id' => $this->evaluateeId,
            'evaluator_employee_id' => $this->evaluatorId,
            'kpi_template_id' => $this->template->id,
            'period_type' => 'monthly',
            'period_start_date' => now()->startOfMonth(),
            'period_end_date' => now()->endOfMonth(),
            'status' => 'draft',
            'total_score' => 0,
            'employee_name_snapshot' => 'KPI Evaluatee',
        ], $overrides));
    }

    // KE-01: assigned evaluator buat evaluasi berhasil
    public function test_assigned_evaluator_can_create_evaluation(): void
    {
        $this->actingAs($this->evaluatorUser)
            ->post('/kpi/evaluations', [
                'employee_id' => $this->evaluateeId,
                'kpi_template_id' => $this->template->id,
                'period_type' => 'monthly',
                'period_start_date' => now()->startOfMonth()->toDateString(),
                'period_end_date' => now()->endOfMonth()->toDateString(),
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('employee_kpi_evaluations', [
            'employee_id' => $this->evaluateeId,
            'evaluator_employee_id' => $this->evaluatorId,
            'status' => 'draft',
        ]);
    }

    // KE-04: non-assigned evaluator → 403
    public function test_non_assigned_evaluator_cannot_create_evaluation(): void
    {
        $this->actingAs($this->otherUser)
            ->post('/kpi/evaluations', [
                'employee_id' => $this->evaluateeId,
                'kpi_template_id' => $this->template->id,
                'period_type' => 'monthly',
                'period_start_date' => now()->startOfMonth()->toDateString(),
                'period_end_date' => now()->endOfMonth()->toDateString(),
            ])
            ->assertStatus(403);
    }

    // KE-05: evaluasi duplikat → 422
    public function test_cannot_create_duplicate_evaluation(): void
    {
        $this->createEvaluation();

        $this->actingAs($this->evaluatorUser)
            ->post('/kpi/evaluations', [
                'employee_id' => $this->evaluateeId,
                'kpi_template_id' => $this->template->id,
                'period_type' => 'monthly',
                'period_start_date' => now()->startOfMonth()->toDateString(),
                'period_end_date' => now()->endOfMonth()->toDateString(),
            ])
            ->assertSessionHasErrors('employee_id');
    }

    // KE-09: update item pada evaluasi finalized → 403
    public function test_cannot_update_item_on_finalized_evaluation(): void
    {
        $eval = $this->createEvaluation(['status' => 'finalized']);
        $indicator = KpiTemplateIndicator::create([
            'kpi_template_id' => $this->template->id,
            'indicator_code' => 'IND-FIN',
            'indicator_name' => 'Finalized Item',
            'weight' => 50,
            'sequence_number' => 1,
            'is_active' => true,
        ]);
        $item = EmployeeKpiEvaluationItem::create([
            'employee_kpi_evaluation_id' => $eval->id,
            'kpi_template_indicator_id' => $indicator->id,
            'indicator_code_snapshot' => 'IND-FIN',
            'indicator_name_snapshot' => 'Finalized Item',
            'weight_snapshot' => 50,
            'target_value' => 100,
            'actual_value' => null,
            'achievement_percentage' => 0,
            'score' => 0,
        ]);

        $this->actingAs($this->evaluatorUser)
            ->put("/kpi/evaluations/{$eval->id}/items/{$item->id}", [
                'actual_value' => 80,
            ])
            ->assertStatus(403);
    }

    // FK-01: finalize evaluasi berhasil
    public function test_evaluator_can_finalize_evaluation(): void
    {
        $eval = $this->createEvaluation(['status' => 'draft']);
        KpiGradeRule::create(['grade' => 'A', 'minimum_score' => 85, 'maximum_score' => 100]);

        $this->actingAs($this->evaluatorUser)
            ->post("/kpi/evaluations/{$eval->id}/finalize", [])
            ->assertRedirect();

        $this->assertDatabaseHas('employee_kpi_evaluations', [
            'id' => $eval->id,
            'status' => 'finalized',
        ]);
    }

    // FK-02: total_score = sum item scores
    public function test_finalize_sums_item_scores_correctly(): void
    {
        $eval = $this->createEvaluation(['status' => 'draft']);

        $ind1 = KpiTemplateIndicator::create([
            'kpi_template_id' => $this->template->id,
            'indicator_code' => 'IND-S1',
            'indicator_name' => 'Score 1',
            'weight' => 50,
            'sequence_number' => 1,
            'is_active' => true,
        ]);
        $ind2 = KpiTemplateIndicator::create([
            'kpi_template_id' => $this->template->id,
            'indicator_code' => 'IND-S2',
            'indicator_name' => 'Score 2',
            'weight' => 50,
            'sequence_number' => 2,
            'is_active' => true,
        ]);

        foreach ([[$ind1->id, 'IND-S1', 30], [$ind2->id, 'IND-S2', 45]] as [$indId, $code, $score]) {
            EmployeeKpiEvaluationItem::create([
                'employee_kpi_evaluation_id' => $eval->id,
                'kpi_template_indicator_id' => $indId,
                'indicator_code_snapshot' => $code,
                'indicator_name_snapshot' => $code,
                'weight_snapshot' => 50,
                'target_value' => 100,
                'score' => $score,
                'achievement_percentage' => $score * 2,
            ]);
        }

        KpiGradeRule::create(['grade' => 'C', 'minimum_score' => 50, 'maximum_score' => 80]);

        $this->actingAs($this->evaluatorUser)
            ->post("/kpi/evaluations/{$eval->id}/finalize", []);

        $updated = EmployeeKpiEvaluation::find($eval->id);
        $this->assertEquals(75.0, (float) $updated->total_score);
        $this->assertEquals('C', $updated->grade);
    }

    // FK-04: finalize evaluasi yang sudah finalized → 403
    public function test_cannot_finalize_already_finalized_evaluation(): void
    {
        $eval = $this->createEvaluation(['status' => 'finalized']);

        $this->actingAs($this->evaluatorUser)
            ->post("/kpi/evaluations/{$eval->id}/finalize", [])
            ->assertStatus(403);
    }

    // FK-05: non-evaluator tidak bisa finalize → 403
    public function test_non_evaluator_cannot_finalize(): void
    {
        $eval = $this->createEvaluation(['status' => 'draft']);

        $this->actingAs($this->otherUser)
            ->post("/kpi/evaluations/{$eval->id}/finalize", [])
            ->assertStatus(403);
    }
}
