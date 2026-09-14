<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Http\Requests\Toefl\StartToeflSessionRequest;
use App\Http\Requests\Toefl\SubmitToeflSectionRequest;
use App\Http\Requests\Toefl\UpdateToeflAnswerRequest;
use App\Models\ToeflSession;
use App\Models\ToeflTest;
use App\Services\Toefl\ToeflSessionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ToeflSessionController extends Controller
{
    public function __construct(
        private readonly ToeflSessionService $sessionService,
    ) {}

    public function index(Request $request): View
    {
        $memberDataId = auth('member')->user()->memberData->id;

        $sessions = ToeflSession::where('members_data_id', $memberDataId)
            ->with('test')
            ->latest()
            ->paginate(10);

        return view('toefl.session.index', compact('sessions'));
    }

    public function start(StartToeflSessionRequest $request, ToeflTest $toeflTest): RedirectResponse
    {
        $member = auth('member')->user()->memberData;
        $session = $this->sessionService->startMemberSession($toeflTest, $member, $request->input('password'));

        return redirect()->route('toefl.session.listening', $session);
    }

    public function showListening(Request $request, ToeflSession $session): View
    {
        $this->sessionService->assertSessionBelongsToMember($session, auth('member')->user()->memberData->id);

        return view('toefl.session.listening', [
            'session' => $session,
            'questions' => $this->sessionService->getSectionQuestions($session, 'listening'),
            'answers' => $this->sessionService->getSectionAnswers($session, 'listening'),
            'timeLimit' => $session->test->listening_time_limit,
        ]);
    }

    public function showStructure(Request $request, ToeflSession $session): View
    {
        $this->sessionService->assertSessionBelongsToMember($session, auth('member')->user()->memberData->id);

        return view('toefl.session.structure', [
            'session' => $session,
            'questions' => $this->sessionService->getSectionQuestions($session, 'structure'),
            'answers' => $this->sessionService->getSectionAnswers($session, 'structure'),
            'timeLimit' => $session->test->structure_time_limit,
        ]);
    }

    public function showReading(Request $request, ToeflSession $session): View
    {
        $this->sessionService->assertSessionBelongsToMember($session, auth('member')->user()->memberData->id);

        return view('toefl.session.reading', [
            'session' => $session,
            'questions' => $this->sessionService->getSectionQuestions($session, 'reading'),
            'answers' => $this->sessionService->getSectionAnswers($session, 'reading'),
            'timeLimit' => $session->test->reading_time_limit,
        ]);
    }

    public function saveAnswer(UpdateToeflAnswerRequest $request, ToeflSession $session): JsonResponse
    {
        $this->sessionService->assertSessionBelongsToMember($session, auth('member')->user()->memberData->id);
        $this->sessionService->saveAnswer($session, $request->validated());

        return response()->json(['ok' => true]);
    }

    public function submitSection(SubmitToeflSectionRequest $request, ToeflSession $session): RedirectResponse
    {
        $this->sessionService->assertSessionBelongsToMember($session, auth('member')->user()->memberData->id);

        $section = $request->input('section');
        $this->sessionService->submitSection($session, $section);

        return match ($section) {
            'listening' => redirect()->route('toefl.session.structure', $session),
            'structure' => redirect()->route('toefl.session.reading', $session),
            'reading' => redirect()->route('toefl.session.result', $session),
        };
    }

    public function result(Request $request, ToeflSession $session): View
    {
        $this->sessionService->assertSessionBelongsToMember($session, auth('member')->user()->memberData->id);
        abort_unless($session->status === ToeflSession::STATUS_COMPLETED, 404);

        return view('toefl.session.result', compact('session'));
    }
}
