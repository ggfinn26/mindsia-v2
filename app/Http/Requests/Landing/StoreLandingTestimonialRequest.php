<?php

namespace App\Http\Requests\Landing;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreLandingTestimonialRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('landing.testimonial.create');
    }

    public function rules(): array
    {
        return [
            'type' => ['required', 'in:text,image'],
            'name' => ['required', 'string', 'max:150'],
            'quote' => ['nullable', 'string'],
            'program' => ['nullable', 'string', 'max:100'],
            'city' => ['nullable', 'string', 'max:100'],
            'photo' => ['nullable', 'file', 'mimes:webp', 'max:5120'],
            'member_review_id' => ['nullable', 'exists:member_reviews,id'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v) {
            if ($this->input('type') === 'text' && empty($this->input('quote'))) {
                $v->errors()->add('quote', 'Quote wajib diisi untuk testimoni teks.');
            }
            if ($this->input('type') === 'image' && ! $this->hasFile('photo')) {
                $v->errors()->add('photo', 'Foto wajib diupload untuk testimoni gambar.');
            }
        });
    }
}
