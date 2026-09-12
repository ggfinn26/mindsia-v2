<?php

namespace App\Http\Controllers\Admin\Recruitment;

use App\Http\Controllers\Controller;
use App\Http\Requests\Recruitment\ReviewEmployeeOnboardingRequest;
use App\Http\Requests\Recruitment\StoreEmployeeOnboardingRequest;
use App\Models\EmployeeOnboarding;
use App\Models\JobApplication;
use App\Repositories\Recruitment\EmployeeOnboardingRepository;
use App\Services\Recruitment\EmployeeOnboardingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmployeeOnboardingController extends Controller
{
    public function __construct(
        private readonly EmployeeOnboardingRepository $repository,
        private readonly EmployeeOnboardingService $onboardingService,
    ) {}

    public function index(): View
    {
        return view('recruitment.onboarding.index', [
            'onboardings' => $this->repository->list(),
        ]);
    }

    public function store(StoreEmployeeOnboardingRequest $request, JobApplication $jobApplication): RedirectResponse
    {
        $jobApplication->loadMissing('offeringLetter');
        abort_unless($jobApplication->offeringLetter, 422, 'Offering letter tidak ditemukan.');

        $onboarding = $this->repository->create(array_merge($request->validated(), [
            'job_application_id' => $jobApplication->id,
            'offering_letter_id' => $jobApplication->offeringLetter->id,
        ]));

        return redirect()->route('recruitment.onboarding.show', $onboarding)->with('success', 'Onboarding dibuat.');
    }

    public function show(EmployeeOnboarding $employeeOnboarding): View
    {
        $employeeOnboarding->load(['application.applicant', 'branch', 'position', 'reviewedBy', 'employee']);

        return view('recruitment.onboarding.show', compact('employeeOnboarding'));
    }

    public function review(ReviewEmployeeOnboardingRequest $request, EmployeeOnboarding $employeeOnboarding): RedirectResponse
    {
        // BOARD: approve atau reject onboarding
        $this->repository->review($employeeOnboarding, $request->validated(), $request->user()->employee->id);

        return redirect()->route('recruitment.onboarding.show', $employeeOnboarding)->with('success', 'Onboarding di-review.');
    }

    public function complete(Request $request, EmployeeOnboarding $employeeOnboarding): RedirectResponse
    {
        abort_unless($request->user()->can('manage employee onboarding'), 403);

        $this->onboardingService->complete($employeeOnboarding, $request->user()->employee->id);

        return redirect()->route('recruitment.onboarding.show', $employeeOnboarding)->with('success', 'Onboarding selesai. Employee baru dibuat.');
    }
}
