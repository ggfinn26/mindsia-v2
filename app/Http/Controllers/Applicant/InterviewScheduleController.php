<?php

namespace App\Http\Controllers\Applicant;

use App\Http\Controllers\Controller;
use App\Models\ApplicantInterviewSchedule;
use Illuminate\View\View;

class InterviewScheduleController extends Controller
{
    public function index(): View
    {
        $applicant = auth('applicant')->user()->applicantData;

        $applicationIds = $applicant->applications()->pluck('id');

        $schedules = ApplicantInterviewSchedule::whereIn('job_application_id', $applicationIds)
            ->with(['application.posting.position', 'interviewer'])
            ->orderBy('scheduled_at')
            ->paginate(15);

        return view('applicant.interviews.index', compact('schedules'));
    }
}
