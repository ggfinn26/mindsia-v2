<?php

namespace App\Http\Requests;

use App\Models\ClassRoom;
use Illuminate\Foundation\Http\FormRequest;

class StoreProgramRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('curriculum.program.create');
    }

    public function messages(): array
    {
        return [
            'program_name.required' => 'Nama program wajib diisi.',
            'program_code.required' => 'Kode program wajib diisi.',
            'program_code.unique' => 'Kode program sudah digunakan.',
            'program_price.required' => 'Harga program wajib diisi.',
            'program_price.numeric' => 'Harga program harus berupa angka.',
            'program_price.min' => 'Harga program tidak boleh negatif.',
            'image.image' => 'File harus berupa gambar.',
            'image.max' => 'Ukuran gambar maksimal 2 MB.',
            'receipt_template.required' => 'Template kwitansi wajib diupload.',
            'receipt_template.mimes' => 'Template kwitansi harus berupa file .docx.',
            'receipt_template.max' => 'Ukuran template maksimal 20 MB.',
        ];
    }

    public function rules(): array
    {
        return [
            'program_name' => ['required', 'string', 'max:255'],
            'program_code' => ['required', 'string', 'max:50', 'unique:programs,program_code'],
            'program_price' => ['required', 'numeric', 'min:0'],
            'admin_fee' => ['nullable', 'numeric', 'min:0'],
            'admin_fee_mode' => ['nullable', 'in:per_registration,per_transaction'],
            'admin_fee_timing' => ['nullable', 'in:on_registration,on_first_payment'],
            'program_description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'max:2048'],
            'receipt_template' => ['nullable', 'file', 'mimes:docx', 'max:20480'],
            'is_active' => ['boolean'],
            'tagline' => ['nullable', 'string', 'max:500'],
            'badge' => ['nullable', 'string', 'max:100'],
            'target_level' => ['nullable', 'string', 'max:100'],
            'duration' => ['nullable', 'string', 'max:100'],
            'facilities' => ['nullable', 'array'],
            'facilities.*' => ['nullable', 'string', 'max:255'],
            'benefits' => ['nullable', 'array'],
            'benefits.*' => ['nullable', 'string', 'max:255'],
            'schedule' => ['nullable', 'array'],
            'schedule.*.day' => ['nullable', 'in:'.implode(',', ClassRoom::PRIMARY_DAYS)],
            'schedule.*.start' => ['nullable', 'date_format:H:i'],
            'schedule.*.end' => ['nullable', 'date_format:H:i'],
            'faqs' => ['nullable', 'array'],
            'faqs.*.q' => ['nullable', 'string', 'max:255'],
            'faqs.*.a' => ['nullable', 'string', 'max:2000'],
            'gallery_images' => ['nullable', 'array'],
            'gallery_images.*' => ['image', 'max:2048'],
            'remove_gallery' => ['nullable', 'array'],
            'remove_gallery.*' => ['string'],
        ];
    }
}
