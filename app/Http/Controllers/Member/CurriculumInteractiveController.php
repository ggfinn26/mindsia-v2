<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Http\Requests\Curriculum\SubmitInteractiveAnswerRequest;
use App\Models\ClassRoom;
use App\Models\CurriculumInteractiveAnswer;
use App\Models\CurriculumInteractiveAttempt;
use App\Models\CurriculumItem;
use App\Models\MemberClass;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CurriculumInteractiveController extends Controller
{
    private function getMemberClass(ClassRoom $classroom): MemberClass
    {
        $memberDataId = auth('member')->user()->members_data_id;

        $memberClass = MemberClass::whereHas('registration', fn ($q) => $q->where('members_data_id', $memberDataId))
            ->where('class_id', $classroom->id)
            ->firstOrFail();

        return $memberClass;
    }

    public function show(ClassRoom $classroom, CurriculumItem $item): View
    {
        $memberClass = $this->getMemberClass($classroom);

        $item->load(['interactiveBlocks' => fn ($q) => $q->with([
            'questions' => fn ($q) => $q->makeVisible(['correct_option', 'explanation'])->select('id', 'block_id', 'sort_order', 'question_text', 'option_a', 'option_b', 'option_c', 'option_d'),
            'audio',
            'image',
        ])]);

        // Ambil attempt aktif atau null (belum mulai)
        $attempt = CurriculumInteractiveAttempt::where('member_class_id', $memberClass->id)
            ->where('curriculum_item_id', $item->id)
            ->whereNull('completed_at')
            ->latest()
            ->first();

        return view('curriculum.interactive.member-show', compact('classroom', 'item', 'memberClass', 'attempt'));
    }

    public function submit(SubmitInteractiveAnswerRequest $request, ClassRoom $classroom, CurriculumItem $item): RedirectResponse
    {
        $memberClass = $this->getMemberClass($classroom);

        DB::transaction(function () use ($request, $memberClass, $item) {
            $attempt = CurriculumInteractiveAttempt::firstOrCreate(
                [
                    'member_class_id' => $memberClass->id,
                    'curriculum_item_id' => $item->id,
                    'completed_at' => null,
                ],
                ['started_at' => now()],
            );

            $answers = $request->validated('answers'); // [question_id => option]
            $correct = 0;
            $total = count($answers);

            foreach ($answers as $questionId => $selectedOption) {
                $question = $item->interactiveBlocks()
                    ->with('questions')
                    ->get()
                    ->pluck('questions')
                    ->flatten()
                    ->firstWhere('id', $questionId);

                if (! $question) {
                    continue;
                }

                $isCorrect = $selectedOption && $selectedOption === $question->correct_option;
                if ($isCorrect) {
                    $correct++;
                }

                CurriculumInteractiveAnswer::updateOrCreate(
                    ['attempt_id' => $attempt->id, 'question_id' => $questionId],
                    [
                        'selected_option' => $selectedOption,
                        'is_correct' => $selectedOption ? $isCorrect : null,
                        'answered_at' => $selectedOption ? now() : null,
                    ],
                );
            }

            $score = $total > 0 ? (int) round(($correct / $total) * 100) : 0;

            $attempt->update([
                'completed_at' => now(),
                'score' => $score,
            ]);
        });

        return redirect()->route('member.curriculum.interactive.result', [$classroom, $item])
            ->with('success', 'Latihan berhasil diselesaikan!');
    }

    public function result(ClassRoom $classroom, CurriculumItem $item): View
    {
        $memberClass = $this->getMemberClass($classroom);

        $attempt = CurriculumInteractiveAttempt::where('member_class_id', $memberClass->id)
            ->where('curriculum_item_id', $item->id)
            ->whereNotNull('completed_at')
            ->latest('completed_at')
            ->with(['answers.question'])
            ->firstOrFail();

        $item->load(['interactiveBlocks.questions' => fn ($q) => $q->makeVisible(['correct_option', 'explanation']), 'interactiveBlocks.audio', 'interactiveBlocks.image']);

        return view('curriculum.interactive.member-result', compact('classroom', 'item', 'attempt'));
    }

    public function history(ClassRoom $classroom, CurriculumItem $item): View
    {
        $memberClass = $this->getMemberClass($classroom);

        $attempts = CurriculumInteractiveAttempt::where('member_class_id', $memberClass->id)
            ->where('curriculum_item_id', $item->id)
            ->whereNotNull('completed_at')
            ->latest('completed_at')
            ->get();

        return view('curriculum.interactive.member-history', compact('classroom', 'item', 'attempts'));
    }
}
