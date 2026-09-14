<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestParticipantAnswer extends Model
{
    protected $fillable = [
        'member_test_result_id',
        'test_question_id',
        'test_choice_id',
        'essay_answer_text',
        'score',
    ];

    public function testResult()
    {
        return $this->belongsTo(MemberTestResult::class, 'member_test_result_id');
    }

    public function question()
    {
        return $this->belongsTo(TestQuestion::class, 'test_question_id');
    }

    public function choice()
    {
        return $this->belongsTo(TestChoice::class, 'test_choice_id');
    }
}
