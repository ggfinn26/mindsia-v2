<?php

namespace App\Http\Requests\Member;

use Illuminate\Foundation\Http\FormRequest;

class StoreMemberNpsResponseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('member')->check();
    }

    public function rules(): array
    {
        return [
            'branch_id' => ['required', 'integer', 'exists:branches,id'],
            'score' => ['required', 'integer', 'min:0', 'max:10'],
            'comment' => ['nullable', 'string'],
        ];
    }
}
