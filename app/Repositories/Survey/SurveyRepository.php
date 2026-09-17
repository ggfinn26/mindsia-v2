<?php

namespace App\Repositories\Survey;

use App\Models\Survey;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class SurveyRepository
{
    public function all(): Collection
    {
        return Survey::withCount(['memberSurveys', 'employeeSurveys'])
            ->orderByDesc('created_at')
            ->get();
    }

    public function findWithDetails(int $id): Survey
    {
        return Survey::with([
            'questions.choices',
            'memberSurveys.member',
            'employeeSurveys.employee',
        ])->findOrFail($id);
    }

    public function create(array $data): Survey
    {
        return Survey::create($data);
    }

    public function update(Survey $survey, array $data): Survey
    {
        $hasAnswers = $survey->memberSurveys()->whereHas('answers')->exists()
            || $survey->employeeSurveys()->whereHas('answers')->exists();

        if ($hasAnswers) {
            throw new \RuntimeException('Tidak bisa edit survey yang sudah ada jawaban.');
        }

        DB::transaction(function () use ($survey, $data) {
            $survey->update([
                'survey_name' => $data['survey_name'],
                'survey_description' => $data['survey_description'] ?? null,
                'deadline_at' => $data['deadline_at'] ?? null,
            ]);

            // Delete existing questions and choices, then recreate
            $survey->questions()->each(fn ($q) => $q->choices()->delete());
            $survey->questions()->delete();

            foreach ($data['questions'] as $qData) {
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
        });

        return $survey;
    }

    public function delete(Survey $survey): void
    {
        if ($survey->memberSurveys()->exists() || $survey->employeeSurveys()->exists()) {
            throw new \RuntimeException('Tidak bisa hapus survey yang sudah di-assign.');
        }

        $survey->delete();
    }
}
