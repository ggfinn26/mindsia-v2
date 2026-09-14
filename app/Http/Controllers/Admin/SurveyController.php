<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Survey\StoreSurveyRequest;
use App\Http\Requests\Survey\UpdateSurveyRequest;
use App\Models\Survey;
use App\Repositories\Survey\SurveyRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SurveyController extends Controller
{
    public function __construct(
        private readonly SurveyRepository $repository,
    ) {}

    public function index(Request $request): View
    {
        abort_unless($request->user()->can('survey.form.view'), 403);

        return view('survey.index', [
            'surveys' => $this->repository->all(),
        ]);
    }

    public function create(): View
    {
        return view('survey.create');
    }

    public function store(StoreSurveyRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $survey = DB::transaction(function () use ($validated) {
            $survey = $this->repository->create([
                'survey_name' => $validated['survey_name'],
                'survey_description' => $validated['survey_description'] ?? null,
                'deadline_at' => $validated['deadline_at'] ?? null,
            ]);

            foreach ($validated['questions'] as $qData) {
                $question = $survey->questions()->create([
                    'question_text' => $qData['question_text'],
                    'question_type' => $qData['question_type'],
                    'scale_min' => $qData['scale_min'] ?? null,
                    'scale_max' => $qData['scale_max'] ?? null,
                    'scale_min_label' => $qData['scale_min_label'] ?? null,
                    'scale_max_label' => $qData['scale_max_label'] ?? null,
                ]);

                foreach ($qData['choices'] ?? [] as $choiceData) {
                    $question->choices()->create(['choice_text' => $choiceData['choice_text']]);
                }
            }

            return $survey;
        });

        return redirect()->route('surveys.show', $survey)->with('success', 'Survey berhasil dibuat.');
    }

    public function show(Request $request, Survey $survey): View
    {
        abort_unless($request->user()->can('survey.form.view'), 403);

        return view('survey.show', [
            'survey' => $this->repository->findWithDetails($survey->id),
        ]);
    }

    public function edit(Request $request, Survey $survey): View
    {
        abort_unless($request->user()->can('survey.form.update'), 403);

        return view('survey.edit', [
            'survey' => $survey->load('questions.choices'),
        ]);
    }

    public function update(UpdateSurveyRequest $request, Survey $survey): RedirectResponse
    {
        $this->repository->update($survey, $request->validated());

        return redirect()->route('surveys.show', $survey)->with('success', 'Survey berhasil diperbarui.');
    }

    public function destroy(Request $request, Survey $survey): RedirectResponse
    {
        abort_unless($request->user()->can('survey.form.delete'), 403);

        $this->repository->delete($survey);

        return redirect()->route('surveys.index')->with('success', 'Survey berhasil dihapus.');
    }
}
