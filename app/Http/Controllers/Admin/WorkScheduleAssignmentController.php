<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Attendance\StoreWorkScheduleAssignmentRequest;
use App\Models\WorkScheduleAssignment;
use Illuminate\Http\RedirectResponse;

class WorkScheduleAssignmentController extends Controller
{
    public function store(StoreWorkScheduleAssignmentRequest $request): RedirectResponse
    {
        WorkScheduleAssignment::create($request->validated());

        return back()->with('success', 'Jadwal kerja berhasil di-assign.');
    }

    public function destroy(WorkScheduleAssignment $workScheduleAssignment): RedirectResponse
    {
        abort_unless(auth()->user()->can('attendance.schedule_rule.manage'), 403);

        $workScheduleAssignment->delete();

        return back()->with('success', 'Assignment jadwal kerja berhasil dihapus.');
    }
}
