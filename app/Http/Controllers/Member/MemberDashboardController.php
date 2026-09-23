<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\MemberClass;
use App\Models\Program;
use App\Models\Province;
use Illuminate\View\View;

class MemberDashboardController extends Controller
{
    public function index(): View
    {
        $member = auth('member')->user();
        $memberDataId = $member->members_data_id;

        $activeClasses = MemberClass::with([
            'classRoom.program',
            'classRoom.schedules',
            'attendances',
            'curriculumProgress',
        ])
            ->withCount([
                'curriculumProgress as completed_items_count' => fn ($q) => $q->where('status', 'completed'),
            ])
            ->whereHas('memberRegistration', fn ($q) => $q->where('members_data_id', $memberDataId))
            ->where('status', 'active')
            ->get()
            ->map(function (MemberClass $mc) {
                $attendances = $mc->attendances;
                $totalAttended = $attendances->whereIn('status', ['present', 'late'])->count();
                $totalAbsent = $attendances->whereIn('status', ['absent', 'excused', 'sick'])->count();
                $totalSessions = $attendances->count();

                // Total curriculum items: sum through program → curriculum → sessions → items
                $totalItems = optional($mc->classRoom->program->curriculum)
                    ->sessions()
                    ->withCount('items')
                    ->get()
                    ->sum('items_count');

                $mc->attendance_summary = [
                    'total' => $totalSessions,
                    'attended' => $totalAttended,
                    'absent' => $totalAbsent,
                    'rate' => $totalSessions > 0 ? round($totalAttended / $totalSessions * 100) : null,
                ];

                $mc->progress_summary = [
                    'completed' => $mc->completed_items_count,
                    'total' => $totalItems,
                    'rate' => $totalItems > 0 ? round($mc->completed_items_count / $totalItems * 100) : null,
                ];

                return $mc;
            });

        $availablePrograms = Program::where('is_active', true)->take(3)->get();
        $provinces = Province::orderBy('name')->get(['id', 'name']);

        return view('member.dashboard', [
            'member' => $member,
            'memberData' => $member->memberData,
            'activeClasses' => $activeClasses,
            'availablePrograms' => $availablePrograms,
            'provinces' => $provinces,
        ]);
    }
}
