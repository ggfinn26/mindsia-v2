<?php

namespace App\Http\Requests\Marketing;

use App\Models\Branch;
use App\Models\Employee;
use Illuminate\Foundation\Http\FormRequest;

class AssignEmployeeSocializationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->canAny(['marketing.socialization.assign', 'marketing.socialization.update']);
    }

    public function rules(): array
    {
        return [
            'employee_id' => ['nullable', 'integer', 'exists:employees,id'],
            'employee_ids' => ['nullable', 'array', 'min:1'],
            'employee_ids.*' => ['exists:employees,id'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($v) {
            $ids = $this->employeeIds();

            if (empty($ids)) {
                $v->errors()->add('employee_id', 'Pegawai wajib dipilih.');

                return;
            }

            $currentEmployee = $this->user()->employee;
            if (! $currentEmployee) {
                return;
            }

            $area = $currentEmployee->area
                ?? ($currentEmployee->branch_id
                    ? Branch::with('area')->find($currentEmployee->branch_id)?->area
                    : null);

            if (! $area) {
                return;
            }

            $allowedIds = Employee::where('is_active', true)
                ->where(fn ($q) => $q
                    ->where('area_id', $area->id)
                    ->orWhereHas('branch', fn ($b) => $b->where('areas_id', $area->id))
                )->pluck('id')->all();

            foreach ($ids as $id) {
                if (! in_array($id, $allowedIds, true)) {
                    $v->errors()->add('employee_id', 'Pegawai tidak berada di area Anda.');
                    break;
                }
            }
        });
    }

    /** Normalize to array of IDs regardless of input shape. */
    public function employeeIds(): array
    {
        if ($this->input('employee_id')) {
            return [(int) $this->input('employee_id')];
        }

        return array_map('intval', $this->input('employee_ids', []));
    }
}
