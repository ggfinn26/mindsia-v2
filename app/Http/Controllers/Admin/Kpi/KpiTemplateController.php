<?php

namespace App\Http\Controllers\Admin\Kpi;

use App\Http\Controllers\Controller;
use App\Http\Requests\Kpi\StoreKpiTemplateRequest;
use App\Http\Requests\Kpi\UpdateKpiTemplateRequest;
use App\Models\KpiTemplate;
use App\Repositories\Kpi\KpiTemplateRepository;
use App\Repositories\PositionRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KpiTemplateController extends Controller
{
    public function __construct(
        private readonly KpiTemplateRepository $repository,
        private readonly PositionRepository $positionRepository,
    ) {}

    public function index(Request $request): View
    {
        return view('kpi.templates.index', [
            'templates' => $this->repository->paginate($request->only('position_id', 'is_active', 'search')),
            'positions' => $this->positionRepository->all(),
        ]);
    }

    public function create(): View
    {
        return view('kpi.templates.create', [
            'positions' => $this->positionRepository->all(),
        ]);
    }

    public function store(StoreKpiTemplateRequest $request): RedirectResponse
    {
        $template = $this->repository->create([
            ...$request->validated(),
            'created_by_employee_id' => auth()->user()->employee->id,
        ]);

        return redirect()->route('kpi.templates.show', $template)->with('success', 'Template KPI berhasil dibuat.');
    }

    public function show(KpiTemplate $kpiTemplate): View
    {
        return view('kpi.templates.show', [
            'template' => $this->repository->find($kpiTemplate->id),
        ]);
    }

    public function edit(KpiTemplate $kpiTemplate): View
    {
        return view('kpi.templates.edit', [
            'template' => $kpiTemplate,
            'positions' => $this->positionRepository->all(),
        ]);
    }

    public function update(UpdateKpiTemplateRequest $request, KpiTemplate $kpiTemplate): RedirectResponse
    {
        $this->repository->update($kpiTemplate, $request->validated());

        return redirect()->route('kpi.templates.show', $kpiTemplate)->with('success', 'Template KPI berhasil diperbarui.');
    }
}
