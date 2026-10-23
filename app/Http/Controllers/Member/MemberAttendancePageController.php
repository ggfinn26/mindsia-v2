<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\MemberClass;
use Carbon\Carbon;
use Illuminate\View\View;

class MemberAttendancePageController extends Controller
{
    public function index(): View
    {
        $memberDataId = auth('member')->user()->members_data_id;

        $classes = MemberClass::with([
            'classRoom.program',
            'classRoom.tutor',
            'classRoom.schedules' => fn ($q) => $q->orderBy('schedule_date')->orderBy('start_time'),
            'attendances',
            'sessionAssessments',
        ])
            ->whereHas('memberRegistration', fn ($q) => $q->where('members_data_id', $memberDataId))
            ->where('status', 'active')
            ->get()
            ->map(function (MemberClass $mc) {
                $attendanceMap = $mc->attendances->keyBy('class_schedule_id');
                $assessmentMap = $mc->sessionAssessments->keyBy('class_schedule_id');
                $today = Carbon::today();

                $sessions = $mc->classRoom->schedules->map(function ($schedule) use ($attendanceMap, $assessmentMap, $today) {
                    $record = $attendanceMap->get($schedule->id);
                    $assessment = $assessmentMap->get($schedule->id);
                    $isPast = $schedule->schedule_date->lte($today);

                    return [
                        'date' => $schedule->schedule_date,
                        'time' => Carbon::parse($schedule->start_time)->format('H:i')
                                      .' - '.Carbon::parse($schedule->end_time)->format('H:i'),
                        'material' => $schedule->material_taught,
                        'status' => $record?->status ?? ($isPast ? 'unrecorded' : 'scheduled'),
                        'notes' => $record?->notes,
                        'score' => $assessment?->score,
                    ];
                });

                $past = $sessions->filter(fn ($s) => $s['status'] !== 'scheduled');
                $recorded = $past->filter(fn ($s) => $s['status'] !== 'unrecorded');
                $attended = $recorded->whereIn('status', ['present', 'late'])->count();
                $absent = $recorded->whereIn('status', ['absent', 'excused', 'sick'])->count();
                $total = $recorded->count();

                return [
                    'class_name' => $mc->classRoom->class_name,
                    'program_name' => $mc->classRoom->program->program_name ?? '-',
                    'tutor_name' => optional($mc->classRoom->tutor)->full_name ?? 'Belum Ditentukan',
                    'sessions' => $sessions,
                    'summary' => [
                        'total' => $total,
                        'attended' => $attended,
                        'absent' => $absent,
                        'rate' => $total > 0 ? round($attended / $total * 100) : null,
                    ],
                ];
            });

        return view('member.attendance.index', compact('classes'));
    }
}
