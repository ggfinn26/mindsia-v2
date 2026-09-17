<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ClassRoom\UpdateClassScheduleMaterialRequest;
use App\Models\ClassSchedule;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ClassScheduleController extends Controller
{
    public function index(): View
    {
        $this->authorize('class.attendance.record');

        $schedules = ClassSchedule::with('classRoom.program', 'classRoom.branch', 'classRoom.tutor')
            ->withCount([
                'memberAttendances as present_count' => fn ($q) => $q->where('status', 'present'),
                'memberAttendances as absent_count' => fn ($q) => $q->where('status', 'absent'),
                'memberAttendances as late_count' => fn ($q) => $q->where('status', 'late'),
            ])
            ->whereHas('classRoom', fn ($q) => $q->whereIn('status', ['active', 'planned']))
            ->orderByDesc('schedule_date')
            ->paginate(20);

        return view('admin.class-schedule.index', compact('schedules'));
    }

    public function update(UpdateClassScheduleMaterialRequest $request, ClassSchedule $schedule): RedirectResponse
    {
        abort_if(
            $schedule->schedule_date > today(),
            422,
            'Materi tidak bisa diisi sebelum tanggal sesi.'
        );

        $schedule->update(['material_taught' => $request->validated('material_taught')]);

        return back()->with('success', 'Materi sesi berhasil disimpan.');
    }
}
