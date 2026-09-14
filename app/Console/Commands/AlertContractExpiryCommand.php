<?php

namespace App\Console\Commands;

use App\Models\EmployeeNotification;
use App\Models\EmploymentStatus;
use App\Models\User;
use App\Services\TelegramLogService;
use Illuminate\Console\Command;

class AlertContractExpiryCommand extends Command
{
    protected $signature = 'notification:alert-contract-expiry';

    protected $description = 'Kirim alert kontrak mendekati berakhir: H-31 (sekali) dan H-7 s.d. H-1 (harian)';

    public function __construct(
        private readonly TelegramLogService $telegram,
    ) {
        parent::__construct();
    }

    public function handle(): void
    {
        $today = now()->toDateString();

        $h31 = now()->addDays(31)->toDateString();
        $h7 = now()->addDays(7)->toDateString();
        $h1 = now()->addDays(1)->toDateString();

        // H-31: sekali
        $expiringH31 = EmploymentStatus::where('contract_end_date', $h31)
            ->whereDoesntHave('offBoarding')
            ->with(['employee.user', 'position'])
            ->get();

        foreach ($expiringH31 as $status) {
            $this->sendAlert($status, 31);
        }

        // H-7 s.d. H-1: harian
        $expiringNear = EmploymentStatus::whereBetween('contract_end_date', [$h1, $h7])
            ->whereDoesntHave('offBoarding')
            ->with(['employee.user', 'position'])
            ->get();

        foreach ($expiringNear as $status) {
            $daysLeft = (int) now()->diffInDays($status->contract_end_date);
            $this->sendAlert($status, $daysLeft);
        }

        $this->info('Contract expiry alerts sent.');
    }

    private function sendAlert(EmploymentStatus $status, int $daysLeft): void
    {
        $employee = $status->employee;
        $positionName = $status->position?->name ?? '-';
        $endDate = $status->contract_end_date->format('d/m/Y');
        $subject = "Kontrak Mendekati Berakhir (H-{$daysLeft})";
        $message = "Kontrak {$employee->full_name} ({$positionName}) berakhir pada {$endDate} ({$daysLeft} hari lagi).";

        // In-app: notify employee
        EmployeeNotification::create([
            'employee_id' => $employee->id,
            'subject' => $subject,
            'message' => $message,
            'status' => 'unread',
        ]);

        // In-app: notify BOARD employees
        User::role('BOARD_OF_DIRECTORS')
            ->whereHas('employee')
            ->with('employee')
            ->get()
            ->each(function ($user) use ($subject, $message) {
                EmployeeNotification::create([
                    'employee_id' => $user->employee->id,
                    'subject' => $subject,
                    'message' => $message,
                    'status' => 'unread',
                ]);
            });

        // Telegram group log
        $this->telegram->log('WARN', 'employment', 'contract_expiry_alert', $message);

        $this->line("Alert sent: {$employee->full_name} (H-{$daysLeft})");
    }
}
