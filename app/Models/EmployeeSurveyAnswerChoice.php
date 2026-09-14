<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class EmployeeSurveyAnswerChoice extends Pivot
{
    protected $table = 'employee_survey_answer_choices';

    public $incrementing = false;

    public $timestamps = false;
}
