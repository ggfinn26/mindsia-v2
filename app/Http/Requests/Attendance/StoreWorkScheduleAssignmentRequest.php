<?php

namespace App\Http\Requests\Attendance;

use App\Models\Employee;
use App\Models\Position;
use App\Models\WorkScheduleAssignment;
use App\Models\WorkScheduleRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use Spatie\Permission\Models\Role;

class StoreWorkScheduleAssignmentRequest extends FormRequest
{
    private const TYPE_MAP = ['employee' => Employee::class, 'position' => Position::class, 'role' => Role::class];

    private const DAY_NAMES = [1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu', 7 => 'Minggu'];

    public function authorize(): bool
    {
        return $this->user()->can('attendance.schedule_rule.manage');
    }

    public function rules(): array
    {
        return [
            'work_schedule_rule_id' => 'required|exists:work_schedule_rules,id',
            'assignable_type' => 'required|in:employee,position,role',
            'assignable_id' => 'required',
            'assignable_id.*' => 'integer|min:1',
            'effective_start_date' => 'required|date',
            'effective_end_date' => 'nullable|date|after_or_equal:effective_start_date',
            'is_active' => 'boolean',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $type = $this->input('assignable_type');
            $id = $this->input('assignable_id');

            $tableMap = [
                'employee' => 'employees',
                'position' => 'positions',
                'role' => 'roles',
            ];

            $table = $tableMap[$type] ?? null;
            if ($table) {
                $ids = (array) $this->input('assignable_id');
                foreach ($ids as $id) {
                    if (! \DB::table($table)->where('id', $id)->exists()) {
                        $validator->errors()->add('assignable_id', "The selected $type (id: $id) does not exist.");
                    }
                }
            }

            if ($validator->errors()->isEmpty()) {
                $this->rejectOverlappingAssignments($validator);
            }
        });
    }

    /**
     * Satu pegawai/posisi/role boleh punya beberapa jadwal (mis. Senin-Jumat dan Sabtu dengan jam berbeda), asalkan tidak
     * ada hari yang sama pada rentang tanggal yang sama. Jadwal tanpa hari kerja berlaku setiap hari.
     */
    private function rejectOverlappingAssignments(Validator $validator): void
    {
        if (! ($this->boolean('is_active', true))) {
            return;
        }

        $type = self::TYPE_MAP[$this->input('assignable_type')] ?? null;
        $newRule = WorkScheduleRule::with('workDays')->find($this->input('work_schedule_rule_id'));
        if (! $type || ! $newRule) {
            return;
        }

        $newDays = $this->daysOf($newRule);
        $start = $this->input('effective_start_date');
        $end = $this->input('effective_end_date');

        foreach ((array) $this->input('assignable_id') as $id) {
            $existing = WorkScheduleAssignment::with('workScheduleRule.workDays')
                ->where('assignable_type', $type)->where('assignable_id', $id)->where('is_active', true)
                ->where('work_schedule_rule_id', '!=', $newRule->id)
                ->where(fn ($q) => $q->whereNull('effective_end_date')->orWhere('effective_end_date', '>=', $start))
                ->when($end, fn ($q) => $q->where('effective_start_date', '<=', $end))
                ->get();

            foreach ($existing as $assignment) {
                $shared = array_intersect($newDays, $this->daysOf($assignment->workScheduleRule));
                if ($shared) {
                    $validator->errors()->add('assignable_id', sprintf(
                        '%s: jadwal "%s" bentrok dengan "%s" yang sudah berlaku pada hari yang sama (%s).',
                        $this->targetName($type, $id), $newRule->setting_name, $assignment->workScheduleRule->setting_name, implode(', ', array_map(fn ($d) => self::DAY_NAMES[$d], $shared)),
                    ));

                    return;
                }
            }
        }
    }

    private function targetName(string $type, mixed $id): string
    {
        $model = $type::find($id);

        return match (true) {
            $model instanceof Employee => $model->full_name,
            $model instanceof Position => $model->position_name,
            default => $model?->name ?? "#{$id}",
        };
    }

    /** @return list<int> hari ISO 1-7; jadwal tanpa hari kerja berlaku setiap hari */
    private function daysOf(WorkScheduleRule $rule): array
    {
        $days = $rule->workDays->pluck('day_of_week')->map(fn ($d) => (int) $d)->all();

        return $days ?: [1, 2, 3, 4, 5, 6, 7];
    }
}
