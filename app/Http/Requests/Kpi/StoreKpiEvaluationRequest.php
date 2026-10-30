<?php

namespace App\Http\Requests\Kpi;

use App\Models\KpiEvaluatorAssignment;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreKpiEvaluationRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        if (! $user->can('kpi.evaluation.create')) {
            return false;
        }

        $evaluatorEmployee = $user->employee;
        if (! $evaluatorEmployee) {
            return false;
        }

        return KpiEvaluatorAssignment::active()
            ->effective()
            ->where('evaluator_employee_id', $evaluatorEmployee->id)
            ->where('evaluatee_employee_id', $this->input('employee_id'))
            ->where(fn ($q) => $q
                ->whereNull('kpi_template_id')
                ->orWhere('kpi_template_id', $this->input('kpi_template_id'))
            )
            ->exists();
    }

    public function rules(): array
    {
        return [
            'employee_id' => [
                'required',
                'exists:employees,id',
                Rule::unique('employee_kpi_evaluations')->where(function ($query) {
                    return $query->where('kpi_template_id', $this->kpi_template_id)
                        ->where('period_type', $this->period_type)
                        ->where('period_start_date', $this->period_start_date)
                        ->where('period_end_date', $this->period_end_date);
                }),
            ],
            'kpi_template_id' => ['required', 'exists:kpi_templates,id'],
            'period_type' => ['required', 'in:monthly,quarterly,yearly'],
            'period_start_date' => ['required', 'date'],
            'period_end_date' => ['required', 'date', 'after_or_equal:period_start_date'],
        ];
    }

    /**
     * Periode wajib utuh dan sudah berakhir: bulan/kuartal/tahun penuh, bukan rentang bebas.
     * Nilai otomatis ditarik sekali saat evaluasi dibuat; periode yang belum selesai atau tidak sejajar
     * dengan kalender menghasilkan nilai salah (bulan/tahun salah).
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v) {
            if ($v->errors()->hasAny(['period_type', 'period_start_date', 'period_end_date'])) {
                return;
            }

            $start = Carbon::parse($this->input('period_start_date'))->startOfDay();
            $end = Carbon::parse($this->input('period_end_date'))->startOfDay();

            [$months, $startMonthValid] = match ($this->input('period_type')) {
                'monthly' => [1, true],
                'quarterly' => [3, in_array($start->month, [1, 4, 7, 10], true)],
                default => [12, $start->month === 1],
            };

            if ($start->day !== 1 || ! $startMonthValid || ! $end->equalTo($start->copy()->addMonthsNoOverflow($months)->subDay())) {
                $v->errors()->add('period_start_date', 'Tanggal periode harus sesuai tipe: bulanan = 1 bulan penuh, kuartalan = Jan/Apr/Jul/Okt selama 3 bulan, tahunan = 1 Januari - 31 Desember.');

                return;
            }

            if ($end->gte(today())) {
                $v->errors()->add('period_end_date', 'Periode belum berakhir. Evaluasi hanya bisa dibuat setelah periode selesai.');
            }
        });
    }

    public function messages(): array
    {
        return [
            'employee_id.unique' => 'Evaluasi KPI untuk pegawai, template, dan periode ini sudah pernah dibuat.',
        ];
    }
}
