<?php

namespace App\Http\Requests\Kpi;

use App\Models\KpiTemplateIndicator;
use App\Services\Kpi\KpiDataSourceService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreKpiTemplateIndicatorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('kpi.template.update');
    }

    public function rules(): array
    {
        $templateId = $this->route('kpiTemplate')->id;

        return [
            'indicator_code' => ['required', 'string', 'max:50', Rule::unique('kpi_template_indicators')->where('kpi_template_id', $templateId)],
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
            $templateId = $this->route('kpiTemplate')?->id;
            $newWeight = (float) $this->input('weight', 0);

            $existingTotal = KpiTemplateIndicator::where('kpi_template_id', $templateId)
                ->where('is_active', true)
                ->sum('weight');

            if ($existingTotal + $newWeight > 100) {
                $v->errors()->add('weight', "Total bobot indikator aktif akan melebihi 100 (saat ini: {$existingTotal}, tambah: {$newWeight}).");
            }
        });
    }
}
