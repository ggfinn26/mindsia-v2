<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Attendance\StoreWorkScheduleAssignmentRequest;
use App\Models\Employee;
use App\Models\Position;
use App\Models\WorkScheduleAssignment;
use Illuminate\Http\RedirectResponse;
use Spatie\Permission\Models\Role;

class WorkScheduleAssignmentController extends Controller
{
    private const TYPE_MAP = [
        'employee' => Employee::class,
        'position' => Position::class,
        'role' => Role::class,
    ];

    public function store(StoreWorkScheduleAssignmentRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $type = self::TYPE_MAP[$data['assignable_type']];
        $ids = (array) $data['assignable_id'];

        foreach ($ids as $id) {
            WorkScheduleAssignment::firstOrCreate(
                ['work_schedule_rule_id' => $data['work_schedule_rule_id'], 'assignable_type' => $type, 'assignable_id' => $id],
                ['effective_start_date' => $data['effective_start_date'], 'effective_end_date' => $data['effective_end_date'] ?? null, 'is_active' => $data['is_active'] ?? true],
            );
        }

        $count = count($ids);

        return back()->with('success', $count === 1 ? 'Jadwal kerja berhasil di-assign.' : "{$count} assignment berhasil dibuat.");
    }

    public function destroy(WorkScheduleAssignment $workScheduleAssignment): RedirectResponse
    {
        abort_unless(auth()->user()->can('attendance.schedule_rule.manage'), 403);

        $workScheduleAssignment->delete();

        return back()->with('success', 'Assignment jadwal kerja berhasil dihapus.');
    }
}
