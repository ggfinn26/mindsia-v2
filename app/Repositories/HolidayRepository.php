<?php

namespace App\Repositories;

use App\Models\Holiday;

class HolidayRepository
{
    public function isHoliday(string $date): bool
    {
        return Holiday::where('holiday_start_date', '<=', $date)
            ->where('holiday_end_date', '>=', $date)
            ->exists();
    }
}
