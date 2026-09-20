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
     * Format pattern with available tokens:
     *   {NO}          → 001 (3-digit seq)
     *   {SEQ:Nd}      → N-digit seq, e.g. {SEQ:5d} → 00001
     *   {TYPE}        → template_code
     *   {BRANCH}      → branch code
     *   {YEAR}        → 2025
     *   {YY}          → 25
     *   {MONTH}       → 09
     *   {BULAN_ROMAWI}→ IX
     *   {BULAN_NAMA}  → September
     *   {MMYY}        → 0925
     *   {MMYYYY}      → 092025
     *
     * Example: {NO}/A/{TYPE}/MINDSIA/{BULAN_ROMAWI}/{YEAR} → 001/A/SP1/MINDSIA/IX/2025
     */
    public function format(string $pattern, array $parts): string
    {
        $seq = $parts['seq'] ?? 1;
        $month = (int) ($parts['month'] ?? date('n'));
        $year = (string) ($parts['year'] ?? date('Y'));

        $result = preg_replace_callback('/\{SEQ:(\d+)d\}/', function ($matches) use ($seq) {
            return str_pad((string) $seq, (int) $matches[1], '0', STR_PAD_LEFT);
        }, $pattern);

        return str_replace(
            ['{BRANCH}', '{YEAR}', '{YY}', '{MONTH}', '{BULAN_ROMAWI}', '{BULAN_NAMA}', '{MMYY}', '{MMYYYY}', '{TYPE}', '{NO}'],
            [
                $parts['branch'] ?? '',
                $year,
                substr($year, -2),
                str_pad((string) $month, 2, '0', STR_PAD_LEFT),
                self::toRoman($month),
                self::toMonthName($month),
                str_pad((string) $month, 2, '0', STR_PAD_LEFT).substr($year, -2),
                str_pad((string) $month, 2, '0', STR_PAD_LEFT).$year,
                $parts['type'] ?? '',
                str_pad((string) $seq, 3, '0', STR_PAD_LEFT),
            ],
            $result
        );
    }

    private static function toRoman(int $month): string
    {
        return ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'][$month - 1] ?? '';
    }

    private static function toMonthName(int $month): string
    {
        return ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'][$month - 1] ?? '';
    }
}
