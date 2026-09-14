<?php

namespace App\Services\Survey;

use App\Models\EmployeeSurvey;
use App\Models\EmployeeSurveyAnswer;
use App\Models\MemberSurvey;
use App\Models\MemberSurveyAnswer;
use Illuminate\Support\Facades\DB;

class SurveyAnswerService
{
    public function submitMemberAnswers(MemberSurvey $memberSurvey, array $answers): void
    {
        if ($memberSurvey->isSubmitted()) {
            throw new \RuntimeException('Survey sudah dijawab.');
        }

        if ($memberSurvey->survey->isExpired()) {
            throw new \RuntimeException('Deadline survey sudah lewat.');
        }

        DB::transaction(function () use ($memberSurvey, $answers) {
            foreach ($answers as $answerData) {
                $answer = MemberSurveyAnswer::create([
                    'member_survey_id' => $memberSurvey->id,
                    'survey_question_id' => $answerData['question_id'],
                    'answer_text' => $answerData['answer_text'] ?? null,
                    'answer_value' => $answerData['answer_value'] ?? null,
                ]);

                if (! empty($answerData['choice_ids'])) {
                    $answer->selectedChoices()->attach($answerData['choice_ids']);
                }
            }
        });
    }

    public function submitEmployeeAnswers(EmployeeSurvey $employeeSurvey, array $answers): void
    {
        if ($employeeSurvey->isSubmitted()) {
            throw new \RuntimeException('Survey sudah dijawab.');
        }

        if ($employeeSurvey->survey->isExpired()) {
            throw new \RuntimeException('Deadline survey sudah lewat.');
        }

        DB::transaction(function () use ($employeeSurvey, $answers) {
            foreach ($answers as $answerData) {
                $answer = EmployeeSurveyAnswer::create([
                    'employee_survey_id' => $employeeSurvey->id,
                    'survey_question_id' => $answerData['question_id'],
                    'answer_text' => $answerData['answer_text'] ?? null,
                    'answer_value' => $answerData['answer_value'] ?? null,
                ]);

                if (! empty($answerData['choice_ids'])) {
                    $answer->selectedChoices()->attach($answerData['choice_ids']);
                }
            }
        });
    }
}
