<?php

namespace App\Services\Kpi;

use App\Models\Employee;
use App\Models\EmployeeKpiEvaluation;
use App\Models\EmployeeKpiEvaluationItem;
use App\Models\KpiGradeRule;
use App\Models\KpiTemplate;
use App\Repositories\Kpi\KpiEvaluationRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class KpiEvaluationService
{
    public function __construct(
        private readonly KpiEvaluationRepository $repository,
        private readonly KpiDataSourceService $dataSource,
    ) {}

    /**
     * Create evaluation + seed items. Auto-pull actual_value for automated indicators.
     */
    public function create(array $data, Employee $evaluatorEmployee): EmployeeKpiEvaluation
    {
        return DB::transaction(function () use ($data, $evaluatorEmployee) {
            $employee = Employee::with('currentStatus.position.role')->findOrFail($data['employee_id']);
            $template = KpiTemplate::with('activeIndicators')->findOrFail($data['kpi_template_id']);

            $evaluation = $this->repository->create([
                ...$data,
                'evaluator_employee_id' => $evaluatorEmployee->id,
                'employee_name_snapshot' => $employee->full_name,
                'position_name_snapshot' => $employee->currentStatus?->position?->position_name,
                'role_name_snapshot' => $employee->currentStatus?->position?->role?->name,
                'status' => 'draft',
                'total_score' => 0,
            ]);

            $periodStart = Carbon::parse($data['period_start_date']);
            $periodEnd = Carbon::parse($data['period_end_date']);

            foreach ($template->activeIndicators as $indicator) {
                $pulled = null;

                if ($indicator->isAutomatic()) {
                    $pulled = $this->dataSource->resolve($indicator->data_source_type, $employee, $periodStart, $periodEnd);
                }

                $this->repository->createItem($evaluation, [
                    'kpi_template_indicator_id' => $indicator->id,
                    'indicator_code_snapshot' => $indicator->indicator_code,
                    'indicator_name_snapshot' => $indicator->indicator_name,
                    'unit_snapshot' => $indicator->unit,
                    'weight_snapshot' => $indicator->weight,
                    'target_value' => $indicator->target_value,
                    'actual_value' => $pulled['value'] ?? null,
                    'achievement_percentage' => 0,
                    'score' => 0,
                    'source_type' => $pulled['source_type'] ?? null,
                    'source_id' => $pulled['source_id'] ?? null,
                ]);
            }

            return $evaluation;
        }); // end DB::transaction
    }

    /**
     * Update one item's actual_value. Recalculates achievement_percentage and score.
     */
    public function updateItem(EmployeeKpiEvaluationItem $item, float $actualValue, ?string $notes): void
    {
        $achievementPct = $item->target_value > 0
            ? round($actualValue / $item->target_value * 100, 2)
            : 0;

        $score = round($achievementPct * $item->weight_snapshot / 100, 2);

        $this->repository->updateItem($item, [
            'actual_value' => $actualValue,
            'achievement_percentage' => $achievementPct,
            'score' => $score,
            'notes' => $notes,
        ]);
    }

    /**
     * Finalize: sum items → total_score, lookup grade, lock evaluation.
     */
    public function finalize(EmployeeKpiEvaluation $evaluation, ?string $evaluatorNotes): EmployeeKpiEvaluation
    {
        $evaluation->load('items');

        $totalScore = $evaluation->items->sum('score');
        $grade = KpiGradeRule::gradeFor((float) $totalScore);

        $evaluation->update(['evaluator_notes' => $evaluatorNotes]);

        return $this->repository->finalize($evaluation, (float) $totalScore, $grade);
    }
}
