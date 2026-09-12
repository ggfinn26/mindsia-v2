<?php

namespace App\Repositories\Recruitment;

use App\Models\JobApplication;
use App\Models\OfferingLetter;

class OfferingLetterRepository
{
    public function create(JobApplication $application, array $data): OfferingLetter
    {
        return OfferingLetter::create(array_merge($data, [
            'job_application_id' => $application->id,
        ]));
    }

    public function update(OfferingLetter $letter, array $data): void
    {
        $letter->update($data);
    }

    public function negotiate(OfferingLetter $letter, array $data): void
    {
        $letter->update([
            'status' => 'negotiating',
            'agreed_salary' => $data['agreed_salary'] ?? $letter->agreed_salary,
            'notes' => $data['notes'] ?? $letter->notes,
        ]);
    }

    public function accept(OfferingLetter $letter): void
    {
        $letter->update(['status' => 'accepted']);
    }

    public function decline(OfferingLetter $letter): void
    {
        $letter->update(['status' => 'declined']);
    }
}
