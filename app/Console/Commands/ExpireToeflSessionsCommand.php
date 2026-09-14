<?php

namespace App\Console\Commands;

use App\Models\ToeflSession;
use Illuminate\Console\Command;

class ExpireToeflSessionsCommand extends Command
{
    protected $signature = 'toefl:expire-sessions';

    protected $description = 'Expire session TOEFL in_progress yang semua timer-nya sudah habis';

    public function handle(): void
    {
        // max total waktu ITP: 35+25+55 = 115 menit + 10 menit buffer
        $maxMinutes = 125;

        $expired = ToeflSession::where('status', ToeflSession::STATUS_IN_PROGRESS)
            ->where('started_at', '<', now()->subMinutes($maxMinutes))
            ->get();

        foreach ($expired as $session) {
            $session->update(['status' => ToeflSession::STATUS_EXPIRED]);
            $this->info("Session #{$session->id} di-expire.");
        }

        $this->info("Total expired: {$expired->count()}");
    }
}
