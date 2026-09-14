<?php

namespace App\Http\Requests\Letter;

use App\Models\OutLetterViaGenerate;
use Illuminate\Foundation\Http\FormRequest;

class UpdateOutLetterViaGenerateRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var OutLetterViaGenerate $letter */
        $letter = $this->route('outLetterViaGenerate');

        return $letter->isDraft() && $this->user()->can('letter.generate.update');
    }

    public function rules(): array
    {
        return [
            'signer_employee_id' => ['nullable', 'exists:employees,id'],
            'recipient' => ['nullable', 'string', 'max:255'],
            'subject' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
