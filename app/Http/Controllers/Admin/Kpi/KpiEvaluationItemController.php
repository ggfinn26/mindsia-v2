<?php

namespace App\Http\Controllers\Admin\Kpi;

use App\Http\Controllers\Controller;
use App\Http\Requests\Kpi\UpdateKpiEvaluationItemRequest;
use App\Models\EmployeeKpiEvaluation;
use App\Models\EmployeeKpiEvaluationItem;
use App\Services\Kpi\KpiEvaluationService;
use Illuminate\Http\RedirectResponse;

class KpiEvaluationItemController extends Controller
{
    public function __construct(
        private readonly KpiEvaluationService $service,
    ) {}

    public function update(UpdateKpiEvaluationItemRequest $request, EmployeeKpiEvaluation $kpiEvaluation, EmployeeKpiEvaluationItem $item): RedirectResponse
    {
        $this->service->updateItem(
            $item,
            (float) $request->validated('actual_value'),
            $request->validated('notes'),
        );

        return redirect()->route('kpi.evaluations.show', $kpiEvaluation)->with('success', 'Item evaluasi berhasil diperbarui.');
    }
}
