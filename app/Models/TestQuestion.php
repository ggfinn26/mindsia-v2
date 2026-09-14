<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestQuestion extends Model
{
    protected $fillable = [
        'test_id',
        'type',
        'question_text',
        'weight',
        'order',
    ];

    public function test()
    {
        return $this->belongsTo(ClassTest::class, 'test_id');
    }

    public function choices()
    {
        return $this->hasMany(TestChoice::class);
    }
}
