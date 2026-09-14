<?php

namespace App\Http\Controllers\Admin\Kpi;

use App\Http\Controllers\Controller;
use App\Http\Requests\Kpi\StoreKpiTemplateIndicatorRequest;
use App\Http\Requests\Kpi\UpdateKpiTemplateIndicatorRequest;
use App\Models\KpiTemplate;
use App\Models\KpiTemplateIndicator;
use App\Repositories\Kpi\KpiTemplateRepository;
use App\Services\Kpi\KpiDataSourceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class KpiTemplateIndicatorController extends Controller
{
    public function __construct(
        private readonly KpiTemplateRepository $repository,
    ) {}

    public function create(KpiTemplate $kpiTemplate): View
    {
        return view('kpi.templates.indicators.create', [
            'template' => $kpiTemplate,
            'allowedSources' => KpiDataSourceService::ALLOWED_SOURCES,
        ]);
    }

    public function store(StoreKpiTemplateIndicatorRequest $request, KpiTemplate $kpiTemplate): RedirectResponse
    {
        $this->repository->createIndicator($kpiTemplate, $request->validated());

        return redirect()->route('kpi.templates.show', $kpiTemplate)->with('success', 'Indikator berhasil ditambahkan.');
    }

    public function edit(KpiTemplate $kpiTemplate, KpiTemplateIndicator $indicator): View
    {
        abort_unless($indicator->kpi_template_id === $kpiTemplate->id, 404);

        return view('kpi.templates.indicators.edit', [
            'template' => $kpiTemplate,
            'indicator' => $indicator,
            'allowedSources' => KpiDataSourceService::ALLOWED_SOURCES,
        ]);
    }

    public function update(UpdateKpiTemplateIndicatorRequest $request, KpiTemplate $kpiTemplate, KpiTemplateIndicator $indicator): RedirectResponse
    {
        abort_unless($indicator->kpi_template_id === $kpiTemplate->id, 404);

        $this->repository->updateIndicator($indicator, $request->validated());

        return redirect()->route('kpi.templates.show', $kpiTemplate)->with('success', 'Indikator berhasil diperbarui.');
    }

    public function destroy(KpiTemplate $kpiTemplate, KpiTemplateIndicator $indicator): RedirectResponse
    {
        abort_unless(request()->user()->can('kpi.template.update'), 403);
        abort_unless($indicator->kpi_template_id === $kpiTemplate->id, 404);

        $this->repository->deleteIndicator($indicator);

        return redirect()->route('kpi.templates.show', $kpiTemplate)->with('success', 'Indikator dinonaktifkan.');
    }
}
