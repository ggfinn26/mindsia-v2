<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Http\Requests\Toefl\StartToeflSessionRequest;
use App\Http\Requests\Toefl\SubmitToeflSectionRequest;
use App\Http\Requests\Toefl\UpdateToeflAnswerRequest;
use App\Models\ToeflDirectionAudio;
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

        $availableTests = ToeflTest::where('status', ToeflTest::STATUS_PUBLISHED)
            ->orderBy('test_name')
            ->get();

        return view('toefl.session.index', compact('sessions', 'availableTests'));
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

        $directionAudios = ToeflDirectionAudio::whereIn('part', ['A', 'B', 'C'])->get()->keyBy('part');

        return view('toefl.session.listening', [
            'session' => $session,
            'questions' => $this->sessionService->getSectionQuestions($session, 'listening', markStarted: false),
            'answers' => $this->sessionService->getSectionAnswers($session, 'listening'),
            'timeLimit' => $session->test->listening_time_limit,
            'directionAudios' => $directionAudios,
        ]);
    }

    public function showStructure(Request $request, ToeflSession $session): View
    {
        $this->sessionService->assertSessionBelongsToMember($session, auth('member')->user()->memberData->id);

        return view('toefl.session.structure', [
            'session' => $session,
            'questions' => $this->sessionService->getSectionQuestions($session, 'structure', markStarted: false),
            'answers' => $this->sessionService->getSectionAnswers($session, 'structure'),
            'timeLimit' => $session->test->structure_time_limit,
        ]);
    }

    public function showReading(Request $request, ToeflSession $session): View
    {
        $this->sessionService->assertSessionBelongsToMember($session, auth('member')->user()->memberData->id);

        return view('toefl.session.reading', [
            'session' => $session,
            'questions' => $this->sessionService->getSectionQuestions($session, 'reading', markStarted: false),
            'answers' => $this->sessionService->getSectionAnswers($session, 'reading'),
            'timeLimit' => $session->test->reading_time_limit,
        ]);
    }

    public function markReadingStarted(ToeflSession $session): JsonResponse
    {
        $this->sessionService->assertSessionBelongsToMember($session, auth('member')->user()->memberData->id);
        $this->sessionService->markSectionStarted($session, 'reading');
        $session->refresh();

        return response()->json(['started_at' => $session->reading_started_at?->timestamp]);
    }

    public function markStructureStarted(ToeflSession $session): JsonResponse
    {
        $this->sessionService->assertSessionBelongsToMember($session, auth('member')->user()->memberData->id);
        $this->sessionService->markSectionStarted($session, 'structure');
        $session->refresh();

        return response()->json(['started_at' => $session->structure_started_at?->timestamp]);
    }

    public function markListeningStarted(ToeflSession $session): JsonResponse
    {
        $this->sessionService->assertSessionBelongsToMember($session, auth('member')->user()->memberData->id);
        $this->sessionService->markSectionStarted($session, 'listening');
        $session->refresh();

        return response()->json(['started_at' => $session->listening_started_at?->timestamp]);
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

        $session->load(['test', 'answers.question:id,section']);

        $rawScores = $session->answers
            ->groupBy(fn ($a) => $a->question->section)
            ->map(fn ($group) => [
                'correct' => $group->where('is_correct', true)->count(),
                'total' => $group->count(),
            ]);

        return view('toefl.session.result', compact('session', 'rawScores'));
    }
}
