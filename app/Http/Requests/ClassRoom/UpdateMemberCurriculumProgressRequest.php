<?php

namespace App\Http\Requests\ClassRoom;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMemberCurriculumProgressRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('class.progress.update');
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'in:not_started,in_progress,completed,needs_review'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
