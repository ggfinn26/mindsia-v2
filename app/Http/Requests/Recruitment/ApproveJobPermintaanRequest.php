<?php

namespace App\Http\Requests\Recruitment;

use Illuminate\Foundation\Http\FormRequest;

class ApproveJobPermintaanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('ops_approve_permintaan') || $this->user()->hasRole('BOARD');
    }

    public function rules(): array
    {
        return [
            'decision' => ['required', 'in:approved,rejected'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
