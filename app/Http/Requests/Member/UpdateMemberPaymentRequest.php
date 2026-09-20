<?php

namespace App\Http\Requests\Member;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMemberPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('member.payment.manage');
    }

    public function rules(): array
    {
        return [
            'payment_status' => ['sometimes', 'required', 'in:paid,unpaid'],
            'payment_method' => ['nullable', 'in:transfer_bank,cash,qris,e_wallet,lainnya'],
            'telegram_payment_proof_id' => ['nullable', 'string', 'max:500'],
            'notes' => ['nullable', 'string'],
            'due_date' => ['sometimes', 'nullable', 'date'],
        ];
    }
}
