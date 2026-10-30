<?php

namespace App\Http\Requests\Kpi;

use App\Models\KpiTemplateIndicator;
use App\Services\Kpi\KpiDataSourceService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateKpiTemplateIndicatorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('kpi.template.update');
    }

    public function rules(): array
    {
        $templateId = $this->route('kpiTemplate')->id;
        $indicatorId = $this->route('indicator')->id;

        return [
            'indicator_code' => ['required', 'string', 'max:50', Rule::unique('kpi_template_indicators')->where('kpi_template_id', $templateId)->ignore($indicatorId)],
            'indicator_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'unit' => ['nullable', 'string', 'max:50'],
            'target_value' => ['nullable', 'numeric', 'min:0'],
            'weight' => ['required', 'numeric', 'min:0', 'max:100'],
            'sequence_number' => ['required', 'integer', 'min:1'],
            'data_source_type' => ['nullable', 'string', Rule::in(KpiDataSourceService::ALLOWED_SOURCES)],
            'is_active' => ['boolean'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($v) {
            if (! $this->boolean('is_active', true)) {
                return;
            }

            $othersTotal = KpiTemplateIndicator::where('kpi_template_id', $this->route('kpiTemplate')->id)
                ->where('is_active', true)
                ->where('id', '!=', $this->route('indicator')->id)
                ->sum('weight');
            $newWeight = (float) $this->input('weight', 0);

            if ($othersTotal + $newWeight > 100) {
                $v->errors()->add('weight', "Total bobot indikator aktif akan melebihi 100 (indikator lain: {$othersTotal}, bobot ini: {$newWeight}).");
            }
        });
    }
}
