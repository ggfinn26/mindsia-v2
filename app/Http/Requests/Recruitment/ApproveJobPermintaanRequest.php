<?php

namespace App\Http\Requests\Recruitment;

use Illuminate\Foundation\Http\FormRequest;

class ApproveJobPermintaanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('recruitment.job_permintaan.approve');
    }

    public function rules(): array
    {
        return [
            'decision' => ['required', 'in:approved,rejected'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
