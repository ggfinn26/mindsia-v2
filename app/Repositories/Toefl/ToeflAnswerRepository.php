<?php

namespace App\Repositories\Toefl;

use App\Models\ToeflAnswer;
use App\Models\ToeflSession;
use Illuminate\Database\Eloquent\Collection;

class ToeflAnswerRepository
{
    public function getSectionAnswers(ToeflSession $session, string $section): Collection
    {
        return ToeflAnswer::where('toefl_session_id', $session->id)
            ->whereHas('question', fn ($q) => $q->where('section', $section))
            ->with('question')
            ->get()
            ->keyBy('toefl_question_id');
    }
}
