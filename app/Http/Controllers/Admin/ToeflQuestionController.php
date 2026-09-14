<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Toefl\StoreToeflQuestionRequest;
use App\Http\Requests\Toefl\UpdateToeflQuestionRequest;
use App\Models\ToeflQuestion;
use App\Models\ToeflTest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ToeflQuestionController extends Controller
{
    public function store(StoreToeflQuestionRequest $request, ToeflTest $toeflTest): RedirectResponse
    {
        abort_unless($toeflTest->status === ToeflTest::STATUS_DRAFT, 403);

        $toeflTest->questions()->create($request->validated());

        return redirect()->route('toefl-tests.show', $toeflTest)->with('success', 'Soal ditambahkan.');
    }

    public function update(UpdateToeflQuestionRequest $request, ToeflQuestion $question): RedirectResponse
    {
        abort_unless($question->test->status === ToeflTest::STATUS_DRAFT, 403);

        $question->update($request->validated());

        return redirect()->route('toefl-tests.show', $question->toefl_test_id)->with('success', 'Soal diperbarui.');
    }

    public function toggleActive(Request $request, ToeflQuestion $question): RedirectResponse
    {
        abort_unless($request->user()->can('toefl.question.update'), 403);
        abort_unless($question->test->status === ToeflTest::STATUS_DRAFT, 403);

        $question->update(['is_active' => ! $question->is_active]);

        return redirect()->back()->with('success', 'Status soal diubah.');
    }

    public function destroy(ToeflQuestion $question): RedirectResponse
    {
        abort_unless($question->test->status === ToeflTest::STATUS_DRAFT, 403);

        $question->delete();

        return redirect()->back()->with('success', 'Soal dihapus.');
    }
}
