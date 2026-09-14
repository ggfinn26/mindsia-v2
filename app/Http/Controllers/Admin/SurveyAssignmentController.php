<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Survey\AssignSurveyRequest;
use App\Models\Survey;
use App\Services\Survey\SurveyAssignmentService;
use Illuminate\Http\RedirectResponse;

class SurveyAssignmentController extends Controller
{
    public function __construct(
        private readonly SurveyAssignmentService $service,
    ) {}

    public function assignMembers(AssignSurveyRequest $request, Survey $survey): RedirectResponse
    {
        $this->service->assignToMembers($survey, $request->validated('member_ids'));

        return redirect()->route('surveys.show', $survey)->with('success', 'Survey berhasil di-assign ke member.');
    }

    public function assignEmployees(AssignSurveyRequest $request, Survey $survey): RedirectResponse
    {
        $this->service->assignToEmployees($survey, $request->validated('employee_ids'));

        return redirect()->route('surveys.show', $survey)->with('success', 'Survey berhasil di-assign ke employee.');
    }
}
