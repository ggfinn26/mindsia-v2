<?php

namespace App\Http\Controllers\Applicant;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class ApplicationListController extends Controller
{
    public function index(): View
    {
        $applicant = auth('applicant')->user()->applicantData;

        $applications = $applicant->applications()
            ->with(['posting.branch', 'posting.position'])
            ->latest('applied_at')
            ->paginate(15);

        return view('applicant.applications.index', compact('applications'));
    }
}
