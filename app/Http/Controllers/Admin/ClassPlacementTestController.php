<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
use App\Models\MemberClass;
use App\Models\PlacementTest;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClassPlacementTestController extends Controller
{
    public function show(ClassRoom $classroom): View
    {
        $this->authorize('class.placement_test.manage');

        $classroom->load(['memberClasses.memberRegistration.memberData']);

        $tests = PlacementTest::where('class_id', $classroom->id)
            ->get()
            ->keyBy('member_data_id');

        return view('admin.classrooms.placement-test.show', compact('classroom', 'tests'));
    }

    public function schedule(Request $request, ClassRoom $classroom): RedirectResponse
    {
        $this->authorize('class.placement_test.manage');

        $request->validate([
            'pre_test_date' => ['required', 'date'],
        ]);

        $classroom->update(['pre_test_date' => $request->pre_test_date]);

        return back()->with('success', 'Pre-test dijadwalkan '.Carbon::parse($request->pre_test_date)->translatedFormat('d F Y').'.');
    }

    public function store(Request $request, ClassRoom $classroom): RedirectResponse
    {
        $this->authorize('class.placement_test.manage');

        $request->validate([
            'tests' => ['required', 'array'],
            'tests.*.score_writing' => ['nullable', 'integer', 'min:0', 'max:100'],
            'tests.*.score_speaking' => ['nullable', 'integer', 'min:0', 'max:100'],
            'tests.*.attended_writing' => ['nullable', 'boolean'],
            'tests.*.attended_speaking' => ['nullable', 'boolean'],
        ]);

        $allowedMemberDataIds = MemberClass::where('class_id', $classroom->id)
            ->join('members_registration', 'member_class.member_registration_id', '=', 'members_registration.id')
            ->pluck('members_registration.members_data_id')
            ->all();

        foreach ($request->input('tests', []) as $memberDataId => $data) {
            if (! in_array((int) $memberDataId, $allowedMemberDataIds, true)) {
                continue;
            }

            $attendedWriting = (bool) ($data['attended_writing'] ?? false);
            $attendedSpeaking = (bool) ($data['attended_speaking'] ?? false);
            $scoreWriting = $attendedWriting ? (int) ($data['score_writing'] ?? 0) : 0;
            $scoreSpeaking = $attendedSpeaking ? (int) ($data['score_speaking'] ?? 0) : 0;

            $divisor = ($attendedWriting ? 1 : 0) + ($attendedSpeaking ? 1 : 0);
            $finalScore = $divisor > 0 ? ($scoreWriting + $scoreSpeaking) / $divisor : 0;

            PlacementTest::updateOrCreate(
                ['class_id' => $classroom->id, 'member_data_id' => (int) $memberDataId],
                [
                    'attended_writing' => $attendedWriting,
                    'attended_speaking' => $attendedSpeaking,
                    'score_writing' => $scoreWriting,
                    'score_speaking' => $scoreSpeaking,
                    'final_score' => $finalScore,
                    'level' => PlacementTest::computeLevel($finalScore),
                    'is_scheduled' => true,
                    'recorded_by' => auth()->id(),
                ]
            );
        }

        return redirect()
            ->route('classrooms.placement-test.show', $classroom)
            ->with('success', 'Hasil pre-test berhasil disimpan.');
    }
}
