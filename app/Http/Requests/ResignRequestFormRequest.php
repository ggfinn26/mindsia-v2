<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ResignRequestFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        $employee = $this->route('employee');

        return $employee && auth()->user()->id === $employee->user_id;
    }

    public function rules(): array
    {
        return [
            'resign_date' => ['required', 'date', 'after:today'],
            'reason' => ['nullable', 'string', 'max:500'],
        ];
    }
}
