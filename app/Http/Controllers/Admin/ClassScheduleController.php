<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ClassRoom\UpdateClassScheduleMaterialRequest;
use App\Models\Branch;
use App\Models\ClassSchedule;
use App\Models\Employee;
use App\Models\Program;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ClassScheduleController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        if (! $user->hasAnyPermission(['class.schedule.view.branchandarea', 'class.schedule.view.nasional'])) {
            abort(403);
        }

        $filters = request()->only(['branch_id', 'area_id', 'tutor_id', 'program_id', 'search', 'sort', 'date_from', 'date_to']);

        $query = ClassSchedule::with('classRoom.program', 'classRoom.branch', 'classRoom.tutor')
            ->withCount([
                'memberAttendances as present_count' => fn ($q) => $q->where('status', 'present'),
                'memberAttendances as absent_count' => fn ($q) => $q->where('status', 'absent'),
                'memberAttendances as late_count' => fn ($q) => $q->where('status', 'late'),
            ])
            ->whereHas('classRoom', fn ($q) => $q->whereIn('status', ['active', 'planned']));

        // Scope: branchandarea = filter by employee's area/branch, nasional = all
        if (! $user->hasPermissionTo('class.schedule.view.nasional') && $user->employee) {
            $emp = $user->employee;
            $query->whereHas('classRoom', function ($q) use ($emp) {
                if ($emp->branch_id) {
                    $q->where('branch_id', $emp->branch_id);
                } elseif ($emp->area_id) {
                    $q->whereHas('branch', fn ($bq) => $bq->where('areas_id', $emp->area_id));
                }
            });
        }

        // User filters
        if (! empty($filters['branch_id'])) {
            $query->whereHas('classRoom', fn ($q) => $q->where('branch_id', $filters['branch_id']));
        }
        if (! empty($filters['area_id'])) {
            $query->whereHas('classRoom.branch', fn ($q) => $q->where('areas_id', $filters['area_id']));
        }
        if (! empty($filters['tutor_id'])) {
            $query->whereHas('classRoom', fn ($q) => $q->where('tutor_id', $filters['tutor_id']));
        }
        if (! empty($filters['program_id'])) {
            $query->whereHas('classRoom', fn ($q) => $q->where('program_id', $filters['program_id']));
        }
        if (! empty($filters['search'])) {
            $query->whereHas('classRoom', fn ($q) => $q->where('class_name', 'like', '%'.$filters['search'].'%'));
        }
        if (! empty($filters['date_from'])) {
            $query->where('schedule_date', '>=', $filters['date_from']);
        }
        if (! empty($filters['date_to'])) {
            $query->where('schedule_date', '<=', $filters['date_to']);
        }

        $sort = $filters['sort'] ?? 'date_desc';
        match ($sort) {
            'date_asc' => $query->orderBy('schedule_date'),
            'class_asc' => $query->join('classes', 'class_schedules.class_id', '=', 'classes.id')->orderBy('classes.class_name')->select('class_schedules.*'),
            default => $query->orderByDesc('schedule_date'),
        };

        $schedules = $query->paginate(20)->withQueryString();

        $branches = Branch::where('is_active', true)->orderBy('branch_name')->get(['id', 'branch_name', 'areas_id']);
        $tutors = Employee::whereHas('employmentStatuses', fn ($q) => $q->whereHas('position', fn ($q) => $q->whereIn('position_name', ['Official Tutor', 'Regular Tutor'])))->orderBy('full_name')->get(['id', 'full_name']);
        $programs = Program::where('is_active', true)->orderBy('program_name')->get(['id', 'program_name']);

        return view('admin.class-schedule.index', compact('schedules', 'branches', 'tutors', 'programs', 'filters', 'sort'));
    }

    public function myClasses(): View
    {
        $this->authorize('class.attendance.record');

        $user = auth()->user();
        $employeeId = $user->employee?->id;

        $schedules = ClassSchedule::with('classRoom.program', 'classRoom.branch')
            ->withCount([
                'memberAttendances as present_count' => fn ($q) => $q->where('status', 'present'),
                'memberAttendances as absent_count' => fn ($q) => $q->where('status', 'absent'),
                'memberAttendances as late_count' => fn ($q) => $q->where('status', 'late'),
            ])
            ->whereHas('classRoom', fn ($q) => $q
                ->whereIn('status', ['active', 'planned'])
                ->where('tutor_id', $employeeId)
            )
            ->orderBy('schedule_date')
            ->paginate(20);

        return view('admin.my-classes.index', compact('schedules'));
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
