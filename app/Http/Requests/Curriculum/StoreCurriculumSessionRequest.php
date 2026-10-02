<?php

namespace App\Http\Requests\Curriculum;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCurriculumSessionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('curriculum.session.create');
    }

    public function rules(): array
    {
        return [
            'session_number' => ['required', 'integer', 'min:1', Rule::unique('curriculum_sessions')->where('curriculum_id', $this->route('curriculum')->id)],
            'session_title' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['boolean'],
            'items' => ['nullable', 'array'],
            'items.*.item_name' => ['required', 'string', 'max:255'],
            'items.*.sequence_number' => ['required', 'integer', 'min:1'],
            'items.*.material_type' => ['required', 'in:file,external_link'],
            'items.*.material_value' => ['required', 'string'],
            'items.*.notes' => ['nullable', 'string'],
            'items.*.is_active' => ['boolean'],
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
