<?php

namespace App\Http\Controllers\Admin\Kpi;

use App\Http\Controllers\Controller;
use App\Http\Requests\Kpi\StoreKpiEvaluatorAssignmentRequest;
use App\Http\Requests\Kpi\UpdateKpiEvaluatorAssignmentRequest;
use App\Models\KpiEvaluatorAssignment;
use App\Repositories\EmployeeRepository;
use App\Repositories\Kpi\KpiTemplateRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KpiEvaluatorAssignmentController extends Controller
{
    public function __construct(
        private readonly EmployeeRepository $employeeRepository,
        private readonly KpiTemplateRepository $templateRepository,
    ) {}

    public function index(Request $request): View
    {
        $assignments = KpiEvaluatorAssignment::with('evaluator', 'evaluatee', 'template')
            ->when($request->evaluator_employee_id, fn ($q, $v) => $q->where('evaluator_employee_id', $v))
            ->when($request->evaluatee_employee_id, fn ($q, $v) => $q->where('evaluatee_employee_id', $v))
            ->when($request->has('is_active'), fn ($q) => $q->where('is_active', $request->boolean('is_active')))
            ->orderByDesc('effective_start_date')
            ->paginate(25);

        return view('kpi.evaluator-assignments.index', [
            'assignments' => $assignments,
            'employees' => $this->employeeRepository->active(),
        ]);
    }

    public function create(): View
    {
        return view('kpi.evaluator-assignments.create', [
            'employees' => $this->employeeRepository->active(),
            'templates' => $this->templateRepository->active(),
        ]);
    }

    public function store(StoreKpiEvaluatorAssignmentRequest $request): RedirectResponse
    {
        KpiEvaluatorAssignment::create([
            ...$request->validated(),
            'created_by_employee_id' => auth()->user()->employee->id,
        ]);

        return redirect()->route('kpi.evaluator-assignments.index')->with('success', 'Assignment evaluator berhasil disimpan.');
    }

    public function edit(KpiEvaluatorAssignment $kpiEvaluatorAssignment): View
    {
        return view('kpi.evaluator-assignments.edit', [
            'assignment' => $kpiEvaluatorAssignment,
            'employees' => $this->employeeRepository->active(),
            'templates' => $this->templateRepository->active(),
        ]);
    }

    public function update(UpdateKpiEvaluatorAssignmentRequest $request, KpiEvaluatorAssignment $kpiEvaluatorAssignment): RedirectResponse
    {
        $kpiEvaluatorAssignment->update($request->validated());

        return redirect()->route('kpi.evaluator-assignments.index')->with('success', 'Assignment evaluator berhasil diperbarui.');
    }
}
