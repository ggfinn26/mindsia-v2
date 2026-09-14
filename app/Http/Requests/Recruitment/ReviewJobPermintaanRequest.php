<?php

namespace App\Http\Requests\Recruitment;

use Illuminate\Foundation\Http\FormRequest;

class ReviewJobPermintaanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('recruitment.job_permintaan.review');
    }

    public function rules(): array
    {
        return [
            'decision' => ['required', 'in:approved,rejected'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
