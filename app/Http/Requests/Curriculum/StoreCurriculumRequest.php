<?php

namespace App\Http\Requests\Curriculum;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCurriculumRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('curriculum.curriculum.create');
    }

    public function rules(): array
    {
        return [
            'program_id' => ['required', 'exists:programs,id', Rule::unique('curriculums', 'program_id')],
            'curriculum_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_active' => ['boolean'],
            'sessions' => ['nullable', 'array'],
            'sessions.*.session_number' => ['required', 'integer', 'min:1'],
            'sessions.*.session_title' => ['required', 'string', 'max:150'],
            'sessions.*.description' => ['nullable', 'string'],
            'sessions.*.sort_order' => ['nullable', 'integer', 'min:0'],
            'sessions.*.is_active' => ['boolean'],
            'sessions.*.items' => ['nullable', 'array'],
            'sessions.*.items.*.item_name' => ['required', 'string', 'max:255'],
            'sessions.*.items.*.sequence_number' => ['required', 'integer', 'min:1'],
            'sessions.*.items.*.material_type' => ['required', 'in:file,external_link'],
            'sessions.*.items.*.material_value' => ['required', 'string'],
            'sessions.*.items.*.notes' => ['nullable', 'string'],
            'sessions.*.items.*.is_active' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'program_id.required' => 'Program wajib dipilih.',
            'program_id.exists' => 'Program tidak ditemukan.',
            'program_id.unique' => 'Program ini sudah memiliki kurikulum.',
            'curriculum_name.required' => 'Nama kurikulum wajib diisi.',
            'curriculum_name.max' => 'Nama kurikulum maksimal 255 karakter.',
            'sessions.*.session_number.required' => 'Nomor minggu wajib diisi.',
            'sessions.*.session_number.integer' => 'Nomor minggu harus berupa angka.',
            'sessions.*.session_number.min' => 'Nomor minggu minimal 1.',
            'sessions.*.session_title.required' => 'Judul sesi wajib diisi.',
            'sessions.*.session_title.max' => 'Judul sesi maksimal 150 karakter.',
            'sessions.*.items.*.item_name.required' => 'Nama materi wajib diisi.',
            'sessions.*.items.*.sequence_number.required' => 'Nomor urutan materi wajib diisi.',
            'sessions.*.items.*.material_type.required' => 'Tipe materi wajib dipilih.',
            'sessions.*.items.*.material_type.in' => 'Tipe materi harus berupa file atau link eksternal.',
            'sessions.*.items.*.material_value.required' => 'File atau URL materi wajib diisi.',
        ];
    }
}
