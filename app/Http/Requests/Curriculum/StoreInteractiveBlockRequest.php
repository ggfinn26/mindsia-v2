<?php

namespace App\Http\Requests\Curriculum;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreInteractiveBlockRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('curriculum.interactive.manage');
    }

    public function rules(): array
    {
        return [
            'blocks' => ['required', 'array', 'min:1'],
            'blocks.*.sort_order' => ['required', 'integer', 'min:0'],
            'blocks.*.instruction_text' => ['nullable', 'string'],
            'blocks.*.audio_file' => ['nullable', 'file', 'mimes:mp3,wav,ogg,m4a', 'max:51200'],
            'blocks.*.image_file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,gif', 'max:10240'],
            'blocks.*.existing_audio_id' => ['nullable', 'integer', Rule::exists('curriculum_media', 'id')->where('uploaded_by_employee_id', $this->user()->employee_id)],
            'blocks.*.existing_image_id' => ['nullable', 'integer', Rule::exists('curriculum_media', 'id')->where('uploaded_by_employee_id', $this->user()->employee_id)],
            'blocks.*.questions' => ['nullable', 'array'],
            'blocks.*.questions.*.sort_order' => ['required', 'integer', 'min:0'],
            'blocks.*.questions.*.question_text' => ['nullable', 'string'],
            'blocks.*.questions.*.option_a' => ['required', 'string', 'max:500'],
            'blocks.*.questions.*.option_b' => ['required', 'string', 'max:500'],
            'blocks.*.questions.*.option_c' => ['nullable', 'string', 'max:500'],
            'blocks.*.questions.*.option_d' => ['nullable', 'string', 'max:500'],
            'blocks.*.questions.*.correct_option' => ['required', 'in:A,B,C,D'],
            'blocks.*.questions.*.explanation' => ['nullable', 'string'],
        ];
    }
}
