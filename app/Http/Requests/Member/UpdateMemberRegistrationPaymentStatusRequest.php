<?php

namespace App\Http\Requests\Member;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMemberRegistrationPaymentStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('member.payment.manage');
    }

    public function rules(): array
    {
        return [
            'payment_status' => ['required', 'in:paid_full,unpaid,installments'],
        ];
    }
}
