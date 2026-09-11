<?php

namespace App\Http\Controllers\Applicant;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class ApplicantDashboardController extends Controller
{
    public function index(): View
    {
        $applicant = auth('applicant')->user();

        return view('applicant.dashboard', [
            'applicant' => $applicant,
            'applicantData' => $applicant->applicantData,
        ]);
    }
}
