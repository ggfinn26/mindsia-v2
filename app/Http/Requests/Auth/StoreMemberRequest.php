<?php

namespace App\Http\Requests\Auth;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class StoreMemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function failedValidation(Validator $validator): never
    {
        Log::warning('Member register validation failed', [
            'errors' => $validator->errors()->toArray(),
            'input' => $this->except(['password', 'password_confirmation']),
        ]);

        throw new ValidationException($validator);
    }

    public function messages(): array
    {
        return [
            'email.unique' => 'Email ini sudah terdaftar.',
            'whatsapp_number.unique' => 'Nomor WhatsApp ini sudah terdaftar.',
            'whatsapp_number.min' => 'Nomor WhatsApp minimal 8 digit.',
        ];
    }

    public function rules(): array
    {
        return [
            'full_name' => ['required', 'string', 'max:255'],
            'gender' => ['required', 'in:L,P'],
            'birthdate' => ['required', 'date', 'before:'.now()->subYears(6)->format('Y-m-d')],
            'birth_place' => ['required', 'string', 'max:255'],
            'whatsapp_number' => ['required', 'string', 'min:8', 'max:15', 'unique:members_data,whatsapp_number'],
            'email' => ['required', 'email', 'max:255', 'unique:members_data,email', 'unique:member_accounts,email'],
            'instagram' => ['nullable', 'string', 'max:255'],
            'father_name' => ['required', 'string', 'max:255'],
            'mother_name' => ['required', 'string', 'max:255'],
            'father_occupation' => ['required', 'string', 'max:255'],
            'mother_occupation' => ['required', 'string', 'max:255'],
            'father_whatsapp' => ['required', 'string', 'max:20'],
            'mother_whatsapp' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string'],
            'branch_id' => ['required', 'exists:branches,id'],
            'institution_id' => ['nullable', 'exists:institutions,id', 'required_without:institution_name_custom'],
            'institution_name_custom' => ['nullable', 'string', 'max:255'],
            'program_id' => ['nullable', 'exists:programs,id'],
            'referred_by_code' => ['nullable', 'string', 'max:20'],
            'education_level' => ['nullable', 'string', 'in:SD,SMP,SMA,SMK,D1,D2,D3,D4,S1,S2,S3,Umum'],
            'class_or_faculty' => ['nullable', 'string', 'max:100'],
            'password' => ['required', Password::defaults(), 'confirmed'],
            'password_confirmation' => ['required'],
        ];
    }
}
