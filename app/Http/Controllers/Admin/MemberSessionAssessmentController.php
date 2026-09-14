<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ClassRoom\StoreMemberSessionAssessmentRequest;
use App\Http\Requests\ClassRoom\UpdateMemberSessionAssessmentRequest;
use App\Models\ClassSchedule;
use App\Models\MemberSessionAssessment;
use Illuminate\Http\RedirectResponse;

class MemberSessionAssessmentController extends Controller
{
    public function store(StoreMemberSessionAssessmentRequest $request, ClassSchedule $schedule): RedirectResponse
    {
        MemberSessionAssessment::updateOrCreate(
            ['member_class_id' => $request->validated('member_class_id'), 'class_schedule_id' => $schedule->id],
            [
                'employee_id' => auth()->user()->employee?->id,
                'score' => $request->validated('score'),
                'notes' => $request->validated('notes'),
            ]
        );

        return back()->with('success', 'Nilai sesi berhasil disimpan.');
    }

    public function update(UpdateMemberSessionAssessmentRequest $request, MemberSessionAssessment $assessment): RedirectResponse
    {
        $assessment->update($request->validated());

        return back()->with('success', 'Nilai sesi berhasil diperbarui.');
    }
}
