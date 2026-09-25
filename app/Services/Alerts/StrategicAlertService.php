<?php

namespace App\Services\Alerts;

class StrategicAlertService
{
    /** @param list<AlertProvider> $providers */
    public function __construct(private readonly array $providers) {}

    /** @return list<StrategicAlert> sorted: danger first, then by occurredAt asc */
    public function alertsFor(?int $branchId): array
    {
        $alerts = [];
        foreach ($this->providers as $provider) {
            foreach ($provider->alertsFor($branchId) as $alert) {
                $alerts[] = $alert;
            }
        }

        usort($alerts, function (StrategicAlert $a, StrategicAlert $b): int {
            if ($a->severity !== $b->severity) {
                return $a->severity === 'danger' ? -1 : 1;
            }

            return ($a->occurredAt?->timestamp ?? 0) <=> ($b->occurredAt?->timestamp ?? 0);
        });

        return $alerts;
    }
}
