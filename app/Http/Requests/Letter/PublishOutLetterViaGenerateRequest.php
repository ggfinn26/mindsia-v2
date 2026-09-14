<?php

namespace App\Http\Requests\Letter;

use App\Models\OutLetterViaGenerate;
use Illuminate\Foundation\Http\FormRequest;

class PublishOutLetterViaGenerateRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var OutLetterViaGenerate $letter */
        $letter = $this->route('outLetterViaGenerate');

        return $letter->isDraft() && $this->user()->can('letter.generate.publish');
    }

    public function rules(): array
    {
        return [];
    }
}
