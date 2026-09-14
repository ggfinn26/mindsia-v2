<?php

namespace App\Http\Requests\Recruitment;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateInterviewScheduleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('recruitment.interview.update');
    }

    public function rules(): array
    {
        return [
            'interview_type' => ['sometimes', 'in:online,offline'],
            'scheduled_at' => ['sometimes', 'date', 'after:now'],
            'meeting_link' => ['nullable', 'url', 'max:500'],
            'location' => ['nullable', 'string', 'max:255'],
            'interviewer_employee_id' => ['sometimes', 'exists:employees,id'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v) {
            $schedule = $this->route('schedule');
            $type = $this->input('interview_type', $schedule->interview_type);

            if ($type === 'online' && ! ($this->filled('meeting_link') || $schedule->meeting_link)) {
                $v->errors()->add('meeting_link', 'meeting_link wajib diisi untuk interview online.');
            }
            if ($type === 'offline' && ! ($this->filled('location') || $schedule->location)) {
                $v->errors()->add('location', 'location wajib diisi untuk interview offline.');
            }
        });
    }
}
