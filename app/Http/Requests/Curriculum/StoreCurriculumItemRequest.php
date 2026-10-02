<?php

namespace App\Http\Requests\Curriculum;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCurriculumItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('curriculum.item.create');
    }

    public function rules(): array
    {
        return [
            'item_name' => ['required', 'string', 'max:255'],
            'sequence_number' => ['required', 'integer', 'min:1', Rule::unique('curriculum_items')->where('curriculum_session_id', $this->route('session')->id)],
            'material_type' => ['required', 'in:file,external_link,interactive'],
            'material_value' => ['nullable', 'string'],
            'material_file' => ['nullable', 'file', 'max:51200', 'mimes:pdf,mp4,webm,ogg,mov,jpg,jpeg,png,gif,webp,mp3,wav,aac,m4a'],
            'notes' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'item_name.required' => 'Nama materi wajib diisi.',
            'sequence_number.required' => 'Nomor urutan wajib diisi.',
            'sequence_number.min' => 'Nomor urutan minimal 1.',
            'sequence_number.unique' => 'Nomor urutan ini sudah dipakai di sesi ini.',
            'material_type.required' => 'Tipe materi wajib dipilih.',
            'material_type.in' => 'Tipe materi tidak valid.',
            'material_file.file' => 'File tidak valid.',
            'material_file.max' => 'Ukuran file maksimal 50MB.',
            'material_file.mimes' => 'Tipe file tidak diizinkan. Hanya PDF, video (mp4/webm/ogg/mov), gambar (jpg/png/gif/webp), dan audio (mp3/wav/aac/m4a).',
        ];
    }
}
