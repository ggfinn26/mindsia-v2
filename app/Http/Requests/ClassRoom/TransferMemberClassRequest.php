<?php

namespace App\Http\Requests\ClassRoom;

use App\Models\ClassRoom;
use Illuminate\Foundation\Http\FormRequest;

class TransferMemberClassRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('class.manage');
    }

    public function rules(): array
    {
        $currentClassId = $this->route('memberClass')?->class_id;

        return [
            'new_class_id' => [
                'required',
                'exists:classes,id',
                "not_in:{$currentClassId}",
                function (string $attribute, mixed $value, \Closure $fail) {
                    $class = ClassRoom::find($value);
                    if ($class && $class->status !== 'active') {
                        $fail('Kelas tujuan harus berstatus aktif.');
                    }
                },
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'new_class_id.not_in' => 'Kelas tujuan tidak boleh sama dengan kelas saat ini.',
        ];
    }
}
