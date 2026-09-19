<?php

namespace App\Services\Recruitment;

use App\Models\Employee;
use App\Models\EmployeeOnboarding;
use App\Models\JobApplication;
use App\Repositories\Recruitment\EmployeeOnboardingRepository;
use App\Repositories\Recruitment\JobPermintaanRepository;
use App\Services\TelegramLogService;
use Illuminate\Support\Facades\DB;

class EmployeeOnboardingService
{
    public function __construct(
        private readonly EmployeeOnboardingRepository $onboardingRepo,
        private readonly JobPermintaanRepository $permintaanRepo,
        private readonly TelegramLogService $telegramLogService,
    ) {}

    public function complete(EmployeeOnboarding $onboarding, int $completedByEmployeeId): Employee
    {
        abort_unless($onboarding->status === 'approved', 422, 'Onboarding belum diapprove.');

        $onboarding->loadMissing(['application.applicant', 'application.posting.permintaan', 'branch.area.region']);

        return DB::transaction(function () use ($onboarding, $completedByEmployeeId) {
            $applicant = $onboarding->application->applicant;

            // map applicant gender (male/female) → employee gender (L/P)
            $genderMap = ['male' => 'L', 'female' => 'P'];
            $gender = $genderMap[$applicant->gender] ?? 'L';

            // derive location from branch hierarchy
            $branch = $onboarding->branch;
            $area = $branch->area;
            $region = $area?->region;

            // buat employee baru dari data onboarding + applicant
            $employee = Employee::create([
                'employee_code' => 'EMP-'.str_pad((string) (Employee::max('id') + 1), 5, '0', STR_PAD_LEFT),
                'full_name' => $applicant->full_name,
                'gender' => $gender,
                'birthdate' => $applicant->birth_date ?? now()->subYears(25),
                'email' => $applicant->email,
                'whatsapp_number' => $applicant->whatsapp_number,
                'branch_id' => $onboarding->branch_id,
                'area_id' => $area?->id,
                'region_id' => $region?->id,
                'is_active' => true,
            ]);

            $onboarding->update([
                'status' => 'completed',
                'employee_id' => $employee->id,
                'completed_at' => now(),
            ]);

            // update job_application.status → hired
            $onboarding->application->update(['status' => JobApplication::STATUS_HIRED]);

            // cek apakah headcount permintaan sudah terpenuhi
            $permintaan = $onboarding->application->posting->permintaan;
            $this->permintaanRepo->checkAndFulfillIfComplete($permintaan);

            $this->telegramLogService->log('INFO', 'recruitment', 'onboarding_completed', "Onboarding {$onboarding->id} selesai oleh employee {$completedByEmployeeId}, employee baru {$employee->id} dibuat.");

            return $employee;
        });
    }
}
