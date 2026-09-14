<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Http\Requests\Survey\SubmitSurveyAnswerRequest;
use App\Models\EmployeeSurvey;
use App\Services\Survey\SurveyAnswerService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmployeeSurveyController extends Controller
{
    public function __construct(
        private readonly SurveyAnswerService $service,
    ) {}

    public function index(Request $request): View
    {
        $employee = $request->user()->employee;

        $surveys = EmployeeSurvey::where('employee_id', $employee->id)
            ->with('survey')
            ->withExists('answers')
            ->orderByDesc('created_at')
            ->get();

        return view('employee.surveys.index', compact('surveys'));
    }

    public function show(Request $request, EmployeeSurvey $employeeSurvey): View
    {
        abort_unless($employeeSurvey->employee_id === $request->user()->employee->id, 403);

        $employeeSurvey->load('survey.questions.choices');

        return view('employee.surveys.show', compact('employeeSurvey'));
    }

    public function store(SubmitSurveyAnswerRequest $request, EmployeeSurvey $employeeSurvey): RedirectResponse
    {
        abort_unless($employeeSurvey->employee_id === $request->user()->employee->id, 403);

        $this->service->submitEmployeeAnswers($employeeSurvey, $request->validated('answers'));

        return redirect()->route('employee.surveys.index')->with('success', 'Jawaban berhasil dikirim.');
    }
}
