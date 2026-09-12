<?php

namespace App\Console\Commands;

use App\Models\JobPosting;
use Illuminate\Console\Command;

class CloseExpiredJobPostingsCommand extends Command
{
    protected $signature = 'recruitment:close-expired-postings';

    protected $description = 'Tutup otomatis posting yang closing_date-nya sudah lewat';

    public function handle(): void
    {
        $count = JobPosting::where('status', JobPosting::STATUS_PUBLISHED)
            ->whereNotNull('closing_date')
            ->where('closing_date', '<', now()->toDateString())
            ->update(['status' => JobPosting::STATUS_CLOSED]);

        $this->info("Ditutup otomatis: {$count} posting.");
    }
}
