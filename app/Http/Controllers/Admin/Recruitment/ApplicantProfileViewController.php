<?php

namespace App\Http\Controllers\Admin\Recruitment;

use App\Http\Controllers\Controller;
use App\Models\ApplicantMasterData;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ApplicantProfileViewController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()->can('applicant.view'), 403);

        $applicants = ApplicantMasterData::withCount('applications')
            ->when($request->search, fn ($q, $s) => $q->where('full_name', 'like', "%{$s}%")
                ->orWhere('email', 'like', "%{$s}%")
                ->orWhere('whatsapp_number', 'like', "%{$s}%")
            )
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('recruitment.applicants.index', compact('applicants'));
    }

    public function show(Request $request, ApplicantMasterData $applicant): View
    {
        abort_unless($request->user()->can('applicant.view'), 403);

        $applicant->load(['educations', 'courses', 'workExperiences', 'applications.posting']);

        return view('recruitment.applicants.show', compact('applicant'));
    }
}
