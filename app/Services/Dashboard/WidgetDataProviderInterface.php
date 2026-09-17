<?php

namespace App\Services\Dashboard;

interface WidgetDataProviderInterface
{
    public function getKey(): string;

    public function getData(?int $branchId = null): array;

    public function getExportData(?int $branchId = null): array;
}
