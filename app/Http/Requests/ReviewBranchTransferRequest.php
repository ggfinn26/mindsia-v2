<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReviewBranchTransferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->can('reviewBranchTransfer');
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'in:approved,rejected'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'Status harus dipilih.',
            'status.in' => 'Status harus berupa approved atau rejected.',
            'notes.max' => 'Catatan tidak boleh lebih dari 1000 karakter.',
        ];
    }
}
