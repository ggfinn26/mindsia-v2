<?php

namespace App\Repositories\Recruitment;

use App\Models\ApplicantCourse;
use App\Models\ApplicantEducation;
use App\Models\ApplicantMasterData;
use App\Models\ApplicantWorkExperience;

class ApplicantRepository
{
    public function update(ApplicantMasterData $applicant, array $data): void
    {
        $applicant->update($data);
    }

    public function addEducation(ApplicantMasterData $applicant, array $data): ApplicantEducation
    {
        return $applicant->educations()->create($data);
    }

    public function updateEducation(ApplicantEducation $education, array $data): void
    {
        $education->update($data);
    }

    public function addCourse(ApplicantMasterData $applicant, array $data): ApplicantCourse
    {
        return $applicant->courses()->create($data);
    }

    public function updateCourse(ApplicantCourse $course, array $data): void
    {
        $course->update($data);
    }

    public function addWorkExperience(ApplicantMasterData $applicant, array $data): ApplicantWorkExperience
    {
        return $applicant->workExperiences()->create($data);
    }

    public function updateWorkExperience(ApplicantWorkExperience $experience, array $data): void
    {
        $experience->update($data);
    }
}
