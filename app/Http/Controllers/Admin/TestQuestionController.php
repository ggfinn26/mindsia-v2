<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassTest;
use App\Models\TestQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TestQuestionController extends Controller
{
    public function store(Request $request, ClassTest $test)
    {
        $this->authorize('class.test.create');

        $validated = $request->validate([
            'type' => 'required|in:mc,essay',
            'question_text' => 'required|string',
            'weight' => 'required|integer|min:1',
            'order' => 'required|integer|min:1',
            'choices' => 'required_if:type,mc|array',
            'choices.*.text' => 'required_if:type,mc|string',
            'choices.*.is_correct' => 'boolean',
        ]);

        DB::transaction(function () use ($validated, $test) {
            $question = $test->questions()->create([
                'type' => $validated['type'],
                'question_text' => $validated['question_text'],
                'weight' => $validated['weight'],
                'order' => $validated['order'],
            ]);

            if ($validated['type'] === 'mc' && ! empty($validated['choices'])) {
                foreach ($validated['choices'] as $choice) {
                    $question->choices()->create([
                        'choice_text' => $choice['text'],
                        'is_correct' => $choice['is_correct'] ?? false,
                    ]);
                }
            }
        });

        return redirect()->back()->with('success', 'Soal berhasil ditambahkan.');
    }

    public function destroy(TestQuestion $question)
    {
        $this->authorize('class.test.delete');
        $question->delete();

        return redirect()->back()->with('success', 'Soal berhasil dihapus.');
    }
}
