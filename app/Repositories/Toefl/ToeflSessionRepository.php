<?php

namespace App\Repositories\Toefl;

use App\Models\ToeflAnswer;
use App\Models\ToeflSession;
use App\Models\ToeflTest;

class ToeflSessionRepository
{
    public function createMemberSession(ToeflTest $test, int $memberDataId): ToeflSession
    {
        return ToeflSession::create([
            'members_data_id' => $memberDataId,
            'toefl_test_id' => $test->id,
            'status' => ToeflSession::STATUS_IN_PROGRESS,
            'started_at' => now(),
        ]);
    }

    public function createGuestSession(ToeflTest $test, array $guestData): ToeflSession
    {
        return ToeflSession::create(array_merge($guestData, [
            'members_data_id' => null,
            'toefl_test_id' => $test->id,
            'status' => ToeflSession::STATUS_IN_PROGRESS,
            'started_at' => now(),
        ]));
    }

    public function seedAnswers(ToeflSession $session): void
    {
        $questionIds = $session->test->questions()
            ->where('is_active', true)
            ->pluck('id');

        $now = now();
        $rows = $questionIds->map(fn ($qId) => [
            'toefl_session_id' => $session->id,
            'toefl_question_id' => $qId,
            'selected_option' => null,
            'is_correct' => null,
            'answered_at' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ])->toArray();

        ToeflAnswer::insert($rows);
    }

    public function markSectionStarted(ToeflSession $session, string $section): void
    {
        $field = "{$section}_started_at";
        if ($session->$field === null) {
            $session->update([$field => now()]);
        }
    }

    public function saveAnswer(ToeflSession $session, int $questionId, string $selectedOption): void
    {
        ToeflAnswer::where('toefl_session_id', $session->id)
            ->where('toefl_question_id', $questionId)
            ->update([
                'selected_option' => $selectedOption,
                'answered_at' => now(),
            ]);
    }

    public function gradeSection(ToeflSession $session, string $section): int
    {
        $answers = ToeflAnswer::where('toefl_session_id', $session->id)
            ->whereHas('question', fn ($q) => $q->where('section', $section))
            ->with('question:id,correct_option')
            ->get();

        $correct = 0;

        foreach ($answers as $answer) {
            $isCorrect = $answer->selected_option === $answer->question->correct_option;
            $answer->update(['is_correct' => $isCorrect]);
            if ($isCorrect) {
                $correct++;
            }
        }

        return $correct;
    }

    public function submitSection(ToeflSession $session, string $section, int $scaledScore): void
    {
        $session->update([
            "{$section}_submitted_at" => now(),
            "score_{$section}" => $scaledScore,
        ]);
    }

    public function finalize(ToeflSession $session, int $totalScore): void
    {
        $session->update([
            'score_total' => $totalScore,
            'status' => ToeflSession::STATUS_COMPLETED,
            'completed_at' => now(),
        ]);
    }
}
