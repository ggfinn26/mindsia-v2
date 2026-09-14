<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Survey extends Model
{
    protected $fillable = [
        'survey_name',
        'survey_description',
        'deadline_at',
    ];

    protected $casts = [
        'deadline_at' => 'datetime',
    ];

    public function questions(): HasMany
    {
        return $this->hasMany(SurveyQuestion::class)->orderBy('id');
    }

    public function memberSurveys(): HasMany
    {
        return $this->hasMany(MemberSurvey::class);
    }

    public function employeeSurveys(): HasMany
    {
        return $this->hasMany(EmployeeSurvey::class);
    }

    public function isExpired(): bool
    {
        return $this->deadline_at !== null && $this->deadline_at->isPast();
    }
}
