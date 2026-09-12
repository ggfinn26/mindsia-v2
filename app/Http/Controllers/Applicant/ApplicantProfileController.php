<?php

namespace App\Http\Controllers\Applicant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Recruitment\StoreApplicantCourseRequest;
use App\Http\Requests\Recruitment\StoreApplicantEducationRequest;
use App\Http\Requests\Recruitment\StoreApplicantWorkExperienceRequest;
use App\Http\Requests\Recruitment\UpdateApplicantCourseRequest;
use App\Http\Requests\Recruitment\UpdateApplicantEducationRequest;
use App\Http\Requests\Recruitment\UpdateApplicantProfileRequest;
use App\Http\Requests\Recruitment\UpdateApplicantWorkExperienceRequest;
use App\Models\ApplicantCourse;
use App\Models\ApplicantEducation;
use App\Models\ApplicantMasterData;
use App\Models\ApplicantWorkExperience;
use App\Repositories\Recruitment\ApplicantRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ApplicantProfileController extends Controller
{
    // guard: applicant — pelamar kelola profil sendiri

    public function __construct(
        private readonly ApplicantRepository $repository,
    ) {}

    public function show(): View
    {
        return view('applicant.profile.show', [
            'applicant' => $this->applicant()->load(['educations', 'courses', 'workExperiences']),
        ]);
    }

    public function edit(): View
    {
        return view('applicant.profile.edit', ['applicant' => $this->applicant()]);
    }

    public function update(UpdateApplicantProfileRequest $request): RedirectResponse
    {
        $this->repository->update($this->applicant(), $request->validated());

        return redirect()->route('applicant.profile.show')->with('success', 'Profil diperbarui.');
    }

    // --- Education ---

    public function storeEducation(StoreApplicantEducationRequest $request): RedirectResponse
    {
        $this->repository->addEducation($this->applicant(), $request->validated());

        return redirect()->route('applicant.profile.show')->with('success', 'Pendidikan ditambahkan.');
    }

    public function updateEducation(UpdateApplicantEducationRequest $request, ApplicantEducation $education): RedirectResponse
    {
        abort_unless($education->applicant_id === $this->applicant()->id, 403);
        $this->repository->updateEducation($education, $request->validated());

        return redirect()->route('applicant.profile.show')->with('success', 'Pendidikan diperbarui.');
    }

    public function destroyEducation(ApplicantEducation $education): RedirectResponse
    {
        abort_unless($education->applicant_id === $this->applicant()->id, 403);
        $education->delete();

        return redirect()->route('applicant.profile.show')->with('success', 'Pendidikan dihapus.');
    }

    // --- Course ---

    public function storeCourse(StoreApplicantCourseRequest $request): RedirectResponse
    {
        $this->repository->addCourse($this->applicant(), $request->validated());

        return redirect()->route('applicant.profile.show')->with('success', 'Sertifikasi ditambahkan.');
    }

    public function updateCourse(UpdateApplicantCourseRequest $request, ApplicantCourse $course): RedirectResponse
    {
        abort_unless($course->applicant_id === $this->applicant()->id, 403);
        $this->repository->updateCourse($course, $request->validated());

        return redirect()->route('applicant.profile.show')->with('success', 'Sertifikasi diperbarui.');
    }

    public function destroyCourse(ApplicantCourse $course): RedirectResponse
    {
        abort_unless($course->applicant_id === $this->applicant()->id, 403);
        $course->delete();

        return redirect()->route('applicant.profile.show')->with('success', 'Sertifikasi dihapus.');
    }

    // --- Work Experience ---

    public function storeWorkExperience(StoreApplicantWorkExperienceRequest $request): RedirectResponse
    {
        $this->repository->addWorkExperience($this->applicant(), $request->validated());

        return redirect()->route('applicant.profile.show')->with('success', 'Pengalaman kerja ditambahkan.');
    }

    public function updateWorkExperience(UpdateApplicantWorkExperienceRequest $request, ApplicantWorkExperience $experience): RedirectResponse
    {
        abort_unless($experience->applicant_id === $this->applicant()->id, 403);
        $this->repository->updateWorkExperience($experience, $request->validated());

        return redirect()->route('applicant.profile.show')->with('success', 'Pengalaman kerja diperbarui.');
    }

    public function destroyWorkExperience(ApplicantWorkExperience $experience): RedirectResponse
    {
        abort_unless($experience->applicant_id === $this->applicant()->id, 403);
        $experience->delete();

        return redirect()->route('applicant.profile.show')->with('success', 'Pengalaman kerja dihapus.');
    }

    private function applicant(): ApplicantMasterData
    {
        return request()->user()->applicantData;
    }
}
