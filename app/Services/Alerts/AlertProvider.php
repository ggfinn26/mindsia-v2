<?php

namespace App\Services\Alerts;

interface AlertProvider
{
    /** @return list<StrategicAlert> */
    public function alertsFor(?int $branchId): array;
}
