<?php

namespace App\Http\Requests\Marketing;

use Illuminate\Foundation\Http\FormRequest;

class StoreSocializationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('marketing.socialization.create');
    }

    public function rules(): array
    {
        return [
            'branch_id' => ['required', 'exists:branches,id'],
            'institution_id' => ['nullable', 'exists:institutions,id'],
            'location_name' => ['nullable', 'string', 'max:255'],
            'schedule_date' => ['required', 'date', 'after_or_equal:today'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i'],
            'schedule_now' => ['nullable', 'boolean'],
            'partner_fee_amount' => ['nullable', 'numeric', 'min:0'],
            'partner_fee_due_date' => ['nullable', 'date'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($v) {
            $institutionId = $this->input('institution_id');
            $hasValidInstitution = $institutionId && ! $v->errors()->has('institution_id');

            if (! $hasValidInstitution && empty(trim($this->location_name ?? ''))) {
                $v->errors()->add('location_name', 'Pilih institusi atau isi nama lokasi.');
            }

            $amount = $this->input('partner_fee_amount');
            if ($amount !== null && (float) $amount > 0 && empty($this->input('partner_fee_due_date'))) {
                $v->errors()->add('partner_fee_due_date', 'Tanggal jatuh tempo wajib diisi jika ada biaya partner.');
            }

            $start = $this->input('start_time');
            $end = $this->input('end_time');
            if ($start && $end && $end <= $start) {
                $v->errors()->add('end_time', 'Jam selesai harus setelah jam mulai.');
            }
        });
    }
}
