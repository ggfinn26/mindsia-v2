<?php

namespace App\Http\Requests\Recruitment;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreInterviewScheduleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('manage recruitment stages');
    }

    public function rules(): array
    {
        return [
            'interview_type' => ['required', 'in:online,offline'],
            'scheduled_at' => ['required', 'date', 'after:now'],
            'meeting_link' => ['nullable', 'url', 'max:500'],
            'location' => ['nullable', 'string', 'max:255'],
            'interviewer_employee_id' => ['required', 'exists:employees,id'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v) {
            $type = $this->input('interview_type');
            if ($type === 'online' && ! $this->filled('meeting_link')) {
                $v->errors()->add('meeting_link', 'meeting_link wajib diisi untuk interview online.');
            }
            if ($type === 'offline' && ! $this->filled('location')) {
                $v->errors()->add('location', 'location wajib diisi untuk interview offline.');
            }
        });
    }
}
