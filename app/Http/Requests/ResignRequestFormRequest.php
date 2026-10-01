<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ResignRequestFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        $employee = $this->route('employee') ?? auth()->user()->employee;

        return $employee && auth()->user()->id === $employee->user_id;
    }

    public function rules(): array
    {
        return [
            'resign_date' => ['required', 'date', 'after:today'],
            'reason' => ['required', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'resign_date.required' => 'Tanggal efektif pengunduran wajib diisi.',
            'resign_date.after' => 'Tanggal efektif harus setelah hari ini.',
            'reason.required' => 'Alasan pengunduran diri wajib diisi.',
        ];
    }
}
