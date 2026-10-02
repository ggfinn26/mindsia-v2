<?php

namespace App\Http\Requests\Curriculum;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCurriculumSessionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('curriculum.session.update');
    }

    public function rules(): array
    {
        return [
            'session_number' => ['required', 'integer', 'min:1', Rule::unique('curriculum_sessions')->where('curriculum_id', $this->route('session')->curriculum_id)->ignore($this->route('session'))],
            'session_title' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'session_number.required' => 'Nomor minggu wajib diisi.',
            'session_number.integer' => 'Nomor minggu harus berupa angka.',
            'session_number.min' => 'Nomor minggu minimal 1.',
            'session_number.unique' => 'Nomor minggu ini sudah dipakai di kurikulum ini.',
            'session_title.required' => 'Judul sesi wajib diisi.',
            'session_title.max' => 'Judul sesi maksimal 150 karakter.',
        ];
    }
}
