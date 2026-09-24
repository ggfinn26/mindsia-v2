<?php

namespace App\Http\Requests\Marketing;

use App\Models\Branch;
use App\Models\Employee;
use Illuminate\Foundation\Http\FormRequest;

class StoreSocializationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('marketing.socialization.create');
    }

    public function rules(): array
    {
        return [
            'institution_id' => ['nullable', 'exists:institutions,id'],
            'location_name' => ['nullable', 'string', 'max:255'],
            'schedule_date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'partner_fee_amount' => ['nullable', 'numeric', 'min:0'],
            'partner_fee_due_date' => ['nullable', 'date', 'required_with:partner_fee_amount'],
            'employee_ids' => ['required', 'array', 'min:1'],
            'employee_ids.*' => ['integer', 'exists:employees,id'],
            'schedule_now' => ['nullable', 'boolean'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($v) {
            if (empty($this->institution_id) && empty(trim($this->location_name ?? ''))) {
                $v->errors()->add('location_name', 'Pilih institusi atau isi nama lokasi.');
            }

            $employee = $this->user()->employee;
            if (! $employee) {
                return;
            }

            $area = $employee->area
                ?? ($employee->branch_id ? Branch::with('area')->find($employee->branch_id)?->area : null);

            if (! $area) {
                return;
            }

            $allowedIds = Employee::where('is_active', true)
                ->where(fn ($q) => $q
                    ->where('area_id', $area->id)
                    ->orWhereHas('branch', fn ($b) => $b->where('areas_id', $area->id))
                )->pluck('id')->all();

            foreach ((array) $this->input('employee_ids', []) as $id) {
                if (! in_array((int) $id, $allowedIds, true)) {
                    $v->errors()->add('employee_ids', 'Salah satu pegawai tidak berada di area Anda.');
                    break;
                }
            }
        });
    }
}
