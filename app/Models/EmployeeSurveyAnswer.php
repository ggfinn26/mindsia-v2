<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class EmployeeSurveyAnswer extends Model
{
    protected $fillable = [
        'employee_survey_id',
        'survey_question_id',
        'answer_text',
        'answer_value',
    ];

    protected $casts = [
        'answer_value' => 'integer',
    ];

    public function employeeSurvey(): BelongsTo
    {
        return $this->belongsTo(EmployeeSurvey::class);
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(SurveyQuestion::class, 'survey_question_id');
    }

    public function selectedChoices(): BelongsToMany
    {
        return $this->belongsToMany(
            SurveyQuestionChoice::class,
            'employee_survey_answer_choices',
            'employee_survey_answer_id',
            'survey_question_choice_id'
        );
    }
}
