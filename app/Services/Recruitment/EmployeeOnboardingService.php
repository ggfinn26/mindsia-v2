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

        $onboarding->loadMissing(['application.posting.permintaan']);

        return DB::transaction(function () use ($onboarding, $completedByEmployeeId) {
            // buat employee baru dari data onboarding
            // field minimal — sisanya diisi HR via domain employee
            $employee = Employee::create([
                'branch_id' => $onboarding->branch_id,
                'position_id' => $onboarding->position_id,
                'employment_type' => $onboarding->employment_type,
                'start_date' => $onboarding->start_date,
                // full_name, NIK, dll diisi via EmployeeController setelah ini
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
