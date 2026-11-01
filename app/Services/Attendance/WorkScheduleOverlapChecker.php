<?php

namespace App\Services\Attendance;

use App\Models\Employee;
use App\Models\Position;
use App\Models\WorkScheduleAssignment;
use App\Models\WorkScheduleRule;
use Spatie\Permission\Models\Role;

/**
 * Satu pegawai/posisi/role boleh punya beberapa jadwal (mis. Senin-Jumat dan Sabtu dengan jam berbeda), asalkan tidak
 * ada hari yang sama pada rentang tanggal yang sama. Jadwal tanpa hari kerja berlaku setiap hari.
 */
class WorkScheduleOverlapChecker
{
    public const TYPE_MAP = ['employee' => Employee::class, 'position' => Position::class, 'role' => Role::class];

    private const DAY_NAMES = [1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu', 7 => 'Minggu'];

    /**
     * Pesan kesalahan untuk bentrokan pertama yang ditemukan, atau null bila aman.
     *
     * @param  list<int|string>  $ids
     * @param  list<int>  $newDays  hari ISO 1-7 milik jadwal baru; kosong = setiap hari
     */
    public function firstConflict(string $typeKey, array $ids, string $newName, array $newDays, string $start, ?string $end, ?int $ignoreRuleId = null): ?string
    {
        $type = self::TYPE_MAP[$typeKey] ?? null;
        if (! $type) {
            return null;
        }

        $newDays = $newDays ?: array_keys(self::DAY_NAMES);

        foreach ($ids as $id) {
            $existing = WorkScheduleAssignment::with('workScheduleRule.workDays')
                ->where('assignable_type', $type)->where('assignable_id', $id)->where('is_active', true)
                ->when($ignoreRuleId, fn ($q) => $q->where('work_schedule_rule_id', '!=', $ignoreRuleId))
                ->where(fn ($q) => $q->whereNull('effective_end_date')->orWhere('effective_end_date', '>=', $start))
                ->when($end, fn ($q) => $q->where('effective_start_date', '<=', $end))
                ->get();

            foreach ($existing as $assignment) {
                $shared = array_intersect($newDays, $this->daysOf($assignment->workScheduleRule));
                if ($shared) {
                    return sprintf(
                        '%s: jadwal "%s" bentrok dengan "%s" yang sudah berlaku pada hari yang sama (%s).',
                        $this->targetName($type, $id), $newName, $assignment->workScheduleRule->setting_name, implode(', ', array_map(fn ($d) => self::DAY_NAMES[$d], $shared)),
                    );
                }
            }
        }

        return null;
    }

    /** @return list<int> hari ISO 1-7; jadwal tanpa hari kerja berlaku setiap hari */
    public function daysOf(WorkScheduleRule $rule): array
    {
        $days = $rule->workDays->pluck('day_of_week')->map(fn ($d) => (int) $d)->all();

        return $days ?: array_keys(self::DAY_NAMES);
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
}
