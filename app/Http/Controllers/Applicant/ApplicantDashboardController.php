<?php

namespace App\Http\Controllers\Applicant;

use App\Http\Controllers\Controller;
use App\Models\JobApplication;
use Illuminate\View\View;

class ApplicantDashboardController extends Controller
{
    public function index(): View
    {
        $applicant = auth('applicant')->user();
        $applicantData = $applicant->applicantData;

        $applications = $applicantData
            ? JobApplication::with(['posting'])
                ->where('applicant_id', $applicantData->id)
                ->latest('applied_at')
                ->get()
            : collect();

        return view('applicant.dashboard', [
            'applicant' => $applicant,
            'applicantData' => $applicantData,
            'applications' => $applications,
        ]);
    }
}
