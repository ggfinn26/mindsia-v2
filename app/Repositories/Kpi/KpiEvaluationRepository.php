<?php

namespace App\Repositories\Kpi;

use App\Models\Employee;
use App\Models\EmployeeKpiEvaluation;
use App\Models\EmployeeKpiEvaluationItem;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class KpiEvaluationRepository
{
    public function paginate(array $filters = []): LengthAwarePaginator
    {
        return EmployeeKpiEvaluation::with('employee', 'template', 'evaluator')
            ->when($filters['employee_id'] ?? null, fn ($q, $v) => $q->where('employee_id', $v))
            ->when($filters['kpi_template_id'] ?? null, fn ($q, $v) => $q->where('kpi_template_id', $v))
            ->when($filters['period_type'] ?? null, fn ($q, $v) => $q->where('period_type', $v))
            ->when($filters['status'] ?? null, fn ($q, $v) => $q->where('status', $v))
            ->when($filters['evaluator_employee_id'] ?? null, fn ($q, $v) => $q->where('evaluator_employee_id', $v))
            ->orderByDesc('period_start_date')
            ->paginate(25);
    }

    public function find(int $id): EmployeeKpiEvaluation
    {
        return EmployeeKpiEvaluation::with(
            'employee',
            'template.indicators',
            'evaluator',
            'items.indicator',
            'documents',
        )->findOrFail($id);
    }

    public function create(array $data): EmployeeKpiEvaluation
    {
        return EmployeeKpiEvaluation::create($data);
    }

    public function createItem(EmployeeKpiEvaluation $evaluation, array $data): EmployeeKpiEvaluationItem
    {
        return $evaluation->items()->create($data);
    }

    public function updateItem(EmployeeKpiEvaluationItem $item, array $data): EmployeeKpiEvaluationItem
    {
        $item->update($data);

        return $item;
    }

    public function finalize(EmployeeKpiEvaluation $evaluation, float $totalScore, ?string $grade): EmployeeKpiEvaluation
    {
        $evaluation->update([
            'total_score' => $totalScore,
            'grade' => $grade,
            'status' => 'finalized',
            'finalized_at' => now(),
        ]);

        return $evaluation;
    }

    public function forEvaluator(Employee $evaluator, array $filters = []): LengthAwarePaginator
    {
        return $this->paginate(array_merge($filters, ['evaluator_employee_id' => $evaluator->id]));
    }
}
