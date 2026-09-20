<?php

namespace App\Http\Requests\Member;

use App\Models\ProgramPaymentPlan;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreMemberRegistrationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('member.manage');
    }

    public function rules(): array
    {
        return [
            'program_id' => ['required', 'integer', 'exists:programs,id'],
            'payment_plan_id' => ['required', 'integer', 'exists:program_payment_plans,id'],
            'discount_code' => ['nullable', 'string', 'exists:discounts,discount_code'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                if ($validator->errors()->has('payment_plan_id') || $validator->errors()->has('program_id')) {
                    return;
                }

                $plan = ProgramPaymentPlan::find($this->payment_plan_id);
                if (! $plan || ! $plan->is_active || (int) $plan->program_id !== (int) $this->program_id) {
                    $validator->errors()->add('payment_plan_id', 'Skema pembayaran tidak valid untuk program ini.');
                }
            },
        ];
    }
}
