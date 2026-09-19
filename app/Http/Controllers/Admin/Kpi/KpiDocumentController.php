<?php

namespace App\Http\Controllers\Admin\Kpi;

use App\Http\Controllers\Controller;
use App\Http\Requests\Kpi\StoreKpiDocumentRequest;
use App\Models\KpiDocument;
use App\Services\TelegramStorageService;
use Illuminate\Http\RedirectResponse;

class KpiDocumentController extends Controller
{
    public function __construct(
        private readonly TelegramStorageService $telegramStorage,
    ) {}

    public function store(StoreKpiDocumentRequest $request): RedirectResponse
    {
        $file = $request->file('file');
        $uploaded = $this->telegramStorage->uploadFile(
            $file->getRealPath(),
            $file->getClientOriginalName(),
            'kpi_document',
            null,
        );

        KpiDocument::create([
            'kpi_template_id' => $request->validated('kpi_template_id'),
            'employee_kpi_evaluation_id' => $request->validated('employee_kpi_evaluation_id'),
            'document_type' => $request->validated('document_type'),
            'title' => $request->validated('title'),
            'description' => $request->validated('description'),
            'telegram_file_id' => $uploaded['file_id'],
            'storage_path' => $uploaded['file_id'],
            'original_name' => $file->getClientOriginalName(),
            'uploaded_by_employee_id' => auth()->user()->employee->id,
            'uploaded_at' => now(),
        ]);

        $redirectRoute = $request->validated('kpi_template_id')
            ? route('kpi.templates.show', $request->validated('kpi_template_id'))
            : route('kpi.evaluations.show', $request->validated('employee_kpi_evaluation_id'));

        return redirect($redirectRoute)->with('success', 'Dokumen berhasil diupload.');
    }

    public function destroy(KpiDocument $kpiDocument): RedirectResponse
    {
        $user = auth()->user();
        $isBoard = $user->can('kpi.document.delete');
        $isUploader = $user->employee?->id === $kpiDocument->uploaded_by_employee_id;

        $isEvaluatorOfDraftEval = false;
        if ($kpiDocument->employee_kpi_evaluation_id) {
            $eval = $kpiDocument->evaluation;
            $isEvaluatorOfDraftEval = $eval?->isDraft()
                && $user->employee?->id === $eval->evaluator_employee_id;
        }

        abort_unless($isBoard || $isUploader || $isEvaluatorOfDraftEval, 403);

        $redirectRoute = $kpiDocument->kpi_template_id
            ? route('kpi.templates.show', $kpiDocument->kpi_template_id)
            : route('kpi.evaluations.show', $kpiDocument->employee_kpi_evaluation_id);

        $kpiDocument->delete();

        return redirect($redirectRoute)->with('success', 'Dokumen berhasil dihapus.');
    }
}
