<?php

namespace App\Http\Controllers\Applicant;

use App\Http\Controllers\Controller;
use App\Models\JobPosting;
use Illuminate\View\View;

class JobListingController extends Controller
{
    public function index(): View
    {
        $applicant = auth('applicant')->user()->applicantData;

        $appliedPostingIds = $applicant->applications()->pluck('job_posting_id');

        $postings = JobPosting::where('status', JobPosting::STATUS_PUBLISHED)
            ->where(function ($q) {
                $q->whereNull('closing_date')->orWhere('closing_date', '>=', today());
            })
            ->with(['branch', 'position'])
            ->latest('publish_date')
            ->paginate(12);

        return view('applicant.job-listings.index', compact('postings', 'appliedPostingIds'));
    }
}
