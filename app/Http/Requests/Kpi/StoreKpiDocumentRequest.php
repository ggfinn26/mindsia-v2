<?php

namespace App\Http\Requests\Kpi;

use App\Models\EmployeeKpiEvaluation;
use Illuminate\Foundation\Http\FormRequest;

class StoreKpiDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        if (! $user->can('kpi.document.create')) {
            return false;
        }

        // Template-level upload: permission alone is sufficient (BOARD via Gate::before)
        if ($this->input('kpi_template_id')) {
            return true;
        }

        // Evaluation-level upload: must be evaluator of a draft evaluation
        $evaluationId = $this->input('employee_kpi_evaluation_id');
        if (! $evaluationId) {
            return false;
        }

        $evaluation = EmployeeKpiEvaluation::findOrFail($evaluationId);

        return $evaluation->isDraft()
            && $user->employee?->id === $evaluation->evaluator_employee_id;
    }

    public function rules(): array
    {
        return [
            'kpi_template_id' => ['nullable', 'exists:kpi_templates,id', 'prohibits:employee_kpi_evaluation_id', 'required_without:employee_kpi_evaluation_id'],
            'employee_kpi_evaluation_id' => ['nullable', 'exists:employee_kpi_evaluations,id', 'prohibits:kpi_template_id', 'required_without:kpi_template_id'],
            'document_type' => ['required', 'string', 'max:50'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'file' => ['required', 'file', 'max:20480', 'mimes:pdf,doc,docx,xlsx,xls,ppt,pptx'],
        ];
    }
}
