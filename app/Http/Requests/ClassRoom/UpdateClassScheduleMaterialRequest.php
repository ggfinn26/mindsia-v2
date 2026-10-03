<?php

namespace App\Http\Requests\ClassRoom;

use Illuminate\Foundation\Http\FormRequest;

class UpdateClassScheduleMaterialRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();
        if (! $user->can('class.session.update')) {
            return false;
        }
        if ($user->can('class.manage')) {
            return true;
        }
        $schedule = $this->route('schedule');

        return $schedule->classRoom?->tutor_id === $user->employee?->id;
    }

    public function rules(): array
    {
        return [
            'material_taught' => ['nullable', 'string', 'max:255'],
        ];
    }
}
