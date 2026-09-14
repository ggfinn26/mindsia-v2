<?php

namespace App\Console\Commands;

use App\Repositories\Facility\BranchRentTerminRepository;
use Illuminate\Console\Command;

class CheckRentTerminOverdueCommand extends Command
{
    protected $signature = 'facility:check-rent-overdue';

    protected $description = 'Mark unpaid rent termins as overdue when due_date has passed';

    public function handle(BranchRentTerminRepository $repository): void
    {
        $overdue = $repository->unpaidOverdue();

        foreach ($overdue as $termin) {
            $repository->markOverdue($termin);
        }

        $this->info("Marked {$overdue->count()} termin(s) as overdue.");
    }
}
