<?php

namespace App\Http\Requests\Survey;

use App\Models\SurveyQuestion;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class SubmitSurveyAnswerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('member')->check() || $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'answers' => ['required', 'array', 'min:1'],
            'answers.*.question_id' => ['required', 'integer', 'exists:survey_questions,id'],
            'answers.*.answer_text' => ['nullable', 'string'],
            'answers.*.answer_value' => ['nullable', 'integer'],
            'answers.*.choice_ids' => ['nullable', 'array'],
            'answers.*.choice_ids.*' => ['integer', 'exists:survey_question_choices,id'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v) {
            $answers = $this->input('answers', []);

            // resolve survey_id from route binding (MemberSurvey or EmployeeSurvey)
            $assignment = $this->route('memberSurvey') ?? $this->route('employeeSurvey');
            $surveyId = $assignment?->survey_id;

            $questions = SurveyQuestion::whereIn('id', collect($answers)->pluck('question_id'))
                ->when($surveyId, fn ($q) => $q->where('survey_id', $surveyId))
                ->get()
                ->keyBy('id');

            foreach ($answers as $i => $answer) {
                $question = $questions[$answer['question_id']] ?? null;
                if (! $question) {
                    continue;
                }

                match ($question->question_type) {
                    'text' => empty($answer['answer_text'])
                        ? $v->errors()->add("answers.$i.answer_text", 'Jawaban teks wajib diisi.')
                        : null,
                    'scale' => (! isset($answer['answer_value'])
                        || $answer['answer_value'] < $question->scale_min
                        || $answer['answer_value'] > $question->scale_max)
                        ? $v->errors()->add("answers.$i.answer_value", "Nilai harus antara {$question->scale_min}–{$question->scale_max}.")
                        : null,
                    'single_choice' => (count($answer['choice_ids'] ?? []) !== 1)
                        ? $v->errors()->add("answers.$i.choice_ids", 'Pilih tepat satu jawaban.')
                        : null,
                    'multiple_choice' => (empty($answer['choice_ids']))
                        ? $v->errors()->add("answers.$i.choice_ids", 'Pilih minimal satu jawaban.')
                        : null,
                };

                // choice_ids must belong to this question (IDOR guard)
                if (! empty($answer['choice_ids'])) {
                    $validChoiceIds = $question->choices()->pluck('id')->toArray();
                    $invalid = array_diff($answer['choice_ids'], $validChoiceIds);
                    if ($invalid) {
                        $v->errors()->add("answers.$i.choice_ids", 'Pilihan tidak valid untuk pertanyaan ini.');
                    }
                }

                // question_id must belong to this survey
                if ($surveyId && $question->survey_id !== $surveyId) {
                    $v->errors()->add("answers.$i.question_id", 'Soal tidak termasuk dalam survey ini.');
                }
            }
        });
    }
}
