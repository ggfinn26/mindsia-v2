<?php

namespace App\Http\Controllers;

use App\Models\JobPosting;
use App\Repositories\Recruitment\JobPostingRepository;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CareerController extends Controller
{
    public function __construct(
        private readonly JobPostingRepository $repository,
    ) {}

    // publik — tanpa auth, ditampilkan di landing page karir
    public function index(Request $request): View
    {
        return view('career.index', [
            'postings' => $this->repository->listPublished($request->only(['branch_id', 'position_id'])),
        ]);
    }

    public function show(JobPosting $jobPosting): View
    {
        // hanya tampilkan jika published
        abort_unless($jobPosting->status === JobPosting::STATUS_PUBLISHED, 404);

        return view('career.show', compact('jobPosting'));
    }
}
