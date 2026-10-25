<?php

namespace App\Console\Commands;

use App\Models\Socialization;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('socialization:await-validation')]
#[Description('Flip scheduled socializations to awaiting_validation once their last schedule end time has passed')]
class AwaitSocializationValidation extends Command
{
    public function handle(): int
    {
        $count = Socialization::query()
            ->where('status', Socialization::STATUS_SCHEDULED)
            ->whereHas('schedules')
            ->whereDoesntHave('schedules', fn ($q) => $q
                ->whereDate('schedule_date', '>', today())
                ->orWhere(fn ($q) => $q->whereDate('schedule_date', today())->where('end_time', '>=', now()->format('H:i:s'))))
            ->update(['status' => Socialization::STATUS_AWAITING_VALIDATION]);

        $this->info("{$count} sosialisasi dipindah ke Menunggu Validasi.");

        return self::SUCCESS;
    }
}
