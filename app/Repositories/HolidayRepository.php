<?php

namespace App\Repositories;

use App\Models\Holiday;
use Carbon\Carbon;

class HolidayRepository
{
    public function isHoliday(string $date): bool
    {
        return Holiday::where('holiday_start_date', '<=', $date)
            ->where('holiday_end_date', '>=', $date)
            ->exists();
    }

    /**
     * Semua tanggal libur nasional yang jatuh dalam rentang (rentang libur dipecah per hari).
     *
     * @return list<string> Y-m-d
     */
    public function datesBetween(string $from, string $to): array
    {
        $dates = [];

        Holiday::where('holiday_start_date', '<=', $to)
            ->where('holiday_end_date', '>=', $from)
            ->get(['holiday_start_date', 'holiday_end_date'])
            ->each(function (Holiday $holiday) use (&$dates, $from, $to) {
                $day = Carbon::parse(max($holiday->holiday_start_date->toDateString(), $from));
                $last = Carbon::parse(min($holiday->holiday_end_date->toDateString(), $to));

                for (; $day->lte($last); $day->addDay()) {
                    $dates[] = $day->toDateString();
                }
            });

        return array_values(array_unique($dates));
    }
}
