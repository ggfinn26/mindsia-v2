<?php

namespace App\Http\Controllers\Admin\Kpi;

use App\Http\Controllers\Controller;
use App\Http\Requests\Kpi\FinalizeKpiEvaluationRequest;
use App\Http\Requests\Kpi\StoreKpiEvaluationRequest;
use App\Models\EmployeeKpiEvaluation;
use App\Repositories\EmployeeRepository;
use App\Repositories\Kpi\KpiEvaluationRepository;
use App\Repositories\Kpi\KpiTemplateRepository;
use App\Services\Kpi\KpiEvaluationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KpiEvaluationController extends Controller
{
    public function __construct(
        private readonly KpiEvaluationRepository $repository,
        private readonly KpiEvaluationService $service,
        private readonly EmployeeRepository $employeeRepository,
        private readonly KpiTemplateRepository $templateRepository,
    ) {}

    public function index(Request $request): View
    {
        $filters = $request->only('employee_id', 'kpi_template_id', 'period_type', 'status');

        // Non-admin evaluators only see their own evaluations
        if (! $request->user()->can('kpi.evaluation.view_all')) {
            $filters['evaluator_employee_id'] = $request->user()->employee?->id;
        }

        return view('kpi.evaluations.index', [
            'evaluations' => $this->repository->paginate($filters),
            'templates' => $this->templateRepository->active(),
            'employees' => $this->employeeRepository->active(),
        ]);
    }

    public function create(): View
    {
        return view('kpi.evaluations.create', [
            'employees' => $this->employeeRepository->active(),
            'templates' => $this->templateRepository->active(),
        ]);
    }

    public function store(StoreKpiEvaluationRequest $request): RedirectResponse
    {
        $evaluator = auth()->user()->employee;
        $evaluation = $this->service->create($request->validated(), $evaluator);

        return redirect()->route('kpi.evaluations.show', $evaluation)->with('success', 'Evaluasi KPI berhasil dibuat.');
    }

    public function show(EmployeeKpiEvaluation $kpiEvaluation): View
    {
        return view('kpi.evaluations.show', [
            'evaluation' => $this->repository->find($kpiEvaluation->id),
        ]);
    }

    public function finalize(FinalizeKpiEvaluationRequest $request, EmployeeKpiEvaluation $kpiEvaluation): RedirectResponse
    {
        $this->service->finalize($kpiEvaluation, $request->validated('evaluator_notes'));

        return redirect()->route('kpi.evaluations.show', $kpiEvaluation)->with('success', 'Evaluasi berhasil di-finalize.');
    }
}
