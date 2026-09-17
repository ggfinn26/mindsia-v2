<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Http\Requests\Survey\SubmitSurveyAnswerRequest;
use App\Models\MemberSurvey;
use App\Services\Survey\SurveyAnswerService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MemberSurveyController extends Controller
{
    public function __construct(
        private readonly SurveyAnswerService $service,
    ) {}

    public function index(Request $request): View
    {
        $member = auth('member')->user();

        $surveys = MemberSurvey::where('member_id', $member->id)
            ->with('survey')
            ->withExists('answers')
            ->orderByDesc('created_at')
            ->get();

        return view('member.surveys.index', compact('surveys'));
    }

    public function show(Request $request, MemberSurvey $memberSurvey): View
    {
        abort_unless($memberSurvey->member_id === auth('member')->id(), 403);

        $memberSurvey->load('survey.questions.choices');

        return view('member.surveys.show', compact('memberSurvey'));
    }

    public function store(SubmitSurveyAnswerRequest $request, MemberSurvey $memberSurvey): RedirectResponse
    {
        abort_unless($memberSurvey->member_id === auth('member')->id(), 403);

        try {
            $this->service->submitMemberAnswers($memberSurvey, $request->validated('answers'));
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('member.surveys.index')->with('success', 'Jawaban berhasil dikirim.');
    }
}
