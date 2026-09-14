<?php

namespace App\Services\Letter;

use App\Models\OutLetterViaGenerate;

class LetterNumberingService
{
    /**
     * Sequence per (letter_type, branch_id, year, month).
     * MUST be called inside DB::transaction() + lockForUpdate() from caller.
     */
    public function nextSequence(string $letterType, int $branchId, int $year, int $month): int
    {
        return OutLetterViaGenerate::where('letter_type', $letterType)
            ->where('branch_id', $branchId)
            ->whereYear('letter_date', $year)
            ->whereMonth('letter_date', $month)
            ->whereNotNull('letter_number')
            ->lockForUpdate()
            ->count() + 1;
    }

    /**
     * Format pattern with available tokens.
     * Tokens: {BRANCH}, {YEAR}, {MONTH}, {TYPE}, {SEQ:Nd}
     *
     * Example: {BRANCH}/{YEAR}/{MONTH}/{SEQ:3d} → SBY/2025/09/001
     */
    public function format(string $pattern, array $parts): string
    {
        $result = preg_replace_callback('/\{SEQ:(\d+)d\}/', function ($matches) use ($parts) {
            return str_pad((string) ($parts['seq'] ?? 1), (int) $matches[1], '0', STR_PAD_LEFT);
        }, $pattern);

        return str_replace(
            ['{BRANCH}', '{YEAR}', '{MONTH}', '{TYPE}'],
            [
                $parts['branch'] ?? '',
                $parts['year'] ?? date('Y'),
                str_pad((string) ($parts['month'] ?? date('m')), 2, '0', STR_PAD_LEFT),
                $parts['type'] ?? '',
            ],
            $result
        );
    }
}
