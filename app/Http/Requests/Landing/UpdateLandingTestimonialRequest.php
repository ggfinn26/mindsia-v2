<?php

namespace App\Http\Requests\Landing;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLandingTestimonialRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('landing.testimonial.update');
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:150'],
            'quote' => ['nullable', 'string'],
            'program' => ['nullable', 'string', 'max:100'],
            'city' => ['nullable', 'string', 'max:100'],
            'photo' => ['nullable', 'file', 'mimes:webp', 'max:5120'],
            'member_review_id' => ['nullable', 'exists:member_reviews,id'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
