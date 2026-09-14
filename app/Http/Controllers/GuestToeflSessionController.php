<?php

namespace App\Http\Controllers;

use App\Http\Requests\Toefl\StartGuestToeflSessionRequest;
use App\Http\Requests\Toefl\SubmitToeflSectionRequest;
use App\Http\Requests\Toefl\UpdateToeflAnswerRequest;
use App\Models\ToeflSession;
use App\Models\ToeflTest;
use App\Services\Toefl\ToeflSessionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class GuestToeflSessionController extends Controller
{
    public function __construct(
        private readonly ToeflSessionService $sessionService,
    ) {}

    public function showEntry(ToeflTest $toeflTest): View
    {
        abort_unless($toeflTest->is_trial && $toeflTest->status === ToeflTest::STATUS_PUBLISHED, 404);

        return view('toefl.guest.entry', compact('toeflTest'));
    }

    public function start(StartGuestToeflSessionRequest $request, ToeflTest $toeflTest): RedirectResponse
    {
        abort_unless($toeflTest->is_trial && $toeflTest->status === ToeflTest::STATUS_PUBLISHED, 404);

        $session = $this->sessionService->startGuestSession($toeflTest, $request->validated());
        session(['guest_toefl_session_id' => $session->id]);

        return redirect()->route('toefl.guest.listening', $session);
    }

    public function showListening(ToeflSession $session): View
    {
        $this->sessionService->assertGuestSession($session);

        return view('toefl.guest.listening', [
            'session' => $session,
            'questions' => $this->sessionService->getSectionQuestions($session, 'listening'),
            'answers' => $this->sessionService->getSectionAnswers($session, 'listening'),
            'timeLimit' => $session->test->listening_time_limit,
        ]);
    }

    public function showStructure(ToeflSession $session): View
    {
        $this->sessionService->assertGuestSession($session);

        return view('toefl.guest.structure', [
            'session' => $session,
            'questions' => $this->sessionService->getSectionQuestions($session, 'structure'),
            'answers' => $this->sessionService->getSectionAnswers($session, 'structure'),
            'timeLimit' => $session->test->structure_time_limit,
        ]);
    }

    public function showReading(ToeflSession $session): View
    {
        $this->sessionService->assertGuestSession($session);

        return view('toefl.guest.reading', [
            'session' => $session,
            'questions' => $this->sessionService->getSectionQuestions($session, 'reading'),
            'answers' => $this->sessionService->getSectionAnswers($session, 'reading'),
            'timeLimit' => $session->test->reading_time_limit,
        ]);
    }

    public function saveAnswer(UpdateToeflAnswerRequest $request, ToeflSession $session): JsonResponse
    {
        $this->sessionService->assertGuestSession($session);
        $this->sessionService->saveAnswer($session, $request->validated());

        return response()->json(['ok' => true]);
    }

    public function submitSection(SubmitToeflSectionRequest $request, ToeflSession $session): RedirectResponse
    {
        $this->sessionService->assertGuestSession($session);

        $section = $request->input('section');
        $this->sessionService->submitSection($session, $section);

        return match ($section) {
            'listening' => redirect()->route('toefl.guest.structure', $session),
            'structure' => redirect()->route('toefl.guest.reading', $session),
            'reading' => redirect()->route('toefl.guest.result', $session),
        };
    }

    public function result(ToeflSession $session): View
    {
        $this->sessionService->assertGuestSession($session);
        abort_unless($session->status === ToeflSession::STATUS_COMPLETED, 404);

        return view('toefl.guest.result', compact('session'));
    }
}
