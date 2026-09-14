<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ClassRoom\StoreMemberAttendanceRequest;
use App\Http\Requests\ClassRoom\UpdateMemberAttendanceRequest;
use App\Models\ClassSchedule;
use App\Models\MemberAttendance;
use App\Models\MemberClass;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class MemberAttendanceController extends Controller
{
    public function store(StoreMemberAttendanceRequest $request, ClassSchedule $schedule): RedirectResponse
    {
        abort_if(
            $schedule->schedule_date > today(),
            422,
            'Absensi tidak bisa dicatat sebelum tanggal sesi.'
        );

        $memberClassIds = collect($request->validated('attendances'))->pluck('member_class_id')->unique()->values();
        $validCount = MemberClass::where('class_id', $schedule->class_id)->whereIn('id', $memberClassIds)->count();
        abort_unless($validCount === $memberClassIds->count(), 403, 'member_class_id tidak valid untuk kelas ini.');

        $employeeId = auth()->user()->employee?->id;

        DB::transaction(function () use ($request, $schedule, $employeeId) {
            foreach ($request->validated('attendances') as $row) {
                MemberAttendance::updateOrCreate(
                    ['member_class_id' => $row['member_class_id'], 'class_schedule_id' => $schedule->id],
                    [
                        'status' => $row['status'],
                        'notes' => $row['notes'] ?? null,
                        'recorded_by_employee_id' => $employeeId,
                    ]
                );
            }
        });

        return back()->with('success', 'Absensi berhasil disimpan.');
    }

    public function update(UpdateMemberAttendanceRequest $request, MemberAttendance $memberAttendance): RedirectResponse
    {
        $memberAttendance->update($request->validated());

        return back()->with('success', 'Absensi berhasil diperbarui.');
    }
}
