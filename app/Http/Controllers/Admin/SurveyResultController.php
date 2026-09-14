<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Survey;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SurveyResultController extends Controller
{
    public function show(Request $request, Survey $survey): View
    {
        abort_unless($request->user()->can('survey.result.view'), 403);

        $survey->load([
            'questions.choices',
            'memberSurveys.answers.question',
            'memberSurveys.answers.selectedChoices',
            'memberSurveys.member',
            'employeeSurveys.answers.question',
            'employeeSurveys.answers.selectedChoices',
            'employeeSurveys.employee',
        ]);

        return view('survey.results', compact('survey'));
    }
}
