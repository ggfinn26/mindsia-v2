<?php

namespace App\Services\Alerts;

use Carbon\CarbonInterface;

class StrategicAlert
{
    public function __construct(
        public readonly string $moduleKey,
        public readonly string $moduleLabel,
        public readonly string $category,
        public readonly string $subject,
        public readonly string $branchName,
        public readonly string $detail,
        public readonly string $url,
        public readonly string $severity, // 'danger' | 'warning'
        public readonly ?CarbonInterface $occurredAt = null,
    ) {}
}
