<?php

namespace App\Repositories\Survey;

use App\Models\Survey;
use Illuminate\Database\Eloquent\Collection;

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

        $survey->update($data);

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
