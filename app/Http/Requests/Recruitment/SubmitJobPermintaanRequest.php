<?php

namespace App\Http\Requests\Recruitment;

use App\Models\JobPermintaan;
use Illuminate\Foundation\Http\FormRequest;

class SubmitJobPermintaanRequest extends FormRequest
{
    public function authorize(): bool
    {
        $permintaan = $this->route('jobPermintaan');

        return $permintaan->status === JobPermintaan::STATUS_DRAFT
            && $this->user()->employee->id === $permintaan->requested_by_employee_id;
    }

    public function rules(): array
    {
        return [];
    }
}
