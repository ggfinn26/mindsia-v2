<?php

namespace App\Http\Requests;

use App\Models\ClassRoom;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProgramRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('curriculum.program.update');
    }

    public function rules(): array
    {
        $programId = $this->route('program')->id;

        return [
            'program_name' => ['required', 'string', 'max:255'],
            'program_code' => ['required', 'string', 'max:50', Rule::unique('programs', 'program_code')->ignore($programId)],
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
        ];
    }
}
