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
use Illuminate\Validation\ValidationException;

class KpiEvaluationService
{
    public function __construct(
        private readonly KpiEvaluationRepository $repository,
        private readonly KpiDataSourceService $dataSource,
    ) {}

    /**
     * Pencapaian dibatasi 100% agar total skor (bobot berjumlah 100) tidak pernah melebihi 100.
     * Target kosong atau 0 menghasilkan 0; template tidak boleh dievaluasi tanpa target (lihat assertTemplateReady).
     *
     * @return array{achievement: float, score: float}
     */
    public function calculate(?float $target, float $actual, float $weight): array
    {
        $achievement = $target !== null && $target > 0
            ? min(round($actual / $target * 100, 2), 100.0)
            : 0.0;

        return ['achievement' => $achievement, 'score' => round($achievement * $weight / 100, 2)];
    }

    /**
     * Template harus siap dievaluasi: total bobot indikator aktif tepat 100 dan semua indikator punya target > 0.
     * Kalau tidak, skor akan diam-diam salah (total tidak 100 atau indikator selalu 0).
     */
    private function assertTemplateReady(KpiTemplate $template): void
    {
        $totalWeight = round((float) $template->activeIndicators->sum('weight'), 2);

        if ($totalWeight !== 100.0) {
            throw ValidationException::withMessages([
                'kpi_template_id' => "Total bobot indikator aktif template harus 100 (saat ini {$totalWeight}).",
            ]);
        }

        $noTarget = $template->activeIndicators->filter(fn ($i) => (float) $i->target_value <= 0);

        if ($noTarget->isNotEmpty()) {
            throw ValidationException::withMessages([
                'kpi_template_id' => 'Indikator berikut belum punya target (> 0): '.$noTarget->pluck('indicator_name')->implode(', ').'.',
            ]);
        }
    }

    /**
     * Create evaluation + seed items. Auto-pull actual_value for automated indicators
     * dan langsung hitung pencapaian + skornya.
     */
    public function create(array $data, Employee $evaluatorEmployee): EmployeeKpiEvaluation
    {
        return DB::transaction(function () use ($data, $evaluatorEmployee) {
            $employee = Employee::with('currentStatus.position.role')->findOrFail($data['employee_id']);
            $template = KpiTemplate::with('activeIndicators')->findOrFail($data['kpi_template_id']);
            $this->assertTemplateReady($template);

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

                $actual = $pulled['value'] ?? null;
                $result = $actual !== null
                    ? $this->calculate((float) $indicator->target_value, (float) $actual, (float) $indicator->weight)
                    : ['achievement' => 0.0, 'score' => 0.0];

                $this->repository->createItem($evaluation, [
                    'kpi_template_indicator_id' => $indicator->id,
                    'indicator_code_snapshot' => $indicator->indicator_code,
                    'indicator_name_snapshot' => $indicator->indicator_name,
                    'unit_snapshot' => $indicator->unit,
                    'weight_snapshot' => $indicator->weight,
                    'target_value' => $indicator->target_value,
                    'actual_value' => $actual,
                    'achievement_percentage' => $result['achievement'],
                    'score' => $result['score'],
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
        if ($item->source_type !== null) {
            throw ValidationException::withMessages([
                'actual_value' => 'Nilai ini ditarik otomatis dari sistem dan tidak dapat diubah manual.',
            ]);
        }

        $result = $this->calculate(
            $item->target_value !== null ? (float) $item->target_value : null,
            $actualValue,
            (float) $item->weight_snapshot,
        );

        $this->repository->updateItem($item, [
            'actual_value' => $actualValue,
            'achievement_percentage' => $result['achievement'],
            'score' => $result['score'],
            'notes' => $notes,
        ]);
    }

    /**
     * Finalize: sum items → total_score, lookup grade, lock evaluation.
     */
    public function finalize(EmployeeKpiEvaluation $evaluation, ?string $evaluatorNotes): EmployeeKpiEvaluation
    {
        $evaluation->load('items');

        if ($evaluation->items->contains(fn ($item) => $item->actual_value === null)) {
            throw ValidationException::withMessages([
                'evaluator_notes' => 'Masih ada indikator tanpa nilai aktual. Lengkapi semua nilai sebelum finalisasi.',
            ]);
        }

        $totalScore = $evaluation->items->sum('score');
        $grade = KpiGradeRule::gradeFor((float) $totalScore);

        $evaluation->update(['evaluator_notes' => $evaluatorNotes]);

        return $this->repository->finalize($evaluation, (float) $totalScore, $grade);
    }
}
