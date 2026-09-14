<?php

namespace App\Console\Commands;

use App\Models\Employee;
use App\Repositories\Facility\BranchRentTerminRepository;
use App\Services\Notification\NotificationDispatchService;
use Illuminate\Console\Command;

class SendRentDueSoonNotificationsCommand extends Command
{
    protected $signature = 'facility:send-rent-due-soon';

    protected $description = 'Send notifications for rent termins due within 7 days';

    public function handle(BranchRentTerminRepository $repository, NotificationDispatchService $notifier): void
    {
        $termins = $repository->dueSoon(7);

        foreach ($termins as $termin) {
            $branch = $termin->contract?->branch;
            if (! $branch) {
                continue;
            }

            // send to each finance employee at this branch who can view rent contracts
            $employees = Employee::where('branch_id', $branch->id)
                ->whereHas('roles', fn ($q) => $q->where('name', 'BOARD_OF_DIRECTORS'))
                ->orWhere(fn ($q) => $q
                    ->where('branch_id', $branch->id)
                    ->whereHas('permissions', fn ($q) => $q->where('name', 'facility.rent_contract.mark_paid'))
                )
                ->get();

            foreach ($employees as $employee) {
                $notifier->send('rent_due_soon_notification', $employee, [
                    'branch_name' => $branch->branch_name,
                    'termin_number' => $termin->termin_number,
                    'due_date' => $termin->due_date->format('d M Y'),
                    'amount' => number_format((float) $termin->amount, 0, ',', '.'),
                    'contract_id' => $termin->contract_id,
                ]);
            }
        }

        $this->info("Processed {$termins->count()} upcoming termin(s).");
    }
}
