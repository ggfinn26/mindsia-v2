<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\ClassTest;
use App\Models\MemberClass;
use App\Models\MemberTestResult;
use App\Models\TestParticipantAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OnlineTestController extends Controller
{
    // The link might contain a generated token or just class_id and test_id. For now, simple endpoints.
    public function show(ClassTest $test, Request $request)
    {
        $memberClass = MemberClass::where('member_id', auth('member')->id())
            ->where('class_id', $test->class_id)
            ->firstOrFail();

        $test->load(['questions.choices' => function ($q) {
            $q->select('id', 'test_question_id', 'choice_text'); // hide is_correct
        }]);

        return view('public.online-test.show', compact('test', 'memberClass'));
    }

    public function submit(Request $request, ClassTest $test)
    {
        $memberClass = MemberClass::where('member_id', auth('member')->id())
            ->where('class_id', $test->class_id)
            ->firstOrFail();

        // 1. Create or get MemberTestResult
        $result = MemberTestResult::firstOrCreate([
            'member_class_id' => $memberClass->id,
            'test_id' => $test->id,
        ], [
            'level' => 'TBD',
            'final_score' => 0,
        ]);

        $answers = $request->input('answers', []);

        DB::transaction(function () use ($test, $result, $answers) {
            foreach ($test->questions as $q) {
                $ans = $answers[$q->id] ?? null;
                $score = 0;
                $choiceId = null;
                $essayText = null;

                if ($q->type === 'mc' && $ans) {
                    $choiceId = $ans;
                    $isCorrect = $q->choices()->where('id', $choiceId)->value('is_correct');
                    if ($isCorrect) {
                        $score = $q->weight;
                    }
                } elseif ($q->type === 'essay') {
                    $essayText = $ans;
                }

                TestParticipantAnswer::updateOrCreate(
                    [
                        'member_test_result_id' => $result->id,
                        'test_question_id' => $q->id,
                    ],
                    [
                        'test_choice_id' => $choiceId,
                        'essay_answer_text' => $essayText,
                        'score' => $score,
                    ]
                );
            }

            // Recalculate auto-score for MC
            $totalScore = TestParticipantAnswer::where('member_test_result_id', $result->id)->sum('score');
            $result->update(['final_score' => $totalScore]);
        });

        return redirect()->route('member.dashboard')->with('success', 'Ujian berhasil diselesaikan.');
    }
}
