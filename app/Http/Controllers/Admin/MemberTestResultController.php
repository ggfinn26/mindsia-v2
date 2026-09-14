<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ClassRoom\StoreMemberTestResultRequest;
use App\Models\ClassTest;
use App\Models\MemberClass;
use App\Models\MemberTestResult;
use App\Models\TestParticipantAnswer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MemberTestResultController extends Controller
{
    public function store(StoreMemberTestResultRequest $request, MemberClass $memberClass, ClassTest $classTest): RedirectResponse
    {
        DB::transaction(function () use ($request, $memberClass, $classTest) {
            $result = MemberTestResult::updateOrCreate(
                ['member_class_id' => $memberClass->id, 'test_id' => $classTest->id],
                ['level' => $request->validated('level'), 'final_score' => $request->validated('final_score')]
            );

            foreach ($request->validated('scores', []) as $criteriaId => $score) {
                $result->scores()->updateOrCreate(
                    ['test_scoring_criteria_id' => $criteriaId],
                    ['score' => $score]
                );
            }
        });

        return back()->with('success', 'Hasil test berhasil disimpan.');
    }

    public function reviewEssay(Request $request, MemberTestResult $result): RedirectResponse
    {
        $this->authorize('class.test.review_essay');
        $validated = $request->validate([
            'scores' => 'required|array',
            'scores.*' => 'numeric|min:0',
        ]);

        DB::transaction(function () use ($validated, $result) {
            foreach ($validated['scores'] as $answerId => $score) {
                TestParticipantAnswer::where('id', $answerId)
                    ->where('member_test_result_id', $result->id)
                    ->update(['score' => $score]);
            }

            // Recalculate total
            $totalScore = TestParticipantAnswer::where('member_test_result_id', $result->id)->sum('score');
            $result->update(['final_score' => $totalScore]);
        });

        return back()->with('success', 'Nilai esai berhasil disimpan.');
    }
}
