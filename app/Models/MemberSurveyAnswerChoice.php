<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class MemberSurveyAnswerChoice extends Pivot
{
    protected $table = 'member_survey_answer_choices';

    public $incrementing = false;

    public $timestamps = false;
}
